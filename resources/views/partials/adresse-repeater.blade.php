@props([
    'adresses' => [],
    'paysList' => collect(),
])

@php
    /** @var array<int, array<string, mixed>> $items */
    $items = old('adresses', collect($adresses)->map(fn ($a) => [
        'rue' => $a->rue ?? '',
        'numero_rue' => $a->numero_rue ?? '',
        'code_postal' => $a->code_postal ?? '',
        'ville' => $a->ville ?? '',
        'pays_code' => $a->pays_code ?? '',
        'is_principale' => (bool) ($a->is_principale ?? false),
    ])->values()->toArray());

    if (empty($items)) {
        $items = [[
            'rue' => '', 'numero_rue' => '', 'code_postal' => '',
            'ville' => '', 'pays_code' => '', 'is_principale' => true,
        ]];
    }
@endphp

<div x-data="{
        adresses: @js($items),
        addAdresse() {
            this.adresses.push({
                rue: '', numero_rue: '', code_postal: '',
                ville: '', pays_code: '', is_principale: this.adresses.length === 0,
            });
        },
        removeAdresse(i) {
            const wasPrincipal = this.adresses[i].is_principale;
            this.adresses.splice(i, 1);
            if (wasPrincipal && this.adresses.length > 0) {
                this.adresses[0].is_principale = true;
            }
        },
        setPrincipale(i) {
            this.adresses.forEach((a, idx) => a.is_principale = idx === i);
        },
     }"
     class="space-y-4">

    <template x-for="(adresse, index) in adresses" :key="index">
        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
            <div class="flex items-center justify-between mb-3">
                <label class="inline-flex items-center text-sm font-medium text-gray-700">
                    <input type="radio" name="adresse_principale_radio"
                           class="mr-2"
                           :checked="adresse.is_principale"
                           @change="setPrincipale(index)">
                    <span x-text="adresse.is_principale ? '{{ __('Primary address') }}' : '{{ __('Set as primary') }}'"></span>
                </label>

                <button type="button"
                        @click="removeAdresse(index)"
                        x-show="adresses.length > 1"
                        class="text-red-600 hover:text-red-800 text-sm">
                    <i class="fas fa-trash mr-1"></i>{{ __('Remove') }}
                </button>
            </div>

            <input type="hidden" :name="`adresses[${index}][is_principale]`" :value="adresse.is_principale ? 1 : 0">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Street') }}</label>
                    <input type="text" :name="`adresses[${index}][rue]`" x-model="adresse.rue"
                           class="form-input w-full" placeholder="{{ __('Enter the street') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Number') }}</label>
                    <input type="text" :name="`adresses[${index}][numero_rue]`" x-model="adresse.numero_rue"
                           class="form-input w-full" placeholder="{{ __('Enter the number') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Postal Code') }}</label>
                    <input type="text" :name="`adresses[${index}][code_postal]`" x-model="adresse.code_postal"
                           class="form-input w-full" placeholder="{{ __('Enter the postal code') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('City') }}</label>
                    <input type="text" :name="`adresses[${index}][ville]`" x-model="adresse.ville"
                           class="form-input w-full" placeholder="{{ __('Enter the city') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Country') }}</label>
                    <select :name="`adresses[${index}][pays_code]`" x-model="adresse.pays_code" class="form-input w-full">
                        <option value="">{{ __('Select a country') }}</option>
                        @foreach($paysList as $pays)
                            <option value="{{ $pays->code }}">{{ $pays->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </template>

    <button type="button"
            @click="addAdresse()"
            class="inline-flex items-center px-4 py-2 border border-dashed border-gray-400 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
        <i class="fas fa-plus mr-2"></i>{{ __('Add an address') }}
    </button>
</div>
