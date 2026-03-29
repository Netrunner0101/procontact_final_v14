<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('My appointments') }} - Pro Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #fbf9f6;
            min-height: 100vh;
            color: #1b1c1a;
        }
        .portal-nav {
            background: rgba(255,255,255,0.70);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(197,200,185,0.10);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .portal-nav-inner {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .portal-logo {
            width: 32px; height: 32px;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #843728, #c4816e);
            display: flex; align-items: center; justify-content: center;
        }
        .portal-logo i { color: white; font-size: 0.875rem; }
        .portal-brand {
            font-family: 'Manrope', sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
        }
        .portal-badge {
            font-size: 0.75rem;
            font-weight: 500;
            color: #843728;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .welcome {
            margin-bottom: 2rem;
        }
        .welcome h1 {
            font-family: 'Manrope', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .welcome p { color: #44483e; }
        .appointment-card {
            background: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(27,28,26,0.03);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.25s ease;
            text-decoration: none;
            display: block;
            color: inherit;
        }
        .appointment-card:hover {
            box-shadow: 0 12px 24px rgba(27,28,26,0.04);
            transform: translateY(-2px);
        }
        .appointment-title {
            font-family: 'Manrope', sans-serif;
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        .appointment-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            color: #44483e;
            font-size: 0.875rem;
        }
        .appointment-meta span {
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }
        .appointment-meta i { color: #843728; }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #44483e;
        }
        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }
        .success-message {
            background: #c0f0b8;
            color: #002204;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <nav class="portal-nav">
        <div class="portal-nav-inner">
            <div class="portal-logo"><i class="fas fa-user-circle"></i></div>
            <div>
                <div class="portal-brand">Pro Contact</div>
                <div class="portal-badge">{{ __('Client Portal') }}</div>
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="welcome">
            <h1>{{ __('Hello :first :last', ['first' => $contact->prenom, 'last' => $contact->nom]) }}</h1>
            <p>{{ __('Here is the list of your appointments.') }}</p>
        </div>

        @forelse($appointments as $appointment)
            <a href="{{ route('portal.appointment', ['token' => $token, 'appointmentId' => $appointment->id]) }}" class="appointment-card">
                <div class="appointment-title">{{ $appointment->titre }}</div>
                <div class="appointment-meta">
                    <span><i class="fas fa-calendar"></i> {{ $appointment->date_debut->format('d/m/Y') }}</span>
                    <span><i class="fas fa-clock"></i> {{ $appointment->heure_debut->format('H:i') }} - {{ $appointment->heure_fin->format('H:i') }}</span>
                    <span><i class="fas fa-briefcase"></i> {{ $appointment->activite->nom }}</span>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>{{ __('No appointments') }}</h3>
                <p>{{ __('You do not have any scheduled appointments yet.') }}</p>
            </div>
        @endforelse
    </div>
</body>
</html>
