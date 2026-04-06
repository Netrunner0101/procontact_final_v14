@extends('layouts.app')

@section('title', $activite->nom . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $activite->nom }}</h1>
                <p class="text-gray-600 mt-2">{{ __('Activity details') }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('activites.edit', $activite) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    {{ __('Edit') }}
                </a>
                <form action="{{ route('activites.destroy', $activite) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200"
                            onclick="return confirm('{{ __('Are you sure you want to delete this activity?') }}')">
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Activity Details Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Activity information') }}</h2>
                    
                    @if($activite->image)
                        <div class="mb-6">
                            <img src="{{ Storage::url($activite->image) }}" alt="{{ $activite->nom }}" 
                                 class="w-full h-48 object-cover rounded-lg">
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ __('Description') }}</label>
                            <p class="mt-1 text-gray-900">{{ $activite->description }}</p>
                        </div>

                        @if($activite->email)
                            <div>
                                <label class="text-sm font-medium text-gray-500">{{ __('Email') }}</label>
                                <p class="mt-1 text-gray-900">
                                    <a href="mailto:{{ $activite->email }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $activite->email }}
                                    </a>
                                </p>
                            </div>
                        @endif

                        @if($activite->numero_telephone)
                            <div>
                                <label class="text-sm font-medium text-gray-500">{{ __('Phone') }}</label>
                                <p class="mt-1 text-gray-900">
                                    <a href="tel:{{ $activite->numero_telephone }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $activite->numero_telephone }}
                                    </a>
                                </p>
                            </div>
                        @endif

                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ __('Created on') }}</label>
                            <p class="mt-1 text-gray-900">{{ $activite->created_at->format('m/d/Y \a\t H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Associated Notes -->
                @if($activite->notes->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Associated notes') }}</h2>
                        <div class="space-y-4">
                            @foreach($activite->notes as $note)
                                <div class="border-l-4 border-blue-500 pl-4 py-2">
                                    <h3 class="font-medium text-gray-900">{{ $note->titre }}</h3>
                                    <p class="text-gray-600 mt-1">{{ $note->commentaire }}</p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        {{ $note->date_create->format('m/d/Y \a\t H:i') }}
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Statistics') }}</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Linked contacts') }}</span>
                            <span class="font-medium">{{ $activite->contacts->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Appointments') }}</span>
                            <span class="font-medium">{{ $activite->rendezVous->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Notes') }}</span>
                            <span class="font-medium">{{ $activite->notes->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Linked Contacts -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Linked contacts') }}</h3>
                        <button type="button" id="addContactBtn"
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition duration-200">
                            {{ __('+ Add') }}
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
                                        <p class="text-sm text-gray-600">{{ $contact->ville ?? __('City not specified') }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('contacts.show', $contact) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm">{{ __('View') }}</a>
                                        <form action="{{ route('activites.contacts.detach', [$activite, $contact]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                {{ __('Remove') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">{{ __('No contacts linked to this activity') }}</p>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Quick actions') }}</h3>
                    <div class="space-y-3">
                        <a href="{{ route('rendez-vous.create', ['activite_id' => $activite->id]) }}" 
                           class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            {{ __('New appointment') }}
                        </a>
                        <a href="{{ route('notes.create', ['activite_id' => $activite->id]) }}" 
                           class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            {{ __('New note') }}
                        </a>
                        <a href="{{ route('statistiques.activite', $activite) }}" 
                           class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            {{ __('Statistics') }}
                        </a>
                        <a href="{{ route('activites.edit', $activite) }}" 
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            {{ __('Edit activity') }}
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
