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
use Illuminate\Support\Facades\Cache;
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
        $userId = Auth::id();
        $cacheKey = "livewire_monthly_stats_{$userId}_{$months}";

        $this->monthlyStats = Cache::remember($cacheKey, 300, function () use ($months, $userId) {
            $startDate = now()->subMonths($months)->startOfMonth();

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

            $monthlyData = [];
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $key = $date->format('Y-m');
                $monthlyData[] = [
                    'month' => $date->format('M Y'),
                    'contacts' => $contactsByMonth[$key] ?? 0,
                    'appointments' => $rdvByMonth[$key] ?? 0,
                    'notes' => $notesByMonth[$key] ?? 0,
                ];
            }

            return $monthlyData;
        });
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
        $query = RendezVous::where('user_id', Auth::id());

        if ($this->selectedActivity) {
            $query->where('activite_id', $this->selectedActivity);
        }

        $today = now()->toDateString();
        $stats = $query->selectRaw("
            count(*) filter (where date_debut >= ?) as upcoming,
            count(*) filter (where date_debut < ?) as past,
            count(*) filter (where date_debut::date = ?) as today
        ", [$today, $today, $today])->first();

        $this->statusDistribution = collect([
            (object)['statut' => __('Upcoming'), 'count' => $stats->upcoming ?? 0],
            (object)['statut' => __('Today'), 'count' => $stats->today ?? 0],
            (object)['statut' => __('Past'), 'count' => $stats->past ?? 0],
        ])->filter(fn($item) => $item->count > 0)->values();
    }

    private function loadPriorityDistribution()
    {
        // Compute note sharing distribution (no priorite column in DB)
        $baseQuery = Note::whereHas('rendezVous', function($q) {
            $q->where('user_id', Auth::id());
            if ($this->selectedActivity) {
                $q->where('activite_id', $this->selectedActivity);
            }
        });

        $shared = (clone $baseQuery)->where('is_shared_with_client', true)->count();
        $private = (clone $baseQuery)->where('is_shared_with_client', false)->count();

        $this->priorityDistribution = collect([
            (object)['priorite' => __('Shared'), 'count' => $shared],
            (object)['priorite' => __('Private'), 'count' => $private],
        ])->filter(fn($item) => $item->count > 0)->values();
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
                fputcsv($file, [__('Type'), __('Month'), __('Count')], ';');

                foreach ($this->monthlyStats as $month) {
                    fputcsv($file, [__('Contacts'), $month['month'], $month['contacts']], ';');
                    fputcsv($file, [__('Appointments'), $month['month'], $month['appointments']], ';');
                    fputcsv($file, [__('Notes'), $month['month'], $month['notes']], ';');
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
