#!/usr/bin/env bash
# Run on server after each GitHub Actions deploy (with sudo)
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/piko_pop}"
cd "$APP_DIR"

composer install --no-interaction --no-dev --optimize-autoloader 2>/dev/null || true

mkdir -p uploads tmp application/cache application/logs

if command -v sudo >/dev/null 2>&1; then
  sudo chown -R ubuntu:www-data "${APP_DIR}"
  sudo find "${APP_DIR}" -type d -exec chmod 775 {} \;
  sudo find "${APP_DIR}" -type f -exec chmod 664 {} \;
  sudo chown ubuntu:www-data .env 2>/dev/null || true
  sudo chmod 640 .env 2>/dev/null || true
  sudo chown -R www-data:www-data uploads tmp application/cache application/logs
  sudo chmod -R 775 uploads tmp application/cache application/logs
else
  chmod -R 775 uploads tmp application/cache application/logs 2>/dev/null || true
fi

echo "Post-deploy complete: $(date -Iseconds)"
