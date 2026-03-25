@extends('layouts.app')

@section('title', 'Nouveau Contact - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-6 lg:py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-gradient mb-2">Nouveau Contact</h1>
                    <p class="text-gray-600 text-lg">Créer un nouveau contact dans votre système</p>
                </div>
                <div class="hidden lg:block">
                    <a href="{{ route('contacts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour à la liste</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                        1
                    </div>
                    <span class="ml-2 text-sm font-medium text-blue-600">Informations personnelles</span>
                </div>
                <div class="flex-1 h-px bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm font-medium">
                        2
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Contact & Adresse</span>
                </div>
                <div class="flex-1 h-px bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm font-medium">
                        3
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Finalisation</span>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card card-elevated">
            <div class="card-header">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                    Informations du Contact
                </h2>
                <p class="text-gray-600 mt-1">Remplissez les informations de base du contact</p>
            </div>
            
            <form method="POST" action="{{ route('contacts.store') }}" id="contactForm">
                @csrf
                
                <div class="card-body space-y-8">
                    <!-- Section 1: Personal Information -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 pb-3 border-b border-gray-200">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-id-card text-blue-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Identité</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                                       class="form-input @error('nom') border-red-500 @enderror"
                                       placeholder="Entrez le nom de famille">
                                @error('nom')
                                    <p class="text-red-500 text-sm mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required
                                       class="form-input @error('prenom') border-red-500 @enderror"
                                       placeholder="Entrez le prénom">
                                @error('prenom')
                                    <p class="text-red-500 text-sm mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                <!-- Dynamic Email Fields -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">Emails *</label>
                        <button type="button" id="addEmail" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                            + Ajouter Email
                        </button>
                    </div>
                    <div id="emailContainer">
                        <div class="email-field flex gap-2 mb-2">
                            <input type="email" name="emails[]" value="{{ old('emails.0') }}" required
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('emails.0') border-red-500 @enderror"
                                   placeholder="email@exemple.com">
                            <button type="button" class="remove-email bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded" style="display: none;">
                                Supprimer
                            </button>
                        </div>
                    </div>
                    @error('emails.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dynamic Phone Fields -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">Numéros de téléphone *</label>
                        <button type="button" id="addPhone" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                            + Ajouter Téléphone
                        </button>
                    </div>
                    <div id="phoneContainer">
                        <div class="phone-field flex gap-2 mb-2">
                            <input type="tel" name="phones[]" value="{{ old('phones.0') }}" required
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phones.0') border-red-500 @enderror"
                                   placeholder="+33 1 23 45 67 89">
                            <button type="button" class="remove-phone bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded" style="display: none;">
                                Supprimer
                            </button>
                        </div>
                    </div>
                    @error('phones.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Optional Address Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="rue" class="block text-sm font-medium text-gray-700 mb-2">Rue</label>
                        <input type="text" id="rue" name="rue" value="{{ old('rue') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="numero" class="block text-sm font-medium text-gray-700 mb-2">Numéro</label>
                        <input type="text" id="numero" name="numero" value="{{ old('numero') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="ville" class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
                        <input type="text" id="ville" name="ville" value="{{ old('ville') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="code_postal" class="block text-sm font-medium text-gray-700 mb-2">Code Postal</label>
                        <input type="text" id="code_postal" name="code_postal" value="{{ old('code_postal') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="pays" class="block text-sm font-medium text-gray-700 mb-2">Pays</label>
                        <input type="text" id="pays" name="pays" value="{{ old('pays', 'France') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                    <!-- Section 3: Status -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 pb-3 border-b border-gray-200">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tags text-green-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Classification</h3>
                        </div>
                        
                        <div class="form-group">
                            <label for="status_id" class="form-label">Status Client</label>
                            <select id="status_id" name="status_id" class="form-select">
                                <option value="">Sélectionner un status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                                        {{ $status->status_client }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Le status aide à catégoriser vos contacts</p>
                        </div>
                    </div>
                </div>
                
                <!-- Form Footer -->
                <div class="card-footer">
                    <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Les champs marqués d'un * sont obligatoires</span>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('contacts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                <span>Annuler</span>
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                <span>Créer le Contact</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Email management
    let emailCount = 1;
    document.getElementById('addEmail').addEventListener('click', function() {
        const container = document.getElementById('emailContainer');
        const newField = document.createElement('div');
        newField.className = 'email-field flex gap-2 mb-2';
        newField.innerHTML = `
            <input type="email" name="emails[]" required
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="email@exemple.com">
            <button type="button" class="remove-email bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded">
                Supprimer
            </button>
        `;
        container.appendChild(newField);
        emailCount++;
        updateRemoveButtons('email');
    });

    // Phone management
    let phoneCount = 1;
    document.getElementById('addPhone').addEventListener('click', function() {
        const container = document.getElementById('phoneContainer');
        const newField = document.createElement('div');
        newField.className = 'phone-field flex gap-2 mb-2';
        newField.innerHTML = `
            <input type="tel" name="phones[]" required
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="+33 1 23 45 67 89">
            <button type="button" class="remove-phone bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded">
                Supprimer
            </button>
        `;
        container.appendChild(newField);
        phoneCount++;
        updateRemoveButtons('phone');
    });

    // Remove field handlers
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-email')) {
            e.target.parentElement.remove();
            emailCount--;
            updateRemoveButtons('email');
        }
        if (e.target.classList.contains('remove-phone')) {
            e.target.parentElement.remove();
            phoneCount--;
            updateRemoveButtons('phone');
        }
    });

    function updateRemoveButtons(type) {
        const fields = document.querySelectorAll(`.${type}-field`);
        fields.forEach((field, index) => {
            const removeBtn = field.querySelector(`.remove-${type}`);
            if (fields.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }
});
</script>
@endsection
