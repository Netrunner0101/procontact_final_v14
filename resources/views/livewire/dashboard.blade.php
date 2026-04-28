<div>
    {{-- Activity Dashboard - Sophisticated Architect Theme --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold" style="color: #1b1c1a;">{{ __('My Activities') }}</h1>
            <p class="mt-2" style="color: #44483e;">{{ __('Select an activity to access all your contacts, appointments and notes') }}</p>
        </div>

        <!-- Quick Stats Summary (top, always visible) -->
        @if($activities->count() > 0)
            <div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="rounded-xl p-5 flex items-center gap-4" style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.04);">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #ffe2da;">
                        <i class="fas fa-briefcase" style="color: #843728;"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold leading-none" style="color: #843728;">{{ $stats['activities'] }}</div>
                        <div class="text-xs font-medium mt-1 truncate" style="color: #44483e;">{{ __('Activities') }}</div>
                    </div>
                </div>
                <div class="rounded-xl p-5 flex items-center gap-4" style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.04);">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #e3f3e3;">
                        <i class="fas fa-users" style="color: #3a6a3a;"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold leading-none" style="color: #3a6a3a;">{{ $stats['contacts'] }}</div>
                        <div class="text-xs font-medium mt-1 truncate" style="color: #44483e;">{{ __('Total') }} {{ __('Contacts') }}</div>
                    </div>
                </div>
                <div class="rounded-xl p-5 flex items-center gap-4" style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.04);">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #fbf1d8;">
                        <i class="fas fa-calendar-alt" style="color: #8a6e2e;"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold leading-none" style="color: #8a6e2e;">{{ $stats['appointments'] }}</div>
                        <div class="text-xs font-medium mt-1 truncate" style="color: #44483e;">{{ __('Total') }} {{ __('Appointments') }}</div>
                    </div>
                </div>
                <div class="rounded-xl p-5 flex items-center gap-4" style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.04);">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #ece7f7;">
                        <i class="fas fa-sticky-note" style="color: #5d4ba1;"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold leading-none" style="color: #5d4ba1;">{{ $stats['notes'] }}</div>
                        <div class="text-xs font-medium mt-1 truncate" style="color: #44483e;">{{ __('Total') }} {{ __('Notes') }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Activities Grid -->
        @if($activities->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activities as $activity)
                    @php
                        $upcomingRdv = $activity->rendezVous
                            ->filter(fn ($rdv) => $rdv->date_debut && $rdv->date_debut->gte(now()->startOfDay()))
                            ->sortBy([['date_debut', 'asc'], ['heure_debut', 'asc']])
                            ->values();
                        $previewRdv = $upcomingRdv->take(3);
                    @endphp
                    <div class="activity-card rounded-2xl overflow-hidden flex flex-col"
                         wire:key="activity-{{ $activity->id }}"
                         x-data="{ showAppointments: false }"
                         style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.04);">

                        <!-- Card Header -->
                        <a href="{{ route('activites.show', $activity->id) }}"
                           class="block group focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                           style="--tw-ring-color: #843728;">
                            <div class="p-6" style="background: linear-gradient(135deg, #2d2e2a, #1b1c1a);">
                                <div class="flex items-center justify-between">
                                    <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: rgba(132,55,40,0.15); backdrop-filter: blur(8px);">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div style="color: rgba(255,255,255,0.5);">
                                        <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Card Body -->
                        <div class="p-6 flex-1 flex flex-col">
                            <a href="{{ route('activites.show', $activity->id) }}" class="focus:outline-none">
                                <h3 class="text-lg font-bold mb-1 hover:underline" style="color: #1b1c1a;">{{ $activity->nom }}</h3>
                            </a>

                            @if($activity->description)
                                <p class="text-sm mb-4 line-clamp-2" style="color: #44483e;">{{ $activity->description }}</p>
                            @endif

                            <!-- Inline counters -->
                            <div class="grid grid-cols-2 gap-4 mt-auto pt-4">
                                <div class="text-center rounded-lg py-2" style="background: #fef5f3;">
                                    <div class="text-2xl font-bold leading-none" style="color: #843728;">{{ $activity->contacts_count ?? $activity->contacts->count() }}</div>
                                    <div class="text-xs font-medium mt-1" style="color: #75786c;">{{ __('Contacts') }}</div>
                                </div>
                                <div class="text-center rounded-lg py-2" style="background: #f0faf0;">
                                    <div class="text-2xl font-bold leading-none" style="color: #3a6a3a;">{{ $activity->rendez_vous_count ?? $activity->rendezVous->count() }}</div>
                                    <div class="text-xs font-medium mt-1" style="color: #75786c;">{{ __('Appts') }}</div>
                                </div>
                            </div>

                            <!-- Date Info -->
                            <div class="mt-4 pt-3 flex items-center justify-between">
                                <p class="text-xs" style="color: #75786c;">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ __('Created on') }} {{ $activity->created_at->format('m/d/Y') }}
                                </p>
                            </div>
                        </div>

                        <!-- Toggle bar -->
                        <button type="button"
                                @click="showAppointments = !showAppointments"
                                :aria-expanded="showAppointments.toString()"
                                aria-controls="appointments-{{ $activity->id }}"
                                class="w-full flex items-center justify-between px-6 py-3 text-sm font-semibold transition-colors"
                                style="background: #fbf9f5; color: #843728; border-top: 1px solid rgba(27,28,26,0.06);">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-calendar-check"></i>
                                <span x-text="showAppointments ? '{{ __('Hide appointments') }}' : '{{ __('See appointments') }}'">{{ __('See appointments') }}</span>
                                @if($upcomingRdv->count() > 0)
                                    <span class="ml-1 inline-flex items-center justify-center min-w-[1.5rem] h-5 px-1.5 rounded-full text-xs font-bold" style="background: #843728; color: white;">{{ $upcomingRdv->count() }}</span>
                                @endif
                            </span>
                            <i class="fas fa-chevron-down transition-transform"
                               :class="{ 'rotate-180': showAppointments }"></i>
                        </button>

                        <!-- Inline appointments panel -->
                        <div id="appointments-{{ $activity->id }}"
                             x-show="showAppointments"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             x-cloak
                             class="px-6 pb-6 pt-2"
                             style="background: #fbf9f5;">
                            <h4 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #75786c;">
                                {{ __('Upcoming appointments') }}
                            </h4>

                            @if($previewRdv->count() > 0)
                                <ul class="space-y-2">
                                    @foreach($previewRdv as $rdv)
                                        <li class="flex items-start gap-3 p-3 rounded-lg" style="background: white; border: 1px solid rgba(27,28,26,0.04);">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex flex-col items-center justify-center text-white" style="background: linear-gradient(135deg, #843728, #c4816e);">
                                                <span class="text-[10px] font-semibold uppercase leading-none">{{ $rdv->date_debut?->locale(app()->getLocale())->isoFormat('MMM') }}</span>
                                                <span class="text-sm font-bold leading-none mt-0.5">{{ $rdv->date_debut?->format('d') }}</span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold truncate" style="color: #1b1c1a;">{{ $rdv->titre }}</p>
                                                <p class="text-xs mt-0.5" style="color: #75786c;">
                                                    @if($rdv->heure_debut)
                                                        <i class="fas fa-clock mr-1"></i>{{ $rdv->heure_debut->format('H:i') }}
                                                    @endif
                                                    @if($rdv->contact)
                                                        <span class="ml-2"><i class="fas fa-user mr-1"></i>{{ $rdv->contact->prenom }} {{ $rdv->contact->nom }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <a href="{{ route('rendez-vous.show', $rdv->id) }}"
                                               class="flex-shrink-0 text-xs font-medium px-2 py-1 rounded transition-colors"
                                               style="color: #843728;"
                                               title="{{ __('View appointment') }}">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                @if($upcomingRdv->count() > $previewRdv->count())
                                    <a href="{{ route('activites.show', $activity->id) }}"
                                       class="block text-center mt-3 text-xs font-semibold hover:underline"
                                       style="color: #843728;">
                                        {{ __('View all') }} ({{ $upcomingRdv->count() }}) &rarr;
                                    </a>
                                @endif
                            @else
                                <div class="text-center py-6 rounded-lg" style="background: white; border: 1px dashed rgba(27,28,26,0.08);">
                                    <i class="far fa-calendar text-2xl mb-2" style="color: #c5c8b9;"></i>
                                    <p class="text-sm" style="color: #75786c;">{{ __('No upcoming appointments') }}</p>
                                    <a href="{{ route('rendez-vous.create', ['activite_id' => $activity->id]) }}"
                                       class="inline-flex items-center mt-2 text-xs font-semibold hover:underline"
                                       style="color: #843728;">
                                        <i class="fas fa-plus mr-1"></i>{{ __('New appointment') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-6" style="background: #ffdbd1;">
                    <svg class="w-10 h-10" style="color: #843728;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2" style="color: #1b1c1a;">{{ __('No activities') }}</h3>
                <p class="mb-6" style="color: #44483e;">{{ __('Create your first activity to get started') }}</p>
                <a href="{{ route('activites.create') }}" class="inline-flex items-center px-6 py-3 text-white font-semibold rounded-xl transition-all hover:shadow-lg" style="background: #843728;">
                    <i class="fas fa-plus mr-2"></i>
                    {{ __('Create an Activity') }}
                </a>
            </div>
        @endif
    </div>

    <style>
        [x-cloak] { display: none !important; }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .activity-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .activity-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -8px rgba(27,28,26,0.08), 0 4px 8px -4px rgba(27,28,26,0.04) !important;
        }
    </style>
</div>
