#!/usr/bin/env bash
# ==============================================================================
# Skrip Otomatisasi Instalasi VPS Ubuntu (22.04 / 24.04 LTS) untuk SIPANDU-WBK
# Rindam III/Siliwangi - Stack: Nginx + PHP 8.3-FPM + SQLite/MySQL + Node.js
#
# Cara Penggunaan:
#   sudo bash /var/www/sipandu/deploy/setup-vps.sh [nama-domain-atau-ip]
# ==============================================================================

set -euo pipefail

# 1. Pastikan script dijalankan sebagai root / sudo
if [ "$EUID" -ne 0 ]; then
    echo "❌ Error: Harap jalankan script ini dengan hak akses root (sudo)!"
    exit 1
fi

DOMAIN_NAME="${1:-}"
PROJECT_DIR="/var/www/sipandu"

echo "======================================================================"
echo "    INSTALASI & KONFIGURASI OTOMATIS SIPANDU DI UBUNTU SERVER VPS"
echo "               Rindam III/Siliwangi - Web Engine"
echo "======================================================================"
echo ""

# Input interaktif untuk Domain / IP jika belum diisi di argumen
if [ -z "${DOMAIN_NAME}" ]; then
    read -rp "👉 Masukkan Nama Domain Anda (contoh: sipandu-rindam.com) atau Alamat IP VPS: " DOMAIN_NAME
fi

if [ -z "${DOMAIN_NAME}" ]; then
    echo "❌ Nama domain atau IP tidak boleh kosong!"
    exit 1
fi

echo ">> Menyiapkan server untuk host: ${DOMAIN_NAME}..."
export DEBIAN_FRONTEND=noninteractive

# 2. Sinkronisasi Zona Waktu (WIB / Asia/Jakarta)
echo ">> [1/12] Mengatur zona waktu ke Asia/Jakarta (WIB)..."
timedatectl set-timezone Asia/Jakarta || true

# 3. Pembaruan Paket Sistem Dasar
echo ">> [2/12] Memperbarui repositori sistem Ubuntu..."
apt-get update -y
apt-get install -y --no-install-recommends \
    curl git unzip zip ca-certificates gnupg software-properties-common \
    lsb-release ufw sqlite3 htop fail2ban tar

# 4. Instalasi Repositori PHP & Ekstensi (Mendukung semua rilis Ubuntu)
echo ">> [3/12] Memasang dependensi PHP & ekstensi..."
# Bersihkan repository PPA rusak jika ada
rm -f /etc/apt/sources.list.d/*ondrej* /etc/apt/sources.list.d/*php* 2>/dev/null || true
apt-get update -y

# Coba pasang paket PHP bawaan resmi Ubuntu terlebih dahulu (sangat stabil & kompatibel)
if ! apt-get install -y php-fpm php-cli php-common php-sqlite3 php-mysql php-mbstring php-xml php-curl php-gd php-zip php-bcmath php-intl php-readline; then
    echo ">> Mengunduh repositori Sury PHP resmi..."
    apt-get install -y apt-transport-https lsb-release
    curl -sSLo /tmp/debsuryorg-archive-keyring.deb https://packages.sury.org/debsuryorg-archive-keyring.deb || true
    dpkg -i /tmp/debsuryorg-archive-keyring.deb 2>/dev/null || true
    echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
    apt-get update -y
    apt-get install -y php-fpm php-cli php-common php-sqlite3 php-mysql php-mbstring php-xml php-curl php-gd php-zip php-bcmath php-intl php-readline
fi

# Deteksi versi PHP yang berhasil terpasang
PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || echo "8.3")
echo ">> Berhasil memasang PHP versi: ${PHP_VER}"

# 5. Konfigurasi Batas Ukuran File di PHP.ini (FPM & CLI)
echo ">> [4/12] Mengonfigurasi batas upload PHP (50MB) untuk impor Dapokdikma..."
for php_ini in /etc/php/${PHP_VER}/fpm/php.ini /etc/php/${PHP_VER}/cli/php.ini; do
    if [ -f "$php_ini" ]; then
        sed -i 's/^upload_max_filesize =.*/upload_max_filesize = 50M/' "$php_ini"
        sed -i 's/^post_max_size =.*/post_max_size = 50M/' "$php_ini"
        sed -i 's/^memory_limit =.*/memory_limit = 256M/' "$php_ini"
        sed -i 's/^max_execution_time =.*/max_execution_time = 300/' "$php_ini"
    fi
done
systemctl restart php${PHP_VER}-fpm 2>/dev/null || systemctl restart php-fpm 2>/dev/null || true

# 6. Pasang Composer (PHP Package Manager)
echo ">> [5/12] Memasang Composer versi terbaru..."
if ! command -v composer >/dev/null 2>&1; then
    curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm -f /tmp/composer-setup.php
fi

# 7. Pasang Node.js 22.x LTS & NPM (NodeSource)
echo ">> [6/12] Memasang Node.js 22.x LTS untuk kompilasi Vite/Tailwind..."
if ! command -v node >/dev/null 2>&1; then
    curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
    apt-get install -y nodejs
fi

# 8. Pasang Nginx Web Server, Supervisor, & Certbot SSL
echo ">> [7/12] Memasang Nginx, Supervisor, dan Certbot Let's Encrypt..."
apt-get install -y nginx supervisor certbot python3-certbot-nginx

