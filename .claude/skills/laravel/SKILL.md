---
name: laravel
description: Project-aware guidance for working in this Laravel 12 + Livewire 3 codebase (ProContact). Use when adding routes, controllers, Eloquent models, migrations, Livewire components, Blade views, jobs, mailables, policies, or running artisan/test/format commands. Covers French domain naming conventions, multi-tenant scoping by user_id, and the project's Pint/PHPUnit workflow.
---

# Laravel skill for ProContact

This is a Laravel 12 application (PHP 8.2) using Livewire 3, Sanctum, Socialite,
and PostgreSQL. The domain language is French — model and column names mix
French (`Contact`, `RendezVous`, `Activite`, `NumeroTelephone`, `nom`, `prenom`,
`rue`, `code_postal`) with English Laravel conventions. Follow the existing
naming when extending the schema; do not anglicize French identifiers.

## Stack reference

- Framework: `laravel/framework` ^12.0
- UI: `livewire/livewire` ^3.6 + Blade
- Auth: `laravel/sanctum`, `laravel/socialite` (Google + mock OAuth in dev)
- DB: PostgreSQL in production, SQLite in `.env.testing`
- Format: `laravel/pint` (run before committing)
- Tests: `phpunit/phpunit` ^11.5 via `php artisan test`

## Multi-tenancy invariant

Most domain tables (`contacts`, `notes`, `rappels`, `emails`,
`numero_telephones`, `rendez_vous`) carry a `user_id` foreign key scoping rows
to the authenticated admin. **Always filter by `Auth::id()`** in queries that
expose data to a user, and add `user_id` to `$fillable` plus the migration when
introducing new tenant-scoped tables. See `MD/milestone3_multitenant_isolation_check.md`
for the audit baseline.

```php
// Correct: scope to authenticated user
Contact::where('user_id', Auth::id())->get();

// Wrong: returns every tenant's contacts
Contact::all();
```

## Conventions

- **Models** live in `app/Models/`. Use typed relation return types
  (`BelongsTo`, `HasMany`, `BelongsToMany`) — see `app/Models/Contact.php`.
- **Controllers** in `app/Http/Controllers/`. Resourceful where it fits;
  thin controllers, business logic in services (`app/Services/`) or models.
- **Livewire** components in `app/Livewire/`, views in
  `resources/views/livewire/` (and a few top-level `*-manager.blade.php`
  wrappers). Use `WithPagination` for lists; cache expensive lookups with
  `Cache::remember` keyed by `Auth::id()`.
- **Routes** are split: `routes/web.php`, `routes/auth.php`, `routes/console.php`.
- **Migrations** use timestamp prefixes; add new ones via
  `php artisan make:migration` rather than editing existing files.
- **Translations** live in `lang/en.json` and `lang/fr.json`. Add keys to both
  when introducing user-facing strings.
- **Status & Role** are normalized into `statuses` and `roles` tables — do not
  hardcode magic IDs; resolve via `Status::where('nom', ...)` or `Role` enum-like
  lookups.

## Common artisan commands

```bash
# Dev loop (server + queue + pail logs + vite)
composer dev

# Tests (clears config first)
composer test
# or a targeted run
php artisan test --filter=ContactTest

# Format (run before commit)
./vendor/bin/pint

# Generate scaffolding
php artisan make:model Foo -mfc       # model + migration + factory + controller
php artisan make:livewire FooManager
php artisan make:migration add_bar_to_foos_table

# DB
php artisan migrate
php artisan migrate:fresh --seed --env=testing
```

## When adding a new feature

1. Migration first — include `user_id` FK with `cascadeOnDelete()` if the data
   is tenant-scoped.
2. Model — declare `$fillable`, relations, and any cast (`'date' => 'datetime'`).
3. Route — pick the right file (`web.php` vs `auth.php`) and apply
   `auth` middleware for protected pages.
4. Controller or Livewire component — scope every query by `Auth::id()`.
5. Blade view — extend `layouts/app.blade.php`; use `@lang()` / `__()` for any
   user-visible string and add the key to both `lang/en.json` and `lang/fr.json`.
6. Test — add a feature test under `tests/Feature/` that asserts the
   tenant-scoping invariant (user A cannot see user B's rows).
7. Run `./vendor/bin/pint` and `php artisan test` before committing.

## Gotchas in this codebase

- `Contact::isClient()` is derived from "has at least one rendez-vous",
  not from a stored column — do not add a denormalized flag without updating
  every read site.
- Phone numbers are stored separately in `numero_telephones` (one-to-many),
  not as a column on `contacts`. New phone metadata goes there.
- The "Contact / Client" filter in `ContactManager` is computed in PHP after
  the query — beware of N+1 if you change the query shape; eager-load
  `rendezVous` when filtering by client status.
- Social auth has a `MockOAuthController` for local development so contributors
  without real Google credentials can still log in; do not remove it.
- `config/app.php` defaults `APP_LOCALE` to `en`; the user-facing app is
  primarily French, so the runtime `.env` should set `APP_LOCALE=fr`.

## Reference docs in repo

- `MD/DATABASE_SCHEMA_COMPLETE.md` — full schema with relationships
- `MD/milestone3_MCD_MLDR_ClassDiagram.md` — conceptual + logical model
- `MD/milestone3_multitenant_isolation_check.md` — tenant isolation audit
- `DESIGN_SYSTEM.md` — UI tokens for Blade views
- `TESTING_REPORT.md` — known test coverage and gaps
- `GOOGLE_OAUTH_SETUP.md` — Socialite Google provider setup
