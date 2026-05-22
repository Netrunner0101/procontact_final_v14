<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRgpdConsent
{
    /**
     * Redirect any authenticated user who has not yet accepted the current
     * GDPR / Terms documents to the consent page, except when they are
     * already on a whitelisted path (consent flow itself, logout, legal pages).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->terms_accepted_at !== null) {
            return $next($request);
        }

        $allowed = [
            'rgpd.consent.show',
            'rgpd.consent.store',
            'logout',
            'legal.privacy',
            'legal.terms',
            'legal.cookies',
            'lang.switch',
        ];

        if (in_array($request->route()?->getName(), $allowed, true)) {
            return $next($request);
        }

        return redirect()->route('rgpd.consent.show');
    }
}
