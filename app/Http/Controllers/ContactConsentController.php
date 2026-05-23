<?php

namespace App\Http\Controllers;

use App\Mail\ContactConsentConfirmationMail;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactConsentController extends Controller
{
    /**
     * Public landing page for the contact to review and decide.
     * Accessed via a tokenized URL sent to their email; no auth required.
     */
    public function show(string $token)
    {
        $contact = Contact::with('user', 'emails')->where('gdpr_consent_token', $token)->first();

        if (! $contact) {
            return response()->view('contact-consent.invalid', [], 404);
        }

        if ($contact->hasSignedGdprConsent()) {
            return view('contact-consent.confirmed', ['contact' => $contact]);
        }

        if ($contact->hasDeclinedGdprConsent()) {
            return view('contact-consent.declined', ['contact' => $contact]);
        }

        return view('contact-consent.show', [
            'contact' => $contact,
            'admin' => $contact->user,
            'contactEmail' => $contact->emails->first()->email ?? null,
        ]);
    }

    public function accept(Request $request, string $token)
    {
        return $this->recordDecision($request, $token, true);
    }

    public function decline(Request $request, string $token)
    {
        return $this->recordDecision($request, $token, false);
    }

    private function recordDecision(Request $request, string $token, bool $consent)
    {
        $contact = Contact::with('user', 'emails')->where('gdpr_consent_token', $token)->first();

        if (! $contact) {
            return response()->view('contact-consent.invalid', [], 404);
        }

        if ($contact->hasSignedGdprConsent() || $contact->hasDeclinedGdprConsent()) {
            return redirect()->route('contact.consent.show', ['token' => $token]);
        }

        $request->validate([
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => __('You must tick the confirmation box before recording your decision.'),
        ]);

        $version = config('app.rgpd_version', '2026-05');
        $contactEmail = $contact->emails->first()->email ?? '—';

        $contact->forceFill([
            'gdpr_consent_signed_at' => $consent ? now() : null,
            'gdpr_consent_declined_at' => $consent ? null : now(),
            'gdpr_consent_version' => $version,
            'gdpr_consent_ip' => $request->ip(),
        ])->save();

        Log::channel('rgpd')->info('Contact GDPR decision recorded', [
            'contact_id' => $contact->id,
            'contact_email' => $contactEmail,
            'admin_id' => $contact->user->id,
            'admin_email' => $contact->user->email,
            'consent' => $consent,
            'version' => $version,
            'decided_at' => now()->toIso8601String(),
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        try {
            Mail::to($contactEmail)->send(new ContactConsentConfirmationMail(
                $contact,
                $contact->user,
                $consent,
                $contactEmail,
                $request->ip(),
            ));
        } catch (\Exception $e) {
            Log::error('Failed to send contact consent confirmation: '.$e->getMessage());
        }

        return $consent
            ? view('contact-consent.confirmed', ['contact' => $contact->fresh()])
            : view('contact-consent.declined', ['contact' => $contact->fresh()]);
    }
}
