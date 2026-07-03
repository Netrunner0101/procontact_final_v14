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

---

# Comment l'intégration a été construite (notes techniques)

Cette section décrit, étape par étape, comment l'abonnement Stripe a été ajouté
au projet — utile pour comprendre le code ou refaire la manipulation.

## a. Choix de la solution

[Laravel Cashier](https://laravel.com/docs/billing) a été retenu plutôt qu'une
intégration Stripe « à la main » : c'est le paquet officiel Laravel, il gère
nativement l'essai gratuit, la résiliation avec période de grâce, le portail
client et la vérification de signature des webhooks. Beaucoup moins de code à
écrire et à maintenir.

## b. Installation

```bash
composer require laravel/cashier
php artisan vendor:publish --tag="cashier-migrations"
```

> Note : Cashier exige l'extension PHP `bcmath`. Sur cet environnement de
> développement elle était absente, l'installation a donc été faite avec
> `--ignore-platform-req=ext-bcmath`. En production, `bcmath` **doit** être
> installée (voir §2).

## c. Modèle User « facturable »

Le trait `Laravel\Cashier\Billable` a été ajouté à `App\Models\User`, ce qui
apporte toutes les méthodes d'abonnement (`newSubscription()`, `subscribed()`,
`subscription()`, `redirectToBillingPortal()`…). Un cast `trial_ends_at` en
`datetime` a aussi été ajouté.

## d. Configuration dédiée (`config/billing.php`)

Trois réglages y sont centralisés, tous pilotés par des variables `.env` :

- `enforce` (`BILLING_ENFORCE`) — active ou non le blocage par abonnement ;
- `price` (`STRIPE_PRICE_PRO`) — l'ID du tarif Stripe à 19 €/mois ;
- `trial_days` (`BILLING_TRIAL_DAYS`) — durée de l'essai (7 jours).

## e. Le « portier » : middleware `EnsureSubscribed`

`app/Http/Middleware/EnsureSubscribed.php` (alias `subscribed`, déclaré dans
`bootstrap/app.php`) protège la zone professionnelle. Sa logique :

1. si `config('billing.enforce')` est `false` → laisse tout passer (c'est le cas
   par défaut, pour ne bloquer ni le dev, ni les tests, ni les comptes existants) ;
2. sinon, si l'utilisateur a un abonnement actif **ou** est en période d'essai
   (`$user->subscribed('default')` — dans Cashier, « trialing » compte comme
   abonné) → accès autorisé ;
3. sinon → redirection vers `/billing`.

Ce middleware a été appliqué au groupe de routes admin **sauf** aux routes
`/billing` elles-mêmes, pour éviter une boucle de redirection (on doit toujours
pouvoir atteindre la page pour s'abonner).

## f. Contrôleur et routes

`App\Http\Controllers\BillingController` expose quatre actions :

| Route | Rôle |
|-------|------|
| `GET /billing` | Affiche l'état de l'abonnement |
| `GET /billing/checkout` | Crée l'abonnement via **Stripe Checkout** (avec essai de 7 jours pour un nouveau client, jamais deux fois) |
| `GET /billing/portal` | Redirige vers le **portail Stripe** (carte, factures, résiliation) |
| `POST /billing/resume` | Réactive un abonnement résilié encore en période de grâce |

Le webhook (`POST /stripe/webhook`) est enregistré automatiquement par Cashier ;
il a fallu seulement l'exclure de la protection CSRF dans `bootstrap/app.php`
(`validateCsrfTokens(except: ['stripe/*'])`), car Stripe n'envoie pas de jeton.

## g. Interface

- `resources/views/billing/index.blade.php` : page d'état (essai / actif /
  résilié) avec les boutons adaptés au statut.
- Un lien « Abonnement » a été ajouté au menu utilisateur du layout de l'app
  (versions bureau et mobile).
- Toutes les chaînes sont traduites (`lang/fr.json`).

## h. Sécurité anti-abus

Le contrôleur ne propose l'essai gratuit qu'aux clients qui n'ont **jamais** eu
d'abonnement (`$user->subscriptions()->where('type','default')->exists()`), pour
qu'un client résilié ne puisse pas enchaîner des semaines gratuites.

## i. Tests

`tests/Feature/BillingTest.php` vérifie que : la page d'abonnement s'affiche ;
l'app reste ouverte quand l'enforcement est désactivé ; un admin non abonné est
redirigé vers `/billing` quand l'enforcement est activé ; et que la page de
facturation reste toujours accessible. Les 4 tests passent.
