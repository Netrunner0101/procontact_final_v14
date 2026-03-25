@extends('layouts.app')

@section('title', 'Notes - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notes</h1>
            <p class="text-gray-600 mt-2">Gérez vos notes personnelles</p>
        </div>
        <a href="{{ route('notes.create') }}" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition duration-200">
            Nouvelle Note
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $notes->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Urgentes</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $notes->where('priorite', 'Urgente')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Haute priorité</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $notes->where('priorite', 'Haute')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Aujourd'hui</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $notes->filter(function($note) { return $note->created_at->isToday(); })->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes List -->
    <div class="bg-white rounded-lg shadow">
        @if($notes->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($notes as $note)
                    <div class="p-6 hover:bg-gray-50 transition duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <a href="{{ route('notes.show', $note) }}" class="hover:text-indigo-600">
                                            {{ $note->titre }}
                                        </a>
                                    </h3>
                                    
                                    <!-- Priority Badge -->
                                    @php
                                        $priorityColors = [
                                            'Basse' => 'bg-gray-100 text-gray-800',
                                            'Normale' => 'bg-blue-100 text-blue-800',
                                            'Haute' => 'bg-orange-100 text-orange-800',
                                            'Urgente' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $priorityColors[$note->priorite] }}">
                                        {{ $note->priorite }}
                                    </span>
                                </div>
                                
                                <p class="text-gray-600 mb-3">{{ Str::limit($note->contenu, 150) }}</p>
                                
                                <!-- Associated Items -->
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @if($note->contact)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $note->contact->prenom }} {{ $note->contact->nom }}
                                        </span>
                                    @endif
                                    
                                    @if($note->activite)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            {{ $note->activite->nom }}
                                        </span>
                                    @endif
                                    
                                    @if($note->rendezVous)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $note->rendezVous->titre }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="text-sm text-gray-500">
                                    Créée le {{ $note->created_at->format('d/m/Y à H:i') }}
                                    @if($note->updated_at != $note->created_at)
                                        • Modifiée le {{ $note->updated_at->format('d/m/Y à H:i') }}
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex space-x-2 ml-4">
                                <a href="{{ route('notes.show', $note) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 text-sm">
                                    Voir
                                </a>
                                <a href="{{ route('notes.edit', $note) }}" 
                                   class="text-blue-600 hover:text-blue-900 text-sm">
                                    Modifier
                                </a>
                                <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 text-sm"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $notes->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune note</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par créer votre première note.</p>
                <div class="mt-6">
                    <a href="{{ route('notes.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Nouvelle Note
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
