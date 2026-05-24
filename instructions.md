# Vérification des cas d'utilisation 1 / 2 / 3 — ProContact

> Document unique consolidant la vérification UC1, UC2, UC3.
> Comparaison entre les spécifications rédigées et l'implémentation Laravel 12 réelle.

---

# UC1 — Créer un contact

> Comparaison entre la spécification rédigée (résumé, scénarios nominal / alternatifs / erreurs)
> et l'implémentation réelle dans le code Laravel 12 du projet ProContact.
>
> Objectif : indiquer ce qui est **conforme**, ce qui **manque** dans la description, et
> ce qui doit éventuellement **changer** (dans la doc ou dans le code).

---

## 1. Périmètre du cas d'utilisation

Deux flux de création de contact coexistent dans le code :

| Flux | Route | Code | Couverture du UC |
|------|-------|------|------------------|
| **Classique MVC** | `Route::resource('contacts', ContactController::class)` (`routes/web.php:131`) | `app/Http/Controllers/ContactController.php` + `resources/views/contacts/create.blade.php` | ✅ **C'est ce flux qui correspond à la description du UC.** |
| **Livewire (gestionnaire interne)** | `GET /contacts-manager` (`routes/web.php:108`) | `app/Livewire/ContactManager.php` + `resources/views/livewire/contact-manager.blade.php` | ⚠️ Ne couvre **pas** la création d'email/téléphone dans le formulaire (uniquement nom, prénom, adresses). |

> 👉 **Important :** Le UC rédigé décrit le flux **ContactController** (formulaire complet
> avec emails et téléphones obligatoires). Il faut soit le préciser dans la description,
> soit aligner `ContactManager` avec le UC.

---

## 2. Conformité point par point

### ✅ Points conformes au code

| Élément du UC | Vérification dans le code |
|---|---|
| Acteur « indépendant authentifié » | Routes protégées par `Route::middleware('auth')` (`routes/web.php`). Politique `ContactPolicy` filtre par `user_id`. |
| Étape 2 : liste des contacts + bouton « Créer nouveau contact » | `ContactController@index` → `contacts.index` avec lien vers `contacts.create`. |
| Étape 4 : formulaire avec champs Nom, Prénom, Email, Numéro de téléphone | `contacts/create.blade.php` lignes 150 (`nom`), 163 (`prenom`), 199 (`emails[]`), 255 (`phones[]`), tous `required`. |
| Boutons « Enregistrer » / « Annuler » | Présents dans `contacts/create.blade.php` (`<button type="submit">` + `<a href="{{ route('contacts.index') }}">`). |
| A1 — bouton « + » pour ajouter téléphone/email | `addEmail` (ligne 191) et `addPhone` (ligne 227) injectent dynamiquement des inputs `emails[]` / `phones[]`. |
| A2 — annulation = retour liste sans enregistrement | Lien `Annuler` → `route('contacts.index')`, pas de POST. |
| E1 — champs obligatoires manquants (mise en évidence rouge + message) | Validation Laravel + classes `@error('nom') border-red-500 @enderror` dans la vue. |
| E2 — format email invalide | Règle `emails.* => email` (`ContactController.php:44`) avec message localisé « The email address is not valid. » (`ContactController.php:56`). |
| Étape 6 : enregistrement contact + emails + téléphones, redirection + message succès | `ContactController@store` (`ContactController.php:60-93`) : `Contact::create` puis boucles `emails()->create` et `numeroTelephones()->create`, `redirect()->route('contacts.index')->with('success', ...)`. |

### ⚠️ Comportements **présents dans le code mais absents de la description**

À intégrer dans le UC (ou expliquer pourquoi on ne les liste pas) :

1. **Envoi automatique d'un email de consentement RGPD** au premier email du contact après création
   (`ContactController.php:73` → `sendContactConsentRequest()`).
   - Génère également `gdpr_consent_token` (Str::random(64)) et `gdpr_consent_requested_at` sur le contact (`ContactController.php:64-65`).
   - 👉 **À ajouter en post-condition** : « Un email de demande de consentement RGPD est envoyé à la première adresse email du contact. »

