# ProContact — Production Deployment & Operations

This document captures the production setup for `procontact.app`, common operational tasks, and known gotchas. Read this before making changes to the prod environment.

---

## Stack overview

| Layer | Provider | Notes |
|---|---|---|
| App | Laravel (PHP 8.4) | Repo: `Netrunner0101/procontact_final_v14`, branch `main` |
| Server | Hetzner via Laravel Forge | Site: `procontact_final_v14-evouuipp.on-forge.com` |
| DNS | Cloudflare | Apex `procontact.app` proxied (orange cloud) |
| SSL | Let's Encrypt via Forge | Auto-renews every ~60 days |
| Database | PostgreSQL on Forge server | DB: `Procontact_Prod_Database`, user: `procontact` |
| Cache / Queue / Sessions | Redis on Forge server | All three on `127.0.0.1:6379` |
| Transactional email | Resend | Domain: `procontact.app`, region: `eu-west-1` |
| Inbox (replies) | Proton Mail | `contact@procontact.app` |
| Auth | Laravel Socialite (Google) | OAuth client in Google Cloud project "ProContact" |

**Server filesystem path:** `/home/forge/procontact_final_v14-evouuipp.on-forge.com/current`

**Public IP:** `129.212.171.21`

---

## Critical environment variables

Set in Forge → site → Environment. After any change, run `php artisan config:cache && php artisan queue:restart`.

```env
APP_NAME=ProContact
APP_ENV=production
APP_KEY=base64:...                    # NEVER rotate on a live app
APP_DEBUG=false                        # NEVER set to true in prod
APP_URL=https://procontact.app
APP_LOCALE=fr
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=daily
LOG_LEVEL=error                        # `debug` floods disk, leaks data

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=Procontact_Prod_Database   # mixed-case, but works
DB_USERNAME=procontact
DB_PASSWORD="<in password manager>"

SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
CACHE_STORE=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

MAIL_MAILER=resend
RESEND_API_KEY=re_...                  # NOT `RESEND_KEY` — package expects `RESEND_API_KEY`
MAIL_FROM_ADDRESS="contact@procontact.app"
MAIL_FROM_NAME="${APP_NAME}"

GOOGLE_CLIENT_ID=...apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-...

TRUSTED_PROXIES=*                      # Cloudflare in front; tighten to CF ranges later
TRUSTED_HOSTS=procontact.app,www.procontact.app
```

---

## DNS records (Cloudflare)

13 records total. **Web records** are proxied (orange cloud), **mail records** are DNS-only (gray cloud) — never proxy mail.

| Purpose | Type | Name | Content | Proxy |
|---|---|---|---|---|
| Web | A | `procontact.app` | `129.212.171.21` | Proxied |
| Web | CNAME | `www` | `procontact.app` | Proxied |
| Resend MX | MX | `send` | `feedback-smtp.eu-west-1.amazonses.com` (10) | DNS only |
| Resend SPF | TXT | `send` | `v=spf1 include:amazonses.com ~all` | DNS only |
| Resend DKIM | TXT | `resend._domainkey` | `p=MIGfMA0GCS...` | DNS only |
| Proton verify | TXT | `procontact.app` | `protonmail-verification=...` | DNS only |
| Proton MX 1 | MX | `procontact.app` | `mail.protonmail.ch` (10) | DNS only |
| Proton MX 2 | MX | `procontact.app` | `mailsec.protonmail.ch` (20) | DNS only |
| Combined SPF | TXT | `procontact.app` | `v=spf1 include:_spf.protonmail.ch include:amazonses.com ~all` | DNS only |
| Proton DKIM 1 | CNAME | `protonmail._domainkey` | `protonmail.domainkey.dXXX.domains.proton.ch` | DNS only |
| Proton DKIM 2 | CNAME | `protonmail2._domainkey` | `protonmail2.domainkey.dXXX.domains.proton.ch` | DNS only |
| Proton DKIM 3 | CNAME | `protonmail3._domainkey` | `protonmail3.domainkey.dXXX.domains.proton.ch` | DNS only |
| DMARC | TXT | `_dmarc` | `v=DMARC1; p=none; rua=mailto:postmaster@procontact.app` | DNS only |

