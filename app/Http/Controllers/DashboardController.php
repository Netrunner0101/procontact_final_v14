<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Activite;
use App\Models\RendezVous;
use App\Models\Rappel;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Statistics
        $totalContacts = Contact::where('user_id', $userId)->count();
        $totalActivites = Activite::where('user_id', $userId)->count();
        $totalRendezVous = RendezVous::where('user_id', $userId)->count();
        $totalRappels = Rappel::whereHas('rendezVous', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();
        $totalNotes = Note::where('user_id', $userId)->count();
        
        // Upcoming reminders
        $upcomingRappels = Rappel::with(['rendezVous.contact', 'rendezVous.activite'])
            ->whereHas('rendezVous', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('date_rappel', '>=', Carbon::now())
            ->orderBy('date_rappel')
            ->limit(5)
            ->get();
        
        // Upcoming appointments
        $upcomingRendezVous = RendezVous::with(['contact', 'activite'])
            ->where('user_id', $userId)
            ->where('date_debut', '>=', Carbon::today())
            ->orderBy('date_debut')
            ->orderBy('heure_debut')
            ->limit(5)
            ->get();
        
        // Recent contacts
        $recentContacts = Contact::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Recent notes
        $recentNotes = Note::with(['contact', 'activite', 'rendezVous'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalContacts',
            'totalActivites', 
            'totalRendezVous',
            'totalRappels',
            'totalNotes',
            'upcomingRendezVous',
            'upcomingRappels',
            'recentContacts',
            'recentNotes'
        ));
    }
}
