@extends('portal.layout', ['activeTab' => 'templates', 'pageTitle' => __('Note templates')])

@section('content')
    <div class="welcome">
        <h1>{{ __('Note templates') }}</h1>
        <p>{{ __('Save reusable note formats and insert them into your appointment notes.') }}</p>
    </div>

    <div class="card">
        <div class="section-head">
            <div class="section-title">
                <i class="fas fa-file-alt"></i> {{ __('Your templates') }}
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="openTemplateModal()">
                <i class="fas fa-plus"></i> {{ __('New template') }}
            </button>
        </div>

        @forelse($templates as $template)
            <div class="note-item"
                 data-tpl-id="{{ $template->id }}"
                 data-tpl-titre="{{ $template->titre }}"
                 data-tpl-commentaire="{{ $template->commentaire }}">
                <div class="note-item-head">
                    <h4>{{ $template->titre }}</h4>
                    <div class="note-actions">
                        <button type="button" class="btn-icon edit-tpl-btn" title="{{ __('Edit') }}">
                            <i class="fas fa-pen"></i>
                        </button>
                        <form method="POST"
                              action="{{ route('portal.deleteTemplate', ['token' => $token, 'templateId' => $template->id]) }}"
                              style="display:inline;"
                              onsubmit="return confirm('{{ __('Delete this template?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="{{ __('Delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <p>{{ $template->commentaire }}</p>
                <div class="note-meta">
                    <span>{{ $template->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <p>{{ __('No templates yet. Create one to get started.') }}</p>
            </div>
        @endforelse
    </div>

    {{-- Template modal --}}
    <div class="modal-backdrop" id="templateModal" onclick="if(event.target===this)closeTemplateModal()">
        <div class="modal">
            <div class="modal-head">
                <div class="modal-title" id="templateModalTitle">{{ __('New template') }}</div>
                <button type="button" class="modal-close" onclick="closeTemplateModal()">&times;</button>
            </div>

            <form method="POST" id="templateForm"
                  action="{{ route('portal.storeTemplate', ['token' => $token]) }}">
                @csrf
                <input type="hidden" name="_method" id="templateMethod" value="POST">

                <div class="form-row">
                    <label for="templateTitre">{{ __('Title') }} *</label>
                    <input type="text" name="titre" id="templateTitre"
                           class="form-control {{ $errors->has('titre') ? 'error' : '' }}"
                           value="{{ old('titre') }}" required>
                    @error('titre')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-row">
                    <label for="templateCommentaire">{{ __('Content') }} *</label>
                    <textarea name="commentaire" id="templateCommentaire" rows="6"
                              class="form-control {{ $errors->has('commentaire') ? 'error' : '' }}"
                              required>{{ old('commentaire') }}</textarea>
                    @error('commentaire')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeTemplateModal()">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const storeTplUrl = @json(route('portal.storeTemplate', ['token' => $token]));
            const updateTplUrlBase = @json(url('portal/' . $token . '/templates'));

            function openTemplateModal(tpl = null) {
                const modal = document.getElementById('templateModal');
                const form = document.getElementById('templateForm');
                const method = document.getElementById('templateMethod');
                const title = document.getElementById('templateModalTitle');
                const titreInput = document.getElementById('templateTitre');
                const commentaireInput = document.getElementById('templateCommentaire');

                if (tpl && tpl.id) {
                    form.action = updateTplUrlBase + '/' + tpl.id;
                    method.value = 'PATCH';
                    title.textContent = @json(__('Edit template'));
                    titreInput.value = tpl.titre || '';
                    commentaireInput.value = tpl.commentaire || '';
                } else {
                    form.action = storeTplUrl;
                    method.value = 'POST';
                    title.textContent = @json(__('New template'));
                    titreInput.value = '';
                    commentaireInput.value = '';
                }
                modal.classList.add('open');
                setTimeout(() => titreInput.focus(), 50);
            }

            function closeTemplateModal() {
                document.getElementById('templateModal').classList.remove('open');
            }

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.edit-tpl-btn');
                if (!btn) return;
                const item = btn.closest('[data-tpl-id]');
                if (!item) return;
                openTemplateModal({
                    id: item.dataset.tplId,
                    titre: item.dataset.tplTitre,
                    commentaire: item.dataset.tplCommentaire,
                });
            });

            @if($errors->any())
                document.addEventListener('DOMContentLoaded', () => openTemplateModal());
            @endif
        </script>
    @endpush
@endsection
