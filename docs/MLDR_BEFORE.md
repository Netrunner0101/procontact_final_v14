# MLDR — BEFORE update

**Pro Contact — Modèle Logique de Données Relationnel (état initial)**

Source: `MD/milestone3_MCD_MLDR_ClassDiagram.md` § 2

---

## Notation

- `PK` clé primaire — `FK` clé étrangère (préfixe `#`) — `UQ` unique
- `CASCADE` / `SET NULL` / `RESTRICT` = action ON DELETE

---

## Schéma relationnel (12 tables métier)

```
ROLES (
    id           : INT             PK AUTO_INCREMENT,
    nom          : VARCHAR(255)    NOT NULL UNIQUE,
    description  : VARCHAR(255)    NULL,
    created_at   : TIMESTAMP       NULL,
    updated_at   : TIMESTAMP       NULL
)

USERS (
    id                   : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    #role_id             : BIGINT UNSIGNED  NOT NULL  FK -> ROLES(id) RESTRICT,
    nom, prenom          : VARCHAR(255)     NOT NULL,
    email                : VARCHAR(255)     NOT NULL UNIQUE,
    telephone            : VARCHAR(255)     NULL,
    rue, numero_rue,
    ville, code_postal,
    pays                 : VARCHAR(255)     NULL,
    email_verified_at    : TIMESTAMP        NULL,
    password             : VARCHAR(255)     NOT NULL,
    remember_token       : VARCHAR(100)     NULL,
    google_id, apple_id,
    provider, avatar     : VARCHAR(255)     NULL,
    #admin_user_id       : BIGINT UNSIGNED  NULL  FK -> USERS(id) CASCADE,
    #contact_id          : BIGINT UNSIGNED  NULL  FK -> CONTACTS(id) SET NULL,
    layout_preference    : VARCHAR(20)      NOT NULL DEFAULT 'modern',
    last_login_at        : TIMESTAMP        NULL,
    created_at,
    updated_at           : TIMESTAMP        NULL
)

STATUSES (
    id            : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    status_client : VARCHAR(255)     NOT NULL,
    timestamps
)

CONTACTS (
    id            : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    #user_id      : BIGINT UNSIGNED  NOT NULL  FK -> USERS(id) CASCADE,
    nom, prenom   : VARCHAR(255)     NOT NULL,
    rue, numero,
    ville,
    code_postal,
    pays          : VARCHAR(255)     NULL,
    state_client  : VARCHAR(255)     NULL,
    #status_id    : BIGINT UNSIGNED  NULL  FK -> STATUSES(id) SET NULL,
    portal_token  : VARCHAR(255)     NULL UNIQUE,
    timestamps
)

ACTIVITES (
    id               : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    #user_id         : BIGINT UNSIGNED  NOT NULL  FK -> USERS(id) CASCADE,
    nom              : VARCHAR(255)     NOT NULL,
    description      : TEXT             NULL,
    numero_telephone,
    email,
    image            : VARCHAR(255)     NULL,
    timestamps
)

RENDEZ_VOUS (
    id           : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    #user_id     : BIGINT UNSIGNED  NOT NULL  FK -> USERS(id) CASCADE,
    #contact_id  : BIGINT UNSIGNED  NOT NULL  FK -> CONTACTS(id) CASCADE,
    #activite_id : BIGINT UNSIGNED  NOT NULL  FK -> ACTIVITES(id) CASCADE,
    titre        : VARCHAR(255)     NOT NULL,
    description  : TEXT             NULL,
    date_debut,
    date_fin     : DATE             NOT NULL,
    heure_debut,
    heure_fin    : TIME             NOT NULL,
    timestamps
)

NOTES (
    id                     : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    #user_id               : BIGINT UNSIGNED  NOT NULL  FK -> USERS(id) CASCADE,
    #rendez_vous_id        : BIGINT UNSIGNED  NOT NULL  FK -> RENDEZ_VOUS(id) CASCADE,
    #activite_id           : BIGINT UNSIGNED  NULL      FK -> ACTIVITES(id) SET NULL,
    titre                  : VARCHAR(255)     NOT NULL,
    commentaire            : TEXT             NOT NULL,
    is_shared_with_client  : BOOLEAN          NOT NULL DEFAULT false,
    date_create,
    date_update            : DATETIME         NOT NULL,
    timestamps
)

RAPPELS (
    id              : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    #user_id        : BIGINT UNSIGNED  NOT NULL  FK -> USERS(id) CASCADE,
    #rendez_vous_id : BIGINT UNSIGNED  NOT NULL  FK -> RENDEZ_VOUS(id) CASCADE,
    date_rappel     : DATETIME         NOT NULL,
    frequence       : VARCHAR(255)     NOT NULL,
    timestamps
)

EMAILS (
    id          : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    #user_id    : BIGINT UNSIGNED  NOT NULL  FK -> USERS(id) CASCADE,
    #contact_id : BIGINT UNSIGNED  NOT NULL  FK -> CONTACTS(id) CASCADE,
    email       : VARCHAR(255)     NOT NULL,
    timestamps
)

NUMERO_TELEPHONES (
    id               : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    #user_id         : BIGINT UNSIGNED  NOT NULL  FK -> USERS(id) CASCADE,
    #contact_id      : BIGINT UNSIGNED  NOT NULL  FK -> CONTACTS(id) CASCADE,
    numero_telephone : VARCHAR(255)     NOT NULL,
    timestamps
)

STATISTIQUES (
    id              : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    #activite_id    : BIGINT UNSIGNED  NOT NULL  FK -> ACTIVITES(id) CASCADE,
    #rendez_vous_id : BIGINT UNSIGNED  NOT NULL  FK -> RENDEZ_VOUS(id) CASCADE,
    #contact_id     : BIGINT UNSIGNED  NOT NULL  FK -> CONTACTS(id) CASCADE,
    timestamps
)

CONTACT_ACTIVITE (
    id           : BIGINT UNSIGNED  PK AUTO_INCREMENT,
    #contact_id  : BIGINT UNSIGNED  NOT NULL  FK -> CONTACTS(id) CASCADE,
    #activite_id : BIGINT UNSIGNED  NOT NULL  FK -> ACTIVITES(id) CASCADE,
    timestamps,
    UNIQUE (contact_id, activite_id)
)
```

## Tables techniques Laravel

`migrations`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`.

## Cascade des suppressions (résumé)

```
DELETE USER (admin) -> CONTACTS / ACTIVITES / RENDEZ_VOUS / clients (USERS.admin_user_id)
DELETE CONTACT     -> EMAILS / NUMERO_TELEPHONES / CONTACT_ACTIVITE / RENDEZ_VOUS / STATISTIQUES
DELETE RENDEZ_VOUS -> NOTES / RAPPELS / STATISTIQUES
DELETE ACTIVITE    -> CONTACT_ACTIVITE / RENDEZ_VOUS / STATISTIQUES
DELETE ROLE        -> RESTRICT (interdit si users le référencent)
```
