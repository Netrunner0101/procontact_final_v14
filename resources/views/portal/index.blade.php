@extends('portal.layout', ['activeTab' => 'appointments', 'pageTitle' => __('My appointments')])

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales-all.global.min.js"></script>
@endpush

@section('content')
    <div class="welcome">
        <h1>{{ __('Hello :first :last', ['first' => $contact->prenom, 'last' => $contact->nom]) }}</h1>
        <p>{{ __('Here is an overview of your appointments.') }}</p>
    </div>

    <div class="two-col">
        {{-- Upcoming appointments --}}
        <div class="card">
            <div class="section-head">
                <div class="section-title">
                    <i class="fas fa-bolt"></i> {{ __('Upcoming appointments') }}
                </div>
                <span style="font-size:0.8rem;color:#75786c;">{{ __(':count of :total', ['count' => $upcoming->count(), 'total' => $appointments->count()]) }}</span>
            </div>

            @if($upcoming->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p>{{ __('You have no upcoming appointments.') }}</p>
                </div>
            @else
                <div class="appt-grid" style="grid-template-columns: 1fr;">
                    @foreach($upcoming as $appointment)
                        <a href="{{ route('portal.appointment', ['token' => $token, 'appointmentId' => $appointment->id]) }}" class="appt-card">
                            <div class="appt-title">{{ $appointment->titre }}</div>
                            <div class="appt-meta">
                                <span><i class="fas fa-calendar"></i> {{ $appointment->date_debut->translatedFormat('l d F Y') }}</span>
                                <span><i class="fas fa-clock"></i> {{ $appointment->heure_debut->format('H:i') }} – {{ $appointment->heure_fin->format('H:i') }}</span>
                                <span><i class="fas fa-briefcase"></i> {{ $appointment->activite->nom ?? '—' }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Calendar --}}
        <div class="card">
            <div class="section-head">
                <div class="section-title">
                    <i class="fas fa-calendar-alt"></i> {{ __('Calendar') }}
                </div>
            </div>
            <div id="calendar"></div>
        </div>
    </div>

    {{-- All appointments (past + future) --}}
    @if($appointments->isNotEmpty())
        <div class="card">
            <div class="section-head">
                <div class="section-title">
                    <i class="fas fa-list"></i> {{ __('All appointments') }}
                </div>
            </div>
            <div class="appt-grid">
                @foreach($appointments->sortByDesc('date_debut') as $appointment)
                    <a href="{{ route('portal.appointment', ['token' => $token, 'appointmentId' => $appointment->id]) }}" class="appt-card">
                        <div class="appt-title">{{ $appointment->titre }}</div>
                        <div class="appt-meta">
                            <span><i class="fas fa-calendar"></i> {{ $appointment->date_debut->format('d/m/Y') }}</span>
                            <span><i class="fas fa-clock"></i> {{ $appointment->heure_debut->format('H:i') }} – {{ $appointment->heure_fin->format('H:i') }}</span>
                            <span><i class="fas fa-briefcase"></i> {{ $appointment->activite->nom ?? '—' }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @php
        $calendarEvents = $appointments->map(function ($a) use ($token) {
            return [
                'id' => $a->id,
                'title' => $a->titre,
                'start' => $a->date_debut->format('Y-m-d') . 'T' . $a->heure_debut->format('H:i:s'),
                'end' => $a->date_fin->format('Y-m-d') . 'T' . $a->heure_fin->format('H:i:s'),
                'url' => route('portal.appointment', ['token' => $token, 'appointmentId' => $a->id]),
            ];
        })->values();
    @endphp

    <script>window.__portalCalendarEvents = @json($calendarEvents);</script>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarEl = document.getElementById('calendar');
                if (!calendarEl) return;

                const events = window.__portalCalendarEvents || [];

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: '{{ app()->getLocale() }}',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,listWeek'
                    },
                    height: 'auto',
                    events: events,
                    eventDisplay: 'block',
                    dayMaxEvents: 3,
                    nowIndicator: true,
                });

                calendar.render();
            });
        </script>
    @endpush
@endsection
