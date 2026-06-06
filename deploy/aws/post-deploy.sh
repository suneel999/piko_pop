#!/usr/bin/env bash
# Run on server after each GitHub Actions deploy
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/piko_pop}"
cd "$APP_DIR"

composer install --no-interaction --no-dev --optimize-autoloader 2>/dev/null || true

mkdir -p uploads tmp application/cache application/logs
chown -R www-data:www-data uploads tmp application/cache application/logs
chmod -R 775 uploads tmp application/cache application/logs

echo "Post-deploy complete: $(date -Iseconds)"
