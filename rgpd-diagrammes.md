# Champs RGPD à ajouter dans les diagrammes

Ce document liste **exactement** ce que tu dois reporter dans ton MCD,
MLD et diagramme de classes pour refléter les migrations RGPD livrées
dans les PRs #72 et #80. Les noms et types ci-dessous viennent
directement des fichiers de migration — aucune invention.

---

## 1. Source de vérité — extrait des migrations

### `users` (PR #72)

Migration : `database/migrations/2026_05_22_100000_add_terms_accepted_to_users_table.php`

```php
$table->timestamp('terms_accepted_at')->nullable()->after('email_verified_at');
$table->string('terms_accepted_version', 16)->nullable()->after('terms_accepted_at');
```

### `contacts` (PR #80)

Migration : `database/migrations/2026_05_23_100000_add_gdpr_consent_to_contacts_table.php`

```php
$table->string('gdpr_consent_token', 64)->nullable()->unique();
$table->timestamp('gdpr_consent_requested_at')->nullable();
$table->timestamp('gdpr_consent_signed_at')->nullable();
$table->timestamp('gdpr_consent_declined_at')->nullable();
$table->string('gdpr_consent_version', 16)->nullable();
$table->string('gdpr_consent_ip', 45)->nullable();
```

---

## 2. À ajouter au MCD (modèle conceptuel)

### Entité `UTILISATEUR` (admin)

Ajouter deux attributs :

| Attribut | Type conceptuel | Description |
|---|---|---|
| `terms_accepted_at` | Date/heure | Quand le consentement aux CGU a été donné |
| `terms_accepted_version` | Texte (≤16) | Version des documents acceptés (ex. `2026-05`) |

### Entité `CONTACT`

Ajouter six attributs :

| Attribut | Type conceptuel | Description |
|---|---|---|
| `gdpr_consent_token` | Texte (64, unique) | Jeton public envoyé par mail au contact |
| `gdpr_consent_requested_at` | Date/heure | Quand l'invitation a été envoyée |
| `gdpr_consent_signed_at` | Date/heure | Quand le contact a accepté |
| `gdpr_consent_declined_at` | Date/heure | Quand le contact a refusé |
| `gdpr_consent_version` | Texte (≤16) | Version des documents au moment de la décision |
| `gdpr_consent_ip` | Texte (≤45) | IP du contact lors de la décision (IPv6 max = 45 car.) |

**Aucune nouvelle entité, aucune nouvelle association.** Les deux blocs
restent rattachés respectivement à `UTILISATEUR` et `CONTACT`.

---

## 3. À ajouter au MLD (modèle logique)

### Table `users`

Ajouter à la fin de la table existante :

```
terms_accepted_at         DATETIME    NULL
terms_accepted_version    VARCHAR(16) NULL
```

### Table `contacts`

Ajouter à la fin de la table existante :

```
gdpr_consent_token         VARCHAR(64) NULL UNIQUE
gdpr_consent_requested_at  DATETIME    NULL
gdpr_consent_signed_at     DATETIME    NULL
gdpr_consent_declined_at   DATETIME    NULL
gdpr_consent_version       VARCHAR(16) NULL
gdpr_consent_ip            VARCHAR(45) NULL
```

**Contrainte** : `UNIQUE(gdpr_consent_token)` — indique-le dans le MLD
(soit avec une notation `UQ`, soit en notant la contrainte sous la
table).

**Aucune nouvelle clé étrangère.**

---

## 4. À ajouter au diagramme de classes UML

### Classe `User` (admin)

Ajouter dans la zone Attributs :

```
+ terms_accepted_at : DateTime?
+ terms_accepted_version : string?       « max 16 caractères »
```

Aucune nouvelle méthode dans `User` (le contrôle se fait dans le
middleware `EnsureRgpdConsent`, pas sur le modèle).

### Classe `Contact`

Ajouter dans la zone Attributs :

```
+ gdpr_consent_token : string?           « unique, max 64 »
+ gdpr_consent_requested_at : DateTime?
+ gdpr_consent_signed_at : DateTime?
+ gdpr_consent_declined_at : DateTime?
+ gdpr_consent_version : string?         « max 16 »
+ gdpr_consent_ip : string?              « max 45 »
```

