@extends('layouts.client')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('My appointments') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('View all your past and upcoming appointments') }}</p>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="{{ route('client.appointments') }}" class="flex flex-col md:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Search') }}</label>
                <input type="text"
                       id="search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('Title, description, activity...') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Status Filter -->
            <div class="md:w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Status') }}</label>
                <select id="status"
                        name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('All') }}</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>{{ __('Upcoming') }}</option>
                    <option value="today" {{ request('status') === 'today' ? 'selected' : '' }}>{{ __('Today') }}</option>
                    <option value="past" {{ request('status') === 'past' ? 'selected' : '' }}>{{ __('Past') }}</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="md:w-32 flex items-end">
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Appointments List -->
    <div class="bg-white rounded-lg shadow">
        @if($appointments->count() > 0)
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ __(':count appointment(s) found', ['count' => $appointments->total()]) }}
                </h2>
            </div>

            <div class="divide-y divide-gray-200">
                @foreach($appointments as $appointment)
                <div class="p-6 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $appointment->titre }}</h3>
                                @if(\Carbon\Carbon::parse($appointment->date_heure)->isPast())
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                        {{ __('Completed') }}
                                    </span>
                                @elseif(\Carbon\Carbon::parse($appointment->date_heure)->isToday())
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        {{ __('Today') }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        {{ __('Upcoming') }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-2 space-y-1">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">{{ __('Activity:') }}</span> {{ $appointment->activite->nom }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">{{ __('Date:') }}</span>
                                    {{ \Carbon\Carbon::parse($appointment->date_heure)->format('d/m/Y') }} {{ __('at') }} {{ \Carbon\Carbon::parse($appointment->date_heure)->format('H:i') }}
                                    <span class="text-blue-600">({{ \Carbon\Carbon::parse($appointment->date_heure)->diffForHumans() }})</span>
                                </p>
                                @if($appointment->description)
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">{{ __('Description:') }}</span>
                                    {{ Str::limit($appointment->description, 100) }}
                                </p>
                                @endif
                            </div>
                        </div>

                        <div class="flex-shrink-0 ml-6">
                            <a href="{{ route('client.appointment', $appointment) }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm transition duration-200">
                                {{ __('View details') }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $appointments->withQueryString()->links() }}
            </div>
        @else
            <div class="p-6">
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No appointments found') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'status']))
                            {{ __('No appointments match your search criteria.') }}
                        @else
                            {{ __('You do not have any scheduled appointments yet.') }}
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status']))
                        <div class="mt-4">
                            <a href="{{ route('client.appointments') }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                {{ __('Clear filters') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
