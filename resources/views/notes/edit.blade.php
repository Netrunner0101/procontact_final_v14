@extends('layouts.app')

@section('title', 'Modifier Note - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Modifier la Note</h1>
            <p class="text-gray-600 mt-2">{{ $note->titre }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('notes.update', $note) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="titre" class="block text-sm font-medium text-gray-700 mb-2">Titre *</label>
                            <input type="text" id="titre" name="titre" 
                                   value="{{ old('titre', $note->titre) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('titre') border-red-500 @enderror"
                                   placeholder="Titre de la note">
                            @error('titre')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div>
                            <label for="contenu" class="block text-sm font-medium text-gray-700 mb-2">Contenu *</label>
                            <textarea id="contenu" name="contenu" rows="12" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('contenu') border-red-500 @enderror"
                                      placeholder="Contenu de la note...">{{ old('contenu', $note->contenu) }}</textarea>
                            @error('contenu')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-600 mt-1">Vous pouvez utiliser du texte formaté, des listes, etc.</p>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Priority -->
                        <div>
                            <label for="priorite" class="block text-sm font-medium text-gray-700 mb-2">Priorité *</label>
                            <select id="priorite" name="priorite" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('priorite') border-red-500 @enderror">
                                <option value="">Sélectionner une priorité</option>
                                <option value="Basse" {{ old('priorite', $note->priorite) == 'Basse' ? 'selected' : '' }}>Basse</option>
                                <option value="Normale" {{ old('priorite', $note->priorite) == 'Normale' ? 'selected' : '' }}>Normale</option>
                                <option value="Haute" {{ old('priorite', $note->priorite) == 'Haute' ? 'selected' : '' }}>Haute</option>
                                <option value="Urgente" {{ old('priorite', $note->priorite) == 'Urgente' ? 'selected' : '' }}>Urgente</option>
                            </select>
                            @error('priorite')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Associated Contact -->
                        <div>
                            <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-2">Contact associé</label>
                            <select id="contact_id" name="contact_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('contact_id') border-red-500 @enderror">
                                <option value="">Aucun contact</option>
                                @foreach($userContacts as $userContact)
                                    <option value="{{ $userContact->id }}" 
                                            {{ (old('contact_id', $note->contact_id) == $userContact->id) ? 'selected' : '' }}>
                                        {{ $userContact->prenom }} {{ $userContact->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('contact_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Associated Activity -->
                        <div>
                            <label for="activite_id" class="block text-sm font-medium text-gray-700 mb-2">Activité associée</label>
                            <select id="activite_id" name="activite_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('activite_id') border-red-500 @enderror">
                                <option value="">Aucune activité</option>
                                @foreach($userActivites as $userActivite)
                                    <option value="{{ $userActivite->id }}" 
                                            {{ (old('activite_id', $note->activite_id) == $userActivite->id) ? 'selected' : '' }}>
                                        {{ $userActivite->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('activite_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Associated Appointment -->
                        <div>
                            <label for="rendez_vous_id" class="block text-sm font-medium text-gray-700 mb-2">Rendez-vous associé</label>
                            <select id="rendez_vous_id" name="rendez_vous_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('rendez_vous_id') border-red-500 @enderror">
                                <option value="">Aucun rendez-vous</option>
                                @foreach($userRendezVous as $userRendezVous)
                                    <option value="{{ $userRendezVous->id }}" 
                                            {{ (old('rendez_vous_id', $note->rendez_vous_id) == $userRendezVous->id) ? 'selected' : '' }}>
                                        {{ $userRendezVous->titre }} - {{ $userRendezVous->contact->prenom }} {{ $userRendezVous->contact->nom }}
                                        ({{ $userRendezVous->date_debut->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('rendez_vous_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Information -->
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">Informations actuelles :</h4>
                            <div class="text-sm text-blue-700 space-y-1">
                                <p><strong>Priorité :</strong> {{ $note->priorite }}</p>
                                <p><strong>Créée le :</strong> {{ $note->created_at->format('d/m/Y à H:i') }}</p>
                                @if($note->updated_at != $note->created_at)
                                    <p><strong>Modifiée le :</strong> {{ $note->updated_at->format('d/m/Y à H:i') }}</p>
                                @endif
                                <p><strong>Caractères :</strong> {{ strlen($note->contenu) }}</p>
                            </div>
                        </div>

                        <!-- Character Counter -->
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Statistiques du contenu :</h4>
                            <div class="text-sm text-gray-700 space-y-1">
                                <p>Caractères : <span id="charCount">{{ strlen($note->contenu) }}</span></p>
                                <p>Mots : <span id="wordCount">{{ str_word_count($note->contenu) }}</span></p>
                                <p>Lignes : <span id="lineCount">{{ substr_count($note->contenu, "\n") + 1 }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('notes.show', $note) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        Annuler
                    </a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contenuTextarea = document.getElementById('contenu');
    const charCount = document.getElementById('charCount');
    const wordCount = document.getElementById('wordCount');
    const lineCount = document.getElementById('lineCount');

    function updateStats() {
        const content = contenuTextarea.value;
        charCount.textContent = content.length;
        wordCount.textContent = content.trim() === '' ? 0 : content.trim().split(/\s+/).length;
        lineCount.textContent = content.split('\n').length;
    }

    // Update stats on input
    contenuTextarea.addEventListener('input', function() {
        updateStats();
        
        // Auto-resize textarea
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });

    // Initial stats update
    updateStats();
});
</script>
@endsection
