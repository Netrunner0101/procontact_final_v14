@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? __('Legal') }} - Pro Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #fbf9f6; color: #1b1c1a; margin: 0; padding: 2rem 1rem 4rem; }
        .container { max-width: 760px; margin: 0 auto; }
        h1 { font-family: 'Manrope', sans-serif; font-size: 1.9rem; margin-bottom: 0.5rem; }
        h2 { font-family: 'Manrope', sans-serif; font-size: 1.15rem; margin: 2rem 0 0.6rem; }
        h3 { font-family: 'Manrope', sans-serif; font-size: 1rem; margin: 1.25rem 0 0.4rem; }
        p, li { line-height: 1.6; color: #44483e; }
        ul, ol { padding-left: 1.4rem; margin: 0.5rem 0 1rem; }
        .lead { color: #75786c; margin-bottom: 1.5rem; }
        .card { background: #ffffff; border-radius: 0.85rem; padding: 2rem; box-shadow: 0 2px 8px rgba(27,28,26,0.04); }
        a { color: #843728; }
        .back { display: inline-block; margin-bottom: 1rem; font-size: 0.9rem; color: #843728; text-decoration: none; }
        .back:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin: 0.5rem 0; }
        th, td { text-align: left; padding: 0.55rem 0.7rem; border-bottom: 1px solid #efecea; font-size: 0.9rem; vertical-align: top; }
        th { background: #f5f3f0; font-weight: 600; }
        .legal-nav { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #efecea; display: flex; flex-wrap: wrap; gap: 1.25rem; font-size: 0.9rem; }
        .legal-nav a { text-decoration: none; }
        .legal-nav a:hover { text-decoration: underline; }
        .lang-switcher { position: fixed; top: 1rem; right: 1rem; display: flex; gap: 0.5rem; }
        .lang-switcher a { padding: 0.25rem 0.6rem; border-radius: 0.4rem; font-size: 0.85rem; text-decoration: none; color: #75786c; }
        .lang-switcher a.active { background: #ffffff; color: #1b1c1a; font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
    </style>
</head>
<body>
    <div class="lang-switcher">
        <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
        <a href="{{ route('lang.switch', 'fr') }}" class="{{ app()->getLocale() === 'fr' ? 'active' : '' }}">FR</a>
    </div>

    <div class="container">
        <a href="{{ url('/') }}" class="back">← {{ __('Back to home') }}</a>

        <div class="card">
            {{ $slot }}

            <nav class="legal-nav">
                <a href="{{ route('legal.privacy') }}">{{ __('Privacy Policy') }}</a>
                <a href="{{ route('legal.terms') }}">{{ __('Terms of Service') }}</a>
                <a href="{{ route('legal.cookies') }}">{{ __('Cookie Policy') }}</a>
            </nav>
        </div>
    </div>
</body>
</html>
