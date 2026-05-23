<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Your data has been deleted — Pro Contact') }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 640px; margin: 0 auto; padding: 20px; }
        .header { background-color: #843728; color: white; padding: 24px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 1.3rem; }
        .content { background-color: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
        table.record { width: 100%; border-collapse: collapse; margin: 16px 0; background: #fff; border: 1px solid #e5e2dc; }
        table.record th, table.record td { padding: 10px 12px; text-align: left; font-size: 14px; border-bottom: 1px solid #efecea; vertical-align: top; }
        table.record th { background: #f5f3f0; font-weight: 600; width: 40%; }
        .hash { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 11px; word-break: break-all; }
        .footer { font-size: 12px; color: #75786c; text-align: center; margin-top: 20px; }
        a { color: #843728; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('Your personal data has been deleted') }}</h1>
    </div>
    <div class="content">
        <p>{{ __('Hello :name,', ['name' => $contactName]) }}</p>

        <p>{!! __('<strong>:admin</strong> has deleted you from their Pro Contact account. All your personal data (name, email, phone, appointments, notes) has been permanently removed from our active systems.', ['admin' => e($adminName)]) !!}</p>

        <table class="record">
            <tr><th>{{ __('Email') }}</th><td>{{ $contactEmail }}</td></tr>
            <tr><th>{{ __('Deleted at (UTC)') }}</th><td>{{ $deletedAtHuman }}</td></tr>
            <tr><th>{{ __('Professional (data controller)') }}</th><td>{{ $adminName }} &lt;{{ $adminEmail }}&gt;</td></tr>
            <tr><th>{{ __('Account hash (audit trail)') }}</th><td class="hash">{{ $contactHash }}</td></tr>
        </table>

        <p style="font-size: 14px;">
            <strong>{{ __('What we keep:') }}</strong>
            {{ __('Only anonymized audit entries that prove the erasure was executed, retained for legal compliance (Art. 17(3)(b)). These entries cannot be reversed back into your identity.') }}
        </p>

        <p style="font-size: 14px;">
            <strong>{{ __('Backups:') }}</strong>
            {{ __('Encrypted backups may still contain your data for up to 30 days. They are not restored to live systems and will be overwritten in the normal rotation.') }}
        </p>

        <p style="font-size: 14px;">
            {{ __('If this deletion was unexpected, please contact your professional directly at :email.', ['email' => $adminEmail]) }}
        </p>

        <p class="footer">
            {{ __('Copy sent to: the professional :admin, and to ProContact (audit).', ['admin' => $adminEmail]) }}<br>
            {{ __('Pro Contact — Bouncy Boring ESHL Tech SRL (Belgium).') }}<br>
            {{ __('This is an automated message; please do not reply to this email.') }}
        </p>
    </div>
</body>
</html>
