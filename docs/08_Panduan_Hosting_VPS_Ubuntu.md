# Panduan Lengkap Hosting SIPANDU-WBK di VPS Ubuntu Server

Panduan ini berisi langkah-langkah praktis dan terperinci untuk mendeploy aplikasi **SIPANDU-WBK Rindam III/Siliwangi** ke server VPS berbasis **Ubuntu Server (22.04 LTS atau 24.04 LTS)** hingga dapat diakses secara publik dengan domain resmi dan protokol keamanan **HTTPS (SSL)**.

---

## 1. Spesifikasi Server VPS yang Dibutuhkan

| Komponen | Spesifikasi Minimum | Spesifikasi Rekomendasi |
| :--- | :--- | :--- |
| **Sistem Operasi** | Ubuntu 22.04 LTS / 24.04 LTS (64-bit) | Ubuntu 24.04 LTS (64-bit) |
| **CPU / Prosesor** | 1 vCPU (Core) | 2 vCPU |
| **RAM** | 1 GB RAM *(+ 1-2 GB Swap)* | 2 GB – 4 GB RAM |
| **Penyimpanan (Disk)**| 20 GB SSD / NVMe | 40 GB – 50 GB SSD |
| **Akses** | Root / Sudo via SSH | Root / Sudo via SSH |

> **Rekomendasi Provider VPS:**  
> DigitalOcean, Linode/Akamai, Hetzner, AWS Lightsail, Contabo, DomaiNesia, IDCloudHost, atau Biznet Gio.

---

## 2. Arsitektur Komponen yang Disediakan

