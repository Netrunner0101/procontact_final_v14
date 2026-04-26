<div wire:poll.5s="refreshNotes" class="bg-white rounded-lg shadow p-6 relative">
    <style>
        @keyframes appt-notes-spin { to { transform: rotate(360deg); } }
        @keyframes appt-notes-pulse { 0%, 100% { opacity: 0.4; } 50% { opacity: 1; } }
        @keyframes appt-notes-fadein { from { opacity: 0; transform: translateY(-2px); } to { opacity: 1; transform: none; } }
        .appt-notes-spin { animation: appt-notes-spin 0.8s linear infinite; }
        .appt-notes-loading-bar { position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #843728, transparent); animation: appt-notes-pulse 1.2s ease-in-out infinite; border-radius: 2px 2px 0 0; }
        .appt-note-item { animation: appt-notes-fadein 0.25s ease-out; }
        .appt-toast { position: fixed; bottom: 1.5rem; right: 1.5rem; background: #1b1c1a; color: #fff; padding: 0.7rem 1rem; border-radius: 0.5rem; font-size: 0.88rem; box-shadow: 0 8px 24px rgba(0,0,0,0.2); z-index: 1000; animation: appt-notes-fadein 0.2s ease-out; }
        .appt-spinner-inline { display: inline-block; width: 14px; height: 14px; border: 2px solid #efecea; border-top-color: #843728; border-radius: 50%; animation: appt-notes-spin 0.8s linear infinite; vertical-align: middle; }
    </style>

    <div wire:loading.delay class="appt-notes-loading-bar"></div>

    <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
        <div class="flex items-center gap-2">
            <h2 class="text-xl font-semibold text-gray-900">{{ __('Notes') }}</h2>
            <span class="text-xs text-gray-500" title="{{ __('Updates every 5 seconds') }}">
                <span wire:loading.remove wire:target="refreshNotes">
                    <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#10b981;"></span>
                    {{ __('Live') }}
                </span>
                <span wire:loading wire:target="refreshNotes" class="text-gray-500">
                    <span class="appt-spinner-inline"></span>
                </span>
            </span>
            @if($hasNew)
                <button type="button" wire:click="dismissNew"
                        class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-medium hover:bg-amber-200">
                    ✨ {{ __('New note') }}
                </button>
            @endif
        </div>

        @unless($showForm)
            <button type="button" wire:click="startCreate"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('Add note') }}
            </button>
        @endunless
    </div>

    {{-- Inline form --}}
    @if($showForm)
        <form wire:submit.prevent="save"
              class="mb-4 p-4 bg-purple-50 border border-purple-100 rounded-lg space-y-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Title') }}</label>
                <input type="text" wire:model="titre" maxlength="255"
                       placeholder="{{ __('Optional title') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                @error('titre') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Note') }} *</label>
                <textarea wire:model="commentaire" rows="3" required
                          placeholder="{{ __('Write your note...') }}"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                @error('commentaire') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" wire:model="is_shared_with_client" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                {{ __('Visible to the client in their portal') }}
            </label>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" wire:click="cancel"
                        class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-50">
                    {{ __('Cancel') }}
                </button>
                <button type="submit"
                        wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 disabled:opacity-60">
                    <span wire:loading wire:target="save" class="appt-spinner-inline" style="border-top-color:#fff;"></span>
                    {{ $editingNoteId ? __('Update') : __('Save') }}
                </button>
            </div>
        </form>
    @endif

    {{-- List --}}
    @if($notes->isEmpty())
        <div class="text-center py-8 text-gray-500 text-sm">
            <svg class="mx-auto h-10 w-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p>{{ __('No notes yet for this appointment.') }}</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($notes as $note)
                @php
                    $isYou = $note->user_id === $currentUserId && !$note->contact_id;
                    $isClient = $note->contact_id !== null;
                    $badgeClass = $isClient
                        ? 'bg-amber-100 text-amber-800'
                        : ($isYou ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700');
                    $badgeLabel = $isClient ? __('Client') : ($isYou ? __('You') : __('Provider'));
                @endphp
                <div class="appt-note-item border-l-4 {{ $isClient ? 'border-amber-400' : 'border-purple-500' }} pl-4 py-2 group"
                     wire:key="note-{{ $note->id }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-medium text-gray-900 truncate">{{ $note->titre }}</h3>
                                <span class="text-[10px] uppercase tracking-wider px-1.5 py-0.5 rounded {{ $badgeClass }} font-semibold">
                                    {{ $badgeLabel }}
                                </span>
                                @if($note->is_shared_with_client)
                                    <span class="text-[10px] uppercase tracking-wider text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded font-semibold" title="{{ __('Visible to client') }}">
                                        🔓 {{ __('Shared') }}
                                    </span>
                                @else
                                    <span class="text-[10px] uppercase tracking-wider text-gray-500 bg-gray-50 px-1.5 py-0.5 rounded font-semibold" title="{{ __('Private to you') }}">
                                        🔒 {{ __('Private') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-600 mt-1 whitespace-pre-wrap text-sm">{{ $note->commentaire }}</p>
                            <p class="text-xs text-gray-500 mt-2">
                                {{ ($note->date_create ?? $note->created_at)->format('d/m/Y · H:i') }}
                                @if($isClient && $note->contact)
                                    · {{ $note->contact->prenom }} {{ $note->contact->nom }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button" wire:click="toggleShared({{ $note->id }})"
                                    title="{{ $note->is_shared_with_client ? __('Hide from client') : __('Share with client') }}"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded">
                                @if($note->is_shared_with_client)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                @endif
                            </button>
                            <button type="button" wire:click="startEdit({{ $note->id }})" title="{{ __('Edit') }}"
                                    class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button type="button" wire:click="delete({{ $note->id }})" title="{{ __('Delete') }}"
                                    wire:confirm="{{ __('Delete this note?') }}"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Toast --}}
    <div x-data="{ show: false, msg: '' }"
         x-on:toast.window="msg = $event.detail.message; show = true; clearTimeout(window.__apptNotesToast); window.__apptNotesToast = setTimeout(() => show = false, 2200)"
         x-show="show" x-transition.opacity x-cloak
         class="appt-toast">
        <span x-text="msg"></span>
    </div>
</div>
