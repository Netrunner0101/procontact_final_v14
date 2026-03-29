<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $appointment->titre }} - Pro Contact</title>
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
            justify-content: space-between;
        }
        .portal-nav-left {
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
        .portal-brand { font-family: 'Manrope', sans-serif; font-weight: 700; font-size: 1.25rem; }
        .portal-badge { font-size: 0.75rem; font-weight: 500; color: #843728; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            color: #843728;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .back-link:hover { text-decoration: underline; }
        .container { max-width: 900px; margin: 0 auto; padding: 2rem; }
        .detail-card {
            background: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(27,28,26,0.03);
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .detail-title {
            font-family: 'Manrope', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }
        .detail-item label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #44483e;
            margin-bottom: 0.25rem;
        }
        .detail-item span {
            font-size: 1rem;
            color: #1b1c1a;
        }
        .detail-description {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
        }
        .detail-description label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #44483e;
            margin-bottom: 0.5rem;
        }
        .detail-description p {
            color: #1b1c1a;
            line-height: 1.6;
        }
        .section-title {
            font-family: 'Manrope', sans-serif;
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-title i { color: #843728; }
        .note-card {
            background: #f5f3f0;
            border-radius: 0.5rem;
            padding: 1rem 1.25rem;
            margin-bottom: 0.75rem;
        }
        .note-card h4 {
            font-weight: 600;
            margin-bottom: 0.375rem;
        }
        .note-card p {
            color: #44483e;
            line-height: 1.5;
            font-size: 0.9rem;
        }
        .note-date {
            font-size: 0.75rem;
            color: #75786c;
            margin-top: 0.5rem;
        }
        .empty-notes {
            color: #44483e;
            font-style: italic;
            padding: 1rem 0;
        }
        .message-form textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #efecea;
            border-radius: 0.5rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            resize: vertical;
            min-height: 100px;
            transition: border-color 0.2s;
        }
        .message-form textarea:focus {
            outline: none;
            border-color: #843728;
        }
        .message-form textarea.error {
            border-color: #ba1a1a;
        }
        .error-text {
            color: #ba1a1a;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        .submit-btn {
            margin-top: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: linear-gradient(135deg, #843728, #c4816e);
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .submit-btn:hover {
            background: linear-gradient(135deg, #6d2a1d, #843728);
            transform: translateY(-1px);
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
        @media (max-width: 640px) {
            .detail-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <nav class="portal-nav">
        <div class="portal-nav-inner">
            <div class="portal-nav-left">
                <div class="portal-logo"><i class="fas fa-user-circle"></i></div>
                <div>
                    <div class="portal-brand">Pro Contact</div>
                    <div class="portal-badge">{{ __('Client Portal') }}</div>
                </div>
            </div>
            <a href="{{ route('portal.index', ['token' => $token]) }}" class="back-link">
                <i class="fas fa-arrow-left"></i> {{ __('Back to appointments') }}
            </a>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Appointment Details -->
        <div class="detail-card">
            <h1 class="detail-title">{{ $appointment->titre }}</h1>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>{{ __('Start date') }}</label>
                    <span>{{ $appointment->date_debut->format('d/m/Y') }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ __('End date') }}</label>
                    <span>{{ $appointment->date_fin->format('d/m/Y') }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ __('Start time') }}</label>
                    <span>{{ $appointment->heure_debut->format('H:i') }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ __('End time') }}</label>
                    <span>{{ $appointment->heure_fin->format('H:i') }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ __('Activity') }}</label>
                    <span>{{ $appointment->activite->nom }}</span>
                </div>
            </div>
            @if($appointment->description)
                <div class="detail-description">
                    <label>{{ __('Description') }}</label>
                    <p>{{ $appointment->description }}</p>
                </div>
            @endif
        </div>

        <!-- Shared Notes -->
        <div class="detail-card">
            <h2 class="section-title"><i class="fas fa-sticky-note"></i> {{ __('Shared notes') }}</h2>
            @forelse($sharedNotes as $note)
                <div class="note-card">
                    <h4>{{ $note->titre }}</h4>
                    <p>{{ $note->commentaire }}</p>
                    <div class="note-date">{{ $note->date_create ? $note->date_create->format('d/m/Y H:i') : $note->created_at->format('d/m/Y H:i') }}</div>
                </div>
            @empty
                <p class="empty-notes">{{ __('No shared notes for this appointment.') }}</p>
            @endforelse
        </div>

        <!-- Leave a Message -->
        <div class="detail-card">
            <h2 class="section-title"><i class="fas fa-envelope"></i> {{ __('Leave a message for your provider') }}</h2>
            <form method="POST" action="{{ route('portal.storeNote', ['token' => $token, 'appointmentId' => $appointment->id]) }}" class="message-form">
                @csrf
                <textarea
                    name="commentaire"
                    placeholder="{{ __('Your message...') }}"
                    class="{{ $errors->has('commentaire') ? 'error' : '' }}"
                >{{ old('commentaire') }}</textarea>
                @error('commentaire')
                    <p class="error-text">{{ $message }}</p>
                @enderror
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> {{ __('Send') }}
                </button>
            </form>
        </div>
    </div>
</body>
</html>
