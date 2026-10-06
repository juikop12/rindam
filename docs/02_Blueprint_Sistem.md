# BLUEPRINT SISTEM — SIPANDU-WBK RINDAM

> Dokumen ini adalah cetak biru (blueprint) fungsional & teknis. Struktur data dan indikator Binsat bersifat **draf awal** dan wajib divalidasi bersama pejabat terkait pada Minggu 1–2.

---

## 1. Visi Produk

> **"Satu platform digital untuk satuan pendidikan yang siap operasional, cerdas dalam pembelajaran, dan bersih dari korupsi."**

| Sasaran | Ukuran |
|---------|--------|
| Kesiapan satuan terukur | Indeks Kesiapan Binsat tersedia *real-time* |
| Pembelajaran modern | 100% mata pelajaran memiliki materi & evaluasi daring |
| Literasi meningkat | Seluruh hanjar & naskah doktrin terdigitalisasi |
| Integritas & pelayanan | Seluruh pengaduan memiliki nomor tiket & batas waktu tindak lanjut |

---

## 2. Arsitektur Tingkat Tinggi

```mermaid
flowchart TB
    subgraph Client["Lapisan Pengguna"]
        PUB[Publik - Browser]
        STF[Staf / Pimpinan]
        GDK[Gadik]
        SIS[Siswa / Serdik]
    end

    subgraph App["Aplikasi Laravel - Monolith Modular"]
        PORTAL["Portal WBK - Blade + Livewire"]
        PANEL["Panel Admin - Filament"]
        LMS["E-Learning - Livewire"]
        LIB["E-Pustaka - Livewire + PDF.js"]
        API["REST API - Sanctum, untuk PWA/mobile"]
        CORE["Core: Auth, RBAC, Audit Log, Notifikasi, Laporan"]
    end

    subgraph Infra["Infrastruktur"]
        DB[(MySQL / MariaDB)]
        REDIS[(Redis - cache, queue, session)]
        STORE[(Storage - dokumen, e-book, video)]
        MAIL[SMTP Mail]
        BKP[(Backup Harian)]
    end

    PUB --> PORTAL
    STF --> PANEL
    GDK --> LMS
    GDK --> PANEL
    SIS --> LMS
    SIS --> LIB
    PORTAL --> CORE
    PANEL --> CORE
    LMS --> CORE
    LIB --> CORE
    API --> CORE
    CORE --> DB
    CORE --> REDIS
    CORE --> STORE
    CORE --> MAIL
    DB --> BKP
    STORE --> BKP
```

**Pola arsitektur**: *Modular Monolith* — satu aplikasi Laravel dengan pemisahan per domain (modul). Dipilih karena cocok untuk tim kecil & tenggat 2 bulan, mudah di-deploy, namun tetap rapi dan dapat dipecah di masa depan.

---

## 3. Peta Modul

```mermaid
mindmap
  root((SIPANDU-WBK))
    Portal WBK
      Profil Satuan
      Berita dan Agenda
      Standar dan Maklumat Pelayanan
      Informasi Publik
      Pengaduan WBS
      Survei SKM IPAK IPKP
      Lapor Gratifikasi
      Galeri dan FAQ
    SIM Binsat
      Organisasi
      Personel
      Materiil
      Fasilitas Pangkalan
      Latihan
      Doktrin Piranti Lunak
      Indeks Kesiapan
    E-Learning
      Program Pendidikan
      Kelas dan Mapel
      Materi dan Hanjar
      Kuis dan Ujian
      Tugas
      Nilai dan Rapor
      Sertifikat
    E-Pustaka
      Katalog
      E-Book Reader
      Sirkulasi
      Anggota
      Statistik
    Sistem
      Pengguna dan Role
      Audit Log
      Notifikasi
      Pengaturan
      Backup
```

---

## 4. Aktor & Matriks Hak Akses

**Keterangan**: C = Create, R = Read, U = Update, D = Delete, A = Approve, — = tanpa akses

