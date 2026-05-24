<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\Pays;
use App\Models\Status;
use App\Services\AdresseSyncer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

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

    public $status_id = '';

    /**
     * Repeater of addresses for the create/edit form.
     * Each item: ['rue','numero_rue','code_postal','ville','pays_code','is_principale'].
     *
     * @var array<int, array<string, mixed>>
     */
    public array $adresses = [];

    protected $rules = [
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'status_id' => 'nullable|exists:statuses,id',
        'adresses' => 'array',
        'adresses.*.rue' => 'nullable|string|max:255',
        'adresses.*.numero_rue' => 'nullable|string|max:20',
        'adresses.*.code_postal' => 'nullable|string|max:20',
        'adresses.*.ville' => 'nullable|string|max:255',
        'adresses.*.pays_code' => 'nullable|string|size:2|exists:pays,code',
        'adresses.*.is_principale' => 'boolean',
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

    public function addAdresse(): void
    {
        $this->adresses[] = [
            'rue' => '',
            'numero_rue' => '',
            'code_postal' => '',
            'ville' => '',
            'pays_code' => '',
            'is_principale' => empty($this->adresses),
        ];
    }

    public function removeAdresse(int $index): void
    {
        if (! isset($this->adresses[$index])) {
            return;
        }

        $wasPrincipal = (bool) ($this->adresses[$index]['is_principale'] ?? false);
        array_splice($this->adresses, $index, 1);
        $this->adresses = array_values($this->adresses);

        if ($wasPrincipal && ! empty($this->adresses)) {
            $this->adresses[0]['is_principale'] = true;
        }
    }

    public function setPrincipale(int $index): void
    {
        foreach ($this->adresses as $i => $_) {
            $this->adresses[$i]['is_principale'] = ($i === $index);
        }
    }

    public function createContact(): void
    {
        $this->validate();

        $contact = Contact::create([
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'status_id' => $this->status_id ?: null,
            'user_id' => Auth::id(),
        ]);

        app(AdresseSyncer::class)->sync($contact, $this->adresses);

        $this->closeModals();
        session()->flash('success', __('Contact created successfully!'));
    }

    public function updateContact(): void
    {
        $this->validate();

        // Verify ownership
        if ($this->selectedContact->user_id !== Auth::id()) {
            abort(403);
        }

        $this->selectedContact->update([
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'status_id' => $this->status_id ?: null,
        ]);

        app(AdresseSyncer::class)->sync($this->selectedContact, $this->adresses);

        $this->closeModals();
        session()->flash('success', __('Contact updated successfully!'));
    }

    public function deleteContact()
    {
        $this->selectedContact->delete();
        $this->closeModals();
        session()->flash('success', __('Contact deleted successfully!'));
    }

    private function resetForm(): void
    {
        $this->nom = '';
        $this->prenom = '';
        $this->status_id = '';
        $this->adresses = [];
    }

    private function fillForm($contact): void
    {
        $this->nom = $contact->nom;
        $this->prenom = $contact->prenom;
        $this->status_id = $contact->status_id;

        $this->adresses = $contact->adresses()->orderByDesc('is_principale')->get()
            ->map(fn ($a) => [
                'rue' => $a->rue ?? '',
                'numero_rue' => $a->numero_rue ?? '',
                'code_postal' => $a->code_postal ?? '',
                'ville' => $a->ville ?? '',
                'pays_code' => $a->pays_code ?? '',
                'is_principale' => (bool) $a->is_principale,
            ])->toArray();
    }

    public function render()
    {
        $contacts = Contact::with(['status', 'emails', 'numeroTelephones', 'adressePrincipale'])
            ->withCount('rendezVous')
            ->where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nom', 'like', '%'.$this->search.'%')
                        ->orWhere('prenom', 'like', '%'.$this->search.'%')
                        ->orWhereHas('emails', function ($eq) {
                            $eq->where('email', 'like', '%'.$this->search.'%');
                        })
                        ->orWhereHas('numeroTelephones', function ($pq) {
                            $pq->where('numero_telephone', 'like', '%'.$this->search.'%');
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

        $statuses = Cache::remember('statuses', 3600, fn () => Status::all());
        $paysList = Cache::remember('pays_list', 3600, fn () => Pays::orderBy('nom')->get());

        return view('livewire.contact-manager', [
            'contacts' => $contacts,
            'statuses' => $statuses,
            'paysList' => $paysList,
        ]);
    }
}
