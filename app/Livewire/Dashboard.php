<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contact;
use App\Models\RendezVous;
use App\Models\Note;
use App\Models\Rappel;
use App\Models\Activite;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $stats = [];
    public $upcomingAppointments = [];
    public $recentContacts = [];
    public $upcomingReminders = [];
    public $lastLogin;

    public function mount()
    {
        $this->loadStats();
        $this->loadUpcomingAppointments();
        $this->loadRecentContacts();
        $this->loadUpcomingReminders();
        $this->lastLogin = Auth::user()->last_login_at;
    }

    public function loadStats()
    {
        $user = Auth::user();
        
        $this->stats = [
            'contacts' => Contact::where('user_id', $user->id)->count(),
            'appointments' => RendezVous::where('user_id', $user->id)->count(),
            'notes' => Note::whereHas('rendezVous', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'reminders' => Rappel::whereHas('rendezVous', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'activities' => Activite::where('user_id', $user->id)->count(),
        ];
    }

    public function loadUpcomingAppointments()
    {
        $this->upcomingAppointments = RendezVous::with(['contact', 'activite'])
            ->where('user_id', Auth::id())
            ->where('date_debut', '>=', now()->toDateString())
            ->orderBy('date_debut')
            ->orderBy('heure_debut')
            ->limit(5)
            ->get();
    }

    public function loadRecentContacts()
    {
        $this->recentContacts = Contact::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function loadUpcomingReminders()
    {
        $this->upcomingReminders = Rappel::with(['rendezVous.contact'])
            ->whereHas('rendezVous', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('date_rappel', '>=', now())
            ->orderBy('date_rappel')
            ->limit(5)
            ->get();
    }

    public function refreshStats()
    {
        $this->loadStats();
        $this->loadUpcomingAppointments();
        $this->loadRecentContacts();
        $this->loadUpcomingReminders();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
