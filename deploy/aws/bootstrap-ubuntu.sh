#!/usr/bin/env bash
# PIKO POP — first-time AWS server setup (Lightsail or EC2, Ubuntu 22.04/24.04)
# Run as root or with sudo on a fresh instance:
#   curl -fsSL https://raw.githubusercontent.com/suneel999/piko_pop/staging/deploy/aws/bootstrap-ubuntu.sh | sudo bash
# Or after cloning:
#   sudo bash deploy/aws/bootstrap-ubuntu.sh

set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/piko_pop}"
APP_USER="${APP_USER:-www-data}"
DB_NAME="${DB_NAME:-pakodi_and_politics}"
DB_USER="${DB_USER:-pikopop}"
DB_PASS="${DB_PASS:-}"
REPO_URL="${REPO_URL:-https://github.com/suneel999/piko_pop.git}"
BRANCH="${BRANCH:-staging}"
DOMAIN="${DOMAIN:-}"

if [[ $EUID -ne 0 ]]; then
  echo "Run as root: sudo bash $0"
  exit 1
fi

if [[ -z "$DB_PASS" ]]; then
  DB_PASS="$(openssl rand -base64 18 | tr -dc 'A-Za-z0-9' | head -c 20)"
  echo "Generated DB password for ${DB_USER}: ${DB_PASS}"
fi

export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y apache2 mysql-server git unzip curl software-properties-common ca-certificates

# Node.js 20 (Tailwind build on server)
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt-get install -y nodejs

# PHP 8.2 + extensions for CodeIgniter 3
add-apt-repository -y ppa:ondrej/php
apt-get update -y
apt-get install -y \
  php8.2 php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml \
  php8.2-curl php8.2-zip php8.2-gd php8.2-intl libapache2-mod-php8.2

a2enmod rewrite headers ssl
systemctl enable apache2 mysql

# Composer
if ! command -v composer >/dev/null 2>&1; then
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# Database
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# App directory
mkdir -p "$(dirname "$APP_DIR")"
if [[ ! -d "${APP_DIR}/.git" ]]; then
  git clone --branch "$BRANCH" "$REPO_URL" "$APP_DIR"
else
  cd "$APP_DIR"
  git fetch origin
  git checkout "$BRANCH"
  git pull origin "$BRANCH"
fi

cd "$APP_DIR"
composer install --no-interaction --no-dev --optimize-autoloader || true
npm ci && npm run build:css || true

mkdir -p uploads tmp application/cache application/logs
chown -R ${APP_USER}:${APP_USER} uploads tmp application/cache application/logs
chmod -R 775 uploads tmp application/cache application/logs

# .env (only create if missing)
if [[ ! -f .env ]]; then
  APP_ENV_VALUE="staging"
  [[ "$BRANCH" == "main" ]] && APP_ENV_VALUE="production"
  cat > .env <<EOF
APP_ENV=${APP_ENV_VALUE}
DB_HOST=localhost
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASS}
EOF
  chmod 600 .env
fi

# Import schema on first run
if [[ -f database/schema.sql ]]; then
  mysql -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < database/schema.sql || true
fi

# Apache vhost
cat > /etc/apache2/sites-available/piko-pop.conf <<EOF
<VirtualHost *:80>
    ServerName ${DOMAIN:-_default_}
    DocumentRoot ${APP_DIR}

    <Directory ${APP_DIR}>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/piko-pop-error.log
    CustomLog \${APACHE_LOG_DIR}/piko-pop-access.log combined
</VirtualHost>
EOF

a2dissite 000-default.conf >/dev/null 2>&1 || true
a2ensite piko-pop.conf
systemctl reload apache2

echo ""
echo "=============================================="
echo " PIKO POP server bootstrap complete"
echo "=============================================="
echo " App path : ${APP_DIR}"
echo " Database : ${DB_NAME}"
echo " DB user  : ${DB_USER}"
echo " DB pass  : ${DB_PASS}"
echo ""
echo " Next:"
echo " 1) Open port 80 in AWS Lightsail/EC2 firewall"
echo " 2) Visit http://YOUR_SERVER_IP/"
echo " 3) Add GitHub Actions SSH deploy secrets"
echo " 4) Optional SSL: sudo apt install certbot python3-certbot-apache -y"
echo "              sudo certbot --apache -d your-domain.com"
echo "=============================================="
