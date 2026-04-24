@extends('layouts.app')

@section('title', __('New Contact') . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-6 lg:py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-gradient mb-2">{{ __('New Contact') }}</h1>
                    <p class="text-gray-600 text-lg">{{ __('Create a new contact in your system') }}</p>
                </div>
                <div class="hidden lg:block">
                    <a href="{{ route('contacts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>{{ __('Back to list') }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card card-elevated">
            <div class="card-header">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-user-plus mr-3" style="color: #843728;"></i>
                    {{ __('Contact Information') }}
                </h2>
                <p class="text-gray-600 mt-1">{{ __('Fill in the basic contact information') }}</p>
            </div>

            <form method="POST" action="{{ route('contacts.store') }}" id="contactForm">
                @csrf

                <div class="card-body">

                    <div class="space-y-10">

                        <!-- Activity Selection -->
                        <div class="space-y-8">
                            <div class="flex items-center space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 55, 40, 0.1);">
                                    <i class="fas fa-briefcase" style="color: #843728;"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Activity') }}</h3>
                                    <p class="text-sm text-gray-500">{{ __('Choose which activity to link this contact to') }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="activite_id" class="form-label">{{ __('Link to an activity') }}</label>
                                <select id="activite_id" name="activite_id" class="form-select">
                                    <option value="">{{ __('No activity (standalone contact)') }}</option>
                                    @foreach($activites as $activite)
                                        <option value="{{ $activite->id }}" {{ (old('activite_id', $selectedActiviteId) == $activite->id) ? 'selected' : '' }}>
                                            {{ $activite->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-sm text-gray-500 mt-2">{{ __('Optionally link this contact to one of your activities') }}</p>
                            </div>
                        </div>

                        <!-- Identity -->
                        <div>
                            <div class="flex items-center space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 55, 40, 0.1);">
                                    <i class="fas fa-id-card" style="color: #843728;"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Identity') }}</h3>
                                    <p class="text-sm text-gray-500">{{ __('Enter the contact\'s name') }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div class="form-group">
                                    <label for="nom" class="form-label">{{ __('Last Name') }} *</label>
                                    <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                                           class="form-input @error('nom') border-red-500 @enderror"
                                           placeholder="{{ __('Enter the last name') }}">
                                    @error('nom')
                                        <p class="text-red-500 text-sm mt-1 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="prenom" class="form-label">{{ __('First Name') }} *</label>
                                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required
                                           class="form-input @error('prenom') border-red-500 @enderror"
                                           placeholder="{{ __('Enter the first name') }}">
                                    @error('prenom')
                                        <p class="text-red-500 text-sm mt-1 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Emails -->
                        <div>
                            <div class="flex items-center space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 55, 40, 0.1);">
                                    <i class="fas fa-envelope" style="color: #843728;"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Emails') }}</h3>
                                    <p class="text-sm text-gray-500">{{ __('Add one or more email addresses') }}</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="flex justify-between items-center mb-3">
                                    <label class="form-label mb-0">{{ __('Email addresses') }} *</label>
                                    <button type="button" id="addEmail" class="btn btn-secondary text-xs px-3 py-1">
                                        <i class="fas fa-plus mr-1"></i>{{ __('Add Email') }}
                                    </button>
                                </div>
                                <div id="emailContainer" class="space-y-3">
                                    <div class="email-field flex gap-3">
                                        <input type="email" name="emails[]" value="{{ old('emails.0') }}" required
                                               class="form-input flex-1 @error('emails.0') border-red-500 @enderror"
                                               placeholder="{{ __('email@example.com') }}">
                                        <button type="button" class="remove-email btn btn-secondary px-3 py-2 text-red-600" style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('emails.*')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Phones -->
                        <div>
                            <div class="flex items-center space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 55, 40, 0.1);">
                                    <i class="fas fa-phone" style="color: #843728;"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Phone numbers') }}</h3>
                                    <p class="text-sm text-gray-500">{{ __('Add one or more phone numbers with country prefix') }}</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="flex justify-between items-center mb-3">
                                    <label class="form-label mb-0">{{ __('Phone numbers') }} *</label>
                                    <button type="button" id="addPhone" class="btn btn-secondary text-xs px-3 py-1">
                                        <i class="fas fa-plus mr-1"></i>{{ __('Add Phone') }}
                                    </button>
                                </div>
                                <div id="phoneContainer" class="space-y-3">
                                    <div class="phone-field flex gap-3 items-start">
                                        <select class="phone-prefix form-select" style="width: 140px; flex-shrink: 0;">
                                            <option value="+33">+33 FR</option>
                                            <option value="+1">+1 US/CA</option>
                                            <option value="+44">+44 UK</option>
                                            <option value="+49">+49 DE</option>
                                            <option value="+34">+34 ES</option>
                                            <option value="+39">+39 IT</option>
                                            <option value="+32">+32 BE</option>
                                            <option value="+41">+41 CH</option>
                                            <option value="+352">+352 LU</option>
                                            <option value="+212">+212 MA</option>
                                            <option value="+216">+216 TN</option>
                                            <option value="+213">+213 DZ</option>
                                            <option value="+351">+351 PT</option>
                                            <option value="+31">+31 NL</option>
                                            <option value="+43">+43 AT</option>
                                            <option value="+48">+48 PL</option>
                                            <option value="+90">+90 TR</option>
                                            <option value="+7">+7 RU</option>
                                        </select>
                                        <input type="tel" class="phone-number form-input flex-1 @error('phones.0') border-red-500 @enderror"
                                               value="{{ old('phones.0') ? preg_replace('/^\+\d+\s*/', '', old('phones.0')) : '' }}"
                                               required
                                               pattern="[0-9\s()-]+"
                                               title="{{ __('Only digits, spaces, dashes and parentheses allowed') }}"
                                               placeholder="6 12 34 56 78">
                                        <input type="hidden" name="phones[]" class="phone-combined">
                                        <button type="button" class="remove-phone btn btn-secondary px-3 py-2 text-red-600" style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('phones.*')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="space-y-6">
                            <div class="flex items-center space-x-3 pb-4 border-b border-gray-200">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 55, 40, 0.1);">
                                    <i class="fas fa-map-marker-alt" style="color: #843728;"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Address') }}</h3>
                                    <p class="text-sm text-gray-500">{{ __('Optional address information') }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label for="rue" class="form-label">{{ __('Street') }}</label>
                                    <input type="text" id="rue" name="rue" value="{{ old('rue') }}"
                                           class="form-input"
                                           placeholder="{{ __('Enter the street') }}">
                                </div>

                                <div class="form-group">
                                    <label for="numero" class="form-label">{{ __('Number') }}</label>
                                    <input type="text" id="numero" name="numero" value="{{ old('numero') }}"
                                           class="form-input"
                                           placeholder="{{ __('Enter the number') }}">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="form-group">
                                    <label for="ville" class="form-label">{{ __('City') }}</label>
                                    <input type="text" id="ville" name="ville" value="{{ old('ville') }}"
                                           class="form-input"
                                           placeholder="{{ __('Enter the city') }}">
                                </div>

                                <div class="form-group">
                                    <label for="code_postal" class="form-label">{{ __('Postal Code') }}</label>
                                    <input type="text" id="code_postal" name="code_postal" value="{{ old('code_postal') }}"
                                           class="form-input"
                                           placeholder="{{ __('Enter the postal code') }}">
                                </div>

                                <div class="form-group">
                                    <label for="pays" class="form-label">{{ __('Country') }}</label>
                                    <input type="text" id="pays" name="pays" value="{{ old('pays', 'France') }}"
                                           class="form-input"
                                           placeholder="{{ __('Enter the country') }}">
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Form Footer -->
                <div class="card-footer">
                    <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>{{ __('Fields marked with * are required') }}</span>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('contacts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                <span>{{ __('Cancel') }}</span>
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                <span>{{ __('Create Contact') }}</span>
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
    const phonePrefixOptions = `
        <option value="+33">+33 FR</option>
        <option value="+1">+1 US/CA</option>
        <option value="+44">+44 UK</option>
        <option value="+49">+49 DE</option>
        <option value="+34">+34 ES</option>
        <option value="+39">+39 IT</option>
        <option value="+32">+32 BE</option>
        <option value="+41">+41 CH</option>
        <option value="+352">+352 LU</option>
        <option value="+212">+212 MA</option>
        <option value="+216">+216 TN</option>
        <option value="+213">+213 DZ</option>
        <option value="+351">+351 PT</option>
        <option value="+31">+31 NL</option>
        <option value="+43">+43 AT</option>
        <option value="+48">+48 PL</option>
        <option value="+90">+90 TR</option>
        <option value="+7">+7 RU</option>
    `;

    // Combine phone prefix + number before form submit
    document.getElementById('contactForm').addEventListener('submit', function() {
        document.querySelectorAll('.phone-field').forEach(function(field) {
            const prefix = field.querySelector('.phone-prefix').value;
            const number = field.querySelector('.phone-number').value.trim();
            field.querySelector('.phone-combined').value = prefix + ' ' + number;
        });
    });

    // Email management
    document.getElementById('addEmail').addEventListener('click', function() {
        const container = document.getElementById('emailContainer');
        const newField = document.createElement('div');
        newField.className = 'email-field flex gap-3';
        newField.innerHTML = `
            <input type="email" name="emails[]" required
                   class="form-input flex-1"
                   placeholder="{{ __('email@example.com') }}">
            <button type="button" class="remove-email btn btn-secondary px-3 py-2 text-red-600">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(newField);
        updateRemoveButtons('email');
    });

    // Phone management
    document.getElementById('addPhone').addEventListener('click', function() {
        const container = document.getElementById('phoneContainer');
        const newField = document.createElement('div');
        newField.className = 'phone-field flex gap-3 items-start';
        newField.innerHTML = `
            <select class="phone-prefix form-select" style="width: 140px; flex-shrink: 0;">
                ${phonePrefixOptions}
            </select>
            <input type="tel" class="phone-number form-input flex-1" required
                   pattern="[0-9\\s()-]+"
                   title="{{ __('Only digits, spaces, dashes and parentheses allowed') }}"
                   placeholder="6 12 34 56 78">
            <input type="hidden" name="phones[]" class="phone-combined">
            <button type="button" class="remove-phone btn btn-secondary px-3 py-2 text-red-600">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(newField);
        updateRemoveButtons('phone');
    });

    // Remove field handlers
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-email')) {
            e.target.closest('.email-field').remove();
            updateRemoveButtons('email');
        }
        if (e.target.closest('.remove-phone')) {
            e.target.closest('.phone-field').remove();
            updateRemoveButtons('phone');
        }
    });

    function updateRemoveButtons(type) {
        const fields = document.querySelectorAll('.' + type + '-field');
        fields.forEach(function(field) {
            const removeBtn = field.querySelector('.remove-' + type);
            removeBtn.style.display = fields.length > 1 ? 'inline-flex' : 'none';
        });
    }

    // Real-time phone number filtering (only on the number input, not prefix)
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('phone-number')) {
            e.target.value = e.target.value.replace(/[^0-9\s()-]/g, '');
        }
    });
});
</script>
@endsection
