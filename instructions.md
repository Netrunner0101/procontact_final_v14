# Vérification du cas d'utilisation 1 — « Créer un contact »

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