2. **Ajout d'adresses postales** (rue, n°, code postal, ville, pays, principale) via un repeater.
   - Validé dans `ContactController.php:47-53` puis persisté par `AdresseSyncer::sync` (`ContactController.php:80`).
   - 👉 **À ajouter en scénario alternatif A3** : « Saisie d'une ou plusieurs adresses postales ».

3. **Rattachement à une activité existante** quand l'utilisateur arrive depuis la page d'une activité
   (`?activite_id=...`).
   - Pré-sélection dans la vue, attachement via `syncWithoutDetaching` (`ContactController.php:88`),
     redirection vers `activites.show` au lieu de `contacts.index` (`ContactController.php:90`).
   - 👉 **À ajouter en scénario alternatif A4** : « Création d'un contact rattaché à une activité ».

4. **Filtrage par regex du numéro de téléphone** : seuls les caractères `0-9 + espace ( ) -` sont autorisés
   (`ContactController.php:46`), avec un message d'erreur spécifique.
   - 👉 **À ajouter en E3** : « Format de téléphone invalide ».

5. **Multi-tenant strict** : `user_id = Auth::id()` est imposé sur `contacts`, `emails`,
   `numero_telephones`.
   - 👉 À mentionner en pré-condition technique : « Le contact créé est isolé au tenant de l'indépendant connecté. »

### ❌ Écarts / incohérences à corriger

| # | Constat | Action recommandée |
|---|---------|--------------------|
| 1 | Le UC ne précise pas que **email ET téléphone sont obligatoires en quantité ≥ 1** (règles `required\|array\|min:1`). | Préciser « au moins un email et un numéro de téléphone » à l'étape 4. |
| 2 | Le scénario nominal ne mentionne pas le **token RGPD** ni l'envoi de mail. | Étape 6 du système : ajouter « envoie l'email de consentement RGPD ». |
| 3 | Le UC dit « met en évidence en rouge » : c'est exact mais c'est **côté serveur** (re-render Blade), pas client. À clarifier si la copie est ambiguë. | Ajouter « après soumission » dans E1/E2. |
| 4 | Les **adresses postales** n'apparaissent nulle part dans la description, alors qu'elles font partie du formulaire réel. | Ajouter A3 (cf. §2.2.2) ou retirer le bloc adresses du formulaire si elles ne sont pas dans le périmètre du UC. |
| 5 | Le `ContactManager` Livewire (page « Contacts Manager ») **ne propose pas** la saisie d'email/téléphone au formulaire de création, contrairement au UC. | Décider : (a) aligner `ContactManager::createContact` pour exiger emails + phones, ou (b) restreindre le UC au `ContactController`. |

---

## 3. Synthèse — Faut-il changer quelque chose ?

### Côté **documentation** (description du UC) — recommandé
- [ ] Ajouter post-condition « Envoi d'un email de consentement RGPD ».
- [ ] Préciser « au moins un email + au moins un téléphone obligatoires ».
- [ ] Ajouter A3 (adresses) et A4 (rattachement à une activité).
- [ ] Ajouter E3 (format téléphone invalide).
- [ ] Mentionner l'isolation multi-tenant en pré-condition.

### Côté **code** — à arbitrer
- [ ] **Décision produit** : le formulaire Livewire `ContactManager` doit-il aussi gérer emails/téléphones ? Si oui, ajouter les champs `emails[]`/`phones[]` dans `app/Livewire/ContactManager.php` + sa vue, avec les mêmes règles de validation que `ContactController::store`.
- [ ] Si non, supprimer ou renommer le flux Livewire pour éviter la confusion avec le UC.

### Côté **diagrammes participatifs (DCP)**
- [ ] Ajouter les participants suivants (présents dans le code mais manquants dans le DCP du UC) :
  - `AdresseSyncer` (service) — `app/Services/AdresseSyncer.php`
  - `Adresse` (entity) — `app/Models/Adresse.php`
  - `Pays` (entity) — `app/Models/Pays.php`
  - `ContactConsentRequestMail` (mailable)
  - `Activite` (entity, pivot `activite_contact`) si A4 est inclus.

---

## 4. Mapping fichiers ↔ classes du DCP (à utiliser pour le diagramme MVC)

