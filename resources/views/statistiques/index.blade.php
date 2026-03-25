@extends('layouts.app')

@section('title', 'Statistiques - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Statistiques</h1>
            <p class="text-gray-600 mt-2">Tableau de bord et analyses de votre activité</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('statistiques.export.global') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                📊 Exporter Rapport Global
            </a>
        </div>
    </div>

    <!-- Global Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Contacts</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalContacts }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Activités</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalActivites }}</p>
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
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalRendezVous }}</p>
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
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalRappels }}</p>
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
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalNotes }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Monthly Trends Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Tendances mensuelles (12 derniers mois)</h2>
            <div class="h-64">
                <canvas id="monthlyTrendsChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity Trends -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Tendances récentes</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-blue-900">Contacts ce mois</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $recentTrends['contacts_this_month'] }}</p>
                    </div>
                    <div class="text-right">
                        @php
                            $contactChange = $recentTrends['contacts_last_month'] > 0 
                                ? (($recentTrends['contacts_this_month'] - $recentTrends['contacts_last_month']) / $recentTrends['contacts_last_month']) * 100 
                                : ($recentTrends['contacts_this_month'] > 0 ? 100 : 0);
                        @endphp
                        <span class="text-sm {{ $contactChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $contactChange >= 0 ? '+' : '' }}{{ number_format($contactChange, 1) }}%
                        </span>
                        <p class="text-xs text-gray-500">vs mois dernier</p>
                    </div>
                </div>

                <div class="flex justify-between items-center p-4 bg-purple-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-purple-900">Rendez-vous ce mois</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $recentTrends['rendez_vous_this_month'] }}</p>
                    </div>
                    <div class="text-right">
                        @php
                            $rdvChange = $recentTrends['rendez_vous_last_month'] > 0 
                                ? (($recentTrends['rendez_vous_this_month'] - $recentTrends['rendez_vous_last_month']) / $recentTrends['rendez_vous_last_month']) * 100 
                                : ($recentTrends['rendez_vous_this_month'] > 0 ? 100 : 0);
                        @endphp
                        <span class="text-sm {{ $rdvChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $rdvChange >= 0 ? '+' : '' }}{{ number_format($rdvChange, 1) }}%
                        </span>
                        <p class="text-xs text-gray-500">vs mois dernier</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Appointment Status Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Répartition des rendez-vous</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded mr-3"></div>
                        <span class="text-gray-700">À venir</span>
                    </div>
                    <span class="font-semibold text-gray-900">{{ $appointmentStats['upcoming'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-yellow-500 rounded mr-3"></div>
                        <span class="text-gray-700">Aujourd'hui</span>
                    </div>
                    <span class="font-semibold text-gray-900">{{ $appointmentStats['today'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-gray-500 rounded mr-3"></div>
                        <span class="text-gray-700">Passés</span>
                    </div>
                    <span class="font-semibold text-gray-900">{{ $appointmentStats['past'] }}</span>
                </div>
            </div>
            <div class="mt-4">
                <canvas id="appointmentChart" height="200"></canvas>
            </div>
        </div>

        <!-- Note Priorities Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Répartition des priorités (Notes)</h2>
            <div class="space-y-4">
                @foreach(['Urgente' => 'red', 'Haute' => 'orange', 'Normale' => 'blue', 'Basse' => 'gray'] as $priority => $color)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-{{ $color }}-500 rounded mr-3"></div>
                            <span class="text-gray-700">{{ $priority }}</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $notePriorities[$priority] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                <canvas id="priorityChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Activity Performance -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Performance par activité</h2>
        </div>
        <div class="p-6">
            @if($activiteStats->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacts</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rendez-vous</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($activiteStats as $activite)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($activite->image)
                                                <img class="h-10 w-10 rounded-full object-cover mr-4" src="{{ asset('storage/' . $activite->image) }}" alt="{{ $activite->nom }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                                                    <span class="text-gray-600 font-medium">{{ substr($activite->nom, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $activite->nom }}</div>
                                                <div class="text-sm text-gray-500">{{ Str::limit($activite->description, 50) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $activite->contacts_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $activite->rendez_vous_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $activite->notes_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('statistiques.activite', $activite) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Détails</a>
                                        <a href="{{ route('statistiques.export.activite', $activite) }}" class="text-green-600 hover:text-green-900">Exporter</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Aucune activité trouvée</p>
            @endif
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trends Chart
    const monthlyData = @json($monthlyStats);
    const monthlyCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [
                {
                    label: 'Contacts',
                    data: monthlyData.map(item => item.contacts),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                },
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
    const appointmentData = @json($appointmentStats);
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

    // Priority Chart
    const priorityData = @json($notePriorities);
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    new Chart(priorityCtx, {
        type: 'doughnut',
        data: {
            labels: ['Urgente', 'Haute', 'Normale', 'Basse'],
            datasets: [{
                data: [
                    priorityData['Urgente'] || 0,
                    priorityData['Haute'] || 0,
                    priorityData['Normale'] || 0,
                    priorityData['Basse'] || 0
                ],
                backgroundColor: ['#ef4444', '#f97316', '#3b82f6', '#6b7280']
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
