<?php

namespace App\Livewire;

use App\Models\Note;
use App\Models\RendezVous;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Realtime notes panel for an appointment.
 *
 * - Polls every 5 seconds and re-fetches the notes list (and emits a
 *   subtle "new note" pulse if the count changed).
 * - Inline create / edit / delete (no full-page navigation).
 * - Author badges: "You" (entrepreneur), "Client" (portal-authored note),
 *   "Provider" (other entrepreneur user).
 * - Toggle controls whether a note is visible to the client portal.
 */
class AppointmentNotes extends Component
{
    public RendezVous $rendezVous;

    public bool $showForm = false;
    public ?int $editingNoteId = null;

    #[Validate('nullable|string|max:255')]
    public string $titre = '';

    #[Validate('required|string|min:1')]
    public string $commentaire = '';

    #[Validate('boolean')]
    public bool $is_shared_with_client = true;

    public int $lastSeenCount = 0;
    public bool $hasNew = false;

    public function mount(RendezVous $rendezVous): void
    {
        abort_unless($rendezVous->user_id === Auth::id(), 403);
        $this->rendezVous = $rendezVous;
        $this->lastSeenCount = $this->notesQuery()->count();
    }

    private function notesQuery()
    {
        return Note::where('rendez_vous_id', $this->rendezVous->id)
            ->where('user_id', $this->rendezVous->user_id);
    }

    public function refreshNotes(): void
    {
        $current = $this->notesQuery()->count();
        if ($current > $this->lastSeenCount) {
            $this->hasNew = true;
            $this->dispatch('notes-new-arrived');
        }
        $this->lastSeenCount = $current;
    }

    public function dismissNew(): void
    {
        $this->hasNew = false;
    }

    public function startCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function startEdit(int $noteId): void
    {
        $note = $this->notesQuery()->findOrFail($noteId);
        $this->editingNoteId = $note->id;
        $this->titre = (string) $note->titre;
        $this->commentaire = (string) $note->commentaire;
        $this->is_shared_with_client = (bool) $note->is_shared_with_client;
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingNoteId) {
            $note = $this->notesQuery()->findOrFail($this->editingNoteId);
            $note->update([
                'titre' => $this->titre ?: $note->titre,
                'commentaire' => $this->commentaire,
                'is_shared_with_client' => $this->is_shared_with_client,
                'date_update' => now(),
            ]);
            $this->dispatch('toast', message: __('Note updated.'));
        } else {
            Note::create([
                'user_id' => $this->rendezVous->user_id,
                'rendez_vous_id' => $this->rendezVous->id,
                'activite_id' => $this->rendezVous->activite_id,
                'titre' => $this->titre ?: __('Note'),
                'commentaire' => $this->commentaire,
                'is_shared_with_client' => $this->is_shared_with_client,
                'date_create' => now(),
                'date_update' => now(),
            ]);
            $this->dispatch('toast', message: __('Note added.'));
        }

        $this->resetForm();
        $this->lastSeenCount = $this->notesQuery()->count();
    }

    public function delete(int $noteId): void
    {
        $note = $this->notesQuery()->findOrFail($noteId);
        $note->delete();
        $this->lastSeenCount = $this->notesQuery()->count();
        $this->dispatch('toast', message: __('Note deleted.'));
    }

    public function toggleShared(int $noteId): void
    {
        $note = $this->notesQuery()->findOrFail($noteId);
        $note->update([
            'is_shared_with_client' => !$note->is_shared_with_client,
            'date_update' => now(),
        ]);
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingNoteId = null;
        $this->titre = '';
        $this->commentaire = '';
        $this->is_shared_with_client = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $notes = $this->notesQuery()
            ->with('contact:id,prenom,nom')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.appointment-notes', [
            'notes' => $notes,
            'currentUserId' => Auth::id(),
        ]);
    }
}
