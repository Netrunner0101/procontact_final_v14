@extends('layouts.app')

@section('title', __('Client') . ' - ' . $client->nom)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">{{ $client->nom }} {{ $client->prenom }}</h1>
    <p>{{ __('Email') }}: {{ $client->email }}</p>
    <div class="mt-4">
        <a href="{{ route('admin.clients.edit', $client) }}" class="px-4 py-2 bg-blue-600 text-white rounded">{{ __('Edit') }}</a>
        <a href="{{ route('admin.clients.index') }}" class="px-4 py-2 border rounded">{{ __('Back') }}</a>
    </div>
</div>
@endsection
