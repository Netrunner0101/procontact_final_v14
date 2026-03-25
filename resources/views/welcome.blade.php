<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pro Contact - Gestion de contacts professionnels</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #fbf9f6; color: #1b1c1a; }
        h1, h2, h3 { font-family: 'Manrope', sans-serif; }
        .btn-primary { background: linear-gradient(135deg, #843728, #c4816e); color: #ffffff; font-weight: 600; letter-spacing: 0.02em; transition: all 0.25s; }
        .btn-primary:hover { background: linear-gradient(135deg, #6d2a1d, #843728); box-shadow: 0 4px 16px rgba(132,55,40,0.35); transform: translateY(-1px); }
        .btn-secondary { background: #ffffff; border: 1px solid rgba(197,200,185,0.4); color: #1b1c1a; font-weight: 600; transition: all 0.25s; }
        .btn-secondary:hover { background: #f5f3f0; box-shadow: 0 2px 8px rgba(27,28,26,0.05); }
        .feature-card { background: #ffffff; border-radius: 0.75rem; box-shadow: 0 4px 20px rgba(27,28,26,0.04); transition: all 0.3s; }
        .feature-card:hover { box-shadow: 0 8px 30px rgba(27,28,26,0.08); transform: translateY(-2px); }
        .gradient-text { background: linear-gradient(135deg, #843728, #c4816e); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50" style="background: rgba(251,249,246,0.9); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(197,200,185,0.2);">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold" style="color: #1b1c1a;">Pro Contact</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-md text-sm btn-secondary">Connexion</a>
                <a href="{{ route('register') }}" class="px-5 py-2 rounded-md text-sm btn-primary">S'inscrire</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-5xl md:text-6xl font-extrabold leading-tight mb-6" style="color: #1b1c1a;">
                Gérez vos contacts<br><span class="gradient-text">professionnels</span> efficacement
            </h2>
            <p class="text-lg md:text-xl max-w-2xl mx-auto mb-10" style="color: #44483e;">
                Pro Contact simplifie la gestion de vos contacts, rendez-vous et activités. Tout ce dont vous avez besoin, au même endroit.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="px-8 py-3 rounded-md text-base btn-primary">
                    Commencer gratuitement
                </a>
                <a href="{{ route('login') }}" class="px-8 py-3 rounded-md text-base btn-secondary">
                    Se connecter
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: #1b1c1a;">Tout pour gérer vos relations</h2>
                <p class="text-lg max-w-xl mx-auto" style="color: #44483e;">Des outils puissants pour organiser et suivre vos contacts professionnels.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card p-8">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-5" style="background: rgba(132,55,40,0.08);">
                        <svg class="w-6 h-6" fill="none" stroke="#843728" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: #1b1c1a;">Gestion des contacts</h3>
                    <p style="color: #44483e;">Centralisez tous vos contacts avec leurs informations, historique et notes dans une interface intuitive.</p>
                </div>
                <!-- Feature 2 -->
                <div class="feature-card p-8">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-5" style="background: rgba(132,55,40,0.08);">
                        <svg class="w-6 h-6" fill="none" stroke="#843728" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: #1b1c1a;">Rendez-vous</h3>
                    <p style="color: #44483e;">Planifiez et suivez vos rendez-vous. Recevez des rappels et partagez les détails avec vos clients.</p>
                </div>
                <!-- Feature 3 -->
                <div class="feature-card p-8">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-5" style="background: rgba(132,55,40,0.08);">
                        <svg class="w-6 h-6" fill="none" stroke="#843728" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: #1b1c1a;">Statistiques</h3>
                    <p style="color: #44483e;">Visualisez vos performances avec des tableaux de bord et des rapports détaillés sur vos activités.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-6">
        <div class="max-w-3xl mx-auto text-center rounded-2xl p-12" style="background: #ffffff; box-shadow: 0 20px 40px rgba(27,28,26,0.05);">
            <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: #1b1c1a;">Prêt à commencer ?</h2>
            <p class="text-lg mb-8" style="color: #44483e;">Créez votre compte et commencez à gérer vos contacts professionnels dès aujourd'hui.</p>
            <a href="{{ route('register') }}" class="inline-block px-8 py-3 rounded-md text-base btn-primary">
                Créer mon compte
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 px-6" style="border-top: 1px solid rgba(197,200,185,0.2);">
        <div class="max-w-6xl mx-auto text-center">
            <p class="text-sm" style="color: #75786c;">&copy; {{ date('Y') }} Pro Contact. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
