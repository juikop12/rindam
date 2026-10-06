# PETA RELASI ENTITAS BASIS DATA (ERD MAP) — SIPANDU-WBK RINDAM

> **Sistem Informasi Pembinaan Satuan Pendidikan Terpadu berbasis Wilayah Bebas dari Korupsi**  
> Dokumen ini memetakan seluruh arsitektur basis data relasional (*Relational Database Management System* - MySQL 8/MariaDB 10.6+) yang mencakup **5 Kluster Domain**, **45 Tabel Entitas**, hubungan kardinalitas, kamus data (*data dictionary*), serta integrasi lintas modul.

---

## 1. Peta Kluster Basis Data (5 Domain Arsitektur)

Sistem SIPANDU-WBK dirancang dengan konsep *Modular Monolith*. Basis data terbagi dalam 5 kluster logis yang saling terhubung melalui relasi kunci asing (*Foreign Key*):

```mermaid
flowchart TB
    subgraph C1["1. CORE & KEAMANAN"]
        U[(users)]
        R[(roles & permissions)]
        AL[(activity_log)]
        NOT[(notifications)]
        SET[(system_settings)]
    end

    subgraph C2["2. SIM BINSAT 6 KOMPONEN"]
        ORG["Organisasi: units, top_positions"]
        PERS["Personel: personnel, ranks, assignments, readiness, morale"]
        MAT["Materiil: materiels, categories, maintenances, mutations"]
        FAS["Fasilitas: facilities, repairs, housing, bookings"]
        LAT["Latihan: training_programs, sessions, participants"]
        DOK["Doktrin: doctrines, versions, reads, socializations"]
        IND["Indeks: readiness_weights, readiness_logs"]
    end

    subgraph C3["3. E-LEARNING (LMS)"]
        EDU["Pendidikan: edu_programs, classrooms, students"]
        CRS["Pembelajaran: courses, lessons, progress"]
        EXM["Evaluasi: question_banks, questions, assessments, attempts, answers"]
        GRD["Hasil: assignments, submissions, report_cards, certificates"]
    end

    subgraph C4["4. E-PUSTAKA (DIGITAL LIBRARY)"]
        CAT["Katalog: library_items, library_categories"]
        CIR["Sirkulasi: library_copies, library_loans, fines, reservations"]
        LOG["Literasi: reading_logs, book_proposals"]
    end

    subgraph C5["5. PORTAL WBK & ZONA INTEGRITAS"]
        WBS["WBS: complaints, complaint_actions"]
        GRAT["Integritas: gratification_reports, public_info_requests"]
        SRV["Survei: surveys, survey_questions, survey_responses"]
        CMS["Publikasi: portal_posts, service_standards, galleries"]
    end

    %% Integrasi Lintas Kluster
    U --> PERS
    U --> EDU
    U --> CIR
    U --> WBS
    U --> GRAT
    U --> AL

    ORG --> PERS
    ORG --> MAT
    ORG --> FAS
    ORG --> LAT

    FAS -.-> LAT
    FAS -.-> EDU
    PERS -.-> LAT
    PERS -.-> EDU

    CAT -.-> CRS
    CAT -.-> DOK
    EXM -.-> DOK

    ORG --> IND
    PERS --> IND
    MAT --> IND
    FAS --> IND
    LAT --> IND
    DOK --> IND
```

---

## 2. Diagram ERD Global Lintas Domain

Diagram di bawah ini menggambarkan relasi entitas utama dari kelima modul yang saling mengunci (*cross-domain foreign keys*):

