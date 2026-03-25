<?php

namespace Tests\Unit\UC1;

use App\Models\Contact;
use App\Models\Email;
use App\Models\NumeroTelephone;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use App\Services\ContactService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * UC1 — Créer un Contact : Tests Unitaires
 *
 * Précondition : L'indépendant est authentifié.
 * Postcondition : Le contact est créé, ses emails et numéros de téléphone sont enregistrés.
 */
class ContactServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ContactService $service;
    protected Status $status;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['nom' => 'admin'], ['description' => 'Administrateur']);
        $this->user = User::factory()->create(['role_id' => $adminRole->id]);
        $this->status = Status::factory()->create();
        $this->service = app(ContactService::class);
    }

    /**
     * U1.1 — Données valides (nom, prénom, email, tél) → contact persisté en DB
     * @test
     */
    public function it_creates_a_contact_with_valid_data(): void
    {
        $data = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => ['jean@example.com'],
            'telephone' => ['+32477000000'],
            'status_id' => $this->status->id,
        ];

        $contact = $this->service->create($this->user, $data);

        $this->assertDatabaseHas('contacts', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'user_id' => $this->user->id,
        ]);
        $this->assertDatabaseHas('emails', ['contact_id' => $contact->id, 'email' => 'jean@example.com']);
        $this->assertDatabaseHas('numero_telephones', ['contact_id' => $contact->id, 'numero_telephone' => '+32477000000']);
    }

    /**
     * U1.2 — Email en double (déjà existant pour cet utilisateur) → ValidationException
     * @test
     */
    public function it_rejects_duplicate_email_for_same_user(): void
    {
        // Create first contact with email
        $existingContact = Contact::create([
            'user_id' => $this->user->id,
            'nom' => 'Existing',
            'prenom' => 'Contact',
            'status_id' => $this->status->id,
        ]);
        $existingContact->emails()->create(['email' => 'jean@example.com']);

        $this->expectException(ValidationException::class);

        $this->service->create($this->user, [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => ['jean@example.com'],
            'telephone' => ['+32477000000'],
        ]);
    }

    /**
     * U1.3 — Champ `nom` vide → rejeter avec message d'erreur
     * @test
     */
    public function it_rejects_empty_nom(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create($this->user, [
            'nom' => '',
            'prenom' => 'Jean',
            'email' => ['jean@example.com'],
        ]);
    }

    /**
     * U1.4 — Champ `prenom` vide → rejeter avec message d'erreur
     * @test
     */
    public function it_rejects_empty_prenom(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create($this->user, [
            'nom' => 'Dupont',
            'prenom' => '',
            'email' => ['jean@example.com'],
        ]);
    }

    /**
     * U1.5 — Email au format invalide → erreur de format
     * @test
     */
    public function it_rejects_invalid_email_format(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create($this->user, [
            'nom' => 'Test',
            'prenom' => 'User',
            'email' => ['not-an-email'],
        ]);
    }

    /**
     * U1.6 — Numéro de téléphone invalide (lettres) → validation format
     * @test
     */
    public function it_rejects_invalid_phone_number(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create($this->user, [
            'nom' => 'Test',
            'prenom' => 'User',
            'email' => ['test@example.com'],
            'telephone' => ['abc-not-a-phone'],
        ]);
    }

    /**
     * U1.7 — Ajout d'un 2e numéro de téléphone (A1)
     * @test
     */
    public function it_adds_a_second_phone_number(): void
    {
        $contact = Contact::create([
            'user_id' => $this->user->id,
            'nom' => 'Test',
            'prenom' => 'User',
            'status_id' => $this->status->id,
        ]);
        $contact->numeroTelephones()->create(['numero_telephone' => '+32477000001']);

        $this->service->addPhone($contact, '+32477000002');

        $this->assertCount(2, $contact->fresh()->numeroTelephones);
    }

    /**
     * U1.8 — Ajout d'un 2e email (A1)
     * @test
     */
    public function it_adds_a_second_email(): void
    {
        $contact = Contact::create([
            'user_id' => $this->user->id,
            'nom' => 'Test',
            'prenom' => 'User',
            'status_id' => $this->status->id,
        ]);
        $contact->emails()->create(['email' => 'first@example.com']);

        $this->service->addEmail($contact, 'second@example.com');

        $this->assertCount(2, $contact->fresh()->emails);
    }

    /**
     * U1.9 — Tous champs obligatoires manquants → retourne les champs en erreur
     * @test
     */
    public function it_rejects_all_missing_required_fields(): void
    {
        try {
            $this->service->create($this->user, []);
            $this->fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $this->assertArrayHasKey('nom', $errors);
            $this->assertArrayHasKey('prenom', $errors);
            $this->assertArrayHasKey('email', $errors);
        }
    }

    /**
     * U1.10 — findByUser retourne uniquement les contacts de l'user connecté
     * @test
     */
    public function it_returns_only_contacts_for_the_user(): void
    {
        $otherUser = User::factory()->create(['role_id' => $this->user->role_id]);

        Contact::create(['user_id' => $this->user->id, 'nom' => 'Mine', 'prenom' => 'A', 'status_id' => $this->status->id]);
        Contact::create(['user_id' => $this->user->id, 'nom' => 'Mine', 'prenom' => 'B', 'status_id' => $this->status->id]);
        Contact::create(['user_id' => $otherUser->id, 'nom' => 'Other', 'prenom' => 'C', 'status_id' => $this->status->id]);

        $contacts = $this->service->findByUser($this->user);

        $this->assertCount(2, $contacts);
        $contacts->each(fn($c) => $this->assertEquals($this->user->id, $c->user_id));
    }
}
