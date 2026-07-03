<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    /**
     * Require an active subscription (or Stripe trial) before granting access
     * to the paid professional area.
     *
     * Enforcement is opt-in via config('billing.enforce'): while it is off
     * (the default), every authenticated user passes through, so local
     * development, the test suite and pre-billing accounts are never blocked.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('billing.enforce')) {
            return $next($request);
        }

        $user = $request->user();

        // A "trialing" subscription still counts as subscribed in Cashier, so
        // this covers both the 1-week free trial and paid/grace-period access.
        if ($user && $user->subscribed('default')) {
            return $next($request);
        }

        return redirect()->route('billing.index')
            ->with('error', __('Please start your subscription to access ProContact.'));
    }
}
