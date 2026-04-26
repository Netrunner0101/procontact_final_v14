<?php

namespace App\Services;

use App\Exceptions\InvalidTokenException;
use App\Models\ClientPortalToken;
use App\Models\Contact;

class PortalTokenService
{
    public function __construct(
        private ?PortalAuthService $authService = null,
    ) {}

    private function auth(): PortalAuthService
    {
        return $this->authService ??= app(PortalAuthService::class);
    }

    public function validate(string $token): Contact
    {
        $contact = $this->auth()->findContactByToken($token);

        if (!$contact) {
            throw new InvalidTokenException();
        }

        return $contact;
    }

    public function generate(Contact $contact): string
    {
        $token = bin2hex(random_bytes(32));
        $hash = $this->auth()->hashToken($token);

        $contact->update(['portal_token' => $token]);

        ClientPortalToken::updateOrCreate(
            ['token_hash' => $hash],
            ['contact_id' => $contact->id, 'last_used_at' => null, 'revoked_at' => null]
        );

        return $token;
    }

    public function revoke(Contact $contact): void
    {
        $this->auth()->revokeAllTokens($contact);
        $this->auth()->revokeAllTrustedDevices($contact);
    }
}
