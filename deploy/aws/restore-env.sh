#!/usr/bin/env bash
# Recreate server .env if deploy removed it. Run on server:
#   sudo bash deploy/aws/restore-env.sh
set -euo pipefail
APP_DIR="${APP_DIR:-/var/www/piko_pop}"
cat > "${APP_DIR}/.env" <<'EOF'
APP_ENV=staging
DB_HOST=localhost
DB_DATABASE=pakodi_and_politics
DB_USERNAME=pikopop
DB_PASSWORD=PikoPop2026!
EOF
# Apache runs as www-data — group must be able to read .env
chown ubuntu:www-data "${APP_DIR}/.env"
chmod 640 "${APP_DIR}/.env"
echo ".env restored at ${APP_DIR}/.env (readable by www-data)"
