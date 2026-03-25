<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mot de passe oubli&eacute; - Pro Contact</title>
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
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full auth-card p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold" style="color: #1b1c1a;">Pro Contact</h1>
                <p class="mt-2" style="color: #44483e;">R&eacute;initialisation du mot de passe</p>
            </div>

            @if (session('status'))
                <div class="mb-4 p-4 rounded-lg text-sm font-medium" style="background: #c0f0b8; color: #002204;">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-6 text-sm" style="color: #44483e;">
                <p>Vous avez oubli&eacute; votre mot de passe ? Pas de probl&egrave;me. Indiquez-nous simplement votre adresse email et nous vous enverrons un lien de r&eacute;initialisation.</p>
            </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium mb-2" style="color: #44483e;">Adresse email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-3 py-2 auth-input @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full auth-btn py-2 px-4 mb-4">
                    Envoyer le lien de r&eacute;initialisation
                </button>
            </form>

            <div class="text-center">
                <p style="color: #44483e;">
                    Vous vous souvenez de votre mot de passe ?
                    <a href="{{ route('login') }}" style="color: #843728; font-weight: 600;" class="hover:opacity-80">Se connecter</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
