<?php

namespace App\Http\Controllers;

use App\Models\Rappel;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RappelController extends Controller
{
    public function index()
    {
        $rappels = Rappel::with(['rendezVous.contact', 'rendezVous.activite'])
            ->whereHas('rendezVous', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('date_rappel', 'asc')
            ->paginate(15);
        
        return view('rappels.index', compact('rappels'));
    }

    public function create(Request $request)
    {
        $rendezVousId = $request->get('rendez_vous_id');
        $rendezVous = null;
        
        if ($rendezVousId) {
            $rendezVous = RendezVous::where('user_id', Auth::id())
                ->findOrFail($rendezVousId);
        }
        
        $userRendezVous = RendezVous::where('user_id', Auth::id())
            ->with(['contact', 'activite'])
            ->orderBy('date_debut', 'desc')
            ->get();
            
        return view('rappels.create', compact('rendezVous', 'userRendezVous'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rendez_vous_id' => 'required|exists:rendez_vous,id',
            'date_rappel' => 'required|date|after:now',
            'frequence' => 'required|in:Une fois,Quotidien,Hebdomadaire,Mensuel',
        ]);

        // Verify the appointment belongs to the current user
        $rendezVous = RendezVous::where('user_id', Auth::id())
            ->findOrFail($validated['rendez_vous_id']);

        Rappel::create($validated);

        return redirect()->route('rendez-vous.show', $rendezVous)
            ->with('success', 'Rappel créé avec succès');
    }

    public function show(Rappel $rappel)
    {
        // Verify access through appointment ownership
        if ($rappel->rendezVous->user_id !== Auth::id()) {
            abort(403);
        }
        
        $rappel->load(['rendezVous.contact', 'rendezVous.activite']);
        return view('rappels.show', compact('rappel'));
    }

    public function edit(Rappel $rappel)
    {
        // Verify access through appointment ownership
        if ($rappel->rendezVous->user_id !== Auth::id()) {
            abort(403);
        }
        
        $userRendezVous = RendezVous::where('user_id', Auth::id())
            ->with(['contact', 'activite'])
            ->orderBy('date_debut', 'desc')
            ->get();
            
        return view('rappels.edit', compact('rappel', 'userRendezVous'));
    }

    public function update(Request $request, Rappel $rappel)
    {
        // Verify access through appointment ownership
        if ($rappel->rendezVous->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'rendez_vous_id' => 'required|exists:rendez_vous,id',
            'date_rappel' => 'required|date|after:now',
            'frequence' => 'required|in:Une fois,Quotidien,Hebdomadaire,Mensuel',
        ]);

        // Verify the new appointment belongs to the current user
        $rendezVous = RendezVous::where('user_id', Auth::id())
            ->findOrFail($validated['rendez_vous_id']);

        $rappel->update($validated);

        return redirect()->route('rappels.show', $rappel)
            ->with('success', 'Rappel mis à jour avec succès');
    }

    public function destroy(Rappel $rappel)
    {
        // Verify access through appointment ownership
        if ($rappel->rendezVous->user_id !== Auth::id()) {
            abort(403);
        }
        
        $rendezVous = $rappel->rendezVous;
        $rappel->delete();
        
        return redirect()->route('rendez-vous.show', $rendezVous)
            ->with('success', 'Rappel supprimé avec succès');
    }
}
