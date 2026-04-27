# MLDR — AFTER update

**Pro Contact — Modèle Logique de Données Relationnel (état actuel)**

> `statistiques` est **conservée** comme table réservée (schéma stable, non alimentée à ce jour). `password_reset_tokens` est **conservée** mais inerte — le projet utilise une implémentation custom sur `users.password_reset_token / password_reset_expires` (voir `TABLES_USAGE_AUDIT.md`).

---

## Notation

- `PK` clé primaire — `FK` clé étrangère (préfixe `#`) — `UQ` unique
- `CASCADE` / `SET NULL` / `RESTRICT` = action ON DELETE
- 🆕 = ajouté · 🔁 = modifié · ❌ = supprimé

---

## Schéma relationnel (16 tables métier)

### Tables inchangées (rappel)

`ROLES`, `STATUSES`, `EMAILS`, `NUMERO_TELEPHONES`, `CONTACT_ACTIVITE` — voir `MLDR_BEFORE.md`.

### Tables modifiées

```
🔁 CONTACTS (
    id            : BIGINT UNSIGNED  PK,
    #user_id      : BIGINT UNSIGNED  NOT NULL  FK -> USERS(id) CASCADE,
    nom, prenom   : VARCHAR(255)     NOT NULL,
    rue, numero, ville,
    code_postal, pays : VARCHAR(255) NULL,
    state_client  : VARCHAR(255)     NULL,
    #status_id    : BIGINT UNSIGNED  NULL  FK -> STATUSES(id) SET NULL,
    -- ❌ portal_token (déplacé vers CLIENT_PORTAL_TOKENS, hashé)
    timestamps
)

🔁 RENDEZ_VOUS (
    id           : BIGINT UNSIGNED  PK,
    #user_id     : BIGINT UNSIGNED  NOT NULL  FK -> USERS(id) CASCADE,
    #contact_id  : BIGINT UNSIGNED  NOT NULL  FK -> CONTACTS(id) CASCADE,
    #activite_id : BIGINT UNSIGNED  NOT NULL  FK -> ACTIVITES(id) CASCADE,
    titre        : VARCHAR(255)     NOT NULL,
    description  : TEXT             NULL,
    date_debut, date_fin : DATE     NOT NULL,
    heure_debut, heure_fin : TIME   NOT NULL,
    🆕 statut    : VARCHAR(255)     NOT NULL DEFAULT 'scheduled',
    timestamps
)

🔁 RAPPELS (
    id              : BIGINT UNSIGNED  PK,
    #user_id        : BIGINT UNSIGNED  NOT NULL  FK -> USERS(id) CASCADE,
    #rendez_vous_id : BIGINT UNSIGNED  NOT NULL  FK -> RENDEZ_VOUS(id) CASCADE,
    date_rappel     : DATETIME         NOT NULL,
    frequence       : VARCHAR(255)     NOT NULL,
    🆕 destinataire : VARCHAR(255)     NOT NULL DEFAULT 'Les deux',
    🆕 emails_cc    : TEXT             NULL,
    timestamps
)
```

### Tables ajoutées 🆕

```
🆕 NOTE_TEMPLATES (
    id           : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    #user_id     : BIGINT UNSIGNED  NOT NULL  FK -> USERS(id) CASCADE,
    #contact_id  : BIGINT UNSIGNED  NULL      FK -> CONTACTS(id) CASCADE,
    titre        : VARCHAR(255)     NOT NULL,
    commentaire  : TEXT             NOT NULL,
    timestamps,
    INDEX (user_id, contact_id)
)

🆕 CLIENT_PORTAL_TOKENS (
    id            : UUID            PK,
    #contact_id   : BIGINT UNSIGNED NOT NULL  FK -> CONTACTS(id) CASCADE,
    token_hash    : VARCHAR(255)    NOT NULL UNIQUE,
    last_used_at  : TIMESTAMP       NULL,
    revoked_at    : TIMESTAMP       NULL,
    timestamps,
    INDEX (contact_id, revoked_at)
)

🆕 CLIENT_PORTAL_OTPS (
    id           : UUID             PK,
    #contact_id  : BIGINT UNSIGNED  NOT NULL  FK -> CONTACTS(id) CASCADE,
    code_hash    : VARCHAR(255)     NOT NULL,
    email_hash   : VARCHAR(64)      NOT NULL,
    attempts     : TINYINT UNSIGNED NOT NULL DEFAULT 0,
    expires_at   : TIMESTAMP        NOT NULL,
    consumed_at  : TIMESTAMP        NULL,
    ip_address   : VARCHAR(45)      NULL,
    timestamps,
    INDEX (contact_id, expires_at)
)

🆕 CLIENT_PORTAL_TRUSTED_DEVICES (
    id                     : UUID            PK,
    #contact_id            : BIGINT UNSIGNED NOT NULL  FK -> CONTACTS(id) CASCADE,
    cookie_hash            : VARCHAR(255)    NOT NULL UNIQUE,
    user_agent_hash        : VARCHAR(64)     NULL,
    ip_address_first_seen  : VARCHAR(45)     NULL,
    last_used_at           : TIMESTAMP       NOT NULL,
    expires_at             : TIMESTAMP       NOT NULL,
    revoked_at             : TIMESTAMP       NULL,
    timestamps,
    INDEX (contact_id, expires_at)
)

🆕 CLIENT_PORTAL_ACCESS_LOG (
    id               : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    #contact_id      : BIGINT UNSIGNED  NULL  FK -> CONTACTS(id) SET NULL,
    event            : VARCHAR(255)     NOT NULL,
    ip_address       : VARCHAR(45)      NULL,
    user_agent_hash  : VARCHAR(64)      NULL,
    metadata         : JSON             NULL,
    created_at       : TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX (contact_id, created_at),
    INDEX (event)
)
```

