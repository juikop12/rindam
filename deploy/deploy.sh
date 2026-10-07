#!/usr/bin/env bash
# ==============================================================================
# Skrip Update & Deploy Cepat SIPANDU di VPS Ubuntu
# Penggunaan: bash /var/www/sipandu/deploy/deploy.sh
# ==============================================================================

set -euo pipefail

PROJECT_DIR="/var/www/sipandu"
cd "${PROJECT_DIR}"

echo "======================================================================"
echo "  MEMULAI PEMBARUAN SIPANDU RINDAM III/SILIWANGI DI VPS"
echo "======================================================================"

# 1. Masuk ke mode pemeliharaan (Maintenance Mode)
echo ">> [1/8] Mengaktifkan mode pemeliharaan..."
php artisan down --refresh=15 --secret="rindam-update" || true

# 2. Ambil perubahan terbaru dari Git
echo ">> [2/8] Mengambil perubahan terbaru dari repository git..."
git pull origin main

# 3. Instal/perbarui dependensi PHP (Composer)
echo ">> [3/8] Menginstal dependensi Composer versi produksi..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 4. Bangun berkas aset front-end (Vite)
echo ">> [4/8] Mengompilasi aset Vite (CSS/JS/Alpine)..."
npm install --no-audit --prefer-offline
npm run build

# 5. Jalankan migrasi database
echo ">> [5/8] Menjalankan migrasi database..."
php artisan migrate --force

# 6. Bersihkan dan bangun ulang cache Laravel untuk performa maksimal
echo ">> [6/8] Mengoptimalkan cache Laravel (config, routes, views)..."
php artisan optimize:clear
php artisan optimize
php artisan view:cache
php artisan event:cache

# 7. Sinkronisasi kepemilikan berkas www-data
echo ">> [7/8] Menyesuaikan izin direktori storage dan cache..."
chown -R www-data:www-data storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache database
if [ -f database/database.sqlite ]; then
    chmod 664 database/database.sqlite
fi

# 8. Muat ulang antrean worker & PHP-FPM
echo ">> [8/8] Memuat ulang queue worker dan PHP-FPM..."
if command -v supervisorctl >/dev/null 2>&1; then
    supervisorctl restart sipandu-worker:* 2>/dev/null || true
fi
systemctl reload php8.3-fpm 2>/dev/null || true

# 9. Matikan mode pemeliharaan
php artisan up

echo "======================================================================"
echo "  ✅ PEMBARUAN SELESAI DENGAN SUKSES! APLIKASI KEMBALI ONLINE."
echo "======================================================================"
