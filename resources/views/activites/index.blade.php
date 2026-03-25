@extends('layouts.app')

@section('title', 'Activités - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard des Activités</h1>
            <p class="text-gray-600 mt-2">Gérez vos activités professionnelles</p>
        </div>
        <a href="{{ route('activites.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition duration-200">
            Créer Nouvelle Activité
        </a>
    </div>

    @if($activites->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($activites as $activite)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <!-- Activity Image/Icon -->
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 relative">
                        @if($activite->image)
                            <img src="{{ Storage::url($activite->image) }}" alt="{{ $activite->nom }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Information Icon -->
                        <div class="absolute top-4 right-4">
                            <a href="{{ route('activites.show', $activite) }}" 
                               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-full transition duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Activity Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $activite->nom }}</h3>
                        <p class="text-gray-600 mb-4 line-clamp-3">
                            {{ Str::limit($activite->description, 100) }}
                        </p>

                        <!-- Contact Info -->
                        <div class="space-y-2 mb-4">
                            @if($activite->email)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $activite->email }}
                                </div>
                            @endif
                            @if($activite->numero_telephone)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    {{ $activite->numero_telephone }}
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-2">
                                <a href="{{ route('activites.edit', $activite) }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition duration-200">
                                    Modifier
                                </a>
                                <form action="{{ route('activites.destroy', $activite) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition duration-200"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                            <span class="text-xs text-gray-500">
                                {{ $activite->contacts->count() }} contacts
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $activites->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="text-gray-500 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune activité</h3>
            <p class="text-gray-600 mb-4">Commencez par créer votre première activité professionnelle.</p>
            <a href="{{ route('activites.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition duration-200">
                Créer une Activité
            </a>
        </div>
    @endif
</div>
@endsection