| Modul | Super Admin | Pimpinan | Operator Binsat | Gadik | Siswa | Pustakawan | Tim ZI | Publik |
|-------|:-----------:|:--------:|:---------------:|:-----:|:-----:|:----------:|:------:|:------:|
| Pengguna & Role | CRUD | R | — | — | — | — | — | — |
| Binsat (6 komponen) | CRUD | R, A | CRU* | R** | — | — | — | — |
| Dashboard Indeks Binsat | R | R | R* | — | — | — | — | — |
| E-Learning (kelola) | CRUD | R | — | CRUD | — | — | — | — |
| E-Learning (belajar) | R | R | — | R | R, C*** | — | — | — |
| E-Pustaka (kelola) | CRUD | R | — | C (usul) | — | CRUD | — | — |
| E-Pustaka (baca/pinjam) | R | R | R | R | R | R | R | R (katalog) |
| Konten Portal | CRUD | R | — | — | — | — | CRUD | R |
| Pengaduan / Gratifikasi | R | R, A | — | C | C | C | CRUD, A | C |
| Survei | CRUD | R | C | C | C | C | CRUD | C |
| Audit Log | R | R | — | — | — | — | R | — |

\* Hanya komponen sesuai seksinya (mis. Staf Pers → Personel) · \*\* Gadik membaca doktrin & jadwal latihan · \*\*\* Siswa mengirim jawaban & tugas

---

## 5. Rincian Modul

### 5.1 Portal WBK (Publik)

| Fitur | Deskripsi | Area ZI |
|-------|-----------|---------|
| Beranda | Hero, statistik layanan, berita terbaru, banner "Zona Integritas" | 1 |
| Profil | Sejarah, visi-misi, struktur organisasi, pejabat | 2 |
| Berita & Agenda | Kegiatan satuan, agenda ZI | 1 |
| Standar Pelayanan | Persyaratan, prosedur, waktu, biaya (Rp0), produk layanan | 6 |
| Maklumat Pelayanan | Janji layanan pimpinan | 6 |
| Informasi Publik (PPID) | Info berkala, serta-merta, setiap saat; permohonan informasi | 2 |
| Pengaduan / WBS | Form pengaduan (bisa anonim), nomor tiket, lacak status | 5, 6 |
| Lapor Gratifikasi | Form pelaporan penerimaan/penolakan gratifikasi | 5 |
| Survei | SKM (9 unsur), IPAK, IPKP — hasil ditampilkan publik | 6 |
| Inovasi | Etalase inovasi pelayanan | 1 |
| Galeri, FAQ, Kontak | — | 6 |

**Alur Pengaduan (WBS)**

```mermaid
sequenceDiagram
    actor P as Pelapor
    participant S as Sistem
    participant Z as Tim ZI
    participant K as Pimpinan
    P->>S: Kirim pengaduan + bukti (opsional anonim)
    S-->>P: Nomor tiket + kode akses
    S->>Z: Notifikasi pengaduan baru
    Z->>S: Verifikasi (Valid / Tidak Valid)
    alt Valid
        Z->>K: Ajukan untuk disposisi
        K->>S: Disposisi ke unit terkait
        S->>Z: Unit tindak lanjut + laporan
        Z->>S: Status Selesai + tanggapan
    else Tidak Valid
        Z->>S: Status Ditolak + alasan
    end
    P->>S: Lacak tiket
    S-->>P: Status & tanggapan
```

SLA: verifikasi ≤ 3 hari kerja, tindak lanjut ≤ 14 hari kerja (dapat dikonfigurasi). Sistem memberi tanda merah jika melewati SLA.

---

### 5.2 SIM Binsat — 6 Komponen

#### 5.2.1 Pembinaan Organisasi
| Fitur | Data / Fungsi |
|-------|---------------|
| Data Satuan & Sub-satuan | Rindam → Dodik/Depo/Detasemen → Seksi/Urusan (struktur pohon) |
| TOP/DSPP | Daftar jabatan menurut TOP: nama jabatan, pangkat, korps, jumlah |
| Struktur Riil | Jabatan yang terisi vs TOP → otomatis hitung *gap* |
| Bagan Organisasi | Org-chart otomatis dari data |
| Dokumen Organisasi | Surat keputusan pembentukan/perubahan organisasi |

