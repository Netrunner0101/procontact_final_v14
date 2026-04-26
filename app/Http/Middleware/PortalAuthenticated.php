<?php

namespace App\Http\Middleware;

use App\Services\PortalAuthService;
use App\Services\PortalTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates portal pages that require an authenticated client (post-OTP).
 *
 * Resolution order:
 *   1. Resolve token from {token} route param.
 *   2. If session marks this token as authenticated, allow.
 *   3. If trusted-device cookie validates for this contact, allow + rotate cookie.
 *   4. Otherwise redirect to portal login (email entry) for this token.
 */
class PortalAuthenticated
{
    public function __construct(
        private PortalTokenService $tokenService,
        private PortalAuthService $authService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = (string) $request->route('token');

        try {
            $contact = $this->tokenService->validate($token);
        } catch (\App\Exceptions\InvalidTokenException $e) {
            return response()->view('portal.error', [], 403);
        }

        $sessionKey = 'portal_auth_' . substr(hash('sha256', $token), 0, 32);
        $rotatedCookie = null;

        if (!$request->session()->get($sessionKey)) {
            $rotatedCookie = $this->authService->validateTrustedDevice($contact, $request);

            if (!$rotatedCookie) {
                return redirect()->route('portal.login', ['token' => $token]);
            }

            $request->session()->put($sessionKey, true);
        }

        $request->attributes->set('portal_contact', $contact);
        $request->attributes->set('portal_token', $token);

        $response = $next($request);

        if ($rotatedCookie instanceof Cookie) {
            $response->headers->setCookie($rotatedCookie);
        }

        return $response;
    }
}