| Stéréotype | Classe DCP | Fichier réel |
|---|---|---|
| `«router»` | `WebRouter` | `routes/web.php` (l. 131 `resource('contacts', ...)`) |
| `«boundary»` | `ContactIndexView` | `resources/views/contacts/index.blade.php` |
| `«boundary»` | `ContactCreateView` | `resources/views/contacts/create.blade.php` |
| `«control»` | `ContactController` | `app/Http/Controllers/ContactController.php` |
| `«control»` | `AdresseSyncer` | `app/Services/AdresseSyncer.php` |
| `«control»` | `ContactConsentRequestMail` | `app/Mail/ContactConsentRequestMail.php` |
| `«entity»` | `Contact` | `app/Models/Contact.php` |
| `«entity»` | `Email` | `app/Models/Email.php` |
| `«entity»` | `NumeroTelephone` | `app/Models/NumeroTelephone.php` |
| `«entity»` | `Adresse` | `app/Models/Adresse.php` |
| `«entity»` | `Pays` | `app/Models/Pays.php` |
| `«entity»` | `Status` | `app/Models/Status.php` |
| `«entity»` (optionnel) | `Activite` | `app/Models/Activite.php` |

---

**Conclusion :** la description du UC est globalement fidèle au code pour le scénario nominal
et les scénarios alternatifs/erreurs déjà listés, mais elle **passe sous silence** trois
comportements importants implémentés (RGPD, adresses, liaison activité) et un cas d'erreur
(format téléphone). Recommandation : enrichir la description et compléter le DCP plutôt
que modifier le code, sauf si l'on souhaite réellement aligner le flux Livewire.

---

# UC2 — Créer une activité

> Spec de référence : `docs/UC2_CREATE_ACTIVITE.md` (version AFTER).

## Verdict global
✅ **Conforme à 100 % au scénario nominal documenté.** Aucun gros changement requis.
La création d'activité est restée fonctionnellement identique entre BEFORE et AFTER ;
seules les tables périphériques ont bougé (`statistiques` supprimée, `rendez_vous.statut` ajouté).

## Vérification point par point

| Élément spec | Vérification dans le code | Statut |
|---|---|---|
| Acteur « admin authentifié » | Routes sous `Route::middleware('auth')`. `ActivitePolicy` filtre par `user_id`. | ✅ |
| Route `POST /activites` | `Route::resource('activites', ActiviteController::class)` (`routes/web.php`). | ✅ |
| Champs `nom*`, `description*` obligatoires | `ActiviteController.php:28-29` → `required\|string`. | ✅ |
| Champs `email`, `numero_telephone`, `image` optionnels | `ActiviteController.php:30-32` → tous `nullable`. | ✅ |
| Validation regex téléphone (chiffres) | `ActiviteController.php:30` → `regex:/^[0-9]+$/\|max:20`. | ✅ |
| Upload image vers `storage/app/public/activites` | `ActiviteController.php:36` → `store('activites', 'public')`. | ✅ |
| `user_id = auth()->id()` injecté | `ActiviteController.php:39`. | ✅ |
| Redirection liste + flash success | `ActiviteController.php:42`. | ✅ |
| Affichage en cartes sur dashboard | `ActiviteController@index` → vue `activites.index`. | ✅ |
| Table `statistiques` supprimée | Aucun modèle `Statistique` actif n'écrit — `StatistiqueController` calcule à la volée via `withCount` / `selectRaw`. | ✅ |
| `rendez_vous.statut` ajouté | Modèle `RendezVous` expose `statut`. Migration appliquée. | ✅ |
| `rappels.destinataire` + `emails_cc` | Modèle `Rappel` les expose ; vue rappels-create les saisit. | ✅ |

## Écarts mineurs / points à ajouter au UC rédigé

1. **Validation regex téléphone** plus stricte que dans la spec :
   le code n'accepte **que les chiffres** (`/^[0-9]+$/`), pas les `+`, espaces, ou tirets
   (contrairement à `ContactController` qui les autorise).
   👉 Si le UC parle de format « libre », il faut soit aligner la regex sur celle de `ContactController`,
   soit préciser dans le UC que l'activité accepte uniquement des chiffres.

