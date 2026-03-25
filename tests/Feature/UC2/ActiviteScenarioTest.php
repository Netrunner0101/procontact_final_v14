<?php

namespace Tests\Feature\UC2;

use App\Models\Activite;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * UC2 — Créer une Activité : Tests Scénarios
 *
 * S2.1 Nominal — Création + confirmation + redirection dashboard
 * S2.2 Nominal — Clic sur « Ok » après confirmation
 * S2.3 E1 — Champs obligatoires manquants → champs en rouge
 * S2.4 Annuler la création → retour dashboard sans nouvelles données
 */
class ActiviteScenarioTest extends TestCase
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
     * S2.1 — Nominal : Création + confirmation + redirection vers index avec message succès
     * @test
     */
    public function nominal_creation_redirects_with_success(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)->post('/activites', [
            'nom' => 'Cours de piano',
            'description' => 'Cours pour adultes',
        ]);

        $response->assertRedirect(route('activites.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('activites', [
            'nom' => 'Cours de piano',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * S2.2 — Nominal : Après création, l'activité est visible dans l'index
     * @test
     */
    public function created_activity_appears_in_index(): void
    {
        Activite::create([
            'user_id' => $this->user->id,
            'nom' => 'Yoga Matinal',
            'description' => 'Yoga le matin',
        ]);

        $response = $this->actingAs($this->user)->get('/activites');
        $response->assertStatus(200);
        $response->assertSee('Yoga Matinal');
    }

    /**
     * S2.3 — E1 : Champs obligatoires manquants → erreurs de validation
     * @test
     */
    public function missing_required_fields_show_errors(): void
    {
        $response = $this->actingAs($this->user)->post('/activites', []);

        $response->assertSessionHasErrors(['nom']);
    }

    /**
     * S2.4 — Annuler la création → retour index sans nouvelles données
     * @test
     */
    public function cancel_creation_returns_to_index_without_data(): void
    {
        // Access create page
        $response = $this->actingAs($this->user)->get('/activites/create');
        $response->assertStatus(200);

        // Go back to index without posting
        $response = $this->actingAs($this->user)->get('/activites');
        $response->assertStatus(200);

        $this->assertEquals(0, Activite::where('user_id', $this->user->id)->count());
    }
}
