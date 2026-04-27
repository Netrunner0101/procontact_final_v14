# MCD — BEFORE update

**Pro Contact — Modèle Conceptuel de Données (état initial)**

Source: `MD/milestone3_MCD_MLDR_ClassDiagram.md`, `MD/MCD_ProContact.puml`
Stack: Laravel 11 / PostgreSQL

---

## 1. Diagramme entité-association (Mermaid)

```mermaid
erDiagram
    ROLE ||--o{ UTILISATEUR : "posseder (1,1)"
    UTILISATEUR ||--o{ CONTACT : "creer (0,N)"
    UTILISATEUR ||--o{ ACTIVITE : "creer (0,N)"
    UTILISATEUR ||--o{ RENDEZ_VOUS : "administrer (0,N)"
    UTILISATEUR ||--o{ UTILISATEUR : "administrer clients (0,N)"
    STATUS ||--o{ CONTACT : "avoir (0,1)"
    CONTACT ||--o{ EMAIL : "posseder (0,N)"
    CONTACT ||--o{ NUMERO_TELEPHONE : "posseder (0,N)"
    CONTACT ||--o{ RENDEZ_VOUS : "participer (0,N)"
    ACTIVITE ||--o{ RENDEZ_VOUS : "concerner (0,N)"
    CONTACT }o--o{ ACTIVITE : "participer N:M"
    RENDEZ_VOUS ||--o{ NOTE : "avoir (0,N)"
    RENDEZ_VOUS ||--o{ RAPPEL : "avoir (0,N)"
    RENDEZ_VOUS ||--o{ STATISTIQUE : "generer (0,N)"
    ACTIVITE ||--o{ STATISTIQUE : "generer (0,N)"
    CONTACT ||--o{ STATISTIQUE : "generer (0,N)"

    ROLE { string nom; string description }
    UTILISATEUR {
        string nom
        string prenom
        string email
        string telephone
        string adresse
        string password
        string layout_preference
    }
    STATUS { string status_client }
    CONTACT {
        string nom
        string prenom
        string adresse
        string state_client
        string portal_token
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
    }
    NOTE {
        string titre
        text commentaire
        boolean is_shared_with_client
        datetime date_create
        datetime date_update
    }
    RAPPEL {
        datetime date_rappel
        string frequence
    }
    EMAIL { string email }
    NUMERO_TELEPHONE { string numero_telephone }
    STATISTIQUE { int activite_id; int rendez_vous_id; int contact_id }
```

## 2. Cardinalités

| Association | A | Card. A | B | Card. B |
|-------------|---|:-------:|---|:-------:|
| posséder | ROLE | 1,N | UTILISATEUR | 1,1 |
| créer | UTILISATEUR | 1,1 | CONTACT | 0,N |
| créer | UTILISATEUR | 1,1 | ACTIVITE | 0,N |
| administrer | UTILISATEUR | 1,1 | RENDEZ_VOUS | 0,N |
| administrer (clients) | UTILISATEUR (admin) | 0,N | UTILISATEUR (client) | 0,1 |
| avoir | STATUS | 0,N | CONTACT | 0,1 |
| posséder | CONTACT | 1,1 | EMAIL | 0,N |
| posséder | CONTACT | 1,1 | NUMERO_TELEPHONE | 0,N |
| participer | CONTACT | 0,N | RENDEZ_VOUS | 1,1 |
| concerner | ACTIVITE | 0,N | RENDEZ_VOUS | 1,1 |
| participer (pivot) | CONTACT | 0,N | ACTIVITE | 0,N |
| avoir | RENDEZ_VOUS | 0,N | NOTE | 1,1 |
| avoir | RENDEZ_VOUS | 0,N | RAPPEL | 1,1 |
| générer | RENDEZ_VOUS / ACTIVITE / CONTACT | 0,N | STATISTIQUE | 1,1 |

## 3. Caractéristiques de l'état initial

- **Portail client** = jeton unique (`contacts.portal_token`) — magic-link permanent, aucune vérification d'identité.
- **STATISTIQUE** = entité présente, prévue pour stocker des agrégats par activité / rdv / contact.
- **NOTE** = appartient à un rendez-vous (`rendez_vous_id` obligatoire) ; un drapeau `is_shared_with_client` existe mais n'est pas exposé dans les formulaires.
- **RAPPEL** = date + fréquence uniquement, pas de destinataire ni de CC.
- **RENDEZ_VOUS** = pas de champ `statut`.
- Aucune entité de **modèle de note** (note_templates).
- Aucune entité d'authentification portail (OTP, jeton hashé, appareil de confiance, journal d'accès).
