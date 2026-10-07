# Aplikasi Android SIPANDU-WBK Rindam III/Siliwangi (Offline & Local Network Ready)

Proyek ini adalah contoh aplikasi Android native (berbasis WebView & Offline Bundled Assets) yang siap di-build menjadi file **`.apk`**.

---

## Fitur Utama Aplikasi Android Ini

1. **Dukungan Jaringan Lokal (Offline / Wi-Fi LAN)**:
   - Menggunakan konfigurasi `network_security_config.xml` (`usesCleartextTraffic="true"`), sehingga aplikasi dapat membuka server HTTP lokal di jaringan Wi-Fi/LAN tanpa diblokir oleh sistem keamanan Android 9, 10, 11, 12, 13, 14.
2. **Pengaturan IP Server Dinamis**:
   - Terdapat tombol dan dialog di dalam aplikasi untuk mengganti IP Server laptop/server secara langsung (misal: `http://192.168.100.20:8000`) tanpa harus meng-compile ulang APK.
3. **Mode Demo Offline Tersemat (Bundled Assets)**:
   - Jika laptop/server dimatikan atau HP tidak mendapatkan sinyal Wi-Fi, aplikasi tidak akan blank/error putih.
   - Pengguna dapat menekan tombol **"Buka Mode Demo Offline"** untuk melihat ringkasan data serdik 5 Satdik secara offline langsung dari aset di dalam HP.
4. **Ikon Resmi Rindam III/Siliwangi**:
   - Menggunakan paket ikon resmi Rindam III/Siliwangi yang sudah terkonversi ke seluruh densitas layar Android (hdpi, mdpi, xhdpi, xxhdpi, xxxhdpi).
5. **Swipe-to-Refresh & Navigasi Back**:
   - Tarik layar ke bawah untuk memuat ulang data terbaru.
   - Tombol "Back" ponsel akan kembali ke riwayat halaman web sebelumnya alih-alih langsung keluar dari aplikasi.

---

## Cara Build Menjadi File APK di Android Studio

1. Buka software **Android Studio**.
2. Pilih menu **File > Open**, lalu arahkan ke folder ini:
   ```
   D:\laragon\rindam\android-app
   ```
3. Tunggu proses Gradle Sync selesai beberapa saat.
4. Untuk menghasilkan file installer **`.apk`**:
   - Klik menu **Build** di toolbar atas.
   - Pilih **Build Bundle(s) / APK(s) > Build APK(s)**.
5. Setelah selesai, Android Studio akan menampilkan notifikasi **"APK(s) generated successfully"**.
6. Klik **Locate**, dan file **`app-debug.apk`** siap dikirim ke HP Android untuk diinstal langsung!
