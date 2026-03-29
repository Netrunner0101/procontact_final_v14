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
            <form method="POST" action="{{ route('contacts.update', $contact) }}">
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
                    <div id="emailContainer">
                        @foreach($contact->emails as $index => $emailObj)
                            <div class="email-field flex gap-2 mb-2">
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
                            <div class="email-field flex gap-2 mb-2">
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

                <!-- Dynamic Phone Fields -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-semibold" style="color: #374151;">{{ __('Phone numbers') }} *</label>
                        <button type="button" id="addPhone" class="text-white px-3 py-1 rounded text-sm" style="background: #3a6a3a;">
                            {{ __('+ Add Phone') }}
                        </button>
                    </div>
                    <div id="phoneContainer">
                        @foreach($contact->numeroTelephones as $index => $phoneObj)
                            <div class="phone-field flex gap-2 mb-2">
                                <input type="tel" name="phones[]" value="{{ old('phones.' . $index, $phoneObj->numero_telephone) }}" required
                                       class="flex-1 px-3 py-2 rounded-lg @error('phones.' . $index) border-2 border-red-500 @enderror"
                                       style="border: 2px solid #efecea;"
                                       placeholder="+33 1 23 45 67 89">
                                <button type="button" class="remove-phone text-white px-3 py-2 rounded" style="background: #ba1a1a; {{ $loop->count <= 1 ? 'display: none;' : '' }}">
                                    −
                                </button>
                            </div>
                        @endforeach
                        @if($contact->numeroTelephones->isEmpty())
                            <div class="phone-field flex gap-2 mb-2">
                                <input type="tel" name="phones[]" value="" required
                                       class="flex-1 px-3 py-2 rounded-lg"
                                       style="border: 2px solid #efecea;"
                                       placeholder="+33 1 23 45 67 89">
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

                <!-- Status -->
                <div class="mb-6">
                    <label for="status_id" class="block text-sm font-semibold mb-2" style="color: #374151;">{{ __('CRM Status') }}</label>
                    <select name="status_id" id="status_id" class="w-full px-3 py-2 rounded-lg" style="border: 2px solid #efecea;">
                        <option value="">-- {{ __('Select') }} --</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ old('status_id', $contact->status_id) == $status->id ? 'selected' : '' }}>{{ $status->status_client }}</option>
                        @endforeach
                    </select>
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
    document.getElementById('addEmail').addEventListener('click', function() {
        const container = document.getElementById('emailContainer');
        const newField = document.createElement('div');
        newField.className = 'email-field flex gap-2 mb-2';
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
        newField.className = 'phone-field flex gap-2 mb-2';
        newField.innerHTML = `
            <input type="tel" name="phones[]" required
                   class="flex-1 px-3 py-2 rounded-lg"
                   style="border: 2px solid #efecea;"
                   placeholder="+33 1 23 45 67 89">
            <button type="button" class="remove-phone text-white px-3 py-2 rounded" style="background: #ba1a1a;">−</button>
        `;
        container.appendChild(newField);
        updateRemoveButtons('phone');
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-email')) {
            e.target.parentElement.remove();
            updateRemoveButtons('email');
        }
        if (e.target.classList.contains('remove-phone')) {
            e.target.parentElement.remove();
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
});
</script>
@endsection
