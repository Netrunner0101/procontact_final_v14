<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidTokenException;
use App\Services\PortalTokenService;
use Illuminate\Http\Request;

class PortalPrivacyController extends Controller
{
    public function __construct(private PortalTokenService $tokenService) {}

    public function show(Request $request, string $token)
    {
        try {
            $contact = $this->tokenService->validate($token);
        } catch (InvalidTokenException $e) {
            return response()->view('portal.error', [], 403);
        }

        return view('portal.privacy', [
            'contact' => $contact,
            'token' => $token,
            'controller' => $contact->user,
        ]);
    }
}