#### 5.2.2 Pembinaan Personel
| Fitur | Data / Fungsi |
|-------|---------------|
| Data Personel | NRP, nama, pangkat, korps, jabatan, satuan, TMT, pendidikan, kontak (data sensitif terenkripsi) |
| Kekuatan Personel | Rekap per satuan/pangkat/golongan: DSPP vs Nyata (+/−) |
| Pemenuhan Jabatan | Jabatan kosong, rangkap, Plt |
| Riwayat | Pangkat, jabatan, pendidikan, penugasan, tanda jasa |
| Kesiapan Harian | Hadir, dinas luar, cuti, sakit, pendidikan |
| Moril Prajurit | Survei moril berkala, kegiatan pembinaan mental/olahraga, penghargaan & hukuman |
| Laporan | Lapsat personel, kekuatan bulanan (PDF/Excel) |

#### 5.2.3 Pembinaan Materiil
| Fitur | Data / Fungsi |
|-------|---------------|
| Inventaris | Kode, nama, kategori (senjata, munisi*, ranmor, alkom, alins/alongins, perlengkapan), nomor seri, satuan pemegang |
| Kondisi | B (Baik), RR (Rusak Ringan), RB (Rusak Berat) |
| Pemeliharaan | Jadwal harwat (harian/mingguan/bulanan), riwayat, petugas, biaya |
| Mutasi Materiil | Penerimaan, distribusi, penghapusan (dengan approval) |
| QR Code | Label QR per item → scan untuk lihat detail & kondisi |
| Laporan | Rekap kondisi & kesiapan materiil per kategori |

\* Data munisi/senjata yang rinci dan berklasifikasi dapat dibatasi hanya rekap jumlah sesuai kebijakan satuan.

#### 5.2.4 Pembinaan Fasilitas / Pangkalan
| Fitur | Data / Fungsi |
|-------|---------------|
| Aset Fasilitas | Gedung kantor, kelas, barak, rumah dinas, lapangan, lapangan tembak, sarana ibadah/olahraga |
| Kondisi & Kapasitas | Kondisi B/RR/RB, luas, kapasitas, foto |
| Rumah Dinas | Penghuni, SIP (Surat Izin Penghunian), masa berlaku |
| Permintaan Perbaikan | Tiket kerusakan → verifikasi → pengerjaan → selesai |
| Pemakaian Fasilitas | Peminjaman ruang/lapangan dengan kalender (cegah bentrok jadwal) |

#### 5.2.5 Pembinaan Latihan
| Fitur | Data / Fungsi |
|-------|---------------|
| Program Latihan | Program tahunan → bulanan → mingguan (bertahap, bertingkat, berlanjut) |
| Jenis Latihan | Latihan perorangan, satuan, menembak, jasmani (UTJ), Garjas, dsb. |
| Jadwal & Peserta | Kalender, peserta, pelatih, lokasi (terhubung modul Fasilitas) |
| Hasil Latihan | Nilai per peserta (mis. nilai menembak, Garjas A/B), dokumentasi |
| Evaluasi | % program terlaksana vs rencana, rata-rata hasil |

#### 5.2.6 Pembinaan Doktrin / Piranti Lunak
| Fitur | Data / Fungsi |
|-------|---------------|
| Repositori Naskah | Bujuk, Bujukmin, Juknis, Protap, SOP, Hanjar — dengan nomor, tahun, status berlaku |
| Versi Naskah | Riwayat revisi; naskah lama otomatis ditandai "Dicabut/Tidak Berlaku" |
| Klasifikasi Akses | Biasa / Terbatas (Rahasia **tidak** diunggah) |
| Sosialisasi | Jadwal sosialisasi + daftar hadir |
| Tes Pemahaman | Kuis pemahaman naskah (memakai mesin kuis E-Learning) |
| Bukti Baca | Tercatat siapa yang telah membaca naskah wajib |

#### 5.2.7 Indeks Kesiapan Binsat (Fitur Unggulan)

Setiap komponen menghasilkan skor 0–100 yang dihitung otomatis. **Bobot & rumus dapat dikonfigurasi** oleh Super Admin agar sesuai ketentuan resmi.

