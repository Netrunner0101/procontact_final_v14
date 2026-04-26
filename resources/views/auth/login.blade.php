<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Login') }} - Pro Contact</title>
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
        .social-btn { background: #ffffff; border: 1px solid rgba(197,200,185,0.3); border-radius: 0.375rem; transition: all 0.25s; }
        .social-btn:hover { background: #f5f3f0; box-shadow: 0 2px 8px rgba(27,28,26,0.03); }
        .divider-line { border-color: rgba(197,200,185,0.3); }
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
                <p class="mt-2" style="color: #44483e;">{{ __('Sign in to your account') }}</p>
            </div>

            @if (session('status') || session('success'))
                <div class="mb-4 flex items-start gap-3 p-4 rounded-lg text-sm font-medium" style="background: #c0f0b8; color: #002204;" role="status">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('status') ?: session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 flex items-start gap-3 p-4 rounded-lg text-sm font-medium" style="background: #ffdad6; color: #410002;" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 flex items-start gap-3 p-4 rounded-lg text-sm font-medium" style="background: #ffdad6; color: #410002;" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium mb-2" style="color: #44483e;">{{ __('Email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 auth-input @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium mb-2" style="color: #44483e;">{{ __('Password') }}</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 auth-input @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 rounded" style="accent-color: #843728;">
                        <label for="remember" class="ml-2 block text-sm" style="color: #44483e;">
                            {{ __('Remember me') }}
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" style="color: #843728;" class="hover:opacity-80">
                            {{ __('Forgot your password?') }}
                        </a>
                    </div>
                </div>

                <button type="submit" class="w-full auth-btn py-2 px-4">
                    {{ __('Log in') }}
                </button>
            </form>

            <!-- Social Login Divider -->
            <div class="mt-6 mb-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t divider-line"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2" style="background: #ffffff; color: #75786c;">{{ __('Or continue with') }}</span>
                    </div>
                </div>
            </div>

            <!-- Social Login Buttons -->
            <div class="space-y-3">
                <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center px-4 py-2 social-btn text-sm font-medium" style="color: #44483e;">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    {{ __('Continue with Google') }}
                </a>
            </div>

            <div class="text-center mt-6">
                <p style="color: #44483e;">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" style="color: #843728; font-weight: 600;" class="hover:opacity-80">{{ __('Sign up') }}</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
