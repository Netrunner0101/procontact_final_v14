<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Contact;
use App\Models\Activite;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::with(['contact', 'activite', 'rendezVous'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('notes.index', compact('notes'));
    }

    public function create(Request $request)
    {
        $contactId = $request->get('contact_id');
        $activiteId = $request->get('activite_id');
        $rendezVousId = $request->get('rendez_vous_id');
        
        $contact = null;
        $activite = null;
        $rendezVous = null;
        
        if ($contactId) {
            $contact = Contact::where('user_id', Auth::id())->findOrFail($contactId);
        }
        
        if ($activiteId) {
            $activite = Activite::where('user_id', Auth::id())->findOrFail($activiteId);
        }
        
        if ($rendezVousId) {
            $rendezVous = RendezVous::where('user_id', Auth::id())->findOrFail($rendezVousId);
        }
        
        $userContacts = Contact::where('user_id', Auth::id())->orderBy('nom')->get();
        $userActivites = Activite::where('user_id', Auth::id())->orderBy('nom')->get();
        $userRendezVous = RendezVous::where('user_id', Auth::id())
            ->with(['contact', 'activite'])
            ->orderBy('date_debut', 'desc')
            ->get();
            
        return view('notes.create', compact(
            'contact', 'activite', 'rendezVous',
            'userContacts', 'userActivites', 'userRendezVous'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'commentaire' => 'required|string',
            'activite_id' => 'nullable|exists:activites,id',
            'rendez_vous_id' => 'nullable|exists:rendez_vous,id',
            'is_shared_with_client' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['date_create'] = now();
        $validated['date_update'] = now();

        if (!empty($validated['activite_id'])) {
            Activite::where('user_id', Auth::id())->findOrFail($validated['activite_id']);
        }

        if (!empty($validated['rendez_vous_id'])) {
            RendezVous::where('user_id', Auth::id())->findOrFail($validated['rendez_vous_id']);
        }

        $note = Note::create($validated);

        return redirect()->route('notes.show', $note)
            ->with('success', __('Note created successfully'));
    }

    public function show(Note $note)
    {
        // Verify ownership
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }
        
        $note->load(['contact', 'activite', 'rendezVous']);
        return view('notes.show', compact('note'));
    }

    public function edit(Note $note)
    {
        // Verify ownership
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }
        
        $userContacts = Contact::where('user_id', Auth::id())->orderBy('nom')->get();
        $userActivites = Activite::where('user_id', Auth::id())->orderBy('nom')->get();
        $userRendezVous = RendezVous::where('user_id', Auth::id())
            ->with(['contact', 'activite'])
            ->orderBy('date_debut', 'desc')
            ->get();
            
        return view('notes.edit', compact('note', 'userContacts', 'userActivites', 'userRendezVous'));
    }

    public function update(Request $request, Note $note)
    {
        // Verify ownership
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'commentaire' => 'required|string',
            'activite_id' => 'nullable|exists:activites,id',
            'rendez_vous_id' => 'nullable|exists:rendez_vous,id',
            'is_shared_with_client' => 'boolean',
        ]);

        $validated['date_update'] = now();

        if (!empty($validated['activite_id'])) {
            Activite::where('user_id', Auth::id())->findOrFail($validated['activite_id']);
        }

        if (!empty($validated['rendez_vous_id'])) {
            RendezVous::where('user_id', Auth::id())->findOrFail($validated['rendez_vous_id']);
        }

        $note->update($validated);

        return redirect()->route('notes.show', $note)
            ->with('success', __('Note updated successfully'));
    }

    public function destroy(Note $note)
    {
        // Verify ownership
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }
        
        $note->delete();
        
        return redirect()->route('notes.index')
            ->with('success', __('Note deleted successfully'));
    }
}