> **Only ONE SPF record per hostname.** The apex SPF must `include:` both Proton and Amazon SES (Resend). Never have two `v=spf1` TXT records on the same host.

---

## Required post-deployment seed data

The `roles` table must contain `admin` and `client` rows or Google OAuth signup will throw `ModelNotFoundException` (caught silently in `SocialAuthController`, redirects user back to login with no obvious error).

After any fresh DB or first-time deploy:

```bash
php artisan tinker
```
```php
\App\Models\Role::firstOrCreate(['nom' => \App\Models\Role::ADMIN]);
\App\Models\Role::firstOrCreate(['nom' => \App\Models\Role::CLIENT]);
exit
```

Or run the seeder:

```bash
php artisan db:seed --class=RoleSeeder --force
```

> This is the single most common cause of "Google login redirects me back to login screen" — the roles table being empty.

---

## Deploy script (Forge → site → Deploy Script)

```bash
cd $FORGE_SITE_PATH
git pull origin $FORGE_SITE_BRANCH
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

if [ -f artisan ]; then
    $FORGE_PHP artisan migrate --force
    $FORGE_PHP artisan config:cache
    $FORGE_PHP artisan route:cache
    $FORGE_PHP artisan view:cache
    $FORGE_PHP artisan event:cache
    $FORGE_PHP artisan storage:link
    $FORGE_PHP artisan queue:restart
fi
```

Critical bits:
- `--no-dev` keeps Faker/Telescope/etc. out of prod
- `config:cache` after env changes is mandatory or env vars don't take effect
- `queue:restart` is mandatory or the worker keeps running with old code/env in memory

---

## Queue worker daemon (Forge → site → Daemons)

Required because `QUEUE_CONNECTION=redis`. Without this daemon, queued jobs (including emails) sit in Redis forever.

```
Command: php /home/forge/procontact_final_v14-evouuipp.on-forge.com/current/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
User: forge
Directory: /home/forge/procontact_final_v14-evouuipp.on-forge.com/current
Processes: 1
Stop wait seconds: 60
```

Verify with `sudo supervisorctl status` — should show `RUNNING`.

---

## Common operations

### Check what's actually running

```bash
cd /home/forge/procontact_final_v14-evouuipp.on-forge.com/current
php artisan about                            # one-page env summary
sudo supervisorctl status                    # queue worker status
redis-cli ping                               # Redis health
redis-cli LLEN queues:default                # queued jobs (should be 0 most of the time)
```

### Tail logs

```bash
ls -la storage/logs/                         # find current daily log
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log
```

If log file is empty/missing, no errors have occurred (not a bug — `LOG_LEVEL=error` only writes errors).

### Test transactional email synchronously (bypasses queue)

```bash
php artisan tinker
```
```php
Mail::raw('test', fn($m) => $m->to('your-real-email@example.com')->subject('Test'));
```

Returns `Illuminate\Mail\SentMessage` on success. Check Resend dashboard → Logs to see the API call.

### Test email through the queue

Trigger any in-app action that sends mail (e.g. "Envoyer par courriel" on an appointment). Then:

```bash
redis-cli LLEN queues:default                # job dispatched
# wait a few seconds
redis-cli LLEN queues:default                # should drop to 0 after worker processes
```

### Database dump / restore

```bash
# Dump
PGPASSWORD='<password>' pg_dump -h 127.0.0.1 -U procontact -d Procontact_Prod_Database > /tmp/backup.sql

# Restore (data only, schema must already exist via migrations)
PGPASSWORD='<password>' psql -h 127.0.0.1 -U procontact -d Procontact_Prod_Database < /tmp/backup.sql
```

For prod backups, configure Forge → Server → Database → Backups (S3 or Backblaze B2).

---

## Known gotchas

