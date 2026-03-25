@extends('layouts.app')

@section('title', $rendezVous->titre . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $rendezVous->titre }}</h1>
                <p class="text-gray-600 mt-2">Détails du rendez-vous</p>
            </div>
            <div class="flex space-x-3">
                <button type="button" id="emailBtn" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Envoyer par Email
                </button>
                <a href="{{ route('rendez-vous.edit', $rendezVous) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Modifier
                </a>
                <form action="{{ route('rendez-vous.destroy', $rendezVous) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Appointment Details Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations du rendez-vous</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Titre</label>
                            <p class="mt-1 text-gray-900 text-lg">{{ $rendezVous->titre }}</p>
                        </div>

                        @if($rendezVous->description)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Description</label>
                                <p class="mt-1 text-gray-900">{{ $rendezVous->description }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Date de début</label>
                                <p class="mt-1 text-gray-900">{{ $rendezVous->date_debut->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Date de fin</label>
                                <p class="mt-1 text-gray-900">{{ $rendezVous->date_fin->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Heure de début</label>
                                <p class="mt-1 text-gray-900">{{ $rendezVous->heure_debut->format('H:i') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Heure de fin</label>
                                <p class="mt-1 text-gray-900">{{ $rendezVous->heure_fin->format('H:i') }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Durée</label>
                            @php
                                $debut = \Carbon\Carbon::parse($rendezVous->heure_debut);
                                $fin = \Carbon\Carbon::parse($rendezVous->heure_fin);
                                $duree = $debut->diffInMinutes($fin);
                                $heures = intval($duree / 60);
                                $minutes = $duree % 60;
                            @endphp
                            <p class="mt-1 text-gray-900">
                                @if($heures > 0){{ $heures }}h @endif
                                @if($minutes > 0){{ $minutes }}min @endif
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Créé le</label>
                            <p class="mt-1 text-gray-900">{{ $rendezVous->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                @if($rendezVous->notes && $rendezVous->notes->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Notes associées</h2>
                        <div class="space-y-4">
                            @foreach($rendezVous->notes as $note)
                                <div class="border-l-4 border-purple-500 pl-4 py-2">
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

                <!-- Reminders Section -->
                @if($rendezVous->rappels && $rendezVous->rappels->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Rappels</h2>
                        <div class="space-y-3">
                            @foreach($rendezVous->rappels as $rappel)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $rappel->date_rappel->format('d/m/Y à H:i') }}</p>
                                        <p class="text-sm text-gray-600">Fréquence: {{ $rappel->frequence }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun rappel</h3>
                            <p class="mt-1 text-sm text-gray-500">Créez un rappel pour ce rendez-vous.</p>
                            <div class="mt-6">
                                <a href="{{ route('rappels.create', ['rendez_vous_id' => $rendezVous->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                                    Premier Rappel
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Contact Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="font-medium text-gray-900">{{ $rendezVous->contact->prenom }} {{ $rendezVous->contact->nom }}</p>
                        </div>
                        @if($rendezVous->contact->emails && $rendezVous->contact->emails->count() > 0)
                            <div>
                                <label class="text-xs text-gray-500">Email</label>
                                <p class="text-sm text-gray-900">{{ $rendezVous->contact->emails->first()->email }}</p>
                            </div>
                        @endif
                        @if($rendezVous->contact->numeroTelephones && $rendezVous->contact->numeroTelephones->count() > 0)
                            <div>
                                <label class="text-xs text-gray-500">Téléphone</label>
                                <p class="text-sm text-gray-900">{{ $rendezVous->contact->numeroTelephones->first()->numero_telephone }}</p>
                            </div>
                        @endif
                        @if($rendezVous->contact->ville)
                            <div>
                                <label class="text-xs text-gray-500">Ville</label>
                                <p class="text-sm text-gray-900">{{ $rendezVous->contact->ville }}</p>
                            </div>
                        @endif
                        <div class="pt-2">
                            <a href="{{ route('contacts.show', $rendezVous->contact) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm">Voir le contact</a>
                        </div>
                    </div>
                </div>

                <!-- Activity Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Activité</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="font-medium text-gray-900">{{ $rendezVous->activite->nom }}</p>
                        </div>
                        @if($rendezVous->activite->description)
                            <div>
                                <p class="text-sm text-gray-600">{{ Str::limit($rendezVous->activite->description, 100) }}</p>
                            </div>
                        @endif
                        <div class="pt-2">
                            <a href="{{ route('activites.show', $rendezVous->activite) }}" 
                               class="text-green-600 hover:text-green-800 text-sm">Voir l'activité</a>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>
                    @php
                        $now = now();
                        $rdvDateTime = $rendezVous->date_debut->setTimeFromTimeString($rendezVous->heure_debut->format('H:i:s'));
                        $status = $rdvDateTime->isPast() ? 'Terminé' : ($rdvDateTime->isToday() ? 'Aujourd\'hui' : 'À venir');
                        $statusColor = $rdvDateTime->isPast() ? 'bg-gray-100 text-gray-800' : ($rdvDateTime->isToday() ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                    @endphp
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColor }}">
                        {{ $status }}
                    </span>
                    @if($rdvDateTime->isFuture())
                        <p class="text-sm text-gray-600 mt-2">
                            Dans {{ $rdvDateTime->diffForHumans() }}
                        </p>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                    <div class="space-y-3">
                        <button type="button" id="addReminderBtn"
                                class="block w-full bg-yellow-600 hover:bg-yellow-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            Ajouter un Rappel
                        </button>
                        <button type="button" id="addNoteBtn"
                                class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center px-4 py-2 rounded transition duration-200">
                            Ajouter une Note
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Modal -->
<div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Envoyer les détails par email</h3>
        <form id="emailForm" action="{{ route('rendez-vous.email', $rendezVous) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="recipient_email" class="block text-sm font-medium text-gray-700 mb-2">Email destinataire</label>
                <input type="email" id="recipient_email" name="recipient_email" 
                       value="{{ $rendezVous->contact->emails->first()->email ?? '' }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="cc_emails" class="block text-sm font-medium text-gray-700 mb-2">CC (optionnel)</label>
                <input type="email" id="cc_emails" name="cc_emails" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="email1@exemple.com, email2@exemple.com">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelEmail" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition duration-200">
                    Annuler
                </button>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition duration-200">
                    Envoyer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailBtn = document.getElementById('emailBtn');
    const emailModal = document.getElementById('emailModal');
    const cancelEmail = document.getElementById('cancelEmail');

    emailBtn.addEventListener('click', function() {
        emailModal.classList.remove('hidden');
        emailModal.classList.add('flex');
    });

    cancelEmail.addEventListener('click', function() {
        emailModal.classList.add('hidden');
        emailModal.classList.remove('flex');
    });

    // Close modal when clicking outside
    emailModal.addEventListener('click', function(e) {
        if (e.target === emailModal) {
            emailModal.classList.add('hidden');
            emailModal.classList.remove('flex');
        }
    });
});
</script>
@endsection
