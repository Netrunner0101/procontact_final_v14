@extends('layouts.app')

@section('title', 'Modifier ' . $rendezVous->titre . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Modifier le Rendez-vous</h1>
            <p class="text-gray-600 mt-2">{{ $rendezVous->titre }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('rendez-vous.update', $rendezVous) }}">
                @csrf
                @method('PUT')
                
                <!-- Required Fields -->
                <div class="mb-6">
                    <label for="titre" class="block text-sm font-medium text-gray-700 mb-2">Titre du rendez-vous *</label>
                    <input type="text" id="titre" name="titre" value="{{ old('titre', $rendezVous->titre) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('titre') border-red-500 @enderror">
                    @error('titre')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('description') border-red-500 @enderror">{{ old('description', $rendezVous->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact and Activity Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-2">Contact *</label>
                        <select id="contact_id" name="contact_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('contact_id') border-red-500 @enderror">
                            <option value="">Sélectionner un contact</option>
                            @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}" {{ (old('contact_id', $rendezVous->contact_id) == $contact->id) ? 'selected' : '' }}>
                                    {{ $contact->prenom }} {{ $contact->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('contact_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="activite_id" class="block text-sm font-medium text-gray-700 mb-2">Activité *</label>
                        <select id="activite_id" name="activite_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('activite_id') border-red-500 @enderror">
                            <option value="">Sélectionner une activité</option>
                            @foreach($activites as $activite)
                                <option value="{{ $activite->id }}" {{ (old('activite_id', $rendezVous->activite_id) == $activite->id) ? 'selected' : '' }}>
                                    {{ $activite->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('activite_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Date Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-2">Date de début *</label>
                        <input type="date" id="date_debut" name="date_debut" value="{{ old('date_debut', $rendezVous->date_debut->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('date_debut') border-red-500 @enderror">
                        @error('date_debut')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-2">Date de fin *</label>
                        <input type="date" id="date_fin" name="date_fin" value="{{ old('date_fin', $rendezVous->date_fin->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('date_fin') border-red-500 @enderror">
                        @error('date_fin')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Time Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="heure_debut" class="block text-sm font-medium text-gray-700 mb-2">Heure de début *</label>
                        <input type="time" id="heure_debut" name="heure_debut" value="{{ old('heure_debut', $rendezVous->heure_debut->format('H:i')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('heure_debut') border-red-500 @enderror">
                        @error('heure_debut')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="heure_fin" class="block text-sm font-medium text-gray-700 mb-2">Heure de fin *</label>
                        <input type="time" id="heure_fin" name="heure_fin" value="{{ old('heure_fin', $rendezVous->heure_fin->format('H:i')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 @error('heure_fin') border-red-500 @enderror">
                        @error('heure_fin')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('rendez-vous.show', $rendezVous) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        Annuler
                    </a>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateDebutInput = document.getElementById('date_debut');
    const dateFinInput = document.getElementById('date_fin');

    // Auto-fill end date when start date changes
    dateDebutInput.addEventListener('change', function() {
        if (this.value > dateFinInput.value) {
            dateFinInput.value = this.value;
        }
    });

    // Set minimum date to today for new appointments
    const today = new Date().toISOString().split('T')[0];
    dateDebutInput.setAttribute('min', today);
    dateFinInput.setAttribute('min', today);
});
</script>
@endsection
