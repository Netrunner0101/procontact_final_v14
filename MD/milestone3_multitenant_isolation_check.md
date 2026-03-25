# Milestone 3 — Multi-Tenant Data Isolation & Role Verification

**Projet:** Pro Contact
**Auteur:** Lung Sze Ho Eric
**Date:** Mars 2026
**Etablissement:** Ifosupwavre, Juin 2025
**Architecture:** Single database, data partitioned by `user_id`
**Stack:** Laravel / PostgreSQL

---

## 1. Role System Analysis

### 1.1 Roles Table (NEW — replaces enum)

Roles are now stored in a dedicated `roles` table instead of an enum column.

**Migration:** `2026_03_15_000001_create_roles_table.php`

| id | nom | description |
|----|-----|-------------|
| 1 | `admin` | Entrepreneur/Independant — acces complet |
| 2 | `client` | Client — consultation des rendez-vous uniquement |

**Migration:** `2026_03_15_000002_update_users_role_system.php`
- Added `role_id` FK → `roles.id` (NOT NULL) — replaces old enum `role` column
- Added `contact_id` FK → `contacts.id` (nullable) — direct link between client user and their contact record
- Old `role` enum column dropped
- Existing data migrated automatically

### 1.2 Role Model (`app/Models/Role.php`)

```php
class Role extends Model
{
    const ADMIN = 'admin';
    const CLIENT = 'client';
}
```

### 1.3 Role Helper Methods (`app/Models/User.php`)

| Method | Logic | Used By |
|--------|-------|---------|
| `isAdmin()` | `$this->role->nom === Role::ADMIN` | AdminMiddleware, visibleRendezVous() |
| `isClient()` | `$this->role->nom === Role::CLIENT` | ClientMiddleware, visibleRendezVous() |
| `visibleRendezVous()` | Admin: own rendezVous. Client: appointments for their `contact_id`, scoped by `admin_user_id` | ClientController |

### 1.4 Admin-Client Relationship

```
User (admin) ──hasMany──► User (client) via admin_user_id
User (client) ──belongsTo──► User (admin) via admin_user_id
User (client) ──belongsTo──► Contact via contact_id (NEW — direct FK link)
```

- An admin creates client accounts via `ClientManagementController::store()`
- Client is assigned `role_id` (client role), `admin_user_id`, and `contact_id` after creation
- `role_id`, `admin_user_id`, `contact_id` are NOT in `$fillable` — set explicitly to prevent mass assignment attacks
- All ClientManagementController actions verify `$client->admin_user_id !== Auth::id()` before proceeding
- Contact ownership is verified: `Contact::where('user_id', Auth::id())->findOrFail()`

### 1.5 Mass Assignment Protection

`role_id`, `admin_user_id`, and `contact_id` are **removed from `$fillable`** to prevent users from setting their own role via form submission. These fields are set explicitly in code after `User::create()`.

---

## 2. Route Protection & Middleware

### 2.1 Middleware

| Middleware | File | Behavior |
|-----------|------|----------|
| `admin` | `app/Http/Middleware/AdminMiddleware.php` | Returns 403 if `!isAdmin()` |
| `client` | `app/Http/Middleware/ClientMiddleware.php` | Redirects to dashboard if `!isClient()` |
| `auth` | Laravel built-in | Ensures user is authenticated |

### 2.2 Route Groups

| Route Group | Middleware | Access |
|-------------|-----------|--------|
| `/profile/*` | `auth` | All authenticated users |
| `/dashboard`, `/contacts-manager`, `/notes-manager`, etc. | `auth, admin` | Admin only |
| Resource routes (contacts, activites, rendez-vous, etc.) | `auth, admin` | Admin only |
| `/admin/clients/*` | `auth, admin` | Admin only |
| `/client/*` | `auth, client` | Client only |

### 2.3 Verification Result

| Check | Status |
|-------|--------|
| Admin routes protected by `admin` middleware | ✅ Passed |
| Client routes protected by `client` middleware | ✅ Passed |
| Client cannot access admin routes | ✅ Passed (403) |
| Admin cannot access client routes | ✅ Passed (redirect) |
| Unauthenticated users redirected to login | ✅ Passed |

---

## 3. Data Isolation Analysis (Per Table)

### 3.1 Core Principle

Each `admin` user is identified by `users.id`. Every piece of data must be **directly or indirectly linked** to this `user_id`.

```
users (id) ──► contacts (user_id)
           ──► activites (user_id)
           ──► rendez_vous (user_id)
           ──► notes (user_id)        [added via migration]
           ──► rappels (user_id)      [added via migration]
```

