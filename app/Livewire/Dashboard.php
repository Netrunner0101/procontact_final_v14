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
    public $activities = [];
    public $lastLogin;

    public function mount()
    {
        $this->loadStats();
        $this->loadUpcomingAppointments();
        $this->loadRecentContacts();
        $this->loadUpcomingReminders();
        $this->loadActivities();
        $this->lastLogin = Auth::user()->last_login_at;
    }

    public function loadStats()
    {
        $userId = Auth::id();

        $this->stats = [
            'contacts' => Contact::where('user_id', $userId)->count(),
            'appointments' => RendezVous::where('user_id', $userId)->count(),
            'notes' => Note::where('user_id', $userId)->count(),
            'reminders' => Rappel::join('rendez_vous', 'rappels.rendez_vous_id', '=', 'rendez_vous.id')
                ->where('rendez_vous.user_id', $userId)
                ->count(),
            'activities' => Activite::where('user_id', $userId)->count(),
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
            ->join('rendez_vous', 'rappels.rendez_vous_id', '=', 'rendez_vous.id')
            ->where('rendez_vous.user_id', Auth::id())
            ->where('rappels.date_rappel', '>=', now())
            ->orderBy('rappels.date_rappel')
            ->select('rappels.*')
            ->limit(5)
            ->get();
    }

    public function loadActivities()
    {
        $this->activities = Activite::where('user_id', Auth::id())
            ->withCount(['contacts', 'rendezVous'])
            ->orderBy('nom')
            ->get();
    }

    public function refreshStats()
    {
        $this->loadStats();
        $this->loadUpcomingAppointments();
        $this->loadRecentContacts();
        $this->loadUpcomingReminders();
        $this->loadActivities();
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'activities' => $this->activities,
        ]);
    }
}
