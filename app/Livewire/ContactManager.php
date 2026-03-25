<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contact;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;

class ContactManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $clientStatusFilter = ''; // Contact/Client auto-status filter
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedContact = null;

    // Form fields
    public $nom = '';
    public $prenom = '';
    public $rue = '';
    public $numero = '';
    public $ville = '';
    public $code_postal = '';
    public $pays = 'France';
    public $status_id = '';

    protected $rules = [
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'rue' => 'nullable|string|max:255',
        'numero' => 'nullable|string|max:50',
        'ville' => 'nullable|string|max:100',
        'code_postal' => 'nullable|string|max:10',
        'pays' => 'nullable|string|max:100',
        'status_id' => 'nullable|exists:statuses,id',
    ];

    public function mount()
    {
        $this->statusFilter = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingClientStatusFilter()
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

    public function openEditModal($contactId)
    {
        $contact = Contact::where('user_id', Auth::id())->findOrFail($contactId);
        $this->selectedContact = $contact;
        $this->fillForm($contact);
        $this->showEditModal = true;
    }

    public function openDeleteModal($contactId)
    {
        $this->selectedContact = Contact::where('user_id', Auth::id())->findOrFail($contactId);
        $this->showDeleteModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->selectedContact = null;
        $this->resetForm();
    }

    public function createContact()
    {
        $this->validate();

        Contact::create([
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'rue' => $this->rue,
            'numero' => $this->numero,
            'ville' => $this->ville,
            'code_postal' => $this->code_postal,
            'pays' => $this->pays,
            'status_id' => $this->status_id ?: null,
            'user_id' => Auth::id(),
        ]);

        $this->closeModals();
        session()->flash('success', 'Contact créé avec succès!');
    }

    public function updateContact()
    {
        $this->validate();

        // Verify ownership
        if ($this->selectedContact->user_id !== Auth::id()) {
            abort(403);
        }

        $this->selectedContact->update([
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'rue' => $this->rue,
            'numero' => $this->numero,
            'ville' => $this->ville,
            'code_postal' => $this->code_postal,
            'pays' => $this->pays,
            'status_id' => $this->status_id ?: null,
        ]);

        $this->closeModals();
        session()->flash('success', 'Contact mis à jour avec succès!');
    }

    public function deleteContact()
    {
        $this->selectedContact->delete();
        $this->closeModals();
        session()->flash('success', 'Contact supprimé avec succès!');
    }

    private function resetForm()
    {
        $this->nom = '';
        $this->prenom = '';
        $this->rue = '';
        $this->numero = '';
        $this->ville = '';
        $this->code_postal = '';
        $this->pays = 'France';
        $this->status_id = '';
    }

    private function fillForm($contact)
    {
        $this->nom = $contact->nom;
        $this->prenom = $contact->prenom;
        $this->rue = $contact->rue;
        $this->numero = $contact->numero;
        $this->ville = $contact->ville;
        $this->code_postal = $contact->code_postal;
        $this->pays = $contact->pays;
        $this->status_id = $contact->status_id;
    }

    public function render()
    {
        $contacts = Contact::with(['status', 'emails', 'numeroTelephones'])
            ->withCount('rendezVous')
            ->where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                      ->orWhereHas('emails', function ($eq) {
                          $eq->where('email', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('numeroTelephones', function ($pq) {
                          $pq->where('numero_telephone', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status_id', $this->statusFilter);
            })
            ->when($this->clientStatusFilter !== '', function ($query) {
                if ($this->clientStatusFilter === 'client') {
                    $query->has('rendezVous');
                } elseif ($this->clientStatusFilter === 'contact') {
                    $query->doesntHave('rendezVous');
                }
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        $statuses = Status::all();

        return view('livewire.contact-manager', [
            'contacts' => $contacts,
            'statuses' => $statuses,
        ]);
    }
}
