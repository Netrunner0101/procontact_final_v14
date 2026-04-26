<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidTokenException;
use App\Models\Contact;
use App\Models\Note;
use App\Models\NoteTemplate;
use App\Services\ClientPortalService;
use App\Services\PortalTokenService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function __construct(
        private PortalTokenService $tokenService,
        private ClientPortalService $portalService,
    ) {}

    /**
     * Display the client's appointment dashboard via magic-link.
     * Includes upcoming appointments list + calendar data.
     */
    public function index(string $token)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
            return response()->view('portal.error', [], 403);
        }

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

        return view('portal.index', [
            'contact' => $contact,
            'appointments' => $appointments,
            'upcoming' => $upcoming,
            'past' => $past,
            'token' => $token,
        ]);
    }

    /**
     * Display appointment details with shared notes and CRUD form.
     */
    public function showAppointment(string $token, int $appointmentId)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
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

    /**
     * Store a new note from the client (shared with the entrepreneur AND visible to client).
     */
    public function storeNote(Request $request, string $token, int $appointmentId)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
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

    /**
     * Update an existing note created by this client.
     */
    public function updateNote(Request $request, string $token, int $noteId)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
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

    /**
     * Delete a note created by this client.
     */
    public function deleteNote(string $token, int $noteId)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
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

    /**
     * List the client's note templates.
     */
    public function templates(string $token)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
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

    /**
     * Create a new note template owned by this client.
     */
    public function storeTemplate(Request $request, string $token)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
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

    /**
     * Update one of the client's templates.
     */
    public function updateTemplate(Request $request, string $token, int $templateId)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
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

    /**
     * Delete one of the client's templates.
     */
    public function deleteTemplate(string $token, int $templateId)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
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
