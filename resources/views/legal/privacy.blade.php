@php($title = __('Privacy Policy'))
<x-legal-layout :title="$title">
    <h1>{{ __('Privacy Policy') }}</h1>
    <p class="lead">{{ __('How Pro Contact processes the personal data of professional users (administrators of the CRM).') }}</p>

    <h2>{{ __('1. Data controller') }}</h2>
    <p>{{ __('Pro Contact is operated by Bouncy Boring ESHL Tech SRL, registered in Belgium. For any data protection request, contact :email.', ['email' => 'privacy@procontact.app']) }}</p>

    <h2>{{ __('2. What data we process') }}</h2>
    <table>
        <thead><tr><th>{{ __('Data') }}</th><th>{{ __('Purpose') }}</th><th>{{ __('Lawful basis (GDPR Art. 6)') }}</th></tr></thead>
        <tbody>
            <tr><td>{{ __('Name, first name, email, phone') }}</td><td>{{ __('Creating and managing your account') }}</td><td>{{ __('Contract performance') }}</td></tr>
            <tr><td>{{ __('Password (hashed) / OAuth identifier') }}</td><td>{{ __('Authentication') }}</td><td>{{ __('Contract performance') }}</td></tr>
            <tr><td>{{ __('Contacts, appointments, notes, activities you create') }}</td><td>{{ __('Providing the CRM service') }}</td><td>{{ __('Contract performance') }}</td></tr>
            <tr><td>{{ __('Login timestamps, IP address') }}</td><td>{{ __('Security and abuse prevention') }}</td><td>{{ __('Legitimate interest') }}</td></tr>
            <tr><td>{{ __('Consent timestamp at sign-up') }}</td><td>{{ __('Proof of consent (GDPR Art. 7)') }}</td><td>{{ __('Legal obligation') }}</td></tr>
        </tbody>
    </table>

    <h2>{{ __('3. Retention') }}</h2>
    <ul>
        <li>{{ __('Account data: kept for as long as your account is active.') }}</li>
        <li>{{ __('After account deletion: most data is erased immediately; anonymized audit entries may be retained for legal compliance (Art. 17(3)(b)).') }}</li>
        <li>{{ __('Expired tokens (email verification, password reset, OTP): purged automatically.') }}</li>
    </ul>

    <h2>{{ __('4. Your rights') }}</h2>
    <p>{{ __('Under the GDPR (Art. 15–22), you have the right to access, rectify, erase, restrict, and port your data, and to object to processing. You can exercise these rights at any time from your profile page:') }}</p>
    <ul>
        <li>{{ __('Export my data') }} — <code>/profile/export</code></li>
        <li>{{ __('Delete my account') }} — <code>/profile</code> ({{ __('Delete account section') }})</li>
    </ul>
    <p>{{ __('You also have the right to lodge a complaint with the Belgian Data Protection Authority (APD/GBA, www.autoriteprotectiondonnees.be).') }}</p>

    <h2>{{ __('5. Recipients & sub-processors') }}</h2>
    <ul>
        <li>{{ __('Hosting: Hetzner Online (Germany / Finland) — within EEA.') }}</li>
        <li>{{ __('Email delivery: Resend (EU region).') }}</li>
        <li>{{ __('Authentication providers (optional): Google, Apple — used only if you choose to sign in with them.') }}</li>
    </ul>

    <h2>{{ __('6. International transfers') }}</h2>
    <p>{{ __('All processing occurs within the European Economic Area. No transfers to third countries occur.') }}</p>

    <h2>{{ __('7. Contact') }}</h2>
    <p>{{ __('For any privacy-related question, write to :email.', ['email' => 'privacy@procontact.app']) }}</p>

    <p class="lead" style="margin-top: 2rem;">{{ __('Last updated: May 2026.') }}</p>
</x-legal-layout>