| Komponen | Rumus Awal (Draf) | Bobot Default |
|----------|-------------------|:-------------:|
| Organisasi | (Jabatan terbentuk sesuai TOP / Jabatan TOP) × 100 | 15% |
| Personel | 60% × Pemenuhan jabatan + 20% × Kesiapan harian + 20% × Indeks moril | 20% |
| Materiil | ((B × 1) + (RR × 0,5) + (RB × 0)) / Total item × 100 | 20% |
| Fasilitas | ((B × 1) + (RR × 0,5) + (RB × 0)) / Total fasilitas × 100 | 15% |
| Latihan | 70% × (Latihan terlaksana / Rencana) + 30% × Rata-rata nilai | 20% |
| Doktrin | 50% × Naskah mutakhir + 50% × Rata-rata tes pemahaman | 10% |

```
Indeks Kesiapan Binsat = Σ (Skor Komponen × Bobot)

≥ 85      : SIAP           (hijau)
70 – 84,9 : CUKUP SIAP     (kuning)
< 70      : KURANG SIAP    (merah)
```

Dashboard menampilkan: *gauge* indeks total, *radar chart* 6 komponen, tren bulanan, dan daftar "titik lemah" yang perlu ditindaklanjuti.

---

### 5.3 E-Learning (LMS)

| Fitur | Deskripsi |
|-------|-----------|
| Program Pendidikan | Mis. Diktukba, Diktukta, Dikjur, Bela Negara — angkatan, periode |
| Kelas / Peleton | Siswa dikelompokkan per kelas/peleton |
| Mata Pelajaran | Kurikulum, jam pelajaran (JP), gadik pengampu |
| Materi | PDF/hanjar (ditautkan dari E-Pustaka), video, tautan, teks; dibuka bertahap (*drip*) |
| Bank Soal | Pilihan ganda, benar/salah, esai; kategori & tingkat kesulitan |
| Kuis & Ujian | Acak soal & opsi, timer, batas percobaan, mode ujian layar penuh, penilaian otomatis |
| Tugas | Unggah berkas, tenggat, penilaian & umpan balik |
| Presensi | Presensi daring per sesi |
| Nilai & Rapor | Bobot nilai (tugas, kuis, UTS, UAS, praktik), rapor otomatis, ranking |
| Progres | Persentase penyelesaian materi per siswa |
| Sertifikat | Sertifikat PDF dengan QR verifikasi |
| Forum | Diskusi per mapel (opsional) |

**Alur Ujian Daring**

```mermaid
flowchart LR
    A[Gadik buat ujian dari Bank Soal] --> B[Atur jadwal, durasi, acak soal]
    B --> C[Publikasi ke kelas]
    C --> D[Siswa mulai ujian - token]
    D --> E[Jawaban autosave tiap 30 detik]
    E --> F{Waktu habis / Submit}
    F --> G[Nilai PG otomatis]
    G --> H[Gadik koreksi esai]
    H --> I[Nilai masuk rapor]
```

---

### 5.4 E-Pustaka (Digital Library)

| Fitur | Deskripsi |
|-------|-----------|
| Katalog | Judul, pengarang, penerbit, tahun, ISBN, kategori (DDC sederhana), sampul, lokasi rak |
| Koleksi Digital | E-book/PDF/hanjar dibaca di *viewer* (PDF.js) **tanpa tombol unduh**, watermark nama & NRP pembaca |
| Koleksi Fisik | Eksemplar dengan barcode/QR |
| Sirkulasi | Pinjam, kembali, perpanjang, reservasi, denda/sanksi keterlambatan |
| Keanggotaan | Otomatis dari data personel & siswa; kartu anggota digital |
| Pencarian | Pencarian *full-text* (judul, pengarang, kata kunci) |
| Fitur Pembaca | Bookmark, riwayat baca, koleksi favorit |
| Statistik | Buku terpopuler, pembaca teraktif, kunjungan |
| Usulan Buku | Gadik/siswa mengusulkan pengadaan koleksi |

**Alur Peminjaman Buku Fisik**

```mermaid
flowchart LR
    A[Anggota cari buku di katalog] --> B{Tersedia?}
    B -- Ya --> C[Datang ke perpustakaan / reservasi]
    B -- Tidak --> R[Reservasi antrean]
    C --> D[Pustakawan scan kartu + barcode buku]
    D --> E[Transaksi pinjam - tenggat 7 hari]
    E --> F[Notifikasi H-1 jatuh tempo]
    F --> G[Pengembalian - scan]
    G --> H{Terlambat?}
    H -- Ya --> I[Catat sanksi]
    H -- Tidak --> J[Selesai]
```

