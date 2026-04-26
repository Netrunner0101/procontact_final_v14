<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? __('My appointments') }} - Pro Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #fbf9f6;
            min-height: 100vh;
            color: #1b1c1a;
        }
        a { color: inherit; }
        button { font-family: inherit; }

        /* Top bar */
        .portal-nav {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(197,200,185,0.20);
            padding: 0.875rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .portal-nav-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .portal-nav-left { display: flex; align-items: center; gap: 0.75rem; }
        .portal-nav-right { display: flex; align-items: center; gap: 0.5rem; }
        .portal-logo {
            width: 34px; height: 34px;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #843728, #c4816e);
            display: flex; align-items: center; justify-content: center;
        }
        .portal-logo i { color: white; font-size: 0.95rem; }
        .portal-brand {
            font-family: 'Manrope', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            line-height: 1.1;
        }
        .portal-badge {
            font-size: 0.72rem;
            font-weight: 500;
            color: #843728;
            margin-top: 1px;
        }

        /* Language switcher */
        .lang-switch {
            display: inline-flex;
            background: #f3efeb;
            border-radius: 0.45rem;
            padding: 2px;
        }
        .lang-switch a {
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            color: #75786c;
            border-radius: 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .lang-switch a.active {
            background: #ffffff;
            color: #843728;
            box-shadow: 0 1px 3px rgba(27,28,26,0.06);
        }

        /* Tabs */
        .portal-tabs {
            background: #ffffff;
            border-bottom: 1px solid #efecea;
        }
        .portal-tabs-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            gap: 0.25rem;
            padding: 0 1.5rem;
            overflow-x: auto;
        }
        .portal-tab {
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #75786c;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            transition: all 0.15s;
        }
        .portal-tab:hover { color: #1b1c1a; }
        .portal-tab.active {
            color: #843728;
            border-bottom-color: #843728;
        }

        /* Container */
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 1.75rem 1.5rem 3rem;
        }

        /* Welcome / hero */
        .welcome { margin-bottom: 1.5rem; }
        .welcome h1 {
            font-family: 'Manrope', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .welcome p { color: #44483e; font-size: 0.95rem; }

        /* Cards / sections */
        .card {
            background: #ffffff;
            border-radius: 0.85rem;
            box-shadow: 0 2px 8px rgba(27,28,26,0.04);
            padding: 1.5rem;
            margin-bottom: 1.25rem;
        }
        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .section-title {
            font-family: 'Manrope', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-title i { color: #843728; }

        /* Appointment cards */
        .appt-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 0.85rem;
        }
        .appt-card {
            display: block;
            text-decoration: none;
            color: inherit;
            background: #fbf9f6;
            border: 1px solid #efecea;
            border-radius: 0.65rem;
            padding: 1rem 1.1rem;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }
        .appt-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, #843728, #c4816e);
        }
        .appt-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(27,28,26,0.06);
            border-color: #d8d3cd;
        }
        .appt-title {
            font-family: 'Manrope', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            padding-left: 0.4rem;
        }
        .appt-meta {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            color: #44483e;
            font-size: 0.82rem;
            padding-left: 0.4rem;
        }
        .appt-meta span {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .appt-meta i { color: #843728; font-size: 0.78rem; width: 12px; text-align: center; }

        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: #75786c;
        }
        .empty-state i {
            font-size: 2.25rem;
            color: #d8d3cd;
            margin-bottom: 0.75rem;
        }
        .empty-state p { font-size: 0.9rem; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.55rem 1rem;
            border: none;
            border-radius: 0.4rem;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #843728, #c4816e);
            color: white;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #6d2a1d, #843728);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #ffffff;
            color: #44483e;
            border: 1px solid #d8d3cd;
        }
        .btn-secondary:hover { background: #f5f3f0; }
        .btn-danger {
            background: #ffffff;
            color: #ba1a1a;
            border: 1px solid #f5c6c6;
        }
        .btn-danger:hover { background: #ffeded; }
        .btn-sm { padding: 0.35rem 0.7rem; font-size: 0.78rem; }
        .btn-icon {
            width: 32px; height: 32px;
            padding: 0;
            justify-content: center;
            border-radius: 0.4rem;
            background: transparent;
            border: 1px solid #efecea;
            color: #75786c;
            cursor: pointer;
        }
        .btn-icon:hover { background: #f5f3f0; color: #1b1c1a; }
        .btn-icon.danger:hover { background: #ffeded; color: #ba1a1a; border-color: #f5c6c6; }

        /* Forms */
        .form-row { margin-bottom: 0.85rem; }
        .form-row label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #44483e;
            margin-bottom: 0.35rem;
        }
        .form-control {
            width: 100%;
            padding: 0.6rem 0.85rem;
            border: 1.5px solid #efecea;
            border-radius: 0.45rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            background: #ffffff;
            transition: border-color 0.15s;
        }
        .form-control:focus {
            outline: none;
            border-color: #843728;
        }
        textarea.form-control { resize: vertical; min-height: 90px; }
        .form-control.error { border-color: #ba1a1a; }
        .error-text { color: #ba1a1a; font-size: 0.78rem; margin-top: 0.25rem; }

        /* Flash */
        .flash-success {
            background: #c0f0b8;
            color: #002204;
            padding: 0.7rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Notes */
        .note-item {
            background: #fbf9f6;
            border: 1px solid #efecea;
            border-radius: 0.55rem;
            padding: 0.95rem 1.1rem;
            margin-bottom: 0.65rem;
        }
        .note-item-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 0.4rem;
        }
        .note-item h4 {
            font-family: 'Manrope', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .note-item p {
            color: #44483e;
            line-height: 1.55;
            font-size: 0.88rem;
            white-space: pre-wrap;
        }
        .note-actions { display: flex; gap: 0.3rem; }
        .note-meta {
            font-size: 0.72rem;
            color: #75786c;
            margin-top: 0.5rem;
            display: flex;
            gap: 0.6rem;
            align-items: center;
        }
        .note-author-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 1px 7px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .note-author-badge.you { background: #e7d2cc; color: #843728; }
        .note-author-badge.pro { background: #d6e5e0; color: #2c5b4d; }

        /* Calendar */
        #calendar { font-size: 0.85rem; }
        .fc .fc-toolbar-title {
            font-family: 'Manrope', sans-serif;
            font-size: 1.1rem !important;
            font-weight: 700;
        }
        .fc .fc-button-primary {
            background: #843728 !important;
            border-color: #843728 !important;
            font-weight: 500;
            text-transform: capitalize;
        }
        .fc .fc-button-primary:hover { background: #6d2a1d !important; }
        .fc .fc-button-primary:disabled { background: #c4816e !important; opacity: 0.7; }
        .fc-event {
            background: #843728 !important;
            border-color: #843728 !important;
            cursor: pointer;
        }

        /* Modals */
        .modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(27,28,26,0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 1rem;
        }
        .modal-backdrop.open { display: flex; }
        .modal {
            background: #ffffff;
            border-radius: 0.85rem;
            max-width: 520px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 1.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .modal-title {
            font-family: 'Manrope', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
        }
        .modal-close {
            background: transparent;
            border: none;
            font-size: 1.25rem;
            color: #75786c;
            cursor: pointer;
            padding: 4px 8px;
        }
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        /* Misc */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            color: #843728;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        .back-link:hover { text-decoration: underline; }

        .two-col {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 1.25rem;
        }
        @media (max-width: 820px) {
            .two-col { grid-template-columns: 1fr; }
        }
    </style>
    @stack('head')
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
            <div class="portal-nav-right">
                <div class="lang-switch">
                    <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                    <a href="{{ route('lang.switch', 'fr') }}" class="{{ app()->getLocale() === 'fr' ? 'active' : '' }}">FR</a>
                </div>
            </div>
        </div>
    </nav>

    @isset($token)
        <div class="portal-tabs">
            <div class="portal-tabs-inner">
                <a href="{{ route('portal.index', ['token' => $token]) }}"
                   class="portal-tab {{ ($activeTab ?? '') === 'appointments' ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i> {{ __('Appointments') }}
                </a>
                <a href="{{ route('portal.templates', ['token' => $token]) }}"
                   class="portal-tab {{ ($activeTab ?? '') === 'templates' ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> {{ __('Note templates') }}
                </a>
            </div>
        </div>
    @endisset

    <div class="container">
        @if(session('success'))
            <div class="flash-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
