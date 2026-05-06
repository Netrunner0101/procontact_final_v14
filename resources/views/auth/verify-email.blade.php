<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Verify your email') }} - Pro Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background: #fbf9f6; color: #1b1c1a; }
        h1 { font-family: 'Manrope', sans-serif; }
        .auth-card { background: #ffffff; border-radius: 0.75rem; box-shadow: 0 20px 40px rgba(27,28,26,0.05); }
        .auth-input { background: #ffffff; border: 1px solid rgba(197,200,185,0.4); border-radius: 0.5rem; color: #1b1c1a; transition: all 0.25s; }
        .auth-input:focus { outline: none; border-color: #843728; box-shadow: 0 0 0 3px rgba(132,55,40,0.08); }
        .auth-btn { background: linear-gradient(135deg, #843728, #c4816e); color: #ffffff; border-radius: 0.375rem; font-weight: 600; letter-spacing: 0.02em; transition: all 0.25s; }
        .auth-btn:hover { background: linear-gradient(135deg, #6d2a1d, #843728); box-shadow: 0 4px 16px rgba(132,55,40,0.35); transform: translateY(-1px); }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="max-w-md w-full auth-card p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold" style="color: #1b1c1a;">{{ __('Confirm your email') }}</h1>
                <p class="mt-2" style="color: #44483e;">{{ __('Your account is created, but we still need to verify your email address before you can log in.') }}</p>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 rounded-lg text-sm font-medium" style="background: #c0f0b8; color: #002204;">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 p-3 rounded-lg text-sm font-medium" style="background: #c0f0b8; color: #002204;">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-3 rounded-lg text-sm font-medium" style="background: #ffdad6; color: #410002;">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg text-sm font-medium" style="background: #ffdad6; color: #410002;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium mb-2" style="color: #44483e;">{{ __('Email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required
                           class="w-full px-3 py-2 auth-input">
                </div>
                <button type="submit" class="w-full auth-btn py-2 px-4">
                    {{ __('Send a new confirmation email') }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm" style="color: #44483e;">
                <a href="{{ route('login') }}" style="color: #843728; font-weight: 600;" class="hover:opacity-80">{{ __('Back to login') }}</a>
            </p>
        </div>
    </div>
</body>
</html>