---

### 5.5 Modul Sistem
- **Manajemen Pengguna & Role** (Spatie Permission)
- **Audit Log** — mencatat siapa, kapan, data apa, nilai lama & baru (Spatie Activitylog) — bukti penguatan pengawasan ZI
- **Notifikasi** — in-app, email (opsional WhatsApp gateway)
- **Pengaturan** — identitas satuan, logo, bobot indeks Binsat, SLA pengaduan
- **Backup** — otomatis harian (database + file), retensi 30 hari

---

## 6. Rancangan Basis Data (ERD)

### 6.1 Core, Organisasi & Personel

```mermaid
erDiagram
    USERS ||--o| PERSONNEL : "terhubung"
    USERS ||--o| STUDENTS : "terhubung"
    UNITS ||--o{ UNITS : "induk"
    UNITS ||--o{ TOP_POSITIONS : "memiliki"
    TOP_POSITIONS ||--o{ PERSONNEL_ASSIGNMENTS : "diisi"
    PERSONNEL ||--o{ PERSONNEL_ASSIGNMENTS : "menjabat"
    PERSONNEL ||--o{ PERSONNEL_HISTORIES : "riwayat"
    PERSONNEL ||--o{ DAILY_READINESS : "kesiapan"
    RANKS ||--o{ PERSONNEL : "pangkat"

    USERS {
        bigint id PK
        string name
        string username
        string email
        string password
        boolean two_factor_enabled
        timestamp last_login_at
    }
    UNITS {
        bigint id PK
        bigint parent_id FK
        string code
        string name
        string type
    }
    TOP_POSITIONS {
        bigint id PK
        bigint unit_id FK
        string title
        string rank_required
        string corps
        int quota
    }
    PERSONNEL {
        bigint id PK
        bigint user_id FK
        string nrp
        string name
        bigint rank_id FK
        string corps
        bigint unit_id FK
        date tmt_service
        text sensitive_data_encrypted
    }
    PERSONNEL_ASSIGNMENTS {
        bigint id PK
        bigint personnel_id FK
        bigint top_position_id FK
        string type
        date start_date
        date end_date
    }
    DAILY_READINESS {
        bigint id PK
        bigint personnel_id FK
        date date
        string status
    }
```

### 6.2 Materiil & Fasilitas

```mermaid
erDiagram
    MATERIEL_CATEGORIES ||--o{ MATERIELS : "kategori"
    UNITS ||--o{ MATERIELS : "pemegang"
    MATERIELS ||--o{ MAINTENANCES : "harwat"
    MATERIELS ||--o{ MATERIEL_MUTATIONS : "mutasi"
    FACILITIES ||--o{ REPAIR_REQUESTS : "perbaikan"
    FACILITIES ||--o{ FACILITY_BOOKINGS : "pemakaian"
    FACILITIES ||--o{ HOUSING_OCCUPANCIES : "penghuni"
    PERSONNEL ||--o{ HOUSING_OCCUPANCIES : "menghuni"

    MATERIELS {
        bigint id PK
        bigint category_id FK
        bigint unit_id FK
        string code
        string name
        string serial_number
        string condition
        string qr_token
    }
    MAINTENANCES {
        bigint id PK
        bigint materiel_id FK
        date scheduled_at
        date done_at
        string type
        text notes
    }
    FACILITIES {
        bigint id PK
        string name
        string type
        decimal area_m2
        int capacity
        string condition
    }
    REPAIR_REQUESTS {
        bigint id PK
        bigint facility_id FK
        string status
        text description
    }
```

### 6.3 Latihan & Doktrin

