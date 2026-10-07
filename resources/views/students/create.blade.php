@extends('layouts.app')

@section('title', 'Pendaftaran Siswa Baru per Satdik — SIPANDU-WBK')

@section('content')

<!-- BREADCRUMB -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted);">
        <a href="{{ route('students.index') }}" style="color:var(--o700); text-decoration:none; font-weight:600;">Data Serdik</a>
        <span class="ms" style="font-size:16px;">chevron_right</span>
        <span style="color:var(--text); font-weight:700;">Tambah Siswa Baru</span>
    </div>
    <a href="{{ route('students.index') }}" class="btn btn-outline btn-sm">
        <span class="ms">arrow_back</span> Kembali ke Daftar
    </a>
</div>

<!-- HEADER BANNER -->
<div class="header-banner" style="margin-bottom:24px;">
    <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <span class="pdp-badge">
                <span class="ms" style="font-size:16px;">lock</span> SIPANDU-WBK SECURITY
            </span>
            <span class="pdp-badge" style="background:rgba(255,255,255,0.15);color:#fff;border-color:rgba(255,255,255,0.25);">
                <span class="ms" style="font-size:16px;">auto_awesome</span> AUTO-GENERATE NOSIS
            </span>
        </div>
        <h1>Registrasi Peserta Didik Baru per Satdik</h1>
        <p>
            Entri data serdik terpartisi berdasarkan Satdik masing-masing. Informasi pribadi sensitif otomatis dienkripsi dengan standar militer AES-256-CBC saat disimpan ke repositori data.
        </p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-warning" style="background:#FFEBEE; border-color:#FFCDD2; color:var(--red); margin-bottom:24px;">
        <span class="ms" style="font-size:24px;">error</span>
        <div>
            <b>Terdapat kesalahan pengisian formulir:</b>
            <ul style="margin:4px 0 0 16px; padding:0; font-size:12.5px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('students.store') }}" method="POST" id="studentForm">
    @csrf

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">

        <!-- KOLOM 1: DATA KEMILITERAN & PENDIDIKAN -->
        <div class="card" style="margin-bottom:0;">
            <div class="card-header" style="background:var(--o50);">
                <h3 class="card-title">
                    <span class="ms" style="color:var(--o700);">military_tech</span> Bagian A: Data Kemiliteran & Satdik
                </h3>
                <span class="badge badge-satdik">Identitas Publik Satdik</span>
            </div>

            <div class="card-body">
                <!-- SATDIK SELECTOR -->
                <div class="form-group">
                    <label class="form-label" for="satdik_id">
                        Satuan Pendidikan (Satdik) <span style="color:var(--red);">*</span>
                    </label>
                    <select name="satdik_id" id="satdik_id" class="form-control" required onchange="handleSatdikChange(this.value)">
                        @foreach($satdiks as $satdik)
                            <option value="{{ $satdik->id }}" {{ old('satdik_id', $selectedSatdikId) == $satdik->id ? 'selected' : '' }}>
                                {{ $satdik->code }} — {{ $satdik->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Pilih Satdik tempat siswa mengikuti pendidikan. NOSIS otomatis berawalan kode Satdik ini.</div>
                </div>

                <!-- PROGRAM PENDIDIKAN (CASCADING) -->
                <div class="form-group">
                    <label class="form-label" for="education_program_id">
                        Program Pendidikan <span style="color:var(--red);">*</span>
                    </label>
                    <select name="education_program_id" id="education_program_id" class="form-control" required onchange="handleProgramChange(this.value)">
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ old('education_program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->name }} (TA {{ $program->academic_year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- KELAS / PELETON (CASCADING & MANUAL BY SISTEM) -->
                <div class="form-group">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <label class="form-label" for="classroom_id" style="margin-bottom:0;">
                            Peleton / Kompi Siswa
                        </label>
                        <button type="button" class="btn btn-outline btn-sm" onclick="openQuickClassroomModal()" style="font-size:11px; padding:3px 8px; border-color:var(--gold); color:var(--o800); display:inline-flex; align-items:center; gap:4px;">
                            <span class="ms" style="font-size:14px; color:var(--gold);">add_circle</span> + Buat Kompi & Peleton Baru
                        </button>
                    </div>

                    <div id="classroomSelectWrapper">
                        <select name="classroom_id" id="classroom_id" class="form-control" onchange="handleClassroomSelect(this.value)">
                            <option value="">-- Pilih Peleton / Kompi --</option>
                            @foreach($classrooms as $cls)
                                <option value="{{ $cls->id }}" {{ old('classroom_id') == $cls->id ? 'selected' : '' }}>
                                    {{ $cls->name }} (Kapasitas: {{ $cls->capacity }})
                                </option>
                            @endforeach
                        </select>
                        <div style="margin-top:6px; display:flex; justify-content:space-between; align-items:center;">
                            <span class="form-hint" style="margin-top:0;">Pilih dari daftar peleton atau klik tombol di atas untuk membuat peleton baru.</span>
                            <a href="javascript:void(0)" onclick="toggleManualClassroomInput(true)" style="font-size:11.5px; color:var(--o800); font-weight:700; text-decoration:underline;">
                                atau ketik manual Kompi & Peleton
                            </a>
                        </div>
                    </div>

                    <!-- INPUT MANUAL TEKS KOMPI & PELETON -->
                    <div id="classroomManualWrapper" style="display:none; background:#F8F9F7; border:1px dashed #CBD5E1; padding:12px; border-radius:8px; margin-top:8px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <b style="font-size:12px; color:var(--o800);">Ketik Kompi & Peleton Manual:</b>
                            <a href="javascript:void(0)" onclick="toggleManualClassroomInput(false)" style="font-size:11.5px; color:var(--red); font-weight:600; text-decoration:none;">
                                ✕ Batal, gunakan pilihan dropdown
                            </a>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                            <div>
                                <label style="font-size:11px; font-weight:700; color:var(--muted); display:block; margin-bottom:3px;">Kompi Siswa</label>
                                <input type="text" list="studentCompanyList" name="company" id="manual_company" class="form-control" placeholder="Contoh: Kompi A">
                                <datalist id="studentCompanyList">
                                    <option value="Kompi A">
                                    <option value="Kompi B">
                                    <option value="Kompi C">
                                    <option value="Kompi Senapan A">
                                    <option value="Kompi Senapan B">
                                    <option value="Kompi Senapan C">
                                    <option value="Kompi Bantuan">
                                    <option value="Kompi Markas">
                                    <option value="Kompi Bela Negara">
                                </datalist>
                            </div>
                            <div>
                                <label style="font-size:11px; font-weight:700; color:var(--muted); display:block; margin-bottom:3px;">Peleton Siswa</label>
                                <input type="text" list="studentPlatoonList" name="platoon" id="manual_platoon" class="form-control" placeholder="Contoh: Peleton 1">
                                <datalist id="studentPlatoonList">
                                    <option value="Peleton 1">
                                    <option value="Peleton 2">
                                    <option value="Peleton 3">
                                    <option value="Peleton 4">
                                    <option value="Peleton Bantuan">
                                    <option value="Peleton Taktik">
                                    <option value="Peleton Senban">
                                    <option value="Peleton Runduk">
                                </datalist>
                            </div>
                        </div>
                        <div class="form-hint" style="margin-top:6px;">Sistem otomatis membuat rombongan belajar dan menghubungkannya ke serdik ini.</div>
                    </div>
                </div>

                <div style="border-top:1px dashed var(--line); margin:20px 0 16px;"></div>

                <!-- NAMA LENGKAP -->
                <div class="form-group">
                    <label class="form-label" for="full_name">
                        Nama Lengkap Siswa / Serdik <span style="color:var(--red);">*</span>
                    </label>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="{{ old('full_name') }}" placeholder="Contoh: Muhammad Rizky Pratama" required>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label" for="student_rank">
                            Pangkat Siswa <span style="color:var(--red);">*</span>
                        </label>
                        <select name="student_rank" id="student_rank" class="form-control" required>
                            <option value="Siswa Secaba" {{ old('student_rank') == 'Siswa Secaba' ? 'selected' : '' }}>Siswa Secaba</option>
                            <option value="Siswa Secata" {{ old('student_rank') == 'Siswa Secata' ? 'selected' : '' }}>Siswa Secata</option>
                            <option value="Prada Siswa" {{ old('student_rank') == 'Prada Siswa' ? 'selected' : '' }}>Prada Siswa</option>
                            <option value="Serda Siswa" {{ old('student_rank') == 'Serda Siswa' ? 'selected' : '' }}>Serda Siswa</option>
                            <option value="Kader Bela Negara" {{ old('student_rank') == 'Kader Bela Negara' ? 'selected' : '' }}>Kader Bela Negara</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="status">
                            Status Siswa & Kategori Hitung <span style="color:var(--red);">*</span>
                        </label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif — Siswa Aktif Terhitung (Pendidikan Berjalan)</option>
                            <option value="Sakit" {{ old('status') == 'Sakit' ? 'selected' : '' }}>Sakit — Siswa Aktif Terhitung (Dalam Perawatan)</option>
                            <option value="Dinas Luar" {{ old('status') == 'Dinas Luar' ? 'selected' : '' }}>Dinas Luar — Siswa Aktif Terhitung (Penugasan Luar)</option>
                            <option value="Selesai" {{ old('status') == 'Selesai' ? 'selected' : '' }}>Selesai — Arsip Siswa Selesai Pendidikan (Tidak Terhitung)</option>
                            <option value="Lulus" {{ old('status') == 'Lulus' ? 'selected' : '' }}>Lulus — Arsip Siswa Lulus (Tidak Terhitung)</option>
                            <option value="DO / Dikeluarkan" {{ old('status') == 'DO / Dikeluarkan' ? 'selected' : '' }}>DO / Dikeluarkan — Arsip (Tidak Terhitung)</option>
                        </select>
                        <div class="form-hint">Hanya siswa status <b>Aktif, Sakit, atau Dinas Luar</b> yang terhitung dalam kekuatan pendidikan berjalan.</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="origin_military_unit">Kodam / Kodim Asal</label>
                    <input type="text" name="origin_military_unit" id="origin_military_unit" list="kodimList" class="form-control" value="{{ old('origin_military_unit') }}" placeholder="Pilih atau ketik satuan asal, contoh: Kodam III/Slw - Kodim 0618/Kota Bandung">
                    <datalist id="kodimList">
                        <option value="Kodam III/Slw - Kodim 0618/Kota Bandung">
                        <option value="Kodam III/Slw - Kodim 0609/Cimahi">
                        <option value="Kodam III/Slw - Kodim 0624/Kab. Bandung">
                        <option value="Kodam III/Slw - Kodim 0607/Kota Sukabumi">
                        <option value="Kodam III/Slw - Kodim 0611/Garut">
                        <option value="Kodam III/Slw - Kodim 0606/Kota Bogor">
                        <option value="Kodam III/Slw - Kodim 0612/Tasikmalaya">
                        <option value="Kodam III/Slw - Kodim 0614/Kota Cirebon">
                        <option value="Kodam Jaya - Kodim 0501/Jakarta Pusat">
                        <option value="Kodam IV/Dip - Kodim 0733/Kota Semarang">
                        <option value="Kodam V/Brw - Kodim 0832/Surabaya Selatan">
                    </datalist>
                    <div class="form-hint">Satuan komando kewilayahan pengirim prajurit siswa (mendukung pencarian otomatis).</div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label" for="birth_place">Tempat Lahir</label>
                        <input type="text" name="birth_place" id="birth_place" class="form-control" value="{{ old('birth_place') }}" placeholder="Contoh: Magelang">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="birth_date">Tanggal Lahir</label>
                        <input type="date" name="birth_date" id="birth_date" class="form-control" value="{{ old('birth_date') }}">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label" for="gender">Jenis Kelamin <span style="color:var(--red);">*</span></label>
                        <select name="gender" id="gender" class="form-control" required>
                            <option value="L" {{ old('gender', 'L') == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="religion">Agama</label>
                        <select name="religion" id="religion" class="form-control">
                            <option value="Islam" {{ old('religion', 'Islam') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('religion') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('religion') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('religion') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="blood_type">Gol. Darah</label>
                        <select name="blood_type" id="blood_type" class="form-control">
                            <option value="O" {{ old('blood_type', 'O') == 'O' ? 'selected' : '' }}>O</option>
                            <option value="A" {{ old('blood_type') == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('blood_type') == 'B' ? 'selected' : '' }}>B</option>
                            <option value="AB" {{ old('blood_type') == 'AB' ? 'selected' : '' }}>AB</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM 2: DATA PRIBADI SENSITIF TERPROTEKSI (SIPANDU-WBK) -->
        <div class="card" style="margin-bottom:0; border: 1.5px solid var(--gold);">
            <div class="card-header" style="background:var(--gold-bg); border-bottom:1px solid #E8D9A8;">
                <h3 class="card-title" style="color:#7A5C07;">
                    <span class="ms">lock</span> Bagian B: Data Pribadi Sensitif Terproteksi
                </h3>
                <span class="badge" style="background:#FFF3CD; color:#856404; border:1px solid #FFEEBA; font-size:11px;">
                    AES-256 Otomatis
                </span>
            </div>

            <div class="card-body">
                <div class="alert alert-warning" style="padding:10px 14px; font-size:12px; margin-bottom:18px;">
                    <span class="ms" style="font-size:20px;">info</span>
                    <div>
                        Kolom-kolom di bawah ini akan <b>dienkripsi penuh secara kriptografis</b> saat disimpan. Operator Satdik hanya akan melihat bentuk tersamar (*masked*).
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label" for="nik">
                            NIK (16 Digit KTP) <span style="color:var(--red);">*</span>
                        </label>
                        <input type="text" name="nik" id="nik" class="form-control" maxlength="16" minlength="16" value="{{ old('nik') }}" placeholder="Contoh: 3308123456780001" required>
                        <div class="form-hint">Harus 16 angka numerik valid (Identitas unik utama pencegah data serdik ganda).</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="family_card_number">Nomor KK (Kartu Keluarga)</label>
                        <input type="text" name="family_card_number" id="family_card_number" class="form-control" maxlength="16" value="{{ old('family_card_number') }}" placeholder="Contoh: 3308123456780002">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label" for="mother_name">
                            Nama Ibu Kandung <span style="color:var(--red);">*</span>
                        </label>
                        <input type="text" name="mother_name" id="mother_name" class="form-control" value="{{ old('mother_name') }}" placeholder="Contoh: Siti Rahayu" required>
                        <div class="form-hint">Kunci validasi verifikasi perbankan & kedinasan.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="father_name">Nama Ayah Kandung</label>
                        <input type="text" name="father_name" id="father_name" class="form-control" value="{{ old('father_name') }}" placeholder="Contoh: Bambang Sudarsono">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="emergency_contact_phone">
                        No HP Kontak Darurat / Orang Tua <span style="color:var(--red);">*</span>
                    </label>
                    <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}" placeholder="Contoh: 081234567890" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="home_address">Alamat Domisili KTP</label>
                    <textarea name="home_address" id="home_address" class="form-control" rows="3" placeholder="Contoh: Jl. Pahlawan No. 45, RT 02 / RW 05, Magelang">{{ old('home_address') }}</textarea>
                </div>

                <div style="background:#E8F5E9; border:1px solid #C8E6C9; border-radius:10px; padding:12px 14px; margin-top:16px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="ms" style="color:#1B5E20; font-size:20px;">info</span>
                        <div>
                            <b style="color:#1B5E20; font-size:12.5px;">Pemisahan Modul Kesehatan Serdik:</b>
                            <div style="font-size:11.5px; color:#2E7D32; line-height:1.4; margin-top:2px;">
                                Sesuai SOP pengamanan data sistem SIPANDU-WBK, data kondisi kesehatan fisik, riwayat medis, dan kondisi kesiapan dicatat serta dikelola tersendiri pada <b>Menu Kesehatan Serdik</b> oleh Tim Poliklinik Satdik.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- KARTU 3: DATA FISIK & STATUS KESEHATAN AWAL (SELARAS FORMAT EXCEL) -->
    <div class="card" style="margin-bottom:24px; border-left:4px solid var(--green);">
        <div class="card-header" style="background:#F6FBF7; border-bottom:1px solid #DCE7DD;">
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:var(--green); font-size:22px;">monitor_heart</span>
                <h3 class="card-title">Bagian C: Data Fisik & Kebugaran Awal Siswa (Selaras Format Excel)</h3>
            </div>
            <span class="badge badge-green">Inisialisasi Rekam Medis</span>
        </div>
        <div class="card-body">
            <div style="font-size:12.5px; color:var(--muted); margin-bottom:16px; line-height:1.5;">
                Kolom di bawah ini selaras dengan <b>Kolom N s.d. S pada Berkas Excel</b>. Mengisi kolom ini otomatis membentuk lembar rekam medis dan kesiapan fisik serdik pada <b>Modul Kesehatan Serdik</b>.
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:16px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="height_cm">Tinggi Badan (TB cm)</label>
                    <input type="number" name="height_cm" id="height_cm" class="form-control" value="{{ old('height_cm') }}" placeholder="Contoh: 172" min="100" max="250">
                    <div class="form-hint">Centimeter (Kolom N Excel).</div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="weight_kg">Berat Badan (BB kg)</label>
                    <input type="number" step="0.5" name="weight_kg" id="weight_kg" class="form-control" value="{{ old('weight_kg') }}" placeholder="Contoh: 68" min="30" max="200">
                    <div class="form-hint">Kilogram (Kolom O Excel).</div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="blood_pressure">Tensi Tekanan Darah</label>
                    <input type="text" name="blood_pressure" id="blood_pressure" class="form-control" value="{{ old('blood_pressure', '120/80') }}" placeholder="Contoh: 120/80">
                    <div class="form-hint">mmHg (Kolom P Excel).</div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="daily_health_status">Status Kesiapan Fisik</label>
                    <select name="daily_health_status" id="daily_health_status" class="form-control">
                        <option value="Siap Latih" {{ old('daily_health_status', 'Siap Latih') == 'Siap Latih' ? 'selected' : '' }}>Sehat Penuh</option>
                        <option value="Berobat Jalan" {{ old('daily_health_status') == 'Berobat Jalan' ? 'selected' : '' }}>Berobat Jalan / Dispen</option>
                        <option value="Rawat Inap Poliklinik" {{ old('daily_health_status') == 'Rawat Inap Poliklinik' ? 'selected' : '' }}>Rawat Inap Poliklinik</option>
                        <option value="Rujuk Rumkit" {{ old('daily_health_status') == 'Rujuk Rumkit' ? 'selected' : '' }}>Rujuk Rumkit Dinas</option>
                    </select>
                    <div class="form-hint">Kolom Q Excel.</div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="stakes_grade">Stakes Militer</label>
                    <select name="stakes_grade" id="stakes_grade" class="form-control">
                        <option value="Stakes I (Sangat Baik)" {{ old('stakes_grade', 'Stakes I (Sangat Baik)') == 'Stakes I (Sangat Baik)' ? 'selected' : '' }}>Stakes I (Sangat Baik)</option>
                        <option value="Stakes II (Baik)" {{ old('stakes_grade') == 'Stakes II (Baik)' ? 'selected' : '' }}>Stakes II (Baik)</option>
                        <option value="Stakes III (Kurang/Dispen)" {{ old('stakes_grade') == 'Stakes III (Kurang/Dispen)' ? 'selected' : '' }}>Stakes III (Kurang/Dispen)</option>
                        <option value="Stakes IV (Tidak Memenuhi Syarat)" {{ old('stakes_grade') == 'Stakes IV (Tidak Memenuhi Syarat)' ? 'selected' : '' }}>Stakes IV (TMS)</option>
                    </select>
                    <div class="form-hint">Kolom R Excel.</div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="doctor_notes">Catatan Medis Awal & Riwayat Alergi</label>
                <input type="text" name="doctor_notes" id="doctor_notes" class="form-control" value="{{ old('doctor_notes') }}" placeholder="Contoh: Bebas riwayat alergi obat, kondisi fisik prima siap latihan (Kolom S Excel)">
            </div>
        </div>
    </div>

    <!-- FORM ACTION FOOTER -->
    <div class="card">
        <div class="card-body" style="display:flex; align-items:center; justify-content:space-between; padding:18px 24px;">
            <div style="font-size:12.5px; color:var(--muted); display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:var(--green); font-size:20px;">verified</span>
                <span>Dengan menyimpan formulir ini, NOSIS otomatis dibuat dan integritas data pribadi diproteksi AES-256.</span>
            </div>
            <div style="display:flex; gap:12px;">
                <a href="{{ route('students.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-gold" style="padding:10px 24px; font-size:14px;">
                    <span class="ms">save</span> Daftarkan & Enkripsi Data
                </button>
            </div>
        </div>
    </div>
</form>

<!-- =====================================================================
     MODAL CEPAT BUAT KOMPI & PELETON BARU BY SISTEM
     ===================================================================== -->
<div class="modal-overlay" id="quickClassroomModal">
    <div class="modal-card" style="max-width:520px;">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:var(--gold2); font-size:22px;">add_circle</span>
                <b style="font-family:'Montserrat',sans-serif; font-size:15px;">Buat Kompi & Peleton Baru</b>
            </div>
            <button type="button" onclick="closeQuickClassroomModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:20px;">&times;</button>
        </div>

        <form id="quickClassroomForm" onsubmit="submitQuickClassroom(event)">
            @csrf
            <div class="modal-body" style="padding:20px;">
                <div id="quickClassroomAlert" style="display:none; padding:10px 12px; border-radius:8px; margin-bottom:14px; font-size:12.5px;"></div>

                <div style="background:#F4F6F2; border:1px solid #E2E6DF; padding:10px 14px; border-radius:8px; margin-bottom:16px; font-size:12px;">
                    Dibuat untuk Program: <b id="quickProgramLabel">-</b>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Nama Kompi: <span style="color:var(--red);">*</span></label>
                        <input type="text" list="studentCompanyList" name="company" id="qc_company" class="form-control" placeholder="Contoh: Kompi A" oninput="updateQcName()" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Peleton: <span style="color:var(--red);">*</span></label>
                        <input type="text" list="studentPlatoonList" name="platoon" id="qc_platoon" class="form-control" placeholder="Contoh: Peleton 1" oninput="updateQcName()" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap Peleton / Rombel: <span style="color:var(--red);">*</span></label>
                    <input type="text" name="name" id="qc_name" class="form-control" placeholder="Kompi A Peleton 1" required>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Danton / Danki (Opsional):</label>
                        <input type="text" name="platoon_leader_name" id="qc_danton" class="form-control" placeholder="Contoh: Lettu Inf Suryadi">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kapasitas Siswa:</label>
                        <input type="number" name="capacity" id="qc_capacity" class="form-control" value="35" min="1" max="250">
                    </div>
                </div>
            </div>

            <div style="padding:14px 20px; background:#FAFBF9; border-top:1px solid var(--line); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeQuickClassroomModal()">Batal</button>
                <button type="submit" class="btn btn-gold" id="btnSaveQc">
                    <span class="ms">save</span> Simpan & Pasang Peleton Ini
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    window.currentPrograms = @json($programs);

    function handleSatdikChange(satdikId) {
        if(!satdikId) return;

        const progSelect = document.getElementById('education_program_id');
        const classSelect = document.getElementById('classroom_id');
        
        progSelect.disabled = true;
        classSelect.disabled = true;
        progSelect.innerHTML = '<option value="">Memuat program pendidikan...</option>';
        classSelect.innerHTML = '<option value="">-- Pilih Peleton / Kompi --</option>';

        fetch(`/api/satdiks/${satdikId}/programs`)
            .then(res => res.json())
            .then(data => {
                progSelect.innerHTML = '';
                window.currentPrograms = data.programs || [];
                if(data.programs && data.programs.length > 0) {
                    data.programs.forEach(prog => {
                        const opt = document.createElement('option');
                        opt.value = prog.id;
                        opt.textContent = `${prog.name} (TA ${prog.academic_year})`;
                        progSelect.appendChild(opt);
                    });

                    // Populate classes for first program
                    populateClasses(data.programs[0]);
                } else {
                    progSelect.innerHTML = '<option value="">Tidak ada program aktif</option>';
                }
                progSelect.disabled = false;
                classSelect.disabled = false;

                // Adjust default rank suggestion based on Satdik
                adjustRankSuggestion(data.satdik.code);
            })
            .catch(err => {
                console.error(err);
                progSelect.disabled = false;
                classSelect.disabled = false;
            });
    }

    function handleProgramChange(progId) {
        if (!window.currentPrograms) return;
        const prog = window.currentPrograms.find(p => p.id == progId);
        if (prog) {
            populateClasses(prog);
        }
    }

    function populateClasses(program) {
        const classSelect = document.getElementById('classroom_id');
        classSelect.innerHTML = '<option value="">-- Pilih Peleton / Kompi --</option>';
        if(program && program.classrooms && program.classrooms.length > 0) {
            program.classrooms.forEach(cls => {
                const opt = document.createElement('option');
                opt.value = cls.id;
                opt.textContent = `${cls.name} (Kapasitas: ${cls.capacity})`;
                classSelect.appendChild(opt);
            });
        }
    }

    function handleClassroomSelect(val) {
        // Jika sudah memilih dari dropdown, bersihkan field manual
        if(val) {
            document.getElementById('manual_company').value = '';
            document.getElementById('manual_platoon').value = '';
        }
    }

    function toggleManualClassroomInput(showManual) {
        const selectWrap = document.getElementById('classroomSelectWrapper');
        const manualWrap = document.getElementById('classroomManualWrapper');
        if(showManual) {
            manualWrap.style.display = 'block';
            selectWrap.style.opacity = '0.5';
            document.getElementById('classroom_id').value = '';
            document.getElementById('manual_company').focus();
        } else {
            manualWrap.style.display = 'none';
            selectWrap.style.opacity = '1';
            document.getElementById('manual_company').value = '';
            document.getElementById('manual_platoon').value = '';
        }
    }

    function openQuickClassroomModal() {
        const progSelect = document.getElementById('education_program_id');
        const selectedText = progSelect.options[progSelect.selectedIndex]?.text || 'Program Pendidikan';
        document.getElementById('quickProgramLabel').textContent = selectedText;
        
        document.getElementById('quickClassroomAlert').style.display = 'none';
        document.getElementById('qc_company').value = '';
        document.getElementById('qc_platoon').value = '';
        document.getElementById('qc_name').value = '';
        document.getElementById('qc_danton').value = '';
        document.getElementById('qc_capacity').value = '35';

        document.getElementById('quickClassroomModal').classList.add('active');
        setTimeout(() => document.getElementById('qc_company').focus(), 100);
    }

    function closeQuickClassroomModal() {
        document.getElementById('quickClassroomModal').classList.remove('active');
    }

    function updateQcName() {
        const comp = document.getElementById('qc_company').value.trim();
        const plt = document.getElementById('qc_platoon').value.trim();
        const parts = [comp, plt].filter(Boolean);
        if(parts.length > 0) {
            document.getElementById('qc_name').value = parts.join(' ');
        }
    }

    function submitQuickClassroom(e) {
        e.preventDefault();
        const progId = document.getElementById('education_program_id').value;
        if(!progId) {
            alert('Pilih Program Pendidikan terlebih dahulu!');
            return;
        }

        const btn = document.getElementById('btnSaveQc');
        btn.disabled = true;
        btn.innerHTML = '<span class="ms">hourglass_empty</span> Menyimpan...';

        const payload = {
            education_program_id: progId,
            company: document.getElementById('qc_company').value.trim(),
            platoon: document.getElementById('qc_platoon').value.trim(),
            name: document.getElementById('qc_name').value.trim(),
            platoon_leader_name: document.getElementById('qc_danton').value.trim(),
            capacity: document.getElementById('qc_capacity').value,
            _token: document.querySelector('input[name="_token"]').value
        };

        fetch('/classrooms', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': payload._token
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<span class="ms">save</span> Simpan & Pasang Peleton Ini';

            if(data.status === 'success') {
                const classSelect = document.getElementById('classroom_id');
                const newOpt = document.createElement('option');
                newOpt.value = data.classroom.id;
                newOpt.textContent = `${data.classroom.name} (Kapasitas: ${data.classroom.capacity})`;
                newOpt.selected = true;
                classSelect.appendChild(newOpt);

                // Pastikan mode dropdown aktif
                toggleManualClassroomInput(false);
                closeQuickClassroomModal();

                // Notifikasi visual
                alert(`Kompi & Peleton [${data.classroom.name}] berhasil dibuat dan langsung dipilih!`);
            } else {
                const alertEl = document.getElementById('quickClassroomAlert');
                alertEl.style.display = 'block';
                alertEl.style.background = '#FFEBEE';
                alertEl.style.color = 'var(--red)';
                alertEl.textContent = data.message || 'Gagal menyimpan peleton.';
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<span class="ms">save</span> Simpan & Pasang Peleton Ini';
            console.error(err);
            alert('Terjadi kesalahan koneksi saat membuat peleton.');
        });
    }

    function adjustRankSuggestion(satdikCode) {
        const rankSelect = document.getElementById('student_rank');
        if(satdikCode === 'SECABA') {
            rankSelect.value = 'Siswa Secaba';
        } else if(satdikCode === 'SECATA') {
            rankSelect.value = 'Siswa Secata';
        } else if(satdikCode === 'DODIKJUR') {
            rankSelect.value = 'Prada Siswa';
        } else if(satdikCode === 'DODIKLATPUR') {
            rankSelect.value = 'Prada Siswa';
        } else if(satdikCode === 'DODIK BELA NEGARA') {
            rankSelect.value = 'Kader Bela Negara';
        }
    }

    window.addEventListener('click', function(e) {
        const qModal = document.getElementById('quickClassroomModal');
        if (e.target === qModal) closeQuickClassroomModal();
    });
</script>
@endsection
