@extends('portal.layout', ['activeTab' => 'appointments', 'pageTitle' => $appointment->titre])

@section('content')
    <a href="{{ route('portal.index', ['token' => $token]) }}" class="back-link">
        <i class="fas fa-arrow-left"></i> {{ __('Back to appointments') }}
    </a>

    {{-- Appointment Details --}}
    <div class="card">
        <h1 style="font-family:'Manrope',sans-serif;font-size:1.5rem;font-weight:700;margin-bottom:1.25rem;">
            {{ $appointment->titre }}
        </h1>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;">
            <div>
                <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#75786c;margin-bottom:0.2rem;">{{ __('Start date') }}</div>
                <div>{{ $appointment->date_debut->format('d/m/Y') }}</div>
            </div>
            <div>
                <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#75786c;margin-bottom:0.2rem;">{{ __('End date') }}</div>
                <div>{{ $appointment->date_fin->format('d/m/Y') }}</div>
            </div>
            <div>
                <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#75786c;margin-bottom:0.2rem;">{{ __('Start time') }}</div>
                <div>{{ $appointment->heure_debut->format('H:i') }}</div>
            </div>
            <div>
                <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#75786c;margin-bottom:0.2rem;">{{ __('End time') }}</div>
                <div>{{ $appointment->heure_fin->format('H:i') }}</div>
            </div>
            <div>
                <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#75786c;margin-bottom:0.2rem;">{{ __('Activity') }}</div>
                <div>{{ $appointment->activite->nom ?? '—' }}</div>
            </div>
        </div>
        @if($appointment->description)
            <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid #efecea;">
                <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#75786c;margin-bottom:0.4rem;">{{ __('Description') }}</div>
                <p style="line-height:1.6;">{{ $appointment->description }}</p>
            </div>
        @endif
    </div>

    {{-- Notes --}}
    <div class="card">
        <div class="section-head">
            <div class="section-title">
                <i class="fas fa-sticky-note"></i> {{ __('Shared notes') }}
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="openNoteModal()">
                <i class="fas fa-plus"></i> {{ __('Add note') }}
            </button>
        </div>

        @forelse($sharedNotes as $note)
            @php $isClient = $note->contact_id === $contact->id; @endphp
            <div class="note-item"
                 data-note-id="{{ $note->id }}"
                 data-note-titre="{{ $note->titre }}"
                 data-note-commentaire="{{ $note->commentaire }}">
                <div class="note-item-head">
                    <h4>{{ $note->titre }}</h4>
                    @if($isClient)
                        <div class="note-actions">
                            <button type="button" class="btn-icon edit-note-btn" title="{{ __('Edit') }}">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form method="POST"
                                  action="{{ route('portal.deleteNote', ['token' => $token, 'noteId' => $note->id]) }}"
                                  style="display:inline;"
                                  onsubmit="return confirm('{{ __('Delete this note?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon danger" title="{{ __('Delete') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
                <p>{{ $note->commentaire }}</p>
                <div class="note-meta">
                    <span class="note-author-badge {{ $isClient ? 'you' : 'pro' }}">
                        <i class="fas fa-{{ $isClient ? 'user' : 'briefcase' }}"></i>
                        {{ $isClient ? __('You') : __('Provider') }}
                    </span>
                    <span>{{ ($note->date_create ?? $note->created_at)->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-sticky-note"></i>
                <p>{{ __('No shared notes for this appointment.') }}</p>
            </div>
        @endforelse
    </div>

    {{-- Note modal --}}
    <div class="modal-backdrop" id="noteModal" onclick="if(event.target===this)closeNoteModal()">
        <div class="modal">
            <div class="modal-head">
                <div class="modal-title" id="noteModalTitle">{{ __('Add note') }}</div>
                <button type="button" class="modal-close" onclick="closeNoteModal()">&times;</button>
            </div>

            @if($templates->isNotEmpty())
                <div class="form-row">
                    <label>{{ __('Insert from template') }}</label>
                    <select class="form-control" id="templatePicker" onchange="applyTemplate(this.value)">
                        <option value="">— {{ __('Choose a template') }} —</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->id }}"
                                    data-titre="{{ $tpl->titre }}"
                                    data-commentaire="{{ $tpl->commentaire }}">
                                {{ $tpl->titre }}{{ $tpl->contact_id ? ' (' . __('Personal') . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <form method="POST" id="noteForm"
                  action="{{ route('portal.storeNote', ['token' => $token, 'appointmentId' => $appointment->id]) }}">
                @csrf
                <input type="hidden" name="_method" id="noteMethod" value="POST">

                <div class="form-row">
                    <label for="noteTitre">{{ __('Title') }}</label>
                    <input type="text" name="titre" id="noteTitre" class="form-control"
                           placeholder="{{ __('Optional title') }}" value="{{ old('titre') }}">
                </div>

                <div class="form-row">
                    <label for="noteCommentaire">{{ __('Note') }} *</label>
                    <textarea name="commentaire" id="noteCommentaire"
                              class="form-control {{ $errors->has('commentaire') ? 'error' : '' }}"
                              placeholder="{{ __('Write your note...') }}" required>{{ old('commentaire') }}</textarea>
                    @error('commentaire')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeNoteModal()">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const storeUrl = @json(route('portal.storeNote', ['token' => $token, 'appointmentId' => $appointment->id]));
            const updateUrlBase = @json(url('portal/' . $token . '/note'));

            function openNoteModal(note = null) {
                const modal = document.getElementById('noteModal');
                const form = document.getElementById('noteForm');
                const methodInput = document.getElementById('noteMethod');
                const titleEl = document.getElementById('noteModalTitle');
                const titreInput = document.getElementById('noteTitre');
                const commentaireInput = document.getElementById('noteCommentaire');
                const picker = document.getElementById('templatePicker');

                if (note && note.id) {
                    form.action = updateUrlBase + '/' + note.id;
                    methodInput.value = 'PATCH';
                    titleEl.textContent = @json(__('Edit note'));
                    titreInput.value = note.titre || '';
                    commentaireInput.value = note.commentaire || '';
                } else {
                    form.action = storeUrl;
                    methodInput.value = 'POST';
                    titleEl.textContent = @json(__('Add note'));
                    titreInput.value = '';
                    commentaireInput.value = '';
                }
                if (picker) picker.value = '';
                modal.classList.add('open');
                setTimeout(() => commentaireInput.focus(), 50);
            }

            function closeNoteModal() {
                document.getElementById('noteModal').classList.remove('open');
            }

            function applyTemplate(id) {
                if (!id) return;
                const opt = document.querySelector('#templatePicker option[value="' + id + '"]');
                if (!opt) return;
                const titre = opt.getAttribute('data-titre') || '';
                const body = opt.getAttribute('data-commentaire') || '';
                const titreInput = document.getElementById('noteTitre');
                const commentaireInput = document.getElementById('noteCommentaire');
                if (!titreInput.value) titreInput.value = titre;
                commentaireInput.value = commentaireInput.value
                    ? commentaireInput.value + '\n\n' + body
                    : body;
            }

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.edit-note-btn');
                if (!btn) return;
                const item = btn.closest('[data-note-id]');
                if (!item) return;
                openNoteModal({
                    id: item.dataset.noteId,
                    titre: item.dataset.noteTitre,
                    commentaire: item.dataset.noteCommentaire,
                });
            });

            @if($errors->any())
                document.addEventListener('DOMContentLoaded', () => openNoteModal());
            @endif
        </script>
    @endpush
@endsection
