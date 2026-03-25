@extends('layouts.app')

@section('title', 'Créer un Client')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Créer un Client</h1>

    <form method="POST" action="{{ route('admin.clients.store') }}" class="space-y-4">
        @csrf
        <div>
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" value="{{ old('nom') }}" class="w-full border rounded p-2" required>
            @error('nom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" class="w-full border rounded p-2" required>
            @error('prenom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full border rounded p-2" required>
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" class="w-full border rounded p-2" required>
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border rounded p-2" required>
        </div>
        @if(isset($contacts) && $contacts->count())
        <div>
            <label for="contact_id">Contact associé (optionnel)</label>
            <select name="contact_id" id="contact_id" class="w-full border rounded p-2">
                <option value="">-- Aucun --</option>
                @foreach($contacts as $contact)
                    <option value="{{ $contact->id }}">{{ $contact->nom }} {{ $contact->prenom }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Créer</button>
            <a href="{{ route('admin.clients.index') }}" class="px-4 py-2 rounded border">Annuler</a>
        </div>
    </form>
</div>
@endsection
