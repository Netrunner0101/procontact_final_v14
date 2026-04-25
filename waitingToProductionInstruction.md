# Waiting List to Production — Launch Instructions

## Overview

Pro Contact currently runs in **waitlist mode**. Two landing page files exist:

| File | Status | Description |
|------|--------|-------------|
| `resources/views/welcome.blade.php` | **Active** | Waitlist mode — displays the Tally signup form (ID: `2EL9Je`) |
| `resources/views/welcome-production.blade.php` | **Ready** | Production mode — login, register, and CTA buttons enabled |

---

## Step 1: Export Tally Submissions

1. Go to [https://tally.so](https://tally.so) and log in
2. Open the **Pro Contact** form
3. Navigate to **Submissions**
4. Click **Export** and download the CSV file
5. The CSV contains: **Name**, **Email**, **Company**, **Type** (Freelancer/Agency), and **Business description**

Save this file — you will use it to send invitation emails.

---

## Step 2: Switch the Landing Page to Production

Replace the waitlist landing page with the production version:

```bash
# From the project root directory
cp resources/views/welcome.blade.php resources/views/welcome-waitlist-backup.blade.php
cp resources/views/welcome-production.blade.php resources/views/welcome.blade.php
```

This will:
- Back up the current waitlist page (just in case)
- Activate the production landing page with:
  - **Navigation**: Connexion / S'inscrire buttons
  - **Hero**: "Commencer gratuitement" and "Se connecter" buttons
  - **Bottom CTA**: "Prêt a commencer ? — Creer mon compte" section
  - **No more Tally embed** or Tally script

---

## Step 3: Deploy

Deploy the updated application to your production environment using your usual deployment process.

---

## Step 4: Send Invitation Emails

Using the CSV exported in Step 1, send an email to each waitlist subscriber.

**Suggested email template:**

> **Subject:** Pro Contact est maintenant disponible !
>
> Bonjour {Name},
>
> Merci de vous etre inscrit(e) sur la liste d'attente de Pro Contact.
>
> Nous sommes ravis de vous annoncer que l'application est maintenant disponible !
> Creez votre compte des maintenant :
>
> **[Creer mon compte](https://procontact.be/register)**
>
> A bientot,
> L'equipe Pro Contact

You can send these emails using tools like:
- **Mailchimp** — import the CSV and create a campaign
- **Brevo (ex-Sendinblue)** — import contacts and send a template
- **A custom script** — loop through the CSV and send via SMTP or an API

---

## Step 5: Post-Launch Verification

After deploying, verify:

- [ ] Landing page shows login and register buttons (not the Tally form)
- [ ] `/register` page works and creates new accounts
- [ ] `/login` page works for returning users
- [ ] Google and Apple OAuth still function correctly
- [ ] Invitation email links point to the correct domain

---

## Optional: Cleanup

Once you are confident everything works, you can remove the backup and production template files:

```bash
rm resources/views/welcome-waitlist-backup.blade.php
rm resources/views/welcome-production.blade.php
```
