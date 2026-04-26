<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SessionController extends Controller
{
    /**
     * Keepalive ping. The EnforceIdleTimeout middleware already updates
     * `last_activity_at` for us; we just need to acknowledge.
     */
    public function keepalive(): Response
    {
        return response()->noContent();
    }

    /**
     * Returns the configured idle window and how many seconds the user has
     * already been idle, so the client-side timer can stay in sync after
     * a tab restore or background-tab freeze.
     */
    public function status(Request $request): JsonResponse
    {
        $idleSeconds = (int) config('security.idle_minutes', 10) * 60;
        $graceSeconds = (int) config('security.idle_grace_seconds', 60);
        $lastActivity = (int) $request->session()->get('last_activity_at', time());

        return response()->json([
            'idle_seconds_limit' => $idleSeconds,
            'grace_seconds' => $graceSeconds,
            'idle_seconds_elapsed' => max(0, time() - $lastActivity),
        ]);
    }
}
