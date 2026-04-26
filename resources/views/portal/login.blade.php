<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Portal access') }} - Pro Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #fbf9f6;
            min-height: 100vh;
            color: #1b1c1a;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
        }
        .topbar-brand { display: flex; align-items: center; gap: 0.75rem; }
        .logo {
            width: 36px; height: 36px;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #843728, #c4816e);
            display: flex; align-items: center; justify-content: center;
        }
        .logo i { color: white; }
        .brand { font-family: 'Manrope', sans-serif; font-weight: 700; font-size: 1.25rem; }
        .badge { font-size: 0.75rem; color: #843728; }
        .lang-switch {
            display: inline-flex;
            background: #f3efeb;
            border-radius: 0.45rem;
            padding: 2px;
        }
        .lang-switch a {
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            color: #75786c;
            border-radius: 0.35rem;
            text-transform: uppercase;
        }
        .lang-switch a.active {
            background: #ffffff;
            color: #843728;
            box-shadow: 0 1px 3px rgba(27,28,26,0.06);
        }

        .wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .card {
            background: #ffffff;
            max-width: 460px;
            width: 100%;
            border-radius: 1rem;
            padding: 2.25rem;
            box-shadow: 0 12px 36px rgba(27,28,26,0.06);
        }
        h1 {
            font-family: 'Manrope', sans-serif;
            font-size: 1.45rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .lead {
            color: #44483e;
            font-size: 0.92rem;
            line-height: 1.55;
            margin-bottom: 1.5rem;
        }
        .form-row { margin-bottom: 1rem; }
        label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #44483e;
            margin-bottom: 0.4rem;
        }
        input[type="email"], input[type="text"] {
            width: 100%;
            padding: 0.75rem 0.95rem;
            border: 1.5px solid #efecea;
            border-radius: 0.5rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            transition: border-color 0.15s;
        }
        input:focus { outline: none; border-color: #843728; }
        input.error { border-color: #ba1a1a; }
        .otp-input {
            text-align: center;
            font-size: 1.5rem !important;
            letter-spacing: 0.5rem;
            font-family: 'Courier New', monospace !important;
            font-weight: 700;
        }
        .error-text { color: #ba1a1a; font-size: 0.8rem; margin-top: 0.4rem; }
        .submit {
            width: 100%;
            padding: 0.8rem 1rem;
            background: linear-gradient(135deg, #843728, #c4816e);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .submit:hover {
            background: linear-gradient(135deg, #6d2a1d, #843728);
            transform: translateY(-1px);
        }
        .info-banner {
            display: flex;
            gap: 0.6rem;
            background: #f5f3f0;
            color: #44483e;
            border-radius: 0.5rem;
            padding: 0.7rem 0.9rem;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            line-height: 1.45;
        }
        .info-banner i { color: #843728; margin-top: 2px; }
        .footer-row {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #efecea;
            font-size: 0.78rem;
            color: #75786c;
            text-align: center;
        }
        .footer-row a {
            color: #843728;
            text-decoration: none;
            margin: 0 0.4rem;
        }
        .footer-row a:hover { text-decoration: underline; }
        .secondary-link {
            display: inline-block;
            margin-top: 0.85rem;
            font-size: 0.85rem;
            color: #843728;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .secondary-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="topbar-brand">
            <div class="logo"><i class="fas fa-user-circle"></i></div>
            <div>
                <div class="brand">Pro Contact</div>
                <div class="badge">{{ __('Client Portal') }}</div>
            </div>
        </div>
        <div class="lang-switch">
            <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
            <a href="{{ route('lang.switch', 'fr') }}" class="{{ app()->getLocale() === 'fr' ? 'active' : '' }}">FR</a>
        </div>
    </header>

    <main class="wrap">
        <div class="card">
            @if(($step ?? 'email') === 'email')
                <h1>{{ __('Verify your email') }}</h1>
                <p class="lead">{{ __('To protect your appointments, we need to verify it\'s really you. Enter your email and we\'ll send you a 6-digit code.') }}</p>

                <div class="info-banner">
                    <i class="fas fa-shield-alt"></i>
                    <span>{{ __('We will never ask for your password. Only the email registered by your provider can access this portal.') }}</span>
                </div>

                <form method="POST" action="{{ route('portal.requestOtp', ['token' => $token]) }}">
                    @csrf
                    <div class="form-row">
                        <label for="email">{{ __('Email address') }}</label>
                        <input type="email" name="email" id="email" required autofocus
                               class="{{ $errors->has('email') ? 'error' : '' }}"
                               value="{{ old('email') }}"
                               autocomplete="email">
                        @error('email')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="submit">
                        <i class="fas fa-paper-plane"></i> {{ __('Send code') }}
                    </button>
                </form>
            @else
                <h1>{{ __('Enter your code') }}</h1>
                <p class="lead">
                    {{ __('If :email is registered for this portal, we just sent a 6-digit code. The code expires in 10 minutes.', ['email' => $email ?? '']) }}
                </p>

                <form method="POST" action="{{ route('portal.verifyOtp', ['token' => $token]) }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email ?? '' }}">

                    <div class="form-row">
                        <label for="code">{{ __('Verification code') }}</label>
                        <input type="text" name="code" id="code" inputmode="numeric"
                               pattern="[0-9]{6}" maxlength="6" required autofocus autocomplete="one-time-code"
                               class="otp-input {{ $errors->has('code') ? 'error' : '' }}"
                               placeholder="000000">
                        @error('code')<p class="error-text">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="submit">
                        <i class="fas fa-check"></i> {{ __('Verify and continue') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('portal.requestOtp', ['token' => $token]) }}" style="margin-top: 0.5rem;">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email ?? '' }}">
                    <button type="submit" class="secondary-link">{{ __('Send a new code') }}</button>
                </form>

                <a href="{{ route('portal.login', ['token' => $token]) }}" class="secondary-link" style="margin-left: 0.75rem;">{{ __('Use a different email') }}</a>
            @endif

            <div class="footer-row">
                <a href="{{ route('portal.privacy', ['token' => $token]) }}">{{ __('Privacy notice') }}</a>
                ·
                <a href="{{ route('portal.privacy', ['token' => $token]) }}#rights">{{ __('Your rights') }}</a>
            </div>
        </div>
    </main>
</body>
</html>
