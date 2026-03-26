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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $cacheKey = "stats_index_{$userId}";

        $data = Cache::remember($cacheKey, 300, function () use ($userId) {
            // Global Statistics - 5 queries
            $totalContacts = Contact::where('user_id', $userId)->count();
            $totalActivites = Activite::where('user_id', $userId)->count();
            $totalRendezVous = RendezVous::where('user_id', $userId)->count();
            $totalRappels = Rappel::join('rendez_vous', 'rappels.rendez_vous_id', '=', 'rendez_vous.id')
                ->where('rendez_vous.user_id', $userId)
                ->count();
            $totalNotes = Note::where('user_id', $userId)->count();

            // Monthly Statistics - 3 grouped queries instead of 36
            $startDate = Carbon::now()->subMonths(12)->startOfMonth();

            $contactsByMonth = Contact::where('user_id', $userId)
                ->where('created_at', '>=', $startDate)
                ->selectRaw("to_char(created_at, 'YYYY-MM') as month, count(*) as count")
                ->groupBy('month')->pluck('count', 'month');

            $rdvByMonth = RendezVous::where('user_id', $userId)
                ->where('created_at', '>=', $startDate)
                ->selectRaw("to_char(created_at, 'YYYY-MM') as month, count(*) as count")
                ->groupBy('month')->pluck('count', 'month');

            $notesByMonth = Note::where('user_id', $userId)
                ->where('created_at', '>=', $startDate)
                ->selectRaw("to_char(created_at, 'YYYY-MM') as month, count(*) as count")
                ->groupBy('month')->pluck('count', 'month');

            $monthlyStats = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $key = $date->format('Y-m');
                $monthlyStats[] = [
                    'month' => $date->format('M Y'),
                    'contacts' => $contactsByMonth[$key] ?? 0,
                    'rendez_vous' => $rdvByMonth[$key] ?? 0,
                    'notes' => $notesByMonth[$key] ?? 0,
                ];
            }

            // Activity Statistics
            $activiteStats = Activite::where('user_id', $userId)
                ->withCount(['rendezVous', 'contacts', 'notes'])
                ->orderBy('rendez_vous_count', 'desc')
                ->get();

            // Recent Trends - reuse grouped data
            $thisMonth = Carbon::now()->format('Y-m');
            $lastMonth = Carbon::now()->subMonth()->format('Y-m');
            $recentTrends = [
                'contacts_this_month' => $contactsByMonth[$thisMonth] ?? 0,
                'contacts_last_month' => $contactsByMonth[$lastMonth] ?? 0,
                'rendez_vous_this_month' => $rdvByMonth[$thisMonth] ?? 0,
                'rendez_vous_last_month' => $rdvByMonth[$lastMonth] ?? 0,
            ];

            // Upcoming vs Past Appointments - 1 query with conditional counts
            $appointmentStats = RendezVous::where('user_id', $userId)
                ->selectRaw("
                    count(*) filter (where date_debut >= ?) as upcoming,
                    count(*) filter (where date_debut < ?) as past,
                    count(*) filter (where date_debut::date = ?) as today
                ", [Carbon::today(), Carbon::today(), Carbon::today()])
                ->first();

            $appointmentStatsArray = [
                'upcoming' => $appointmentStats->upcoming ?? 0,
                'past' => $appointmentStats->past ?? 0,
                'today' => $appointmentStats->today ?? 0,
            ];

            // Note sharing distribution
            $notePriorities = Note::where('user_id', $userId)
                ->whereNotNull('is_shared_with_client')
                ->selectRaw("case when is_shared_with_client then 'shared' else 'private' end as priorite, count(*) as count")
                ->groupByRaw("case when is_shared_with_client then 'shared' else 'private' end")
                ->pluck('count', 'priorite')
                ->toArray();

            return compact(
                'totalContacts', 'totalActivites', 'totalRendezVous', 'totalRappels', 'totalNotes',
                'monthlyStats', 'activiteStats', 'recentTrends', 'notePriorities'
            ) + ['appointmentStats' => $appointmentStatsArray];
        });

        return view('statistiques.index', $data);
    }
    
    public function activite(Activite $activite)
    {
        if ($activite->user_id !== Auth::id()) {
            abort(403);
        }

        $cacheKey = "stats_activite_{$activite->id}";

        $data = Cache::remember($cacheKey, 300, function () use ($activite) {
            // Activity-specific statistics - use join for rappels instead of whereHas
            $stats = [
                'total_contacts' => $activite->contacts()->count(),
                'total_rendez_vous' => $activite->rendezVous()->count(),
                'total_notes' => $activite->notes()->count(),
                'total_rappels' => Rappel::join('rendez_vous', 'rappels.rendez_vous_id', '=', 'rendez_vous.id')
                    ->where('rendez_vous.activite_id', $activite->id)
                    ->count(),
            ];

            // Monthly trends - 2 grouped queries instead of 12
            $startDate = Carbon::now()->subMonths(6)->startOfMonth();

            $rdvByMonth = $activite->rendezVous()
                ->where('created_at', '>=', $startDate)
                ->selectRaw("to_char(created_at, 'YYYY-MM') as month, count(*) as count")
                ->groupBy('month')->pluck('count', 'month');

            $notesByMonth = $activite->notes()
                ->where('created_at', '>=', $startDate)
                ->selectRaw("to_char(created_at, 'YYYY-MM') as month, count(*) as count")
                ->groupBy('month')->pluck('count', 'month');

            $monthlyTrends = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $key = $date->format('Y-m');
                $monthlyTrends[] = [
                    'month' => $date->format('M Y'),
                    'rendez_vous' => $rdvByMonth[$key] ?? 0,
                    'notes' => $notesByMonth[$key] ?? 0,
                ];
            }

            $topContacts = $activite->contacts()
                ->withCount('rendezVous')
                ->orderBy('rendez_vous_count', 'desc')
                ->limit(5)
                ->get();

            $recentAppointments = $activite->rendezVous()
                ->with('contact')
                ->orderBy('date_debut', 'desc')
                ->limit(10)
                ->get();

            // Appointment status - 1 query with conditional counts
            $apptStats = $activite->rendezVous()
                ->selectRaw("
                    count(*) filter (where date_debut >= ?) as upcoming,
                    count(*) filter (where date_debut < ?) as past,
                    count(*) filter (where date_debut::date = ?) as today
                ", [Carbon::today(), Carbon::today(), Carbon::today()])
                ->first();

            $appointmentStatus = [
                'upcoming' => $apptStats->upcoming ?? 0,
                'past' => $apptStats->past ?? 0,
                'today' => $apptStats->today ?? 0,
            ];

            return compact('stats', 'monthlyTrends', 'topContacts', 'recentAppointments', 'appointmentStatus');
        });

        return view('statistiques.activite', array_merge(['activite' => $activite], $data));
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
