<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription - Pro Contact</title>
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
        .social-btn:hover { background: #f5f3f0; }
        .divider-line { border-color: rgba(197,200,185,0.3); }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="max-w-md w-full auth-card p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold" style="color: #1b1c1a;">Pro Contact</h1>
                <p class="mt-2" style="color: #44483e;">Cr&eacute;ez votre compte</p>
            </div>

            @if (session('status'))
                <div class="mb-4 p-4 rounded-lg text-sm font-medium" style="background: #c0f0b8; color: #002204;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-4">
                    <label for="nom" class="block text-sm font-medium mb-2" style="color: #44483e;">Nom</label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                           class="w-full px-3 py-2 auth-input @error('nom') border-red-500 @enderror">
                    @error('nom')
                        <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="prenom" class="block text-sm font-medium mb-2" style="color: #44483e;">Pr&eacute;nom</label>
                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required
                           class="w-full px-3 py-2 auth-input @error('prenom') border-red-500 @enderror">
                    @error('prenom')
                        <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium mb-2" style="color: #44483e;">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 auth-input @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="telephone" class="block text-sm font-medium mb-2" style="color: #44483e;">T&eacute;l&eacute;phone (optionnel)</label>
                    <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}"
                           class="w-full px-3 py-2 auth-input @error('telephone') border-red-500 @enderror">
                    @error('telephone')
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

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium mb-2" style="color: #44483e;">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-3 py-2 auth-input">
                </div>

                <button type="submit" class="w-full auth-btn py-2 px-4">
                    Cr&eacute;er mon compte
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm" style="color: #44483e;">
                    D&eacute;j&agrave; un compte ?
                    <a href="{{ route('login') }}" style="color: #843728; font-weight: 600;" class="hover:opacity-80">Se connecter</a>
                </p>
            </div>

            <!-- Social Registration -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t divider-line"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2" style="background: #ffffff; color: #75786c;">Ou s'inscrire avec</span>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center px-4 py-2 social-btn text-sm font-medium" style="color: #44483e;">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        S'inscrire avec Google
                    </a>

                    <a href="{{ route('auth.apple') }}" class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition duration-200" style="background: #1b1c1a; border-radius: 0.375rem;">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.017 0C8.396 0 8.025.044 6.79.207 5.56.37 4.703.644 3.967 1.006c-.757.372-1.4.861-2.04 1.5-.64.64-1.128 1.283-1.5 2.04C.044 5.282-.23 6.14.207 7.37.37 8.604.044 8.975.044 12.596s.326 3.992.489 5.227c.163 1.23.437 2.088.799 2.825.372.757.861 1.4 1.5 2.04.64.64 1.283 1.128 2.04 1.5.737.362 1.595.636 2.825.799 1.235.163 1.606.207 5.227.207s3.992-.044 5.227-.207c1.23-.163 2.088-.437 2.825-.799.757-.372 1.4-.861 2.04-1.5.64-.64 1.128-1.283 1.5-2.04.362-.737.636-1.595.799-2.825.163-1.235.207-1.606.207-5.227s-.044-3.992-.207-5.227c-.163-1.23-.437-2.088-.799-2.825-.372-.757-.861-1.4-1.5-2.04-.64-.64-1.283-1.128-2.04-1.5-.737-.362-1.595-.636-2.825-.799C15.009.044 14.638 0 11.017 0h1z"/>
                        </svg>
                        S'inscrire avec Apple
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
