<?php

namespace Tests\Unit\UC2;

use App\Models\Activite;
use App\Models\Role;
use App\Models\User;
use App\Services\ActiviteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * UC2 — Créer une Activité : Tests Unitaires
 *
 * Précondition : L'indépendant est connecté.
 * Postcondition : La nouvelle activité est visible sur le dashboard avec son nom et son icône.
 */
class ActiviteServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ActiviteService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['nom' => 'admin'], ['description' => 'Administrateur']);
        $this->user = User::factory()->create(['role_id' => $adminRole->id]);
        $this->service = app(ActiviteService::class);
    }

    /**
     * U2.1 — Champs obligatoires valides (nom + description) → persisté en DB avec user_id
     * @test
     */
    public function it_creates_activity_with_required_fields(): void
    {
        $activite = $this->service->create($this->user, [
            'nom' => 'Cours de violon',
            'description' => 'Cours de violon pour débutants',
        ]);

        $this->assertDatabaseHas('activites', [
            'nom' => 'Cours de violon',
            'user_id' => $this->user->id,
        ]);
        $this->assertInstanceOf(Activite::class, $activite);
    }

    /**
     * U2.2 — Champ `nom` vide → ValidationException
     * @test
     */
    public function it_rejects_empty_nom(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create($this->user, [
            'nom' => '',
            'description' => 'Some description',
        ]);
    }

    /**
     * U2.3 — Champ `description` vide → ValidationException
     * @test
     */
    public function it_rejects_empty_description(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create($this->user, [
            'nom' => 'Cours de violon',
            'description' => '',
        ]);
    }

    /**
     * U2.4 — Avec email optionnel valide → email enregistré
     * @test
     */
    public function it_creates_activity_with_optional_email(): void
    {
        $activite = $this->service->create($this->user, [
            'nom' => 'Coaching',
            'description' => 'Coaching sportif',
            'email' => 'coaching@example.com',
        ]);

        $this->assertDatabaseHas('activites', [
            'id' => $activite->id,
            'email' => 'coaching@example.com',
        ]);
    }

    /**
     * U2.5 — Avec email optionnel invalide → rejeté
     * @test
     */
    public function it_rejects_invalid_optional_email(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create($this->user, [
            'nom' => 'Coaching',
            'description' => 'Coaching sportif',
            'email' => 'not-an-email',
        ]);
    }

    /**
     * U2.6 — Avec image (upload) → fichier stocké
     * @test
     */
    public function it_creates_activity_with_image(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->image('activite.jpg');

        $activite = $this->service->create($this->user, [
            'nom' => 'Photo Activity',
            'description' => 'Activity with image',
            'image' => $file,
        ]);

        $this->assertNotNull($activite->image);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($activite->image);
    }

    /**
     * U2.7 — Nom d'activité en doublon pour le même user → rejeté
     * @test
     */
    public function it_rejects_duplicate_name_for_same_user(): void
    {
        $this->service->create($this->user, [
            'nom' => 'Yoga',
            'description' => 'First yoga',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($this->user, [
            'nom' => 'Yoga',
            'description' => 'Duplicate yoga',
        ]);
    }

    /**
     * U2.8 — findByUser retourne uniquement les activités de l'user
     * @test
     */
    public function it_returns_only_activities_for_the_user(): void
    {
        $otherUser = User::factory()->create(['role_id' => $this->user->role_id]);

        $this->service->create($this->user, ['nom' => 'Mine1', 'description' => 'Desc1']);
        $this->service->create($this->user, ['nom' => 'Mine2', 'description' => 'Desc2']);
        $this->service->create($otherUser, ['nom' => 'Other', 'description' => 'Desc3']);

        $activites = $this->service->findByUser($this->user);

        $this->assertCount(2, $activites);
        $activites->each(fn($a) => $this->assertEquals($this->user->id, $a->user_id));
    }
}
