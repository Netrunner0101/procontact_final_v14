#!/bin/bash
# Laravel Forge Deploy Script
# Copy this into your Forge deployment script, or use it as a reference.

cd /home/forge/procontact.be

git pull origin $FORGE_SITE_BRANCH

# Install/update PHP dependencies (no dev packages in production)
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Install/update Node dependencies & build assets
npm ci
npm run build

# Run new migrations
$FORGE_PHP artisan migrate --force

# Clear and rebuild all caches for production performance
$FORGE_PHP artisan config:cache
$FORGE_PHP artisan route:cache
$FORGE_PHP artisan view:cache
$FORGE_PHP artisan event:cache

# Restart queue workers to pick up new code
$FORGE_PHP artisan queue:restart

# Notify Nightwatch of the new deployment (no-op if NIGHTWATCH_TOKEN is unset)
$FORGE_PHP artisan nightwatch:deploy || true

# Restart FPM to clear OPcache
( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmrestart.lock
