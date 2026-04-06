<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\Contact;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::with(['emails', 'numeroTelephones'])
            ->withCount('rendezVous')
            ->where('user_id', Auth::id())
            ->paginate(15);
        
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        $activites = Activite::where('user_id', Auth::id())->get();
        $selectedActiviteId = request('activite_id');
        return view('contacts.create', compact('activites', 'selectedActiviteId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email|max:255',
            'phones' => 'required|array|min:1',
            'phones.*' => ['required', 'regex:/^[0-9+\s()-]+$/', 'max:20'],
            'rue' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:50',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:20',
            'pays' => 'nullable|string|max:255',
            'activite_id' => 'nullable|exists:activites,id',
        ], [
            'emails.*.email' => __('The email address is not valid.'),
            'phones.*.regex' => __('The phone number must contain only digits, +, spaces, dashes and parentheses.'),
        ]);

        $validated['user_id'] = Auth::id();
        $contact = Contact::create($validated);

        // Save emails
        foreach ($validated['emails'] as $email) {
            $contact->emails()->create(['email' => $email, 'user_id' => Auth::id()]);
        }

        // Save phone numbers
        foreach ($validated['phones'] as $phone) {
            $contact->numeroTelephones()->create(['numero_telephone' => $phone, 'user_id' => Auth::id()]);
        }

        // Link to activity if provided
        if ($request->filled('activite_id')) {
            $activite = Activite::findOrFail($validated['activite_id']);
            if ($activite->user_id !== Auth::id()) {
                abort(403);
            }
            $activite->contacts()->syncWithoutDetaching([$contact->id]);

            return redirect()->route('activites.show', $activite)->with('success', __('Contact created and linked to the activity successfully'));
        }

        return redirect()->route('contacts.index')->with('success', __('Contact created successfully'));
    }

    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);
        $contact->load(['emails', 'numeroTelephones', 'rendezVous']);
        $contact->loadCount('rendezVous');
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);
        $contact->load(['emails', 'numeroTelephones']);
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email|max:255',
            'phones' => 'required|array|min:1',
            'phones.*' => ['required', 'regex:/^[0-9+\s()-]+$/', 'max:20'],
            'rue' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:50',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:20',
            'pays' => 'nullable|string|max:255',
        ], [
            'emails.*.email' => __('The email address is not valid.'),
            'phones.*.regex' => __('The phone number must contain only digits, +, spaces, dashes and parentheses.'),
        ]);

        $contact->update($validated);

        // Sync emails: delete old ones and create new
        $contact->emails()->delete();
        foreach ($validated['emails'] as $email) {
            $contact->emails()->create(['email' => $email, 'user_id' => Auth::id()]);
        }

        // Sync phones: delete old ones and create new
        $contact->numeroTelephones()->delete();
        foreach ($validated['phones'] as $phone) {
            $contact->numeroTelephones()->create(['numero_telephone' => $phone, 'user_id' => Auth::id()]);
        }

        return redirect()->route('contacts.show', $contact)->with('success', __('Contact updated successfully'));
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', __('Contact deleted successfully'));
    }
}
