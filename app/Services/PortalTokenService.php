<?php

namespace App\Services;

use App\Exceptions\InvalidTokenException;
use App\Models\Contact;

class PortalTokenService
{
    public function validate(string $token): Contact
    {
        $contact = Contact::where('portal_token', $token)->first();

        if (!$contact) {
            throw new InvalidTokenException();
        }

        return $contact;
    }

    public function generate(Contact $contact): string
    {
        $token = bin2hex(random_bytes(32));
        $contact->update(['portal_token' => $token]);

        return $token;
    }

    public function revoke(Contact $contact): void
    {
        $contact->update(['portal_token' => null]);
    }
}