```mermaid
erDiagram
    %% CORE RELATIONS
    USERS ||--o| PERSONNEL : "memiliki profil"
    USERS ||--o| STUDENTS : "memiliki akun siswa"
    USERS ||--o{ LIBRARY_LOANS : "meminjam buku"
    USERS ||--o{ COMPLAINT_ACTIONS : "menindaklanjuti"
    USERS ||--o{ GRATIFICATION_REPORTS : "melaporkan"
    USERS ||--o{ DOCTRINE_READS : "mencatat baca"

    %% BINSAT - ORGANISASI & PERSONEL
    UNITS ||--o{ UNITS : "sub-satuan"
    UNITS ||--o{ TOP_POSITIONS : "daftar jabatan TOP"
    UNITS ||--o{ PERSONNEL : "penempatan satuan"
    UNITS ||--o{ MATERIELS : "satuan pemegang"
    UNITS ||--o{ FACILITIES : "lokasi pangkalan"
    UNITS ||--o{ TRAINING_PROGRAMS : "program latihan"
    UNITS ||--o{ READINESS_CALCULATION_LOGS : "indeks Binsat"

    RANKS ||--o{ PERSONNEL : "tingkat kepangkatan"
    TOP_POSITIONS ||--o{ PERSONNEL_ASSIGNMENTS : "posisi jabatan"
    PERSONNEL ||--o{ PERSONNEL_ASSIGNMENTS : "riwayat penempatan"
    PERSONNEL ||--o{ PERSONNEL_HISTORIES : "rekam jejak"
    PERSONNEL ||--o{ DAILY_READINESS : "kesiapan harian"
    PERSONNEL ||--o{ HOUSING_OCCUPANCIES : "menghuni rumdis"
    PERSONNEL ||--o{ TRAINING_PARTICIPANTS : "mengikuti latihan"
    PERSONNEL ||--o{ COURSES : "gadik pengampu"

    %% BINSAT - MATERIIL & FASILITAS
    MATERIEL_CATEGORIES ||--o{ MATERIELS : "kategori bekal/alutsista"
    MATERIELS ||--o{ MATERIEL_MAINTENANCES : "riwayat harwat"
    MATERIELS ||--o{ MATERIEL_MUTATIONS : "riwayat mutasi/distribusi"
    FACILITIES ||--o{ FACILITY_REPAIR_REQUESTS : "permohonan perbaikan"
    FACILITIES ||--o{ FACILITY_BOOKINGS : "jadwal pemakaian"
    FACILITIES ||--o{ HOUSING_OCCUPANCIES : "hunian rumah dinas"
    FACILITIES ||--o{ TRAINING_SESSIONS : "lapangan/sarana latihan"

    %% BINSAT - LATIHAN & DOKTRIN
    TRAINING_PROGRAMS ||--o{ TRAINING_SESSIONS : "jadwal latihan"
    TRAINING_SESSIONS ||--o{ TRAINING_PARTICIPANTS : "rekap nilai peserta"
    DOCTRINES ||--o{ DOCTRINE_VERSIONS : "riwayat revisi naskah"
    DOCTRINES ||--o{ DOCTRINE_READS : "bukti baca naskah"
    DOCTRINES ||--o{ DOCTRINE_SOCIALIZATIONS : "sosialisasi naskah"
    ASSESSMENTS ||--o{ DOCTRINES : "evaluasi pemahaman doktrin"

    %% E-LEARNING
    EDU_PROGRAMS ||--o{ CLASSROOMS : "rombongan belajar/peleton"
    CLASSROOMS ||--o{ STUDENTS : "daftar serdik/siswa"
    CLASSROOMS ||--o{ COURSES : "kurikulum mapel"
    COURSES ||--o{ LESSONS : "materi/hanjar"
    LIBRARY_ITEMS ||--o{ LESSONS : "tautan sumber hanjar"
    COURSES ||--o{ ASSESSMENTS : "kuis dan ujian"
    QUESTION_BANKS ||--o{ QUESTIONS : "bank soal"
    QUESTIONS ||--o{ QUESTION_OPTIONS : "opsi pilihan ganda"
    ASSESSMENTS ||--o{ EXAM_ATTEMPTS : "sesi pengerjaan"
    STUDENTS ||--o{ EXAM_ATTEMPTS : "mengerjakan ujian"
    EXAM_ATTEMPTS ||--o{ EXAM_ANSWERS : "lembar jawaban"
    COURSES ||--o{ ASSIGNMENTS : "tugas terstruktur"
    ASSIGNMENTS ||--o{ ASSIGNMENT_SUBMISSIONS : "unggah tugas siswa"
    STUDENTS ||--o{ STUDENT_REPORT_CARDS : "rapor hasil didik"
    STUDENTS ||--o{ CERTIFICATES : "ijazah/sertifikat digital"

    %% E-PUSTAKA
    LIBRARY_CATEGORIES ||--o{ LIBRARY_ITEMS : "klasifikasi DDC"
    LIBRARY_ITEMS ||--o{ LIBRARY_COPIES : "eksemplar fisik"
    LIBRARY_COPIES ||--o{ LIBRARY_LOANS : "transaksi peminjaman"
    LIBRARY_LOANS ||--o| LIBRARY_FINES : "denda/sanksi keterlambatan"
    LIBRARY_ITEMS ||--o{ LIBRARY_READING_LOGS : "riwayat baca digital"
    LIBRARY_ITEMS ||--o{ LIBRARY_BOOK_RESERVATIONS : "antrean reservasi"

    %% WBK & ZONA INTEGRITAS
    COMPLAINTS ||--o{ COMPLAINT_ACTIONS : "tindak lanjut pengaduan/WBS"
    SURVEYS ||--o{ SURVEY_QUESTIONS : "indikator kepuasan"
    SURVEYS ||--o{ SURVEY_RESPONSES : "data responden SKM/IPAK/IPKP"
```

---

## 3. Rincian Kamus Data Per Kluster (Data Dictionary)

### 3.1 Kluster 1: Core & Keamanan Sistem

| Tabel | Kolom Utama | Tipe Data | Keterangan Relasi / Atribut |
|---|---|---|---|
| **`users`** | `id` (PK)<br>`name`<br>`username` (Unique)<br>`email` (Unique)<br>`password`<br>`phone`<br>`role_type`<br>`two_factor_secret`<br>`two_factor_confirmed_at`<br>`is_active`<br>`last_login_at` | BIGINT UNSIGNED<br>VARCHAR(150)<br>VARCHAR(50)<br>VARCHAR(100)<br>VARCHAR(255)<br>VARCHAR(25)<br>ENUM<br>TEXT<br>TIMESTAMP<br>BOOLEAN<br>TIMESTAMP | Akun akses terpusat seluruh role (Super Admin, Danrindam, Staf Binsat, Gadik, Siswa, Pustakawan, Tim ZI, Publik Terdaftar). Dilengkapi 2FA TOTP. |
| **`roles`** | `id` (PK)<br>`name`<br>`guard_name` | BIGINT UNSIGNED<br>VARCHAR(50)<br>VARCHAR(50) | Manajemen Peran Spatie Permission (`super_admin`, `pimpinan`, `operator_binsat`, `gadik`, `siswa`, `pustakawan`, `tim_zi`, `publik`). |
| **`permissions`** | `id` (PK)<br>`name`<br>`guard_name` | BIGINT UNSIGNED<br>VARCHAR(100)<br>VARCHAR(50) | Hak akses spesifik granular (contoh: `create_personnel`, `approve_mutation`, `view_readiness_index`). |
| **`activity_log`** | `id` (PK)<br>`log_name`<br>`description`<br>`subject_type`<br>`subject_id`<br>`causer_type`<br>`causer_id` (FK)<br>`properties`<br>`created_at` | BIGINT UNSIGNED<br>VARCHAR(50)<br>TEXT<br>VARCHAR(100)<br>BIGINT UNSIGNED<br>VARCHAR(100)<br>BIGINT UNSIGNED<br>JSON<br>TIMESTAMP | Jejak audit digital tak terhapuskan (*immutable audit trail*) untuk pemenuhan Area 5 ZI (Penguatan Pengawasan). |
| **`system_settings`** | `id` (PK)<br>`key` (Unique)<br>`value`<br>`group`<br>`description` | BIGINT UNSIGNED<br>VARCHAR(100)<br>TEXT<br>VARCHAR(50)<br>VARCHAR(255) | Konfigurasi sistem: batas SLA pengaduan, bobot Indeks Binsat, watermark viewer, informasi identitas satuan. |

