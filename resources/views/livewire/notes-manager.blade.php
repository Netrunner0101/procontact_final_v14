<div class="notes-manager">
    <!-- Header -->
    <div class="manager-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-sticky-note"></i>
                {{ __('Notes Management') }}
            </h1>
            <p class="page-subtitle">{{ __('Organize and manage all your important notes') }}</p>
        </div>
        <div class="header-actions">
            <button wire:click="openCreateModal" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('New Note') }}
            </button>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="filters-section">
        <div class="search-box">
            <div class="search-input-group">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by title, content or contact...') }}"
                    class="search-input"
                >
            </div>
        </div>

        <div class="filter-controls">
            <select wire:model.live="sharingFilter" class="filter-select">
                <option value="">{{ __('All notes') }}</option>
                <option value="1">{{ __('Shared with client') }}</option>
                <option value="0">{{ __('Not shared') }}</option>
            </select>

            <select wire:model.live="appointmentFilter" class="filter-select">
                <option value="">{{ __('All appointments') }}</option>
                @foreach($appointments as $appointment)
                    <option value="{{ $appointment->id }}">
                        {{ $appointment->contact->nom }} {{ $appointment->contact->prenom }} - {{ $appointment->date_debut->format('d/m/Y') }}
                    </option>
                @endforeach
            </select>

            <div class="sort-controls">
                <button wire:click="sortBy('created_at')" class="sort-btn {{ $sortBy === 'created_at' ? 'active' : '' }}">
                    {{ __('Date') }}
                    @if($sortBy === 'created_at')
                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                    @endif
                </button>
                <button wire:click="sortBy('titre')" class="sort-btn {{ $sortBy === 'titre' ? 'active' : '' }}">
                    {{ __('Title') }}
                    @if($sortBy === 'titre')
                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Notes Grid -->
    <div class="notes-grid">
        @forelse($notes as $note)
            <div class="note-card" wire:key="note-{{ $note->id }}">
                <div class="note-header">
                    <div class="note-sharing">
                        @if($note->is_shared_with_client)
                            <span class="sharing-badge shared">
                                <i class="fas fa-share-alt"></i> {{ __('Shared') }}
                            </span>
                        @else
                            <span class="sharing-badge private">
                                <i class="fas fa-lock"></i> {{ __('Private') }}
                            </span>
                        @endif
                    </div>
                    <div class="note-actions">
                        <button wire:click="openEditModal({{ $note->id }})" class="action-btn edit-btn" title="{{ __('Edit') }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="openDeleteModal({{ $note->id }})" class="action-btn delete-btn" title="{{ __('Delete') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="note-content">
                    <h3 class="note-title">{{ $note->titre }}</h3>

                    <div class="note-appointment">
                        <i class="fas fa-calendar"></i>
                        <span>{{ $note->rendezVous->contact->nom }} {{ $note->rendezVous->contact->prenom }}</span>
                        <span class="appointment-date">{{ $note->rendezVous->date_debut->format('d/m/Y') }}</span>
                    </div>

                    <div class="note-activity">
                        <i class="fas fa-briefcase"></i>
                        <span>{{ $note->rendezVous->activite->nom }}</span>
                    </div>

                    <div class="note-text">
                        {{ Str::limit($note->commentaire, 150) }}
                    </div>

                    <div class="note-footer">
                        <span class="note-date">
                            <i class="fas fa-clock"></i>
                            {{ $note->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-sticky-note"></i>
                <h3>{{ __('No notes found') }}</h3>
                <p>{{ __('Start by creating your first note') }}</p>
                <button wire:click="openCreateModal" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('Create a note') }}
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notes->hasPages())
        <div class="pagination-wrapper">
            {{ $notes->links() }}
        </div>
    @endif

    <!-- Create/Edit Modal -->
    @if($showCreateModal || $showEditModal)
        <div class="modal-overlay" wire:click="closeModals">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2 class="modal-title">
                        <i class="fas fa-{{ $showCreateModal ? 'plus' : 'edit' }}"></i>
                        {{ $showCreateModal ? __('New Note') : __('Edit Note') }}
                    </h2>
                    <button wire:click="closeModals" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="{{ $showCreateModal ? 'createNote' : 'updateNote' }}" class="modal-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="rendez_vous_id" class="form-label">{{ __('Appointment') }} *</label>
                            <select id="rendez_vous_id" wire:model="rendez_vous_id" class="form-select" required>
                                <option value="">{{ __('Select an appointment') }}</option>
                                @foreach($appointments as $appointment)
                                    <option value="{{ $appointment->id }}">
                                        {{ $appointment->contact->nom }} {{ $appointment->contact->prenom }} -
                                        {{ $appointment->date_debut->format('d/m/Y') }} -
                                        {{ $appointment->activite->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rendez_vous_id') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('Share with client') }}</label>
                            <div class="sharing-toggle">
                                <label class="toggle-label">
                                    <input type="checkbox" wire:model="is_shared_with_client" class="toggle-input">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-text">{{ $is_shared_with_client ? __('Shared') : __('Private') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="titre" class="form-label">{{ __('Title') }} *</label>
                            <input type="text" id="titre" wire:model="titre" class="form-input" required>
                            @error('titre') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group full-width">
                            <label for="commentaire" class="form-label">{{ __('Comment') }} *</label>
                            <textarea id="commentaire" wire:model="commentaire" class="form-textarea" rows="6" required></textarea>
                            @error('commentaire') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" wire:click="closeModals" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            {{ $showCreateModal ? __('Create') : __('Update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal && $selectedNote)
        <div class="modal-overlay" wire:click="closeModals">
            <div class="modal-content small" wire:click.stop>
                <div class="modal-header">
                    <h2 class="modal-title">
                        <i class="fas fa-trash text-red-500"></i>
                        {{ __('Delete Note') }}
                    </h2>
                    <button wire:click="closeModals" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <p>{{ __('Are you sure you want to delete the note :title?', ['title' => $selectedNote->titre]) }}</p>
                    <p class="text-sm text-gray-600">{{ __('This action is irreversible.') }}</p>
                </div>

                <div class="modal-actions">
                    <button wire:click="closeModals" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="deleteNote" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
    .notes-manager {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
}

.manager-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 2rem;
    background: linear-gradient(135deg, #2d2e2a 0%, #44483e 100%);
    border-radius: 1rem;
    color: white;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-subtitle {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
}

.filters-section {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.search-box {
    flex: 1;
    min-width: 300px;
}

.search-input-group {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input-group i {
    position: absolute;
    left: 1rem;
    color: #6b7280;
    z-index: 1;
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: none;
    border-radius: 0.5rem;
    font-size: 1rem;
}

.filter-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.filter-select {
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 0.5rem;
    background: white;
    min-width: 150px;
}

.sort-controls {
    display: flex;
    gap: 0.5rem;
}

.sort-btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.5rem;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
}

.sort-btn:hover, .sort-btn.active {
    background: #8a6e2e;
    color: white;
    border-color: #8a6e2e;
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-success {
    background: #c0f0b8;
    color: #065f46;
    border: none;
}

.notes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.note-card {
    background: white;
    border-radius: 1rem;
    border: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.note-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

.note-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: #fbf9f6;
    border-bottom: none;
}

.sharing-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}

.sharing-badge.shared { background: #c0f0b8; color: #065f46; }
.sharing-badge.private { background: #f5f3f0; color: #44483e; }

.sharing-toggle {
    display: flex;
    align-items: center;
    padding-top: 0.5rem;
}

.toggle-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
}

.toggle-input {
    width: 1.25rem;
    height: 1.25rem;
    accent-color: #3a6a3a;
}

.toggle-text {
    font-size: 0.9rem;
    color: #374151;
}

.note-actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.8rem;
}

.edit-btn {
    background: #ffdfa0;
    color: #6d5624;
}

.edit-btn:hover {
    background: #d4a844;
    color: white;
}

.delete-btn {
    background: #fee2e2;
    color: #dc2626;
}

.delete-btn:hover {
    background: #ef4444;
    color: white;
}

.note-content {
    padding: 1.5rem;
}

.note-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 1rem 0;
    color: #1f2937;
}

.note-appointment, .note-activity {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0.5rem 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.appointment-date {
    margin-left: auto;
    font-weight: 500;
    color: #843728;
}

.note-text {
    margin: 1rem 0;
    color: #374151;
    line-height: 1.6;
}

.note-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: none;
}

.note-date {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.8rem;
    color: #9ca3af;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #d1d5db;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin: 1rem 0 0.5rem 0;
    color: #374151;
}

/* Modal styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
}

.modal-content {
    background: white;
    border-radius: 1rem;
    border: none;
    max-width: 800px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-content.small {
    max-width: 400px;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.25rem;
    cursor: pointer;
    color: #6b7280;
    padding: 0.5rem;
    border-radius: 0.25rem;
}

.modal-close:hover {
    background: #f3f4f6;
}

.modal-form {
    padding: 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #374151;
}

.form-input, .form-select, .form-textarea {
    padding: 0.75rem;
    border: none;
    border-radius: 0.5rem;
    font-size: 1rem;
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: #8a6e2e;
    box-shadow: 0 0 0 3px rgba(138, 110, 46, 0.1);
}

.error-message {
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.modal-actions {
    padding: 1.5rem;
    border-top: none;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    border: none;
}

.btn-primary {
    background: #8a6e2e;
    color: white;
}

.btn-primary:hover {
    background: #6d5624;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: none;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
}

@media (max-width: 768px) {
    .manager-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .filters-section {
        flex-direction: column;
    }

    .filter-controls {
        flex-wrap: wrap;
    }

    .search-box {
        min-width: 0;
        width: 100%;
    }

    .filter-select {
        min-width: 0;
        width: 100%;
    }

    .notes-grid {
        grid-template-columns: 1fr;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }
}
    </style>
</div>
