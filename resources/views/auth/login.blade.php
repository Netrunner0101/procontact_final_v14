<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Pro Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full auth-card p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold" style="color: #1b1c1a;">Pro Contact</h1>
                <p class="mt-2" style="color: #44483e;">Connectez-vous &agrave; votre compte</p>
            </div>

            @if (session('status'))
                <div class="mb-4 p-4 rounded-lg text-sm font-medium" style="background: #c0f0b8; color: #002204;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium mb-2" style="color: #44483e;">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 auth-input @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium mb-2" style="color: #44483e;">Mot de passe</label>
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
                            Se souvenir de moi
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" style="color: #843728;" class="hover:opacity-80">
                            Mot de passe oubli&eacute; ?
                        </a>
                    </div>
                </div>

                <button type="submit" class="w-full auth-btn py-2 px-4">
                    Se connecter
                </button>
            </form>

            <!-- Social Login Divider -->
            <div class="mt-6 mb-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t divider-line"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2" style="background: #ffffff; color: #75786c;">Ou continuer avec</span>
                    </div>
                </div>
            </div>

            <!-- Social Login Buttons -->
            <div class="space-y-3">
                <a href="{{ route('mock.oauth.google.show') }}" class="w-full flex items-center justify-center px-4 py-2 social-btn text-sm font-medium" style="color: #44483e;">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continuer avec Google (Test)
                </a>

                <a href="{{ route('auth.apple') }}" class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition duration-200" style="background: #1b1c1a; border-radius: 0.375rem;">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.017 0C8.396 0 8.025.044 8.025.044c0 0-.396 2.24 1.537 4.477C11.497 6.76 13.5 6.76 13.5 6.76s2.003 0 3.938-2.24C19.371 2.284 18.975.044 18.975.044S18.604 0 15.983 0h-3.966zm2.523 7.847c-1.277 0-2.297.02-2.297.02-.277 0-.5.223-.5.5v8.133c0 .277.223.5.5.5h4.594c.277 0 .5-.223.5-.5V8.367c0-.277-.223-.5-.5-.5h-2.297z"/>
                    </svg>
                    Continuer avec Apple
                </a>
            </div>

            <div class="text-center mt-6">
                <p style="color: #44483e;">
                    Pas de compte ?
                    <a href="{{ route('register') }}" style="color: #843728; font-weight: 600;" class="hover:opacity-80">S'inscrire</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
