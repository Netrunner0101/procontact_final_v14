<x-legal-layout :title="__('Confirm how your data is processed')">
    <h1>{{ __('Confirm how your data is processed') }}</h1>
    <p class="lead">{{ __('Hello :name, please review the information below and record your decision.', ['name' => trim($contact->prenom.' '.$contact->nom)]) }}</p>

    <h2>{{ __('Who manages your data') }}</h2>
    <p>{{ __('The data controller is :admin (contact: :email), the professional who added you to their Pro Contact account.', [
        'admin' => trim($admin->prenom.' '.$admin->nom) ?: $admin->email,
        'email' => $admin->email,
    ]) }}</p>
    <p>{{ __('The technical platform is operated as a data processor by Bouncy Boring ESHL Tech SRL (Belgium).') }}</p>

    <h2>{{ __('What data is processed') }}</h2>
    <table>
        <thead><tr><th>{{ __('Data') }}</th><th>{{ __('Purpose') }}</th><th>{{ __('Lawful basis (GDPR Art. 6)') }}</th></tr></thead>
        <tbody>
            <tr><td>{{ __('Name, first name, email, phone, postal address') }}</td><td>{{ __('Identifying you and contacting you') }}</td><td>{{ __('Contract performance / Legitimate interest') }}</td></tr>
            <tr><td>{{ __('Appointments, notes, activities you are linked to') }}</td><td>{{ __('Providing the service requested') }}</td><td>{{ __('Contract performance') }}</td></tr>
            <tr><td>{{ __('Consent timestamp and IP address') }}</td><td>{{ __('Proof of consent (GDPR Art. 7)') }}</td><td>{{ __('Legal obligation') }}</td></tr>
        </tbody>
    </table>

    <h2>{{ __('Your rights') }}</h2>
    <ul>
        <li>{{ __('Ask your professional to give you a copy of your data.') }}</li>
        <li>{{ __('Ask your professional to correct any inaccurate data.') }}</li>
        <li>{{ __('Ask for your data to be deleted (right to erasure, GDPR Art. 17).') }}</li>
        <li>{{ __('Lodge a complaint with the Belgian Data Protection Authority (APD/GBA).') }}</li>
    </ul>

    <h2>{{ __('Reference documents') }}</h2>
    <p>
        <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener">{{ __('Privacy Policy') }}</a>
        ·
        <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener">{{ __('Terms of Service') }}</a>
        ·
        <a href="{{ route('legal.cookies') }}" target="_blank" rel="noopener">{{ __('Cookie Policy') }}</a>
    </p>

    @if ($errors->any())
        <div style="background:#ffdad6;color:#410002;padding:1rem;border-radius:0.5rem;margin:1rem 0;font-size:0.9rem;">
            {{ $errors->first() }}
        </div>
    @endif

    <h2>{{ __('Your decision') }}</h2>
    <p>{{ __('A confirmation email signed cryptographically (SHA-256) will be sent to you, your professional and the platform.') }}</p>

    <form method="POST" action="{{ route('contact.consent.accept', ['token' => $contact->gdpr_consent_token]) }}" style="margin-top:1rem;">
        @csrf
        <label style="display:flex;align-items:flex-start;gap:0.5rem;margin-bottom:1rem;font-size:0.95rem;">
            <input type="checkbox" name="terms" value="1" required style="margin-top:4px;">
            <span>{{ __('I have read the information above and I confirm my decision.') }}</span>
        </label>
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
            <button type="submit" style="background:linear-gradient(135deg,#0a7a2a,#34a853);color:#fff;border:none;padding:0.6rem 1.2rem;border-radius:0.4rem;font-weight:600;cursor:pointer;font-family:inherit;">
                {{ __('Accept') }}
            </button>
            <button type="submit" formaction="{{ route('contact.consent.decline', ['token' => $contact->gdpr_consent_token]) }}" style="background:#fff;color:#ba1a1a;border:1px solid #ba1a1a;padding:0.6rem 1.2rem;border-radius:0.4rem;font-weight:600;cursor:pointer;font-family:inherit;">
                {{ __('Refuse') }}
            </button>
        </div>
    </form>
</x-legal-layout>
