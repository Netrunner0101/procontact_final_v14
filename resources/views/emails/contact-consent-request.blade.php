<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Action required: confirm how your data is processed — Pro Contact') }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 640px; margin: 0 auto; padding: 20px; }
        .header { background-color: #843728; color: white; padding: 24px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 1.3rem; }
        .content { background-color: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; background: linear-gradient(135deg, #843728, #c4816e); color: #ffffff !important; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; margin: 16px 0; }
        .footer { font-size: 12px; color: #75786c; text-align: center; margin-top: 20px; }
        a { color: #843728; }
        .url-fallback { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 12px; word-break: break-all; color: #44483e; background: #fff; padding: 8px 10px; border: 1px dashed #c5c8b9; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('Confirm how your data is processed') }}</h1>
    </div>
    <div class="content">
        <p>{{ __('Hello :name,', ['name' => trim(($contact->prenom ?? '').' '.($contact->nom ?? '')) ?: __('there')]) }}</p>

        <p>{!! __('<strong>:admin</strong> has added you as a contact in their Pro Contact account, a CRM tool used to manage their professional activity.', ['admin' => e(trim($admin->prenom.' '.$admin->nom) ?: $admin->email)]) !!}</p>

        <p>{{ __('Before they continue using your data (name, email, phone, future appointments and notes), we ask you to review how it will be processed and to confirm or refuse.') }}</p>

        <p style="text-align: center;">
            <a href="{{ $consentUrl }}" class="button">{{ __('Review and respond') }}</a>
        </p>

        <p>{{ __('If the button does not work, copy this link into your browser:') }}</p>
        <p class="url-fallback">{{ $consentUrl }}</p>

        <p>{{ __('You can accept or refuse — your choice is recorded with a cryptographic signature for your records and ours.') }}</p>

        <p class="footer">
            {{ __('Pro Contact — Bouncy Boring ESHL Tech SRL (Belgium).') }}<br>
            {{ __('This is an automated message; please do not reply to this email.') }}<br>
            {{ __('For any privacy-related question, write to :email.', ['email' => 'privacy@procontact.app']) }}
        </p>
    </div>
</body>
</html>
