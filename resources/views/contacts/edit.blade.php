@extends('layouts.app')

@section('title', 'Modifier Contact - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Modifier {{ $contact->nom }} {{ $contact->prenom }}</h1>

    <form method="POST" action="{{ route('contacts.update', $contact) }}" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nom" class="block font-semibold">Nom</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom', $contact->nom) }}" class="w-full border rounded p-2" required>
                @error('nom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="prenom" class="block font-semibold">Prénom</label>
                <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $contact->prenom) }}" class="w-full border rounded p-2" required>
                @error('prenom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        <div>
            <label for="status_id" class="block font-semibold">Statut</label>
            <select name="status_id" id="status_id" class="w-full border rounded p-2">
                <option value="">-- Sélectionner --</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ old('status_id', $contact->status_id) == $status->id ? 'selected' : '' }}>{{ $status->status_client }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Enregistrer</button>
            <a href="{{ route('contacts.show', $contact) }}" class="px-4 py-2 border rounded">Annuler</a>
        </div>
    </form>
</div>
@endsection