### 3.2 Table: `users`

| Column | Constraint | Status |
|--------|-----------|--------|
| `id` | PK, auto-increment | ✅ |
| `email` | Unique | ✅ |
| `role_id` | FK → roles.id, NOT NULL | ✅ (was enum, now FK) |
| `admin_user_id` | Nullable FK → users.id | ✅ |
| `contact_id` | Nullable FK → contacts.id | ✅ (NEW) |

### 3.2b Table: `roles` (NEW)

| Column | Constraint | Status |
|--------|-----------|--------|
| `id` | PK | ✅ |
| `nom` | Unique string | ✅ |
| `description` | Nullable string | ✅ |

### 3.3 Table: `contacts`

| Column | Constraint | Status |
|--------|-----------|--------|
| `user_id` | FK → users.id, NOT NULL, CASCADE DELETE | ✅ |
| `status_id` | FK → statuses.id | ✅ |
| `state_client` | varchar (contact/client) | ✅ |

**Isolation in code:**
- `ContactController::index()` → `Contact::where('user_id', Auth::id())` ✅
- `ContactManager` Livewire → `Contact::where('user_id', Auth::id())` ✅
- `ContactPolicy::view()` → `$user->id === $contact->user_id` ✅

### 3.4 Table: `activites`

| Column | Constraint | Status |
|--------|-----------|--------|
| `user_id` | FK → users.id, NOT NULL, CASCADE DELETE | ✅ |

**Isolation in code:**
- `ActiviteController::index()` → `Activite::where('user_id', Auth::id())` ✅
- `ActivitePolicy::view()` → `$user->id === $activite->user_id` ✅

### 3.5 Pivot Table: `contact_activite`

| Column | Constraint | Status |
|--------|-----------|--------|
| `contact_id` | FK → contacts.id | ✅ |
| `activite_id` | FK → activites.id | ✅ |

**Cross-tenant risk: HIGH**

This pivot table has **no direct `user_id`**. Isolation relies on both the contact and activity belonging to the same user.

**SQL verification required:**
```sql
SELECT ca.contact_id, ca.activite_id,
       c.user_id AS contact_owner,
       a.user_id AS activite_owner
FROM contact_activite ca
JOIN contacts c ON ca.contact_id = c.id
JOIN activites a ON ca.activite_id = a.id
WHERE c.user_id != a.user_id;
-- Expected: 0 rows
```

**Code check:** `ActiviteController::attachContact()` should verify both entities belong to Auth::id() before linking. ⚠️ **Requires manual verification.**

### 3.6 Table: `rendez_vous`

| Column | Constraint | Status |
|--------|-----------|--------|
| `user_id` | FK → users.id, NOT NULL, CASCADE DELETE | ✅ |
| `contact_id` | FK → contacts.id | ✅ |
| `activite_id` | FK → activites.id | ✅ |

**Isolation in code:**
- `RendezVousController` → `RendezVous::where('user_id', Auth::id())` ✅
- `RendezVousPolicy::view()` → `$user->id === $rendezVous->user_id` ✅

**Cross-tenant risk:** A rendez_vous could reference a contact/activite from another user if not validated at creation.

### 3.7 Table: `notes`

| Column | Constraint | Status |
|--------|-----------|--------|
| `user_id` | FK → users.id, CASCADE DELETE (added in migration `2025_07_19_201921`) | ✅ |
| `rendez_vous_id` | FK → rendez_vous.id | ✅ |
| `activite_id` | FK → activites.id | ✅ |

**Isolation in code:**
- `NoteController::index()` → `Note::where('user_id', Auth::id())` ✅
- `NotePolicy::view()` → checks via `rendezVous->user_id` or `activite->user_id` ✅

### 3.8 Table: `rappels`

| Column | Constraint | Status |
|--------|-----------|--------|
| `user_id` | FK → users.id, CASCADE DELETE (added in migration `2025_07_19_202104`) | ✅ |
| `rendez_vous_id` | FK → rendez_vous.id | ✅ |

**Isolation in code:**
- `RappelController` → filters via `whereHas('rendezVous', fn($q) => $q->where('user_id', Auth::id()))` ✅
- `RappelPolicy::view()` → `$user->id === $rappel->rendezVous->user_id` ✅

### 3.9 Table: `emails`

| Column | Constraint | Status |
|--------|-----------|--------|
| `contact_id` | FK → contacts.id, CASCADE DELETE | ✅ |

**No direct `user_id`** — isolation is **indirect** via `email → contact → user`. This is acceptable since emails are always accessed through their parent contact.

