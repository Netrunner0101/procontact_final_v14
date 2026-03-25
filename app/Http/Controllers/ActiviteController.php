<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ActiviteController extends Controller
{
    public function index()
    {
        $activites = Activite::where('user_id', Auth::id())->paginate(15);
        return view('activites.index', compact('activites'));
    }

    public function create()
    {
        return view('activites.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'numero_telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('activites', 'public');
        }

        $validated['user_id'] = Auth::id();
        Activite::create($validated);

        return redirect()->route('activites.index')->with('success', 'Activité créée avec succès');
    }

    public function show(Activite $activite)
    {
        $this->authorize('view', $activite);
        $activite->load(['rendezVous', 'contacts']);
        return view('activites.show', compact('activite'));
    }

    public function edit(Activite $activite)
    {
        $this->authorize('update', $activite);
        return view('activites.edit', compact('activite'));
    }

    public function update(Request $request, Activite $activite)
    {
        $this->authorize('update', $activite);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'numero_telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($activite->image) {
                Storage::disk('public')->delete($activite->image);
            }
            $validated['image'] = $request->file('image')->store('activites', 'public');
        }

        $activite->update($validated);
        return redirect()->route('activites.show', $activite)->with('success', 'Activité mise à jour');
    }

    public function destroy(Activite $activite)
    {
        $this->authorize('delete', $activite);
        if ($activite->image) {
            Storage::disk('public')->delete($activite->image);
        }
        $activite->delete();
        return redirect()->route('activites.index')->with('success', 'Activité supprimée');
    }

    public function attachContact(Request $request, Activite $activite)
    {
        $this->authorize('update', $activite);
        
        $request->validate([
            'contact_id' => 'required|exists:contacts,id'
        ]);

        $contact = Contact::findOrFail($request->contact_id);
        
        // Verify the contact belongs to the same user
        if ($contact->user_id !== Auth::id()) {
            abort(403);
        }

        $activite->contacts()->syncWithoutDetaching([$contact->id]);
        
        return redirect()->route('activites.show', $activite)
            ->with('success', 'Contact ajouté à l\'activité avec succès');
    }

    public function detachContact(Activite $activite, Contact $contact)
    {
        $this->authorize('update', $activite);
        
        $activite->contacts()->detach($contact->id);
        
        return redirect()->route('activites.show', $activite)
            ->with('success', 'Contact retiré de l\'activité avec succès');
    }
}
