<?php

namespace App\Http\Controllers;

use App\Mail\ContactConsentRequestMail;
use App\Mail\ContactDeletionNotificationMail;
use App\Models\Activite;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::with(['emails', 'numeroTelephones'])
            ->withCount('rendezVous')
            ->where('user_id', Auth::id())
            ->paginate(15);

        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        $activites = Activite::where('user_id', Auth::id())->get();
        $selectedActiviteId = request('activite_id');

        return view('contacts.create', compact('activites', 'selectedActiviteId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email|max:255',
            'phones' => 'required|array|min:1',
            'phones.*' => ['required', 'regex:/^[0-9+\s()-]+$/', 'max:20'],
            'rue' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:50',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:20',
            'pays' => 'nullable|string|max:255',
            'activite_id' => 'nullable|exists:activites,id',
        ], [
            'emails.*.email' => __('The email address is not valid.'),
            'phones.*.regex' => __('The phone number must contain only digits, +, spaces, dashes and parentheses.'),
        ]);

        $validated['user_id'] = Auth::id();
        $validated['gdpr_consent_token'] = Str::random(64);
        $validated['gdpr_consent_requested_at'] = now();
        $contact = Contact::create($validated);

        // Save emails
        foreach ($validated['emails'] as $email) {
            $contact->emails()->create(['email' => $email, 'user_id' => Auth::id()]);
        }

        $this->sendContactConsentRequest($contact, $validated['emails'][0]);

        // Save phone numbers
        foreach ($validated['phones'] as $phone) {
            $contact->numeroTelephones()->create(['numero_telephone' => $phone, 'user_id' => Auth::id()]);
        }

        // Link to activity if provided
        if ($request->filled('activite_id')) {
            $activite = Activite::findOrFail($validated['activite_id']);
            if ($activite->user_id !== Auth::id()) {
                abort(403);
            }
            $activite->contacts()->syncWithoutDetaching([$contact->id]);

            return redirect()->route('activites.show', $activite)->with('success', __('Contact created and linked to the activity successfully'));
        }

        return redirect()->route('contacts.index')->with('success', __('Contact created successfully'));
    }

    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);
        $contact->load(['emails', 'numeroTelephones', 'rendezVous']);
        $contact->loadCount('rendezVous');

        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);
        $contact->load(['emails', 'numeroTelephones']);

        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email|max:255',
            'phones' => 'required|array|min:1',
            'phones.*' => ['required', 'regex:/^[0-9+\s()-]+$/', 'max:20'],
            'rue' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:50',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:20',
            'pays' => 'nullable|string|max:255',
        ], [
            'emails.*.email' => __('The email address is not valid.'),
            'phones.*.regex' => __('The phone number must contain only digits, +, spaces, dashes and parentheses.'),
        ]);

        $contact->update($validated);

        // Sync emails: delete old ones and create new
        $contact->emails()->delete();
        foreach ($validated['emails'] as $email) {
            $contact->emails()->create(['email' => $email, 'user_id' => Auth::id()]);
        }

        // Sync phones: delete old ones and create new
        $contact->numeroTelephones()->delete();
        foreach ($validated['phones'] as $phone) {
            $contact->numeroTelephones()->create(['numero_telephone' => $phone, 'user_id' => Auth::id()]);
        }

        return redirect()->route('contacts.show', $contact)->with('success', __('Contact updated successfully'));
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);

        $contact->loadMissing('emails');
        $primaryEmail = $contact->emails->first()->email ?? null;

        if ($primaryEmail) {
            $this->sendContactDeletionNotification($contact, $primaryEmail);
        }

        $contactHash = hash('sha256', $contact->id.'|'.($primaryEmail ?? ''));
        Log::channel('rgpd')->info('Contact GDPR deletion executed', [
            'contact_hash' => $contactHash,
            'admin_id' => Auth::id(),
            'admin_email' => Auth::user()->email,
            'occurred_at' => now()->toIso8601String(),
        ]);

        $contact->delete();

        return redirect()->route('contacts.index')->with('success', __('Contact deleted successfully'));
    }

    private function sendContactConsentRequest(Contact $contact, string $email): void
    {
        try {
            Mail::to($email)->send(new ContactConsentRequestMail($contact, Auth::user()));
            Log::channel('rgpd')->info('Contact GDPR consent request sent', [
                'contact_id' => $contact->id,
                'contact_email' => $email,
                'admin_id' => Auth::id(),
                'admin_email' => Auth::user()->email,
                'version' => config('app.rgpd_version', '2026-05'),
                'sent_at' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send contact consent request: '.$e->getMessage());
        }
    }

    private function sendContactDeletionNotification(Contact $contact, string $email): void
    {
        try {
            Mail::to($email)->send(new ContactDeletionNotificationMail($contact, Auth::user(), $email));
        } catch (\Exception $e) {
            Log::error('Failed to send contact deletion notification: '.$e->getMessage());
        }
    }
}
