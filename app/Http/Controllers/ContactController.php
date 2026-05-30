<?php

namespace App\Http\Controllers;

use App\Mail\ContactConsentRequestMail;
use App\Mail\ContactDeletionNotificationMail;
use App\Models\Activite;
use App\Models\Contact;
use App\Models\Pays;
use App\Services\AdresseSyncer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::with(['emails', 'numeroTelephones.pays', 'adressePrincipale'])
            ->withCount('rendezVous')
            ->where('user_id', Auth::id())
            ->paginate(15);

        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        $activites = Activite::where('user_id', Auth::id())->get();
        $selectedActiviteId = request('activite_id');
        $paysList = Pays::orderBy('nom')->get();

        return view('contacts.create', compact('activites', 'selectedActiviteId', 'paysList'));
    }

    public function store(Request $request, AdresseSyncer $adresseSyncer)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email|max:255',
            'phones' => 'required|array|min:1',
            'phones.*' => ['required', 'regex:/^[0-9+\s()-]+$/', 'max:20'],
            'phone_pays' => 'nullable|array',
            'phone_pays.*' => 'nullable|string|size:2|exists:pays,code',
            'adresses' => 'nullable|array',
            'adresses.*.rue' => 'nullable|string|max:255',
            'adresses.*.numero_rue' => 'nullable|string|max:20',
            'adresses.*.ville' => 'nullable|string|max:255',
            'adresses.*.code_postal' => 'nullable|string|max:20',
            'adresses.*.pays_code' => 'nullable|string|size:2|exists:pays,code',
            'adresses.*.is_principale' => 'nullable|boolean',
            'activite_id' => 'nullable|exists:activites,id',
        ], [
            'emails.*.email' => __('The email address is not valid.'),
            'phones.*.regex' => __('The phone number must contain only digits, +, spaces, dashes and parentheses.'),
        ]);

        $contact = Contact::create([
            'user_id' => Auth::id(),
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'gdpr_consent_token' => Str::random(64),
            'gdpr_consent_requested_at' => now(),
        ]);

        // Save emails
        foreach ($validated['emails'] as $email) {
            $contact->emails()->create(['email' => $email, 'user_id' => Auth::id()]);
        }

        $consentMailSent = $this->sendContactConsentRequest($contact, $validated['emails'][0]);

        // Save phone numbers (prefix decomposed into pays_code)
        foreach ($validated['phones'] as $i => $phone) {
            $contact->numeroTelephones()->create([
                'numero_telephone' => $phone,
                'pays_code' => $validated['phone_pays'][$i] ?? null,
                'user_id' => Auth::id(),
            ]);
        }

        $adresseSyncer->sync($contact, $validated['adresses'] ?? []);

        // Link to activity if provided
        if ($request->filled('activite_id')) {
            $activite = Activite::findOrFail($validated['activite_id']);
            if ($activite->user_id !== Auth::id()) {
                abort(403);
            }
            $activite->contacts()->syncWithoutDetaching([$contact->id]);

            $response = redirect()->route('activites.show', $activite)
                ->with('success', __('Contact created and linked to the activity successfully'));

            if (! $consentMailSent) {
                $response->with('warning', __('Contact saved, but the GDPR consent email could not be sent. Check mail configuration.'));
            }

            return $response;
        }

        $response = redirect()->route('contacts.index')->with('success', __('Contact created successfully'));

        if (! $consentMailSent) {
            $response->with('warning', __('Contact saved, but the GDPR consent email could not be sent. Check mail configuration.'));
        }

        return $response;
    }

    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);
        $contact->load(['emails', 'numeroTelephones.pays', 'rendezVous', 'adresses.pays']);
        $contact->loadCount('rendezVous');

        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);
        $contact->load(['emails', 'numeroTelephones', 'adresses.pays']);
        $paysList = Pays::orderBy('nom')->get();

        return view('contacts.edit', compact('contact', 'paysList'));
    }

    public function update(Request $request, Contact $contact, AdresseSyncer $adresseSyncer)
    {
        $this->authorize('update', $contact);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email|max:255',
            'phones' => 'required|array|min:1',
            'phones.*' => ['required', 'regex:/^[0-9+\s()-]+$/', 'max:20'],
            'phone_pays' => 'nullable|array',
            'phone_pays.*' => 'nullable|string|size:2|exists:pays,code',
            'adresses' => 'nullable|array',
            'adresses.*.rue' => 'nullable|string|max:255',
            'adresses.*.numero_rue' => 'nullable|string|max:20',
            'adresses.*.ville' => 'nullable|string|max:255',
            'adresses.*.code_postal' => 'nullable|string|max:20',
            'adresses.*.pays_code' => 'nullable|string|size:2|exists:pays,code',
            'adresses.*.is_principale' => 'nullable|boolean',
        ], [
            'emails.*.email' => __('The email address is not valid.'),
            'phones.*.regex' => __('The phone number must contain only digits, +, spaces, dashes and parentheses.'),
        ]);

        $contact->update([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
        ]);

        $adresseSyncer->sync($contact, $validated['adresses'] ?? []);

        // Sync emails: delete old ones and create new
        $contact->emails()->delete();
        foreach ($validated['emails'] as $email) {
            $contact->emails()->create(['email' => $email, 'user_id' => Auth::id()]);
        }

        // Sync phones: delete old ones and create new (prefix decomposed)
        $contact->numeroTelephones()->delete();
        foreach ($validated['phones'] as $i => $phone) {
            $contact->numeroTelephones()->create([
                'numero_telephone' => $phone,
                'pays_code' => $validated['phone_pays'][$i] ?? null,
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()->route('contacts.show', $contact)->with('success', __('Contact updated successfully'));
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);

        $contact->loadMissing('emails');
        $primaryEmail = $contact->emails->first()->email ?? null;

        $deletionMailSent = null;
        if ($primaryEmail) {
            $deletionMailSent = $this->sendContactDeletionNotification($contact, $primaryEmail);
        }

        $contactHash = hash('sha256', $contact->id.'|'.($primaryEmail ?? ''));
        Log::channel('rgpd')->info('Contact GDPR deletion executed', [
            'contact_hash' => $contactHash,
            'admin_id' => Auth::id(),
            'admin_email' => Auth::user()->email,
            'occurred_at' => now()->toIso8601String(),
        ]);

        $contact->delete();

        $response = redirect()->route('contacts.index')->with('success', __('Contact deleted successfully'));

        if ($deletionMailSent === false) {
            $response->with('warning', __('Contact deleted, but the notification email could not be sent. Check mail configuration.'));
        }

        return $response;
    }

    private function sendContactConsentRequest(Contact $contact, string $email): bool
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

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send contact consent request', [
                'contact_id' => $contact->id,
                'contact_email' => $email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function sendContactDeletionNotification(Contact $contact, string $email): bool
    {
        try {
            Mail::to($email)->send(new ContactDeletionNotificationMail($contact, Auth::user(), $email));

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send contact deletion notification', [
                'contact_id' => $contact->id,
                'contact_email' => $email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
