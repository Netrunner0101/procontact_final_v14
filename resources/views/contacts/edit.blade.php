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
                    <div id="phoneContainer" class="space-y-3">
                        @foreach($contact->numeroTelephones as $index => $phoneObj)
                            @php
                                $fullPhone = old('phones.' . $index, $phoneObj->numero_telephone);
                                $detectedPrefix = '+33';
                                $phoneNumber = $fullPhone;
                                $prefixes = ['+352','+213','+216','+212','+351','+44','+49','+34','+39','+32','+41','+43','+48','+90','+33','+31','+1','+7'];
                                foreach ($prefixes as $p) {
                                    if (str_starts_with($fullPhone, $p)) {
                                        $detectedPrefix = $p;
                                        $phoneNumber = trim(substr($fullPhone, strlen($p)));
                                        break;
                                    }
                                }
                            @endphp
                            <div class="phone-field flex gap-3 items-start">
                                <select class="phone-prefix form-select" style="width: 140px; flex-shrink: 0;">
                                    <option value="+33" {{ $detectedPrefix === '+33' ? 'selected' : '' }}>+33 FR</option>
                                    <option value="+1" {{ $detectedPrefix === '+1' ? 'selected' : '' }}>+1 US/CA</option>
                                    <option value="+44" {{ $detectedPrefix === '+44' ? 'selected' : '' }}>+44 UK</option>
                                    <option value="+49" {{ $detectedPrefix === '+49' ? 'selected' : '' }}>+49 DE</option>
                                    <option value="+34" {{ $detectedPrefix === '+34' ? 'selected' : '' }}>+34 ES</option>
                                    <option value="+39" {{ $detectedPrefix === '+39' ? 'selected' : '' }}>+39 IT</option>
                                    <option value="+32" {{ $detectedPrefix === '+32' ? 'selected' : '' }}>+32 BE</option>
                                    <option value="+41" {{ $detectedPrefix === '+41' ? 'selected' : '' }}>+41 CH</option>
                                    <option value="+352" {{ $detectedPrefix === '+352' ? 'selected' : '' }}>+352 LU</option>
                                    <option value="+212" {{ $detectedPrefix === '+212' ? 'selected' : '' }}>+212 MA</option>
                                    <option value="+216" {{ $detectedPrefix === '+216' ? 'selected' : '' }}>+216 TN</option>
                                    <option value="+213" {{ $detectedPrefix === '+213' ? 'selected' : '' }}>+213 DZ</option>
                                    <option value="+351" {{ $detectedPrefix === '+351' ? 'selected' : '' }}>+351 PT</option>
                                    <option value="+31" {{ $detectedPrefix === '+31' ? 'selected' : '' }}>+31 NL</option>
                                    <option value="+43" {{ $detectedPrefix === '+43' ? 'selected' : '' }}>+43 AT</option>
                                    <option value="+48" {{ $detectedPrefix === '+48' ? 'selected' : '' }}>+48 PL</option>
                                    <option value="+90" {{ $detectedPrefix === '+90' ? 'selected' : '' }}>+90 TR</option>
                                    <option value="+7" {{ $detectedPrefix === '+7' ? 'selected' : '' }}>+7 RU</option>
                                </select>
                                <input type="tel" class="phone-number flex-1 px-3 py-2 rounded-lg @error('phones.' . $index) border-2 border-red-500 @enderror"
                                       value="{{ $phoneNumber }}" required
                                       style="border: 2px solid #efecea;"
                                       pattern="[0-9\s()-]+"
                                       title="{{ __('Only digits, spaces, dashes and parentheses allowed') }}"
                                       placeholder="6 12 34 56 78">
                                <input type="hidden" name="phones[]" class="phone-combined">
                                <button type="button" class="remove-phone text-white px-3 py-2 rounded" style="background: #ba1a1a; {{ $loop->count <= 1 ? 'display: none;' : '' }}">
                                    −
                                </button>
                            </div>
                        @endforeach
                        @if($contact->numeroTelephones->isEmpty())
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
                                <input type="tel" class="phone-number flex-1 px-3 py-2 rounded-lg" value="" required
                                       style="border: 2px solid #efecea;"
                                       pattern="[0-9\s()-]+"
                                       title="{{ __('Only digits, spaces, dashes and parentheses allowed') }}"
                                       placeholder="6 12 34 56 78">
                                <input type="hidden" name="phones[]" class="phone-combined">
                                <button type="button" class="remove-phone text-white px-3 py-2 rounded" style="background: #ba1a1a; display: none;">
                                    −
                                </button>
                            </div>
                        @endif
                    </div>
                    @error('phones.*') <p class="text-sm mt-1" style="color: #ba1a1a;">{{ $message }}</p> @enderror
                </div>

                <!-- Address Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="rue" class="block text-sm font-semibold mb-2" style="color: #374151;">{{ __('Street') }}</label>
                        <input type="text" id="rue" name="rue" value="{{ old('rue', $contact->rue) }}"
                               class="w-full px-3 py-2 rounded-lg" style="border: 2px solid #efecea;">
                    </div>
                    <div>
                        <label for="numero" class="block text-sm font-semibold mb-2" style="color: #374151;">{{ __('Number') }}</label>
                        <input type="text" id="numero" name="numero" value="{{ old('numero', $contact->numero) }}"
                               class="w-full px-3 py-2 rounded-lg" style="border: 2px solid #efecea;">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="ville" class="block text-sm font-semibold mb-2" style="color: #374151;">{{ __('City') }}</label>
                        <input type="text" id="ville" name="ville" value="{{ old('ville', $contact->ville) }}"
                               class="w-full px-3 py-2 rounded-lg" style="border: 2px solid #efecea;">
                    </div>
                    <div>
                        <label for="code_postal" class="block text-sm font-semibold mb-2" style="color: #374151;">{{ __('Postal Code') }}</label>
                        <input type="text" id="code_postal" name="code_postal" value="{{ old('code_postal', $contact->code_postal) }}"
                               class="w-full px-3 py-2 rounded-lg" style="border: 2px solid #efecea;">
                    </div>
                    <div>
                        <label for="pays" class="block text-sm font-semibold mb-2" style="color: #374151;">{{ __('Country') }}</label>
                        <input type="text" id="pays" name="pays" value="{{ old('pays', $contact->pays ?? 'France') }}"
                               class="w-full px-3 py-2 rounded-lg" style="border: 2px solid #efecea;">
                    </div>
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
    document.getElementById('editContactForm').addEventListener('submit', function() {
        document.querySelectorAll('.phone-field').forEach(function(field) {
            const prefix = field.querySelector('.phone-prefix').value;
            const number = field.querySelector('.phone-number').value.trim();
            field.querySelector('.phone-combined').value = prefix + ' ' + number;
        });
    });

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
            <select class="phone-prefix form-select" style="width: 140px; flex-shrink: 0;">
                ${phonePrefixOptions}
            </select>
            <input type="tel" class="phone-number flex-1 px-3 py-2 rounded-lg" required
                   style="border: 2px solid #efecea;"
                   pattern="[0-9\\s()-]+"
                   title="{{ __('Only digits, spaces, dashes and parentheses allowed') }}"
                   placeholder="6 12 34 56 78">
            <input type="hidden" name="phones[]" class="phone-combined">
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
