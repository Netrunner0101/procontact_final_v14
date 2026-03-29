<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('New Password') }} - Pro Contact</title>
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
    <!-- Language Switcher -->
    <div class="fixed top-4 right-4 z-50 flex gap-2">
        <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 text-sm rounded-lg {{ app()->getLocale() === 'en' ? 'bg-white text-gray-900 font-bold' : 'text-white/70 hover:text-white' }} transition-colors">EN</a>
        <a href="{{ route('lang.switch', 'fr') }}" class="px-3 py-1 text-sm rounded-lg {{ app()->getLocale() === 'fr' ? 'bg-white text-gray-900 font-bold' : 'text-white/70 hover:text-white' }} transition-colors">FR</a>
    </div>

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full auth-card p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold" style="color: #1b1c1a;">Pro Contact</h1>
                <p class="mt-2" style="color: #44483e;">{{ __('Create a new password') }}</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium mb-2" style="color: #44483e;">{{ __('Email address') }}</label>
                    <input type="email" id="email" name="email" value="{{ request()->get('email') ?? old('email') }}" required
                           class="w-full px-3 py-2 auth-input @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium mb-2" style="color: #44483e;">{{ __('New password') }}</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 auth-input @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p>
                    @enderror
                    <p class="text-sm mt-1" style="color: #75786c;">{{ __('Minimum 8 characters') }}</p>
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium mb-2" style="color: #44483e;">{{ __('Confirm password') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-3 py-2 auth-input">
                </div>

                <button type="submit" class="w-full auth-btn py-2 px-4">
                    {{ __('Reset password') }}
                </button>
            </form>

            <div class="text-center mt-6">
                <p style="color: #44483e;">
                    <a href="{{ route('login') }}" style="color: #843728; font-weight: 600;" class="hover:opacity-80">{{ __('Back to login') }}</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmation = this.value;
            if (password !== confirmation && confirmation.length > 0) {
                this.setCustomValidity('{{ __("Passwords do not match") }}');
                this.style.borderColor = '#ba1a1a';
            } else {
                this.setCustomValidity('');
                this.style.borderColor = 'rgba(197,200,185,0.4)';
            }
        });
    </script>
</body>
</html>
