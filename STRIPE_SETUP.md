# Stripe billing setup (ProContact Pro — €19/month)

ProContact uses [Laravel Cashier](https://laravel.com/docs/billing) to handle
the **€19/month** subscription with a **1-week free trial** and **cancel
anytime**. This document lists exactly what to configure on Stripe and in your
environment to switch billing on.

## 1. What was added to the code

- `laravel/cashier` dependency + the `Billable` trait on `App\Models\User`.
- Cashier migrations (Stripe columns on `users`, `subscriptions` and
  `subscription_items` tables).
- `config/billing.php` — enforcement flag, Stripe price ID, trial length.
- `App\Http\Middleware\EnsureSubscribed` (alias `subscribed`) guarding the
  professional area. **Off by default** (`BILLING_ENFORCE=false`).
- `App\Http\Controllers\BillingController` + routes under `/billing`
  (`index`, `checkout`, `portal`, `resume`).
- Billing page at `resources/views/billing/index.blade.php`.
- Stripe webhook route auto-registered by Cashier at `POST /stripe/webhook`
  (CSRF-exempted in `bootstrap/app.php`).

## 2. Server requirement

Cashier needs the PHP **bcmath** extension. Make sure it is installed in
production:

```bash
# Debian/Ubuntu example
sudo apt-get install php8.2-bcmath
```

## 3. What you configure in the Stripe dashboard

1. **Get your API keys** — <https://dashboard.stripe.com/apikeys>
   - Publishable key → `STRIPE_KEY`
   - Secret key → `STRIPE_SECRET`
2. **Create the product & price** — Products → *Add product*
   - Name: `ProContact Pro`
   - Pricing: **Recurring**, **€19.00**, billing period **Monthly**, currency **EUR**
   - Save, then copy the **Price ID** (looks like `price_1AbC...`) → `STRIPE_PRICE_PRO`
   - The 1-week free trial is applied automatically by the app at checkout — you
     do **not** need to set a trial on the price itself.
3. **Enable the Billing Customer Portal** — <https://dashboard.stripe.com/settings/billing/portal>
   - Turn it on and allow customers to **cancel subscriptions** and **update
     payment methods**. This is what powers the "Manage billing" / cancel-anytime
     button.
4. **Create a webhook endpoint** — <https://dashboard.stripe.com/webhooks>
   - URL: `https://YOUR_DOMAIN/stripe/webhook`
   - Listen to (at minimum) these events:
     `customer.subscription.created`, `customer.subscription.updated`,
     `customer.subscription.deleted`, `invoice.payment_succeeded`,
     `invoice.payment_action_required`, `customer.updated`, `customer.deleted`
   - Copy the **Signing secret** (`whsec_...`) → `STRIPE_WEBHOOK_SECRET`

## 4. Environment variables

Add these to your production `.env` (see `.env.example`):

```dotenv
STRIPE_KEY=pk_live_xxx
STRIPE_SECRET=sk_live_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
STRIPE_PRICE_PRO=price_xxx
CASHIER_CURRENCY=eur
BILLING_TRIAL_DAYS=7

# Flip this to true when you are ready to require payment to use the app
BILLING_ENFORCE=true
```

## 5. Run the migrations

```bash
php artisan migrate
```

## 6. How it behaves

- With `BILLING_ENFORCE=false` (default): nothing changes — everyone keeps full
  access. Good for development and for existing users during rollout.
- With `BILLING_ENFORCE=true`: a professional without an active subscription is
  redirected to `/billing`, where **"Start your 1-week free trial"** sends them
  to Stripe Checkout. After the trial Stripe charges €19/month. They can cancel
  anytime from the Stripe portal ("Manage billing"); access continues until the
  end of the paid period, and can be resumed during that grace period.

## 7. Test in Stripe test mode first

Use your `sk_test_...` / `pk_test_...` keys and Stripe's test card
`4242 4242 4242 4242` (any future date / any CVC) to run through checkout before
going live.
