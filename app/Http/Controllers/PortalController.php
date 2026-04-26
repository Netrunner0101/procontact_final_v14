<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidTokenException;
use App\Models\Contact;
use App\Models\Note;
use App\Models\NoteTemplate;
use App\Services\ClientPortalService;
use App\Services\PortalAuthService;
use App\Services\PortalTokenService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PortalController extends Controller
{
    public function __construct(
        private PortalTokenService $tokenService,
        private ClientPortalService $portalService,
        private PortalAuthService $authService,
    ) {}

    /**
     * Resolve the contact bound to the magic-link token.
     * Returns null on invalid token (caller renders 403 view).
     */
    private function resolveContact(string $token): ?Contact
    {
        try {
            return $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
            return null;
        }
    }

    private function sessionKey(string $token): string
    {
        return 'portal_auth_' . substr(hash('sha256', $token), 0, 32);
    }

    /**
     * Step 1: Magic-link landing.
     * - Logs the visit.
     * - If trusted device cookie validates → grant access immediately.
     * - Otherwise → render the login page (email entry).
     */
    public function show(Request $request, string $token)
    {
        $contact = $this->resolveContact($token);
        if (!$contact) {
            return response()->view('portal.error', [], 403);
        }

        $this->authService->log($contact->id, 'token_visit', $request);

        if ($request->session()->get($this->sessionKey($token))) {
            return $this->dashboard($request, $token, $contact);
        }

        $rotated = $this->authService->validateTrustedDevice($contact, $request);
        if ($rotated) {
            $request->session()->put($this->sessionKey($token), true);
            return $this->dashboard($request, $token, $contact)->withCookie($rotated);
        }

        return view('portal.login', [
            'token' => $token,
            'step' => 'email',
        ]);
    }

    /**
     * Step 2: Client submits their email.
     * Always returns a generic "code sent" view to prevent enumeration.
     */
    public function requestOtp(Request $request, string $token)
    {
        $contact = $this->resolveContact($token);
        if (!$contact) {
            return response()->view('portal.error', [], 403);
        }

        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $this->authService->issueOtp($contact, $data['email'], $request);

        return view('portal.login', [
            'token' => $token,
            'step' => 'otp',
            'email' => $data['email'],
        ]);
    }

    /**
     * Step 3: Client submits the OTP. On success, set trusted-device cookie
     * and redirect to the portal dashboard.
     */
    public function verifyOtp(Request $request, string $token)
    {
        $contact = $this->resolveContact($token);
        if (!$contact) {
            return response()->view('portal.error', [], 403);
        }

        $data = $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $ok = $this->authService->verifyOtp($contact, $data['email'], $data['code'], $request);

        if (!$ok) {
            return back()
                ->withInput(['email' => $data['email']])
                ->withErrors(['code' => __('Invalid or expired code. Please try again.')])
                ->with('portal_step', 'otp');
        }

        $cookie = $this->authService->issueTrustedDevice($contact, $request);
        $request->session()->put($this->sessionKey($token), true);
        $request->session()->regenerate();

        return redirect()
            ->route('portal.index', ['token' => $token])
            ->withCookie($cookie);
    }

    /**
     * Logout: revoke trusted device + clear session, return to login.
     */
    public function logout(Request $request, string $token)
    {
        $contact = $this->resolveContact($token);
        if ($contact) {
            $this->authService->revokeTrustedDevice($contact, $request);
        }

        $request->session()->forget($this->sessionKey($token));

        return redirect()
            ->route('portal.login', ['token' => $token])
            ->withCookie(Cookie::forget(PortalAuthService::COOKIE_NAME, '/portal'));
    }

    /**
     * Right-of-erasure entry point (Art. 17 GDPR).
     * Revokes all access immediately and queues the entrepreneur notification.
     */
    public function requestErasure(Request $request, string $token)
    {
        $contact = $this->resolveContact($token);
        if (!$contact) {
            return response()->view('portal.error', [], 403);
        }

        $this->authService->log($contact->id, 'erasure_requested', $request);
        $this->authService->revokeAllTokens($contact);
        $this->authService->revokeAllTrustedDevices($contact);

        $request->session()->forget($this->sessionKey($token));

        return view('portal.erasure-confirmed', [
            'contact' => $contact,
        ]);
    }

    /**
     * Authenticated dashboard — same logic as the previous index().
     * Called from show() when the visitor is already authenticated.
     */
    private function dashboard(Request $request, string $token, Contact $contact)
    {
        $appointments = $this->portalService->getAppointments($contact);
        $now = Carbon::now();

        $upcoming = $appointments
            ->filter(fn($a) => $a->date_debut && $a->date_debut->copy()->setTimeFromTimeString(
                $a->heure_debut ? $a->heure_debut->format('H:i:s') : '00:00:00'
            )->gte($now))
            ->sortBy('date_debut')
            ->take(5)
            ->values();

        $past = $appointments
            ->filter(fn($a) => !$upcoming->contains('id', $a->id))
            ->sortByDesc('date_debut')
            ->values();

        return response()->view('portal.index', [
            'contact' => $contact,
            'appointments' => $appointments,
            'upcoming' => $upcoming,
            'past' => $past,
            'token' => $token,
        ]);
    }

    /**
     * Index — kept for the route name; now just delegates to show().
     */
    public function index(Request $request, string $token)
    {
        return $this->show($request, $token);
    }

    /**
     * Login page (no-auth GET helper used by middleware redirects).
     */
    public function login(Request $request, string $token)
    {
        if (!$this->resolveContact($token)) {
            return response()->view('portal.error', [], 403);
        }

        return view('portal.login', [
            'token' => $token,
            'step' => session('portal_step', 'email'),
            'email' => session('email'),
        ]);
    }

    public function showAppointment(Request $request, string $token, int $appointmentId)
    {
        $contact = $request->attributes->get('portal_contact') ?: $this->resolveContact($token);
        if (!$contact) {
            return response()->view('portal.error', [], 403);
        }

        $appointment = $contact->rendezVous()
            ->with(['activite', 'notes' => function ($query) {
                $query->where('is_shared_with_client', true)->orderByDesc('created_at');
            }])
            ->findOrFail($appointmentId);

        $templates = NoteTemplate::where('user_id', $appointment->user_id)
            ->where(function ($q) use ($contact) {
                $q->whereNull('contact_id')->orWhere('contact_id', $contact->id);
            })
            ->orderBy('titre')
            ->get();

        return view('portal.appointment', [
            'contact' => $contact,
            'appointment' => $appointment,
            'sharedNotes' => $appointment->notes,
            'templates' => $templates,
            'token' => $token,
        ]);
    }

    public function storeNote(Request $request, string $token, int $appointmentId)
    {
        $contact = $request->attributes->get('portal_contact') ?: $this->resolveContact($token);
        if (!$contact) {
            return response()->view('portal.error', [], 403);
        }

        $data = $request->validate([
            'titre' => 'nullable|string|max:255',
            'commentaire' => 'required|string',
        ], [
            'commentaire.required' => __('This field is required.'),
        ]);

        $appointment = $contact->rendezVous()->findOrFail($appointmentId);

        Note::create([
            'user_id' => $appointment->user_id,
            'rendez_vous_id' => $appointment->id,
            'contact_id' => $contact->id,
            'titre' => $data['titre'] ?: __('Client note — :name', ['name' => trim($contact->prenom . ' ' . $contact->nom)]),
            'commentaire' => $data['commentaire'],
            'is_shared_with_client' => true,
            'date_create' => now(),
            'date_update' => now(),
        ]);

        return redirect()
            ->route('portal.appointment', ['token' => $token, 'appointmentId' => $appointmentId])
            ->with('success', __('Your note has been added.'));
    }

    public function updateNote(Request $request, string $token, int $noteId)
    {
        $contact = $request->attributes->get('portal_contact') ?: $this->resolveContact($token);
        if (!$contact) {
            return response()->view('portal.error', [], 403);
        }

        $data = $request->validate([
            'titre' => 'nullable|string|max:255',
            'commentaire' => 'required|string',
        ], [
            'commentaire.required' => __('This field is required.'),
        ]);

        $note = Note::where('id', $noteId)
            ->where('contact_id', $contact->id)
            ->whereHas('rendezVous', fn($q) => $q->where('contact_id', $contact->id))
            ->firstOrFail();

        $note->update([
            'titre' => $data['titre'] ?: $note->titre,
            'commentaire' => $data['commentaire'],
            'date_update' => now(),
        ]);

        return redirect()
            ->route('portal.appointment', ['token' => $token, 'appointmentId' => $note->rendez_vous_id])
            ->with('success', __('Your note has been updated.'));
    }

    public function deleteNote(Request $request, string $token, int $noteId)
    {
        $contact = $request->attributes->get('portal_contact') ?: $this->resolveContact($token);
        if (!$contact) {
            return response()->view('portal.error', [], 403);
        }

        $note = Note::where('id', $noteId)
            ->where('contact_id', $contact->id)
            ->whereHas('rendezVous', fn($q) => $q->where('contact_id', $contact->id))
            ->firstOrFail();

        $appointmentId = $note->rendez_vous_id;
        $note->delete();

        return redirect()
            ->route('portal.appointment', ['token' => $token, 'appointmentId' => $appointmentId])
            ->with('success', __('Your note has been deleted.'));
    }

    public function templates(Request $request, string $token)
    {
        $contact = $request->attributes->get('portal_contact') ?: $this->resolveContact($token);
        if (!$contact) {
            return response()->view('portal.error', [], 403);
        }

        $templates = NoteTemplate::where('contact_id', $contact->id)
            ->orderBy('titre')
            ->get();

        return view('portal.templates', [
            'contact' => $contact,
            'templates' => $templates,
            'token' => $token,
        ]);
    }

    public function storeTemplate(Request $request, string $token)
    {
        $contact = $request->attributes->get('portal_contact') ?: $this->resolveContact($token);
        if (!$contact) {
            return response()->view('portal.error', [], 403);
        }

        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'commentaire' => 'required|string',
        ], [
            'titre.required' => __('This field is required.'),
            'commentaire.required' => __('This field is required.'),
        ]);

        NoteTemplate::create([
            'user_id' => $contact->user_id,
            'contact_id' => $contact->id,
            'titre' => $data['titre'],
            'commentaire' => $data['commentaire'],
        ]);

        return redirect()
            ->route('portal.templates', ['token' => $token])
            ->with('success', __('Template created.'));
    }

    public function updateTemplate(Request $request, string $token, int $templateId)
    {
        $contact = $request->attributes->get('portal_contact') ?: $this->resolveContact($token);
        if (!$contact) {
            return response()->view('portal.error', [], 403);
        }

        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'commentaire' => 'required|string',
        ], [
            'titre.required' => __('This field is required.'),
            'commentaire.required' => __('This field is required.'),
        ]);

        $template = NoteTemplate::where('id', $templateId)
            ->where('contact_id', $contact->id)
            ->firstOrFail();

        $template->update($data);

        return redirect()
            ->route('portal.templates', ['token' => $token])
            ->with('success', __('Template updated.'));
    }

    public function deleteTemplate(Request $request, string $token, int $templateId)
    {
        $contact = $request->attributes->get('portal_contact') ?: $this->resolveContact($token);
        if (!$contact) {
            return response()->view('portal.error', [], 403);
        }

        $template = NoteTemplate::where('id', $templateId)
            ->where('contact_id', $contact->id)
            ->firstOrFail();

        $template->delete();

        return redirect()
            ->route('portal.templates', ['token' => $token])
            ->with('success', __('Template deleted.'));
    }
}
