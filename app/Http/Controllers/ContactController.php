<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::with(['status', 'emails', 'numeroTelephones'])
            ->where('user_id', Auth::id())
            ->paginate(15);
        
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        $statuses = Status::all();
        return view('contacts.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email|max:255',
            'phones' => 'required|array|min:1',
            'phones.*' => 'required|string|max:20',
            'rue' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:50',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:20',
            'pays' => 'nullable|string|max:255',
            'state_client' => 'nullable|string|max:255',
            'status_id' => 'nullable|exists:statuses,id',
        ]);

        $validated['user_id'] = Auth::id();
        $contact = Contact::create($validated);

        // Save emails
        foreach ($validated['emails'] as $email) {
            $contact->emails()->create(['email' => $email]);
        }

        // Save phone numbers
        foreach ($validated['phones'] as $phone) {
            $contact->numeroTelephones()->create(['numero_telephone' => $phone]);
        }

        return redirect()->route('contacts.index')->with('success', 'Contact créé avec succès');
    }

    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);
        $contact->load(['status', 'emails', 'numeroTelephones', 'rendezVous']);
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);
        $statuses = Status::all();
        return view('contacts.edit', compact('contact', 'statuses'));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'rue' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:50',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:20',
            'pays' => 'nullable|string|max:255',
            'state_client' => 'nullable|string|max:255',
            'status_id' => 'nullable|exists:statuses,id',
        ]);

        $contact->update($validated);
        return redirect()->route('contacts.show', $contact)->with('success', 'Contact mis à jour');
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact supprimé');
    }
}