---

### 3.2 Kluster 2: SIM Binsat 6 Komponen

#### A. Pembinaan Organisasi & Personel
| Tabel | Kolom Utama | Tipe Data | Keterangan Relasi / Atribut |
|---|---|---|---|
| **`units`** | `id` (PK)<br>`parent_id` (FK nullable)<br>`code` (Unique)<br>`name`<br>`type`<br>`level`<br>`is_active` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(30)<br>VARCHAR(150)<br>ENUM<br>TINYINT<br>BOOLEAN | Pohon struktur satuan (Rindam → Dodikjur/Dodiklatpur/Depo Pendidikan → Seksi/Urusan). Self-referencing ke `parent_id`. |
| **`top_positions`** | `id` (PK)<br>`unit_id` (FK)<br>`position_name`<br>`rank_required`<br>`corps_required`<br>`quota_top`<br>`notes` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(150)<br>VARCHAR(50)<br>VARCHAR(30)<br>INT UNSIGNED<br>TEXT | Daftar formasi jabatan resmi menurut TOP/DSPP yang disahkan pimpinan TNI AD. |
| **`ranks`** | `id` (PK)<br>`code`<br>`name`<br>`category`<br>`order_level` | BIGINT UNSIGNED<br>VARCHAR(20)<br>VARCHAR(100)<br>ENUM<br>TINYINT | Standar kepangkatan militer (Pamen, Pama, Bintara Tinggi, Bintara, Tamtama, PNS). |
| **`personnel`** | `id` (PK)<br>`user_id` (FK nullable)<br>`nrp` (Unique)<br>`full_name`<br>`rank_id` (FK)<br>`corps`<br>`unit_id` (FK)<br>`birth_date`<br>`tmt_service`<br>`tmt_rank`<br>`sensitive_data_encrypted`<br>`photo_path`<br>`status` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(30)<br>VARCHAR(150)<br>BIGINT UNSIGNED<br>VARCHAR(30)<br>BIGINT UNSIGNED<br>DATE<br>DATE<br>DATE<br>TEXT<br>VARCHAR(255)<br>ENUM | Master data prajurit/pegawai organik. Data rahasia/sensitif (NIK, riwayat kesehatan, gaji) dienkripsi dengan AES-256 (`encrypted` cast). |
| **`personnel_assignments`** | `id` (PK)<br>`personnel_id` (FK)<br>`top_position_id` (FK)<br>`assignment_type`<br>`sk_number`<br>`start_date`<br>`end_date`<br>`is_current` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>ENUM<br>VARCHAR(100)<br>DATE<br>DATE nullable<br>BOOLEAN | Riwayat pemenuhan formasi jabatan. Menghitung otomatis kesesuaian Nyata vs TOP dan jabatan kosong/rangkap. |
| **`daily_readiness`** | `id` (PK)<br>`personnel_id` (FK)<br>`unit_id` (FK)<br>`date`<br>`status`<br>`notes` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>DATE<br>ENUM<br>VARCHAR(255) | Kesiapan harian prajurit: `Hadir`, `Dinas Luar`, `Cuti`, `Sakit`, `Pendidikan`, `Tanpa Keterangan`. Dihitung setiap apel pagi. |
| **`morale_records`** | `id` (PK)<br>`unit_id` (FK)<br>`period`<br>`activity_type`<br>`participant_count`<br>`morale_score`<br>`documentation_path` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(7)<br>ENUM<br>INT UNSIGNED<br>DECIMAL(5,2)<br>VARCHAR(255) | Pembinaan moril prajurit: kegiatan ibadah/bintal, olahraga gabungan, rekam penghargaan & penegakan disiplin. |