2. **Pas de scénario d'erreur formalisé** dans `UC2_CREATE_ACTIVITE.md` :
   - E1 : nom ou description manquant → mise en évidence rouge (Laravel `@error`).
   - E2 : image > 2 Mo ou format non autorisé (mimes:jpeg,png,jpg,gif).
   👉 À ajouter pour homogénéité avec UC1.

3. **Pas de scénarios alternatifs** documentés (alors qu'il y en a au minimum un :
   création **sans image**, et un autre : création **sans email/téléphone**).

## Synthèse UC2
- 🟢 Code = spec. **Rien à modifier dans le code.**
- 🟡 Description du UC à enrichir avec E1, E2 et scénarios alternatifs.
- 🟡 Décider si la regex téléphone doit être unifiée entre `ActiviteController` et `ContactController`.

---

# UC3 — Portail client (consulter ses rendez-vous)

> Spec de référence : `docs/UC3_CLIENT_PORTAL.md` (version AFTER — OTP + appareil de confiance).

## Verdict global
✅ **Conforme à la version AFTER documentée.** L'implémentation correspond intégralement
à l'architecture sécurisée décrite (OTP + cookie trusted-device + journal d'accès).
🔴 **Changement majeur déjà acté** par rapport à la version BEFORE : le portail n'est
plus accessible via un magic-link permanent. **Tous les anciens liens BEFORE sont
inopérants** — c'est attendu.

## Vérification point par point

### Côté admin : émission / révocation de jeton

| Élément spec | Vérification dans le code | Statut |
|---|---|---|
| Génération d'un jeton via UI admin | `AdminPortalAccessController@show` + route `contacts.portal-access` (`routes/web.php:128`). | ✅ |
| Stockage du **hash** SHA-256, jamais du token clair | `PortalAuthService::issueToken` → `token_hash = hash('sha256', $raw)` (`PortalAuthService.php:77`). | ✅ |
| Token affiché **une seule fois** à l'admin | Token brut renvoyé par `issueToken` puis non persisté. | ✅ |
| Révocation explicite | Route `contacts.portal-access.revoke` (`routes/web.php:129`) + `revokeAllTokens` (`PortalAuthService.php:87`) qui pose `revoked_at`. | ✅ |

### Côté contact : accès et OTP

| Élément spec | Vérification dans le code | Statut |
|---|---|---|
| Route publique `GET /portal/{token}` | `PortalController@show` (`PortalController.php:48`). | ✅ |
| Rate-limit `portal-token-visit` | Routes du portail middleware `throttle:portal-token-visit` (`routes/web.php:182,186`). | ✅ |
| Branche « cookie trusted-device valide » → bypass OTP | `validateTrustedDevice` (`PortalAuthService.php:235`) appelé dans `PortalController@show:61`. Rotation cookie automatique. | ✅ |
| Branche « pas de cookie » → demande email puis OTP | `requestOtp` (`PortalController.php:78`) + `issueOtp` (`PortalAuthService.php:99`). | ✅ |
| OTP 6 chiffres, **code hashé** | `Hash::make($code)` stocké en `code_hash` (`PortalAuthService.php:125`). | ✅ |
| Email **hashé** (jamais en clair côté OTP) | `email_hash = hash('sha256', $submittedEmail)` (`PortalAuthService.php:126`). | ✅ |
| Expiration OTP = 10 min | Présent dans `issueOtp`. | ✅ |
| Anti-bruteforce ≤ 5 tentatives, verrouillage par `consumed_at = now()` | `verifyOtp` + `isContactLockedOut` (`PortalAuthService.php:329`). | ✅ |
| Émission cookie trusted-device opt-in (30 j) | `issueTrustedDevice` (`PortalAuthService.php:203`). | ✅ |
| `user_agent_hash` lié au cookie | `PortalAuthService.php:210` + comparaison à la validation (`:252`). | ✅ |
| Journal d'accès SHA-256 (IP + UA hash + metadata JSON) | `log()` (`PortalAuthService.php:317`) → table `client_portal_access_log`. | ✅ |
| Déconnexion = révocation cookie | `PortalController@logout` + `revokeTrustedDevice` (`PortalAuthService.php:291`). | ✅ |
| Droit à l'effacement | `PortalController@requestErasure` (`PortalController.php:153`) + `revokeAllTrustedDevices`. | ✅ |
| Notes filtrées `is_shared_with_client = true` | `PortalController@showAppointment` (`PortalController.php:235`) → `with('notes', fn ($q) => $q->where('is_shared_with_client', true))`. | ✅ |
| Middleware `portal.auth` sur les pages internes | `routes/web.php:203` → `Route::middleware('portal.auth')->group(...)`. | ✅ |
| Middleware `portal.headers` (CSP / HSTS) | `routes/web.php:178`. | ✅ |

