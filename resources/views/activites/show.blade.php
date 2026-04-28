@extends('layouts.app')

@section('title', $activite->nom . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="{ tab: 'rendezVous' }">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-wrap justify-between items-start gap-4 mb-8">
            <div class="flex items-start gap-4">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-3 py-2 rounded-lg transition-colors mt-1"
                   style="background: #f5f3f0; color: #44483e;"
                   title="{{ __('Back') }}">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold" style="color: #1b1c1a;">{{ $activite->nom }}</h1>
                    <p class="text-sm mt-1" style="color: #75786c;">{{ __('Activity details') }}</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('activites.edit', $activite) }}"
                   class="inline-flex items-center text-white px-4 py-2 rounded-lg transition-colors"
                   style="background: #843728;">
                    <i class="fas fa-edit mr-2"></i>{{ __('Edit') }}
                </a>
                <form action="{{ route('activites.destroy', $activite) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors"
                            onclick="return confirm('{{ __('Are you sure you want to delete this activity?') }}')">
                        <i class="fas fa-trash mr-2"></i>{{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- KPI Strip (always visible) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="rounded-xl p-4 flex items-center gap-3" style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.04);">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #fef5f3;">
                    <i class="fas fa-users" style="color: #843728;"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold leading-none" style="color: #843728;">{{ $stats['contacts_count'] }}</p>
                    <p class="text-xs font-medium mt-1" style="color: #44483e;">{{ __('Linked contacts') }}</p>
                </div>
            </div>
            <div class="rounded-xl p-4 flex items-center gap-3" style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.04);">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #f0faf0;">
                    <i class="fas fa-calendar-alt" style="color: #3a6a3a;"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold leading-none" style="color: #3a6a3a;">{{ $stats['total_rdv'] }}</p>
                    <p class="text-xs font-medium mt-1" style="color: #44483e;">{{ __('Appointments') }}</p>
                </div>
            </div>
            <div class="rounded-xl p-4 flex items-center gap-3" style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.04);">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #fbf1d8;">
                    <i class="fas fa-calendar-day" style="color: #8a6e2e;"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold leading-none" style="color: #8a6e2e;">{{ $stats['upcoming_rdv'] }}</p>
                    <p class="text-xs font-medium mt-1" style="color: #44483e;">{{ __('Upcoming') }}</p>
                </div>
            </div>
            <div class="rounded-xl p-4 flex items-center gap-3" style="background: white; box-shadow: 0 2px 8px rgba(27,28,26,0.04);">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #ece7f7;">
                    <i class="fas fa-sticky-note" style="color: #5d4ba1;"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold leading-none" style="color: #5d4ba1;">{{ $stats['notes_count'] }}</p>
                    <p class="text-xs font-medium mt-1" style="color: #44483e;">{{ __('Notes') }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Activity Information Card -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4" style="color: #1b1c1a;">{{ __('Activity information') }}</h2>

                    @if($activite->image)
                        <div class="mb-6">
                            <img src="{{ Storage::url($activite->image) }}" alt="{{ $activite->nom }}"
                                 class="w-full h-48 object-cover rounded-lg">
                        </div>
                    @endif

                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider" style="color: #75786c;">{{ __('Description') }}</dt>
                            <dd class="mt-1" style="color: #1b1c1a;">{{ $activite->description }}</dd>
                        </div>

                        @if($activite->email)
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: #75786c;">{{ __('Email') }}</dt>
                                <dd class="mt-1">
                                    <a href="mailto:{{ $activite->email }}" class="hover:underline" style="color: #843728;">
                                        {{ $activite->email }}
                                    </a>
                                </dd>
                            </div>
                        @endif

                        @if($activite->numero_telephone)
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: #75786c;">{{ __('Phone') }}</dt>
                                <dd class="mt-1">
                                    <a href="tel:{{ $activite->numero_telephone }}" class="hover:underline" style="color: #843728;">
                                        {{ $activite->numero_telephone }}
                                    </a>
                                </dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider" style="color: #75786c;">{{ __('Created on') }}</dt>
                            <dd class="mt-1" style="color: #1b1c1a;">{{ $activite->created_at->format('m/d/Y') }} {{ __('at') }} {{ $activite->created_at->format('H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Tabs: Appointments + Statistics -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <!-- Tab Navigation -->
                    <div class="flex border-b" style="border-color: rgba(27,28,26,0.06);" role="tablist">
                        <button type="button"
                                role="tab"
                                @click="tab = 'rendezVous'"
                                :aria-selected="tab === 'rendezVous'"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-4 text-sm font-semibold transition-colors"
                                :class="tab === 'rendezVous' ? 'tab-active' : 'tab-inactive'">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ __('Appointments') }}</span>
                            <span class="ml-1 inline-flex items-center justify-center min-w-[1.5rem] h-5 px-1.5 rounded-full text-xs font-bold"
                                  style="background: #f5f3f0; color: #44483e;">{{ $stats['total_rdv'] }}</span>
                        </button>
                        <button type="button"
                                role="tab"
                                @click="tab = 'stats'"
                                :aria-selected="tab === 'stats'"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-4 text-sm font-semibold transition-colors"
                                :class="tab === 'stats' ? 'tab-active' : 'tab-inactive'">
                            <i class="fas fa-chart-bar"></i>
                            <span>{{ __('Statistics') }}</span>
                        </button>
                    </div>

                    <!-- Appointments Tab -->
                    <div x-show="tab === 'rendezVous'" x-cloak role="tabpanel" class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-lg" style="color: #1b1c1a;">{{ __('Appointments') }}</h3>
                            <a href="{{ route('rendez-vous.create', ['activite_id' => $activite->id]) }}"
                               class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white rounded-lg transition-colors"
                               style="background: #843728;">
                                <i class="fas fa-plus mr-1.5"></i>{{ __('New appointment') }}
                            </a>
                        </div>

                        @if($activite->rendezVous->count() > 0)
                            @php
                                $upcoming = $activite->rendezVous
                                    ->filter(fn ($r) => $r->date_debut && $r->date_debut->gte(now()->startOfDay()))
                                    ->sortBy([['date_debut', 'asc'], ['heure_debut', 'asc']])
                                    ->values();
                                $past = $activite->rendezVous
                                    ->filter(fn ($r) => $r->date_debut && $r->date_debut->lt(now()->startOfDay()))
                                    ->sortByDesc('date_debut')
                                    ->values();
                            @endphp

                            @if($upcoming->count() > 0)
                                <h4 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #75786c;">
                                    {{ __('Upcoming') }} ({{ $upcoming->count() }})
                                </h4>
                                <ul class="space-y-2 mb-6">
                                    @foreach($upcoming as $rdv)
                                        @include('activites.partials.rdv-row', ['rdv' => $rdv])
                                    @endforeach
                                </ul>
                            @endif

                            @if($past->count() > 0)
                                <h4 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #75786c;">
                                    {{ __('Past') }} ({{ $past->count() }})
                                </h4>
                                <ul class="space-y-2">
                                    @foreach($past->take(10) as $rdv)
                                        @include('activites.partials.rdv-row', ['rdv' => $rdv, 'isPast' => true])
                                    @endforeach
                                </ul>
                                @if($past->count() > 10)
                                    <p class="mt-3 text-xs text-center" style="color: #75786c;">
                                        {{ __('Showing the 10 most recent past appointments.') }}
                                    </p>
                                @endif
                            @endif
                        @else
                            <div class="text-center py-12 rounded-lg" style="background: #fbf9f5; border: 1px dashed rgba(27,28,26,0.08);">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-3" style="background: white;">
                                    <i class="far fa-calendar text-2xl" style="color: #c5c8b9;"></i>
                                </div>
                                <p class="font-medium mb-1" style="color: #1b1c1a;">{{ __('No appointments') }}</p>
                                <p class="text-sm mb-4" style="color: #75786c;">{{ __('Start by creating your first appointment.') }}</p>
                                <a href="{{ route('rendez-vous.create', ['activite_id' => $activite->id]) }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white rounded-lg transition-colors"
                                   style="background: #843728;">
                                    <i class="fas fa-plus mr-2"></i>{{ __('New appointment') }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Statistics Tab -->
                    <div x-show="tab === 'stats'" x-cloak role="tabpanel" class="p-6">
                        <h3 class="font-semibold text-lg mb-4" style="color: #1b1c1a;">{{ __('Statistics') }}</h3>

                        <!-- Snapshot grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                            <div class="rounded-lg p-4 text-center" style="background: #fbf9f5;">
                                <p class="text-2xl font-bold" style="color: #843728;">{{ $stats['total_rdv'] }}</p>
                                <p class="text-xs font-medium mt-1" style="color: #44483e;">{{ __('Appointments') }}</p>
                            </div>
                            <div class="rounded-lg p-4 text-center" style="background: #fbf9f5;">
                                <p class="text-2xl font-bold" style="color: #3a6a3a;">{{ $stats['upcoming_rdv'] }}</p>
                                <p class="text-xs font-medium mt-1" style="color: #44483e;">{{ __('Upcoming') }}</p>
                            </div>
                            <div class="rounded-lg p-4 text-center" style="background: #fbf9f5;">
                                <p class="text-2xl font-bold" style="color: #8a6e2e;">{{ $stats['today_rdv'] }}</p>
                                <p class="text-xs font-medium mt-1" style="color: #44483e;">{{ __('Today') }}</p>
                            </div>
                            <div class="rounded-lg p-4 text-center" style="background: #fbf9f5;">
                                <p class="text-2xl font-bold" style="color: #75786c;">{{ $stats['past_rdv'] }}</p>
                                <p class="text-xs font-medium mt-1" style="color: #44483e;">{{ __('Past') }}</p>
                            </div>
                        </div>

                        <!-- Status distribution -->
                        @if($stats['rdv_by_status']->count() > 0)
                            <h4 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #75786c;">
                                {{ __('Status Distribution') }}
                            </h4>
                            @php
                                $statusColors = [
                                    'scheduled' => '#8a6e2e',
                                    'confirmed' => '#3a6a3a',
                                    'completed' => '#5d4ba1',
                                    'cancelled' => '#b54848',
                                    'no_show'   => '#75786c',
                                ];
                            @endphp
                            <div class="space-y-3 mb-6">
                                @foreach(\App\Models\RendezVous::STATUTS as $statusKey => $statusLabel)
                                    @php
                                        $count = $stats['rdv_by_status'][$statusKey] ?? 0;
                                        $pct = $stats['total_rdv'] > 0 ? round(($count / $stats['total_rdv']) * 100) : 0;
                                        $color = $statusColors[$statusKey] ?? '#843728';
                                    @endphp
                                    <div>
                                        <div class="flex items-center justify-between text-sm mb-1">
                                            <span style="color: #1b1c1a;">{{ __($statusLabel) }}</span>
                                            <span class="font-semibold" style="color: #44483e;">{{ $count }} <span class="text-xs" style="color: #75786c;">({{ $pct }}%)</span></span>
                                        </div>
                                        <div class="h-2 rounded-full overflow-hidden" style="background: #f5f3f0;">
                                            <div class="h-full rounded-full transition-all"
                                                 style="width: {{ $pct }}%; background: {{ $color }};"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Detailed report link -->
                        <a href="{{ route('statistiques.activite', $activite) }}"
                           class="inline-flex items-center justify-center w-full px-4 py-3 rounded-lg font-semibold text-sm transition-colors"
                           style="background: #fbf9f5; color: #843728;">
                            <i class="fas fa-chart-line mr-2"></i>
                            {{ __('Detailed activity analysis') }}
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Associated Notes -->
                @if($activite->notes->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4" style="color: #1b1c1a;">{{ __('Associated notes') }}</h2>
                        <div class="space-y-3">
                            @foreach($activite->notes as $note)
                                <div class="border-l-4 pl-4 py-2" style="border-color: #843728;">
                                    <h3 class="font-medium" style="color: #1b1c1a;">{{ $note->titre }}</h3>
                                    <p class="mt-1" style="color: #44483e;">{{ $note->commentaire }}</p>
                                    <p class="text-xs mt-2" style="color: #75786c;">
                                        {{ $note->date_create->format('m/d/Y') }} {{ __('at') }} {{ $note->date_create->format('H:i') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Linked Contacts -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold" style="color: #1b1c1a;">{{ __('Linked contacts') }}</h3>
                        <button type="button" id="addContactBtn"
                                class="inline-flex items-center text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                style="background: #3a6a3a;">
                            <i class="fas fa-plus mr-1"></i>{{ __('Add') }}
                        </button>
                    </div>

                    @if($activite->contacts->count() > 0)
                        <ul class="space-y-2">
                            @foreach($activite->contacts as $contact)
                                <li class="flex items-center justify-between p-3 rounded-lg" style="background: #fbf9f5;">
                                    <div class="min-w-0">
                                        <p class="font-medium truncate" style="color: #1b1c1a;">
                                            {{ $contact->prenom }} {{ $contact->nom }}
                                        </p>
                                        <p class="text-xs truncate" style="color: #75786c;">{{ $contact->ville ?? __('City not specified') }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <a href="{{ route('contacts.show', $contact) }}"
                                           class="text-xs font-medium hover:underline"
                                           style="color: #843728;">{{ __('View') }}</a>
                                        <form action="{{ route('activites.contacts.detach', [$activite, $contact]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium hover:underline text-red-600">
                                                {{ __('Remove') }}
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-center py-4" style="color: #75786c;">{{ __('No contacts linked to this activity') }}</p>
                    @endif
                </div>

                <!-- Quick Actions (no Statistics button - it's now a tab) -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold mb-4" style="color: #1b1c1a;">{{ __('Quick actions') }}</h3>
                    <div class="space-y-2">
                        <a href="{{ route('rendez-vous.create', ['activite_id' => $activite->id]) }}"
                           class="flex items-center w-full px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                           style="background: #fef5f3; color: #843728;">
                            <i class="fas fa-calendar-plus w-5 mr-2"></i>{{ __('New appointment') }}
                        </a>
                        <a href="{{ route('notes.create', ['activite_id' => $activite->id]) }}"
                           class="flex items-center w-full px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                           style="background: #ece7f7; color: #5d4ba1;">
                            <i class="fas fa-pen-to-square w-5 mr-2"></i>{{ __('New note') }}
                        </a>
                        <a href="{{ route('activites.edit', $activite) }}"
                           class="flex items-center w-full px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                           style="background: #f5f3f0; color: #44483e;">
                            <i class="fas fa-pen w-5 mr-2"></i>{{ __('Edit activity') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Contact Modal -->
<div id="addContactModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Add a contact to the activity') }}</h3>

        <!-- Tab buttons -->
        <div class="flex border-b border-gray-200 mb-4">
            <button type="button" id="tabExisting"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition duration-200"
                    style="border-color: #843728; color: #843728;">
                <i class="fas fa-users mr-1"></i>{{ __('Existing contact') }}
            </button>
            <button type="button" id="tabNew"
                    class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition duration-200">
                <i class="fas fa-user-plus mr-1"></i>{{ __('New contact') }}
            </button>
        </div>

        <!-- Tab 1: Existing contact selector -->
        <div id="panelExisting">
            <form id="addContactForm" action="{{ route('activites.contacts.attach', $activite) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Select a contact') }}</label>
                    <select id="contact_id" name="contact_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2" style="--tw-ring-color: #843728;">
                        <option value="">{{ __('Choose a contact...') }}</option>
                        @foreach(Auth::user()->contacts()->whereNotIn('id', $activite->contacts->pluck('id'))->get() as $contact)
                            <option value="{{ $contact->id }}">{{ $contact->prenom }} {{ $contact->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelAddContact"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition duration-200">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                            class="text-white px-4 py-2 rounded transition duration-200"
                            style="background: linear-gradient(135deg, #843728, #c4816e);">
                        {{ __('Add') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Tab 2: Create new contact -->
        <div id="panelNew" class="hidden">
            <div class="text-center py-6">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: rgba(132, 55, 40, 0.1);">
                    <i class="fas fa-user-plus text-2xl" style="color: #843728;"></i>
                </div>
                <p class="text-gray-600 mb-4">{{ __('Create a new contact and automatically link it to this activity.') }}</p>
                <a href="{{ route('contacts.create', ['activite_id' => $activite->id]) }}"
                   class="inline-flex items-center text-white px-6 py-2 rounded-lg transition duration-200 hover:opacity-90"
                   style="background: linear-gradient(135deg, #843728, #c4816e);">
                    <i class="fas fa-user-plus mr-2"></i>
                    {{ __('Create a new contact') }}
                </a>
            </div>
            <div class="flex justify-end">
                <button type="button" id="cancelAddContact2"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition duration-200">
                    {{ __('Cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }

    .tab-active {
        color: #843728;
        background: #fbf9f5;
        border-bottom: 2px solid #843728;
    }

    .tab-inactive {
        color: #75786c;
        background: white;
        border-bottom: 2px solid transparent;
    }

    .tab-inactive:hover {
        color: #1b1c1a;
        background: #fbf9f5;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addContactBtn = document.getElementById('addContactBtn');
    const addContactModal = document.getElementById('addContactModal');
    const cancelAddContact = document.getElementById('cancelAddContact');
    const cancelAddContact2 = document.getElementById('cancelAddContact2');
    const tabExisting = document.getElementById('tabExisting');
    const tabNew = document.getElementById('tabNew');
    const panelExisting = document.getElementById('panelExisting');
    const panelNew = document.getElementById('panelNew');

    function openModal() {
        addContactModal.classList.remove('hidden');
        addContactModal.classList.add('flex');
    }

    function closeModal() {
        addContactModal.classList.add('hidden');
        addContactModal.classList.remove('flex');
    }

    addContactBtn.addEventListener('click', openModal);
    cancelAddContact.addEventListener('click', closeModal);
    cancelAddContact2.addEventListener('click', closeModal);

    // Tab switching
    tabExisting.addEventListener('click', function() {
        panelExisting.classList.remove('hidden');
        panelNew.classList.add('hidden');
        tabExisting.style.borderColor = '#843728';
        tabExisting.style.color = '#843728';
        tabExisting.classList.remove('border-transparent');
        tabNew.style.borderColor = 'transparent';
        tabNew.style.color = '';
        tabNew.classList.add('border-transparent', 'text-gray-500');
        tabExisting.classList.remove('text-gray-500');
    });

    tabNew.addEventListener('click', function() {
        panelNew.classList.remove('hidden');
        panelExisting.classList.add('hidden');
        tabNew.style.borderColor = '#843728';
        tabNew.style.color = '#843728';
        tabNew.classList.remove('border-transparent', 'text-gray-500');
        tabExisting.style.borderColor = 'transparent';
        tabExisting.style.color = '';
        tabExisting.classList.add('border-transparent', 'text-gray-500');
        tabNew.classList.remove('text-gray-500');
    });

    // Close modal when clicking outside
    addContactModal.addEventListener('click', function(e) {
        if (e.target === addContactModal) {
            closeModal();
        }
    });

    // Auto-open modal when arriving via #addContact anchor
    if (window.location.hash === '#addContact') {
        openModal();
    }
});
</script>
@endsection
