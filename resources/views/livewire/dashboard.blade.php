<div>
    {{-- Activity Dashboard - Sophisticated Architect Theme --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold" style="color: #1b1c1a;">Mes Activit&eacute;s</h1>
            <p class="mt-2" style="color: #44483e;">S&eacute;lectionnez une activit&eacute; pour acc&eacute;der &agrave; tous vos contacts, rendez-vous et notes</p>
        </div>

        <!-- Activities Grid -->
        @if($activities->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activities as $activity)
                    <a href="{{ route('activites.view', $activity->id) }}"
                       class="activity-card block rounded-2xl overflow-hidden group"
                       wire:key="activity-{{ $activity->id }}"
                       style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.03);">
                        <!-- Card Header -->
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

                        <!-- Card Content -->
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-1" style="color: #1b1c1a;">{{ $activity->nom }}</h3>

                            @if($activity->description)
                                <p class="text-sm mb-4 line-clamp-2" style="color: #44483e;">{{ $activity->description }}</p>
                            @endif

                            <!-- Stats -->
                            <div class="grid grid-cols-2 gap-4 mt-4 pt-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold" style="color: #843728;">{{ $activity->contacts->count() }}</div>
                                    <div class="text-xs font-medium" style="color: #75786c;">Contacts</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold" style="color: #3a6a3a;">{{ $activity->rendezVous->count() }}</div>
                                    <div class="text-xs font-medium" style="color: #75786c;">RDV</div>
                                </div>
                            </div>

                            <!-- Date Info -->
                            <div class="mt-4 pt-3">
                                <p class="text-xs" style="color: #75786c;">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Cr&eacute;&eacute; le {{ $activity->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </a>
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
                <h3 class="text-lg font-semibold mb-2" style="color: #1b1c1a;">Aucune activit&eacute;</h3>
                <p class="mb-6" style="color: #44483e;">Cr&eacute;ez votre premi&egrave;re activit&eacute; pour commencer</p>
                <a href="{{ route('activites.create') }}" class="inline-flex items-center px-6 py-3 text-white font-semibold rounded-xl transition-all hover:shadow-lg" style="background: #843728;">
                    <i class="fas fa-plus mr-2"></i>
                    Cr&eacute;er une activit&eacute;
                </a>
            </div>
        @endif

        <!-- Quick Stats Summary -->
        @if($activities->count() > 0)
            <div class="mt-10 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="rounded-xl p-5 text-center" style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.03);">
                    <div class="text-3xl font-bold" style="color: #843728;">{{ $stats['activities'] }}</div>
                    <div class="text-sm font-medium mt-1" style="color: #44483e;">Activit&eacute;s</div>
                </div>
                <div class="rounded-xl p-5 text-center" style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.03);">
                    <div class="text-3xl font-bold" style="color: #3a6a3a;">{{ $stats['contacts'] }}</div>
                    <div class="text-sm font-medium mt-1" style="color: #44483e;">Contacts Total</div>
                </div>
                <div class="rounded-xl p-5 text-center" style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.03);">
                    <div class="text-3xl font-bold" style="color: #8a6e2e;">{{ $stats['appointments'] }}</div>
                    <div class="text-sm font-medium mt-1" style="color: #44483e;">RDV Total</div>
                </div>
                <div class="rounded-xl p-5 text-center" style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.03);">
                    <div class="text-3xl font-bold" style="color: #8a6e2e;">{{ $stats['notes'] }}</div>
                    <div class="text-sm font-medium mt-1" style="color: #44483e;">Notes Total</div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .activity-card {
            transition: all 0.2s ease;
        }

        .activity-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(27,28,26,0.05), 0 8px 10px -6px rgba(27,28,26,0.03) !important;
        }
    </style>
</div>
