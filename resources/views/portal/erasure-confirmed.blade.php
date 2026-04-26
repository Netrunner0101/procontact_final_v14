<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Erasure request received') }} - Pro Contact</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #fbf9f6; color: #1b1c1a; margin: 0; padding: 4rem 1rem; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #ffffff; max-width: 460px; padding: 2.5rem; border-radius: 0.85rem; box-shadow: 0 8px 24px rgba(27,28,26,0.06); text-align: center; }
        h1 { font-family: 'Manrope', sans-serif; font-size: 1.5rem; margin-bottom: 0.75rem; }
        p { color: #44483e; line-height: 1.6; }
        .icon { width: 64px; height: 64px; border-radius: 50%; background: #d6e5e0; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.75rem; color: #2c5b4d; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✓</div>
        <h1>{{ __('Erasure request received') }}</h1>
        <p>{{ __('Your portal access has been revoked immediately. Your provider has been notified and will complete the erasure of your data shortly.') }}</p>
        <p style="margin-top: 1rem; font-size: 0.85rem; color: #75786c;">{{ __('You can close this page.') }}</p>
    </div>
</body>
</html>
