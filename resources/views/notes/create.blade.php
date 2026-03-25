@extends('layouts.app')

@section('title', 'Nouvelle Note - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Nouvelle Note</h1>
            <p class="text-gray-600 mt-2">Créer une nouvelle note personnelle</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('notes.store') }}">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="titre" class="block text-sm font-medium text-gray-700 mb-2">Titre *</label>
                            <input type="text" id="titre" name="titre" 
                                   value="{{ old('titre') }}" required
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
                                      placeholder="Contenu de la note...">{{ old('contenu') }}</textarea>
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
                                <option value="Basse" {{ old('priorite') == 'Basse' ? 'selected' : '' }}>Basse</option>
                                <option value="Normale" {{ old('priorite', 'Normale') == 'Normale' ? 'selected' : '' }}>Normale</option>
                                <option value="Haute" {{ old('priorite') == 'Haute' ? 'selected' : '' }}>Haute</option>
                                <option value="Urgente" {{ old('priorite') == 'Urgente' ? 'selected' : '' }}>Urgente</option>
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
                                            {{ (old('contact_id', $contact?->id) == $userContact->id) ? 'selected' : '' }}>
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
                                            {{ (old('activite_id', $activite?->id) == $userActivite->id) ? 'selected' : '' }}>
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
                                            {{ (old('rendez_vous_id', $rendezVous?->id) == $userRendezVous->id) ? 'selected' : '' }}>
                                        {{ $userRendezVous->titre }} - {{ $userRendezVous->contact->prenom }} {{ $userRendezVous->contact->nom }}
                                        ({{ $userRendezVous->date_debut->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('rendez_vous_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quick Templates -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Modèles rapides</label>
                            <div class="space-y-2">
                                <button type="button" class="template-btn w-full text-left px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm transition duration-200" 
                                        data-title="Suivi client" 
                                        data-content="## Points abordés&#10;- &#10;- &#10;- &#10;&#10;## Actions à suivre&#10;- &#10;- &#10;&#10;## Prochaine étape&#10;Date: &#10;Action: ">
                                    📋 Suivi client
                                </button>
                                <button type="button" class="template-btn w-full text-left px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm transition duration-200"
                                        data-title="Idée projet"
                                        data-content="## Description de l'idée&#10;&#10;&#10;## Avantages&#10;- &#10;- &#10;&#10;## Défis potentiels&#10;- &#10;- &#10;&#10;## Prochaines étapes&#10;- ">
                                    💡 Idée projet
                                </button>
                                <button type="button" class="template-btn w-full text-left px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm transition duration-200"
                                        data-title="Compte-rendu réunion"
                                        data-content="## Participants&#10;- &#10;&#10;## Ordre du jour&#10;- &#10;- &#10;&#10;## Décisions prises&#10;- &#10;&#10;## Actions à réaliser&#10;- [ ] &#10;- [ ] ">
                                    📝 Compte-rendu réunion
                                </button>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        @if($contact || $activite || $rendezVous)
                            <div class="p-4 bg-indigo-50 rounded-lg">
                                <h4 class="text-sm font-medium text-indigo-900 mb-2">Éléments pré-sélectionnés :</h4>
                                @if($contact)
                                    <p class="text-sm text-indigo-700">👤 Contact: {{ $contact->prenom }} {{ $contact->nom }}</p>
                                @endif
                                @if($activite)
                                    <p class="text-sm text-indigo-700">🏢 Activité: {{ $activite->nom }}</p>
                                @endif
                                @if($rendezVous)
                                    <p class="text-sm text-indigo-700">📅 Rendez-vous: {{ $rendezVous->titre }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('notes.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        Annuler
                    </a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        Créer la Note
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const titreInput = document.getElementById('titre');
    const contenuTextarea = document.getElementById('contenu');
    const templateButtons = document.querySelectorAll('.template-btn');

    // Template buttons
    templateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const title = this.dataset.title;
            const content = this.dataset.content.replace(/&#10;/g, '\n');
            
            if (confirm('Utiliser ce modèle ? Cela remplacera le contenu actuel.')) {
                titreInput.value = title;
                contenuTextarea.value = content;
                
                // Focus on content for editing
                contenuTextarea.focus();
                contenuTextarea.setSelectionRange(contenuTextarea.value.length, contenuTextarea.value.length);
            }
        });
    });

    // Auto-resize textarea
    contenuTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});
</script>
@endsection