# 9. Konfigurasi Virtual Host Nginx
echo ">> [8/12] Mengonfigurasi Nginx server block untuk SIPANDU..."
NGINX_TEMPLATE="${PROJECT_DIR}/deploy/nginx/sipandu.conf"
NGINX_TARGET="/etc/nginx/sites-available/sipandu"

if [ -f "${NGINX_TEMPLATE}" ]; then
    sed -e "s/{{DOMAIN_OR_IP}}/${DOMAIN_NAME}/g" \
        -e "s/php8.3-fpm.sock/php${PHP_VER}-fpm.sock/g" \
        "${NGINX_TEMPLATE}" > "${NGINX_TARGET}"
    if [ ! -S "/run/php/php${PHP_VER}-fpm.sock" ] && [ -S "/run/php/php-fpm.sock" ]; then
        sed -i "s/php${PHP_VER}-fpm.sock/php-fpm.sock/g" "${NGINX_TARGET}"
    fi
else
    echo "❌ Berkas template ${NGINX_TEMPLATE} tidak ditemukan!"
    exit 1
fi

ln -sf "${NGINX_TARGET}" /etc/nginx/sites-enabled/sipandu
rm -f /etc/nginx/sites-enabled/default

nginx -t
systemctl reload nginx

# 10. Persiapan Direktori Aplikasi & File Lingkungan (.env)
echo ">> [9/12] Menyiapkan berkas lingkungan (.env) & direktori kerja..."
cd "${PROJECT_DIR}"

if [ ! -f .env ]; then
    if [ -f .env.production.example ]; then
        cp .env.production.example .env
    else
        cp .env.example .env
    fi
    sed -i "s|APP_URL=.*|APP_URL=http://${DOMAIN_NAME}|" .env
fi

# Pastikan database SQLite siap
mkdir -p database storage/app/public storage/framework/{cache,sessions,views} bootstrap/cache
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

# Beri izin kepemilikan www-data pada folder penyimpanan
chown -R www-data:www-data storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache database
chmod 664 database/database.sqlite

# 11. Instalasi Dependensi & Bangun Frontend
echo ">> [10/12] Membangun dependensi PHP & aset Vite..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
npm install --no-audit --prefer-offline
npm run build

# Generate APP_KEY jika belum ada
if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate --force
fi

php artisan storage:link || true
php artisan migrate --force

# Seed database jika tabel users masih kosong
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null || echo "0")
if [ "${USER_COUNT}" = "0" ]; then
    echo ">> Menginisialisasi data dasar (Satdik & Akun Pengguna Default)..."
    php artisan db:seed --force || true
fi

# Optimasi Cache Produksi
php artisan optimize:clear
php artisan optimize
php artisan view:cache
php artisan event:cache

# 12. Konfigurasi Cron Scheduler & Background Worker
echo ">> [11/12] Memasang Laravel Scheduler cron & Supervisor worker..."
cat << 'EOF' > /etc/cron.d/sipandu-scheduler
* * * * * www-data php /var/www/sipandu/artisan schedule:run >> /dev/null 2>&1
EOF
chmod 644 /etc/cron.d/sipandu-scheduler

# Pasang cron backup harian jam 02:00 WIB
cat << 'EOF' > /etc/cron.d/sipandu-backup
0 2 * * * root /bin/bash /var/www/sipandu/deploy/backup-database.sh >> /var/log/sipandu-backup.log 2>&1
EOF
chmod 644 /etc/cron.d/sipandu-backup
chmod +x "${PROJECT_DIR}/deploy/backup-database.sh"
chmod +x "${PROJECT_DIR}/deploy/deploy.sh"

if [ -f "${PROJECT_DIR}/deploy/supervisor/sipandu-worker.conf" ]; then
    cp "${PROJECT_DIR}/deploy/supervisor/sipandu-worker.conf" /etc/supervisor/conf.d/sipandu-worker.conf
    supervisorctl reread || true
    supervisorctl update || true
fi

# 13. Konfigurasi Firewall UFW
echo ">> [12/12] Mengamankan server dengan Firewall UFW (SSH, HTTP, HTTPS)..."
ufw allow OpenSSH || true
ufw allow 'Nginx Full' || true
ufw --force enable || true

# Verifikasi ulang izin file
chown -R www-data:www-data storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache database
chmod 664 database/database.sqlite

echo ""
echo "======================================================================"
echo "  🎉 INSTALASI SIPANDU-WBK BERHASIL SELESAI!"
echo "======================================================================"
echo "  🌐 Akses Web Anda: http://${DOMAIN_NAME}"
echo ""
echo "  🔑 Akun Login Bawaan (Default):"
echo "     * Super Admin : superadmin@rindam.mil.id (Pass: password)"
echo "     * Danrindam   : danrindam@rindam.mil.id   (Pass: password)"
echo ""
echo "  🔒 LANGKAH BERIKUTNYA UNTUK HTTPS SSL GRATIS (LET'S ENCRYPT):"
echo "     Jika domain '${DOMAIN_NAME}' sudah diarahkan (DNS A Record) ke IP ini,"
echo "     cukup ketik perintah ini untuk mengaktifkan HTTPS otomatis:"
echo ""
echo "     sudo certbot --nginx -d ${DOMAIN_NAME}"
echo ""
echo "     Lalu sesuaikan .env: APP_URL=https://${DOMAIN_NAME}"
echo "     dan jalankan: php artisan optimize"
echo "======================================================================"
