@extends('layouts.client')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Bonjour {{ Auth::user()->prenom }} {{ Auth::user()->nom }}
        </h1>
        <p class="text-gray-600 mt-2">Voici un aperçu de vos rendez-vous</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Appointments -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Total Rendez-vous</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalAppointmentsCount }}</p>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">À venir</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $upcomingAppointments->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Past Appointments -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Passés</h3>
                    <p class="text-2xl font-bold text-gray-600">{{ $pastAppointmentsCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Appointments Section -->
    @if($upcomingAppointments->count() > 0)
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Prochains rendez-vous</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($upcomingAppointments as $appointment)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ $appointment->titre }}</h3>
                        <p class="text-sm text-gray-600">{{ $appointment->activite->nom }}</p>
                        <p class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($appointment->date_heure)->format('d/m/Y à H:i') }}
                            <span class="text-blue-600">({{ \Carbon\Carbon::parse($appointment->date_heure)->diffForHumans() }})</span>
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('client.appointment', $appointment) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm transition duration-200">
                            Voir détails
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Appointments -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900">Historique des rendez-vous</h2>
            <a href="{{ route('client.appointments') }}" 
               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Voir tous →
            </a>
        </div>
        <div class="p-6">
            @if($appointments->count() > 0)
            <div class="space-y-4">
                @foreach($appointments->take(5) as $appointment)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ $appointment->titre }}</h3>
                        <p class="text-sm text-gray-600">{{ $appointment->activite->nom }}</p>
                        <p class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($appointment->date_heure)->format('d/m/Y à H:i') }}
                            @if(\Carbon\Carbon::parse($appointment->date_heure)->isPast())
                                <span class="text-gray-500">(Terminé)</span>
                            @else
                                <span class="text-green-600">(À venir)</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('client.appointment', $appointment) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Voir détails
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun rendez-vous</h3>
                <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore de rendez-vous planifiés.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