#### B. Pembinaan Materiil & Fasilitas Pangkalan
| Tabel | Kolom Utama | Tipe Data | Keterangan Relasi / Atribut |
|---|---|---|---|
| **`materiel_categories`** | `id` (PK)<br>`code`<br>`name`<br>`type`<br>`description` | BIGINT UNSIGNED<br>VARCHAR(20)<br>VARCHAR(100)<br>ENUM<br>TEXT | Klasifikasi bekal/logistik: `Senjata`, `Munisi`, `Kendaraan Bermotor`, `Alat Komunikasi`, `Alins/Alongins`, `Perlengkapan Perorangan`. |
| **`materiels`** | `id` (PK)<br>`category_id` (FK)<br>`unit_id` (FK)<br>`code` (Unique)<br>`name`<br>`serial_number`<br>`acquisition_year`<br>`condition`<br>`is_operational`<br>`qr_code_token`<br>`notes` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(50)<br>VARCHAR(150)<br>VARCHAR(100)<br>YEAR<br>ENUM('B','RR','RB')<br>BOOLEAN<br>VARCHAR(64)<br>TEXT | Inventaris materiil. Dilengkapi kode token QR untuk pemindaian fisik dan kondisi kesiapan operasional. |
| **`materiel_maintenances`** | `id` (PK)<br>`materiel_id` (FK)<br>`scheduled_date`<br>`completed_date`<br>`maintenance_type`<br>`technician_name`<br>`cost`<br>`condition_after`<br>`notes` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>DATE<br>DATE nullable<br>VARCHAR(100)<br>VARCHAR(100)<br>DECIMAL(14,2)<br>ENUM('B','RR','RB')<br>TEXT | Rekam pemeliharaan dan perawatan (Harwat) terjadwal maupun insidentil. |
| **`materiel_mutations`** | `id` (PK)<br>`materiel_id` (FK)<br>`from_unit_id` (FK)<br>`to_unit_id` (FK)<br>`sk_number`<br>`mutation_date`<br>`status`<br>`approved_by` (FK) | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(100)<br>DATE<br>ENUM<br>BIGINT UNSIGNED | Riwayat penerimaan, pergeseran logistik antar-seksi/dodik, hingga penghapusan bekal. |
| **`facilities`** | `id` (PK)<br>`unit_id` (FK)<br>`code` (Unique)<br>`name`<br>`facility_type`<br>`area_m2`<br>`capacity`<br>`condition`<br>`photo_path`<br>`address` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(50)<br>VARCHAR(150)<br>ENUM<br>DECIMAL(10,2)<br>INT UNSIGNED<br>ENUM('B','RR','RB')<br>VARCHAR(255)<br>TEXT | Pangkalan militer: Markas Komando, barak serdik, kelas belajar, rumah dinas prajurit, lapangan tembak, lapangan upacara. |
| **`facility_repair_requests`**| `id` (PK)<br>`facility_id` (FK)<br>`reported_by` (FK)<br>`description`<br>`photo_damage_path`<br>`urgency`<br>`status`<br>`cost_estimate` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>TEXT<br>VARCHAR(255)<br>ENUM<br>ENUM<br>DECIMAL(14,2) | Tiket kerusakan fasilitas fisik: diajukan → diverifikasi Pejabat Logistik → dikerjakan Staf Fasjas → diselesaikan. |
| **`housing_occupancies`** | `id` (PK)<br>`facility_id` (FK)<br>`personnel_id` (FK)<br>`sip_number`<br>`start_date`<br>`end_date`<br>`status`<br>`family_count` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(100)<br>DATE<br>DATE nullable<br>ENUM<br>TINYINT | Tertib penghunian Rumah Dinas (Rumdis) berbasis Surat Izin Penghunian (SIP) resmi. |
| **`facility_bookings`** | `id` (PK)<br>`facility_id` (FK)<br>`booked_by` (FK)<br>`purpose`<br>`start_time`<br>`end_time`<br>`status` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(200)<br>DATETIME<br>DATETIME<br>ENUM | Peminjaman fasilitas umum/kelas/lapangan dengan kalender bentrok jadwal otomatis. |

#### C. Pembinaan Latihan, Doktrin, dan Indeks Kesiapan
| Tabel | Kolom Utama | Tipe Data | Keterangan Relasi / Atribut |
|---|---|---|---|
| **`training_programs`** | `id` (PK)<br>`unit_id` (FK)<br>`name`<br>`level`<br>`year`<br>`quarter`<br>`target_participants`<br>`description` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(150)<br>ENUM<br>YEAR<br>TINYINT<br>INT UNSIGNED<br>TEXT | Rencana program latihan (Renlat) bertahap, bertingkat, berlanjut: Latihan Perorangan Dasar (Latorsar), UTJ, Menembak, Garjas. |
| **`training_sessions`** | `id` (PK)<br>`program_id` (FK)<br>`facility_id` (FK)<br>`instructor_personnel_id` (FK)<br>`title`<br>`start_at`<br>`end_at`<br>`status` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(150)<br>DATETIME<br>DATETIME<br>ENUM | Pelaksanaan sesi latihan terhubung ke modul Fasilitas (lokasi) dan Personel (pelatih). |
| **`training_participants`** | `id` (PK)<br>`session_id` (FK)<br>`personnel_id` (FK)<br>`attendance`<br>`theory_score`<br>`practice_score`<br>`final_score`<br>`result` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BOOLEAN<br>DECIMAL(5,2)<br>DECIMAL(5,2)<br>DECIMAL(5,2)<br>ENUM('Lulus','TidakLulus') | Penilaian kemampuan perorangan hasil latihan fisik, taktik, dan ketangkasan militer. |
| **`doctrines`** | `id` (PK)<br>`document_number`<br>`title`<br>`doctrine_type`<br>`classification`<br>`year`<br>`issuing_authority`<br>`status`<br>`assessment_id` (FK nullable) | BIGINT UNSIGNED<br>VARCHAR(100)<br>VARCHAR(200)<br>ENUM<br>ENUM('Biasa','Terbatas')<br>YEAR<br>VARCHAR(100)<br>ENUM('Berlaku','Dicabut')<br>BIGINT UNSIGNED | Repositori Doktrin, Bujuk, Juknis, Protap, dan SOP dinas. Terhubung ke kuis evaluasi LMS untuk uji pemahaman. |
| **`doctrine_versions`** | `id` (PK)<br>`doctrine_id` (FK)<br>`version_number`<br>`revision_notes`<br>`file_path`<br>`effective_date`<br>`uploaded_by` (FK) | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(20)<br>TEXT<br>VARCHAR(255)<br>DATE<br>BIGINT UNSIGNED | Riwayat amandemen naskah dinas agar prajurit selalu mengacu pada versi naskah terbitan mutakhir. |
| **`doctrine_reads`** | `id` (PK)<br>`doctrine_id` (FK)<br>`user_id` (FK)<br>`read_at`<br>`duration_seconds`<br>`ip_address` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>TIMESTAMP<br>INT UNSIGNED<br>VARCHAR(45) | Bukti kepatuhan literasi doktrin militer oleh segenap prajurit. |
| **`readiness_weight_configs`**| `id` (PK)<br>`component_name` (Unique)<br>`weight_percentage`<br>`formula_code`<br>`is_active` | BIGINT UNSIGNED<br>VARCHAR(50)<br>DECIMAL(5,2)<br>VARCHAR(100)<br>BOOLEAN | Konfigurasi bobot komponen Binsat (Organisasi 15%, Pers 20%, Mat 20%, Fas 15%, Lat 20%, Dok 10%). |
| **`readiness_calculation_logs`**| `id` (PK)<br>`unit_id` (FK)<br>`period_date`<br>`org_score`<br>`pers_score`<br>`mat_score`<br>`fas_score`<br>`lat_score`<br>`dok_score`<br>`total_index`<br>`status`<br>`calculated_at` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>DATE<br>DECIMAL(5,2)<br>DECIMAL(5,2)<br>DECIMAL(5,2)<br>DECIMAL(5,2)<br>DECIMAL(5,2)<br>DECIMAL(5,2)<br>DECIMAL(5,2)<br>ENUM('SIAP','CUKUP','KURANG')<br>TIMESTAMP | Arsip rekam nilai Indeks Kesiapan Satuan untuk tren visual radar chart dan laporan wasrik. |

