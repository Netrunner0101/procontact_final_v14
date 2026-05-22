<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Action required: review our updated terms') }} - Pro Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background: #fbf9f6; color: #1b1c1a; }
        h1, h2 { font-family: 'Manrope', sans-serif; }
        .auth-card { background: #ffffff; border-radius: 0.75rem; box-shadow: 0 20px 40px rgba(27,28,26,0.05); }
        .auth-btn { background: linear-gradient(135deg, #843728, #c4816e); color: #ffffff; border-radius: 0.375rem; font-weight: 600; letter-spacing: 0.02em; transition: all 0.25s; }
        .auth-btn:hover { background: linear-gradient(135deg, #6d2a1d, #843728); }
        .auth-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .secondary-btn { background: #ffffff; border: 1px solid rgba(197,200,185,0.4); border-radius: 0.375rem; color: #44483e; }
        .secondary-btn:hover { background: #f5f3f0; }
        .info-box { background: #fff8e1; color: #6b5d00; border-radius: 0.5rem; padding: 1rem; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="max-w-xl w-full auth-card p-8">
            <h1 class="text-2xl font-bold mb-2">{{ __('We updated our terms') }}</h1>
            <p class="text-sm mb-6" style="color: #44483e;">
                {{ __('Hello :name, before continuing we need your consent on our updated Privacy Policy and Terms of Service. This is a one-time step.', ['name' => $user->prenom ?: $user->email]) }}
            </p>

            <div class="info-box mb-6">
                {{ __('Please review the documents below. They open in a new tab so you don\'t lose this page.') }}
            </div>

            <ul class="mb-6 space-y-2 text-sm">
                <li>📄 <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener" style="color:#843728;font-weight:600;">{{ __('Privacy Policy') }}</a></li>
                <li>📄 <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener" style="color:#843728;font-weight:600;">{{ __('Terms of Service') }}</a></li>
                <li>📄 <a href="{{ route('legal.cookies') }}" target="_blank" rel="noopener" style="color:#843728;font-weight:600;">{{ __('Cookie Policy') }}</a></li>
            </ul>

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg text-sm font-medium" style="background: #ffdad6; color: #410002;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('rgpd.consent.store') }}">
                @csrf
                <label class="flex items-start gap-2 text-sm mb-6" style="color: #1b1c1a;">
                    <input type="checkbox" name="terms" value="1" required class="mt-1">
                    <span>{{ __('I have read and accept the Privacy Policy and the Terms of Service.') }}</span>
                </label>

                <button type="submit" class="w-full auth-btn py-2 px-4">
                    {{ __('Accept and continue') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full secondary-btn py-2 px-4 text-sm">
                    {{ __('Sign out') }}
                </button>
            </form>

            <p class="mt-6 text-xs text-center" style="color: #75786c;">
                {{ __('If you decline, you can still export or delete your data at :email.', ['email' => 'privacy@procontact.app']) }}
            </p>
        </div>
    </div>
</body>
</html>
