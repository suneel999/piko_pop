#!/usr/bin/env bash
# Resume / fix PIKO POP server when bootstrap stopped (e.g. mysql-server failed)
# Run on the server:
#   cd /var/www/piko_pop && sudo bash deploy/aws/fix-server.sh

set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/piko_pop}"
APP_USER="${APP_USER:-www-data}"
DB_NAME="${DB_NAME:-pakodi_and_politics}"
DB_USER="${DB_USER:-pikopop}"
DB_PASS="${DB_PASS:-PikoPop2026!}"

if [[ $EUID -ne 0 ]]; then
  echo "Run as root: sudo bash $0"
  exit 1
fi

export DEBIAN_FRONTEND=noninteractive

echo "==> Fixing broken packages..."
dpkg --configure -a || true
apt-get install -f -y || true

echo "==> Installing MariaDB (replaces failed MySQL on small instances)..."
systemctl stop mysql 2>/dev/null || true
DEBIAN_FRONTEND=noninteractive apt-get remove --purge -y \
  mysql-server mysql-server-8.0 mysql-client mysql-client-8.0 mysql-common 2>/dev/null || true
apt-get autoremove -y
DEBIAN_FRONTEND=noninteractive apt-get install -y mariadb-server
systemctl enable mariadb
systemctl start mariadb

echo "==> Installing PHP 8.2 + Apache modules..."
apt-get install -y software-properties-common ca-certificates curl git unzip
add-apt-repository -y ppa:ondrej/php 2>/dev/null || true
apt-get update -y
apt-get install -y \
  apache2 php8.2 php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml \
  php8.2-curl php8.2-zip php8.2-gd php8.2-intl libapache2-mod-php8.2

if ! command -v node >/dev/null 2>&1; then
  curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
  apt-get install -y nodejs
fi

if ! command -v composer >/dev/null 2>&1; then
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

a2enmod rewrite headers
systemctl enable apache2 mariadb

echo "==> Creating database..."
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';" 2>/dev/null \
  || mysql -e "CREATE USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

if [[ ! -d "${APP_DIR}" ]]; then
  git clone -b staging https://github.com/suneel999/piko_pop.git "${APP_DIR}"
fi

cd "${APP_DIR}"
git pull origin staging 2>/dev/null || true
composer install --no-interaction --no-dev --optimize-autoloader 2>/dev/null || true
npm ci 2>/dev/null && npm run build:css 2>/dev/null || true

mkdir -p uploads tmp application/cache application/logs
chown -R ubuntu:www-data "${APP_DIR}"
find "${APP_DIR}" -type d -exec chmod 775 {} \;
find "${APP_DIR}" -type f -exec chmod 664 {} \;
chmod 600 .env 2>/dev/null || true

cat > .env <<EOF
APP_ENV=staging
DB_HOST=localhost
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASS}
EOF
chmod 600 .env

if [[ -f database/schema.sql ]]; then
  mysql -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < database/schema.sql 2>/dev/null || true
fi

echo "==> Configuring Apache for PIKO POP..."
cat > /etc/apache2/sites-available/piko-pop.conf <<EOF
<VirtualHost *:80>
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

a2dissite 000-default.conf 2>/dev/null || true
a2ensite piko-pop.conf
systemctl reload apache2

echo ""
echo "=============================================="
echo " FIX COMPLETE"
echo "=============================================="
echo " Site  : http://$(curl -s ifconfig.me 2>/dev/null || echo YOUR_IP)/"
echo " Admin : /admin_root  (admin / admin123)"
echo " DB    : ${DB_NAME}"
echo " User  : ${DB_USER}"
echo " Pass  : ${DB_PASS}"
echo "=============================================="
