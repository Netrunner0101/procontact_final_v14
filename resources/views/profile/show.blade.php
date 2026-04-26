@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('My Profile') }}</h1>
            <p class="text-gray-600">{{ __('Manage your personal information and security settings') }}</p>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Information -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-user mr-2"></i>
                            {{ __('Personal Information') }}
                        </h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nom -->
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Last Name') }}</label>
                                    <input type="text" id="nom" name="nom" value="{{ old('nom', $user->nom) }}" 
                                           class="form-input @error('nom') border-red-500 @enderror" required>
                                    @error('nom')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Prénom -->
                                <div>
                                    <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">{{ __('First Name') }}</label>
                                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" 
                                           class="form-input @error('prenom') border-red-500 @enderror" required>
                                    @error('prenom')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="md:col-span-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email') }}</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                           class="form-input @error('email') border-red-500 @enderror" required>
                                    @error('email')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Téléphone -->
                                <div>
                                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Phone') }}</label>
                                    <input type="tel" id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}" 
                                           class="form-input @error('telephone') border-red-500 @enderror">
                                    @error('telephone')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Numéro de rue -->
                                <div>
                                    <label for="numero_rue" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Number') }}</label>
                                    <input type="text" id="numero_rue" name="numero_rue" value="{{ old('numero_rue', $user->numero_rue) }}" 
                                           class="form-input @error('numero_rue') border-red-500 @enderror">
                                    @error('numero_rue')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Rue -->
                                <div class="md:col-span-2">
                                    <label for="rue" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Street') }}</label>
                                    <input type="text" id="rue" name="rue" value="{{ old('rue', $user->rue) }}" 
                                           class="form-input @error('rue') border-red-500 @enderror">
                                    @error('rue')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Ville -->
                                <div>
                                    <label for="ville" class="block text-sm font-medium text-gray-700 mb-2">{{ __('City') }}</label>
                                    <input type="text" id="ville" name="ville" value="{{ old('ville', $user->ville) }}" 
                                           class="form-input @error('ville') border-red-500 @enderror">
                                    @error('ville')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Code postal -->
                                <div>
                                    <label for="code_postal" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Postal Code') }}</label>
                                    <input type="text" id="code_postal" name="code_postal" value="{{ old('code_postal', $user->code_postal) }}" 
                                           class="form-input @error('code_postal') border-red-500 @enderror">
                                    @error('code_postal')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Pays -->
                                <div class="md:col-span-2">
                                    <label for="pays" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Country') }}</label>
                                    <input type="text" id="pays" name="pays" value="{{ old('pays', $user->pays) }}" 
                                           class="form-input @error('pays') border-red-500 @enderror">
                                    @error('pays')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ __('Update Profile') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Update -->
                <div class="card mt-8">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-lock mr-2"></i>
                            {{ __('Change Password') }}
                        </h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.password') }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-6">
                                <!-- Current Password -->
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Current Password') }}</label>
                                    <input type="password" id="current_password" name="current_password" 
                                           class="form-input @error('current_password') border-red-500 @enderror" required>
                                    @error('current_password')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- New Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('New Password') }}</label>
                                    <input type="password" id="password" name="password" 
                                           class="form-input @error('password') border-red-500 @enderror" required>
                                    @error('password')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Confirm Password') }}</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" 
                                           class="form-input" required>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-key mr-2"></i>
                                    {{ __('Change Password') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Profile Avatar -->
                <div class="card mb-6">
                    <div class="card-body text-center">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="Avatar" class="w-24 h-24 rounded-full mx-auto mb-4">
                        @else
                            <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <i class="fas fa-user text-gray-600 text-2xl"></i>
                            </div>
                        @endif
                        <h3 class="text-lg font-semibold text-gray-900">{{ $user->prenom }} {{ $user->nom }}</h3>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        @if($user->role)
                            <span class="badge badge-primary mt-2">{{ ucfirst($user->role) }}</span>
                        @endif
                    </div>
                </div>

                <!-- Social Accounts -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-link mr-2"></i>
                            {{ __('Linked Accounts') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-4">
                            <!-- Google Account -->
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-3" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                    <span class="text-sm font-medium">Google</span>
                                </div>
                                @if($user->google_id)
                                    <form method="POST" action="{{ route('auth.unlink') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="provider" value="google">
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                            {{ __('Unlink') }}
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('auth.google') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                        {{ __('Link') }}
                                    </a>
                                @endif
                            </div>

                            <!-- Apple Account -->
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.017 0C8.396 0 8.025.044 8.025.044c0 0-.396 2.24 1.537 4.477C11.497 6.76 13.5 6.76 13.5 6.76s2.003 0 3.938-2.24C19.371 2.284 18.975.044 18.975.044S18.604 0 15.983 0h-3.966zm2.523 7.847c-1.277 0-2.297.02-2.297.02-.277 0-.5.223-.5.5v8.133c0 .277.223.5.5.5h4.594c.277 0 .5-.223.5-.5V8.367c0-.277-.223-.5-.5-.5h-2.297z"/>
                                    </svg>
                                    <span class="text-sm font-medium">Apple</span>
                                </div>
                                @if($user->apple_id)
                                    <form method="POST" action="{{ route('auth.unlink') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="provider" value="apple">
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                            {{ __('Unlink') }}
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('auth.apple') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                        {{ __('Link') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="card mt-6">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-info-circle mr-2"></i>
                            {{ __('Account Information') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Member since:') }}</span>
                                <span class="font-medium">{{ $user->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if($user->last_login_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('Last login:') }}</span>
                                    <span class="font-medium">{{ $user->last_login_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                            @if($user->provider)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('Connected via:') }}</span>
                                    <span class="font-medium">{{ ucfirst($user->provider) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Data & Privacy (GDPR) -->
        <div class="card mt-8" x-data="{ showDelete: false }">
            <div class="card-header">
                <h2 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-shield-alt mr-2"></i>
                    {{ __('My Data & Privacy') }}
                </h2>
            </div>
            <div class="card-body space-y-6">
                <!-- Data export -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-gray-200">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Download my data') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('Get a copy of all the personal data we hold about you in a portable JSON file.') }}</p>
                    </div>
                    <a href="{{ route('profile.export') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 whitespace-nowrap">
                        <i class="fas fa-download mr-2"></i>
                        {{ __('Export my data') }}
                    </a>
                </div>

                <!-- Danger zone: delete account -->
                <div class="rounded-lg border border-red-300 bg-red-50 p-4">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-red-800">{{ __('Delete my account') }}</h3>
                            <p class="text-sm text-red-700 mt-1">
                                {{ __('Once you delete your account, all your contacts, appointments, notes, reminders, emails and phone numbers will be permanently removed. This action cannot be undone.') }}
                            </p>
                        </div>
                        <button type="button" @click="showDelete = true" x-show="!showDelete"
                                class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium bg-red-600 text-white hover:bg-red-700 whitespace-nowrap">
                            <i class="fas fa-trash mr-2"></i>
                            {{ __('Delete my account') }}
                        </button>
                    </div>

                    <div x-show="showDelete" x-cloak class="mt-4 pt-4 border-t border-red-200">
                        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                            @csrf
                            @method('DELETE')

                            <p class="text-sm text-red-800">
                                {{ __('To confirm, please retype your email and the word ":phrase" below.', ['phrase' => __('DELETE')]) }}
                            </p>

                            <div>
                                <label for="confirm_email" class="block text-sm font-medium text-red-900 mb-1">{{ __('Your email') }}</label>
                                <input type="email" id="confirm_email" name="confirm_email" autocomplete="off"
                                       class="form-input @error('confirm_email') border-red-500 @enderror" required>
                                @error('confirm_email')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="confirm_phrase" class="block text-sm font-medium text-red-900 mb-1">
                                    {{ __('Type ":phrase" to confirm', ['phrase' => __('DELETE')]) }}
                                </label>
                                <input type="text" id="confirm_phrase" name="confirm_phrase" autocomplete="off"
                                       class="form-input @error('confirm_phrase') border-red-500 @enderror" required>
                                @error('confirm_phrase')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            @if (! $user->provider)
                                <div>
                                    <label for="delete_current_password" class="block text-sm font-medium text-red-900 mb-1">{{ __('Current Password') }}</label>
                                    <input type="password" id="delete_current_password" name="current_password" autocomplete="current-password"
                                           class="form-input @error('current_password') border-red-500 @enderror" required>
                                    @error('current_password')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div class="flex flex-col sm:flex-row gap-2 pt-2">
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium bg-red-600 text-white hover:bg-red-700">
                                    <i class="fas fa-trash mr-2"></i>
                                    {{ __('Permanently delete my account') }}
                                </button>
                                <button type="button" @click="showDelete = false"
                                        class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
