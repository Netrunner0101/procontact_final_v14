@extends('layouts.app')

@section('title', __('New Reminder') . ' - Pro Contact')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('New Reminder') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('Create a reminder for an appointment') }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('rappels.store') }}">
                @csrf

                <!-- Appointment Selection -->
                <div class="mb-6">
                    <label for="rendez_vous_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Appointment') }} *</label>
                    <select id="rendez_vous_id" name="rendez_vous_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('rendez_vous_id') border-red-500 @enderror">
                        <option value="">{{ __('Select an appointment') }}</option>
                        @foreach($userRendezVous as $rdv)
                            <option value="{{ $rdv->id }}"
                                    {{ (old('rendez_vous_id', $rendezVous?->id) == $rdv->id) ? 'selected' : '' }}
                                    data-date="{{ $rdv->date_debut->format('Y-m-d') }}"
                                    data-time="{{ $rdv->heure_debut->format('H:i') }}">
                                {{ $rdv->titre }} - {{ $rdv->contact->prenom }} {{ $rdv->contact->nom }}
                                ({{ $rdv->date_debut->format('d/m/Y') }} {{ __('at') }} {{ $rdv->heure_debut->format('H:i') }})
                            </option>
                        @endforeach
                    </select>
                    @error('rendez_vous_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reminder Date and Time -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Reminder date and time') }} *</label>
                    @php
                        [$oldDate, $oldHour, $oldMin] = ['', '', ''];
                        if (old('date_rappel')) {
                            $parts = preg_split('/[T ]/', old('date_rappel'));
                            $oldDate = $parts[0] ?? '';
                            if (! empty($parts[1])) {
                                [$oldHour, $oldMin] = array_pad(explode(':', $parts[1]), 2, '');
                            }
                        }
                    @endphp
                    <input type="hidden" id="date_rappel" name="date_rappel" value="{{ old('date_rappel') }}" required>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <input type="date" id="rappel_date_part"
                               value="{{ $oldDate }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('date_rappel') border-red-500 @enderror">
                        <select id="rappel_hour_part"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('date_rappel') border-red-500 @enderror">
                            <option value="">{{ __('Hour') }}</option>
                            @for ($h = 0; $h < 24; $h++)
                                @php $hh = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                                <option value="{{ $hh }}" {{ $oldHour === $hh ? 'selected' : '' }}>{{ $hh }} h</option>
                            @endfor
                        </select>
                        <select id="rappel_minute_part"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('date_rappel') border-red-500 @enderror">
                            <option value="">{{ __('Minute') }}</option>
                            @for ($m = 0; $m < 60; $m += 5)
                                @php $mm = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                                <option value="{{ $mm }}" {{ $oldMin === $mm ? 'selected' : '' }}>{{ $mm }}</option>
                            @endfor
                        </select>
                    </div>
                    @error('date_rappel')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-600 mt-1">{{ __('The reminder must be scheduled in the future') }}</p>
                </div>

                <!-- Frequency Selection -->
                <div class="mb-6">
                    <label for="frequence" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Frequency') }} *</label>
                    <select id="frequence" name="frequence" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('frequence') border-red-500 @enderror">
                        <option value="">{{ __('Select a frequency') }}</option>
                        <option value="Une fois" {{ old('frequence') == 'Une fois' ? 'selected' : '' }}>{{ __('Once') }}</option>
                        <option value="Quotidien" {{ old('frequence') == 'Quotidien' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                        <option value="Hebdomadaire" {{ old('frequence') == 'Hebdomadaire' ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                        <option value="Mensuel" {{ old('frequence') == 'Mensuel' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                    </select>
                    @error('frequence')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Recipient Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Send the reminder to') }} *</label>
                    <div class="space-y-2">
                        @foreach (['Utilisateur' => __('Me (the user)'), 'Client' => __('The client'), 'Les deux' => __('Both')] as $value => $label)
                            <label class="flex items-center">
                                <input type="radio" name="destinataire" value="{{ $value }}" required
                                       {{ old('destinataire', 'Les deux') === $value ? 'checked' : '' }}
                                       class="mr-2 text-yellow-600 focus:ring-yellow-500">
                                <span class="text-sm text-gray-900">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('destinataire')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CC Emails -->
                <div class="mb-6">
                    <label for="emails_cc" class="block text-sm font-medium text-gray-700 mb-2">{{ __('CC (optional)') }}</label>
                    <input type="text" id="emails_cc" name="emails_cc"
                           value="{{ old('emails_cc') }}"
                           placeholder="{{ __('email1@example.com, email2@example.com') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('emails_cc') border-red-500 @enderror">
                    <p class="text-sm text-gray-600 mt-1">{{ __('Separate multiple emails with a comma.') }}</p>
                    @error('emails_cc')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quick Reminder Options -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Quick reminders') }}</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <button type="button" class="quick-reminder bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition duration-200" data-minutes="15">
                            {{ __('15 min before') }}
                        </button>
                        <button type="button" class="quick-reminder bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition duration-200" data-minutes="60">
                            {{ __('1h before') }}
                        </button>
                        <button type="button" class="quick-reminder bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition duration-200" data-minutes="1440">
                            {{ __('1 day before') }}
                        </button>
                        <button type="button" class="quick-reminder bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition duration-200" data-minutes="10080">
                            {{ __('1 week before') }}
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">{{ __('Click a button to quickly set the reminder time') }}</p>
                </div>

                <!-- Appointment Preview -->
                <div id="appointmentPreview" class="mb-6 p-4 bg-yellow-50 rounded-lg hidden">
                    <h4 class="text-sm font-medium text-yellow-900 mb-2">{{ __('Appointment preview:') }}</h4>
                    <div id="previewContent" class="text-sm text-yellow-700"></div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ $rendezVous ? route('rendez-vous.show', $rendezVous) : route('rappels.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        {{ __('Create Reminder') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rendezVousSelect = document.getElementById('rendez_vous_id');
    const dateRappelInput = document.getElementById('date_rappel');
    const datePart = document.getElementById('rappel_date_part');
    const hourPart = document.getElementById('rappel_hour_part');
    const minutePart = document.getElementById('rappel_minute_part');
    const frequenceSelect = document.getElementById('frequence');
    const appointmentPreview = document.getElementById('appointmentPreview');
    const previewContent = document.getElementById('previewContent');
    const quickReminderButtons = document.querySelectorAll('.quick-reminder');

    const pad = (n) => String(n).padStart(2, '0');
    const fmtLocalDate = (d) => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());

    // Round minutes down to nearest 5 so it always matches a <select> option.
    const roundToStep = (m) => Math.floor(m / 5) * 5;

    const setPartsFromDate = (d) => {
        datePart.value = fmtLocalDate(d);
        hourPart.value = pad(d.getHours());
        const m = roundToStep(d.getMinutes());
        minutePart.value = pad(m);
        composeHidden();
    };

    const composeHidden = () => {
        if (datePart.value && hourPart.value !== '' && minutePart.value !== '') {
            dateRappelInput.value = datePart.value + 'T' + hourPart.value + ':' + minutePart.value;
        } else {
            dateRappelInput.value = '';
        }
        dateRappelInput.setCustomValidity('');
    };

    [datePart, hourPart, minutePart].forEach(el => el.addEventListener('change', composeHidden));

    // Set minimum date to today (local).
    datePart.setAttribute('min', fmtLocalDate(new Date()));

    const getSelectedAppointmentDateTime = () => {
        const opt = rendezVousSelect.options[rendezVousSelect.selectedIndex];
        if (!opt || !opt.value) return null;
        const d = opt.dataset.date, t = opt.dataset.time;
        if (!d || !t) return null;
        return new Date(d + 'T' + t);
    };

    const syncBoundsToAppointment = () => {
        const appt = getSelectedAppointmentDateTime();
        if (!appt) { datePart.removeAttribute('max'); return; }
        datePart.setAttribute('max', fmtLocalDate(appt));
        if (dateRappelInput.value && new Date(dateRappelInput.value) >= appt) {
            datePart.value = ''; hourPart.value = ''; minutePart.value = '';
            composeHidden();
        }
    };

    rendezVousSelect.addEventListener('change', function() {
        if (this.value) {
            previewContent.textContent = this.options[this.selectedIndex].textContent;
            appointmentPreview.classList.remove('hidden');
            if (!frequenceSelect.value) frequenceSelect.value = 'Une fois';
        } else {
            appointmentPreview.classList.add('hidden');
        }
        syncBoundsToAppointment();
    });

    quickReminderButtons.forEach(button => {
        button.addEventListener('click', function() {
            const appt = getSelectedAppointmentDateTime();
            if (!appt) {
                alert('{{ __('Please select an appointment first.') }}');
                return;
            }
            const minutes = parseInt(this.dataset.minutes, 10);
            const reminder = new Date(appt.getTime() - (minutes * 60000));
            if (reminder <= new Date()) {
                alert('{{ __('The reminder would be in the past. Please select a future appointment or adjust the time manually.') }}');
                return;
            }
            setPartsFromDate(reminder);
            quickReminderButtons.forEach(b => b.classList.remove('ring-2', 'ring-yellow-500', 'bg-yellow-100'));
            this.classList.add('ring-2', 'ring-yellow-500', 'bg-yellow-100');
        });
    });

    dateRappelInput.form.addEventListener('submit', function(e) {
        composeHidden();
        if (!dateRappelInput.value) {
            e.preventDefault();
            datePart.setCustomValidity('{{ __('Reminder date and time') }}');
            datePart.reportValidity();
            return;
        }
        const appt = getSelectedAppointmentDateTime();
        if (appt && new Date(dateRappelInput.value) >= appt) {
            e.preventDefault();
            datePart.setCustomValidity('{{ __('The reminder must be scheduled before the appointment.') }}');
            datePart.reportValidity();
        } else {
            datePart.setCustomValidity('');
        }
    });
    [datePart, hourPart, minutePart].forEach(el => el.addEventListener('input', () => datePart.setCustomValidity('')));

    if (rendezVousSelect.value) {
        rendezVousSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
