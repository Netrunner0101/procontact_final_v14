<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Privacy notice') }} - Pro Contact</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #fbf9f6; color: #1b1c1a; margin: 0; padding: 2rem 1rem 4rem; }
        .container { max-width: 760px; margin: 0 auto; }
        h1 { font-family: 'Manrope', sans-serif; font-size: 1.8rem; margin-bottom: 0.5rem; }
        h2 { font-family: 'Manrope', sans-serif; font-size: 1.15rem; margin: 2rem 0 0.6rem; }
        h3 { font-family: 'Manrope', sans-serif; font-size: 1rem; margin: 1.25rem 0 0.4rem; }
        p, li { line-height: 1.6; color: #44483e; }
        ul { padding-left: 1.4rem; margin: 0.5rem 0 1rem; }
        .lead { color: #75786c; margin-bottom: 1.5rem; }
        .card { background: #ffffff; border-radius: 0.85rem; padding: 2rem; box-shadow: 0 2px 8px rgba(27,28,26,0.04); }
        a { color: #843728; }
        .back { display: inline-block; margin-bottom: 1rem; font-size: 0.9rem; color: #843728; text-decoration: none; }
        .back:hover { text-decoration: underline; }
        .erasure-form {
            margin-top: 1.5rem;
            padding: 1.25rem;
            background: #fff5f5;
            border: 1px solid #ffdada;
            border-radius: 0.6rem;
        }
        .btn-danger {
            background: #ba1a1a;
            color: white;
            border: none;
            border-radius: 0.4rem;
            padding: 0.55rem 1rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-danger:hover { background: #92140f; }
        table { width: 100%; border-collapse: collapse; margin: 0.5rem 0; }
        th, td { text-align: left; padding: 0.55rem 0.7rem; border-bottom: 1px solid #efecea; font-size: 0.9rem; vertical-align: top; }
        th { background: #f5f3f0; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('portal.index', ['token' => $token]) }}" class="back">← {{ __('Back to portal') }}</a>

        <div class="card">
            <h1>{{ __('Privacy notice') }}</h1>
            <p class="lead">{{ __('How your personal data is processed when using this client portal.') }}</p>

            <h2>{{ __('1. Who controls your data') }}</h2>
            <p>
                {{ __('The data controller is :name, the independent professional who invited you to this portal.', ['name' => trim(($controller->prenom ?? '') . ' ' . ($controller->nom ?? '') . ' (' . ($controller->email ?? '—') . ')')]) }}
            </p>
            <p>
                {{ __('The portal infrastructure is operated as a data processor by Bouncy Boring ESHL Tech SRL (Belgium).') }}
            </p>

            <h2>{{ __('2. What data we process') }}</h2>
            <table>
                <thead><tr><th>{{ __('Data') }}</th><th>{{ __('Purpose') }}</th><th>{{ __('Lawful basis (GDPR Art. 6)') }}</th></tr></thead>
                <tbody>
                    <tr><td>{{ __('Name, email, phone (if provided)') }}</td><td>{{ __('Identifying you and contacting you about appointments') }}</td><td>{{ __('Legitimate interest / contract') }}</td></tr>
                    <tr><td>{{ __('Appointments and shared notes') }}</td><td>{{ __('Providing the service you requested') }}</td><td>{{ __('Contract performance') }}</td></tr>
                    <tr><td>{{ __('IP address and access events') }}</td><td>{{ __('Security and abuse prevention') }}</td><td>{{ __('Legitimate interest (security)') }}</td></tr>
                    <tr><td>{{ __('Trusted-device cookie (portal_td)') }}</td><td>{{ __('Avoid asking for the verification code on every visit') }}</td><td>{{ __('Strictly necessary — explicitly requested by you') }}</td></tr>
                </tbody>
            </table>

            <h2>{{ __('3. Cookies') }}</h2>
            <p>{{ __('We only set one cookie (portal_td) and only after you successfully verify your email. It is HttpOnly, Secure (in production), SameSite=Lax, and bound to your browser. It expires after 60 days. No tracking, advertising, or analytics cookies are used on this portal.') }}</p>

            <h2>{{ __('4. Retention') }}</h2>
            <ul>
                <li>{{ __('Client record: kept for the duration of your relationship with the professional, then on request.') }}</li>
                <li>{{ __('Access log: 12 months, then automatically purged.') }}</li>
                <li>{{ __('Expired verification codes: purged daily.') }}</li>
                <li>{{ __('Expired trusted-device records: purged 30 days after expiry.') }}</li>
            </ul>

            <h2 id="rights">{{ __('5. Your rights') }}</h2>
            <p>{{ __('Under GDPR (Art. 15–22), you have the right to access, rectify, erase, restrict, and port your data, and to object to processing. You can also lodge a complaint with the Belgian Data Protection Authority (APD/GBA).') }}</p>

            <h3>{{ __('Erase my data') }}</h3>
            <p>{{ __('You can request immediate erasure of your data. This will revoke your portal access, delete your contact record at this professional, and notify them. Anonymized audit entries may be retained for legal compliance (Art. 17(3)(b)).') }}</p>

            <div class="erasure-form">
                <form method="POST" action="{{ route('portal.erasure', ['token' => $token]) }}"
                      onsubmit="return confirm('{{ __('This will permanently delete your data and revoke access. Continue?') }}');">
                    @csrf
                    <button type="submit" class="btn-danger">
                        {{ __('Erase my data and revoke access') }}
                    </button>
                </form>
            </div>

            <h2>{{ __('6. Recipients & sub-processors') }}</h2>
            <ul>
                <li>{{ __('Hosting: Hetzner Online (Germany / Finland) — within EEA.') }}</li>
                <li>{{ __('Email delivery: Resend (EU region) — for sending verification codes.') }}</li>
            </ul>

            <h2>{{ __('7. International transfers') }}</h2>
            <p>{{ __('All processing occurs within the European Economic Area. No transfers to third countries occur.') }}</p>

            <h2>{{ __('8. Contact') }}</h2>
            <p>{{ __('For data requests, contact your professional first :email. For platform-level questions, contact privacy@procontact.app.', ['email' => $controller->email ?? '']) }}</p>

            <p class="lead" style="margin-top: 2rem;">{{ __('Last updated: April 2026.') }}</p>
        </div>
    </div>
</body>
</html>
