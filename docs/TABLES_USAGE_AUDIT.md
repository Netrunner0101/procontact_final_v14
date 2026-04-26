# Audit d'utilisation des tables

**Date :** 2026-04-26
**Branche :** `claude/audit-database-tables-aQnBK`

Vérification systématique : pour chaque table créée par les migrations, est-ce que le code applicatif la lit ou l'écrit réellement ?

## Méthode

Recherche dans `app/`, `routes/`, `database/seeders/`, `config/`, `.env.example` pour :

1. Le modèle Eloquent (PascalCase singulier) — `::create`, `::insert`, `::query`, `::find`, `::where`, `new ModelName`, `->save()`, `use App\Models\X`
2. Les requêtes directes : `DB::table('xxx')`
3. Les relations Eloquent pointant dessus : `belongsTo`, `hasMany`, `hasOne`, `belongsToMany`
4. Les opérations pivot : `attach`, `detach`, `sync`, `syncWithoutDetaching`
5. Pour les tables techniques Laravel : la configuration de driver actif (`config/cache.php`, `queue.php`, `session.php`)

## Résultats — 24 tables

### Tables métier (17)

| Table | Statut | Preuve |
|-------|--------|--------|
| `activites` | ✅ Active | `ActiviteController.php` `Activite::create`, `::where` ; `Livewire\StatisticsDashboard` |
| `client_portal_access_log` | ✅ Active | `Services\PortalAuthService` `ClientPortalAccessLog::create / ::where / ::delete` |
| `client_portal_otps` | ✅ Active | `Services\PortalAuthService` `ClientPortalOtp::create / ::where / ::delete` |
| `client_portal_tokens` | ✅ Active | `Services\PortalAuthService` `ClientPortalToken::create / ::where` |
| `client_portal_trusted_devices` | ✅ Active | `Services\PortalAuthService` `ClientPortalTrustedDevice::create / ::where / ::delete` |
| `contact_activite` | ✅ Active | `Models\Contact::activites()` belongsToMany ; `ActiviteController` `syncWithoutDetaching`, `detach` |
| `contacts` | ✅ Active | `ContactController` (CRUD complet) ; `Livewire\ContactManager` |
| `emails` | ✅ Active | Relation `Contact::emails()` HasMany ; `ContactService` create/delete |
| `note_templates` | ✅ Active | `PortalController` `NoteTemplate::where`, `::create` (PR #55) |
| `notes` | ✅ Active | `Livewire\NotesManager` `Note::create / ::where / ::findOrFail` |
| `numero_telephones` | ✅ Active | Relation `Contact::numeroTelephones()` ; `ContactService` |
| `rappels` | ✅ Active | `RappelController` `Rappel::create` ; `SendAppointmentEmail` job |
| `rendez_vous` | ✅ Active | `Livewire\AppointmentManager` `RendezVous::create / ::where` ; multiples controllers |
| `roles` | ✅ Active | `Auth\AuthController` `Role::where` ; `ClientManagementController` |
| `statuses` | ✅ Active | `Livewire\ContactManager` `Status::all()` ; relation Contact |
| `users` | ✅ Active | `Auth\AuthController` `User::create / ::where` ; partout |
| **`statistiques`** | ❌ **Morte** | Modèle importé dans `StatistiqueController` mais **jamais instancié**. Aucun `Statistique::create / ::insert / new Statistique` dans tout le code. Les agrégats sont calculés à la volée par `selectRaw` sur les autres tables. |

### Tables techniques Laravel (7)

| Table | Statut | Preuve |
|-------|--------|--------|
| `migrations` | ✅ Active | Gérée par `php artisan migrate` |
| `sessions` | ✅ Active | `config/session.php` driver=`database` ; `SESSION_DRIVER=database` |
| `cache` | ✅ Active | `config/cache.php` store=`database` ; `Cache::remember` dans `StatistiqueController`, `ContactManager` |
| `cache_locks` | ✅ Active | Table de verrous pour le store `database` |
| `jobs` | ✅ Active | `config/queue.php` driver=`database` ; `SendAppointmentEmail`, `PortalOtpMail` mis en file |
| `job_batches` | ✅ Active | Table de batches pour le queue driver `database` |
| `failed_jobs` | ✅ Active | Table d'échecs pour le queue driver `database` |
| **`password_reset_tokens`** | ⚠️ **Inerte** | Créée par défaut Laravel mais **jamais lue/écrite par ce projet**. `AuthController::initiatePasswordReset()` stocke le jeton sur `users.password_reset_token` + `users.password_reset_expires` ; `AuthController::resetPassword()` les relit. `Password::broker()` n'est jamais invoqué donc cette table reste vide. |

## Synthèse

| Catégorie | Nombre |
|-----------|:------:|
| Tables métier actives | 16 |
| Tables métier mortes | **1** (`statistiques`) |
| Tables techniques actives | 7 |
| Tables techniques inertes | **1** (`password_reset_tokens`) |
| **Total** | **24 → 22 utiles** |

## Recommandations

1. **Supprimer `statistiques`** dans une PR séparée :
   - Nouvelle migration `drop_statistiques_table.php`
   - Supprimer `app/Models/Statistique.php`
   - Retirer `hasMany(Statistique::class)` dans `Activite`, `RendezVous`, `Contact`
   - Retirer le `use App\Models\Statistique` inutilisé dans `StatistiqueController`
   - Aucun impact fonctionnel — toutes les statistiques sont déjà calculées à la volée.

2. **Pour `password_reset_tokens`** — deux options :
   - **Option A (préservation)** : laisser telle quelle. Conforme à l'installation Laravel par défaut, ne consomme rien tant qu'aucune ligne n'y est insérée.
   - **Option B (cohérence)** : supprimer via une migration et basculer sur le système standard `Password::sendResetLink()` / `Password::reset()`. Plus de surface, mais aligne le projet sur la pratique Laravel.

3. **Conserver tout le reste** — chaque autre table est référencée au moins une fois en lecture ou en écriture par le code applicatif.
