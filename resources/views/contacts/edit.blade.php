@extends('layouts.app')

@section('title', __('Edit Contact') . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-6 lg:py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold" style="color: #1b1c1a;">{{ __('Edit :first :last', ['first' => $contact->prenom, 'last' => $contact->nom]) }}</h1>
            <p class="mt-2" style="color: #44483e;">{{ __('Update the contact information') }}</p>
        </div>

        <div class="bg-white rounded-xl p-6" style="box-shadow: 0 4px 12px rgba(27,28,26,0.03);">
            <form method="POST" action="{{ route('contacts.update', $contact) }}" id="editContactForm">
                @csrf
                @method('PUT')

                <!-- Name Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="nom" class="block text-sm font-semibold mb-2" style="color: #374151;">{{ __('Last Name') }} *</label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $contact->nom) }}" required
                               class="w-full px-3 py-2 rounded-lg @error('nom') border-2 border-red-500 @enderror"
                               style="border: 2px solid #efecea;">
                        @error('nom') <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="prenom" class="block text-sm font-semibold mb-2" style="color: #374151;">{{ __('First Name') }} *</label>
                        <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $contact->prenom) }}" required
                               class="w-full px-3 py-2 rounded-lg @error('prenom') border-2 border-red-500 @enderror"
                               style="border: 2px solid #efecea;">
                        @error('prenom') <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Dynamic Email Fields -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-semibold" style="color: #374151;">{{ __('Emails') }} *</label>
                        <button type="button" id="addEmail" class="text-white px-3 py-1 rounded text-sm" style="background: #3a6a3a;">
                            {{ __('+ Add Email') }}
                        </button>
                    </div>
                    <div id="emailContainer" class="space-y-3">
                        @foreach($contact->emails as $index => $emailObj)
                            <div class="email-field flex gap-3">
                                <input type="email" name="emails[]" value="{{ old('emails.' . $index, $emailObj->email) }}" required
                                       class="flex-1 px-3 py-2 rounded-lg @error('emails.' . $index) border-2 border-red-500 @enderror"
                                       style="border: 2px solid #efecea;"
                                       placeholder="{{ __('email@example.com') }}">
                                <button type="button" class="remove-email text-white px-3 py-2 rounded" style="background: #ba1a1a; {{ $loop->count <= 1 ? 'display: none;' : '' }}">
                                    −
                                </button>
                            </div>
                        @endforeach
                        @if($contact->emails->isEmpty())
                            <div class="email-field flex gap-3">
                                <input type="email" name="emails[]" value="" required
                                       class="flex-1 px-3 py-2 rounded-lg"
                                       style="border: 2px solid #efecea;"
                                       placeholder="{{ __('email@example.com') }}">
                                <button type="button" class="remove-email text-white px-3 py-2 rounded" style="background: #ba1a1a; display: none;">
                                    −
                                </button>
                            </div>
                        @endif
                    </div>
                    @error('emails.*') <p class="text-sm mt-1" style="color: #ba1a1a;">{{ __('The email address is not valid.') }}</p> @enderror
                </div>

                <!-- Dynamic Phone Fields with Country Prefix -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-semibold" style="color: #374151;">{{ __('Phone numbers') }} *</label>
                        <button type="button" id="addPhone" class="text-white px-3 py-1 rounded text-sm" style="background: #3a6a3a;">
                            {{ __('+ Add Phone') }}
                        </button>
                    </div>
                    @php
                        // Map indicatif (e.g. "+33") -> ISO code, to recover the
                        // country of legacy numbers that still embed the prefix.
                        $indicatifToCode = $paysList->pluck('code', 'indicatif');
                    @endphp
                    <div id="phoneContainer" class="space-y-3">
                        @forelse($contact->numeroTelephones as $index => $phoneObj)
                            @php
                                $selectedCode = old('phone_pays.' . $index, $phoneObj->pays_code);
                                $phoneNumber = old('phones.' . $index, $phoneObj->numero_telephone);
                                // Legacy fallback: pays_code empty but prefix glued in string.
                                if (! $selectedCode) {
                                    $selectedCode = 'BE';
                                    foreach ($indicatifToCode as $ind => $code) {
                                        if (str_starts_with((string) $phoneNumber, $ind)) {
                                            $selectedCode = $code;
                                            $phoneNumber = trim(substr($phoneNumber, strlen($ind)));
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <div class="phone-field flex gap-3 items-start">
                                <select name="phone_pays[]" class="phone-prefix form-select" style="width: 160px; flex-shrink: 0;">
                                    @foreach($paysList as $pays)
                                        <option value="{{ $pays->code }}" @selected($selectedCode === $pays->code)>{{ $pays->indicatif }} {{ $pays->code }}</option>
                                    @endforeach
                                </select>
                                <input type="tel" name="phones[]" class="phone-number flex-1 px-3 py-2 rounded-lg @error('phones.' . $index) border-2 border-red-500 @enderror"
                                       value="{{ $phoneNumber }}" required
                                       style="border: 2px solid #efecea;"
                                       pattern="[0-9\s()-]+"
                                       title="{{ __('Only digits, spaces, dashes and parentheses allowed') }}"
                                       placeholder="6 12 34 56 78">
                                <button type="button" class="remove-phone text-white px-3 py-2 rounded" style="background: #ba1a1a; {{ $loop->count <= 1 ? 'display: none;' : '' }}">
                                    −
                                </button>
                            </div>
                        @empty
                            <div class="phone-field flex gap-3 items-start">
                                <select name="phone_pays[]" class="phone-prefix form-select" style="width: 160px; flex-shrink: 0;">
                                    @foreach($paysList as $pays)
                                        <option value="{{ $pays->code }}" @selected($pays->code === 'BE')>{{ $pays->indicatif }} {{ $pays->code }}</option>
                                    @endforeach
                                </select>
                                <input type="tel" name="phones[]" class="phone-number flex-1 px-3 py-2 rounded-lg" value="" required
                                       style="border: 2px solid #efecea;"
                                       pattern="[0-9\s()-]+"
                                       title="{{ __('Only digits, spaces, dashes and parentheses allowed') }}"
                                       placeholder="6 12 34 56 78">
                                <button type="button" class="remove-phone text-white px-3 py-2 rounded" style="background: #ba1a1a; display: none;">
                                    −
                                </button>
                            </div>
                        @endforelse
                    </div>
                    @error('phones.*') <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p> @enderror
                </div>

                <!-- Address Fields -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4" style="color: #374151;">
                        <i class="fas fa-map-marker-alt mr-2"></i>{{ __('Addresses') }}
                    </h3>
                    @include('partials.adresse-repeater', [
                        'adresses' => $contact->adresses,
                        'paysList' => $paysList,
                    ])
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('contacts.show', $contact) }}" class="px-6 py-2 rounded-lg font-semibold" style="background: #f5f3f0; color: #374151;">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="px-6 py-2 rounded-lg font-semibold text-white" style="background: linear-gradient(135deg, #843728, #c4816e);">
                        {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phonePrefixOptions = `
        @foreach($paysList as $pays)
        <option value="{{ $pays->code }}">{{ $pays->indicatif }} {{ $pays->code }}</option>
        @endforeach
    `;

    document.getElementById('addEmail').addEventListener('click', function() {
        const container = document.getElementById('emailContainer');
        const newField = document.createElement('div');
        newField.className = 'email-field flex gap-3';
        newField.innerHTML = `
            <input type="email" name="emails[]" required
                   class="flex-1 px-3 py-2 rounded-lg"
                   style="border: 2px solid #efecea;"
                   placeholder="{{ __('email@example.com') }}">
            <button type="button" class="remove-email text-white px-3 py-2 rounded" style="background: #ba1a1a;">−</button>
        `;
        container.appendChild(newField);
        updateRemoveButtons('email');
    });

    document.getElementById('addPhone').addEventListener('click', function() {
        const container = document.getElementById('phoneContainer');
        const newField = document.createElement('div');
        newField.className = 'phone-field flex gap-3 items-start';
        newField.innerHTML = `
            <select name="phone_pays[]" class="phone-prefix form-select" style="width: 160px; flex-shrink: 0;">
                ${phonePrefixOptions}
            </select>
            <input type="tel" name="phones[]" class="phone-number flex-1 px-3 py-2 rounded-lg" required
                   style="border: 2px solid #efecea;"
                   pattern="[0-9\\s()-]+"
                   title="{{ __('Only digits, spaces, dashes and parentheses allowed') }}"
                   placeholder="6 12 34 56 78">
            <button type="button" class="remove-phone text-white px-3 py-2 rounded" style="background: #ba1a1a;">−</button>
        `;
        container.appendChild(newField);
        updateRemoveButtons('phone');
    });

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
        const fields = document.querySelectorAll(`.${type}-field`);
        fields.forEach(field => {
            const removeBtn = field.querySelector(`.remove-${type}`);
            removeBtn.style.display = fields.length > 1 ? 'block' : 'none';
        });
    }

    // Real-time phone number filtering (only on the number input)
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('phone-number')) {
            e.target.value = e.target.value.replace(/[^0-9\s()-]/g, '');
        }
    });
});
</script>
@endsection
