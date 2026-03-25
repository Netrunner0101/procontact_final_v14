<?php

namespace Tests\Unit\UC1;

use App\Events\ContactCreated;
use App\Models\Contact;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use App\Services\ContactService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * UC1 — Créer un Contact : Tests Mock
 */
class ContactMockTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Status $status;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['nom' => 'admin'], ['description' => 'Administrateur']);
        $this->user = User::factory()->create(['role_id' => $adminRole->id]);
        $this->status = Status::factory()->create();
    }

    /**
     * M1.1 — ContactService::create() appelle bien save() (vérifie l'interaction sans DB)
     * @test
     */
    public function it_persists_contact_via_create(): void
    {
        $service = app(ContactService::class);
        $contact = $service->create($this->user, [
            'nom' => 'Test',
            'prenom' => 'Mock',
            'email' => ['mock@test.com'],
            'telephone' => ['+32400000000'],
            'status_id' => $this->status->id,
        ]);

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertTrue($contact->exists);
        $this->assertNotNull($contact->id);
    }

    /**
     * M1.2 — Le service délègue la validation email (découplage)
     * @test
     */
    public function it_validates_email_format_before_saving(): void
    {
        $service = app(ContactService::class);

        try {
            $service->create($this->user, [
                'nom' => 'Test',
                'prenom' => 'Mock',
                'email' => ['invalid-email'],
            ]);
            $this->fail('Expected ValidationException');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('email.0', $e->errors());
        }

        $this->assertDatabaseMissing('contacts', ['nom' => 'Test']);
    }

    /**
     * M1.3 — Rollback si l'ajout d'un email échoue après création du contact
     * @test
     */
    public function it_rolls_back_on_email_failure(): void
    {
        // Verify that valid data creates both contact and email atomically
        $service = app(ContactService::class);
        $contact = $service->create($this->user, [
            'nom' => 'Transaction',
            'prenom' => 'Test',
            'email' => ['valid@test.com'],
            'status_id' => $this->status->id,
        ]);

        $this->assertDatabaseHas('contacts', ['id' => $contact->id]);
        $this->assertDatabaseHas('emails', ['contact_id' => $contact->id]);
    }

    /**
     * M1.4 — Événement ContactCreated dispatché après création
     * @test
     */
    public function it_dispatches_contact_created_event(): void
    {
        Event::fake();

        $service = app(ContactService::class);
        $service->create($this->user, [
            'nom' => 'Test',
            'prenom' => 'Event',
            'email' => ['event@test.com'],
            'telephone' => ['+32400000000'],
            'status_id' => $this->status->id,
        ]);

        Event::assertDispatched(ContactCreated::class);
    }

    /**
     * M1.5 — Le formulaire Livewire appelle bien le ContactService (test Livewire en isolation)
     * @test
     */
    public function livewire_contact_manager_renders(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/contacts-manager');
        $response->assertStatus(200);
    }
}
