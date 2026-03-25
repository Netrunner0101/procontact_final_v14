@extends('layouts.app')

@section('title', $note->titre . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $note->titre }}</h1>
                <div class="flex items-center space-x-4 mt-2">
                    @php
                        $priorityColors = [
                            'Basse' => 'bg-gray-100 text-gray-800',
                            'Normale' => 'bg-blue-100 text-blue-800',
                            'Haute' => 'bg-orange-100 text-orange-800',
                            'Urgente' => 'bg-red-100 text-red-800'
                        ];
                    @endphp
                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $priorityColors[$note->priorite] }}">
                        Priorité {{ $note->priorite }}
                    </span>
                    <span class="text-gray-600 text-sm">
                        Créée le {{ $note->created_at->format('d/m/Y à H:i') }}
                    </span>
                    @if($note->updated_at != $note->created_at)
                        <span class="text-gray-600 text-sm">
                            • Modifiée le {{ $note->updated_at->format('d/m/Y à H:i') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('notes.edit', $note) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Modifier
                </a>
                <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?')">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="prose max-w-none">
                        {!! nl2br(e($note->contenu)) !!}
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Associated Items -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Éléments associés</h3>
                    
                    @if($note->contact || $note->activite || $note->rendezVous)
                        <div class="space-y-4">
                            @if($note->contact)
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <h4 class="font-medium text-gray-900 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Contact
                                    </h4>
                                    <p class="text-gray-600">{{ $note->contact->prenom }} {{ $note->contact->nom }}</p>
                                    @if($note->contact->emails->count() > 0)
                                        <p class="text-sm text-gray-500">{{ $note->contact->emails->first()->email }}</p>
                                    @endif
                                    <div class="mt-2">
                                        <a href="{{ route('contacts.show', $note->contact) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm">Voir le contact</a>
                                    </div>
                                </div>
                            @endif

                            @if($note->activite)
                                <div class="border-l-4 border-green-500 pl-4">
                                    <h4 class="font-medium text-gray-900 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Activité
                                    </h4>
                                    <p class="text-gray-600">{{ $note->activite->nom }}</p>
                                    @if($note->activite->description)
                                        <p class="text-sm text-gray-500">{{ Str::limit($note->activite->description, 60) }}</p>
                                    @endif
                                    <div class="mt-2">
                                        <a href="{{ route('activites.show', $note->activite) }}" 
                                           class="text-green-600 hover:text-green-800 text-sm">Voir l'activité</a>
                                    </div>
                                </div>
                            @endif

                            @if($note->rendezVous)
                                <div class="border-l-4 border-purple-500 pl-4">
                                    <h4 class="font-medium text-gray-900 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Rendez-vous
                                    </h4>
                                    <p class="text-gray-600">{{ $note->rendezVous->titre }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $note->rendezVous->date_debut->format('d/m/Y') }} à {{ $note->rendezVous->heure_debut->format('H:i') }}
                                    </p>
                                    <div class="mt-2">
                                        <a href="{{ route('rendez-vous.show', $note->rendezVous) }}" 
                                           class="text-purple-600 hover:text-purple-800 text-sm">Voir le rendez-vous</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Aucun élément associé</p>
                    @endif
                </div>

                <!-- Note Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Priorité</label>
                            <p class="text-gray-900">{{ $note->priorite }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Date de création</label>
                            <p class="text-gray-900">{{ $note->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        @if($note->updated_at != $note->created_at)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Dernière modification</label>
                                <p class="text-gray-900">{{ $note->updated_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nombre de caractères</label>
                            <p class="text-gray-900">{{ strlen($note->contenu) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nombre de mots</label>
                            <p class="text-gray-900">{{ str_word_count($note->contenu) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                    <div class="space-y-3">
                        <a href="{{ route('notes.create') }}" 
                           class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            Nouvelle note
                        </a>
                        
                        @if($note->contact)
                            <a href="{{ route('notes.create', ['contact_id' => $note->contact->id]) }}" 
                               class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded transition duration-200">
                                Note pour ce contact
                            </a>
                        @endif
                        
                        @if($note->activite)
                            <a href="{{ route('notes.create', ['activite_id' => $note->activite->id]) }}" 
                               class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded transition duration-200">
                                Note pour cette activité
                            </a>
                        @endif
                        
                        @if($note->rendezVous)
                            <a href="{{ route('notes.create', ['rendez_vous_id' => $note->rendezVous->id]) }}" 
                               class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center px-4 py-2 rounded transition duration-200">
                                Note pour ce rendez-vous
                            </a>
                        @endif
                        
                        <a href="{{ route('notes.index') }}" 
                           class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            Toutes les notes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
