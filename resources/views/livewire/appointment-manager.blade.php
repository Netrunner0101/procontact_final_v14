<div class="appointment-manager">
    <!-- Header -->
    <div class="manager-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-calendar-alt"></i>
                Gestion des Rendez-vous
            </h1>
            <p class="page-subtitle">Planifiez et gérez tous vos rendez-vous</p>
        </div>
        <div class="header-actions">
            <div class="view-toggle">
                <button wire:click="setViewMode('list')" class="view-btn {{ $viewMode === 'list' ? 'active' : '' }}">
                    <i class="fas fa-list"></i> Liste
                </button>
                <button wire:click="setViewMode('calendar')" class="view-btn {{ $viewMode === 'calendar' ? 'active' : '' }}">
                    <i class="fas fa-calendar"></i> Calendrier
                </button>
            </div>
            <button wire:click="openCreateModal" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau RDV
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
                    wire:model.live="search"
                    placeholder="Rechercher par nom de contact..."
                    class="search-input"
                >
            </div>
        </div>

        <div class="filter-controls">
            <select wire:model.live="statusFilter" class="filter-select">
                <option value="">Tous les statuts</option>
                <option value="Programmé">Programmé</option>
                <option value="Confirmé">Confirmé</option>
                <option value="En cours">En cours</option>
                <option value="Terminé">Terminé</option>
                <option value="Annulé">Annulé</option>
                <option value="Reporté">Reporté</option>
            </select>

            <select wire:model.live="contactFilter" class="filter-select">
                <option value="">Tous les contacts</option>
                @foreach($contacts as $contact)
                    <option value="{{ $contact->id }}">{{ $contact->nom }} {{ $contact->prenom }}</option>
                @endforeach
            </select>

            <select wire:model.live="activiteFilter" class="filter-select">
                <option value="">Toutes les activités</option>
                @foreach($activites as $activite)
                    <option value="{{ $activite->id }}">{{ $activite->nom }}</option>
                @endforeach
            </select>

            <select wire:model.live="dateFilter" class="filter-select">
                <option value="">Toutes les dates</option>
                <option value="today">Aujourd'hui</option>
                <option value="tomorrow">Demain</option>
                <option value="this_week">Cette semaine</option>
                <option value="next_week">Semaine prochaine</option>
                <option value="this_month">Ce mois</option>
            </select>
        </div>
    </div>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Appointments List View -->
    @if($viewMode === 'list')
        <div class="appointments-grid">
            @forelse($appointments as $appointment)
                <div class="appointment-card" wire:key="appointment-{{ $appointment->id }}">
                    <div class="appointment-header">
                        <div class="appointment-date-time">
                            <div class="date-display">
                                <div class="date-day">{{ $appointment->date_debut?->format('d') ?? '-' }}</div>
                                <div class="date-month">{{ $appointment->date_debut?->format('M') ?? '-' }}</div>
                                <div class="date-year">{{ $appointment->date_debut?->format('Y') ?? '-' }}</div>
                            </div>
                            <div class="time-display">
                                <i class="fas fa-clock"></i>
                                {{ $appointment->heure_debut ?? '-' }} - {{ $appointment->heure_fin ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="appointment-content">
                        <div class="appointment-info">
                            <h3 class="appointment-title">
                                <i class="fas fa-calendar-check"></i>
                                {{ $appointment->titre }}
                            </h3>

                            <p class="contact-name">
                                <i class="fas fa-user"></i>
                                {{ $appointment->contact->nom ?? 'N/A' }} {{ $appointment->contact->prenom ?? '' }}
                            </p>

                            <p class="activity-name">
                                <i class="fas fa-briefcase"></i>
                                {{ $appointment->activite->nom ?? 'N/A' }}
                            </p>

                            @if($appointment->description)
                                <p class="appointment-notes">
                                    <i class="fas fa-sticky-note"></i>
                                    {{ Str::limit($appointment->description, 100) }}
                                </p>
                            @endif
                        </div>

                        <div class="appointment-actions">
                            <button wire:click="openEditModal({{ $appointment->id }})" class="action-btn edit-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="{{ route('rendez-vous.show', $appointment) }}" class="action-btn view-btn" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button wire:click="openDeleteModal({{ $appointment->id }})" class="action-btn delete-btn" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Aucun rendez-vous trouvé</h3>
                    <p>Commencez par planifier votre premier rendez-vous</p>
                    <button wire:click="openCreateModal" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Créer un rendez-vous
                    </button>
                </div>
            @endforelse
        </div>
    @endif

    <!-- Calendar View (Placeholder) -->
    @if($viewMode === 'calendar')
        <div class="calendar-view">
            <div class="calendar-placeholder">
                <i class="fas fa-calendar-alt"></i>
                <h3>Vue Calendrier</h3>
                <p>La vue calendrier sera implémentée prochainement</p>
                <button wire:click="setViewMode('list')" class="btn btn-primary">
                    <i class="fas fa-list"></i> Retour à la liste
                </button>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    @if($appointments->hasPages())
        <div class="pagination-wrapper">
            {{ $appointments->links() }}
        </div>
    @endif

    <!-- Create/Edit Modal -->
    @if($showCreateModal || $showEditModal)
        <div class="modal-overlay" wire:click="closeModals">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2 class="modal-title">
                        <i class="fas fa-{{ $showCreateModal ? 'plus' : 'edit' }}"></i>
                        {{ $showCreateModal ? 'Nouveau Rendez-vous' : 'Modifier Rendez-vous' }}
                    </h2>
                    <button wire:click="closeModals" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="{{ $showCreateModal ? 'createAppointment' : 'updateAppointment' }}" class="modal-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="contact_id" class="form-label">Contact *</label>
                            <select id="contact_id" wire:model="contact_id" class="form-select" required>
                                <option value="">Sélectionner un contact</option>
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->nom }} {{ $contact->prenom }}</option>
                                @endforeach
                            </select>
                            @error('contact_id') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="activite_id" class="form-label">Activité *</label>
                            <select id="activite_id" wire:model="activite_id" class="form-select" required>
                                <option value="">Sélectionner une activité</option>
                                @foreach($activites as $activite)
                                    <option value="{{ $activite->id }}">{{ $activite->nom }}</option>
                                @endforeach
                            </select>
                            @error('activite_id') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="date_heure" class="form-label">Date et Heure *</label>
                            <input type="datetime-local" id="date_heure" wire:model="date_heure" class="form-input" required>
                            @error('date_heure') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="duree" class="form-label">Durée (minutes) *</label>
                            <input type="number" id="duree" wire:model="duree" class="form-input" min="15" max="480" required>
                            @error('duree') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="statut" class="form-label">Statut *</label>
                            <select id="statut" wire:model="statut" class="form-select" required>
                                <option value="Programmé">Programmé</option>
                                <option value="Confirmé">Confirmé</option>
                                <option value="En cours">En cours</option>
                                <option value="Terminé">Terminé</option>
                                <option value="Annulé">Annulé</option>
                                <option value="Reporté">Reporté</option>
                            </select>
                            @error('statut') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="lieu" class="form-label">Lieu</label>
                            <input type="text" id="lieu" wire:model="lieu" class="form-input">
                            @error('lieu') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group full-width">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea id="notes" wire:model="notes" class="form-textarea" rows="3"></textarea>
                            @error('notes') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" wire:click="closeModals" class="btn btn-secondary">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            {{ $showCreateModal ? 'Créer' : 'Mettre à jour' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal && $selectedAppointment)
        <div class="modal-overlay" wire:click="closeModals">
            <div class="modal-content small" wire:click.stop>
                <div class="modal-header">
                    <h2 class="modal-title">
                        <i class="fas fa-trash text-red-500"></i>
                        Supprimer Rendez-vous
                    </h2>
                    <button wire:click="closeModals" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer le rendez-vous avec <strong>{{ $selectedAppointment->contact->nom }} {{ $selectedAppointment->contact->prenom }}</strong> ?</p>
                    <p class="text-sm text-gray-600">Cette action est irréversible.</p>
                </div>

                <div class="modal-actions">
                    <button wire:click="closeModals" class="btn btn-secondary">
                        Annuler
                    </button>
                    <button wire:click="deleteAppointment" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
