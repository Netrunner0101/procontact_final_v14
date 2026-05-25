<?php

namespace App\Http\Controllers;

use App\Jobs\SendAppointmentEmail;
use App\Models\Activite;
use App\Models\Contact;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RendezVousController extends Controller
{
    public function index()
    {
        $rendezVous = RendezVous::with(['contact', 'activite'])
            ->where('user_id', Auth::id())
            ->orderBy('date_debut', 'desc')
            ->paginate(15);

        return view('rendez-vous.index', compact('rendezVous'));
    }

    public function create()
    {
        $contacts = Contact::where('user_id', Auth::id())->get();
        $activites = Activite::where('user_id', Auth::id())->get();
        $selectedActiviteId = request('activite_id');

        return view('rendez-vous.create', compact('contacts', 'activites', 'selectedActiviteId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'activite_id' => 'required|exists:activites,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        $validated['user_id'] = Auth::id();
        $rendezVous = RendezVous::create($validated);

        // Always send email notification
        SendAppointmentEmail::dispatch($rendezVous);

        return redirect()->route('rendez-vous.index')->with('success', __('Appointment created successfully and email notification sent'));
    }

    public function show(RendezVous $rendezVous)
    {
        $this->authorize('view', $rendezVous);
        $rendezVous->load(['contact.emails', 'contact.numeroTelephones', 'contact.adressePrincipale', 'activite', 'notes', 'rappels']);

        return view('rendez-vous.show', compact('rendezVous'));
    }

    public function edit(RendezVous $rendezVous)
    {
        $this->authorize('update', $rendezVous);
        $contacts = Contact::where('user_id', Auth::id())->get();
        $activites = Activite::where('user_id', Auth::id())->get();

        return view('rendez-vous.edit', compact('rendezVous', 'contacts', 'activites'));
    }

    public function update(Request $request, RendezVous $rendezVous)
    {
        $this->authorize('update', $rendezVous);

        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'activite_id' => 'required|exists:activites,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        $rendezVous->update($validated);

        return redirect()->route('rendez-vous.show', $rendezVous)->with('success', __('Appointment updated successfully'));
    }

    public function destroy(RendezVous $rendezVous)
    {
        $this->authorize('delete', $rendezVous);
        $rendezVous->delete();

        return redirect()->route('rendez-vous.index')->with('success', __('Appointment deleted successfully'));
    }

    public function updateStatus(Request $request, RendezVous $rendezVous)
    {
        $this->authorize('update', $rendezVous);

        $validated = $request->validate([
            'statut' => ['required', 'string', 'in:'.implode(',', array_keys(RendezVous::STATUTS))],
        ]);

        $rendezVous->update($validated);

        return redirect()->route('rendez-vous.show', $rendezVous)
            ->with('success', __('Status updated'));
    }

    public function email(Request $request, RendezVous $rendezVous)
    {
        $this->authorize('view', $rendezVous);

        $validated = $request->validate([
            'recipient_email' => 'required|email',
            'cc_emails' => 'nullable|string',
        ]);

        $ccEmails = [];
        if (! empty($validated['cc_emails'])) {
            $rawCc = preg_split('/[\s,;]+/', $validated['cc_emails'], -1, PREG_SPLIT_NO_EMPTY);
            $ccEmails = array_values(array_filter(
                array_map('trim', $rawCc),
                fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL)
            ));
        }

        try {
            // dispatchSync so the user sees the real outcome of sending. With
            // dispatch() the job sits in the queue and the UI reports success
            // even when no queue worker is running or the mailer fails.
            SendAppointmentEmail::dispatchSync($rendezVous, $validated['recipient_email'], $ccEmails);
        } catch (\Throwable $e) {
            Log::error('Failed to send appointment email', [
                'rendez_vous_id' => $rendezVous->id,
                'recipient' => $validated['recipient_email'],
                'cc' => $ccEmails,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('rendez-vous.show', $rendezVous)
                ->with('error', __('The email could not be sent. Please try again.').' ('.$e->getMessage().')');
        }

        return redirect()->route('rendez-vous.show', $rendezVous)
            ->with('success', __('Email sent successfully.'));
    }
}
