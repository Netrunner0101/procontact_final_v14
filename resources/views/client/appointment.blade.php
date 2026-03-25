@extends('layouts.client')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $rendezVous->titre }}</h1>
                <p class="text-gray-600 mt-2">Détails du rendez-vous</p>
            </div>
            <div>
                <a href="{{ route('client.appointments') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm transition duration-200">
                    ← Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Appointment Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations du rendez-vous</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Titre</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $rendezVous->titre }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Activité</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $rendezVous->activite->nom }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date et heure</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($rendezVous->date_heure)->format('d/m/Y à H:i') }}
                            <span class="text-blue-600">({{ \Carbon\Carbon::parse($rendezVous->date_heure)->diffForHumans() }})</span>
                        </p>
                    </div>
                    
                    @if($rendezVous->description)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $rendezVous->description }}</div>
                    </div>
                    @endif
                    
                    @if($rendezVous->lieu)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lieu</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $rendezVous->lieu }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contact Information -->
            @if($rendezVous->contact)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Contact</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $rendezVous->contact->prenom }} {{ $rendezVous->contact->nom }}</p>
                    </div>
                    
                    @if($rendezVous->contact->email)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <a href="mailto:{{ $rendezVous->contact->email }}" class="text-blue-600 hover:text-blue-800">
                                {{ $rendezVous->contact->email }}
                            </a>
                        </p>
                    </div>
                    @endif
                    
                    @if($rendezVous->contact->numeroTelephones->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <div class="mt-1 space-y-1">
                            @foreach($rendezVous->contact->numeroTelephones as $numero)
                            <p class="text-sm text-gray-900">
                                <a href="tel:{{ $numero->numero }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $numero->numero }}
                                </a>
                                @if($numero->type)
                                    <span class="text-gray-500">({{ $numero->type }})</span>
                                @endif
                            </p>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut</h3>
                <div class="text-center">
                    @if(\Carbon\Carbon::parse($rendezVous->date_heure)->isPast())
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Terminé
                        </div>
                    @elseif(\Carbon\Carbon::parse($rendezVous->date_heure)->isToday())
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Aujourd'hui
                        </div>
                    @else
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            À venir
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reminders -->
            @if($rendezVous->rappels->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Rappels</h3>
                <div class="space-y-3">
                    @foreach($rendezVous->rappels as $rappel)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            @if(\Carbon\Carbon::parse($rappel->date_heure)->isPast())
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                            @else
                                <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">{{ $rappel->titre }}</p>
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($rappel->date_heure)->format('d/m/Y à H:i') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    @if($rendezVous->contact && $rendezVous->contact->email)
                    <a href="mailto:{{ $rendezVous->contact->email }}" 
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded transition duration-200">
                        Envoyer un email
                    </a>
                    @endif
                    
                    @if($rendezVous->contact && $rendezVous->contact->numeroTelephones->count() > 0)
                    <a href="tel:{{ $rendezVous->contact->numeroTelephones->first()->numero }}" 
                       class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded transition duration-200">
                        Appeler
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
