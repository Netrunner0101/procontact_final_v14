@extends('layouts.client')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mes rendez-vous</h1>
        <p class="text-gray-600 mt-2">Consultez tous vos rendez-vous passés et à venir</p>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="{{ route('client.appointments') }}" class="flex flex-col md:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Titre, description, activité..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Status Filter -->
            <div class="md:w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select id="status" 
                        name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>À venir</option>
                    <option value="today" {{ request('status') === 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                    <option value="past" {{ request('status') === 'past' ? 'selected' : '' }}>Passés</option>
                </select>
            </div>
            
            <!-- Submit Button -->
            <div class="md:w-32 flex items-end">
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Appointments List -->
    <div class="bg-white rounded-lg shadow">
        @if($appointments->count() > 0)
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $appointments->total() }} rendez-vous trouvé(s)
                </h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                @foreach($appointments as $appointment)
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $appointment->titre }}</h3>
                                @if(\Carbon\Carbon::parse($appointment->date_heure)->isPast())
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                        Terminé
                                    </span>
                                @elseif(\Carbon\Carbon::parse($appointment->date_heure)->isToday())
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        Aujourd'hui
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        À venir
                                    </span>
                                @endif
                            </div>
                            
                            <div class="mt-2 space-y-1">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Activité:</span> {{ $appointment->activite->nom }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Date:</span> 
                                    {{ \Carbon\Carbon::parse($appointment->date_heure)->format('d/m/Y à H:i') }}
                                    <span class="text-blue-600">({{ \Carbon\Carbon::parse($appointment->date_heure)->diffForHumans() }})</span>
                                </p>
                                @if($appointment->description)
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Description:</span> 
                                    {{ Str::limit($appointment->description, 100) }}
                                </p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex-shrink-0 ml-6">
                            <a href="{{ route('client.appointment', $appointment) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm transition duration-200">
                                Voir détails
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $appointments->withQueryString()->links() }}
            </div>
        @else
            <div class="p-6">
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun rendez-vous trouvé</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'status']))
                            Aucun rendez-vous ne correspond à vos critères de recherche.
                        @else
                            Vous n'avez pas encore de rendez-vous planifiés.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status']))
                        <div class="mt-4">
                            <a href="{{ route('client.appointments') }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Effacer les filtres
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
