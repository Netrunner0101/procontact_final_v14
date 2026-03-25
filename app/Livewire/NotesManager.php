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
    public $priorityFilter = '';
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
    public $contenu = '';
    public $priorite = 'Normale';

    protected $rules = [
        'rendez_vous_id' => 'required|exists:rendez_vous,id',
        'titre' => 'required|string|max:255',
        'contenu' => 'required|string',
        'priorite' => 'required|in:Basse,Normale,Haute,Urgente',
    ];

    public function mount()
    {
        $this->priorityFilter = '';
        $this->appointmentFilter = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
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
            'rendez_vous_id' => $this->rendez_vous_id,
            'titre' => $this->titre,
            'contenu' => $this->contenu,
            'priorite' => $this->priorite,
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
            'contenu' => $this->contenu,
            'priorite' => $this->priorite,
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
        $this->contenu = '';
        $this->priorite = 'Normale';
    }

    private function fillForm($note)
    {
        $this->rendez_vous_id = $note->rendez_vous_id;
        $this->titre = $note->titre;
        $this->contenu = $note->contenu;
        $this->priorite = $note->priorite;
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
                      ->orWhere('contenu', 'like', '%' . $this->search . '%')
                      ->orWhereHas('rendezVous.contact', function($subQ) {
                          $subQ->where('nom', 'like', '%' . $this->search . '%')
                               ->orWhere('prenom', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->priorityFilter, function ($query) {
                $query->where('priorite', $this->priorityFilter);
            })
            ->when($this->appointmentFilter, function ($query) {
                $query->where('rendez_vous_id', $this->appointmentFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);

        $appointments = RendezVous::with('contact')
            ->where('user_id', Auth::id())
            ->orderBy('date_heure', 'desc')
            ->get();

        return view('livewire.notes-manager', [
            'notes' => $notes,
            'appointments' => $appointments,
        ]);
    }
}
