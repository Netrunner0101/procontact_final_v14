<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceIdleTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $idleMinutes = (int) config('security.idle_minutes', 10);
        $graceSeconds = (int) config('security.idle_grace_seconds', 60);

        if ($idleMinutes <= 0) {
            return $next($request);
        }

        $allowedSeconds = ($idleMinutes * 60) + max(0, $graceSeconds);
        $lastActivity = $request->session()->get('last_activity_at');

        if ($lastActivity !== null && (time() - (int) $lastActivity) > $allowedSeconds) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => __('Your session expired due to inactivity.'),
                ], 401);
            }

            return redirect()->route('login')
                ->with('status', __('You were signed out due to inactivity.'));
        }

        $request->session()->put('last_activity_at', time());

        return $next($request);
    }
}