/* Sophisticated Architect theme - appointment manager */
.appointment-manager {
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
    background: linear-gradient(135deg, #1b1c1a 0%, #843728 100%);
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

.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.view-toggle {
    display: flex;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 0.5rem;
    padding: 0.25rem;
}

.view-btn {
    padding: 0.5rem 1rem;
    border: none;
    background: transparent;
    color: white;
    border-radius: 0.25rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.view-btn.active, .view-btn:hover {
    background: rgba(255, 255, 255, 0.3);
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
    color: #75786c;
    z-index: 1;
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: none;
    box-shadow: 0 2px 8px rgba(27,28,26,0.03);
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
    box-shadow: 0 2px 8px rgba(27,28,26,0.03);
    border-radius: 0.5rem;
    background: white;
    min-width: 150px;
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
    color: #2d5a2d;
    border: none;
}

.appointments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.appointment-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 2px 8px rgba(27,28,26,0.03);
    border: none;
    overflow: hidden;
    transition: all 0.3s ease;
}

.appointment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

.appointment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: #fbf9f6;
    border-bottom: none;
    box-shadow: 0 2px 8px rgba(27,28,26,0.03);
}

.appointment-date-time {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.date-display {
    text-align: center;
    min-width: 60px;
}

.date-day {
    font-size: 1.5rem;
    font-weight: 700;
    color: #843728;
}

.date-month, .date-year {
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #75786c;
}

.time-display {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #44483e;
    font-weight: 600;
}

.duration {
    font-size: 0.875rem;
    color: #75786c;
    font-weight: normal;
}

.status-dropdown {
    position: relative;
}

.status-select {
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    border: none;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    cursor: pointer;
}

.status-programmé { background: #fef5f3; color: #6d2a1d; }
.status-confirmé { background: #f0faf0; color: #2d5a2d; }
.status-en-cours { background: #fffbf0; color: #6d5624; }
.status-terminé { background: #faf6e8; color: #6d5624; }
.status-annulé { background: #fff5f5; color: #9c1414; }
.status-reporté { background: #f5f3f0; color: #44483e; }

.appointment-content {
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.appointment-info {
    flex: 1;
}

.contact-name {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    color: #2d2e2a;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.activity-name, .appointment-location, .appointment-notes {
    margin: 0.5rem 0;
    color: #75786c;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.appointment-actions {
    display: flex;
    gap: 0.5rem;
    margin-left: 1rem;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}

.edit-btn {
    background: #ffdfa0;
    color: #6d5624;
}

.edit-btn:hover {
    background: #d4a844;
    color: white;
}

.view-btn {
    background: #fef5f3;
    color: #6d2a1d;
    text-decoration: none;
}

.view-btn:hover {
    background: #843728;
    color: white;
}

.delete-btn {
    background: #ffdad6;
    color: #ba1a1a;
}

.delete-btn:hover {
    background: #e05252;
    color: white;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    color: #75786c;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #c5c8b9;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin: 1rem 0 0.5rem 0;
    color: #44483e;
}

.calendar-view {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    padding: 4rem 2rem;
}

.calendar-placeholder {
    text-align: center;
    color: #75786c;
}

.calendar-placeholder i {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #c5c8b9;
}

.calendar-placeholder h3 {
    font-size: 1.5rem;
    margin: 1rem 0 0.5rem 0;
    color: #44483e;
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
    box-shadow: 0 2px 8px rgba(27,28,26,0.03);
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
    box-shadow: 0 2px 8px rgba(27,28,26,0.03);
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
    color: #75786c;
    padding: 0.5rem;
    border-radius: 0.25rem;
}

.modal-close:hover {
    background: #f5f3f0;
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
    color: #44483e;
}

.form-input, .form-select, .form-textarea {
    padding: 0.75rem;
    border: none;
    box-shadow: 0 2px 8px rgba(27,28,26,0.03);
    border-radius: 0.5rem;
    font-size: 1rem;
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: #843728;
    box-shadow: 0 0 0 3px rgba(132, 55, 40, 0.1);
}

.error-message {
    color: #ba1a1a;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.modal-actions {
    padding: 1.5rem;
    border-top: none;
    box-shadow: 0 -2px 8px rgba(27,28,26,0.03);
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
    background: #843728;
    color: white;
}

.btn-primary:hover {
    background: #6d2a1d;
}

.btn-secondary {
    background: #f5f3f0;
    color: #44483e;
    border: none;
}

.btn-secondary:hover {
    background: #e9e6e3;
}

.btn-danger {
    background: #ba1a1a;
    color: white;
}

.btn-danger:hover {
    background: #9c1414;
}

@media (max-width: 768px) {
    .manager-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .header-actions {
        flex-direction: column;
        width: 100%;
    }

    .filters-section {
        flex-direction: column;
    }

    .filter-controls {
        flex-wrap: wrap;
    }

    .appointments-grid {
        grid-template-columns: 1fr;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .appointment-header {
        flex-direction: column;
        gap: 1rem;
    }

    .appointment-content {
        flex-direction: column;
        gap: 1rem;
    }

    .appointment-actions {
        margin-left: 0;
        justify-content: center;
    }
}
</style>
</div>
