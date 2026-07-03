<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Billing enforcement
    |--------------------------------------------------------------------------
    |
    | When enabled, professionals (admins) must have an active subscription or
    | be in their Stripe trial period to access the app. Kept OFF by default so
    | local development, tests and existing accounts are never locked out. Set
    | BILLING_ENFORCE=true in production once Stripe is configured.
    |
    */

    'enforce' => env('BILLING_ENFORCE', false),

    /*
    |--------------------------------------------------------------------------
    | Pro plan Stripe price
    |--------------------------------------------------------------------------
    |
    | The Stripe Price ID (starts with "price_") for the €19/month Pro plan.
    | Create a recurring monthly price in the Stripe dashboard and paste its ID
    | here via the STRIPE_PRICE_PRO environment variable.
    |
    */

    'price' => env('STRIPE_PRICE_PRO'),

    /*
    |--------------------------------------------------------------------------
    | Free trial length (days)
    |--------------------------------------------------------------------------
    */

    'trial_days' => (int) env('BILLING_TRIAL_DAYS', 7),
];
