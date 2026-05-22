<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Your GDPR consent record — Pro Contact') }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 640px; margin: 0 auto; padding: 20px; }
        .header { background-color: #843728; color: white; padding: 24px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 1.4rem; }
        .content { background-color: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
        table.record { width: 100%; border-collapse: collapse; margin: 16px 0; background: #fff; border: 1px solid #e5e2dc; }
        table.record th, table.record td { padding: 10px 12px; text-align: left; font-size: 14px; border-bottom: 1px solid #efecea; vertical-align: top; }
        table.record th { background: #f5f3f0; font-weight: 600; width: 38%; }
        .sig { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 11px; word-break: break-all; background: #fff; padding: 10px; border: 1px dashed #c5c8b9; border-radius: 6px; color: #44483e; }
        .badge-yes { display: inline-block; padding: 2px 10px; background: #c0f0b8; color: #002204; border-radius: 999px; font-weight: 600; font-size: 13px; }
        .badge-no { display: inline-block; padding: 2px 10px; background: #ffdad6; color: #410002; border-radius: 999px; font-weight: 600; font-size: 13px; }
        .footer { font-size: 12px; color: #75786c; text-align: center; margin-top: 20px; }
        a { color: #843728; }
        h2 { font-size: 1.05rem; margin: 1.5rem 0 0.5rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('Your GDPR consent has been recorded') }}</h1>
    </div>
    <div class="content">
        <p>{{ __('Hello :name,', ['name' => trim($user->prenom.' '.$user->nom) ?: $user->email]) }}</p>

        <p>{{ __('This message is the proof of consent we keep on your behalf. Please archive it — it confirms exactly what you accepted, when, and from which account.') }}</p>

        <h2>{{ __('Consent record') }}</h2>
        <table class="record">
            <tr><th>{{ __('User ID') }}</th><td>#{{ $user->id }}</td></tr>
            <tr><th>{{ __('Name') }}</th><td>{{ trim($user->prenom.' '.$user->nom) ?: '—' }}</td></tr>
            <tr><th>{{ __('Email') }}</th><td>{{ $user->email }}</td></tr>
            <tr><th>{{ __('Accepted at (UTC)') }}</th><td>{{ $acceptedAtHuman }}</td></tr>
            <tr><th>{{ __('Consent') }}</th><td>
                @if($consent)
                    <span class="badge-yes">{{ __('YES — accepted') }}</span>
                @else
                    <span class="badge-no">{{ __('NO — declined / withdrawn') }}</span>
                @endif
            </td></tr>
            <tr><th>{{ __('Document version') }}</th><td>{{ $version }}</td></tr>
            <tr><th>{{ __('Documents accepted') }}</th><td>
                <a href="{{ $privacyUrl }}">{{ __('Privacy Policy') }}</a><br>
                <a href="{{ $termsUrl }}">{{ __('Terms of Service') }}</a><br>
                <a href="{{ $cookiesUrl }}">{{ __('Cookie Policy') }}</a>
            </td></tr>
        </table>

        <h2>{{ __('Integrity signature') }}</h2>
        <p style="font-size: 13px; color: #44483e;">{{ __('Cryptographic signature (SHA-256) covering the fields above. We can use it to prove this record has not been altered.') }}</p>
        <div class="sig">{{ $signature }}</div>

        <h2>{{ __('Your rights at any time') }}</h2>
        <ul style="font-size: 14px;">
            <li>{{ __('Access / export your data — from your profile page.') }}</li>
            <li>{{ __('Rectify your data — from your profile page.') }}</li>
            <li>{{ __('Delete your account — from your profile page.') }}</li>
            <li>{{ __('Contact us at :email for any privacy-related request.', ['email' => 'privacy@procontact.app']) }}</li>
        </ul>

        <p class="footer">
            {{ __('Pro Contact — Bouncy Boring ESHL Tech SRL (Belgium).') }}<br>
            {{ __('This is an automated message; please do not reply to this email.') }}
        </p>
    </div>
</body>
</html>
