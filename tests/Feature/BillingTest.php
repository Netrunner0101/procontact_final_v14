<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::factory()->create(['role_id' => 1]);
    }

    public function test_admin_can_view_billing_page(): void
    {
        $response = $this->actingAs($this->admin())->get(route('billing.index'));

        $response->assertStatus(200);
        $response->assertViewIs('billing.index');
        $response->assertSee(__('No active subscription'), false);
    }

    public function test_app_is_open_when_billing_enforcement_is_off(): void
    {
        config(['billing.enforce' => false]);

        $response = $this->actingAs($this->admin())->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_unsubscribed_admin_is_redirected_to_billing_when_enforced(): void
    {
        config(['billing.enforce' => true]);

        $response = $this->actingAs($this->admin())->get('/dashboard');

        $response->assertRedirect(route('billing.index'));
    }

    public function test_billing_page_itself_is_reachable_when_enforced(): void
    {
        config(['billing.enforce' => true]);

        // Users must always be able to reach the billing page to subscribe,
        // even without an active subscription.
        $response = $this->actingAs($this->admin())->get(route('billing.index'));

        $response->assertStatus(200);
    }
}