Ajouter dans la zone Méthodes :

```
+ hasSignedGdprConsent() : bool
+ hasDeclinedGdprConsent() : bool
+ isGdprConsentPending() : bool
```

### Nouvelles classes à représenter (optionnel mais recommandé)

Ce sont les classes RGPD que tu peux ajouter au diagramme si tu veux
montrer l'architecture complète :

| Classe | Stéréotype | Rôle |
|---|---|---|
| `RgpdConsentController` | « Controller » | Page de rappel admin |
| `ContactConsentController` | « Controller » | Page publique tokenizée pour le contact |
| `EnsureRgpdConsent` | « Middleware » | Redirige les admins sans consentement |
| `RgpdConsentMail` | « Mailable » | Preuve signée envoyée à l'admin |
| `RgpdAccountDeletedMail` | « Mailable » | Notification de suppression admin |
| `ContactConsentRequestMail` | « Mailable » | Invitation au contact |
| `ContactConsentConfirmationMail` | « Mailable » | Preuve signée au contact + CC admin + BCC ProContact |
| `ContactDeletionNotificationMail` | « Mailable » | Notification de suppression contact |

Relations (dépendances) à tracer :

```
AuthController            ──> RgpdConsentMail
RgpdConsentController     ──> RgpdConsentMail
ProfileController         ──> RgpdAccountDeletedMail
ContactController         ──> ContactConsentRequestMail
ContactController         ──> ContactDeletionNotificationMail
ContactConsentController  ──> ContactConsentConfirmationMail
EnsureRgpdConsent         ──> User
```

---

## 5. Règles métier à noter sous les diagrammes (optionnel)

Si ton rapport a une section « Règles métier », tu peux ajouter :

- **R1** — Un compte admin ne peut accéder à l'application que si
  `users.terms_accepted_at IS NOT NULL` (sinon redirection forcée vers
  la page de consentement).
- **R2** — À la création d'un contact, un `gdpr_consent_token` aléatoire
  est généré et envoyé par e-mail au contact ; `gdpr_consent_requested_at`
  est positionné à `NOW()`.
- **R3** — Pour un contact donné, **un seul** des deux champs
  `gdpr_consent_signed_at` ou `gdpr_consent_declined_at` peut être non
  nul à la fois (décision exclusive).
- **R4** — La version des documents (`terms_accepted_version` /
  `gdpr_consent_version`) est figée au moment de la décision, jamais
  recalculée a posteriori.
- **R5** — La signature SHA-256 envoyée par e-mail est calculée sur
  l'ensemble des champs ci-dessus + `APP_KEY` (secret serveur) ; elle
  n'est **pas** stockée en base — la base reste l'origine, l'e-mail est
  la preuve infalsifiable côté utilisateur.

---

## 6. Ce qui n'est **pas** stocké en base (à ne pas ajouter aux diagrammes)

Pour éviter toute confusion :

- ❌ La **signature SHA-256** n'est pas stockée — elle est recalculée à
  la demande à partir des champs ci-dessus + `APP_KEY`. Donc pas de
  colonne `consent_signature` dans aucune table.
- ❌ Les **logs RGPD** (`storage/logs/rgpd-*.log`) sont en fichier,
  pas en base — donc pas d'entité « ConsentLog » au MCD/MLD.
- ❌ Les **e-mails envoyés** sont chez le destinataire et chez Resend,
  pas en base — donc pas d'entité « ConsentEmail » non plus.

---

## 7. Récap chiffré

| Diagramme | Entités modifiées | Nouveaux attributs | Nouvelles entités | Nouvelles associations |
|---|---|---|---|---|
| MCD | UTILISATEUR, CONTACT | 2 + 6 = **8** | 0 | 0 |
| MLD | users, contacts | 2 + 6 = **8 colonnes** | 0 tables | 0 FK |
| Classes | User, Contact | 8 attributs + 3 méthodes sur Contact | 8 (optionnelles : controllers/middleware/mailables) | dépendances seulement |
