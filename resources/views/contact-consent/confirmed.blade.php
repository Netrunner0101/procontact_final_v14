<x-legal-layout :title="__('Decision recorded')">
    <h1>✅ {{ __('Decision recorded') }}</h1>
    <p class="lead">{{ __('Thank you :name. Your acceptance has been recorded on :date.', [
        'name' => trim($contact->prenom.' '.$contact->nom),
        'date' => optional($contact->gdpr_consent_signed_at)->format('Y-m-d H:i \U\T\C') ?? '—',
    ]) }}</p>

    <p>{{ __('A confirmation email signed with a SHA-256 cryptographic signature has just been sent to you (with a copy to your professional and to ProContact). Please archive it: it serves as proof of your decision.') }}</p>

    <h2>{{ __('What happens now') }}</h2>
    <ul>
        <li>{{ __('Your professional can continue to use your data for the purposes listed.') }}</li>
        <li>{{ __('You can ask at any time to access, correct or delete your data — contact your professional directly.') }}</li>
        <li>{{ __('You can lodge a complaint with the Belgian Data Protection Authority (APD/GBA, www.autoriteprotectiondonnees.be) at any time.') }}</li>
    </ul>
</x-legal-layout>