---

### 3.3 Kluster 3: E-Learning (Learning Management System)

| Tabel | Kolom Utama | Tipe Data | Keterangan Relasi / Atribut |
|---|---|---|---|
| **`education_programs`** | `id` (PK)<br>`code` (Unique)<br>`name`<br>`category`<br>`batch_number`<br>`year`<br>`start_date`<br>`end_date`<br>`is_active` | BIGINT UNSIGNED<br>VARCHAR(30)<br>VARCHAR(150)<br>ENUM<br>VARCHAR(20)<br>YEAR<br>DATE<br>DATE<br>BOOLEAN | Program pendidikan resmi: Diktukba, Diktukta, Dikjur Ba/Ta, Pendidikan Bela Negara, dsb. |
| **`classrooms`** | `id` (PK)<br>`program_id` (FK)<br>`code` (Unique)<br>`name`<br>`platoon_name`<br>`commander_personnel_id` (FK)<br>`capacity` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(30)<br>VARCHAR(100)<br>VARCHAR(50)<br>BIGINT UNSIGNED<br>INT UNSIGNED | Rombongan belajar per kelas / kompi / peleton serdik dengan Komandan Peleton pengasuh. |
| **`students`** | `id` (PK)<br>`user_id` (FK nullable)<br>`classroom_id` (FK)<br>`nosik` (Unique)<br>`full_name`<br>`rank`<br>`origin_unit`<br>`status`<br>`photo_path` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(30)<br>VARCHAR(150)<br>VARCHAR(50)<br>VARCHAR(100)<br>ENUM<br>VARCHAR(255) | Data induk serdik/siswa siswa pendidikan. Terhubung ke akun pengguna untuk login LMS. |
| **`courses`** | `id` (PK)<br>`classroom_id` (FK)<br>`instructor_personnel_id` (FK)<br>`code`<br>`name`<br>`credit_hours_jp`<br>`passing_grade`<br>`description` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(30)<br>VARCHAR(150)<br>INT UNSIGNED<br>DECIMAL(5,2)<br>TEXT | Mata Pelajaran (Mapel) yang diajarkan oleh Gadik pengampu. |
| **`lessons`** | `id` (PK)<br>`course_id` (FK)<br>`library_item_id` (FK nullable)<br>`title`<br>`lesson_type`<br>`file_path`<br>`content_text`<br>`drip_available_at`<br>`order_no` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(200)<br>ENUM<br>VARCHAR(255)<br>LONGTEXT<br>DATETIME nullable<br>TINYINT | Bahan ajar per pertemuan. Terintegrasi ke E-Pustaka (`library_item_id`) untuk hanjar digital. |
| **`lesson_progresses`** | `id` (PK)<br>`lesson_id` (FK)<br>`student_id` (FK)<br>`is_completed`<br>`completed_at`<br>`last_page_read` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BOOLEAN<br>TIMESTAMP nullable<br>INT UNSIGNED | Pemantauan tingkat kepatuhan membaca materi oleh siswa secara individual. |
| **`question_banks`** | `id` (PK)<br>`course_id` (FK)<br>`title`<br>`created_by` (FK) | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(150)<br>BIGINT UNSIGNED | Wadah bank soal ujian untuk memudahkan pengacakan dan penggunaan berulang. |
| **`questions`** | `id` (PK)<br>`bank_id` (FK)<br>`question_text`<br>`question_type`<br>`points`<br>`explanation` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>TEXT<br>ENUM<br>DECIMAL(5,2)<br>TEXT | Soal evaluasi: Pilihan Ganda (PG), Benar/Salah, atau Esai Uraian. |
| **`question_options`** | `id` (PK)<br>`question_id` (FK)<br>`option_label`<br>`option_text`<br>`is_correct` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>CHAR(1)<br>TEXT<br>BOOLEAN | Pilihan jawaban A, B, C, D, E untuk jenis soal pilihan ganda. |
| **`assessments`** | `id` (PK)<br>`course_id` (FK)<br>`title`<br>`assessment_type`<br>`duration_minutes`<br>`start_time`<br>`end_time`<br>`shuffle_questions`<br>`passing_grade`<br>`is_published` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(150)<br>ENUM<br>INT UNSIGNED<br>DATETIME<br>DATETIME<br>BOOLEAN<br>DECIMAL(5,2)<br>BOOLEAN | Ujian terikat waktu: Kuis harian, UTS, UAS, Ujian Ulang (Remedial), atau Uji Doktrin. |
| **`exam_attempts`** | `id` (PK)<br>`assessment_id` (FK)<br>`student_id` (FK)<br>`token_used`<br>`started_at`<br>`submitted_at`<br>`total_score`<br>`is_passed`<br>`status` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(20)<br>DATETIME<br>DATETIME nullable<br>DECIMAL(5,2)<br>BOOLEAN<br>ENUM | Sesi pengerjaan ujian siswa dengan validasi token dan batas waktu otomatis (*timeout*). |
| **`exam_answers`** | `id` (PK)<br>`attempt_id` (FK)<br>`question_id` (FK)<br>`selected_option_id` (FK nullable)<br>`essay_text`<br>`score_awarded`<br>`is_reviewed`<br>`reviewed_by` (FK) | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>TEXT<br>DECIMAL(5,2)<br>BOOLEAN<br>BIGINT UNSIGNED | Lembar jawaban autosave per butir soal. PG dinilai otomatis, esai dikoreksi manual Gadik. |
| **`assignments`** | `id` (PK)<br>`course_id` (FK)<br>`title`<br>`instructions`<br>`attachment_path`<br>`due_date`<br>`max_score` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(200)<br>TEXT<br>VARCHAR(255)<br>DATETIME<br>DECIMAL(5,2) | Lembar tugas mandiri atau kelompok dari Gadik kepada serdik. |
| **`assignment_submissions`**| `id` (PK)<br>`assignment_id` (FK)<br>`student_id` (FK)<br>`file_path`<br>`notes`<br>`submitted_at`<br>`score`<br>`feedback`<br>`graded_by` (FK) | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(255)<br>TEXT<br>DATETIME<br>DECIMAL(5,2)<br>TEXT<br>BIGINT UNSIGNED | Berkas tugas yang dikirim serdik beserta nilai dan catatan evaluasi dari Gadik. |
| **`student_report_cards`** | `id` (PK)<br>`classroom_id` (FK)<br>`student_id` (FK)<br>`theory_gpa`<br>`practice_gpa`<br>`attitude_score`<br>`final_gpa`<br>`ranking`<br>`issued_at` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>DECIMAL(5,2)<br>DECIMAL(5,2)<br>DECIMAL(5,2)<br>DECIMAL(5,2)<br>INT UNSIGNED<br>DATE | Rapor hasil didik lengkap (Triratna Teladan: Tanggon, Tanggap, Trengginas). |
| **`certificates`** | `id` (PK)<br>`student_id` (FK)<br>`program_id` (FK)<br>`certificate_number`<br>`qr_hash`<br>`pdf_path`<br>`issue_date`<br>`is_valid` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(100)<br>VARCHAR(64)<br>VARCHAR(255)<br>DATE<br>BOOLEAN | Ijazah / Sertifikat kelulusan elektronik dengan QR Code verifikasi integritas publik. |