Semua berkas otomasi telah disiapkan di dalam folder [`deploy/`](file:///c:/Users/T14s%20Touch/Documents/sipandu/deploy):

```text
sipandu/
├── deploy/
│   ├── setup-vps.sh               # Skrip instalasi lengkap 1-perintah (Nginx, PHP 8.3, Composer, Node 22, SSL)
│   ├── deploy.sh                  # Skrip pembaruan rutin (Zero-Downtime update setelah git pull)
│   ├── backup-database.sh         # Skrip pencadangan database & media harian otomatis (Cron)
│   ├── nginx/
│   │   └── sipandu.conf           # Konfigurasi Nginx produksi (Gzip, caching PWA, proteksi keamanan)
│   └── supervisor/
│       └── sipandu-worker.conf    # Pengelola antrean background worker Laravel
├── .env.production.example        # Template konfigurasi produksi
└── upload-db-ke-vps.bat           # Skrip Windows 1-klik untuk upload database lokal ke VPS
```

---

## 3. Tahap 1: Hubungkan Domain ke VPS (DNS Management)

Sebelum memulai instalasi, arahkan domain yang akan digunakan ke alamat IP VPS Anda:

1. Buka panel DNS tempat Anda membeli domain (misal: Cloudflare, Niagahoster, DomaiNesia, Rumahweb, dll).
2. Tambahkan **DNS Record**:
   - **Tipe**: `A`
   - **Name / Host**: `@` (atau subdomain seperti `sipandu`)
   - **Target / Value / IP**: *[Alamat IP Publik VPS Anda]*
   - **TTL**: Auto / 300
3. Simpan perubahan. Tunggu 2–10 menit hingga domain terhubung ke IP server.

---

## 4. Tahap 2: Akses SSH & Unduh Proyek ke VPS

Buka terminal di komputer Anda (PowerShell, Command Prompt, atau Terminal Linux/macOS):

```bash
# 1. Masuk ke VPS Anda via SSH (ganti dengan IP server Anda)
ssh root@103.187.xxx.xxx

# 2. Buat folder direktori web
mkdir -p /var/www
cd /var/www

# 3. Klon repositori SIPANDU dari GitHub
git clone https://github.com/juikop12/rindam.git sipandu
cd /var/www/sipandu
```

---

## 5. Tahap 3: Jalankan Skrip Instalasi Otomatis (1 Perintah)

Jalankan skrip otomasi yang telah disiapkan. Skrip ini akan memasang seluruh paket sistem, PHP 8.3, Nginx, Composer, Node.js 22 LTS, UFW Firewall, dan mengoptimalkan konfigurasi sistem:

```bash
sudo bash deploy/setup-vps.sh sipandu.domainanda.com
```

*(Ganti `sipandu.domainanda.com` dengan nama domain Anda, atau gunakan IP server jika belum memiliki domain).*

### Apa yang Dilakukan Skrip Ini Secara Otomatis?
1. Mengatur zona waktu server ke **WIB (Asia/Jakarta)**.
2. Memasang **PHP 8.3-FPM** dan seluruh ekstensi yang dibutuhkan (`sqlite3`, `gd`, `zip`, `xml`, `mbstring`, `bcmath`, `curl`, `intl`).
3. Mengonfigurasi `php.ini` batas upload menjadi **50MB** (aman untuk foto serdik & impor buku data Dapokdikma).
4. Memasang **Composer** dan **Node.js 22 LTS**.
5. Mengonfigurasi dan mengaktifkan virtual host **Nginx** dengan Gzip kompresi dan keamanan berkas sensitif.
6. Mengompilasi aset front-end produksi (`npm run build`).
7. Menghubungkan penyimpanan publik (`php artisan storage:link`).
8. Menjalankan migrasi database (`php artisan migrate --force`).
9. Memasang tugas terjadwal **Laravel Scheduler** dan **Backup Database Harian (Pukul 02:00 WIB)** ke Cron.
10. Mengaktifkan **Firewall UFW** (Port 22 SSH, 80 HTTP, dan 443 HTTPS).

---

## 6. Tahap 4: Mengaktifkan HTTPS SSL Gratis (Let's Encrypt)

Setelah domain berhasil terhubung ke server dan instalasi di atas selesai, aktifkan sertifikat SSL resmi agar web menggunakan protokol HTTPS yang aman:

```bash
sudo certbot --nginx -d sipandu.domainanda.com
```

- Masukkan alamat email Anda untuk notifikasi sertifikat.
- Setujui syarat dan ketentuan (*Type `Y`*).
- Pilih opsi pengalihan otomatis (*Redirect HTTP to HTTPS*).

Setelah sertifikat berhasil dipasang, perbarui nilai `APP_URL` pada file lingkungan VPS:
```bash
nano /var/www/sipandu/.env
```
Ubah menjadi:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sipandu.domainanda.com
```
Simpan (`Ctrl + O`, `Enter`, lalu `Ctrl + X`), kemudian perbarui cache Laravel:
```bash
php artisan optimize
```

---

## 7. Tahap 5: Transfer Database & Data Siswa dari Komputer Lokal ke VPS

Jika Anda telah memiliki data siswa, nilai, dan rekam medis di komputer lokal (Windows) dan ingin membawanya ke VPS:

### Cara A: Menggunakan Skrip Cepat Windows (Paling Praktis)
1. Buka folder proyek SIPANDU di komputer Windows Anda.
2. Klik ganda file [**`upload-db-ke-vps.bat`**](file:///c:/Users/T14s%20Touch/Documents/sipandu/upload-db-ke-vps.bat).
3. Masukkan Alamat IP VPS Anda dan tekan Enter.
4. Database `database.sqlite` dan berkas akan disalin otomatis ke server dan izin berkas `www-data` akan langsung disesuaikan.

### Cara B: Perintah Manual (Dari Terminal Laptop Anda)
```bash
scp database/database.sqlite root@103.187.xxx.xxx:/var/www/sipandu/database/database.sqlite
```
Setelah diunggah, jalankan perintah ini di VPS agar server memiliki hak baca/tulis:
```bash
chown -R www-data:www-data /var/www/sipandu/database
chmod 664 /var/www/sipandu/database/database.sqlite
php artisan optimize:clear && php artisan optimize
```

---

## 8. Tahap 6: Cara Melakukan Pembaruan Kode di Masa Depan

Bila sewaktu-waktu ada pembaruan fitur atau perbaikan kode yang telah Anda `git push` ke GitHub, Anda cukup menjalankan skrip deploy cepat di VPS:

```bash
bash /var/www/sipandu/deploy/deploy.sh
```

Skrip ini akan secara otomatis:
- Memasang mode pemeliharaan sementara (*Maintenance Mode*).
- Menarik kode terbaru (`git pull origin main`).
- Memasang paket baru (`composer install` & `npm run build`).
- Menjalankan migrasi database (`migrate --force`).
- Memperbarui cache performa (`optimize`).
- Mengembalikan aplikasi kembali online tanpa gangguan.

---

## 9. Pemeliharaan, Pencadangan, & Pemantauan Sistem

### 1. Lokasi Berkas Cadangan (Backup)
Berkas database dan upload otomatis dicadangkan setiap pukul 02:00 WIB ke:
```text
/var/backups/sipandu/
```
Untuk mengunduh cadangan ke laptop Anda:
```bash
scp root@103.187.xxx.xxx:/var/backups/sipandu/sipandu_db_*.sqlite.gz ./
```

### 2. Memeriksa Status Log Jika Terjadi Masalah
- **Log Aplikasi Laravel**:
  ```bash
  tail -n 100 -f /var/www/sipandu/storage/logs/laravel.log
  ```
- **Log Server Nginx**:
  ```bash
  tail -n 100 -f /var/log/nginx/sipandu_error.log
  ```
- **Log Layanan PHP-FPM**:
  ```bash
  systemctl status php8.3-fpm
  ```

---

## 10. Checklist Kesiapan Produksi (Go-Live)

- [x] Nginx Virtual Host aktif mengarah ke folder `/public`.
- [x] PHP 8.3-FPM aktif dengan batas upload 50MB.
- [x] Izin direktori `storage/`, `bootstrap/cache/`, dan `database/` dimiliki oleh user `www-data`.
- [x] Database SQLite menggunakan mode **WAL** (*Write-Ahead Logging*) dengan timeout 5.000ms untuk konkurensi multi-user.
- [x] Sertifikat SSL HTTPS Let's Encrypt terpasang dan diperbarui otomatis.
- [x] Laravel Scheduler cron aktif di `/etc/cron.d/sipandu-scheduler`.
- [x] Backup harian terkonfigurasi di `/etc/cron.d/sipandu-backup`.
- [x] PWA Manifest dan Service Worker dapat dipasang langsung di HP Android personel via HTTPS.