### 3.10 Table: `numero_telephones`

| Column | Constraint | Status |
|--------|-----------|--------|
| `contact_id` | FK → contacts.id, CASCADE DELETE | ✅ |

Same indirect isolation as `emails`. ✅

### 3.11 Table: `statuses`

| Column | Constraint | Status |
|--------|-----------|--------|
| `id` | PK | ✅ |
| `status_client` | varchar | ✅ |

**Shared reference table** — no `user_id` needed. All users share the same status values. ✅

---

## 4. Isolation Summary

| Table | Direct user_id | Indirect via FK | Controller Filter | Policy Check | Cross-Tenant Risk |
|-------|:-:|:-:|:-:|:-:|---|
| `users` | — (is the user) | — | — | — | — |
| `contacts` | ✅ | — | ✅ | ✅ | Low |
| `activites` | ✅ | — | ✅ | ✅ | Low |
| `contact_activite` | — | ✅ via contacts + activites | ⚠️ Verify attach | — | **High** |
| `rendez_vous` | ✅ | — | ✅ | ✅ | Medium (FK refs) |
| `notes` | ✅ | ✅ via rendez_vous | ✅ | ✅ | Low |
| `rappels` | ✅ | ✅ via rendez_vous | ✅ | ✅ | Low |
| `emails` | — | ✅ via contacts | ✅ (via contact) | — | Low |
| `numero_telephones` | — | ✅ via contacts | ✅ (via contact) | — | Low |
| `statuses` | — (shared) | — | — | — | None |

---

## 5. Laravel Isolation Mechanisms

### 5.1 No Global Scopes

The application does **NOT** use Eloquent Global Scopes for tenant isolation. Instead, every controller and Livewire component manually applies `where('user_id', Auth::id())`.

**Recommendation:** Consider adding a `UserScope` global scope to `Contact`, `Activite`, `RendezVous`, `Note`, and `Rappel` models to prevent accidental data leakage if a developer forgets the filter.

```php
// Example: app/Models/Scopes/UserScope.php
protected static function booted()
{
    static::addGlobalScope('user', function (Builder $builder) {
        $builder->where('user_id', auth()->id());
    });
}
```

### 5.2 Authorization Policies

All five policies correctly enforce ownership:

| Policy | File | Ownership Check |
|--------|------|----------------|
| ContactPolicy | `app/Policies/ContactPolicy.php` | `$user->id === $contact->user_id` |
| ActivitePolicy | `app/Policies/ActivitePolicy.php` | `$user->id === $activite->user_id` |
| RendezVousPolicy | `app/Policies/RendezVousPolicy.php` | `$user->id === $rendezVous->user_id` |
| NotePolicy | `app/Policies/NotePolicy.php` | Via `rendezVous->user_id` or `activite->user_id` |
| RappelPolicy | `app/Policies/RappelPolicy.php` | Via `rendezVous->user_id` |

### 5.3 Cascade Delete on Foreign Keys

All `user_id` foreign keys use `onDelete('cascade')`, ensuring that when a user is deleted, all their data is removed. ✅

---

## 6. Client Role — Specific Checks

### 6.1 Client Data Access

| Action | Controller | Method | Isolation |
|--------|-----------|--------|-----------|
| View dashboard | ClientController | `dashboard()` | `visibleRendezVous()` ✅ |
| List appointments | ClientController | `appointments()` | `visibleRendezVous()` ✅ |
| View single appointment | ClientController | `showAppointment()` | `visibleRendezVous()->where('id', ...)->exists()` ✅ |

### 6.2 Client Visibility Logic (FIXED)

```php
// User.php — after fix
public function visibleRendezVous() {
    if ($this->isAdmin()) {
        return $this->rendezVous(); // Own appointments
    }
    // Client: only appointments linked to their contact, owned by their admin
    return RendezVous::where('user_id', $this->admin_user_id)
        ->where('contact_id', $this->contact_id);
}
```

