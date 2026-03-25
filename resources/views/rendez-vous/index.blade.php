@extends('layouts.app')

@section('title', 'Rendez-vous - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Rendez-vous</h1>
            <p class="text-gray-600 mt-2">Gérez vos rendez-vous clients</p>
        </div>
        <a href="{{ route('rendez-vous.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition duration-200">
            Nouveau Rendez-vous
        </a>
    </div>

    @if($rendezVous->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Titre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Client
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Activité
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date & Heure
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rendezVous as $rdv)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $rdv->titre }}</div>
                                @if($rdv->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($rdv->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $rdv->contact->prenom }} {{ $rdv->contact->nom }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $rdv->activite->nom }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $rdv->date_debut->format('d/m/Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $rdv->heure_debut->format('H:i') }} - {{ $rdv->heure_fin->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $now = now();
                                    $rdvDateTime = $rdv->date_debut->setTimeFromTimeString($rdv->heure_debut->format('H:i:s'));
                                    $status = $rdvDateTime->isPast() ? 'Terminé' : ($rdvDateTime->isToday() ? 'Aujourd\'hui' : 'À venir');
                                    $statusColor = $rdvDateTime->isPast() ? 'bg-gray-100 text-gray-800' : ($rdvDateTime->isToday() ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('rendez-vous.show', $rdv) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Détails</a>
                                <a href="{{ route('rendez-vous.edit', $rdv) }}" class="text-green-600 hover:text-green-900 mr-3">Modifier</a>
                                <form action="{{ route('rendez-vous.destroy', $rdv) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $rendezVous->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="text-gray-500 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun rendez-vous</h3>
            <p class="text-gray-600 mb-4">Commencez par créer votre premier rendez-vous.</p>
            <a href="{{ route('rendez-vous.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition duration-200">
                Créer un Rendez-vous
            </a>
        </div>
    @endif
</div>
@endsection
