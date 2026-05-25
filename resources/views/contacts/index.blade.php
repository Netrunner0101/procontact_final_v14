@extends('layouts.app')

@section('title', __('Contacts') . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Contacts') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('Manage your client contacts') }}</p>
        </div>
        <a href="{{ route('contacts.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-sm transition duration-200">
            <i class="fas fa-plus"></i>
            {{ __('New Contact') }}
        </a>
    </div>

    @if($contacts->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Name') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Status') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Phone') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Email') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Address') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($contacts as $contact)
                        @php
                            $adresse = $contact->adressePrincipale;
                            $rue = trim(($adresse?->numero_rue ? $adresse->numero_rue.' ' : '').($adresse?->rue ?? ''));
                            $localite = trim(($adresse?->code_postal ? $adresse->code_postal.' ' : '').($adresse?->ville ?? ''));
                            $phone = $contact->numeroTelephones->first()?->numero_telephone;
                            $email = $contact->emails->first()?->email;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $contact->prenom }} {{ $contact->nom }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($contact->rendez_vous_count > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" title="{{ __('Client') }}">
                                        <i class="fas fa-star text-green-600 mr-1"></i>
                                        {{ __('Client') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600" title="{{ __('Contact') }}">
                                        <i class="fas fa-user text-gray-500 mr-1"></i>
                                        {{ __('Contact') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if($phone)
                                    <a href="tel:{{ $phone }}" class="inline-flex items-center gap-2 hover:text-blue-600">
                                        <i class="fas fa-phone text-gray-400 text-xs"></i>
                                        {{ $phone }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if($email)
                                    <a href="mailto:{{ $email }}" class="inline-flex items-center gap-2 hover:text-blue-600">
                                        <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                        {{ $email }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                @if($adresse && ! $adresse->isEmpty())
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-map-marker-alt text-gray-400 text-xs mt-1"></i>
                                        <div class="leading-tight">
                                            @if($rue)<div>{{ $rue }}</div>@endif
                                            @if($localite)<div class="text-gray-500">{{ $localite }}</div>@endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('contacts.show', $contact) }}"
                                       title="{{ __('View') }}"
                                       aria-label="{{ __('View') }}"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-indigo-600 hover:text-white hover:bg-indigo-600 transition duration-150">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('contacts.edit', $contact) }}"
                                       title="{{ __('Edit') }}"
                                       aria-label="{{ __('Edit') }}"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-amber-600 hover:text-white hover:bg-amber-600 transition duration-150">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="{{ __('Delete') }}"
                                                aria-label="{{ __('Delete') }}"
                                                onclick="return confirm('{{ __('Are you sure you want to delete this contact?') }}')"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 hover:text-white hover:bg-red-600 transition duration-150">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $contacts->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="text-gray-500 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No contacts') }}</h3>
            <p class="text-gray-600 mb-4">{{ __('Start by creating your first contact.') }}</p>
            <a href="{{ route('contacts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                {{ __('Create a contact') }}
            </a>
        </div>
    @endif
</div>
@endsection
