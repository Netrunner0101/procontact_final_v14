@extends('layouts.app')

@section('title', 'Modifier Client')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Modifier {{ $client->nom }} {{ $client->prenom }}</h1>

    <form method="POST" action="{{ route('admin.clients.update', $client) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" value="{{ old('nom', $client->nom) }}" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $client->prenom) }}" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}" class="w-full border rounded p-2" required>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Enregistrer</button>
            <a href="{{ route('admin.clients.show', $client) }}" class="px-4 py-2 border rounded">Annuler</a>
        </div>
    </form>
</div>
@endsection
