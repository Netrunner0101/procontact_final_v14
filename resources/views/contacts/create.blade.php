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

        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <div class="flex items-center" id="step-indicator-1">
                    <div class="step-circle w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium" style="background-color: #843728;">
                        1
                    </div>
                    <span class="ml-2 text-sm font-medium" style="color: #843728;">{{ __('Activity') }}</span>
                </div>
                <div class="flex-1 h-px bg-gray-300" id="step-line-1"></div>
                <div class="flex items-center" id="step-indicator-2">
                    <div class="step-circle w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm font-medium">
                        2
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-500">{{ __('Personal Info & Contact') }}</span>
                </div>
                <div class="flex-1 h-px bg-gray-300" id="step-line-2"></div>
                <div class="flex items-center" id="step-indicator-3">
                    <div class="step-circle w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm font-medium">
                        3
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-500">{{ __('Address') }}</span>
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

                <div class="card-body space-y-8">

                    <!-- Step 1: Activity Selection -->
                    <div class="step-section" data-step="1">
                        <div class="space-y-6">
                            <div class="flex items-center space-x-3 pb-3 border-b border-gray-200">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 55, 40, 0.1);">
                                    <i class="fas fa-briefcase" style="color: #843728;"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('Activity') }}</h3>
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
                                <p class="text-sm text-gray-500 mt-1">{{ __('Optionally link this contact to one of your activities') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Personal Info & Contact -->
                    <div class="step-section hidden" data-step="2">
                        <div class="space-y-6">
                            <!-- Identity -->
                            <div class="flex items-center space-x-3 pb-3 border-b border-gray-200">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 55, 40, 0.1);">
                                    <i class="fas fa-id-card" style="color: #843728;"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('Identity') }}</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                            <!-- Emails -->
                            <div class="flex items-center space-x-3 pb-3 border-b border-gray-200 mt-8">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 55, 40, 0.1);">
                                    <i class="fas fa-envelope" style="color: #843728;"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('Emails') }}</h3>
                            </div>

                            <div class="form-group">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="form-label mb-0">{{ __('Email addresses') }} *</label>
                                    <button type="button" id="addEmail" class="btn btn-secondary text-xs px-3 py-1">
                                        <i class="fas fa-plus mr-1"></i>{{ __('Add Email') }}
                                    </button>
                                </div>
                                <div id="emailContainer">
                                    <div class="email-field flex gap-2 mb-2">
                                        <input type="email" name="emails[]" value="{{ old('emails.0') }}" required
                                               class="form-input flex-1 @error('emails.0') border-red-500 @enderror"
                                               placeholder="{{ __('email@example.com') }}">
                                        <button type="button" class="remove-email btn btn-secondary px-3 py-2 text-red-600" style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('emails.*')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phones -->
                            <div class="flex items-center space-x-3 pb-3 border-b border-gray-200 mt-8">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 55, 40, 0.1);">
                                    <i class="fas fa-phone" style="color: #843728;"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('Phone numbers') }}</h3>
                            </div>

                            <div class="form-group">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="form-label mb-0">{{ __('Phone numbers') }} *</label>
                                    <button type="button" id="addPhone" class="btn btn-secondary text-xs px-3 py-1">
                                        <i class="fas fa-plus mr-1"></i>{{ __('Add Phone') }}
                                    </button>
                                </div>
                                <div id="phoneContainer">
                                    <div class="phone-field flex gap-2 mb-2">
                                        <input type="tel" name="phones[]" value="{{ old('phones.0') }}" required
                                               class="form-input flex-1 @error('phones.0') border-red-500 @enderror"
                                               pattern="[0-9+\s()-]+"
                                               title="{{ __('Only digits, +, spaces, dashes and parentheses allowed') }}"
                                               placeholder="+33 1 23 45 67 89">
                                        <button type="button" class="remove-phone btn btn-secondary px-3 py-2 text-red-600" style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('phones.*')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Address -->
                    <div class="step-section hidden" data-step="3">
                        <div class="space-y-6">
                            <div class="flex items-center space-x-3 pb-3 border-b border-gray-200">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 55, 40, 0.1);">
                                    <i class="fas fa-map-marker-alt" style="color: #843728;"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('Address') }}</h3>
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

                <!-- Form Footer with Navigation -->
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
                            <button type="button" id="prevBtn" class="btn btn-secondary" style="display: none;">
                                <i class="fas fa-arrow-left"></i>
                                <span>{{ __('Previous') }}</span>
                            </button>
                            <button type="button" id="nextBtn" class="btn btn-primary">
                                <span>{{ __('Next') }}</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                            <button type="submit" id="submitBtn" class="btn btn-primary" style="display: none;">
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
    let currentStep = 1;
    const totalSteps = 3;
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    // Detect which step has validation errors on page load
    const errorStep = detectErrorStep();
    if (errorStep > 1) {
        currentStep = errorStep;
    }
    updateStepDisplay();

    function detectErrorStep() {
        const allErrors = document.querySelectorAll('.text-red-500');
        for (const error of allErrors) {
            const section = error.closest('.step-section');
            if (section) {
                return parseInt(section.dataset.step);
            }
        }
        return 1;
    }

    function updateStepDisplay() {
        // Show/hide step sections
        document.querySelectorAll('.step-section').forEach(function(section) {
            if (parseInt(section.dataset.step) === currentStep) {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
            }
        });

        // Update progress indicators
        for (let i = 1; i <= totalSteps; i++) {
            const indicator = document.getElementById('step-indicator-' + i);
            const circle = indicator.querySelector('.step-circle');
            const label = indicator.querySelector('span');

            if (i < currentStep) {
                // Completed step
                circle.style.backgroundColor = '#3a6a3a';
                circle.className = 'step-circle w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium';
                circle.innerHTML = '<i class="fas fa-check"></i>';
                label.style.color = '#3a6a3a';
                label.className = 'ml-2 text-sm font-medium';
            } else if (i === currentStep) {
                // Active step
                circle.style.backgroundColor = '#843728';
                circle.className = 'step-circle w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium';
                circle.innerHTML = i;
                label.style.color = '#843728';
                label.className = 'ml-2 text-sm font-medium';
            } else {
                // Upcoming step
                circle.style.backgroundColor = '';
                circle.className = 'step-circle w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm font-medium';
                circle.innerHTML = i;
                label.style.color = '';
                label.className = 'ml-2 text-sm font-medium text-gray-500';
            }
        }

        // Update step connection lines
        for (let i = 1; i < totalSteps; i++) {
            const line = document.getElementById('step-line-' + i);
            if (i < currentStep) {
                line.style.backgroundColor = '#3a6a3a';
            } else {
                line.style.backgroundColor = '';
                line.className = 'flex-1 h-px bg-gray-300';
            }
        }

        // Show/hide navigation buttons
        prevBtn.style.display = currentStep > 1 ? 'inline-flex' : 'none';
        nextBtn.style.display = currentStep < totalSteps ? 'inline-flex' : 'none';
        submitBtn.style.display = currentStep === totalSteps ? 'inline-flex' : 'none';
    }

    function validateCurrentStep() {
        const currentSection = document.querySelector('.step-section[data-step="' + currentStep + '"]');
        const requiredFields = currentSection.querySelectorAll('[required]');
        let valid = true;

        for (const field of requiredFields) {
            if (!field.reportValidity()) {
                valid = false;
                break;
            }
        }
        return valid;
    }

    nextBtn.addEventListener('click', function() {
        if (validateCurrentStep()) {
            currentStep++;
            updateStepDisplay();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    prevBtn.addEventListener('click', function() {
        currentStep--;
        updateStepDisplay();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Email management
    document.getElementById('addEmail').addEventListener('click', function() {
        const container = document.getElementById('emailContainer');
        const newField = document.createElement('div');
        newField.className = 'email-field flex gap-2 mb-2';
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
        newField.className = 'phone-field flex gap-2 mb-2';
        newField.innerHTML = `
            <input type="tel" name="phones[]" required
                   class="form-input flex-1"
                   pattern="[0-9+\\s()-]+"
                   title="{{ __('Only digits, +, spaces, dashes and parentheses allowed') }}"
                   placeholder="+33 1 23 45 67 89">
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

    // Real-time phone number filtering
    document.addEventListener('input', function(e) {
        if (e.target.name === 'phones[]') {
            e.target.value = e.target.value.replace(/[^0-9+\s()-]/g, '');
        }
    });
});
</script>
@endsection
