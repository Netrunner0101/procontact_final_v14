@extends('layouts.app')

@section('title', 'Statistiques - ' . $activite->nom . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <div class="flex items-center mb-2">
                <a href="{{ route('statistiques.index') }}" class="text-gray-500 hover:text-gray-700 mr-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Statistiques - {{ $activite->nom }}</h1>
            </div>
            <p class="text-gray-600">Analyse détaillée de l'activité</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('activites.show', $activite) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                Voir l'activité
            </a>
            <a href="{{ route('statistiques.export.activite', $activite) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                📊 Exporter Rapport
            </a>
        </div>
    </div>

    <!-- Activity Overview -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center mb-4">
            @if($activite->image)
                <img class="h-16 w-16 rounded-full object-cover mr-4" src="{{ asset('storage/' . $activite->image) }}" alt="{{ $activite->nom }}">
            @else
                <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                    <span class="text-gray-600 font-medium text-xl">{{ substr($activite->nom, 0, 1) }}</span>
                </div>
            @endif
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $activite->nom }}</h2>
                @if($activite->description)
                    <p class="text-gray-600 mt-1">{{ $activite->description }}</p>
                @endif
                <p class="text-sm text-gray-500 mt-1">Créée le {{ $activite->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Contacts</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_contacts'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Rendez-vous</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_rendez_vous'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Notes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_notes'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Rappels</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_rappels'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Monthly Trends -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Tendances mensuelles (6 derniers mois)</h2>
            <div class="h-64">
                <canvas id="monthlyTrendsChart"></canvas>
            </div>
        </div>

        <!-- Appointment Status Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Répartition des rendez-vous</h2>
            <div class="space-y-4 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded mr-3"></div>
                        <span class="text-gray-700">À venir</span>
                    </div>
                    <span class="font-semibold text-gray-900">{{ $appointmentStatus['upcoming'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-yellow-500 rounded mr-3"></div>
                        <span class="text-gray-700">Aujourd'hui</span>
                    </div>
                    <span class="font-semibold text-gray-900">{{ $appointmentStatus['today'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-gray-500 rounded mr-3"></div>
                        <span class="text-gray-700">Passés</span>
                    </div>
                    <span class="font-semibold text-gray-900">{{ $appointmentStatus['past'] }}</span>
                </div>
            </div>
            <div class="h-48">
                <canvas id="appointmentChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Contacts -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Top contacts</h2>
                <p class="text-gray-600 text-sm">Contacts avec le plus de rendez-vous</p>
            </div>
            <div class="p-6">
                @if($topContacts->count() > 0)
                    <div class="space-y-4">
                        @foreach($topContacts as $contact)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $contact->prenom }} {{ $contact->nom }}</h3>
                                    @if($contact->emails->count() > 0)
                                        <p class="text-sm text-gray-600">{{ $contact->emails->first()->email }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $contact->rendez_vous_count }} RDV
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Aucun contact trouvé</p>
                @endif
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Rendez-vous récents</h2>
                <p class="text-gray-600 text-sm">10 derniers rendez-vous</p>
            </div>
            <div class="p-6">
                @if($recentAppointments->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentAppointments as $rdv)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $rdv->titre }}</h3>
                                    <p class="text-sm text-gray-600">{{ $rdv->contact->prenom }} {{ $rdv->contact->nom }}</p>
                                    <p class="text-xs text-gray-500">{{ $rdv->date_debut->format('d/m/Y à H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    @php
                                        $isPast = $rdv->date_debut->isPast();
                                        $isToday = $rdv->date_debut->isToday();
                                        $status = $isPast ? 'Terminé' : ($isToday ? 'Aujourd\'hui' : 'À venir');
                                        $statusColor = $isPast ? 'bg-gray-100 text-gray-800' : ($isToday ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ $status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Aucun rendez-vous trouvé</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions rapides</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('rendez-vous.create', ['activite_id' => $activite->id]) }}" 
               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg text-center transition duration-200">
                Nouveau rendez-vous
            </a>
            <a href="{{ route('notes.create', ['activite_id' => $activite->id]) }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg text-center transition duration-200">
                Nouvelle note
            </a>
            <a href="{{ route('activites.show', $activite) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg text-center transition duration-200">
                Voir l'activité
            </a>
            <a href="{{ route('activites.edit', $activite) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg text-center transition duration-200">
                Modifier l'activité
            </a>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trends Chart
    const monthlyData = @json($monthlyTrends);
    const monthlyCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [
                {
                    label: 'Rendez-vous',
                    data: monthlyData.map(item => item.rendez_vous),
                    borderColor: 'rgb(147, 51, 234)',
                    backgroundColor: 'rgba(147, 51, 234, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Notes',
                    data: monthlyData.map(item => item.notes),
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Appointment Status Chart
    const appointmentData = @json($appointmentStatus);
    const appointmentCtx = document.getElementById('appointmentChart').getContext('2d');
    new Chart(appointmentCtx, {
        type: 'doughnut',
        data: {
            labels: ['À venir', 'Aujourd\'hui', 'Passés'],
            datasets: [{
                data: [appointmentData.upcoming, appointmentData.today, appointmentData.past],
                backgroundColor: ['#10b981', '#f59e0b', '#6b7280']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
@endsection
