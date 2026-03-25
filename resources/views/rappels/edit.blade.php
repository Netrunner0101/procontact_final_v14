@extends('layouts.app')

@section('title', 'Modifier Rappel - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Modifier le Rappel</h1>
            <p class="text-gray-600 mt-2">{{ $rappel->rendezVous->titre }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('rappels.update', $rappel) }}">
                @csrf
                @method('PUT')
                
                <!-- Appointment Selection -->
                <div class="mb-6">
                    <label for="rendez_vous_id" class="block text-sm font-medium text-gray-700 mb-2">Rendez-vous *</label>
                    <select id="rendez_vous_id" name="rendez_vous_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('rendez_vous_id') border-red-500 @enderror">
                        <option value="">Sélectionner un rendez-vous</option>
                        @foreach($userRendezVous as $rdv)
                            <option value="{{ $rdv->id }}" 
                                    {{ (old('rendez_vous_id', $rappel->rendez_vous_id) == $rdv->id) ? 'selected' : '' }}
                                    data-date="{{ $rdv->date_debut->format('Y-m-d') }}"
                                    data-time="{{ $rdv->heure_debut->format('H:i') }}">
                                {{ $rdv->titre }} - {{ $rdv->contact->prenom }} {{ $rdv->contact->nom }} 
                                ({{ $rdv->date_debut->format('d/m/Y') }} à {{ $rdv->heure_debut->format('H:i') }})
                            </option>
                        @endforeach
                    </select>
                    @error('rendez_vous_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reminder Date and Time -->
                <div class="mb-6">
                    <label for="date_rappel" class="block text-sm font-medium text-gray-700 mb-2">Date et heure du rappel *</label>
                    <input type="datetime-local" id="date_rappel" name="date_rappel" 
                           value="{{ old('date_rappel', $rappel->date_rappel->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('date_rappel') border-red-500 @enderror">
                    @error('date_rappel')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-600 mt-1">Le rappel doit être programmé dans le futur</p>
                </div>

                <!-- Frequency Selection -->
                <div class="mb-6">
                    <label for="frequence" class="block text-sm font-medium text-gray-700 mb-2">Fréquence *</label>
                    <select id="frequence" name="frequence" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('frequence') border-red-500 @enderror">
                        <option value="">Sélectionner une fréquence</option>
                        <option value="Une fois" {{ old('frequence', $rappel->frequence) == 'Une fois' ? 'selected' : '' }}>Une fois</option>
                        <option value="Quotidien" {{ old('frequence', $rappel->frequence) == 'Quotidien' ? 'selected' : '' }}>Quotidien</option>
                        <option value="Hebdomadaire" {{ old('frequence', $rappel->frequence) == 'Hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="Mensuel" {{ old('frequence', $rappel->frequence) == 'Mensuel' ? 'selected' : '' }}>Mensuel</option>
                    </select>
                    @error('frequence')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quick Reminder Options -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Rappels rapides</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <button type="button" class="quick-reminder bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition duration-200" data-minutes="15">
                            15 min avant
                        </button>
                        <button type="button" class="quick-reminder bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition duration-200" data-minutes="60">
                            1h avant
                        </button>
                        <button type="button" class="quick-reminder bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition duration-200" data-minutes="1440">
                            1 jour avant
                        </button>
                        <button type="button" class="quick-reminder bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition duration-200" data-minutes="10080">
                            1 semaine avant
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Cliquez sur un bouton pour définir rapidement l'heure du rappel</p>
                </div>

                <!-- Current vs New Reminder Info -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Informations actuelles :</h4>
                    <div class="text-sm text-blue-700 space-y-1">
                        <p><strong>Rappel actuel :</strong> {{ $rappel->date_rappel->format('d/m/Y à H:i') }}</p>
                        <p><strong>Fréquence actuelle :</strong> {{ $rappel->frequence }}</p>
                        <p><strong>Rendez-vous :</strong> {{ $rappel->rendezVous->date_debut->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>

                <!-- Appointment Preview -->
                <div id="appointmentPreview" class="mb-6 p-4 bg-yellow-50 rounded-lg">
                    <h4 class="text-sm font-medium text-yellow-900 mb-2">Aperçu du rendez-vous :</h4>
                    <div id="previewContent" class="text-sm text-yellow-700"></div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('rappels.show', $rappel) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        Annuler
                    </a>
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rendezVousSelect = document.getElementById('rendez_vous_id');
    const dateRappelInput = document.getElementById('date_rappel');
    const appointmentPreview = document.getElementById('appointmentPreview');
    const previewContent = document.getElementById('previewContent');
    const quickReminderButtons = document.querySelectorAll('.quick-reminder');

    // Set minimum datetime to now
    const now = new Date();
    const minDateTime = now.toISOString().slice(0, 16);
    dateRappelInput.setAttribute('min', minDateTime);

    // Show appointment preview when selection changes
    function updatePreview() {
        const selectedOption = rendezVousSelect.options[rendezVousSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const appointmentText = selectedOption.textContent;
            previewContent.textContent = appointmentText;
            appointmentPreview.classList.remove('hidden');
        } else {
            appointmentPreview.classList.add('hidden');
        }
    }

    rendezVousSelect.addEventListener('change', updatePreview);

    // Quick reminder buttons
    quickReminderButtons.forEach(button => {
        button.addEventListener('click', function() {
            const minutes = parseInt(this.dataset.minutes);
            const selectedOption = rendezVousSelect.options[rendezVousSelect.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                const appointmentDate = selectedOption.dataset.date;
                const appointmentTime = selectedOption.dataset.time;
                
                if (appointmentDate && appointmentTime) {
                    const appointmentDateTime = new Date(appointmentDate + 'T' + appointmentTime);
                    const reminderDateTime = new Date(appointmentDateTime.getTime() - (minutes * 60000));
                    
                    // Check if reminder time is in the future
                    if (reminderDateTime > now) {
                        const reminderString = reminderDateTime.toISOString().slice(0, 16);
                        dateRappelInput.value = reminderString;
                        
                        // Highlight the selected button temporarily
                        this.classList.add('bg-yellow-200');
                        setTimeout(() => {
                            this.classList.remove('bg-yellow-200');
                        }, 1000);
                    } else {
                        alert('Le rappel serait dans le passé. Veuillez sélectionner un rendez-vous futur ou ajuster l\'heure manuellement.');
                    }
                }
            } else {
                alert('Veuillez d\'abord sélectionner un rendez-vous.');
            }
        });
    });

    // Initialize preview
    updatePreview();
});
</script>
@endsection