---

### 3.4 Kluster 4: E-Pustaka (Digital Library)

| Tabel | Kolom Utama | Tipe Data | Keterangan Relasi / Atribut |
|---|---|---|---|
| **`library_categories`** | `id` (PK)<br>`ddc_code`<br>`name`<br>`description` | BIGINT UNSIGNED<br>VARCHAR(20)<br>VARCHAR(100)<br>TEXT | Pengelompokan klasifikasi buku berdasarkan sistem DDC (Dewey Decimal Classification) militer. |
| **`library_items`** | `id` (PK)<br>`category_id` (FK)<br>`title`<br>`author`<br>`publisher`<br>`publish_year`<br>`isbn`<br>`format_type`<br>`total_copies`<br>`available_copies`<br>`digital_file_path`<br>`cover_path`<br>`shelf_location`<br>`description` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(200)<br>VARCHAR(150)<br>VARCHAR(150)<br>YEAR<br>VARCHAR(30)<br>ENUM<br>INT UNSIGNED<br>INT UNSIGNED<br>VARCHAR(255)<br>VARCHAR(255)<br>VARCHAR(50)<br>TEXT | Katalog koleksi induk. Format bisa `Buku Cetak`, `E-Book`, `Hanjar Militer`, atau `Jurnal Ilmiah`. Berkas digital disimpan di private storage. |
| **`library_copies`** | `id` (PK)<br>`library_item_id` (FK)<br>`barcode_rfid` (Unique)<br>`copy_number`<br>`condition`<br>`status` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(50)<br>INT UNSIGNED<br>ENUM('Baik','Rusak','Hilang')<br>ENUM('Tersedia','Dipinjam','Perawatan') | Eksemplar buku fisik dengan nomor barcode/RFID individual untuk sirkulasi cepat di meja perpustakaan. |
| **`library_loans`** | `id` (PK)<br>`copy_id` (FK)<br>`user_id` (FK)<br>`borrow_date`<br>`due_date`<br>`return_date`<br>`status`<br>`processed_by` (FK) | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>DATE<br>DATE<br>DATE nullable<br>ENUM('Dipinjam','Kembali','Terlambat','Hilang')<br>BIGINT UNSIGNED | Transaksi peminjaman buku fisik (standar pinjam 7 hari dengan notifikasi pengingat H-1). |
| **`library_fines`** | `id` (PK)<br>`loan_id` (FK)<br>`late_days`<br>`fine_amount`<br>`disciplinary_notes`<br>`is_settled`<br>`settled_at` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>INT UNSIGNED<br>DECIMAL(10,2)<br>VARCHAR(255)<br>BOOLEAN<br>TIMESTAMP nullable | Sanksi/denda keterlambatan pengembalian buku fisik (denda materiil atau sanksi disiplin satuan). |
| **`library_reading_logs`** | `id` (PK)<br>`library_item_id` (FK)<br>`user_id` (FK)<br>`read_at`<br>`duration_minutes`<br>`pages_viewed`<br>`ip_address` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>TIMESTAMP<br>INT UNSIGNED<br>INT UNSIGNED<br>VARCHAR(45) | Statistik pembaca buku digital untuk pemeringkatan buku terfavorit dan pembaca teraktif. |
| **`library_book_reservations`**| `id` (PK)<br>`library_item_id` (FK)<br>`user_id` (FK)<br>`reservation_date`<br>`status` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>DATETIME<br>ENUM('Antre','Tersedia','Kedaluwarsa','Batal') | Antrean reservasi buku fisik apabila seluruh eksemplar sedang berada di tangan peminjam lain. |
| **`library_book_proposals`** | `id` (PK)<br>`proposed_by` (FK)<br>`title`<br>`author`<br>`publisher`<br>`reason`<br>`status` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(200)<br>VARCHAR(150)<br>VARCHAR(150)<br>TEXT<br>ENUM | Usulan penambahan literatur baru dari Gadik dan serdik untuk pengadaan seksi perpustakaan. |

