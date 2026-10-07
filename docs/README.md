# SIPANDU-WBK Rindam
### Sistem Informasi Pembinaan Satuan Pendidikan Terpadu berbasis Wilayah Bebas dari Korupsi

Platform web berbasis **Laravel** yang mengintegrasikan:

| No | Pilar | Isi |
|----|-------|-----|
| 1 | **Portal WBK / Zona Integritas** | Transparansi, pengaduan (WBS), survei kepuasan, lapor gratifikasi, standar pelayanan |
| 2 | **SIM Binsat (6 Komponen)** | Organisasi, Personel, Materiil, Fasilitas/Pangkalan, Latihan, Doktrin/Piranti Lunak |
| 3 | **E-Learning (LMS)** | Mata pelajaran, materi/hanjar, kuis & ujian, tugas, nilai, sertifikat |
| 4 | **E-Pustaka (Digital Library)** | Katalog, e-book/hanjar digital, sirkulasi buku fisik, statistik baca |
| 5 | **Dashboard Pimpinan** | Indeks Kesiapan Binsat, statistik pendidikan, kinerja pelayanan WBK |

## Daftar Dokumen

| No | Dokumen | Keterangan |
|----|---------|------------|
| 🗺️ | [SIPANDU_WBK_Peta_ERD.pdf](file:///d:/laragon/rindam/docs/pdf/SIPANDU_WBK_Peta_ERD.pdf) | **Poster Peta ERD (A3 Landscape)** — Diagram visual interaktif/cetak 5 kluster & 45 tabel basis data |
| 🌐 | [peta_erd.html (Peta Interaktif)](file:///d:/laragon/rindam/docs/pdf/build/peta_erd.html) | **Peta Visual Interaktif (Web View)** — Zoom, pan, filter kluster, search field, & drawer detail tabel |
| 📕 | [SIPANDU_WBK_Bahan_Paparan.pdf](file:///d:/laragon/rindam/docs/pdf/SIPANDU_WBK_Bahan_Paparan.pdf) | **Slide Presentasi Lanskap (16:9, 25 Halaman)** — Siap dipaparkan di depan pimpinan/sidang |
| 📘 | [SIPANDU_WBK_Dokumen_Komprehensif.pdf](file:///d:/laragon/rindam/docs/pdf/SIPANDU_WBK_Dokumen_Komprehensif.pdf) | **Naskah Lengkap A4 (64 Halaman)** — Dokumen Penelitian, Blueprint, Peta ERD, Kerangka Teknis, & Timeline |
| 01 | [Dokumen Penelitian](file:///d:/laragon/rindam/docs/01_Dokumen_Penelitian.md) | Latar belakang, rumusan masalah, landasan teori, metodologi, analisis kebutuhan, rencana pengujian |
| 02 | [Blueprint Sistem](file:///d:/laragon/rindam/docs/02_Blueprint_Sistem.md) | Arsitektur, peta modul, hak akses, ERD, alur proses, sitemap, desain UI, keamanan |
| 05 | [Peta ERD Lengkap](file:///d:/laragon/rindam/docs/05_Peta_ERD_Lengkap.md) | Kamus data 45 tabel, diagram relasi, kunci asing antar-domain, dan normalisasi 3NF |
| 06 | [Rancang Bangun Data Siswa & Keamanan Sistem](file:///d:/laragon/rindam/docs/06_Rancang_Bangun_Data_Siswa_Satdik.md) | Desain partisi multi-tenancy per Satdik, enkripsi data pribadi AES-256 (SIPANDU-WBK), masking, & audit trail |
| 07 | [Panduan Aplikasi Android & PWA](file:///c:/Users/T14s%20Touch/Documents/sipandu/docs/07_Panduan_Aplikasi_Android.md) | Standar PWA, instalasi mandiri Chrome/Android, dan build APK Android installer |
| 08 | [Panduan Hosting VPS Ubuntu Server](file:///c:/Users/T14s%20Touch/Documents/sipandu/docs/08_Panduan_Hosting_VPS_Ubuntu.md) | Panduan lengkap deploy produksi VPS Ubuntu (22.04/24.04), Nginx, PHP 8.3, SSL HTTPS, & pencadangan otomatis |
| 03 | [Kerangka Kerja Teknis](file:///d:/laragon/rindam/docs/03_Kerangka_Kerja_Teknis.md) | Tech stack, package, struktur folder, standar kode, Git workflow, setup Laragon |
| 04 | [Timeline Proyek 2 Bulan](file:///d:/laragon/rindam/docs/04_Timeline_Proyek.md) | Gantt chart, rincian sprint mingguan, milestone, tim, manajemen risiko, checklist go-live |

## Ringkasan Jadwal

```
Minggu 1  | 05–09 Okt 2026 | Analisis & pengumpulan data
Minggu 2  | 12–16 Okt 2026 | Desain sistem & setup proyek
Minggu 3  | 19–23 Okt 2026 | Sprint 1 : Core sistem + Portal WBK
Minggu 4  | 26–30 Okt 2026 | Sprint 2 : Binsat Organisasi, Personel, Materiil
Minggu 5  | 02–06 Nov 2026 | Sprint 3 : Binsat Fasilitas, Latihan, Doktrin + Dashboard
Minggu 6  | 09–13 Nov 2026 | Sprint 4 : E-Learning (LMS)
Minggu 7  | 16–20 Nov 2026 | Sprint 5 : E-Pustaka + Fitur WBK lanjutan + Integrasi
Minggu 8  | 23–27 Nov 2026 | Pengujian, UAT, Deployment, Pelatihan, Serah terima
(+1)      | 30 Nov–04 Des  | Hypercare / pendampingan pasca go-live (opsional)
```