### Tables réservées 🟡

```
🟡 STATISTIQUES (
    id              : BIGINT UNSIGNED  PK,
    #activite_id    : BIGINT UNSIGNED  NOT NULL  FK -> ACTIVITES(id) CASCADE,
    #rendez_vous_id : BIGINT UNSIGNED  NOT NULL  FK -> RENDEZ_VOUS(id) CASCADE,
    #contact_id     : BIGINT UNSIGNED  NOT NULL  FK -> CONTACTS(id) CASCADE,
    timestamps
)

Statut : conservée mais non alimentée.
Les agrégats sont calculés à la volée par StatistiqueController via selectRaw
sur CONTACTS / ACTIVITES / RENDEZ_VOUS / RAPPELS / NOTES.
Schéma stable pour matérialisation future (snapshots historiques,
cache pré-calculé pour dashboards lourds).
```

### Tables inertes (Laravel framework) ⚠️

| Table | Statut | Justification |
|-------|--------|---------------|
| `password_reset_tokens` | **Inerte** (jamais lue/écrite) | Le projet a sa propre implémentation de reset : `AuthController::initiatePasswordReset()` écrit `users.password_reset_token` + `users.password_reset_expires`, `AuthController::resetPassword()` les lit. `Password::broker()` n'est jamais invoqué, donc cette table reste vide. **Conservée** (créée par défaut Laravel, sans coût tant qu'elle est vide). Voir `TABLES_USAGE_AUDIT.md` pour le diagramme de séquence du reset. |

> Toutes les autres tables techniques (`migrations`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`) restent activement utilisées par les drivers configurés en `database`.

## Diff récapitulatif

| Δ | Table | Détail |
|---|-------|--------|
| 🆕 | `note_templates` | Modèles de note réutilisables |
| 🆕 | `client_portal_tokens` | Session portail (UUID + hash) |
| 🆕 | `client_portal_otps` | Code OTP à usage unique |
| 🆕 | `client_portal_trusted_devices` | Cookie « se souvenir de moi » |
| 🆕 | `client_portal_access_log` | Journal RGPD des accès |
| 🔁 | `rendez_vous` | + `statut` (default `'scheduled'`) |
| 🔁 | `rappels` | + `destinataire`, + `emails_cc` |
| 🔁 | `contacts` | − `portal_token` (remplacé par jeton hashé) |
| 🟡 | `statistiques` | Conservée comme table réservée — non alimentée à ce jour (stats calculées à la volée) |
| ⚠️ | `password_reset_tokens` | Conservée (table Laravel par défaut) mais jamais lue/écrite — reset implémenté via `users.password_reset_token` |

**Compte des tables métier : 12 → 17** (+5 portail/templates ; `statistiques` conservée).

## Cascade des suppressions (mis à jour)

```
DELETE USER (admin) -> CONTACTS / ACTIVITES / RENDEZ_VOUS / NOTE_TEMPLATES / clients
DELETE CONTACT     -> EMAILS / NUMERO_TELEPHONES / CONTACT_ACTIVITE / RENDEZ_VOUS
                      / NOTE_TEMPLATES / CLIENT_PORTAL_TOKENS
                      / CLIENT_PORTAL_OTPS / CLIENT_PORTAL_TRUSTED_DEVICES
                      / CLIENT_PORTAL_ACCESS_LOG (SET NULL)
                      / STATISTIQUES (cascade conservée pour future utilisation)
DELETE RENDEZ_VOUS -> NOTES / RAPPELS / STATISTIQUES
DELETE ACTIVITE    -> CONTACT_ACTIVITE / RENDEZ_VOUS / STATISTIQUES
DELETE ROLE        -> RESTRICT
```
