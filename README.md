# ProContact

CRM léger pour indépendants et petites équipes — Laravel 12 + Livewire 3, bilingue FR/EN.

## Fonctionnalités

- **Activités, Contacts, Rendez-vous, Rappels, Notes** — modèle de domaine en français, scopé par utilisateur (multi-tenant naturel).
- **Portail client RGPD** — accès sécurisé par OTP courriel + appareils de confiance, journal d'accès, purge automatique.
- **Authentification** — comptes locaux + OAuth Google et Apple via Laravel Socialite.
- **Notifications courriel** — confirmation de RDV avec CC/BCC, demandes de consentement RGPD, notifications de suppression, rappels programmés.
- **Internationalisation** — interface complète en français et en anglais, bascule via `/lang/fr` ou `/lang/en`.
- **Sécurité** — Sanctum pour l'API, déconnexion sur inactivité, validation stricte, anonymisation à la suppression.

## Stack

- PHP 8.2+
- Laravel 12
- Livewire 3 + Alpine.js
- Tailwind CSS
- Sanctum (API), Socialite (OAuth), Resend (courriel)
- MySQL / PostgreSQL / SQLite

## Installation locale

```bash
git clone https://github.com/Netrunner0101/procontact_final_v14.git
cd procontact_final_v14

composer install
npm install

cp .env.example .env
php artisan key:generate
# Configurer DB_*, MAIL_MAILER=resend, RESEND_API_KEY, OAuth si besoin

php artisan migrate --seed

npm run dev          # assets
php artisan serve    # http://localhost:8000
```

## Commandes utiles

| Commande | Description |
|---|---|
| `php artisan test` | Lance la suite PHPUnit |
| `vendor/bin/pint` | Formatage PHP (Laravel Pint) |
| `php artisan mail:diagnose [email]` | Diagnostic configuration courriel + envoi test |
| `php artisan portal:purge` | Purge OTP expirés, appareils de confiance, logs d'accès (rétention RGPD) |
| `php artisan queue:work` | Worker pour les jobs en file d'attente |

## Conventions

- **Domaine en français** : modèles, tables et URLs utilisent les termes métier français (`Activite`, `RendezVous`, `Rappel`, `NumeroTelephone`, etc.).
- **Multi-tenant par `user_id`** : chaque ressource appartient à un utilisateur, filtrage systématique dans les controllers et policies.
- **Branches `claude/*`** : utilisées pour les PR générées via Claude Code.
- **Workflow git** : développement sur `dev`, fusion vers `main` pour les releases.

## Licence

MIT.