```mermaid
erDiagram
    TRAINING_PROGRAMS ||--o{ TRAINING_SESSIONS : "jadwal"
    TRAINING_SESSIONS ||--o{ TRAINING_PARTICIPANTS : "peserta"
    PERSONNEL ||--o{ TRAINING_PARTICIPANTS : "ikut"
    DOCTRINES ||--o{ DOCTRINE_VERSIONS : "versi"
    DOCTRINES ||--o{ DOCTRINE_READS : "dibaca"
    DOCTRINES ||--o{ DOCTRINE_SOCIALIZATIONS : "sosialisasi"

    TRAINING_PROGRAMS {
        bigint id PK
        string name
        string level
        int year
        int month
    }
    TRAINING_SESSIONS {
        bigint id PK
        bigint program_id FK
        bigint facility_id FK
        datetime start_at
        string status
    }
    TRAINING_PARTICIPANTS {
        bigint id PK
        bigint session_id FK
        bigint personnel_id FK
        decimal score
        string result
    }
    DOCTRINES {
        bigint id PK
        string number
        string title
        string type
        string classification
        string status
    }
```

### 6.4 E-Learning

```mermaid
erDiagram
    EDU_PROGRAMS ||--o{ CLASSROOMS : "kelas"
    CLASSROOMS ||--o{ STUDENTS : "anggota"
    CLASSROOMS ||--o{ COURSES : "mapel"
    COURSES ||--o{ LESSONS : "materi"
    COURSES ||--o{ ASSESSMENTS : "evaluasi"
    QUESTION_BANKS ||--o{ QUESTIONS : "soal"
    ASSESSMENTS }o--o{ QUESTIONS : "memakai"
    ASSESSMENTS ||--o{ ATTEMPTS : "dikerjakan"
    STUDENTS ||--o{ ATTEMPTS : "mengerjakan"
    ATTEMPTS ||--o{ ANSWERS : "jawaban"
    STUDENTS ||--o{ LESSON_PROGRESS : "progres"

    COURSES {
        bigint id PK
        bigint classroom_id FK
        bigint teacher_id FK
        string name
        int credit_hours
    }
    LESSONS {
        bigint id PK
        bigint course_id FK
        string title
        string type
        bigint library_item_id FK
        datetime available_at
    }
    ASSESSMENTS {
        bigint id PK
        bigint course_id FK
        string type
        int duration_minutes
        datetime start_at
        datetime end_at
        boolean shuffle
    }
    ATTEMPTS {
        bigint id PK
        bigint assessment_id FK
        bigint student_id FK
        decimal score
        datetime submitted_at
    }
```

### 6.5 E-Pustaka & WBK

```mermaid
erDiagram
    LIBRARY_ITEMS ||--o{ LIBRARY_COPIES : "eksemplar"
    LIBRARY_COPIES ||--o{ LOANS : "dipinjam"
    USERS ||--o{ LOANS : "meminjam"
    LIBRARY_ITEMS ||--o{ READING_LOGS : "dibaca"
    COMPLAINTS ||--o{ COMPLAINT_RESPONSES : "tindak lanjut"
    SURVEYS ||--o{ SURVEY_QUESTIONS : "unsur"
    SURVEYS ||--o{ SURVEY_RESPONSES : "jawaban"

    LIBRARY_ITEMS {
        bigint id PK
        string title
        string author
        string isbn
        string category
        string format
        string file_path
    }
    LOANS {
        bigint id PK
        bigint copy_id FK
        bigint user_id FK
        date borrowed_at
        date due_at
        date returned_at
    }
    COMPLAINTS {
        bigint id PK
        string ticket_no
        string access_code_hash
        boolean is_anonymous
        string category
        string status
        datetime sla_due_at
    }
    SURVEYS {
        bigint id PK
        string type
        string period
    }
```

Tabel pendukung: `posts`, `pages`, `agendas`, `galleries`, `gratification_reports`, `public_info_requests`, `notifications`, `activity_log`, `settings`, `media`.

---

## 7. Sitemap / Struktur Menu

