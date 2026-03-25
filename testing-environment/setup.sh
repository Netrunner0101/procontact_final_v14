#!/bin/bash
# ProContact v14 — Testing Environment Setup
# Run this script from the project root: ./testing-environment/setup.sh

set -e

echo "=========================================="
echo "  ProContact v14 — Testing Setup"
echo "=========================================="

# Navigate to project root
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

echo ""
echo "[1/7] Copying test environment configuration..."
cp testing-environment/.env.testing .env

echo "[2/7] Generating application key..."
php artisan key:generate --force

echo "[3/7] Installing PHP dependencies..."
composer install --no-interaction

echo "[4/7] Installing front-end dependencies and building assets..."
npm install
npm run build

echo "[5/7] Creating SQLite database..."
touch database/database.sqlite

echo "[6/7] Running migrations and seeding demo data..."
php artisan migrate:fresh --force
php artisan db:seed --class=DemoSeeder --force

echo "[7/7] Creating storage link..."
php artisan storage:link 2>/dev/null || true

echo ""
echo "=========================================="
echo "  Setup complete!"
echo "=========================================="
echo ""
echo "  Demo credentials:"
echo "    Admin: admin@procontact.test / password"
echo ""
echo "  Start the server with:"
echo "    php artisan serve --host=0.0.0.0 --port=8000"
echo ""
echo "  Then open: http://localhost:8000"
echo ""
echo "  Emails are logged to: storage/logs/laravel.log"
echo "  (Search for 'portal' to find magic-link URLs)"
echo ""
