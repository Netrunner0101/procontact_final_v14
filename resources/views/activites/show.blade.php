@extends('layouts.app')

@section('title', $activite->nom . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $activite->nom }}</h1>
                <p class="text-gray-600 mt-2">Détails de l'activité</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('activites.edit', $activite) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Modifier
                </a>
                <form action="{{ route('activites.destroy', $activite) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Activity Details Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations de l'activité</h2>
                    
                    @if($activite->image)
                        <div class="mb-6">
                            <img src="{{ Storage::url($activite->image) }}" alt="{{ $activite->nom }}" 
                                 class="w-full h-48 object-cover rounded-lg">
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Description</label>
                            <p class="mt-1 text-gray-900">{{ $activite->description }}</p>
                        </div>

                        @if($activite->email)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Email</label>
                                <p class="mt-1 text-gray-900">
                                    <a href="mailto:{{ $activite->email }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $activite->email }}
                                    </a>
                                </p>
                            </div>
                        @endif

                        @if($activite->numero_telephone)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Téléphone</label>
                                <p class="mt-1 text-gray-900">
                                    <a href="tel:{{ $activite->numero_telephone }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $activite->numero_telephone }}
                                    </a>
                                </p>
                            </div>
                        @endif

                        <div>
                            <label class="text-sm font-medium text-gray-500">Créé le</label>
                            <p class="mt-1 text-gray-900">{{ $activite->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Associated Notes -->
                @if($activite->notes->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Notes associées</h2>
                        <div class="space-y-4">
                            @foreach($activite->notes as $note)
                                <div class="border-l-4 border-blue-500 pl-4 py-2">
                                    <h3 class="font-medium text-gray-900">{{ $note->titre }}</h3>
                                    <p class="text-gray-600 mt-1">{{ $note->commentaire }}</p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        {{ $note->date_create->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Contacts liés</span>
                            <span class="font-medium">{{ $activite->contacts->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rendez-vous</span>
                            <span class="font-medium">{{ $activite->rendezVous->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Notes</span>
                            <span class="font-medium">{{ $activite->notes->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Linked Contacts -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Contacts liés</h3>
                        <button type="button" id="addContactBtn" 
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition duration-200">
                            + Ajouter
                        </button>
                    </div>

                    @if($activite->contacts->count() > 0)
                        <div class="space-y-3">
                            @foreach($activite->contacts as $contact)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ $contact->prenom }} {{ $contact->nom }}
                                        </p>
                                        <p class="text-sm text-gray-600">{{ $contact->ville ?? 'Ville non renseignée' }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('contacts.show', $contact) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm">Voir</a>
                                        <form action="{{ route('activites.contacts.detach', [$activite, $contact]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                Retirer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Aucun contact lié à cette activité</p>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                    <div class="space-y-3">
                        <a href="{{ route('rendez-vous.create', ['activite_id' => $activite->id]) }}" 
                           class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            Nouveau rendez-vous
                        </a>
                        <a href="{{ route('notes.create', ['activite_id' => $activite->id]) }}" 
                           class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            Nouvelle note
                        </a>
                        <a href="{{ route('statistiques.activite', $activite) }}" 
                           class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            📊 Statistiques
                        </a>
                        <a href="{{ route('activites.edit', $activite) }}" 
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            Modifier l'activité
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
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ajouter un contact à l'activité</h3>
        <form id="addContactForm" action="{{ route('activites.contacts.attach', $activite) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-2">Sélectionner un contact</label>
                <select id="contact_id" name="contact_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Choisir un contact...</option>
                    @foreach(Auth::user()->contacts()->whereNotIn('id', $activite->contacts->pluck('id'))->get() as $contact)
                        <option value="{{ $contact->id }}">{{ $contact->prenom }} {{ $contact->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelAddContact" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition duration-200">
                    Annuler
                </button>
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition duration-200">
                    Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addContactBtn = document.getElementById('addContactBtn');
    const addContactModal = document.getElementById('addContactModal');
    const cancelAddContact = document.getElementById('cancelAddContact');

    addContactBtn.addEventListener('click', function() {
        addContactModal.classList.remove('hidden');
        addContactModal.classList.add('flex');
    });

    cancelAddContact.addEventListener('click', function() {
        addContactModal.classList.add('hidden');
        addContactModal.classList.remove('flex');
    });

    // Close modal when clicking outside
    addContactModal.addEventListener('click', function(e) {
        if (e.target === addContactModal) {
            addContactModal.classList.add('hidden');
            addContactModal.classList.remove('flex');
        }
    });
});
</script>
@endsection
