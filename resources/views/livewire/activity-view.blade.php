<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header with Back Button -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg transition-colors" style="background: #f5f3f0; color: #44483e;">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold" style="color: #1b1c1a;">{{ $activity->nom }}</h1>
                        @if($activity->description)
                            <p class="mt-1" style="color: #44483e;">{{ $activity->description }}</p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('activites.edit', $activity->id) }}" class="inline-flex items-center px-4 py-2 text-white rounded-lg transition-colors" style="background: #843728;">
                    <i class="fas fa-edit mr-2"></i>
                    Modifier
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="rounded-lg shadow p-6" style="background: white; border: none;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm" style="color: #44483e;">Contacts</p>
                        <p class="text-3xl font-bold" style="color: #843728;">{{ $activity->contacts->count() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: #fef5f3;">
                        <i class="fas fa-users text-xl" style="color: #843728;"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-lg shadow p-6" style="background: white; border: none;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm" style="color: #44483e;">Rendez-vous</p>
                        <p class="text-3xl font-bold" style="color: #3a6a3a;">{{ $activity->rendezVous->count() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: #f0faf0;">
                        <i class="fas fa-calendar-alt text-xl" style="color: #3a6a3a;"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-lg shadow p-6" style="background: white; border: none;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm" style="color: #44483e;">Notes</p>
                        <p class="text-3xl font-bold" style="color: #8a6e2e;">{{ $activity->notes->count() ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: #f5f3ff;">
                        <i class="fas fa-sticky-note text-xl" style="color: #8a6e2e;"></i>
                    </div>
                </div>
                <p class="text-xs mt-2" style="color: #75786c;">Accessible via rendez-vous</p>
            </div>

            <div class="rounded-lg shadow p-6" style="background: white; border: none;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm" style="color: #44483e;">Statistiques</p>
                        <p class="text-3xl font-bold" style="color: #8a6e2e;">{{ $activity->statistiques->count() ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: #fffbf0;">
                        <i class="fas fa-chart-bar text-xl" style="color: #8a6e2e;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="rounded-lg shadow mb-6" style="background: white; border: none;">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button wire:click="setActiveTab('overview')"
                            class="tab-button {{ $activeTab === 'overview' ? 'tab-active' : '' }}">
                        <i class="fas fa-home mr-2"></i>
                        Vue d'ensemble
                    </button>
                    <button wire:click="setActiveTab('contacts')"
                            class="tab-button {{ $activeTab === 'contacts' ? 'tab-active' : '' }}">
                        <i class="fas fa-users mr-2"></i>
                        Contacts
                    </button>
                    <button wire:click="setActiveTab('appointments')"
                            class="tab-button {{ $activeTab === 'appointments' ? 'tab-active' : '' }}">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Rendez-vous
                    </button>
                    <button wire:click="setActiveTab('statistics')"
                            class="tab-button {{ $activeTab === 'statistics' ? 'tab-active' : '' }}">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Statistiques
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="rounded-lg shadow p-6" style="background: white; border: none;">
            @if($activeTab === 'overview')
                <div>
                    <h2 class="text-2xl font-bold mb-6" style="color: #1b1c1a;">Vue d'ensemble</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Recent Contacts -->
                        <div class="rounded-lg p-4" style="border: none;">
                            <h3 class="font-semibold text-lg mb-4 flex items-center justify-between">
                                <span>Contacts r&eacute;cents</span>
                                <button wire:click="setActiveTab('contacts')" class="text-sm" style="color: #843728;">
                                    Voir tout &rarr;
                                </button>
                            </h3>
                            @if($activity->contacts->take(5)->count() > 0)
                                <ul class="space-y-2">
                                    @foreach($activity->contacts->take(5) as $contact)
                                        <li class="flex items-center justify-between py-2 border-b last:border-0">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background: #fef5f3;">
                                                    <i class="fas fa-user" style="color: #843728;"></i>
                                                </div>
                                                <div>
                                                    <p class="font-medium">{{ $contact->nom }} {{ $contact->prenom }}</p>
                                                    <p class="text-sm" style="color: #75786c;">{{ $contact->email }}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm" style="color: #75786c;">Aucun contact</p>
                            @endif
                        </div>

                        <!-- Recent Appointments -->
                        <div class="rounded-lg p-4" style="border: none;">
                            <h3 class="font-semibold text-lg mb-4 flex items-center justify-between">
                                <span>Prochains rendez-vous</span>
                                <button wire:click="setActiveTab('appointments')" class="text-sm" style="color: #843728;">
                                    Voir tout &rarr;
                                </button>
                            </h3>
                            @if($activity->rendezVous->where('date_debut', '>=', now()->toDateString())->take(5)->count() > 0)
                                <ul class="space-y-2">
                                    @foreach($activity->rendezVous->where('date_debut', '>=', now()->toDateString())->sortBy('date_debut')->take(5) as $rdv)
                                        <li class="flex items-center justify-between py-2 border-b last:border-0">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background: #f0faf0;">
                                                    <i class="fas fa-calendar" style="color: #3a6a3a;"></i>
                                                </div>
                                                <div>
                                                    <p class="font-medium">{{ $rdv->titre }}</p>
                                                    <p class="text-sm" style="color: #75786c;">{{ \Carbon\Carbon::parse($rdv->date_debut)->format('d/m/Y') }} à {{ \Carbon\Carbon::parse($rdv->heure_debut)->format('H:i') }}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm" style="color: #75786c;">Aucun rendez-vous à venir</p>
                            @endif
                        </div>
                    </div>

                    <!-- Activity Info -->
                    <div class="mt-6 rounded-lg p-4" style="border: none;">
                        <h3 class="font-semibold text-lg mb-4">Informations de l'activit&eacute;</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium" style="color: #75786c;">Date de cr&eacute;ation</dt>
                                <dd class="mt-1 text-sm" style="color: #1b1c1a;">{{ $activity->created_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium" style="color: #75786c;">Derni&egrave;re modification</dt>
                                <dd class="mt-1 text-sm" style="color: #1b1c1a;">{{ $activity->updated_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                            @if($activity->description)
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium" style="color: #75786c;">Description</dt>
                                    <dd class="mt-1 text-sm" style="color: #1b1c1a;">{{ $activity->description }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            @elseif($activeTab === 'contacts')
                <div>
                    <livewire:contact-manager :activity-id="$activityId" :key="'contacts-'.$activityId" />
                </div>
            @elseif($activeTab === 'appointments')
                <div>
                    <livewire:appointment-manager :activity-id="$activityId" :key="'appointments-'.$activityId" />
                </div>
            @elseif($activeTab === 'statistics')
                <div>
                    <livewire:statistics-dashboard :activity-id="$activityId" :key="'statistics-'.$activityId" />
                </div>
            @endif
        </div>
    </div>

    <style>
        .tab-button {
            padding: 1rem 1.5rem;
            font-weight: 500;
            color: #6b7280;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .tab-button:hover {
            color: #6d2a1d;
            border-bottom-color: #d1d5db;
        }

        .tab-active {
            color: #843728 !important;
            border-bottom-color: #843728 !important;
        }
    </style>
</div>
