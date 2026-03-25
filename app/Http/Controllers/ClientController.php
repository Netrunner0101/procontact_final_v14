<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display the client dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get appointments for this client
        $appointments = $user->visibleRendezVous()
            ->with(['contact', 'activite'])
            ->orderBy('date_debut', 'desc')
            ->orderBy('heure_debut', 'desc')
            ->paginate(10);

        // Get upcoming appointments
        $upcomingAppointments = $user->visibleRendezVous()
            ->with(['contact', 'activite'])
            ->where('date_debut', '>=', now()->toDateString())
            ->orderBy('date_debut', 'asc')
            ->orderBy('heure_debut', 'asc')
            ->limit(5)
            ->get();

        // Get past appointments count
        $pastAppointmentsCount = $user->visibleRendezVous()
            ->where('date_debut', '<', now()->toDateString())
            ->count();
        
        // Get total appointments count
        $totalAppointmentsCount = $user->visibleRendezVous()->count();
        
        return view('client.dashboard', compact(
            'appointments',
            'upcomingAppointments', 
            'pastAppointmentsCount',
            'totalAppointmentsCount'
        ));
    }
    
    /**
     * Display the specified appointment.
     */
    public function showAppointment(RendezVous $rendezVous)
    {
        $user = Auth::user();
        
        // Check if client can access this appointment
        $canAccess = $user->visibleRendezVous()
            ->where('id', $rendezVous->id)
            ->exists();
        
        if (!$canAccess) {
            abort(403, 'Vous n\'avez pas accès à ce rendez-vous.');
        }
        
        $rendezVous->load(['contact.emails', 'contact.numeroTelephones', 'activite', 'rappels', 'notes']);
        
        return view('client.appointment', compact('rendezVous'));
    }
    
    /**
     * Display all appointments for the client.
     */
    public function appointments(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->visibleRendezVous()->with(['contact', 'activite']);
        
        // Filter by status
        if ($request->has('status')) {
            $now = now();
            switch ($request->status) {
                case 'upcoming':
                    $query->where('date_debut', '>=', $now->toDateString());
                    break;
                case 'past':
                    $query->where('date_debut', '<', $now->toDateString());
                    break;
                case 'today':
                    $query->whereDate('date_debut', $now->toDateString());
                    break;
            }
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('activite', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%");
                  });
            });
        }

        $appointments = $query->orderBy('date_debut', 'desc')->orderBy('heure_debut', 'desc')->paginate(15);
        
        return view('client.appointments', compact('appointments'));
    }
}
