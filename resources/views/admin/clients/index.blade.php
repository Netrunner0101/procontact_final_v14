@extends('layouts.app')

@section('title', 'Gestion des Clients')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestion des Clients</h1>
        <a href="{{ route('admin.clients.create') }}" class="btn-primary px-4 py-2 rounded">Nouveau Client</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="p-3 text-left">Nom</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr class="border-b">
                        <td class="p-3">{{ $client->nom }} {{ $client->prenom }}</td>
                        <td class="p-3">{{ $client->email }}</td>
                        <td class="p-3">
                            <a href="{{ route('admin.clients.show', $client) }}">Voir</a>
                            <a href="{{ route('admin.clients.edit', $client) }}">Modifier</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-3 text-center">Aucun client</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $clients->links() }}
    </div>
</div>
@endsection
