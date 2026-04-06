@extends('layouts.app')

@section('title', __('New Appointment') . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('New Appointment') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('Schedule a new client appointment') }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('rendez-vous.store') }}" id="rdvForm">
                @csrf

                <!-- Required Fields -->
                <div class="mb-6">
                    <label for="titre" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Appointment title') }} *</label>
                    <input type="text" id="titre" name="titre" value="{{ old('titre') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('titre') border-red-500 @enderror">
                    @error('titre')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description') }}</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact and Activity Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Contact') }} *</label>
                        <select id="contact_id" name="contact_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('contact_id') border-red-500 @enderror">
                            <option value="">{{ __('Select a contact') }}</option>
                            @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}" {{ old('contact_id') == $contact->id ? 'selected' : '' }}>
                                    {{ $contact->prenom }} {{ $contact->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('contact_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="activite_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Activity') }} *</label>
                        @if(isset($selectedActiviteId) && $selectedActiviteId)
                            @php
                                $selectedActivite = $activites->firstWhere('id', $selectedActiviteId);
                            @endphp
                            <input type="hidden" name="activite_id" value="{{ $selectedActiviteId }}">
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">
                                <i class="fas fa-briefcase mr-1 text-gray-500"></i>
                                {{ $selectedActivite->nom ?? __('Activity') }}
                            </div>
                        @else
                            <select id="activite_id" name="activite_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('activite_id') border-red-500 @enderror">
                                <option value="">{{ __('Select an activity') }}</option>
                                @foreach($activites as $activite)
                                    <option value="{{ $activite->id }}" {{ old('activite_id') == $activite->id ? 'selected' : '' }}>
                                        {{ $activite->nom }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('activite_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Date Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Start date') }} *</label>
                        <input type="date" id="date_debut" name="date_debut" value="{{ old('date_debut') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('date_debut') border-red-500 @enderror">
                        @error('date_debut')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-2">{{ __('End date') }} *</label>
                        <input type="date" id="date_fin" name="date_fin" value="{{ old('date_fin') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('date_fin') border-red-500 @enderror">
                        @error('date_fin')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Time Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="heure_debut" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Start time') }} *</label>
                        <input type="time" id="heure_debut" name="heure_debut" value="{{ old('heure_debut') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('heure_debut') border-red-500 @enderror">
                        @error('heure_debut')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="heure_fin" class="block text-sm font-medium text-gray-700 mb-2">{{ __('End time') }} *</label>
                        <input type="time" id="heure_fin" name="heure_fin" value="{{ old('heure_fin') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('heure_fin') border-red-500 @enderror">
                        @error('heure_fin')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email Notification Info -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg" id="emailInfo">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-envelope text-blue-600 mr-2"></i>
                        <h4 class="text-sm font-medium text-blue-900">{{ __('Automatic email notification') }}</h4>
                    </div>
                    <p class="text-sm text-blue-700">{{ __('An email notification will be automatically sent to the client when this appointment is created.') }}</p>
                    <p id="contactEmail" class="text-sm text-blue-700 mt-1 font-medium hidden"></p>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('rendez-vous.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        {{ __('Create Appointment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactSelect = document.getElementById('contact_id');
    const contactEmailEl = document.getElementById('contactEmail');
    const dateDebutInput = document.getElementById('date_debut');
    const dateFinInput = document.getElementById('date_fin');

    // Contact data for email preview
    const contacts = @json($contacts->mapWithKeys(function($contact) {
        return [$contact->id => $contact->emails->first()->email ?? __('No email')];
    }));

    // Auto-fill end date when start date changes
    dateDebutInput.addEventListener('change', function() {
        if (!dateFinInput.value) {
            dateFinInput.value = this.value;
        }
    });

    // Show email preview when contact is selected
    function updateEmailPreview() {
        if (contactSelect.value) {
            const email = contacts[contactSelect.value] || '{{ __('No email available') }}';
            contactEmailEl.textContent = '{{ __('Email will be sent to:') }} ' + email;
            contactEmailEl.classList.remove('hidden');
        } else {
            contactEmailEl.classList.add('hidden');
        }
    }

    contactSelect.addEventListener('change', updateEmailPreview);
    updateEmailPreview();

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    dateDebutInput.setAttribute('min', today);
    dateFinInput.setAttribute('min', today);
});
</script>
@endsection
