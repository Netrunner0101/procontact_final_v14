<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <title>{{ $locale === 'en' ? 'Your ProContact access code' : "Votre code d'accès ProContact" }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #fbf9f6; color: #1b1c1a; margin: 0; padding: 24px; }
        .wrap { max-width: 540px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(27,28,26,0.05); }
        .brand { font-size: 18px; font-weight: 700; color: #843728; margin-bottom: 24px; }
        h1 { font-size: 22px; margin: 0 0 16px; }
        p { line-height: 1.55; color: #44483e; margin: 0 0 16px; }
        .code { display: inline-block; padding: 16px 24px; font-size: 28px; font-weight: 700; letter-spacing: 8px; background: #f5f3f0; border-radius: 8px; color: #1b1c1a; font-family: 'Courier New', monospace; margin: 12px 0 20px; }
        .meta { font-size: 13px; color: #75786c; margin-top: 24px; padding-top: 16px; border-top: 1px solid #efecea; }
        a { color: #843728; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="brand">Pro Contact</div>

        @if($locale === 'en')
            <h1>Your access code</h1>
            <p>Hello {{ $contact->prenom }},</p>
            <p>Use this code to verify your email and access your client portal:</p>
            <div class="code">{{ $code }}</div>
            <p>This code expires in <strong>{{ $ttlMinutes }} minutes</strong> and can only be used once.</p>
            <p class="meta">
                If you didn't request this code, simply ignore this email — no action is needed and no one can access your portal without it.
            </p>
        @else
            <h1>Votre code d'accès</h1>
            <p>Bonjour {{ $contact->prenom }},</p>
            <p>Utilisez ce code pour vérifier votre adresse e-mail et accéder à votre portail client :</p>
            <div class="code">{{ $code }}</div>
            <p>Ce code expire dans <strong>{{ $ttlMinutes }} minutes</strong> et ne peut être utilisé qu'une seule fois.</p>
            <p class="meta">
                Si vous n'avez pas demandé ce code, ignorez simplement cet e-mail — aucune action n'est nécessaire et personne ne peut accéder à votre portail sans ce code.
            </p>
        @endif
    </div>
</body>
</html>
