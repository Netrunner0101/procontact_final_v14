@extends('layouts.app')

@section('title', __('Reminder') . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Reminder Details') }}</h1>
                <p class="text-gray-600 mt-2">{{ $rappel->rendezVous->titre }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('rappels.edit', $rappel) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    {{ __('Edit') }}
                </a>
                <form action="{{ route('rappels.destroy', $rappel) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200"
                            onclick="return confirm('{{ __('Are you sure you want to delete this reminder?') }}')">
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Reminder Details Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Reminder information') }}</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ __('Reminder date and time') }}</label>
                            <p class="mt-1 text-gray-900 text-lg">{{ $rappel->date_rappel->format('d/m/Y à H:i') }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ __('Frequency') }}</label>
                            <p class="mt-1 text-gray-900">{{ $rappel->frequence }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ __('Status') }}</label>
                            @php
                                $now = now();
                                $isPast = $rappel->date_rappel->isPast();
                                $isToday = $rappel->date_rappel->isToday();
                                $status = $isPast ? __('Expired') : ($isToday ? __('Today') : __('Upcoming'));
                                $statusColor = $isPast ? 'bg-red-100 text-red-800' : ($isToday ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                            @endphp
                            <span class="mt-1 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColor }}">
                                {{ $status }}
                            </span>
                            @if($rappel->date_rappel->isFuture())
                                <p class="text-sm text-gray-600 mt-2">
                                    {{ $rappel->date_rappel->diffForHumans() }}
                                </p>
                            @endif
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ __('Created on') }}</label>
                            <p class="mt-1 text-gray-900">{{ $rappel->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Associated Appointment -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Associated appointment') }}</h2>

                    <div class="border-l-4 border-yellow-500 pl-4 py-2">
                        <h3 class="font-medium text-gray-900 text-lg">{{ $rappel->rendezVous->titre }}</h3>
                        @if($rappel->rendezVous->description)
                            <p class="text-gray-600 mt-1">{{ $rappel->rendezVous->description }}</p>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500">{{ __('Date:') }}</span>
                                <span class="text-sm text-gray-900">{{ $rappel->rendezVous->date_debut->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">{{ __('Time:') }}</span>
                                <span class="text-sm text-gray-900">{{ $rappel->rendezVous->heure_debut->format('H:i') }} - {{ $rappel->rendezVous->heure_fin->format('H:i') }}</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('rendez-vous.show', $rappel->rendezVous) }}"
                               class="text-blue-600 hover:text-blue-800 text-sm">
                                {{ __('View full appointment') }} →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Contact Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Contact') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="font-medium text-gray-900">{{ $rappel->rendezVous->contact->prenom }} {{ $rappel->rendezVous->contact->nom }}</p>
                        </div>
                        @if($rappel->rendezVous->contact->emails->count() > 0)
                            <div>
                                <label class="text-xs text-gray-500">{{ __('Email') }}</label>
                                <p class="text-sm text-gray-900">{{ $rappel->rendezVous->contact->emails->first()->email }}</p>
                            </div>
                        @endif
                        @if($rappel->rendezVous->contact->numeroTelephones->count() > 0)
                            <div>
                                <label class="text-xs text-gray-500">{{ __('Phone') }}</label>
                                <p class="text-sm text-gray-900">{{ $rappel->rendezVous->contact->numeroTelephones->first()->numero_telephone }}</p>
                            </div>
                        @endif
                        <div class="pt-2">
                            <a href="{{ route('contacts.show', $rappel->rendezVous->contact) }}"
                               class="text-blue-600 hover:text-blue-800 text-sm">{{ __('View contact') }}</a>
                        </div>
                    </div>
                </div>

                <!-- Activity Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Activity') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="font-medium text-gray-900">{{ $rappel->rendezVous->activite->nom }}</p>
                        </div>
                        @if($rappel->rendezVous->activite->description)
                            <div>
                                <p class="text-sm text-gray-600">{{ Str::limit($rappel->rendezVous->activite->description, 100) }}</p>
                            </div>
                        @endif
                        <div class="pt-2">
                            <a href="{{ route('activites.show', $rappel->rendezVous->activite) }}"
                               class="text-green-600 hover:text-green-800 text-sm">{{ __('View activity') }}</a>
                        </div>
                    </div>
                </div>

                <!-- Time Comparison -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Time comparison') }}</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ __('Reminder:') }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $rappel->date_rappel->format('d/m H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ __('Appointment:') }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $rappel->rendezVous->date_debut->format('d/m H:i') }}</span>
                        </div>
                        <div class="border-t pt-2">
                            @php
                                $timeDiff = $rappel->rendezVous->date_debut->setTimeFromTimeString($rappel->rendezVous->heure_debut->format('H:i:s'))
                                    ->diffInMinutes($rappel->date_rappel);
                                $hours = intval($timeDiff / 60);
                                $minutes = $timeDiff % 60;
                            @endphp
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ __('Lead time:') }}</span>
                                <span class="text-sm font-medium text-yellow-600">
                                    @if($hours > 0){{ $hours }}h @endif
                                    @if($minutes > 0){{ $minutes }}min @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Quick actions') }}</h3>
                    <div class="space-y-3">
                        <a href="{{ route('rappels.create', ['rendez_vous_id' => $rappel->rendezVous->id]) }}"
                           class="block w-full bg-yellow-600 hover:bg-yellow-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            {{ __('Create another reminder') }}
                        </a>
                        <a href="{{ route('rappels.index') }}"
                           class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            {{ __('View all reminders') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
