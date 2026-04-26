<?php

namespace Tests\Feature\Portal;

use App\Mail\PortalOtpMail;
use App\Models\ClientPortalAccessLog;
use App\Models\ClientPortalOtp;
use App\Models\ClientPortalToken;
use App\Models\ClientPortalTrustedDevice;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use App\Services\PortalAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class PortalOtpAuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Contact $contact;
    protected string $token = 'test-portal-token-abc';
    protected string $contactEmail = 'client@example.com';

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        RateLimiter::clear('portal-otp-request');
        RateLimiter::clear('portal-otp-verify');
        RateLimiter::clear('portal-token-visit');

        $adminRole = Role::firstOrCreate(['nom' => 'admin'], ['description' => 'Admin']);
        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $status = Status::factory()->create();

        $this->contact = Contact::create([
            'user_id' => $this->admin->id,
            'nom' => 'Client',
            'prenom' => 'Test',
            'status_id' => $status->id,
            'portal_token' => $this->token,
        ]);

        Email::create([
            'user_id' => $this->admin->id,
            'contact_id' => $this->contact->id,
            'email' => $this->contactEmail,
        ]);
    }

    public function test_visiting_valid_token_shows_login_form(): void
    {
        $response = $this->get("/portal/{$this->token}");

        $response->assertStatus(200);
        $response->assertSee('Verify your email', false);
    }

    public function test_visiting_invalid_token_returns_403(): void
    {
        $response = $this->get('/portal/not-a-real-token');

        $response->assertStatus(403);
    }

    public function test_request_otp_with_matching_email_creates_otp_and_sends_mail(): void
    {
        $response = $this->post("/portal/{$this->token}/request-otp", [
            'email' => $this->contactEmail,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('client_portal_otps', 1);
        Mail::assertQueued(PortalOtpMail::class, fn($mail) => $mail->hasTo($this->contactEmail));
    }

    public function test_request_otp_with_non_matching_email_does_not_send_but_returns_same_view(): void
    {
        $response = $this->post("/portal/{$this->token}/request-otp", [
            'email' => 'attacker@example.com',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('client_portal_otps', 0);
        Mail::assertNothingQueued();
    }

    public function test_correct_otp_grants_access_and_sets_trusted_device_cookie(): void
    {
        $service = app(PortalAuthService::class);
        $code = '654321';

        ClientPortalOtp::create([
            'contact_id' => $this->contact->id,
            'code_hash' => Hash::make($code),
            'email_hash' => hash('sha256', $this->contactEmail),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->post("/portal/{$this->token}/verify-otp", [
            'email' => $this->contactEmail,
            'code' => $code,
        ]);

        $response->assertRedirect(route('portal.index', ['token' => $this->token]));
        $response->assertCookie(PortalAuthService::COOKIE_NAME);
        $this->assertDatabaseCount('client_portal_trusted_devices', 1);
    }

    public function test_wrong_otp_increments_attempts_and_keeps_otp_active(): void
    {
        $code = '111111';

        $otp = ClientPortalOtp::create([
            'contact_id' => $this->contact->id,
            'code_hash' => Hash::make($code),
            'email_hash' => hash('sha256', $this->contactEmail),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->post("/portal/{$this->token}/verify-otp", [
            'email' => $this->contactEmail,
            'code' => '999999',
        ]);

        $otp->refresh();
        $this->assertEquals(1, $otp->attempts);
        $this->assertNull($otp->consumed_at);
    }

    public function test_max_attempts_invalidates_otp(): void
    {
        $code = '222222';

        $otp = ClientPortalOtp::create([
            'contact_id' => $this->contact->id,
            'code_hash' => Hash::make($code),
            'email_hash' => hash('sha256', $this->contactEmail),
            'attempts' => 5,
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->post("/portal/{$this->token}/verify-otp", [
            'email' => $this->contactEmail,
            'code' => $code,
        ]);

        $response->assertSessionHasErrors('code');
        $otp->refresh();
        $this->assertNotNull($otp->consumed_at);
    }

    public function test_expired_otp_is_rejected(): void
    {
        $code = '333333';

        ClientPortalOtp::create([
            'contact_id' => $this->contact->id,
            'code_hash' => Hash::make($code),
            'email_hash' => hash('sha256', $this->contactEmail),
            'attempts' => 0,
            'expires_at' => now()->subMinutes(1),
        ]);

        $response = $this->post("/portal/{$this->token}/verify-otp", [
            'email' => $this->contactEmail,
            'code' => $code,
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_consumed_otp_cannot_be_reused(): void
    {
        $code = '444444';

        ClientPortalOtp::create([
            'contact_id' => $this->contact->id,
            'code_hash' => Hash::make($code),
            'email_hash' => hash('sha256', $this->contactEmail),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(5),
            'consumed_at' => now(),
        ]);

        $response = $this->post("/portal/{$this->token}/verify-otp", [
            'email' => $this->contactEmail,
            'code' => $code,
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_trusted_device_cookie_grants_direct_access_on_return(): void
    {
        $service = app(PortalAuthService::class);
        $raw = bin2hex(random_bytes(32));

        ClientPortalTrustedDevice::create([
            'contact_id' => $this->contact->id,
            'cookie_hash' => $service->hashToken($raw),
            'user_agent_hash' => hash('sha256', 'Symfony'),
            'last_used_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        $response = $this->withCookie(PortalAuthService::COOKIE_NAME, $raw)
            ->get("/portal/{$this->token}");

        $response->assertStatus(200);
        $response->assertSee('Pro Contact', false);
        $response->assertDontSee('Verify your email', false);
    }

    public function test_logout_revokes_trusted_device(): void
    {
        $service = app(PortalAuthService::class);
        $raw = bin2hex(random_bytes(32));

        $device = ClientPortalTrustedDevice::create([
            'contact_id' => $this->contact->id,
            'cookie_hash' => $service->hashToken($raw),
            'user_agent_hash' => hash('sha256', 'Symfony'),
            'last_used_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        $this->withCookie(PortalAuthService::COOKIE_NAME, $raw)
            ->post("/portal/{$this->token}/logout");

        $device->refresh();
        $this->assertNotNull($device->revoked_at);
    }

    public function test_access_log_records_otp_events(): void
    {
        $this->post("/portal/{$this->token}/request-otp", [
            'email' => $this->contactEmail,
        ]);

        $this->assertTrue(
            ClientPortalAccessLog::where('contact_id', $this->contact->id)
                ->where('event', 'otp_sent')
                ->exists()
        );
    }

    public function test_erasure_revokes_access_and_clears_tokens(): void
    {
        $service = app(PortalAuthService::class);
        $raw = bin2hex(random_bytes(32));

        ClientPortalTrustedDevice::create([
            'contact_id' => $this->contact->id,
            'cookie_hash' => $service->hashToken($raw),
            'user_agent_hash' => hash('sha256', 'Symfony'),
            'last_used_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        $this->post("/portal/{$this->token}/erasure");

        $this->assertEquals(0, ClientPortalTrustedDevice::where('contact_id', $this->contact->id)->whereNull('revoked_at')->count());
        $this->assertNull($this->contact->fresh()->portal_token);
    }

    public function test_authenticated_routes_redirect_to_login_when_not_verified(): void
    {
        $response = $this->get("/portal/{$this->token}/templates");
        $response->assertRedirect(route('portal.login', ['token' => $this->token]));
    }

    public function test_purge_command_removes_expired_records(): void
    {
        ClientPortalOtp::create([
            'contact_id' => $this->contact->id,
            'code_hash' => Hash::make('1'),
            'email_hash' => hash('sha256', 'a@b'),
            'attempts' => 0,
            'expires_at' => now()->subDays(2),
        ]);

        $this->artisan('portal:purge')->assertExitCode(0);

        $this->assertEquals(0, ClientPortalOtp::count());
    }
}
