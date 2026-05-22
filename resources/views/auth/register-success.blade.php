<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Account created') }} - Pro Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background: #fbf9f6; color: #1b1c1a; }
        h1 { font-family: 'Manrope', sans-serif; }
        .auth-card { background: #ffffff; border-radius: 0.75rem; box-shadow: 0 20px 40px rgba(27,28,26,0.05); }
        .auth-btn { background: linear-gradient(135deg, #843728, #c4816e); color: #ffffff; border-radius: 0.375rem; font-weight: 600; letter-spacing: 0.02em; transition: all 0.25s; }
        .auth-btn:hover { background: linear-gradient(135deg, #6d2a1d, #843728); box-shadow: 0 4px 16px rgba(132,55,40,0.35); transform: translateY(-1px); }
        .secondary-btn { background: #ffffff; border: 1px solid rgba(197,200,185,0.4); border-radius: 0.375rem; transition: all 0.25s; color: #44483e; }
        .secondary-btn:hover { background: #f5f3f0; }
        .badge-success { background: #c0f0b8; color: #002204; }
    </style>
</head>
<body>
    <div class="fixed top-4 right-4 z-50 flex gap-2">
        <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 text-sm rounded-lg {{ app()->getLocale() === 'en' ? 'bg-white text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-900' }} transition-colors">EN</a>
        <a href="{{ route('lang.switch', 'fr') }}" class="px-3 py-1 text-sm rounded-lg {{ app()->getLocale() === 'fr' ? 'bg-white text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-900' }} transition-colors">FR</a>
    </div>

    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="max-w-md w-full auth-card p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4" style="background: #c0f0b8;">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #002204;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold" style="color: #1b1c1a;">{{ __('Check your inbox') }}</h1>
                <p class="mt-3" style="color: #44483e;">
                    {{ __('We just sent a confirmation link to:') }}
                </p>
                <p class="mt-1 font-semibold break-all" style="color: #843728;">{{ $email }}</p>
            </div>

            <div class="mb-6 p-4 rounded-lg text-sm" style="background: #f5f3f0; color: #44483e;">
                <p class="font-medium mb-2" style="color: #1b1c1a;">{{ __('What happens next?') }}</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>{{ __('Open the email from Pro Contact.') }}</li>
                    <li>{{ __('Click the confirmation link.') }}</li>
                    <li>{{ __('Log in and start using your account.') }}</li>
                </ol>
            </div>

            <div class="mb-6 p-3 rounded-lg text-xs" style="background: #fff8e1; color: #6b5d00;">
                {{ __('The link expires in 24 hours. Until your email is confirmed, you will not be able to log in.') }}
            </div>

            <div class="mb-6 p-4 rounded-lg text-xs" style="background: #f5f3f0; color: #44483e;">
                <p class="font-medium mb-1" style="color: #1b1c1a;">{{ __('GDPR consent recorded') }}</p>
                <p>{{ __('We just emailed you a signed record of your consent (user, email, date, accepted documents). Please keep it for your records. Review the documents any time:') }}</p>
                <p class="mt-2">
                    <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener" style="color: #843728; font-weight: 600;">{{ __('Privacy Policy') }}</a>
                    ·
                    <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener" style="color: #843728; font-weight: 600;">{{ __('Terms of Service') }}</a>
                    ·
                    <a href="{{ route('legal.cookies') }}" target="_blank" rel="noopener" style="color: #843728; font-weight: 600;">{{ __('Cookie Policy') }}</a>
                </p>
            </div>

            @if (session('status'))
                <div class="mb-4 flex items-start gap-3 p-3 rounded-lg text-sm font-medium badge-success">
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 flex items-start gap-3 p-3 rounded-lg text-sm font-medium" style="background: #ffdad6; color: #410002;">
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('verification.resend') }}" class="mb-3">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" class="w-full secondary-btn py-2 px-4 text-sm font-medium">
                    {{ __('Resend the confirmation email') }}
                </button>
            </form>

            <a href="{{ route('login') }}" class="block w-full text-center auth-btn py-2 px-4">
                {{ __('Go to login') }}
            </a>

            <p class="mt-6 text-center text-xs" style="color: #75786c;">
                {{ __("Didn't receive anything? Check your spam folder, or") }}
                <a href="{{ route('register') }}" style="color: #843728; font-weight: 600;" class="hover:opacity-80">{{ __('try again with a different address') }}</a>.
            </p>
        </div>
    </div>
</body>
</html>