---

### 3.5 Kluster 5: Portal WBK & Pengawasan Integritas

| Tabel | Kolom Utama | Tipe Data | Keterangan Relasi / Atribut |
|---|---|---|---|
| **`complaints`** | `id` (PK)<br>`ticket_number` (Unique)<br>`access_code_hash`<br>`reporter_name`<br>`reporter_contact`<br>`is_anonymous`<br>`category`<br>`title`<br>`description`<br>`evidence_file_path`<br>`status`<br>`sla_due_at`<br>`is_overdue`<br>`created_at` | BIGINT UNSIGNED<br>VARCHAR(30)<br>VARCHAR(255)<br>VARCHAR(100) nullable<br>VARCHAR(100) nullable<br>BOOLEAN<br>ENUM<br>VARCHAR(200)<br>TEXT<br>VARCHAR(255)<br>ENUM<br>DATETIME<br>BOOLEAN<br>TIMESTAMP | Whistleblowing System (WBS) & Pengaduan Masyarakat. Mendukung mode anonim, kode akses di-hash bcrypt, pelacakan ber-SLA. |
| **`complaint_actions`** | `id` (PK)<br>`complaint_id` (FK)<br>`action_by` (FK)<br>`action_type`<br>`response_text`<br>`internal_notes`<br>`attachment_path`<br>`created_at` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>ENUM<br>TEXT nullable<br>TEXT nullable<br>VARCHAR(255)<br>TIMESTAMP | Buku rekam tindak lanjut pengaduan: Verifikasi Tim ZI, Disposisi Komandan, Tindak Lanjut Seksi, Tanggapan Publik. |
| **`gratification_reports`** | `id` (PK)<br>`reporter_user_id` (FK)<br>`event_date`<br>`report_type`<br>`item_description`<br>`estimated_value`<br>`giver_identity`<br>`chronology`<br>`evidence_path`<br>`status` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>DATE<br>ENUM('Penerimaan','Penolakan')<br>TEXT<br>DECIMAL(14,2)<br>VARCHAR(150)<br>TEXT<br>VARCHAR(255)<br>ENUM | Pelaporan penerimaan atau penolakan gratifikasi oleh prajurit/pegawai (Komponen Pengawasan ZI Area 5). |
| **`public_info_requests`** | `id` (PK)<br>`request_number` (Unique)<br>`applicant_name`<br>`nik`<br>`email`<br>`phone`<br>`requested_info`<br>`purpose`<br>`status`<br>`completed_at` | BIGINT UNSIGNED<br>VARCHAR(30)<br>VARCHAR(100)<br>VARCHAR(20)<br>VARCHAR(100)<br>VARCHAR(25)<br>TEXT<br>TEXT<br>ENUM<br>TIMESTAMP nullable | Layanan Pejabat Pengelola Informasi dan Dokumentasi (PPID) sesuai UU Keterbukaan Informasi Publik No. 14/2008. |
| **`surveys`** | `id` (PK)<br>`title`<br>`survey_type`<br>`year`<br>`quarter`<br>`is_active`<br>`description` | BIGINT UNSIGNED<br>VARCHAR(150)<br>ENUM('SKM','IPAK','IPKP')<br>YEAR<br>TINYINT<br>BOOLEAN<br>TEXT | Survei resmi: Survei Kepuasan Masyarakat (SKM PermenPAN-RB 14/2017), Indeks Persepsi Anti Korupsi (IPAK), & IPKP. |
| **`survey_questions`** | `id` (PK)<br>`survey_id` (FK)<br>`code`<br>`question_text`<br>`domain_element`<br>`order_no` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>VARCHAR(10)<br>TEXT<br>VARCHAR(100)<br>TINYINT | 9 Butir Unsur SKM (Persyaratan, Prosedur, Waktu, Biaya, Produk, Kompetensi, Perilaku, Sarpras, Pengaduan). |
| **`survey_responses`** | `id` (PK)<br>`survey_id` (FK)<br>`respondent_type`<br>`answers_json`<br>`calculated_score`<br>`submitted_at`<br>`ip_address` | BIGINT UNSIGNED<br>BIGINT UNSIGNED<br>ENUM<br>JSON<br>DECIMAL(5,2)<br>TIMESTAMP<br>VARCHAR(45) | Data isian responden dengan perhitungan otomatis nilai interval kepuasan (A = Sangat Baik, B = Baik, C = Kurang). |
| **`portal_posts`** | `id` (PK)<br>`title`<br>`slug` (Unique)<br>`category`<br>`content`<br>`featured_image`<br>`author_id` (FK)<br>`published_at`<br>`view_count` | BIGINT UNSIGNED<br>VARCHAR(200)<br>VARCHAR(200)<br>ENUM<br>LONGTEXT<br>VARCHAR(255)<br>BIGINT UNSIGNED<br>DATETIME nullable<br>INT UNSIGNED | CMS Berita kegiatan satuan, pengumuman pendidikan, dokumentasi sosialisasi ZI, dan artikel wawasan kebangsaan. |
| **`portal_service_standards`**| `id` (PK)<br>`service_name`<br>`requirements`<br>`procedure`<br>`time_sla`<br>`fee`<br>`output_product`<br>`complaint_channel` | BIGINT UNSIGNED<br>VARCHAR(150)<br>TEXT<br>TEXT<br>VARCHAR(100)<br>VARCHAR(50)<br>VARCHAR(150)<br>TEXT | Publikasi resmi Standar Pelayanan Publik satuan (Persyaratan, Biaya Rp 0,-, Waktu Selesai, Jaminan Layanan). |

