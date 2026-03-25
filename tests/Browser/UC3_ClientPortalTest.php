<?php

namespace Tests\Browser;

use App\Models\Activite;
use App\Models\Contact;
use App\Models\RendezVous;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UC3_ClientPortalTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Helper: create a contact with a portal token and an appointment.
     *
     * @return array{contact: Contact, appointment: RendezVous, token: string}
     */
    private function createPortalFixtures(): array
    {
        $role = Role::firstOrCreate(['nom' => 'admin']);
        $status = Status::firstOrCreate(['status_client' => 'Prospect']);

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $token = bin2hex(random_bytes(32));

        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'portal_token' => $token,
            'nom' => 'Martin',
            'prenom' => 'Sophie',
        ]);

        $activite = Activite::factory()->create([
            'user_id' => $user->id,
            'nom' => 'Coaching individuel',
        ]);

        $appointment = RendezVous::factory()->create([
            'user_id' => $user->id,
            'contact_id' => $contact->id,
            'activite_id' => $activite->id,
            'titre' => 'Séance découverte',
            'description' => 'Première séance de coaching.',
            'date_debut' => now()->addDays(3)->format('Y-m-d'),
            'date_fin' => now()->addDays(3)->format('Y-m-d'),
            'heure_debut' => '10:00',
            'heure_fin' => '11:00',
        ]);

        return compact('contact', 'appointment', 'token');
    }

    /**
     * A valid portal token shows the appointment list.
     */
    public function testPortalAccessWithValidToken(): void
    {
        $fixtures = $this->createPortalFixtures();

        $this->browse(function (Browser $browser) use ($fixtures) {
            $browser->visit('/portal/' . $fixtures['token'])
                ->assertSee('Bonjour Sophie Martin')
                ->assertSee('Séance découverte')
                ->assertSee('Coaching individuel');
        });
    }

    /**
     * Viewing an appointment detail page shows title, dates, and activity.
     */
    public function testPortalViewAppointmentDetails(): void
    {
        $fixtures = $this->createPortalFixtures();
        $appointmentId = $fixtures['appointment']->id;

        $this->browse(function (Browser $browser) use ($fixtures, $appointmentId) {
            $browser->visit('/portal/' . $fixtures['token'] . '/appointment/' . $appointmentId)
                ->assertSee('Séance découverte')
                ->assertSee('Coaching individuel')
                ->assertSee('Date de début')
                ->assertSee('Date de fin')
                ->assertSee('Activité');
        });
    }

    /**
     * Submitting a message via the portal form shows a success message.
     */
    public function testPortalLeaveMessage(): void
    {
        $fixtures = $this->createPortalFixtures();
        $appointmentId = $fixtures['appointment']->id;
        $url = '/portal/' . $fixtures['token'] . '/appointment/' . $appointmentId;

        $this->browse(function (Browser $browser) use ($url) {
            $browser->visit($url)
                ->assertSee('Laisser un message')
                ->type('commentaire', 'Bonjour, je souhaite confirmer ma présence.')
                ->press('Envoyer')
                ->waitForText('Votre message a été envoyé avec succès')
                ->assertSee('Votre message a été envoyé avec succès');
        });
    }

    /**
     * Submitting an empty message shows a validation error.
     */
    public function testPortalEmptyMessage(): void
    {
        $fixtures = $this->createPortalFixtures();
        $appointmentId = $fixtures['appointment']->id;
        $url = '/portal/' . $fixtures['token'] . '/appointment/' . $appointmentId;

        $this->browse(function (Browser $browser) use ($url) {
            $browser->visit($url)
                ->clear('commentaire')
                ->script("document.querySelectorAll('textarea[required]').forEach(i => i.removeAttribute('required'))");

            $browser->press('Envoyer')
                ->waitForText('obligatoire')
                ->assertSee('obligatoire');
        });
    }

    /**
     * An invalid token shows the error page.
     */
    public function testPortalInvalidToken(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/portal/invalidtoken')
                ->assertSee("n'est plus valide");
        });
    }
}
