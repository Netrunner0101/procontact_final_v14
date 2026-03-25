<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Activite;
use App\Models\RendezVous;
use App\Models\Rappel;
use App\Models\Note;
use App\Models\Statistique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Global Statistics
        $totalContacts = Contact::where('user_id', $userId)->count();
        $totalActivites = Activite::where('user_id', $userId)->count();
        $totalRendezVous = RendezVous::where('user_id', $userId)->count();
        $totalRappels = Rappel::whereHas('rendezVous', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();
        $totalNotes = Note::where('user_id', $userId)->count();
        
        // Monthly Statistics (Last 12 months)
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('M Y');
            
            $monthlyStats[] = [
                'month' => $monthLabel,
                'contacts' => Contact::where('user_id', $userId)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'rendez_vous' => RendezVous::where('user_id', $userId)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'notes' => Note::where('user_id', $userId)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }
        
        // Activity Statistics
        $activiteStats = Activite::where('user_id', $userId)
            ->withCount(['rendezVous', 'contacts', 'notes'])
            ->orderBy('rendez_vous_count', 'desc')
            ->get();
        
        // Recent Activity Trends
        $recentTrends = [
            'contacts_this_month' => Contact::where('user_id', $userId)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'contacts_last_month' => Contact::where('user_id', $userId)
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->count(),
            'rendez_vous_this_month' => RendezVous::where('user_id', $userId)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'rendez_vous_last_month' => RendezVous::where('user_id', $userId)
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->count(),
        ];
        
        // Upcoming vs Past Appointments
        $appointmentStats = [
            'upcoming' => RendezVous::where('user_id', $userId)
                ->where('date_debut', '>=', Carbon::today())
                ->count(),
            'past' => RendezVous::where('user_id', $userId)
                ->where('date_debut', '<', Carbon::today())
                ->count(),
            'today' => RendezVous::where('user_id', $userId)
                ->whereDate('date_debut', Carbon::today())
                ->count(),
        ];
        
        // Priority Distribution for Notes
        $notePriorities = Note::where('user_id', $userId)
            ->select('priorite', DB::raw('count(*) as count'))
            ->groupBy('priorite')
            ->pluck('count', 'priorite')
            ->toArray();
        
        return view('statistiques.index', compact(
            'totalContacts', 'totalActivites', 'totalRendezVous', 'totalRappels', 'totalNotes',
            'monthlyStats', 'activiteStats', 'recentTrends', 'appointmentStats', 'notePriorities'
        ));
    }
    
    public function activite(Activite $activite)
    {
        // Verify ownership
        if ($activite->user_id !== Auth::id()) {
            abort(403);
        }
        
        $userId = Auth::id();
        
        // Activity-specific statistics
        $stats = [
            'total_contacts' => $activite->contacts()->count(),
            'total_rendez_vous' => $activite->rendezVous()->count(),
            'total_notes' => $activite->notes()->count(),
            'total_rappels' => Rappel::whereHas('rendezVous', function($query) use ($activite) {
                $query->where('activite_id', $activite->id);
            })->count(),
        ];
        
        // Monthly trends for this activity (Last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->format('M Y');
            
            $monthlyTrends[] = [
                'month' => $monthLabel,
                'rendez_vous' => $activite->rendezVous()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'notes' => $activite->notes()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }
        
        // Top contacts for this activity
        $topContacts = $activite->contacts()
            ->withCount('rendezVous')
            ->orderBy('rendez_vous_count', 'desc')
            ->limit(5)
            ->get();
        
        // Recent appointments
        $recentAppointments = $activite->rendezVous()
            ->with('contact')
            ->orderBy('date_debut', 'desc')
            ->limit(10)
            ->get();
        
        // Appointment status distribution
        $appointmentStatus = [
            'upcoming' => $activite->rendezVous()
                ->where('date_debut', '>=', Carbon::today())
                ->count(),
            'past' => $activite->rendezVous()
                ->where('date_debut', '<', Carbon::today())
                ->count(),
            'today' => $activite->rendezVous()
                ->whereDate('date_debut', Carbon::today())
                ->count(),
        ];
        
        return view('statistiques.activite', compact(
            'activite', 'stats', 'monthlyTrends', 'topContacts', 
            'recentAppointments', 'appointmentStatus'
        ));
    }
    
    public function exportGlobal()
    {
        $userId = Auth::id();
        
        // Prepare data for export
        $contacts = Contact::where('user_id', $userId)->with(['emails', 'numeroTelephones'])->get();
        $activites = Activite::where('user_id', $userId)->withCount(['contacts', 'rendezVous', 'notes'])->get();
        $rendezVous = RendezVous::where('user_id', $userId)->with(['contact', 'activite'])->get();
        $notes = Note::where('user_id', $userId)->with(['contact', 'activite', 'rendezVous'])->get();
        
        return $this->generateExcelReport([
            'contacts' => $contacts,
            'activites' => $activites,
            'rendez_vous' => $rendezVous,
            'notes' => $notes
        ], 'rapport_global_' . date('Y-m-d'));
    }
    
    public function exportActivite(Activite $activite)
    {
        // Verify ownership
        if ($activite->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Prepare activity-specific data
        $contacts = $activite->contacts()->with(['emails', 'numeroTelephones'])->get();
        $rendezVous = $activite->rendezVous()->with('contact')->get();
        $notes = $activite->notes()->with(['contact', 'rendezVous'])->get();
        
        return $this->generateExcelReport([
            'activite' => $activite,
            'contacts' => $contacts,
            'rendez_vous' => $rendezVous,
            'notes' => $notes
        ], 'rapport_' . \Illuminate\Support\Str::slug($activite->nom) . '_' . date('Y-m-d'));
    }
    
    private function generateExcelReport($data, $filename)
    {
        // Create CSV content (simplified Excel export)
        $output = fopen('php://temp', 'r+');
        
        // Add BOM for UTF-8
        fputs($output, "\xEF\xBB\xBF");
        
        // Global report
        if (isset($data['contacts']) && !isset($data['activite'])) {
            // Summary sheet
            fputcsv($output, ['RAPPORT GLOBAL - ' . date('d/m/Y H:i')], ';');
            fputcsv($output, [], ';');
            fputcsv($output, ['RÉSUMÉ'], ';');
            fputcsv($output, ['Total Contacts', count($data['contacts'])], ';');
            fputcsv($output, ['Total Activités', count($data['activites'])], ';');
            fputcsv($output, ['Total Rendez-vous', count($data['rendez_vous'])], ';');
            fputcsv($output, ['Total Notes', count($data['notes'])], ';');
            fputcsv($output, [], ';');
            
            // Contacts
            fputcsv($output, ['CONTACTS'], ';');
            fputcsv($output, ['Nom', 'Prénom', 'Email', 'Téléphone', 'Ville', 'Date création'], ';');
            foreach ($data['contacts'] as $contact) {
                fputcsv($output, [
                    $contact->nom,
                    $contact->prenom,
                    $contact->emails->first()->email ?? '',
                    $contact->numeroTelephones->first()->numero_telephone ?? '',
                    $contact->ville ?? '',
                    $contact->created_at->format('d/m/Y')
                ], ';');
            }
            fputcsv($output, [], ';');
            
            // Activities
            fputcsv($output, ['ACTIVITÉS'], ';');
            fputcsv($output, ['Nom', 'Description', 'Contacts', 'Rendez-vous', 'Notes', 'Date création'], ';');
            foreach ($data['activites'] as $activite) {
                fputcsv($output, [
                    $activite->nom,
                    $activite->description ?? '',
                    $activite->contacts_count,
                    $activite->rendez_vous_count,
                    $activite->notes_count,
                    $activite->created_at->format('d/m/Y')
                ], ';');
            }
        } else {
            // Activity-specific report
            $activite = $data['activite'];
            fputcsv($output, ['RAPPORT ACTIVITÉ - ' . $activite->nom], ';');
            fputcsv($output, ['Généré le ' . date('d/m/Y H:i')], ';');
            fputcsv($output, [], ';');
            
            // Activity info
            fputcsv($output, ['INFORMATIONS ACTIVITÉ'], ';');
            fputcsv($output, ['Nom', $activite->nom], ';');
            fputcsv($output, ['Description', $activite->description ?? ''], ';');
            fputcsv($output, ['Date création', $activite->created_at->format('d/m/Y')], ';');
            fputcsv($output, [], ';');
            
            // Contacts for this activity
            fputcsv($output, ['CONTACTS ASSOCIÉS'], ';');
            fputcsv($output, ['Nom', 'Prénom', 'Email', 'Téléphone', 'Ville'], ';');
            foreach ($data['contacts'] as $contact) {
                fputcsv($output, [
                    $contact->nom,
                    $contact->prenom,
                    $contact->emails->first()->email ?? '',
                    $contact->numeroTelephones->first()->numero_telephone ?? '',
                    $contact->ville ?? ''
                ], ';');
            }
            fputcsv($output, [], ';');
            
            // Appointments for this activity
            fputcsv($output, ['RENDEZ-VOUS'], ';');
            fputcsv($output, ['Titre', 'Contact', 'Date', 'Heure début', 'Heure fin', 'Description'], ';');
            foreach ($data['rendez_vous'] as $rdv) {
                fputcsv($output, [
                    $rdv->titre,
                    $rdv->contact->prenom . ' ' . $rdv->contact->nom,
                    $rdv->date_debut->format('d/m/Y'),
                    $rdv->heure_debut->format('H:i'),
                    $rdv->heure_fin->format('H:i'),
                    $rdv->description ?? ''
                ], ';');
            }
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');
    }
}
