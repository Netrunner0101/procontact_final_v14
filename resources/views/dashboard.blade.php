@extends('layouts.app')

@section('title', 'Dashboard - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-6 lg:py-8">
    <!-- Welcome Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl lg:text-4xl font-bold text-gradient mb-2">Dashboard</h1>
                <p class="text-gray-600 text-lg">Bienvenue, <span class="font-semibold text-gray-800">{{ auth()->user()->prenom }}</span> ! Voici un aperçu de votre activité.</p>
            </div>
            <div class="hidden lg:block">
                <div class="text-right">
                    <p class="text-sm text-gray-500">Dernière connexion</p>
                    <p class="text-sm font-medium text-gray-700">
                        {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('d/m/Y à H:i') : 'Première connexion' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 lg:gap-6 mb-8">
        <!-- Contacts Card -->
        <div class="card group cursor-pointer" onclick="window.location.href='{{ route('contacts.index') }}'">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <i class="fas fa-users text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $totalContacts }}</h3>
                                <p class="text-sm text-gray-600 font-medium">Contacts</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activités Card -->
        <div class="card group cursor-pointer" onclick="window.location.href='{{ route('activites.index') }}'">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <i class="fas fa-briefcase text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $totalActivites }}</h3>
                                <p class="text-sm text-gray-600 font-medium">Activités</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-green-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rendez-vous Card -->
        <div class="card group cursor-pointer" onclick="window.location.href='{{ route('rendez-vous.index') }}'">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <i class="fas fa-calendar-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $totalRendezVous }}</h3>
                                <p class="text-sm text-gray-600 font-medium">Rendez-vous</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Rappels Card -->
        <div class="card group cursor-pointer" onclick="window.location.href='{{ route('rappels.index') }}'">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <i class="fas fa-bell text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $totalRappels }}</h3>
                                <p class="text-sm text-gray-600 font-medium">Rappels</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-yellow-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <i class="fas fa-arrow-right"></i>
                    </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Upcoming Appointments -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Prochains Rendez-vous</h2>
            </div>
            <div class="p-6">
                @if($upcomingRendezVous->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingRendezVous as $rdv)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $rdv->titre }}</h3>
                                    <p class="text-sm text-gray-600">{{ $rdv->contact->prenom }} {{ $rdv->contact->nom }}</p>
                                    <p class="text-sm text-gray-500">{{ $rdv->activite->nom }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $rdv->date_debut->format('d/m/Y') }}</p>
                                    <p class="text-sm text-gray-600">{{ $rdv->heure_debut->format('H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucun rendez-vous à venir</p>
                @endif
            </div>
        </div>

        <!-- Upcoming Reminders -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Prochains Rappels</h2>
            </div>
            <div class="p-6">
                @if($upcomingRappels->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingRappels as $rappel)
                            <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $rappel->rendezVous->titre }}</h3>
                                    <p class="text-sm text-gray-600">{{ $rappel->rendezVous->contact->prenom }} {{ $rappel->rendezVous->contact->nom }}</p>
                                    <p class="text-sm text-gray-500">{{ $rappel->frequence }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-yellow-700">{{ $rappel->date_rappel->format('d/m/Y') }}</p>
                                    <p class="text-sm text-yellow-600">{{ $rappel->date_rappel->format('H:i') }}</p>
                                    @if($rappel->date_rappel->isFuture())
                                        <p class="text-xs text-gray-500">{{ $rappel->date_rappel->diffForHumans() }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucun rappel à venir</p>
                @endif
            </div>
        </div>

        <!-- Recent Contacts -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Contacts Récents</h2>
            </div>
            <div class="p-6">
                @if($recentContacts->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentContacts as $contact)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $contact->prenom }} {{ $contact->nom }}</h3>
                                    <p class="text-sm text-gray-600">{{ $contact->ville ?? 'Ville non renseignée' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">{{ $contact->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucun contact récent</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Actions Rapides</h2>
            <p class="text-gray-600">Créez rapidement de nouveaux éléments</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-4">
            <!-- Nouveau Contact -->
            <a href="{{ route('contacts.create') }}" class="btn btn-primary group flex-col h-auto py-4 space-y-2">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <i class="fas fa-user-plus text-white"></i>
                </div>
                <span class="text-sm font-medium">Nouveau Contact</span>
            </a>
            
            <!-- Nouvelle Activité -->
            <a href="{{ route('activites.create') }}" class="btn btn-success group flex-col h-auto py-4 space-y-2">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <i class="fas fa-briefcase text-white"></i>
                </div>
                <span class="text-sm font-medium">Nouvelle Activité</span>
            </a>
            
            <!-- Nouveau Rendez-vous -->
            <a href="{{ route('rendez-vous.create') }}" class="btn group flex-col h-auto py-4 space-y-2" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <i class="fas fa-calendar-plus text-white"></i>
                </div>
                <span class="text-sm font-medium">Nouveau RDV</span>
            </a>
            
            <!-- Nouveau Rappel -->
            <a href="{{ route('rappels.create') }}" class="btn btn-warning group flex-col h-auto py-4 space-y-2">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <i class="fas fa-bell text-white"></i>
                </div>
                <span class="text-sm font-medium">Nouveau Rappel</span>
            </a>
            
            <!-- Nouvelle Note -->
            <a href="{{ route('notes.create') }}" class="btn group flex-col h-auto py-4 space-y-2" style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: white;">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <i class="fas fa-sticky-note text-white"></i>
                </div>
                <span class="text-sm font-medium">Nouvelle Note</span>
            </a>
            
            @if(auth()->user()->isAdmin())
                <!-- Nouveau Client -->
                <a href="{{ route('admin.clients.create') }}" class="btn group flex-col h-auto py-4 space-y-2" style="background: linear-gradient(135deg, #f97316, #ea580c); color: white;">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <i class="fas fa-user-tie text-white"></i>
                    </div>
                    <span class="text-sm font-medium">Nouveau Client</span>
                </a>
            @endif
            
            <!-- Statistiques -->
            <a href="{{ route('statistiques.index') }}" class="btn btn-secondary group flex-col h-auto py-4 space-y-2">
                <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <i class="fas fa-chart-bar text-gray-600"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Statistiques</span>
            </a>
        </div>
    </div>
    
    <!-- Footer Info -->
    <div class="mt-12 text-center">
        <div class="inline-flex items-center space-x-2 text-gray-500 text-sm">
            <i class="fas fa-info-circle"></i>
            <span>Pro Contact - Système de gestion multi-entreprises</span>
        </div>
    </div>
</div>
@endsection
