<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Note;
use App\Models\RendezVous;
use Illuminate\Support\Facades\Auth;

class NotesManager extends Component
{
    use WithPagination;

    public $search = '';
    public $sharingFilter = '';
    public $appointmentFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedNote = null;

    // Form fields
    public $rendez_vous_id = '';
    public $titre = '';
    public $commentaire = '';
    public $is_shared_with_client = false;

    protected $rules = [
        'rendez_vous_id' => 'required|exists:rendez_vous,id',
        'titre' => 'required|string|max:255',
        'commentaire' => 'required|string',
        'is_shared_with_client' => 'boolean',
    ];

    public function mount()
    {
        $this->sharingFilter = '';
        $this->appointmentFilter = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSharingFilter()
    {
        $this->resetPage();
    }

    public function updatingAppointmentFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($noteId)
    {
        $note = Note::findOrFail($noteId);
        $this->selectedNote = $note;
        $this->fillForm($note);
        $this->showEditModal = true;
    }

    public function openDeleteModal($noteId)
    {
        $this->selectedNote = Note::findOrFail($noteId);
        $this->showDeleteModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->selectedNote = null;
        $this->resetForm();
    }

    public function createNote()
    {
        $this->validate();

        Note::create([
            'user_id' => Auth::id(),
            'rendez_vous_id' => $this->rendez_vous_id,
            'titre' => $this->titre,
            'commentaire' => $this->commentaire,
            'is_shared_with_client' => $this->is_shared_with_client,
            'date_create' => now(),
            'date_update' => now(),
        ]);

        $this->closeModals();
        session()->flash('success', 'Note créée avec succès!');
    }

    public function updateNote()
    {
        $this->validate();

        $this->selectedNote->update([
            'rendez_vous_id' => $this->rendez_vous_id,
            'titre' => $this->titre,
            'commentaire' => $this->commentaire,
            'is_shared_with_client' => $this->is_shared_with_client,
            'date_update' => now(),
        ]);

        $this->closeModals();
        session()->flash('success', 'Note mise à jour avec succès!');
    }

    public function deleteNote()
    {
        $this->selectedNote->delete();
        $this->closeModals();
        session()->flash('success', 'Note supprimée avec succès!');
    }

    private function resetForm()
    {
        $this->rendez_vous_id = '';
        $this->titre = '';
        $this->commentaire = '';
        $this->is_shared_with_client = false;
    }

    private function fillForm($note)
    {
        $this->rendez_vous_id = $note->rendez_vous_id;
        $this->titre = $note->titre;
        $this->commentaire = $note->commentaire;
        $this->is_shared_with_client = $note->is_shared_with_client ?? false;
    }

    public function render()
    {
        $notes = Note::with(['rendezVous.contact', 'rendezVous.activite'])
            ->whereHas('rendezVous', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('titre', 'like', '%' . $this->search . '%')
                      ->orWhere('commentaire', 'like', '%' . $this->search . '%')
                      ->orWhereHas('rendezVous.contact', function($subQ) {
                          $subQ->where('nom', 'like', '%' . $this->search . '%')
                               ->orWhere('prenom', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->sharingFilter !== '', function ($query) {
                $query->where('is_shared_with_client', $this->sharingFilter === '1');
            })
            ->when($this->appointmentFilter, function ($query) {
                $query->where('rendez_vous_id', $this->appointmentFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);

        $appointments = RendezVous::with('contact')
            ->where('user_id', Auth::id())
            ->orderBy('date_debut', 'desc')
            ->get();

        return view('livewire.notes-manager', [
            'notes' => $notes,
            'appointments' => $appointments,
        ]);
    }
}
