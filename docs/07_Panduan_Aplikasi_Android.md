# Panduan Implementasi Aplikasi Android SIPANDU-WBK Rindam III/Siliwangi

Dokumen ini menjelaskan cara menggunakan dan mendistribusikan aplikasi SIPANDU-WBK Rindam III/Siliwangi dalam bentuk **Aplikasi Android**.

---

## 1. Arsitektur yang Telah Dipasang (PWA Ready)

Aplikasi SIPANDU saat ini telah dilengkapi dengan standar **Progressive Web App (PWA)**:
* **Web App Manifest (`public/manifest.json`)**: Berisi nama aplikasi, skema warna gelap militer (`#0F172A`), orientasi layar, dan ikon resmi Rindam.
* **Ikon Aplikasi Lengkap (`public/img/icons/`)**: Ukuran 72x72, 96x96, 128x128, 144x144, 152x152, 192x192, 384x384, 512x512, serta 512x512 *Maskable Icon* (khusus adaptasi sudut bulat/squircle Android).
* **Service Worker (`public/sw.js`)**: Mengelola caching cerdas (Network-First untuk data dinamis siswa & Cache-First untuk aset grafis).
* **Halaman Offline Militer (`public/offline.html`)**: Ditampilkan otomatis jika ponsel kehilangan sinyal/koneksi internet.
* **Tombol Cepat "Pasang Aplikasi"**: Tersedia otomatis di topbar saat dibuka melalui peramban ponsel.

---

## 2. Cara Menggunakan Langsung di HP Android (Tanpa Install APK Manual)

Metode ini adalah yang paling mudah dan direkomendasikan untuk seluruh personel:
1. Pastikan HP Android dan komputer/server terhubung dalam jaringan yang sama (Wi-Fi/LAN/VPN) atau server memiliki IP/Domain publik.
2. Buka peramban **Google Chrome** di HP Android.
3. Masuk ke alamat SIPANDU (misal: `http://192.168.x.x:8000` atau domain resmi).
4. Di bagian atas layar akan muncul tombol **"Pasang Aplikasi"**, atau buka menu Chrome (titik tiga di kanan atas) lalu pilih **"Tambahkan ke Layar Utama" / "Install Aplikasi"**.
5. Ikon **SIPANDU Rindam** akan muncul di menu aplikasi HP.
6. Saat dibuka, aplikasi akan berjalan mandiri (*Standalone Full-Screen*) tanpa address bar browser, persis seperti aplikasi Android native.

---

## 3. Cara Mengubah Menjadi File APK Installer (`.apk`)

Jika instansi membutuhkan file installer **`.apk`** murni untuk dibagikan via WhatsApp, Telegram, atau Flashdisk, berikut cara termudahnya:

### Opsi A: Menggunakan PWABuilder (Paling Cepat, 2 Menit)
1. Pastikan aplikasi web dapat diakses via HTTPS (misal menggunakan Cloudflare tunnel, ngrok, atau server VPS hosting).
2. Buka situs [https://www.pwabuilder.com](https://www.pwabuilder.com).
3. Masukkan tautan URL web aplikasi SIPANDU.
4. Klik **"Start"** lalu pilih **"Package for Android"**.
5. Unduh paket APK yang dihasilkan. File `.apk` siap diinstal di HP personel.

### Opsi B: Menggunakan Google Bubblewrap CLI (Command Line)
1. Install Bubblewrap CLI:
   ```bash
   npm install -g @bubblewrap/cli
   ```
2. Inisialisasi proyek Android dari manifest:
   ```bash
   bubblewrap init --manifest=http://domain-anda/manifest.json
   ```
3. Bangun file APK:
   ```bash
   bubblewrap build
   ```
4. File `app-release-signed.apk` akan dihasilkan secara otomatis.

---

## 4. Keuntungan Pendekatan Ini untuk Rindam III/Siliwangi

1. **Efisiensi & Biaya Nol**: Tidak memerlukan perombakan kode dari nol.
2. **Sinkronisasi Instan**: Setiap pembaruan data atau fitur di server pusat langsung tampak di HP seluruh pengguna tanpa perlu mengirim update APK ulang.
3. **Keamanan Tetap Utuh**:
   * Enkripsi data sensitif **AES-256-CBC** tetap berjalan di backend.
   * Pembatasan RBAC (Danrindam murni monitoring tanpa CRUD, Operator Satdik terkunci pada satuannya) tetap terlindungi 100%.