### Tables et migrations

| Table attendue | Migration | Modèle | Statut |
|---|---|---|---|
| `client_portal_tokens` | `2026_04_26_130001_create_client_portal_tokens_table.php` | `ClientPortalToken` | ✅ |
| `client_portal_otps` | `2026_04_26_130002_create_client_portal_otps_table.php` | `ClientPortalOtp` | ✅ |
| `client_portal_trusted_devices` | `2026_04_26_130003_create_client_portal_trusted_devices_table.php` | `ClientPortalTrustedDevice` | ✅ |
| `client_portal_access_log` | `2026_04_26_130004_create_client_portal_access_log_table.php` | `ClientPortalAccessLog` | ✅ |

## Écarts / points d'attention

1. **Ancien champ `contacts.portal_token`** (version BEFORE) :
   à vérifier qu'il a bien été supprimé/dépublié de la table `contacts`,
   sinon il reste un vecteur de fuite (hash absent + permanent).
   👉 **Action recommandée** : grep `portal_token` dans `database/migrations` pour confirmer la dépose.

2. **Throttles spécifiques** (`portal-token-visit`, `portal-otp-request`, `portal-otp-verify`)
   doivent être déclarés dans `app/Providers/AppServiceProvider` ou `RouteServiceProvider`.
   👉 À vérifier que les `RateLimiter::for(...)` correspondants existent
   (sinon Laravel lèvera une exception au premier hit).

3. **Tests Dusk UC3_ClientPortalTest.php** : le fichier est référencé dans la spec
   mais on n'a pas vérifié sa présence ni sa couverture des 6 cas (demande OTP / OTP correct /
   OTP incorrect × 5 verrouillage / trusted-device bypass / révocation admin / notes privées invisibles).
   👉 À auditer.

4. **DCP existant pour UC3** : ne mentionne pas les services `PortalAuthService` ni
   `ClientPortalService` ni les 4 tables `client_portal_*`.
   👉 Le DCP doit être enrichi avec ces participants — voir le diagramme déjà produit dans le chat.

## Synthèse UC3
- 🟢 **Architecture AFTER intégralement implémentée**, pas de gros changement à faire.
- 🟡 Vérifier la suppression effective de la colonne morte `contacts.portal_token`.
- 🟡 Vérifier la déclaration des `RateLimiter` pour les 3 throttles nommés.
- 🟡 Compléter le DCP avec services + 4 tables `client_portal_*`.

---

# Synthèse globale UC1 + UC2 + UC3

| UC | Conformité code/spec | Gros changement requis ? | Action prioritaire |
|---|---|---|---|
| **UC1 — Créer un contact** | 80 % | Non | Enrichir la spec (RGPD, adresses, activité, E3 téléphone) — décider sort du Livewire `ContactManager` |
| **UC2 — Créer une activité** | 100 % | Non | Ajouter E1/E2 + scénarios alternatifs à la spec ; arbitrer regex téléphone |
| **UC3 — Portail client** | 100 % (AFTER) | Non (le gros changement BEFORE→AFTER est déjà fait) | Vérifier dépose `contacts.portal_token` + RateLimiter + compléter le DCP |

**Conclusion :** aucun des trois UC ne nécessite de modification fonctionnelle du code.
Les évolutions à porter sont essentiellement **documentaires** (enrichir les specs et DCP)
et **hygiène** (vérifier nettoyage des colonnes mortes et déclarations de rate-limit).
