@extends('layouts.app')

@section('title', $contact->nom . ' ' . $contact->prenom . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $contact->nom }} {{ $contact->prenom }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('contacts.edit', $contact) }}" class="px-4 py-2 bg-blue-600 text-white rounded">{{ __('Edit') }}</a>
            <a href="{{ route('contacts.index') }}" class="px-4 py-2 border rounded">{{ __('Back') }}</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="font-semibold text-gray-600">{{ __('Last Name') }}</dt>
                <dd>{{ $contact->nom }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-600">{{ __('First Name') }}</dt>
                <dd>{{ $contact->prenom }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-600">{{ __('Status') }}</dt>
                <dd>
                    @if($contact->rendez_vous_count > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-star text-green-600 mr-1"></i>
                            {{ __('Client') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            <i class="fas fa-user text-gray-500 mr-1"></i>
                            {{ __('Contact') }}
                        </span>
                    @endif
                </dd>
            </div>
        </dl>

        @if($contact->adresses->count())
        <div class="mt-4">
            <h3 class="font-semibold text-gray-600">{{ __('Addresses') }}</h3>
            <ul class="space-y-2 mt-2">
                @foreach($contact->adresses as $adresse)
                    <li class="border rounded-lg p-3 bg-gray-50">
                        @if($adresse->is_principale)
                            <span class="inline-block text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded mb-1">{{ __('Primary address') }}</span>
                        @endif
                        <div class="text-sm text-gray-800">
                            {{ trim(($adresse->rue ?? '').' '.($adresse->numero_rue ?? '')) }}@if(($adresse->rue || $adresse->numero_rue) && ($adresse->ville || $adresse->code_postal)),@endif
                            {{ trim(($adresse->code_postal ?? '').' '.($adresse->ville ?? '')) }}@if(($adresse->ville || $adresse->code_postal) && $adresse->pays),@endif
                            {{ $adresse->pays?->nom }}
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($contact->emails->count())
        <div class="mt-4">
            <h3 class="font-semibold text-gray-600">{{ __('Emails') }}</h3>
            <ul>
                @foreach($contact->emails as $email)
                    <li>{{ $email->email }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($contact->numeroTelephones->count())
        <div class="mt-4">
            <h3 class="font-semibold text-gray-600">{{ __('Phones') }}</h3>
            <ul>
                @foreach($contact->numeroTelephones as $phone)
                    <li>{{ $phone->numero_telephone }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    @if(isset($contact->rendezVous) && $contact->rendezVous->count())
    <div class="mt-6">
        <h2 class="text-xl font-bold mb-4">{{ __('Appointments') }}</h2>
        <div class="bg-white rounded-lg shadow">
            @foreach($contact->rendezVous as $rdv)
            <div class="p-4 border-b">
                <strong>{{ $rdv->titre }}</strong> - {{ $rdv->date_debut?->format('d/m/Y') }}
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
