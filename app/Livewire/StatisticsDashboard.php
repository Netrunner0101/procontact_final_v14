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
use Illuminate\Support\Facades\DB;

class StatisticsDashboard extends Component
{
    public $dateRange = '12'; // months
    public $selectedActivity = '';
    public $chartData = [];
    public $monthlyStats = [];
    public $activityStats = [];
    public $statusDistribution = [];
    public $priorityDistribution = [];

    public function mount()
    {
        $this->loadStatistics();
    }

    public function updatedDateRange()
    {
        $this->loadStatistics();
    }

    public function updatedSelectedActivity()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $this->loadMonthlyStats();
        $this->loadActivityStats();
        $this->loadStatusDistribution();
        $this->loadPriorityDistribution();
    }

    private function loadMonthlyStats()
    {
        $months = (int)$this->dateRange;
        $startDate = now()->subMonths($months);
        
        $monthlyData = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthName = $date->format('M Y');
            
            $contacts = Contact::where('user_id', Auth::id())
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $appointments = RendezVous::where('user_id', Auth::id())
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $notes = Note::whereHas('rendezVous', function($query) use ($date) {
                $query->where('user_id', Auth::id());
            })
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
            
            $monthlyData[] = [
                'month' => $monthName,
                'contacts' => $contacts,
                'appointments' => $appointments,
                'notes' => $notes,
            ];
        }
        
        $this->monthlyStats = $monthlyData;
    }

    private function loadActivityStats()
    {
        $query = Activite::withCount(['rendezVous'])
            ->where('user_id', Auth::id());
            
        if ($this->selectedActivity) {
            $query->where('id', $this->selectedActivity);
        }
        
        $this->activityStats = $query->orderBy('rendez_vous_count', 'desc')->get();
    }

    private function loadStatusDistribution()
    {
        $query = RendezVous::select('statut', DB::raw('count(*) as count'))
            ->where('user_id', Auth::id())
            ->groupBy('statut');
            
        if ($this->selectedActivity) {
            $query->where('activite_id', $this->selectedActivity);
        }
        
        $this->statusDistribution = $query->get();
    }

    private function loadPriorityDistribution()
    {
        $query = Note::select('priorite', DB::raw('count(*) as count'))
            ->whereHas('rendezVous', function($q) {
                $q->where('user_id', Auth::id());
                if ($this->selectedActivity) {
                    $q->where('activite_id', $this->selectedActivity);
                }
            })
            ->groupBy('priorite');
            
        $this->priorityDistribution = $query->get();
    }

    public function exportData($type = 'global')
    {
        $filename = 'statistiques_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            if ($type === 'global') {
                // Global export
                fputcsv($file, ['Type', 'Mois', 'Nombre'], ';');
                
                foreach ($this->monthlyStats as $month) {
                    fputcsv($file, ['Contacts', $month['month'], $month['contacts']], ';');
                    fputcsv($file, ['Rendez-vous', $month['month'], $month['appointments']], ';');
                    fputcsv($file, ['Notes', $month['month'], $month['notes']], ';');
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        $activities = Activite::where('user_id', Auth::id())->orderBy('nom')->get();
        
        return view('livewire.statistics-dashboard', [
            'activities' => $activities,
        ]);
    }
}
