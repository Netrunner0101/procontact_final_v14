@php
    $isPast = $isPast ?? false;
    $statusColors = [
        'scheduled' => ['bg' => '#fbf1d8', 'fg' => '#8a6e2e'],
        'confirmed' => ['bg' => '#e3f3e3', 'fg' => '#3a6a3a'],
        'completed' => ['bg' => '#ece7f7', 'fg' => '#5d4ba1'],
        'cancelled' => ['bg' => '#fee4e2', 'fg' => '#b54848'],
        'no_show'   => ['bg' => '#f5f3f0', 'fg' => '#75786c'],
    ];
    $statusColor = $statusColors[$rdv->statut] ?? ['bg' => '#f5f3f0', 'fg' => '#75786c'];
    $statusLabel = \App\Models\RendezVous::STATUTS[$rdv->statut] ?? $rdv->statut;
@endphp

<li>
    <a href="{{ route('rendez-vous.show', $rdv->id) }}"
       class="flex items-center gap-3 p-3 rounded-lg transition-colors hover:shadow-sm"
       style="background: {{ $isPast ? '#fbf9f5' : 'white' }}; border: 1px solid rgba(27,28,26,0.06);">
        <!-- Date pill -->
        <div class="flex-shrink-0 w-12 h-14 rounded-lg flex flex-col items-center justify-center text-white"
             style="background: {{ $isPast ? 'linear-gradient(135deg, #75786c, #44483e)' : 'linear-gradient(135deg, #843728, #c4816e)' }};">
            <span class="text-[10px] font-semibold uppercase leading-none">{{ $rdv->date_debut?->locale(app()->getLocale())->isoFormat('MMM') }}</span>
            <span class="text-base font-bold leading-none mt-1">{{ $rdv->date_debut?->format('d') }}</span>
            <span class="text-[9px] leading-none mt-0.5 opacity-80">{{ $rdv->date_debut?->format('Y') }}</span>
        </div>

        <!-- Main content -->
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 flex-wrap">
                <p class="text-sm font-semibold truncate" style="color: #1b1c1a;">{{ $rdv->titre }}</p>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide"
                      style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['fg'] }};">
                    {{ __($statusLabel) }}
                </span>
            </div>
            <div class="mt-1 text-xs flex items-center gap-3 flex-wrap" style="color: #75786c;">
                @if($rdv->heure_debut)
                    <span><i class="far fa-clock mr-1"></i>{{ $rdv->heure_debut->format('H:i') }}@if($rdv->heure_fin)–{{ $rdv->heure_fin->format('H:i') }}@endif</span>
                @endif
                @if($rdv->contact)
                    <span><i class="far fa-user mr-1"></i>{{ $rdv->contact->prenom }} {{ $rdv->contact->nom }}</span>
                @endif
            </div>
        </div>

        <i class="fas fa-chevron-right text-xs flex-shrink-0" style="color: #c5c8b9;"></i>
    </a>
</li>
