<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Confirm your email') }}</title>
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
            background-color: #843728;
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
            background-color: #843728;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #6d2a1d;
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
        <p>{{ __('Confirm your email address') }}</p>
    </div>

    <div class="content">
        <h2>{{ __('Hello :first :last,', ['first' => $user->prenom, 'last' => $user->nom]) }}</h2>

        <p>{{ __('Thank you for signing up to Pro Contact. To finish creating your account, please confirm your email address by clicking the button below.') }}</p>

        <div style="text-align: center;">
            <a href="{{ $verifyUrl }}" class="button">{{ __('Confirm my email') }}</a>
        </div>

        <div class="warning">
            <strong>{{ __('Important:') }}</strong> {{ __('This link will expire in 24 hours. You will not be able to log in until your email is confirmed.') }}
        </div>

        <p>{{ __('If you did not create an account, you can safely ignore this email.') }}</p>

        <p>{{ __('If you have trouble clicking the button, copy and paste the following URL into your browser:') }}</p>
        <p style="word-break: break-all; color: #843728;">{{ $verifyUrl }}</p>

        <div class="footer">
            <p>{{ __('Best regards,') }}<br>{{ __('The Pro Contact Team') }}</p>
            <p><small>{!! __('Need help? Email us at <a href="mailto:contact@procontact.app">contact@procontact.app</a>.') !!}</small></p>
        </div>
    </div>
</body>
</html>
