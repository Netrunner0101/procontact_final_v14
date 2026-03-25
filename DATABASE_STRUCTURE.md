# PostgreSQL Database Structure - Laravel Multi-Business Agenda

## Database Configuration
- **Host:** localhost
- **Port:** 5445
- **Database:** agenda_app
- **Username:** postgres
- **Password:** root

## Tables Overview (19 total)

### Core Business Tables

#### 1. users
- `id` (bigint, primary key)
- `nom` (varchar) - Last name
- `prenom` (varchar) - First name
- `email` (varchar, unique)
- `telephone` (varchar)
- `rue` (varchar) - Street
- `numero_rue` (varchar) - Street number
- `ville` (varchar) - City
- `code_postal` (varchar) - Postal code
- `pays` (varchar) - Country
- `email_verified_at` (timestamp)
- `password` (varchar)
- `remember_token` (varchar)
- `last_login_at` (timestamp)
- `password_reset_token` (varchar)
- `password_reset_expires` (timestamp)
- `google_id` (varchar) - Google OAuth ID
- `apple_id` (varchar) - Apple OAuth ID
- `provider` (varchar) - OAuth provider
- `avatar` (varchar) - Profile picture
- `role` (varchar) - admin/client
- `admin_user_id` (bigint) - For client-admin relationship
- `created_at`, `updated_at` (timestamps)

#### 2. contacts
- `id` (bigint, primary key)
- `user_id` (bigint, foreign key to users)
- `nom` (varchar) - Last name
- `prenom` (varchar) - First name
- `rue` (varchar) - Street
- `numero` (varchar) - Street number
- `ville` (varchar) - City
- `code_postal` (varchar) - Postal code
- `pays` (varchar) - Country
- `state_client` (varchar) - Client state
- `status_id` (bigint, foreign key to statuses)
- `created_at`, `updated_at` (timestamps)

#### 3. statuses
- `id` (bigint, primary key)
- `status_client` (varchar) - Status name
- `created_at`, `updated_at` (timestamps)

#### 4. activites
- `id` (bigint, primary key)
- `nom` (varchar) - Activity name
- `description` (text) - Activity description
- `user_id` (bigint, foreign key to users)
- `created_at`, `updated_at` (timestamps)

#### 5. rendez_vous (appointments)
- `id` (bigint, primary key)
- `user_id` (bigint, foreign key to users)
- `contact_id` (bigint, foreign key to contacts)
- `activite_id` (bigint, foreign key to activites)
- `titre` (varchar) - Appointment title
- `description` (text) - Appointment description
- `date_debut` (date) - Start date
- `date_fin` (date) - End date
- `heure_debut` (time) - Start time
- `heure_fin` (time) - End time
- `created_at`, `updated_at` (timestamps)

#### 6. notes
- `id` (bigint, primary key)
- `user_id` (bigint, foreign key to users) - **FIXED**
- `rendez_vous_id` (bigint, foreign key to rendez_vous)
- `activite_id` (bigint, foreign key to activites)
- `titre` (varchar) - Note title
- `commentaire` (text) - Note content
- `date_create` (timestamp) - Creation date
- `date_update` (timestamp) - Update date
- `created_at`, `updated_at` (timestamps)

#### 7. rappels (reminders)
- `id` (bigint, primary key)
- `user_id` (bigint, foreign key to users) - **ADDED**
- `rendez_vous_id` (bigint, foreign key to rendez_vous)
- `date_rappel` (timestamp) - Reminder date
- `frequence` (varchar) - Frequency (Une fois, Quotidien, etc.)
- `created_at`, `updated_at` (timestamps)

#### 8. emails
- `id` (bigint, primary key)
- `user_id` (bigint, foreign key to users) - **ADDED**
- `contact_id` (bigint, foreign key to contacts)
- `email` (varchar) - Email address
- `created_at`, `updated_at` (timestamps)

#### 9. numero_telephones
- `id` (bigint, primary key)
- `user_id` (bigint, foreign key to users) - **ADDED**
- `contact_id` (bigint, foreign key to contacts)
- `numero_telephone` (varchar) - Phone number
- `created_at`, `updated_at` (timestamps)

#### 10. statistiques
- `id` (bigint, primary key)
- `user_id` (bigint, foreign key to users)
- `activite_id` (bigint, foreign key to activites)
- `mois` (integer) - Month
- `annee` (integer) - Year
- `nombre_contacts` (integer) - Number of contacts
- `nombre_rendez_vous` (integer) - Number of appointments
- `created_at`, `updated_at` (timestamps)

### Pivot Tables

#### 11. contact_activite
- `id` (bigint, primary key)
- `contact_id` (bigint, foreign key to contacts)
- `activite_id` (bigint, foreign key to activites)
- `created_at`, `updated_at` (timestamps)

### Laravel System Tables

#### 12. migrations
- Migration tracking table

#### 13. cache & cache_locks
- Application caching tables

#### 14. jobs & job_batches & failed_jobs
- Queue management tables

#### 15. sessions
- Session management table

#### 16. password_reset_tokens
- Password reset functionality

## Seed Data

### Users (3 total)
1. **Admin User**
   - Email: admin@agenda.com
   - Password: password
   - Role: admin

2. **Client User**
   - Email: client@agenda.com
   - Password: password
   - Role: client
   - Admin: linked to admin user

### Statuses (8 total)
- Prospect
- Client actif
- Client inactif
- Lead qualifié
- Lead non qualifié
- En négociation
- Fermé gagné
- Fermé perdu

### Activities (3 total)
- Consultation Médicale
- Coaching Personnel
- Formation Professionnelle

### Contacts (2 total)
- Jean Dupont (Paris)
- Marie Martin (Lyon)

## Recent Fixes Applied

1. **Fixed Migration Order:** Reordered migrations to ensure statuses table is created before contacts table
2. **Added Missing user_id Columns:** Added user_id foreign key to:
   - notes table
   - rappels table
   - emails table
   - numero_telephones table
3. **Fixed Seed Data:** Updated seeders to match actual table structures
4. **Database Connection:** Successfully configured PostgreSQL connection

## Application Status
✅ All 19 tables created successfully
✅ All foreign key relationships established
✅ Seed data populated
✅ Application running on http://127.0.0.1:8000
✅ Database structure validated and working

## Login Credentials
- **Admin:** admin@agenda.com / password
- **Client:** client@agenda.com / password
