<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\Contact;
use App\Models\Activite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\RendezVousNotification;

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
        return view('rendez-vous.create', compact('contacts', 'activites'));
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
            'send_email' => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $rendezVous = RendezVous::create($validated);

        // Send email notification if requested
        if ($request->has('send_email') && $request->send_email) {
            $contact = Contact::with('emails')->find($validated['contact_id']);
            if ($contact->emails->count() > 0) {
                try {
                    Mail::to($contact->emails->first()->email)
                        ->send(new RendezVousNotification($rendezVous));
                    $message = 'Rendez-vous créé avec succès et email envoyé au client';
                } catch (\Exception $e) {
                    $message = 'Rendez-vous créé avec succès mais erreur lors de l\'envoi de l\'email';
                }
            } else {
                $message = 'Rendez-vous créé avec succès mais aucun email disponible pour le contact';
            }
        } else {
            $message = 'Rendez-vous créé avec succès';
        }

        return redirect()->route('rendez-vous.index')->with('success', $message);
    }

    public function show(RendezVous $rendezVous)
    {
        $this->authorize('view', $rendezVous);
        $rendezVous->load(['contact.emails', 'contact.numeroTelephones', 'activite', 'notes', 'rappels']);
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
        return redirect()->route('rendez-vous.show', $rendezVous)->with('success', 'Rendez-vous mis à jour');
    }

    public function destroy(RendezVous $rendezVous)
    {
        $this->authorize('delete', $rendezVous);
        $rendezVous->delete();
        return redirect()->route('rendez-vous.index')->with('success', 'Rendez-vous supprimé');
    }

    public function email(Request $request, RendezVous $rendezVous)
    {
        $this->authorize('view', $rendezVous);
        
        $validated = $request->validate([
            'recipient_email' => 'required|email',
            'cc_emails' => 'nullable|string',
        ]);

        try {
            $mail = Mail::to($validated['recipient_email']);
            
            // Add CC recipients if provided
            if (!empty($validated['cc_emails'])) {
                $ccEmails = array_map('trim', explode(',', $validated['cc_emails']));
                $ccEmails = array_filter($ccEmails, function($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });
                if (!empty($ccEmails)) {
                    $mail->cc($ccEmails);
                }
            }
            
            $mail->send(new RendezVousNotification($rendezVous));
            
            return redirect()->route('rendez-vous.show', $rendezVous)
                ->with('success', 'Email envoyé avec succès');
        } catch (\Exception $e) {
            return redirect()->route('rendez-vous.show', $rendezVous)
                ->with('error', 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
        }
    }
}
