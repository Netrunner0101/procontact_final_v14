<x-legal-layout :title="__('Refusal recorded')">
    <h1>{{ __('Refusal recorded') }}</h1>
    <p class="lead">{{ __('Your refusal has been recorded on :date. A confirmation email signed cryptographically has just been sent to you (with a copy to your professional and to ProContact).', [
        'date' => optional($contact->gdpr_consent_declined_at)->format('Y-m-d H:i \U\T\C') ?? '—',
    ]) }}</p>

    <h2>{{ __('What happens now') }}</h2>
    <p>{{ __('Your professional will be informed of your refusal. They may still be required to keep certain data when the law obliges them to (e.g. accounting), but they cannot use your data for purposes that are based on consent.') }}</p>
    <p>{{ __('You can ask at any time to have your data deleted — contact your professional directly, or write to :email.', ['email' => 'privacy@procontact.app']) }}</p>
</x-legal-layout>
