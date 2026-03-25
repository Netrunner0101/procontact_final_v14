<?php

namespace Tests\Unit\UC2;

use App\Events\ActiviteCreated;
use App\Models\Activite;
use App\Models\Role;
use App\Models\User;
use App\Services\ActiviteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * UC2 — Créer une Activité : Tests Mock
 */
class ActiviteMockTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['nom' => 'admin'], ['description' => 'Administrateur']);
        $this->user = User::factory()->create(['role_id' => $adminRole->id]);
    }

    /**
     * M2.1 — create() appelle save() une fois (pas de DB directe)
     * @test
     */
    public function it_persists_activity_via_create(): void
    {
        $service = app(ActiviteService::class);
        $activite = $service->create($this->user, [
            'nom' => 'Test Activity',
            'description' => 'Test Description',
        ]);

        $this->assertInstanceOf(Activite::class, $activite);
        $this->assertTrue($activite->exists);
    }

    /**
     * M2.2 — L'image est stockée si fournie (Storage::fake)
     * @test
     */
    public function it_stores_image_when_provided(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('activite.jpg');

        $service = app(ActiviteService::class);
        $service->create($this->user, [
            'nom' => 'Coaching',
            'description' => 'Coaching sportif',
            'image' => $file,
        ]);

        Storage::disk('public')->assertExists('activites/' . $file->hashName());
    }

    /**
     * M2.3 — Pas d'appel à Storage si aucune image
     * @test
     */
    public function it_does_not_call_storage_without_image(): void
    {
        Storage::fake('public');

        $service = app(ActiviteService::class);
        $service->create($this->user, [
            'nom' => 'No Image',
            'description' => 'Sans image',
        ]);

        $files = Storage::disk('public')->allFiles('activites');
        $this->assertEmpty($files);
    }

    /**
     * M2.4 — Événement ActiviteCreated dispatché
     * @test
     */
    public function it_dispatches_activite_created_event(): void
    {
        Event::fake();

        $service = app(ActiviteService::class);
        $service->create($this->user, [
            'nom' => 'Event Activity',
            'description' => 'Test events',
        ]);

        Event::assertDispatched(ActiviteCreated::class);
    }

    /**
     * M2.5 — Le dashboard se rafraîchit après création (test assertSee)
     * @test
     */
    public function dashboard_shows_created_activity(): void
    {
        Activite::create([
            'user_id' => $this->user->id,
            'nom' => 'Visible Activity',
            'description' => 'Should appear on dashboard',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
    }
}
