# ProContact v14 — Testing Guide

## Quick Start

```bash
# From project root:
./testing-environment/setup.sh
php artisan serve --host=0.0.0.0 --port=8000
```

Then open: **http://localhost:8000**

## Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@procontact.test | password |

## UC1 — Create a Contact

1. Login as admin
2. Navigate to **Contacts** (sidebar or /contacts)
3. Click **"Créer"** or go to /contacts/create
4. Fill in: Nom, Prénom
5. Add Email (click **"+ Ajouter Email"** for more)
6. Add Phone (click **"+ Ajouter Téléphone"** for more)
7. Click **"Créer le Contact"**
8. Verify: success message, contact appears in list

### Test validation:
- Leave Nom empty → red border + error message
- Enter invalid email (e.g. "abc") → "L'adresse email n'est pas valide."
- Click **"Annuler"** → returns to list, no data saved

## UC2 — Create an Activity

1. Navigate to **Activités** (/activites)
2. Verify activities display as **cards**
3. Click **"Créer Nouvelle Activité"**
4. Fill in: Nom (required), Description (required)
5. Optionally add: Email, Phone, Image
6. Click **"Créer l'Activité"**
7. Verify: success message, new card visible on dashboard

### Test validation:
- Leave Name empty → red highlight, no save
- Leave Description empty → red highlight, no save

## UC3 — Client Portal (Magic-Link)

### Find the portal link:
The portal links are shown when running the seeder. You can also find them in the database:
```bash
php artisan tinker
> App\Models\Contact::whereNotNull('portal_token')->get(['prenom', 'nom', 'portal_token'])
```

### Test nominal flow:
1. Open portal link: **http://localhost:8000/portal/{token}**
2. Verify: appointment list is shown (no login required!)
3. Click on an appointment → detail page
4. Verify: title, dates, times, activity are displayed
5. If shared notes exist → they appear below details
6. Fill the **"Laisser un message"** form → click **"Envoyer"**
7. Verify: "Votre message a été envoyé avec succès."

### Test no shared notes:
- Appointment with no shared notes → "Aucune note partagée pour ce rendez-vous."
- The message form is still visible

### Test empty message:
- Leave the message empty, click **"Envoyer"**
- Verify: red border, "Ce champ est obligatoire."

### Test invalid token:
- Visit **http://localhost:8000/portal/invalidtoken123**
- Verify: error page "Ce lien n'est plus valide. Veuillez contacter votre prestataire de services."
- No appointment or contact data is shown

## Emails

Emails are logged (not sent). To see them:
```bash
tail -100 storage/logs/laravel.log | grep -A 50 "portal"
```

## Contact Status Auto-Promotion

1. Create a new contact (no appointments) → status shows **"Contact"**
2. Create an appointment for that contact → status changes to **"Client"**
3. Delete all appointments for that contact → status reverts to **"Contact"**

## Running Tests

```bash
# Unit + Feature tests
php artisan test

# Browser tests (requires Chrome)
php artisan dusk
```
