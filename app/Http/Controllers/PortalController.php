<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidTokenException;
use App\Models\Contact;
use App\Models\Note;
use App\Services\ClientPortalService;
use App\Services\PortalTokenService;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function __construct(
        private PortalTokenService $tokenService,
        private ClientPortalService $portalService,
    ) {}

    /**
     * Display the client's appointment list via magic-link.
     */
    public function index(string $token)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
            return response()->view('portal.error', [], 403);
        }

        $appointments = $this->portalService->getAppointments($contact);

        return view('portal.index', [
            'contact' => $contact,
            'appointments' => $appointments,
            'token' => $token,
        ]);
    }

    /**
     * Display appointment details with shared notes and message form.
     */
    public function showAppointment(string $token, int $appointmentId)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
            return response()->view('portal.error', [], 403);
        }

        $appointment = $contact->rendezVous()
            ->with(['activite', 'notes' => function ($query) {
                $query->where('is_shared_with_client', true);
            }])
            ->findOrFail($appointmentId);

        return view('portal.appointment', [
            'contact' => $contact,
            'appointment' => $appointment,
            'sharedNotes' => $appointment->notes,
            'token' => $token,
        ]);
    }

    /**
     * Store a note from the client (not shared back — visible only to the professional).
     */
    public function storeNote(Request $request, string $token, int $appointmentId)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
            return response()->view('portal.error', [], 403);
        }

        $request->validate([
            'commentaire' => 'required|string',
        ], [
            'commentaire.required' => 'Ce champ est obligatoire.',
        ]);

        // Verify appointment belongs to this contact
        $appointment = $contact->rendezVous()->findOrFail($appointmentId);

        Note::create([
            'user_id' => $appointment->user_id,
            'rendez_vous_id' => $appointment->id,
            'titre' => 'Message du client — ' . $contact->prenom . ' ' . $contact->nom,
            'commentaire' => $request->commentaire,
            'is_shared_with_client' => false,
            'date_create' => now(),
            'date_update' => now(),
        ]);

        return redirect()
            ->route('portal.appointment', ['token' => $token, 'appointmentId' => $appointmentId])
            ->with('success', 'Votre message a été envoyé avec succès.');
    }
}