```
PUBLIK  (domain utama)
├── /                     Beranda
├── /profil               Sejarah, Visi-Misi, Struktur, Pejabat
├── /berita               Berita & Agenda
├── /zona-integritas      Program ZI, Inovasi, Maklumat, Standar Pelayanan
├── /informasi-publik     PPID
├── /pengaduan            Form + Lacak Tiket
├── /gratifikasi          Form Lapor Gratifikasi
├── /survei               SKM / IPAK / IPKP + Hasil
├── /pustaka              Katalog Publik (judul & ketersediaan)
└── /login

PANEL STAF  (/panel — Filament)
├── Dashboard Pimpinan (Indeks Binsat, statistik)
├── Binsat
│   ├── Organisasi   ├── Personel   ├── Materiil
│   ├── Fasilitas    ├── Latihan    └── Doktrin
├── Pendidikan (Program, Kelas, Siswa, Gadik)
├── Perpustakaan (Katalog, Eksemplar, Sirkulasi, Anggota)
├── Zona Integritas (Konten Portal, Pengaduan, Gratifikasi, Survei)
├── Laporan
└── Sistem (Pengguna, Role, Audit Log, Pengaturan, Backup)

E-LEARNING  (/belajar — Livewire, ramah mobile)
├── Beranda Siswa (jadwal, tugas tenggat, progres)
├── Kelas Saya → Mapel → Materi / Kuis / Tugas
├── Nilai & Sertifikat
└── Pustaka Digital (Reader)
```

---

## 8. Panduan Desain UI/UX

| Elemen | Ketentuan |
|--------|-----------|
| Warna Primer | Hijau Tentara `#3D5229` / Olive gelap `#2B3A1E` |
| Warna Aksen | Emas `#C9A227` (identitas militer & prestasi) |
| Netral | `#F5F5F0` (terang), `#1A1F16` (mode gelap) |
| Status | Hijau `#2E7D32` Siap · Kuning `#F9A825` Cukup · Merah `#C62828` Kurang |
| Tipografi | **Inter** (UI) & **Poppins/Montserrat** (judul) |
| Gaya | Bersih, formal, *card-based*, mode terang & gelap, ikon Heroicons |
| Aksesibilitas | Kontras WCAG AA, ukuran font ≥ 14px, navigasi keyboard |
| Responsif | Mobile-first untuk E-Learning & Portal; desktop-first untuk Panel Staf |

Wireframe/mockup dibuat pada Minggu 2 (Figma) untuk: Beranda Portal, Dashboard Pimpinan, Form Binsat, Halaman Kelas, Halaman Ujian, Reader E-Book.

---

## 9. Blueprint Keamanan

| Lapisan | Kontrol |
|---------|---------|
| Jaringan | HTTPS/TLS, firewall, Panel Staf idealnya hanya dapat diakses dari **intranet/VPN** |
| Autentikasi | Password kuat, 2FA (TOTP) untuk admin/pimpinan/operator, kunci akun setelah 5x gagal |
| Otorisasi | RBAC + *Policy* Laravel per model; filter data per satuan (*row-level*) |
| Data | Enkripsi kolom sensitif (`encrypted` cast), hash kode akses pengaduan, nama file acak |
| Aplikasi | CSRF, XSS escaping (Blade), Eloquent (anti SQLi), validasi tipe/ukuran file, *rate limiting* form publik, CAPTCHA |
| Konten Digital | Reader tanpa unduh, watermark dinamis, URL bertanda tangan (*signed URL*) berbatas waktu |
| Pengawasan | Audit log seluruh perubahan data, log login, alert aktivitas mencurigakan |
| Ketersediaan | Backup harian terenkripsi, uji restore bulanan |
| Keamanan Sistem | SIPANDU-WBK: otorisasi bertingkat, minimisasi data, audit trail hak akses |

---

## 10. Topologi Deployment

```mermaid
flowchart LR
    I((Internet)) --> FW[Firewall / Reverse Proxy - Nginx]
    FW --> WEB["App Server - PHP-FPM 8.3 + Laravel"]
    LAN((Intranet Satuan)) --> FW
    WEB --> DB[(MySQL 8 / MariaDB)]
    WEB --> R[(Redis)]
    WEB --> Q[Queue Worker - Supervisor]
    WEB --> FS[(Storage)]
    DB --> B[(Backup Server / NAS)]
    FS --> B
```

| Lingkungan | Keterangan |
|-----------|------------|
| **Local** | Laragon (Windows) — developer |
| **Staging** | Server uji untuk demo sprint & UAT |
| **Production** | Server satuan / data center Kodam / cloud pemerintah (sesuai kebijakan) |

Spesifikasi minimum server production: 4 vCPU, 8 GB RAM, 200 GB SSD (+ storage tambahan untuk video/e-book), Ubuntu Server 22.04/24.04 LTS.
