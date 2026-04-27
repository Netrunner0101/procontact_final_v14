# MCD — AFTER update

**Pro Contact — Modèle Conceptuel de Données (état actuel)**

Stack: Laravel 11 / PostgreSQL

> `STATISTIQUE` est conservée comme entité réservée — schéma stable, prête pour des agrégats matérialisés futurs. Aujourd'hui les chiffres sont calculés à la volée par `StatistiqueController`, mais l'entité reste dans le modèle.

---

## 1. Diagramme entité-association (Mermaid)

```mermaid
erDiagram
    ROLE ||--o{ UTILISATEUR : "posseder (1,1)"
    UTILISATEUR ||--o{ CONTACT : "creer (0,N)"
    UTILISATEUR ||--o{ ACTIVITE : "creer (0,N)"
    UTILISATEUR ||--o{ RENDEZ_VOUS : "administrer (0,N)"
    UTILISATEUR ||--o{ UTILISATEUR : "administrer clients (0,N)"
    UTILISATEUR ||--o{ NOTE_TEMPLATE : "rediger (0,N)"
    STATUS ||--o{ CONTACT : "avoir (0,1)"
    CONTACT ||--o{ EMAIL : "posseder (0,N)"
    CONTACT ||--o{ NUMERO_TELEPHONE : "posseder (0,N)"
    CONTACT ||--o{ RENDEZ_VOUS : "participer (0,N)"
    CONTACT ||--o{ NOTE_TEMPLATE : "cibler (0,N)"
    CONTACT ||--o{ CLIENT_PORTAL_TOKEN : "authentifier (0,N)"
    CONTACT ||--o{ CLIENT_PORTAL_OTP : "verifier (0,N)"
    CONTACT ||--o{ CLIENT_PORTAL_TRUSTED_DEVICE : "memoriser (0,N)"
    CONTACT ||--o{ CLIENT_PORTAL_ACCESS_LOG : "tracer (0,N)"
    ACTIVITE ||--o{ RENDEZ_VOUS : "concerner (0,N)"
    CONTACT }o--o{ ACTIVITE : "participer N:M"
    RENDEZ_VOUS ||--o{ NOTE : "avoir (0,N)"
    RENDEZ_VOUS ||--o{ RAPPEL : "avoir (0,N)"
    RENDEZ_VOUS ||--o{ STATISTIQUE : "generer-reserve (0,N)"
    ACTIVITE ||--o{ STATISTIQUE : "generer-reserve (0,N)"
    CONTACT ||--o{ STATISTIQUE : "generer-reserve (0,N)"

    ROLE { string nom; string description }
    UTILISATEUR {
        string nom
        string prenom
        string email
        string telephone
        string adresse
        string password
        string layout_preference
        datetime last_login_at
    }
    STATUS { string status_client }
    CONTACT {
        string nom
        string prenom
        string adresse
        string state_client
    }
    ACTIVITE {
        string nom
        text description
        string numero_telephone
        string email
        string image
    }
    RENDEZ_VOUS {
        string titre
        text description
        date date_debut
        date date_fin
        time heure_debut
        time heure_fin
        string statut
    }
    NOTE {
        string titre
        text commentaire
        boolean is_shared_with_client
        datetime date_create
        datetime date_update
    }
    NOTE_TEMPLATE {
        string titre
        text commentaire
    }
    RAPPEL {
        datetime date_rappel
        string frequence
        string destinataire
        text emails_cc
    }
    EMAIL { string email }
    NUMERO_TELEPHONE { string numero_telephone }
    STATISTIQUE {
        int activite_id
        int rendez_vous_id
        int contact_id
    }
    CLIENT_PORTAL_TOKEN {
        uuid id
        string token_hash
        datetime last_used_at
        datetime revoked_at
    }
    CLIENT_PORTAL_OTP {
        uuid id
        string code_hash
        string email_hash
        int attempts
        datetime expires_at
        datetime consumed_at
        string ip_address
    }
    CLIENT_PORTAL_TRUSTED_DEVICE {
        uuid id
        string cookie_hash
        string user_agent_hash
        string ip_address_first_seen
        datetime last_used_at
        datetime expires_at
        datetime revoked_at
    }
    CLIENT_PORTAL_ACCESS_LOG {
        string event
        string ip_address
        string user_agent_hash
        json metadata
        datetime created_at
    }
```

## 2. Cardinalités (deltas)

| Association | A | Card. A | B | Card. B | Δ |
|-------------|---|:-------:|---|:-------:|---|
| rédiger | UTILISATEUR | 1,1 | NOTE_TEMPLATE | 0,N | **+** |
| cibler | CONTACT | 0,1 | NOTE_TEMPLATE | 0,N | **+** |
| authentifier | CONTACT | 1,1 | CLIENT_PORTAL_TOKEN | 0,N | **+** |
| vérifier | CONTACT | 1,1 | CLIENT_PORTAL_OTP | 0,N | **+** |
| mémoriser | CONTACT | 1,1 | CLIENT_PORTAL_TRUSTED_DEVICE | 0,N | **+** |
| tracer | CONTACT | 0,1 | CLIENT_PORTAL_ACCESS_LOG | 0,N | **+** |
| générer | RENDEZ_VOUS / ACTIVITE / CONTACT | — | STATISTIQUE | — | **réservée (non alimentée à ce jour)** |

## 3. Changements vs. état initial

### Entités ajoutées (5)

| Entité | Rôle |
|--------|------|
| `NOTE_TEMPLATE` | Modèles de note réutilisables par utilisateur, optionnellement liés à un contact |
| `CLIENT_PORTAL_TOKEN` | Jeton de session portail — UUID + empreinte SHA-256, révocable |
| `CLIENT_PORTAL_OTP` | Code à usage unique (6 chiffres) — empreinte du code et de l'e-mail, expire en 10 min |
| `CLIENT_PORTAL_TRUSTED_DEVICE` | Cookie « se souvenir de cet appareil » — empreinte du cookie + UA, expire après 30 j |
| `CLIENT_PORTAL_ACCESS_LOG` | Journal RGPD des événements portail (login, échec OTP, révocation…) |

### Entités réservées (1)

| Entité | Raison |
|--------|--------|
| `STATISTIQUE` | Conservée mais non alimentée. Aucun code n'écrit ni ne lit la table aujourd'hui ; les statistiques sont calculées à la volée à partir de `CONTACTS / ACTIVITES / RENDEZ_VOUS / RAPPELS / NOTES`. Schéma stable pour matérialisation future (snapshots historiques, cache d'agrégats). |

### Attributs ajoutés / modifiés

| Entité | Attribut | Δ |
|--------|----------|---|
| `RENDEZ_VOUS` | `statut` (string, default `'scheduled'`) | **+** |
| `RAPPEL` | `destinataire` (string, default `'Les deux'`) | **+** |
| `RAPPEL` | `emails_cc` (text, nullable) | **+** |
| `CONTACT` | `portal_token` | **— (remplacé par `client_portal_tokens` hashés)** |

### Effet métier

- Le portail client passe d'un **lien magique permanent en clair** à une **authentification à deux étapes** (e-mail + OTP) avec session révocable et appareil de confiance.
- Les notes peuvent désormais être **sauvegardées comme modèles** réutilisables (`note_templates`).
- Les rappels peuvent **adresser explicitement** le contact, le créateur, ou les deux, avec une liste de destinataires CC.
- Le rendez-vous porte un **état métier** (`scheduled`, `done`, `cancelled`, …).
