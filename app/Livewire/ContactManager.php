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
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedContact = null;

    // Form fields
    public $nom = '';
    public $prenom = '';
    public $email = '';
    public $telephone = '';
    public $adresse = '';
    public $ville = '';
    public $code_postal = '';
    public $pays = 'France';
    public $status_id = '';
    public $notes = '';

    protected $rules = [
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'telephone' => 'nullable|string|max:20',
        'adresse' => 'nullable|string|max:255',
        'ville' => 'nullable|string|max:100',
        'code_postal' => 'nullable|string|max:10',
        'pays' => 'nullable|string|max:100',
        'status_id' => 'required|exists:statuses,id',
        'notes' => 'nullable|string',
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
        $contact = Contact::findOrFail($contactId);
        $this->selectedContact = $contact;
        $this->fillForm($contact);
        $this->showEditModal = true;
    }

    public function openDeleteModal($contactId)
    {
        $this->selectedContact = Contact::findOrFail($contactId);
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
            'email' => $this->email,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'ville' => $this->ville,
            'code_postal' => $this->code_postal,
            'pays' => $this->pays,
            'status_id' => $this->status_id,
            'notes' => $this->notes,
            'user_id' => Auth::id(),
        ]);

        $this->closeModals();
        session()->flash('success', 'Contact créé avec succès!');
    }

    public function updateContact()
    {
        $this->validate();

        $this->selectedContact->update([
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'ville' => $this->ville,
            'code_postal' => $this->code_postal,
            'pays' => $this->pays,
            'status_id' => $this->status_id,
            'notes' => $this->notes,
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
        $this->email = '';
        $this->telephone = '';
        $this->adresse = '';
        $this->ville = '';
        $this->code_postal = '';
        $this->pays = 'France';
        $this->status_id = '';
        $this->notes = '';
    }

    private function fillForm($contact)
    {
        $this->nom = $contact->nom;
        $this->prenom = $contact->prenom;
        $this->email = $contact->email;
        $this->telephone = $contact->telephone;
        $this->adresse = $contact->adresse;
        $this->ville = $contact->ville;
        $this->code_postal = $contact->code_postal;
        $this->pays = $contact->pays;
        $this->status_id = $contact->status_id;
        $this->notes = $contact->notes;
    }

    public function render()
    {
        $contacts = Contact::with('status')
            ->where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('telephone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status_id', $this->statusFilter);
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
