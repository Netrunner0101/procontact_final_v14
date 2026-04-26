<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Password Reset') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #1d4ed8;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pro Contact</h1>
        <p>{{ __('Reset your password') }}</p>
    </div>

    <div class="content">
        <h2>{{ __('Hello :first :last,', ['first' => $user->prenom, 'last' => $user->nom]) }}</h2>

        <p>{{ __('You are receiving this email because we received a password reset request for your account.') }}</p>

        <p>{{ __('Click the button below to reset your password:') }}</p>

        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">{{ __('Reset my password') }}</a>
        </div>

        <div class="warning">
            <strong>{{ __('Important:') }}</strong> {{ __('This link will expire in 1 hour for security reasons.') }}
        </div>

        <p>{{ __('If you did not request this reset, simply ignore this email. Your current password will remain unchanged.') }}</p>

        <p>{{ __('If you have trouble clicking the button, copy and paste the following URL into your browser:') }}</p>
        <p style="word-break: break-all; color: #2563eb;">{{ $resetUrl }}</p>

        <div class="footer">
            <p>{{ __('Best regards,') }}<br>{{ __('The Pro Contact Team') }}</p>
            <p><small>{!! __('Need help? Email us at <a href="mailto:contact@procontact.app">contact@procontact.app</a>.') !!}</small></p>
        </div>
    </div>
</body>
</html>
