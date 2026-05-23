# RGPD / GDPR — ProContact

Documentation du dispositif de conformité RGPD intégré dans l'application
(commit initial : branche `claude/magical-planck-GtrFY`, PR #72).

Pour les utilisateurs finaux, les documents publics sont accessibles à :

- `/legal/privacy` — Politique de confidentialité
- `/legal/terms` — Conditions Générales d'Utilisation
- `/legal/cookies` — Politique relative aux cookies

---

## 1. Schéma — ce qui a été ajouté

### Base de données

Migration `2026_05_22_100000_add_terms_accepted_to_users_table.php` — ajoute
deux colonnes sur `users` :

| Colonne                  | Type                    | Rôle                                       |
| ------------------------ | ----------------------- | ------------------------------------------ |
| `terms_accepted_at`      | `timestamp` nullable    | Horodatage du consentement (UTC).          |
| `terms_accepted_version` | `string(16)` nullable   | Version des documents acceptés (ex `2026-05`). |

La version est lue depuis `config('app.rgpd_version')` (variable
d'environnement `RGPD_VERSION`, défaut `2026-05`). Quand vous publiez une
nouvelle version des documents légaux, incrémentez cette valeur : tous les
comptes existants seront automatiquement invités à reconsentir.

### Fichiers créés

```
app/Http/Controllers/RgpdConsentController.php   # page de rappel + traitement
app/Http/Middleware/EnsureRgpdConsent.php        # redirection vers la page de rappel
app/Mail/RgpdConsentMail.php                     # mail de preuve de consentement
app/Mail/RgpdAccountDeletedMail.php              # mail de confirmation de suppression

resources/views/legal/privacy.blade.php          # Politique de confidentialité
resources/views/legal/terms.blade.php            # CGU
resources/views/legal/cookies.blade.php          # Politique cookies
resources/views/components/legal-layout.blade.php # Layout partagé des pages légales

resources/views/rgpd/consent.blade.php           # Page de rappel (case à cocher)
resources/views/emails/rgpd-consent.blade.php    # Template du mail de consentement
resources/views/emails/rgpd-account-deleted.blade.php  # Template du mail de suppression

database/migrations/2026_05_22_100000_add_terms_accepted_to_users_table.php
```

### Fichiers modifiés

- `app/Http/Controllers/Auth/AuthController.php` — validation `terms`,
  écriture de `terms_accepted_at`, envoi du mail, log audit.
- `app/Http/Controllers/ProfileController.php` — envoi du mail de
  suppression avant `delete()`, log dédié.
- `app/Models/User.php` — `fillable` + cast datetime de
  `terms_accepted_at`.
- `database/factories/UserFactory.php` — état par défaut `terms_accepted_at = now()`
  et état nommé `withoutGdprConsent()` pour les tests.
- `resources/views/auth/register.blade.php` — case à cocher obligatoire
  dans les deux flux (email/password et OAuth).
- `resources/views/auth/register-success.blade.php` — bloc de rappel
  RGPD avec liens.
- `resources/views/welcome-production.blade.php` — footer pointant vers
  les vraies pages légales.
- `routes/web.php` — routes `legal.*` (publiques) et `rgpd.consent.*`
  (authentifiées), middleware `rgpd.consent` ajouté aux groupes admin
  et client.
- `bootstrap/app.php` — alias middleware `rgpd.consent`.
- `config/app.php` — clé `rgpd_version`.
- `config/logging.php` — channel `rgpd` (rétention 365 jours).
- `lang/fr.json` — traductions FR des nouvelles chaînes.

---

## 2. Flux fonctionnels

### 2.1 Inscription (nouveau compte)

1. L'utilisateur coche la case **« J'ai lu et j'accepte la Politique de
   confidentialité et les Conditions Générales »** (les liens ouvrent
   les documents dans un nouvel onglet).
2. `AuthController@register` valide `terms => accepted` ; sans la case,
   le compte n'est pas créé.
3. À la création, `terms_accepted_at` est défini à `now()` et
   `terms_accepted_version` à la version courante.
4. Un mail **« Votre enregistrement de consentement RGPD »** est envoyé
   à l'utilisateur, contenant :
   - User ID, nom, email
   - Date d'acceptation (UTC)
   - Statut du consentement (OUI / NON)
   - Version du document
   - Liens vers les trois documents acceptés
   - **Signature SHA-256** (`hash('sha256', user_id|email|date|consent|version|APP_KEY)`)
     qui prouve que l'enregistrement n'a pas été altéré.
5. Une entrée est écrite dans `storage/logs/rgpd-YYYY-MM-DD.log` avec
   l'IP et le user-agent.

Même flux pour l'inscription via OAuth (Google / Apple), via la méthode
`confirmOauthRegistration`.

### 2.2 Rappel — utilisateur existant sans consentement enregistré

Le middleware `rgpd.consent` est appliqué sur les groupes de routes
admin et client. Quand un utilisateur authentifié dont
`terms_accepted_at` est `NULL` accède à n'importe quelle route protégée :

1. Il est redirigé vers `/rgpd/consent` (page `rgpd.consent.show`).
2. Cette page liste les trois documents (ouverture en nouvel onglet) et
   présente une case à cocher.
3. La validation et l'enregistrement sont identiques au flux
   d'inscription : mise à jour des champs, mail de preuve, log RGPD.
4. Routes exemptées du middleware : `rgpd.consent.*`, `logout`,
   `legal.*`, `lang.switch` (pour éviter une boucle).

Cas d'usage typique : vous mettez à jour les CGU → vous incrémentez
`RGPD_VERSION` dans `.env` → un script SQL passe `terms_accepted_at = NULL`
sur les comptes concernés → ils sont automatiquement invités à reconsentir.

### 2.3 Consentement des contacts (clients de l’admin)

Quand un admin crée un nouveau contact via `ContactController@store` :

1. Un token aléatoire `gdpr_consent_token` (64 caractères) est généré et
   stocké sur le contact, avec `gdpr_consent_requested_at = now()`.
2. Un mail `ContactConsentRequestMail` est envoyé à l’adresse email
   principale du contact. Il contient un lien public
   `/consent/{token}` (sans authentification).
3. Sur la page `/consent/{token}` le contact voit :
   - Qui est le responsable du traitement (l’admin)
   - Quelles données sont traitées et pour quelles finalités
   - Ses droits (accès, rectification, effacement)
   - Les liens vers les pages légales de la plateforme
   - Deux boutons : **Accepter** ou **Refuser** (case à cocher requise)
4. À la décision :
   - `gdpr_consent_signed_at` ou `gdpr_consent_declined_at` est défini
   - `gdpr_consent_ip` et `gdpr_consent_version` sont enregistrés
   - Un log `Contact GDPR decision recorded` est écrit dans `rgpd.log`
   - Un mail `ContactConsentConfirmationMail` est envoyé avec :
     - **To** : le contact (preuve principale)
     - **CC** : l’admin (notification au responsable)
     - **BCC** : `contact@procontact.app` (audit plateforme)
     - Signature SHA-256 sur (contact_id|contact_email|admin_id|admin_email|date|consent|version|APP_KEY)

Si le contact n’a pas encore décidé et revient sur le lien, il revoit la
page de décision. S’il a déjà décidé, il voit la page de confirmation
correspondante.

### 2.4 Suppression d’un contact par l’admin

Quand l’admin supprime un contact via `ContactController@destroy` :

1. Un mail `ContactDeletionNotificationMail` est envoyé **avant** la
   suppression :
   - **To** : le contact (information sur la suppression)
   - **CC** : l’admin (confirmation de l’action effectuée)
   - **BCC** : `contact@procontact.app` (audit)
2. Un log `Contact GDPR deletion executed` est écrit avec un hash
   anonymisé `SHA-256(contact_id|email)`.
3. Suppression effective du contact (cascade sur emails, téléphones,
   rendez-vous, notes…).

### 2.5 Suppression de compte (admin)

`ProfileController@destroy` :

1. Triple confirmation existante (email + phrase + mot de passe).
2. **Avant** la suppression, envoi du mail **« Votre compte Pro Contact
   a été supprimé »** contenant :
   - Email du compte supprimé
   - Date de suppression (UTC)
   - Hash anonymisé `SHA-256(id|email)` (piste d'audit non réversible)
   - Précisions sur la rétention des sauvegardes (~30 jours) et les
     entrées d'audit légales (Art. 17(3)(b)).
3. Suppression effective en transaction (`DB::transaction`), cascade
   sur toutes les données associées (contacts, rendez-vous, notes…).
4. Deux entrées de log :
   - `laravel.log` (audit applicatif standard, avec hash)
   - `rgpd.log` (channel dédié, conservé 365 jours)

---

## 3. Sécurité et preuve

### 3.1 Signature des mails de consentement

```
signature = SHA-256( user_id | email | accepted_at_iso | "true|false" | version | APP_KEY )
```

`APP_KEY` étant un secret serveur, un attaquant ne peut pas forger une
signature valide. En cas de contestation, vous pouvez recalculer la
signature à partir des données et la comparer à celle envoyée à
l'utilisateur dans le mail.

### 3.2 Channel de log dédié

`storage/logs/rgpd.log` (rotation quotidienne, rétention 365 jours
configurable via `RGPD_LOG_DAYS`). Chaque évènement contient :

- `user_id`, `email`, `consent`, `version`, `accepted_at`
- `ip`, `user_agent` (tronqué à 255 caractères)

À sauvegarder hors-ligne dans votre politique de rétention pour les
audits CNIL / APD.

### 3.3 Données conservées après suppression

Conformément à l'**Art. 17(3)(b) du RGPD**, seules les entrées
anonymisées suivantes sont retenues :

- Hash du compte (`SHA-256(id|email)`) — non réversible.
- Date et IP de la demande d'effacement.

Tout le reste (nom, prénom, email, téléphone, contacts, rendez-vous,
notes, rappels) est effacé en cascade dans la même transaction.

---

## 4. Configuration

`.env` :

```dotenv
RGPD_VERSION=2026-05                       # version des documents légaux
RGPD_LOG_DAYS=365                          # rétention du log rgpd (jours)
RGPD_AUDIT_EMAIL=contact@procontact.app    # BCC sur les mails RGPD (laisser vide pour désactiver)
```

`config/app.php` lit `RGPD_VERSION` (défaut `2026-05`) et `RGPD_AUDIT_EMAIL`
(défaut `contact@procontact.app`). Si la valeur est non vide, les deux
mailables RGPD (`RgpdConsentMail` et `RgpdAccountDeletedMail`) ajoutent
cette adresse en **BCC** pour conserver une copie côté entreprise sans
l'exposer à l'utilisateur.

`config/logging.php` lit `RGPD_LOG_DAYS` (défaut `365`).

---

## 5. Tests

- L'état par défaut de `UserFactory` pose `terms_accepted_at = now()`
  pour ne pas casser les tests existants.
- L'état `withoutGdprConsent()` est disponible pour tester le flux de
  rappel : `User::factory()->withoutGdprConsent()->create()`.
- Les tests d'inscription (`RegisterFlowTest`, `AuthenticationTest`)
  passent désormais `'terms' => '1'` dans le payload.

Baseline avant / après : 127 verts dans les deux cas ; les 56 échecs
préexistants ne sont pas liés au RGPD (manifeste Vite manquant en
environnement de test, assertion `fillable` obsolète…).

---

## 6. Checklist de conformité

| Exigence RGPD                                  | Statut | Référence                                |
| ---------------------------------------------- | ------ | ---------------------------------------- |
| Information claire (Art. 13–14)                | ✅     | `resources/views/legal/privacy.blade.php` |
| Consentement libre, spécifique, éclairé (Art. 7) | ✅     | Case obligatoire au signup               |
| Preuve du consentement (Art. 7§1)              | ✅     | Mail signé + log RGPD                    |
| Droit d'accès / portabilité (Art. 15 / 20)     | ✅     | `/profile/export` (existant)             |
| Droit à l'effacement (Art. 17)                 | ✅     | `/profile` → suppression                 |
| Notification de la suppression                 | ✅     | `RgpdAccountDeletedMail`                 |
| Cookies strictement nécessaires uniquement     | ✅     | `resources/views/legal/cookies.blade.php` |
| Registre des traitements                       | ⚠️    | À tenir hors application                 |
| DPO désigné si requis                          | ⚠️    | Selon la taille de l'organisation        |
| Analyse d'impact (AIPD/DPIA) si requis         | ⚠️    | Selon la nature des traitements          |

Les trois lignes ⚠️ relèvent de l'organisation (registres internes,
gouvernance), pas du code.