**Fix applied:** Client visibility is now scoped by both `admin_user_id` (only their admin's data) and `contact_id` (only their contact record). No more cross-admin data leakage. ✅

### 6.3 Admin Management of Clients

| Action | Method | Ownership Check |
|--------|--------|----------------|
| List clients | `index()` | `Auth::user()->clients()` ✅ |
| Create client | `store()` | Sets `role_id`, `admin_user_id`, `contact_id` after create ✅ |
| View client | `show()` | `$client->admin_user_id !== Auth::id()` ✅ |
| Edit client | `edit()` | `$client->admin_user_id !== Auth::id()` ✅ |
| Update client | `update()` | `$client->admin_user_id !== Auth::id()` ✅ |
| Delete client | `destroy()` | `$client->admin_user_id !== Auth::id()` ✅ |

**Contact ownership (FIXED):** `store()` now uses `Contact::where('user_id', Auth::id())->findOrFail()` to verify the contact belongs to the current admin. ✅

---

## 7. Fixes Applied (2026-03-15)

### 7.1 Issues Found & Resolution

| # | Severity | Issue | Status |
|---|----------|-------|--------|
| 1 | **High** | Role stored as enum string — no proper roles table | ✅ **FIXED** — Created `roles` table with FK |
| 2 | **High** | `role` and `admin_user_id` in `$fillable` — mass assignment risk | ✅ **FIXED** — Removed from `$fillable` |
| 3 | **High** | No direct FK between client user and contact record | ✅ **FIXED** — Added `contact_id` FK on `users` |
| 4 | **Medium** | `ClientManagementController::store()` no contact ownership check | ✅ **FIXED** — Uses `Contact::where('user_id', Auth::id())->findOrFail()` |
| 5 | **Medium** | `visibleRendezVous()` — client sees across all admins | ✅ **FIXED** — Scoped by `admin_user_id` + `contact_id` |
| 6 | **Medium** | `visibleRendezVous()` queries non-existent `contacts.email` column | ✅ **FIXED** — Now uses `contact_id` FK |
| 7 | **Low** | `contact_activite` pivot has no user_id constraint | ⚠️ **Open** — Verify `attachContact()` |
| 8 | **Low** | No Global Scopes — relies on manual filtering | ⚠️ **Open** — Consider adding for defense-in-depth |
| 9 | **Low** | Livewire `ContactManager::openEditModal()` — no explicit auth check | ⚠️ **Open** |

### 7.2 Files Modified

| File | Change |
|------|--------|
| `app/Models/Role.php` | **NEW** — Role model with constants |
| `database/migrations/2026_03_15_000001_create_roles_table.php` | **NEW** — roles table + seed data |
| `database/migrations/2026_03_15_000002_update_users_role_system.php` | **NEW** — role_id, contact_id on users, drop enum |
| `app/Models/User.php` | Removed `role`/`admin_user_id` from fillable, added `role()` and `contact()` relationships, fixed `isAdmin()`/`isClient()`/`visibleRendezVous()` |
| `app/Http/Controllers/ClientManagementController.php` | Added contact ownership check, role assignment after create, contact_id support |
| `app/Http/Controllers/SocialAuthController.php` | Set role_id after user creation instead of in create array |
| `app/Http/Controllers/Auth/AuthController.php` | Set role_id explicitly after registration |

---

## 8. SQL Verification Queries

Run these against the database to verify data integrity:

```sql
-- 1. Contacts without user_id (should be 0)
SELECT id, user_id FROM contacts WHERE user_id IS NULL;

-- 2. Activities without user_id (should be 0)
SELECT id, user_id FROM activites WHERE user_id IS NULL;

-- 3. Cross-tenant links in contact_activite (should be 0)
SELECT ca.contact_id, ca.activite_id,
       c.user_id AS contact_owner,
       a.user_id AS activite_owner
FROM contact_activite ca
JOIN contacts c ON ca.contact_id = c.id
JOIN activites a ON ca.activite_id = a.id
WHERE c.user_id != a.user_id;

-- 4. Cross-tenant links in rendez_vous (should be 0)
SELECT rdv.id, c.user_id AS contact_owner, a.user_id AS activite_owner
FROM rendez_vous rdv
JOIN contacts c ON rdv.contact_id = c.id
JOIN activites a ON rdv.activite_id = a.id
WHERE c.user_id != a.user_id;

-- 5. Data distribution per user
SELECT user_id, COUNT(*) as nb_contacts FROM contacts GROUP BY user_id;
SELECT user_id, COUNT(*) as nb_activites FROM activites GROUP BY user_id;
SELECT user_id, COUNT(*) as nb_rdv FROM rendez_vous GROUP BY user_id;

-- 6. Client-admin relationships (updated for roles table)
SELECT u.id, u.email, r.nom AS role, u.admin_user_id, u.contact_id
FROM users u
JOIN roles r ON u.role_id = r.id
ORDER BY r.nom, u.admin_user_id;
```

---

*Verification date: 2026-03-15*
*Stack: Laravel / PostgreSQL*
*Based on: procontact_ActorPrimarySecondary_multitenant_check.md*