---

## 4. Matriks Integritas Kunci Asing Antar-Domain (Cross-Domain Foreign Keys)

Tabel berikut menunjukkan bagaimana entitas dari satu domain menjadi fondasi bagi domain lainnya:

| Entitas Sumber | Kolom Kunci Asing (FK) | Entitas Target | Kegunaan Bisnis |
|---|---|---|---|
| `personnel` | `user_id` | `users(id)` | Prajurit dapat login ke sistem untuk membaca doktrin, cek kesiapan, pinjam buku, atau lapor gratifikasi. |
| `students` | `user_id` | `users(id)` | Serdik login ke portal E-Learning untuk mengikuti ujian, membaca materi, dan mengecek rapor. |
| `personnel` | `rank_id` | `ranks(id)` | Menstandarkan kepangkatan untuk perhitungan pemenuhan formasi jabatan TOP. |
| `top_positions` | `unit_id` | `units(id)` | Struktur formasi jabatan melekat pada hierarki satuan organisasi. |
| `materiels` | `unit_id` | `units(id)` | Akuntabilitas penanggung jawab satuan pemegang logistik dan alutsista. |
| `facilities` | `unit_id` | `units(id)` | Pemeliharaan pangkalan dan aset fisik di bawah kendali seksi/dodik terkait. |
| `lessons` | `library_item_id` | `library_items(id)` | **Integrasi LMS & E-Pustaka**: Bahan ajar di kelas langsung mengambil rujukan naskah digital ber-watermark. |
| `doctrines` | `assessment_id` | `assessments(id)` | **Integrasi Binsat & LMS**: Naskah doktrin yang diwajibkan langsung memiliki kuis evaluasi pemahaman di LMS. |
| `training_sessions` | `facility_id` | `facilities(id)` | **Integrasi Latihan & Fasilitas**: Penjadwalan latihan lapangan mengunci ketersediaan fasilitas agar tidak bentrok. |
| `library_loans` | `user_id` | `users(id)` | Prajurit dan siswa dapat meminjam buku fisik dengan akun terpadu satuan. |
| `complaints` | - | `users(id)` (opsional) | Pengaduan dapat disampaikan oleh publik (anonim) atau prajurit internal terautentikasi. |

---

## 5. Standar Normalisasi & Optimasi Kinerja

1. **Bentuk Normalisasi**: Skema didesain pada **Third Normal Form (3NF)** untuk mencegah redundansi data dan inkonsistensi saat operasi *update* atau *delete*.
2. **Indeks Performa (Database Indexing)**:
   - Index pada seluruh kolom *Foreign Key* (`*_id`).
   - Unique Index pada kolom identitas utama: `users(email, username)`, `personnel(nrp)`, `students(nosik)`, `materiels(code)`, `complaints(ticket_number)`.
   - Composite Index untuk kueri frekuensi tinggi:
     - `daily_readiness(unit_id, date)`
     - `exam_attempts(assessment_id, student_id)`
     - `library_loans(user_id, status)`
     - `complaints(status, sla_due_at)`
3. **Penyimpanan Data Aman (Security At-Rest)**:
   - Kolom rahasia di tabel `personnel` dan `survey_responses` menggunakan *Laravel Eloquent Encrypted Cast* (AES-256-CBC).
   - Seluruh berkas PDF, naskah doktrin berklasifikasi terbatas, dan e-book disimpan di direktori `storage/app/private/` yang tidak dapat diakses langsung via URL web, melainkan hanya melalui *Signed URL Controller* dengan verifikasi hak akses dan watermark dinamis.