### "Email is being sent" but nothing arrives
1. `MAIL_MAILER` is still `log` → check `php artisan about | grep -i mail`
2. `RESEND_API_KEY` not set or wrong name (package expects `RESEND_API_KEY`, not `RESEND_KEY`)
3. Queue worker not running → `sudo supervisorctl status`
4. Resend domain not verified → check resend.com → Domains
5. Email landed in spam → check Resend → Logs (shows delivery status)

### Google OAuth redirects back to login screen with no error
Almost always: `roles` table is empty. See "Required post-deployment seed data" above.

Other possibilities:
- `SESSION_DRIVER=redis` but Redis is down → `redis-cli ping`
- Old session cookie in browser pointing to user from a dropped DB → clear cookies
- `SocialAuthController` swallows the exception silently → add logging in the `catch` block

### `redirect_uri_mismatch` error from Google
The redirect URI in Google Cloud Console doesn't exactly match what Laravel sends. Must be:
- `https://procontact.app/auth/google/callback`
- No trailing slash, https not http, exact path match

### Let's Encrypt fails to issue cert
Cloudflare proxy (orange cloud) is intercepting the HTTP-01 challenge.

Fix: temporarily gray-cloud the A records → issue cert in Forge → re-enable proxy. Or use a Cloudflare Origin Certificate instead and set CF SSL mode to Full (strict).

### Cloudflare in front: visitors all show same IP in Laravel logs
Set `TRUSTED_PROXIES=*` in env. For tighter security, use Cloudflare's published IP ranges instead of `*`.

### Migrations show "Nothing to migrate" but tables aren't there
Wrong DB connected. Verify: `php artisan tinker` → `DB::connection()->getDatabaseName()`.

---

## What NOT to do

- Never set `APP_DEBUG=true` in prod (leaks env vars and stack traces)
- Never rotate `APP_KEY` on a live app (corrupts encrypted columns, invalidates all sessions)
- Never run `php artisan migrate:fresh` in prod (drops all tables)
- Never commit `.env` to git
- Never put `RESEND_API_KEY` or `GOOGLE_CLIENT_SECRET` in committed code
- Never use Cloudflare proxy on mail records (MX, mail SPF/DKIM)
- Never have two SPF records on the same hostname
- Never use `p=quarantine` or `p=reject` in DMARC for the first 2-4 weeks (start with `p=none`)
- Never paste credentials into public docs, screenshots, or AI chat logs

---

## TODO / nice-to-haves

- [ ] Set up daily DB backups to Backblaze B2 via Forge
- [ ] Add error tracking (Sentry free tier or Spatie Flare)
- [ ] Lock origin firewall to Cloudflare IP ranges only
- [ ] Submit Google OAuth consent screen for verification (currently in Testing mode, capped at 100 users)
- [ ] Add logging in `SocialAuthController` catch blocks (currently silent)
- [ ] Add `admin_user_id` to `User` model `$fillable` (currently missing — silently dropped on mass assignment)
- [ ] Tighten DMARC to `p=quarantine` after 2-4 weeks of monitoring
- [ ] Tighten `TRUSTED_PROXIES` from `*` to actual Cloudflare ranges
- [ ] Decide on file storage (currently `local`; switch to S3/B2 if user uploads grow)
- [ ] Add a `RoleSeeder` to `database/seeders/` and call it from `DatabaseSeeder` so fresh deploys auto-seed
- [ ] Consider Nightwatch (Laravel observability) — already evaluated, free tier is enough for pre-launch

---

## Quick reference: Forge resources

- Server: `gorgeous-beijing` (Hetzner)
- Site: `procontact_final_v14-evouuipp.on-forge.com` with custom domain `procontact.app`
- Repo branch: `main`
- Quick Deploy: enabled (push to `main` triggers deploy)

## Quick reference: external accounts

- Cloudflare (DNS): `Plshe6572@gmail.com`
- Resend: same / app-specific
- Proton: `lshe6572@protonmail.com` (Workspace Standard)
- Google Cloud (OAuth): project name "ProContact"
- Forge: bouncing-boring-tech-srl
