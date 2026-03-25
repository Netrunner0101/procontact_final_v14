# ProContact - Documentation Compl&egrave;te de la Base de Donn&eacute;es

## Table des mati&egrave;res
1. [Vue d'ensemble](#vue-densemble)
2. [MCD - Mod&egrave;le Conceptuel de Donn&eacute;es](#mcd---mod&egrave;le-conceptuel-de-donn&eacute;es)
3. [MLD - Mod&egrave;le Logique de Donn&eacute;es](#mld---mod&egrave;le-logique-de-donn&eacute;es)
4. [Tables d&eacute;taill&eacute;es](#tables-d&eacute;taill&eacute;es)
5. [Relations et cardinalit&eacute;s](#relations-et-cardinalit&eacute;s)
6. [Donn&eacute;es de r&eacute;f&eacute;rence (Seeds)](#donn&eacute;es-de-r&eacute;f&eacute;rence-seeds)
7. [Analyse et remarques](#analyse-et-remarques)

---

## Vue d'ensemble

**Application :** ProContact - Agenda multi-business pour ind&eacute;pendants/entrepreneurs
**Framework :** Laravel (PHP)
**Base de donn&eacute;es :** PostgreSQL (port 5445, base `agenda_app`)
**Nombre de tables :** 20 (11 m&eacute;tier + 2 pivot/stats + 7 syst&egrave;me Laravel)

### Architecture g&eacute;n&eacute;rale
L'application permet &agrave; un **admin** (entrepreneur/ind&eacute;pendant) de g&eacute;rer ses contacts, activit&eacute;s/services, rendez-vous, notes et rappels. Les **clients** ont un acc&egrave;s en consultation limit&eacute; &agrave; leurs propres rendez-vous.

---

## MCD - Mod&egrave;le Conceptuel de Donn&eacute;es

### Entit&eacute;s et leurs attributs

```
+=====================+          +================+
|       UTILISATEUR   |          |      ROLE      |
+=====================+          +================+
| nom                 |          | nom            |
| prenom              |  N --- 1 | description    |
| email (unique)      |----------+================+
| telephone           |
| rue, numero_rue     |
| ville, code_postal  |
| pays                |
| password            |
| avatar              |
| google_id, apple_id |
| provider            |
| last_login_at       |
+=====================+
     |  1                    1  |
     |                          | (self-ref: admin/client)
     | N                     N  |
+=====================+
|      CONTACT        |
+=====================+         +================+
| nom                 |         |    STATUT      |
| prenom              | N --- 1 +================+
| rue, numero         |-------->| status_client  |
| ville, code_postal  |         +================+
| pays                |
| state_client        |
+=====================+
   |1          |1        \  N
   |           |          \
   |N          |N          \ M
+==========+ +============+ +==============+
|  EMAIL   | | TELEPHONE  | |  ACTIVITE    |
+==========+ +============+ +==============+
| email    | | numero_tel | | nom          |
+==========+ +============+ | description  |
                             | telephone    |
                             | email        |
                             | image        |
                             +==============+
                                  |1
                                  |
                                  |N
                          +===============+
                          |  RENDEZ-VOUS  |
                          +===============+
                          | titre         |
                          | description   |
                          | date_debut    |
                          | date_fin      |
                          | heure_debut   |
                          | heure_fin     |
                          +===============+
                            |1        |1
                            |         |
                            |N        |N
                      +=========+ +==========+
                      |  NOTE   | |  RAPPEL  |
                      +=========+ +==========+
                      | titre   | | date_rappel|
                      |commentaire| frequence |
                      |date_create| +==========+
                      |date_update|
                      +=========+
```

### Cardinalit&eacute;s MCD (notation Merise)

| Relation | Entit&eacute; A | Cardinalit&eacute; | Entit&eacute; B | Cardinalit&eacute; | Description |
|---|---|---|---|---|---|
| Poss&eacute;der_role | Utilisateur | (1,1) | R&ocirc;le | (1,N) | Chaque user a exactement 1 r&ocirc;le |
| Superviser | Utilisateur (client) | (0,1) | Utilisateur (admin) | (0,N) | Un client a 0 ou 1 admin, un admin a 0 ou N clients |
| Li&eacute;_&agrave;_contact | Utilisateur (client) | (0,1) | Contact | (0,1) | Un client peut &ecirc;tre li&eacute; &agrave; une fiche contact |
| G&eacute;rer_contacts | Utilisateur | (1,1) | Contact | (0,N) | Un user g&egrave;re 0 &agrave; N contacts |
| Avoir_statut | Contact | (0,1) | Statut | (0,N) | Un contact a 0 ou 1 statut |
| Avoir_emails | Contact | (1,1) | Email | (0,N) | Un contact a 0 &agrave; N emails |
| Avoir_t&eacute;l&eacute;phones | Contact | (1,1) | T&eacute;l&eacute;phone | (0,N) | Un contact a 0 &agrave; N num&eacute;ros |
| Pratiquer | Contact | (0,N) | Activit&eacute; | (0,N) | **N:M** via pivot `contact_activite` |
| Proposer | Utilisateur | (1,1) | Activit&eacute; | (0,N) | Un user propose 0 &agrave; N activit&eacute;s |
| Planifier | User + Contact + Activit&eacute; | (1,1) chacun | Rendez-vous | (0,N) | Un RDV lie un user, un contact et une activit&eacute; |
| Annoter | Rendez-vous | (1,1) | Note | (0,N) | Un RDV a 0 &agrave; N notes |
| Rappeler | Rendez-vous | (1,1) | Rappel | (0,N) | Un RDV a 0 &agrave; N rappels |
| Tracker | Activit&eacute; + RDV + Contact | (1,1) chacun | Statistique | (0,N) | Stats li&eacute;es &agrave; un triplet |

---

## MLD - Mod&egrave;le Logique de Donn&eacute;es

### Sch&eacute;ma relationnel

```
roles (id, nom, description, created_at, updated_at)

users (id, #role_id, nom, prenom, email, telephone, rue, numero_rue, ville,
       code_postal, pays, password, remember_token, email_verified_at,
       last_login_at, password_reset_token, password_reset_expires,
       google_id, apple_id, provider, avatar, #admin_user_id, #contact_id,
       created_at, updated_at)

statuses (id, status_client, created_at, updated_at)

contacts (id, #user_id, nom, prenom, rue, numero, ville, code_postal, pays,
          state_client, #status_id, created_at, updated_at)

activites (id, #user_id, nom, description, numero_telephone, email, image,
           created_at, updated_at)

contact_activite (id, #contact_id, #activite_id, created_at, updated_at)
    UNIQUE(contact_id, activite_id)

rendez_vous (id, #user_id, #contact_id, #activite_id, titre, description,
             date_debut, date_fin, heure_debut, heure_fin, created_at, updated_at)

notes (id, #user_id, #rendez_vous_id, #activite_id, titre, commentaire,
       date_create, date_update, created_at, updated_at)

rappels (id, #user_id, #rendez_vous_id, date_rappel, frequence,
         created_at, updated_at)

emails (id, #user_id, #contact_id, email, created_at, updated_at)

numero_telephones (id, #user_id, #contact_id, numero_telephone,
                   created_at, updated_at)

statistiques (id, #activite_id, #rendez_vous_id, #contact_id,
              created_at, updated_at)
```

> **Notation :** `#champ` = cl&eacute; &eacute;trang&egrave;re (FK)

---

## Tables d&eacute;taill&eacute;es

### 1. `roles` - R&ocirc;les utilisateurs
| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | bigint | PK, auto-increment | Identifiant |
| nom | varchar | UNIQUE, NOT NULL | Nom du r&ocirc;le (`admin`, `client`) |
| description | varchar | NULL | Description du r&ocirc;le |
| created_at | timestamp | | Horodatage cr&eacute;ation |
| updated_at | timestamp | | Horodatage modification |

### 2. `users` - Utilisateurs
| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | bigint | PK, auto-increment | Identifiant |
| role_id | bigint | FK &rarr; roles.id, NOT NULL, ON DELETE RESTRICT | R&ocirc;le de l'utilisateur |
| nom | varchar | NOT NULL | Nom de famille |
| prenom | varchar | NOT NULL | Pr&eacute;nom |
| email | varchar | UNIQUE, NOT NULL | Adresse email |
| telephone | varchar | NULL | T&eacute;l&eacute;phone |
| rue | varchar | NULL | Rue |
| numero_rue | varchar | NULL | Num&eacute;ro de rue |
| ville | varchar | NULL | Ville |
| code_postal | varchar | NULL | Code postal |
| pays | varchar | NULL | Pays |
| password | varchar | NOT NULL | Mot de passe (hash&eacute;) |
| remember_token | varchar | NULL | Token "Se souvenir de moi" |
| email_verified_at | timestamp | NULL | Date v&eacute;rification email |
| last_login_at | timestamp | NULL | Derni&egrave;re connexion |
| password_reset_token | varchar | NULL | Token de r&eacute;initialisation |
| password_reset_expires | timestamp | NULL | Expiration du token |
| google_id | varchar | NULL | ID OAuth Google |
| apple_id | varchar | NULL | ID OAuth Apple |
| provider | varchar | NULL | Fournisseur OAuth |
| avatar | varchar | NULL | URL photo de profil |
| admin_user_id | bigint | FK &rarr; users.id, NULL | Admin li&eacute; (pour clients) |
| contact_id | bigint | FK &rarr; contacts.id, NULL, ON DELETE SET NULL | Fiche contact li&eacute;e (pour clients) |
| created_at | timestamp | | Horodatage cr&eacute;ation |
| updated_at | timestamp | | Horodatage modification |

### 3. `statuses` - Statuts des contacts
| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | bigint | PK, auto-increment | Identifiant |
| status_client | varchar | NOT NULL | Libell&eacute; du statut |
| created_at | timestamp | | Horodatage cr&eacute;ation |
| updated_at | timestamp | | Horodatage modification |

### 4. `contacts` - Contacts
| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | bigint | PK, auto-increment | Identifiant |
| user_id | bigint | FK &rarr; users.id, NOT NULL, ON DELETE CASCADE | Propri&eacute;taire du contact |
| nom | varchar | NOT NULL | Nom de famille |
| prenom | varchar | NOT NULL | Pr&eacute;nom |
| rue | varchar | NULL | Rue |
| numero | varchar | NULL | Num&eacute;ro de rue |
| ville | varchar | NULL | Ville |
| code_postal | varchar | NULL | Code postal |
| pays | varchar | NULL | Pays |
| state_client | varchar | NULL | &Eacute;tat du client |
| status_id | bigint | FK &rarr; statuses.id, NULL, ON DELETE SET NULL | Statut du contact |
| created_at | timestamp | | Horodatage cr&eacute;ation |
| updated_at | timestamp | | Horodatage modification |

### 5. `activites` - Activit&eacute;s / Services
| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | bigint | PK, auto-increment | Identifiant |
| user_id | bigint | FK &rarr; users.id, NOT NULL, ON DELETE CASCADE | Propri&eacute;taire de l'activit&eacute; |
| nom | varchar | NOT NULL | Nom de l'activit&eacute; |
| description | text | NULL | Description |
| numero_telephone | varchar | NULL | T&eacute;l&eacute;phone de l'activit&eacute; |
| email | varchar | NULL | Email de l'activit&eacute; |
| image | varchar | NULL | Image / logo |
| created_at | timestamp | | Horodatage cr&eacute;ation |
| updated_at | timestamp | | Horodatage modification |

### 6. `contact_activite` - Table pivot Contact &harr; Activit&eacute; (N:M)
| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | bigint | PK, auto-increment | Identifiant |
| contact_id | bigint | FK &rarr; contacts.id, ON DELETE CASCADE | Contact |
| activite_id | bigint | FK &rarr; activites.id, ON DELETE CASCADE | Activit&eacute; |
| created_at | timestamp | | Horodatage cr&eacute;ation |
| updated_at | timestamp | | Horodatage modification |

> **Contrainte UNIQUE** sur `(contact_id, activite_id)` pour &eacute;viter les doublons.

### 7. `rendez_vous` - Rendez-vous
| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | bigint | PK, auto-increment | Identifiant |
| user_id | bigint | FK &rarr; users.id, NOT NULL, ON DELETE CASCADE | Propri&eacute;taire |
| contact_id | bigint | FK &rarr; contacts.id, NOT NULL, ON DELETE CASCADE | Contact concern&eacute; |
| activite_id | bigint | FK &rarr; activites.id, NOT NULL, ON DELETE CASCADE | Activit&eacute; concern&eacute;e |
| titre | varchar | NOT NULL | Titre du RDV |
| description | text | NULL | Description |
| date_debut | date | NOT NULL | Date de d&eacute;but |
| date_fin | date | NOT NULL | Date de fin |
| heure_debut | time | NOT NULL | Heure de d&eacute;but |
| heure_fin | time | NOT NULL | Heure de fin |
| created_at | timestamp | | Horodatage cr&eacute;ation |
| updated_at | timestamp | | Horodatage modification |

### 8. `notes` - Notes sur les rendez-vous
| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | bigint | PK, auto-increment | Identifiant |
| user_id | bigint | FK &rarr; users.id (ajout&eacute; post-cr&eacute;ation) | Propri&eacute;taire |
| rendez_vous_id | bigint | FK &rarr; rendez_vous.id, NOT NULL, ON DELETE CASCADE | Rendez-vous li&eacute; |
| activite_id | bigint | FK &rarr; activites.id, NULL, ON DELETE SET NULL | Activit&eacute; li&eacute;e (optionnelle) |
| titre | varchar | NOT NULL | Titre de la note |
| commentaire | text | NOT NULL | Contenu |
| date_create | datetime | NOT NULL | Date de cr&eacute;ation manuelle |
| date_update | datetime | NOT NULL | Date de mise &agrave; jour manuelle |
| created_at | timestamp | | Horodatage cr&eacute;ation |
| updated_at | timestamp | | Horodatage modification |

### 9. `rappels` - Rappels
| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | bigint | PK, auto-increment | Identifiant |
| user_id | bigint | FK &rarr; users.id (ajout&eacute; post-cr&eacute;ation) | Propri&eacute;taire |
| rendez_vous_id | bigint | FK &rarr; rendez_vous.id, NOT NULL, ON DELETE CASCADE | Rendez-vous li&eacute; |
| date_rappel | datetime | NOT NULL | Date/heure du rappel |
| frequence | varchar | NOT NULL | Fr&eacute;quence (Une fois, Quotidien, Hebdomadaire, Mensuel) |
| created_at | timestamp | | Horodatage cr&eacute;ation |
| updated_at | timestamp | | Horodatage modification |

### 10. `emails` - Emails des contacts
| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | bigint | PK, auto-increment | Identifiant |
| user_id | bigint | FK &rarr; users.id (ajout&eacute; post-cr&eacute;ation) | Propri&eacute;taire |
| contact_id | bigint | FK &rarr; contacts.id, NOT NULL, ON DELETE CASCADE | Contact li&eacute; |
| email | varchar | NOT NULL | Adresse email |
| created_at | timestamp | | Horodatage cr&eacute;ation |
| updated_at | timestamp | | Horodatage modification |

### 11. `numero_telephones` - T&eacute;l&eacute;phones des contacts
| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | bigint | PK, auto-increment | Identifiant |
| user_id | bigint | FK &rarr; users.id (ajout&eacute; post-cr&eacute;ation) | Propri&eacute;taire |
| contact_id | bigint | FK &rarr; contacts.id, NOT NULL, ON DELETE CASCADE | Contact li&eacute; |
| numero_telephone | varchar | NOT NULL | Num&eacute;ro de t&eacute;l&eacute;phone |
| created_at | timestamp | | Horodatage cr&eacute;ation |
| updated_at | timestamp | | Horodatage modification |

### 12. `statistiques` - Statistiques
| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | bigint | PK, auto-increment | Identifiant |
| activite_id | bigint | FK &rarr; activites.id, NOT NULL, ON DELETE CASCADE | Activit&eacute; |
| rendez_vous_id | bigint | FK &rarr; rendez_vous.id, NOT NULL, ON DELETE CASCADE | Rendez-vous |
| contact_id | bigint | FK &rarr; contacts.id, NOT NULL, ON DELETE CASCADE | Contact |
| created_at | timestamp | | Horodatage cr&eacute;ation |
| updated_at | timestamp | | Horodatage modification |

### Tables syst&egrave;me Laravel

| Table | Description |
|---|---|
| `password_reset_tokens` | Tokens de r&eacute;initialisation de mot de passe (PK: email) |
| `sessions` | Sessions utilisateurs (PK: id string, FK: user_id) |
| `cache` | Cache applicatif (PK: key) |
| `cache_locks` | Verrous de cache |
| `jobs` | File d'attente de jobs |
| `job_batches` | Lots de jobs |
| `failed_jobs` | Jobs &eacute;chou&eacute;s |
| `migrations` | Suivi des migrations |

---

## Relations et cardinalit&eacute;s

### Diagramme des relations (format texte)

```
                    +--------+
                    | roles  |
                    +--------+
                       | 1
                       |
                       | N
+--------+  1    N  +--------+  1    N  +-----------+
| users  |--------->| users  |--------->| activites |
| (admin)|  admin   |(client)|  owner   +-----------+
+--------+  _user   +--------+              |1   \N
    |1      _id        |0..1               |     \
    |                  |contact_id         |N     \N:M
    |N                 v                   |       \
+-----------+  0..1  +-----------+    +-----------+ +------------------+
| contacts  |<-------| users     |    |rendez_vous| | contact_activite |
+-----------+        | (client)  |    +-----------+ +------------------+
  |1  |1  \N:M       +-----------+      |1    |1
  |   |    \                            |     |
  |N  |N    \                          |N    |N
+------+ +----------+            +-------+ +--------+
|emails| |num_tel   |            | notes | | rappels|
+------+ +----------+            +-------+ +--------+
```

### R&eacute;sum&eacute; des FK et ON DELETE

| Table source | Colonne FK | Table cible | ON DELETE |
|---|---|---|---|
| users | role_id | roles | RESTRICT |
| users | admin_user_id | users | (d&eacute;faut) |
| users | contact_id | contacts | SET NULL |
| contacts | user_id | users | CASCADE |
| contacts | status_id | statuses | SET NULL |
| activites | user_id | users | CASCADE |
| contact_activite | contact_id | contacts | CASCADE |
| contact_activite | activite_id | activites | CASCADE |
| rendez_vous | user_id | users | CASCADE |
| rendez_vous | contact_id | contacts | CASCADE |
| rendez_vous | activite_id | activites | CASCADE |
| notes | rendez_vous_id | rendez_vous | CASCADE |
| notes | activite_id | activites | SET NULL |
| rappels | rendez_vous_id | rendez_vous | CASCADE |
| emails | contact_id | contacts | CASCADE |
| numero_telephones | contact_id | contacts | CASCADE |
| statistiques | activite_id | activites | CASCADE |
| statistiques | rendez_vous_id | rendez_vous | CASCADE |
| statistiques | contact_id | contacts | CASCADE |

---

## Donn&eacute;es de r&eacute;f&eacute;rence (Seeds)

### R&ocirc;les (2)
| ID | Nom | Description |
|---|---|---|
| 1 | admin | Entrepreneur/Ind&eacute;pendant - acc&egrave;s complet |
| 2 | client | Client - consultation des rendez-vous uniquement |

### Statuts (8)
| Statut | Description |
|---|---|
| Prospect | Nouveau contact potentiel |
| Client actif | Client en cours |
| Client inactif | Client dormant |
| Lead qualifi&eacute; | Contact qualifi&eacute; |
| Lead non qualifi&eacute; | Contact non qualifi&eacute; |
| En n&eacute;gociation | N&eacute;gociation en cours |
| Ferm&eacute; gagn&eacute; | Affaire conclue |
| Ferm&eacute; perdu | Affaire perdue |

### Activit&eacute;s (3 exemples)
- Consultation M&eacute;dicale
- Coaching Personnel
- Formation Professionnelle

### Utilisateurs test
| Email | Mot de passe | R&ocirc;le |
|---|---|---|
| admin@agenda.com | password | admin |
| client@agenda.com | password | client |

---

## Analyse et remarques

### Points forts du mod&egrave;le
1. **Bonne normalisation** : Emails et t&eacute;l&eacute;phones dans des tables s&eacute;par&eacute;es (1NF &agrave; 3NF respect&eacute;es)
2. **Relation N:M** contacts &harr; activit&eacute;s correctement mod&eacute;lis&eacute;e avec `contact_activite`
3. **Multi-tenant** : Chaque entit&eacute; est li&eacute;e &agrave; un `user_id` pour l'isolation des donn&eacute;es
4. **Self-referencing** : `admin_user_id` dans `users` pour la hi&eacute;rarchie admin/client
5. **Double lien user &harr; contact** : Un user-client peut &ecirc;tre li&eacute; &agrave; une fiche contact d'un admin

### Points &agrave; discuter / Am&eacute;liorer

#### 1. `emails` et `activites` - Pas de lien direct
Les emails appartiennent aux **contacts**, pas aux activit&eacute;s. Le champ `email` dans `activites` est un email de **contact propre &agrave; l'activit&eacute;** (ex: le mail de la salle de consultation), pas une FK vers la table `emails`. C'est correct mais porte &agrave; confusion.

#### 2. `statistiques` - Table de tracking, pas de pr&eacute;-agr&eacute;gation
Dans le code actuel, `statistiques` stocke des triplets `(activite_id, rendez_vous_id, contact_id)` - c'est une **table de tracking/log** qui enregistre chaque &eacute;v&eacute;nement. Ce n'est PAS une vue mat&eacute;rialis&eacute;e avec mois/ann&eacute;e (contrairement &agrave; ce que sugg&egrave;re l'ancienne doc). Dans un MCD pur, on pourrait la consid&eacute;rer comme une **association ternaire** plut&ocirc;t qu'une entit&eacute; propre.

#### 3. `notes.activite_id` - Double rattachement
Une note est **toujours** li&eacute;e &agrave; un `rendez_vous_id` (NOT NULL). Le `activite_id` est optionnel (NULL) et permet de cat&eacute;goriser la note par activit&eacute; en plus du RDV. C'est une commodit&eacute; de filtrage, pas un rattachement alternatif.

#### 4. `users.contact_id` - Lien bidirectionnel
Quand un admin cr&eacute;e un compte client pour un de ses contacts, `contact_id` permet de lier ce user-client &agrave; sa fiche contact chez l'admin. Le client ne voit alors que les RDV de cette fiche contact.

#### 5. Redondance `state_client` vs `status_id`
La table `contacts` a &agrave; la fois `state_client` (varchar libre) et `status_id` (FK vers `statuses`). C'est potentiellement redondant - un seul m&eacute;canisme de statut serait pr&eacute;f&eacute;rable.

#### 6. `user_id` ajout&eacute; apr&egrave;s coup
Les tables `notes`, `rappels`, `emails`, `numero_telephones` ont re&ccedil;u `user_id` via des migrations ult&eacute;rieures pour le multi-tenant. Ces colonnes sont potentiellement NULL sur les anciennes donn&eacute;es.

---

*Derni&egrave;re mise &agrave; jour : 2026-03-17*
