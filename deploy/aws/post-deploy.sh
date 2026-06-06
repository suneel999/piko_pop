#!/usr/bin/env bash
# Run on server after each GitHub Actions deploy
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/piko_pop}"
cd "$APP_DIR"

composer install --no-interaction --no-dev --optimize-autoloader 2>/dev/null || true

mkdir -p uploads tmp application/cache application/logs
# ubuntu = GitHub Actions deploy user; www-data = Apache
chown -R ubuntu:www-data uploads tmp application/cache application/logs "${APP_DIR}"
find "${APP_DIR}" -type d -exec chmod 775 {} \;
find "${APP_DIR}" -type f -exec chmod 664 {} \;
chmod 600 .env 2>/dev/null || true

echo "Post-deploy complete: $(date -Iseconds)"
