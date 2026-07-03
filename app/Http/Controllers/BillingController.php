<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    /**
     * Show the subscription / billing status page.
     */
    public function index(Request $request)
    {
        return view('billing.index', [
            'user' => $request->user(),
            'priceConfigured' => filled(config('billing.price')),
            'trialDays' => config('billing.trial_days', 7),
        ]);
    }

    /**
     * Start (or restart) the €19/month subscription via Stripe Checkout.
     * New subscribers get a Stripe-managed free trial first.
     */
    public function checkout(Request $request)
    {
        $user = $request->user();

        abort_unless(filled(config('billing.price')), 500, 'Stripe price is not configured.');

        if ($user->subscribed('default')) {
            return redirect()->route('billing.index');
        }

        $subscription = $user->newSubscription('default', config('billing.price'));

        // Only offer the free trial to people who have never subscribed before,
        // so a returning customer cannot farm repeated free weeks.
        $hasSubscribedBefore = $user->subscriptions()->where('type', 'default')->exists();

        if (! $hasSubscribedBefore) {
            $subscription->trialDays(config('billing.trial_days', 7));
        }

        return $subscription->checkout([
            'success_url' => route('billing.index').'?checkout=success',
            'cancel_url' => route('billing.index').'?checkout=cancelled',
        ]);
    }

    /**
     * Redirect to the Stripe Billing Portal where the user can update their
     * card, download invoices and cancel at any time.
     */
    public function portal(Request $request): RedirectResponse
    {
        return $request->user()->redirectToBillingPortal(route('billing.index'));
    }

    /**
     * Resume a subscription that was cancelled but is still in its grace period.
     */
    public function resume(Request $request): RedirectResponse
    {
        $subscription = $request->user()->subscription('default');

        if ($subscription && $subscription->onGracePeriod()) {
            $subscription->resume();

            return redirect()->route('billing.index')
                ->with('success', __('Your subscription has been resumed.'));
        }

        return redirect()->route('billing.index');
    }
}
