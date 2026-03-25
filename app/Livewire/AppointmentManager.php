<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RendezVous;
use App\Models\Contact;
use App\Models\Activite;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $contactFilter = '';
    public $activiteFilter = '';
    public $dateFilter = '';
    public $sortBy = 'date_debut';
    public $sortDirection = 'desc';
    public $viewMode = 'list'; // list, calendar
    
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedAppointment = null;

    // Form fields
    public $contact_id = '';
    public $activite_id = '';
    public $date_debut = '';
    public $date_fin = '';
    public $heure_debut = '';
    public $heure_fin = '';
    public $titre = '';
    public $description = '';
    public $duree = 60;
    public $statut = 'Programmé';
    public $notes = '';
    public $lieu = '';

    protected $rules = [
        'contact_id' => 'required|exists:contacts,id',
        'activite_id' => 'required|exists:activites,id',
        'date_debut' => 'required|date|after_or_equal:today',
        'date_fin' => 'nullable|date|after_or_equal:date_debut',
        'heure_debut' => 'required|date_format:H:i',
        'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
        'titre' => 'required|string|max:255',
        'description' => 'nullable|string',
        'statut' => 'required|in:Programmé,Confirmé,En cours,Terminé,Annulé,Reporté',
        'notes' => 'nullable|string',
        'lieu' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->dateFilter = '';
        $this->date_debut = now()->addDay()->format('Y-m-d');
        $this->heure_debut = '09:00';
        $this->heure_fin = '10:00';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingContactFilter()
    {
        $this->resetPage();
    }

    public function updatingActiviteFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
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

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($appointmentId)
    {
        $appointment = RendezVous::findOrFail($appointmentId);
        $this->selectedAppointment = $appointment;
        $this->fillForm($appointment);
        $this->showEditModal = true;
    }

    public function openDeleteModal($appointmentId)
    {
        $this->selectedAppointment = RendezVous::findOrFail($appointmentId);
        $this->showDeleteModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->selectedAppointment = null;
        $this->resetForm();
    }

    public function createAppointment()
    {
        $this->validate();

        RendezVous::create([
            'contact_id' => $this->contact_id,
            'activite_id' => $this->activite_id,
            'titre' => $this->titre,
            'description' => $this->description,
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin ?: $this->date_debut,
            'heure_debut' => $this->heure_debut,
            'heure_fin' => $this->heure_fin ?: $this->heure_debut,
            'user_id' => Auth::id(),
        ]);

        $this->closeModals();
        session()->flash('success', 'Rendez-vous créé avec succès!');
    }

    public function updateAppointment()
    {
        $this->validate();

        $this->selectedAppointment->update([
            'contact_id' => $this->contact_id,
            'activite_id' => $this->activite_id,
            'titre' => $this->titre,
            'description' => $this->description,
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin ?: $this->date_debut,
            'heure_debut' => $this->heure_debut,
            'heure_fin' => $this->heure_fin ?: $this->heure_debut,
        ]);

        $this->closeModals();
        session()->flash('success', 'Rendez-vous mis à jour avec succès!');
    }

    public function deleteAppointment()
    {
        $this->selectedAppointment->delete();
        $this->closeModals();
        session()->flash('success', 'Rendez-vous supprimé avec succès!');
    }

    public function updateStatus($appointmentId, $status)
    {
        $appointment = RendezVous::where('user_id', Auth::id())->findOrFail($appointmentId);
        $appointment->update(['statut' => $status]);
        session()->flash('success', 'Statut mis à jour avec succès!');
    }

    private function resetForm()
    {
        $this->contact_id = '';
        $this->activite_id = '';
        $this->titre = '';
        $this->description = '';
        $this->date_debut = now()->addDay()->format('Y-m-d');
        $this->date_fin = '';
        $this->heure_debut = '09:00';
        $this->heure_fin = '10:00';
        $this->duree = 60;
        $this->statut = 'Programmé';
        $this->notes = '';
        $this->lieu = '';
    }

    private function fillForm($appointment)
    {
        $this->contact_id = $appointment->contact_id;
        $this->activite_id = $appointment->activite_id;
        $this->titre = $appointment->titre;
        $this->description = $appointment->description;
        $this->date_debut = $appointment->date_debut->format('Y-m-d');
        $this->date_fin = $appointment->date_fin ? $appointment->date_fin->format('Y-m-d') : '';
        $this->heure_debut = $appointment->heure_debut->format('H:i');
        $this->heure_fin = $appointment->heure_fin ? $appointment->heure_fin->format('H:i') : '';
    }

    public function render()
    {
        $appointments = RendezVous::with(['contact', 'activite'])
            ->where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->whereHas('contact', function ($q) {
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('statut', $this->statusFilter);
            })
            ->when($this->contactFilter, function ($query) {
                $query->where('contact_id', $this->contactFilter);
            })
            ->when($this->activiteFilter, function ($query) {
                $query->where('activite_id', $this->activiteFilter);
            })
            ->when($this->dateFilter, function ($query) {
                switch ($this->dateFilter) {
                    case 'today':
                        $query->whereDate('date_debut', today());
                        break;
                    case 'tomorrow':
                        $query->whereDate('date_debut', today()->addDay());
                        break;
                    case 'this_week':
                        $query->whereBetween('date_debut', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
                        break;
                    case 'next_week':
                        $query->whereBetween('date_debut', [now()->addWeek()->startOfWeek()->toDateString(), now()->addWeek()->endOfWeek()->toDateString()]);
                        break;
                    case 'this_month':
                        $query->whereMonth('date_debut', now()->month)
                              ->whereYear('date_debut', now()->year);
                        break;
                }
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);

        $contacts = Contact::where('user_id', Auth::id())->orderBy('nom')->get();
        $activites = Activite::where('user_id', Auth::id())->orderBy('nom')->get();

        return view('livewire.appointment-manager', [
            'appointments' => $appointments,
            'contacts' => $contacts,
            'activites' => $activites,
        ]);
    }
}
