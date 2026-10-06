@extends('layouts.app')

@section('title', 'Pengolahan Data Siswa per Satdik — SIPANDU-WBK')

@section('content')

<!-- HEADER BANNER -->
<div class="header-banner">
    <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <span class="pdp-badge">
                <span class="ms" style="font-size:16px;">lock</span> SIPANDU-WBK SECURITY
            </span>
            <span class="pdp-badge" style="background:rgba(255,255,255,0.15);color:#fff;border-color:rgba(255,255,255,0.25);">
                <span class="ms" style="font-size:16px;">shield</span> ENKRIPSI AES-256-CBC
            </span>
        </div>
        <h1>Pengolahan Data Siswa per Satuan Pendidikan (Satdik)</h1>
        <p>
            Pengelolaan peserta didik militer terpartisi antar Satdik dan Program Pendidikan (DIKMABA, DIKJURBA, DIKMATA, dll.). Hanya prajurit siswa berstatus <b>Aktif</b> yang terhitung dalam kekuatan pendidikan, sedangkan status <b>Selesai</b> otomatis dialihkan ke Arsip.
        </p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('students.import', ['satdik_id' => $selectedSatdikId]) }}" class="btn btn-outline" style="background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.3);">
            <span class="ms">upload_file</span> Impor Format Excel
        </a>
        <button type="button" class="btn btn-gold" onclick="openCreateStudentModal()">
            <span class="ms">person_add</span> Tambah Siswa Baru
        </button>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-warning" style="background:#FFEBEE; border-color:#FFCDD2; color:var(--red); margin-bottom:20px; border-radius:10px; padding:14px 18px; display:flex; align-items:flex-start; gap:12px;">
        <span class="ms" style="font-size:24px; color:#DC2626;">error</span>
        <div>
            <b style="font-size:14px;">Terdapat kesalahan penginputan data:</b>
            <ul style="margin:4px 0 0 16px; padding:0; font-size:12.5px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if(session('import_warnings'))
    <div class="alert alert-warning" style="background:#FFF9C4; border-color:#FFF59D; color:#795548; margin-bottom:20px;">
        <span class="ms" style="font-size:24px;">info</span>
        <div>
            <b>Catatan / Peringatan Penginputan Excel:</b>
            <ul style="margin:4px 0 0 16px; padding:0; font-size:12.5px;">
                @foreach (session('import_warnings') as $w)
                    <li>{{ $w }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!-- STATS SUMMARY: AKTIF TERHITUNG VS ARSIP SELESAI -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--green), #2E7D32);">
            <span class="ms">how_to_reg</span>
        </div>
        <div>
            <div class="stat-val" style="color:var(--green);">{{ $stats['overall_counted'] }}</div>
            <div class="stat-lbl">Siswa Aktif Terhitung (Pendidikan Berjalan)</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                {{ $stats['overall_active'] }} Siap Latih • {{ $stats['overall_sick'] }} Sakit/Dispen
            </small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, #475569, #1E293B);">
            <span class="ms">archive</span>
        </div>
        <div>
            <div class="stat-val" style="color:#475569;">{{ $stats['overall_archived'] }}</div>
            <div class="stat-lbl">Arsip Siswa Selesai (Tidak Terhitung)</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                {{ $stats['overall_finished'] }} Selesai/Lulus Pendidikan
            </small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--o700), var(--o500));">
            <span class="ms">school</span>
        </div>
        <div>
            <div class="stat-val">{{ count($stats['program_list'] ?? []) }}</div>
            <div class="stat-lbl">Program Pendidikan (TA 2026)</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                DIKMABA, DIKJURBA, DIKMATA, dll.
            </small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, #1565C0, #1E88E5);">
            <span class="ms">domain</span>
        </div>
        <div>
            <div class="stat-val">5</div>
            <div class="stat-lbl">Satdik Rindam III/Siliwangi</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                Total Rekam: {{ $stats['overall_total'] }} Serdik
            </small>
        </div>
    </div>
</div>

<!-- SATDIK NAVIGATION TABS -->
@if(auth()->check() && !auth()->user()->isPimpinan() && auth()->user()->satdik_id)
    <div class="satdik-nav" style="margin-bottom:20px;">
        <div class="satdik-tab active" style="cursor:default; background:var(--o800); color:var(--gold2); border-color:var(--gold); box-shadow:0 4px 12px rgba(29,42,22,0.15);">
            <span class="ms" style="color:var(--gold);">lock</span> 
            <span>SATDIK ANDA: <b>{{ auth()->user()->satdik?->code }}</b> — {{ auth()->user()->satdik?->name }}</span>
            <span class="tab-badge" style="background:var(--gold); color:var(--o900); font-weight:800;">
                🔒 Terkunci Sesuai Wewenang Akun
            </span>
        </div>
    </div>
@else
    <div class="satdik-nav">
        <a href="{{ route('students.index', array_merge(request()->except(['satdik_id', 'page']))) }}" class="satdik-tab {{ empty($selectedSatdikId) ? 'active' : '' }}">
            <span class="ms">domain</span> Semua Satdik
            <span class="tab-badge" title="Siswa Aktif Terhitung">{{ $stats['overall_counted'] }} Aktif</span>
        </a>

        @foreach($satdiks as $satdik)
            @php
                $satdikCounted = $satdik->counted_students ?? $satdik->students()->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count();
            @endphp
            <a href="{{ route('students.index', array_merge(request()->except(['page']), ['satdik_id' => $satdik->id])) }}" class="satdik-tab {{ $selectedSatdikId == $satdik->id ? 'active' : '' }}">
                <span class="ms">military_tech</span> {{ $satdik->code }}
                <span class="tab-badge" title="Siswa Aktif Terhitung">{{ $satdikCounted }} Aktif</span>
            </a>
        @endforeach
    </div>
@endif

<!-- SECTION REKAPITULASI PROGRAM PENDIDIKAN -->
<div style="background:#fff; border:1px solid var(--line); border-radius:12px; padding:18px 20px; margin-bottom:20px; box-shadow:var(--shadow-sm);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:36px; height:36px; border-radius:8px; background:var(--o100); color:var(--o800); display:grid; place-items:center;">
                <span class="ms" style="font-size:20px;">layers</span>
            </div>
            <div>
                <h4 style="margin:0; font-size:14.5px; font-weight:800; color:var(--o900); text-transform:uppercase; letter-spacing:0.04em;">
                    Rekapitulasi Kekuatan per Program Pendidikan
                </h4>
                <div style="color:var(--muted); font-size:12px; margin-top:2px;">
                    Klik salah satu program untuk menyaring daftar prajurit siswa di bawah
                </div>
            </div>
        </div>
        @if($selectedProgramId)
            <a href="{{ route('students.index', array_merge(request()->except(['program_id', 'page']))) }}" class="btn btn-outline btn-sm" style="font-size:12px; padding:6px 12px; border-color:var(--line); color:var(--muted);">
                <span class="ms" style="font-size:15px;">close</span> Tampilkan Semua Program
            </a>
        @endif
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(210px, 1fr)); gap:12px;">
        @foreach($stats['program_list'] as $prog)
            @php
                $isProgActive = ($selectedProgramId == $prog['id']);
            @endphp
            <a href="{{ route('students.index', array_merge(request()->except(['page']), ['program_id' => $isProgActive ? null : $prog['id']])) }}"
               style="text-decoration:none; display:block; padding:12px 14px; border-radius:10px; border:2px solid {{ $isProgActive ? 'var(--gold)' : 'var(--line)' }}; background:{{ $isProgActive ? '#FFFDF5' : '#FAFAFA' }}; transition:all 0.15s ease; box-shadow:{{ $isProgActive ? '0 4px 12px rgba(201,162,39,0.18)' : 'none' }};">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
                    <span style="font-weight:800; font-size:13px; color:{{ $isProgActive ? 'var(--o800)' : 'var(--text)' }};">
                        {{ $prog['name'] }}
                    </span>
                    <span class="badge" style="font-size:10px; background:#ECEEE9; color:var(--o800); font-weight:700;">
                        {{ $prog['satdik_code'] }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:11.5px; margin-top:8px; padding-top:8px; border-top:1px dashed var(--line);">
                    <span style="color:var(--green); font-weight:700; display:flex; align-items:center; gap:4px;" title="Terhitung dalam kekuatan pendidikan">
                        <span class="ms" style="font-size:15px;">check_circle</span>
                        <b>{{ $prog['counted_students'] }}</b> Terhitung
                    </span>
                    <span style="color:#64748B; font-weight:600; display:flex; align-items:center; gap:4px;" title="Selesai Pendidikan (Arsip - Tidak Terhitung)">
                        <span class="ms" style="font-size:15px;">archive</span>
                        <b>{{ $prog['archived_students'] }}</b> Arsip
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</div>

<!-- SEGMENTED TABS: SEMUA vs SISWA AKTIF (TERHITUNG) vs ARSIP SELESAI -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; gap:12px; flex-wrap:wrap;">
    <div style="display:flex; background:#EAECE7; padding:4px; border-radius:10px; gap:4px;">
        <a href="{{ route('students.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'all'])) }}"
           style="padding:7px 16px; border-radius:7px; font-size:12.5px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px; transition:all 0.15s; {{ $tab == 'all' ? 'background:#fff; color:var(--o900); box-shadow:0 1px 3px rgba(0,0,0,0.1);' : 'color:var(--muted);' }}">
            <span class="ms" style="font-size:16px;">people</span> Semua Serdik
            <span class="badge" style="background:#E2E8F0; color:#334155; font-size:11px; padding:2px 7px;">{{ $stats['overall_total'] }}</span>
        </a>
        <a href="{{ route('students.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'aktif'])) }}"
           style="padding:7px 16px; border-radius:7px; font-size:12.5px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px; transition:all 0.15s; {{ $tab == 'aktif' ? 'background:#fff; color:var(--green); box-shadow:0 1px 3px rgba(0,0,0,0.1);' : 'color:var(--muted);' }}">
            <span class="ms" style="font-size:16px; color:var(--green);">how_to_reg</span> Siswa Aktif (Terhitung)
            <span class="badge badge-green" style="font-size:11px; padding:2px 7px;">{{ $stats['overall_counted'] }}</span>
        </a>
        <a href="{{ route('students.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'arsip'])) }}"
           style="padding:7px 16px; border-radius:7px; font-size:12.5px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px; transition:all 0.15s; {{ $tab == 'arsip' ? 'background:#fff; color:#475569; box-shadow:0 1px 3px rgba(0,0,0,0.1);' : 'color:var(--muted);' }}">
            <span class="ms" style="font-size:16px; color:#475569;">archive</span> Arsip Siswa Selesai (Tidak Terhitung)
            <span class="badge" style="background:#CBD5E1; color:#1E293B; font-size:11px; padding:2px 7px;">{{ $stats['overall_archived'] }}</span>
        </a>
    </div>

    @if($selectedProgram)
        <div style="background:#FFF9C4; border:1px solid #FFE082; padding:6px 14px; border-radius:8px; font-size:12.5px; color:#5D4037; display:flex; align-items:center; gap:8px;">
            <span class="ms" style="font-size:18px; color:var(--gold);">filter_alt</span>
            Filter Program: <b>{{ $selectedProgram->name }} ({{ $selectedProgram->satdik->code }})</b>
            <a href="{{ route('students.index', array_merge(request()->except(['program_id', 'page']))) }}" style="color:#C62828; text-decoration:none; margin-left:6px; font-weight:bold; font-size:14px;" title="Hapus filter program">✕</a>
        </div>
    @endif
</div>

<!-- MAIN DATA CARD -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <span class="ms" style="color:var(--o700);">badge</span>
            Daftar Peserta Didik {{ $selectedSatdik ? '— ' . $selectedSatdik->name : 'Seluruh Satdik Rindam' }}
        </h3>

        <!-- SEARCH & FILTER FORM -->
        <form method="GET" action="{{ route('students.index') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            @if($selectedSatdikId)
                <input type="hidden" name="satdik_id" value="{{ $selectedSatdikId }}">
            @endif
            @if($tab && $tab !== 'all')
                <input type="hidden" name="tab" value="{{ $tab }}">
            @endif

            <!-- PILIH PROGRAM PENDIDIKAN -->
            <select name="program_id" class="form-control" style="width:190px;" onchange="this.form.submit()">
                <option value="">Semua Program Pendidikan</option>
                @foreach($availablePrograms as $ap)
                    <option value="{{ $ap->id }}" {{ $selectedProgramId == $ap->id ? 'selected' : '' }}>
                        {{ $ap->name }}
                    </option>
                @endforeach
            </select>

            <!-- PILIH STATUS DETAIL -->
            <select name="status" class="form-control" style="width:165px;" onchange="this.form.submit()">
                <option value="">Semua Status Detail</option>
                <option value="Aktif" {{ $status == 'Aktif' ? 'selected' : '' }}>Aktif (Terhitung)</option>
                <option value="Sakit" {{ $status == 'Sakit' ? 'selected' : '' }}>Sakit (Terhitung)</option>
                <option value="Selesai" {{ $status == 'Selesai' ? 'selected' : '' }}>Selesai (Arsip)</option>
                <option value="Lulus" {{ $status == 'Lulus' ? 'selected' : '' }}>Lulus (Arsip)</option>
                <option value="DO / Dikeluarkan" {{ $status == 'DO / Dikeluarkan' ? 'selected' : '' }}>DO (Arsip)</option>
            </select>

            <div style="position:relative;">
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari Nosik, Nama, Kodam/Kodim Asal..." class="form-control" style="width:250px; padding-left:32px;">
                <span class="ms" style="position:absolute; left:8px; top:10px; color:var(--muted); font-size:18px;">search</span>
            </div>

            <button type="submit" class="btn btn-outline btn-sm">Filter</button>
            @if($keyword || $status || $selectedProgramId || ($tab && $tab !== 'all'))
                <a href="{{ route('students.index', ['satdik_id' => $selectedSatdikId]) }}" class="btn btn-sm" style="color:var(--muted);text-decoration:none;">Reset</a>
            @endif
        </form>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="data-table" style="width:100%;">
                <thead>
                    <tr>
                        <th style="width:45px; text-align:center;">No</th>
                        <th style="min-width:210px;">Siswa / Serdik</th>
                        <th style="white-space:nowrap;">Satuan Pendidikan (Satdik)</th>
                        <th style="min-width:180px;">Program Pendidikan & Peleton</th>
                        <th style="min-width:160px;">Kodam / Kodim Asal</th>
                        <th style="white-space:nowrap; min-width:180px;">NIK (Tersamar / Encrypted)</th>
                        <th style="white-space:nowrap; min-width:130px;">Ibu Kandung (Masked)</th>
                        <th style="white-space:nowrap;">Status & Keaktifan</th>
                        <th style="white-space:nowrap; text-align:right; min-width:330px;">Aksi & Kelola Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        <tr style="cursor:pointer; transition:background-color 0.15s;" 
                            onclick="if(!event.target.closest('a') && !event.target.closest('button')) window.location='{{ route('students.show', $student) }}'" 
                            title="Klik baris untuk melihat dossier lengkap {{ $student->full_name }}"
                            onmouseover="this.style.backgroundColor='#F9FAF7'" 
                            onmouseout="this.style.backgroundColor=''">
                            <td>{{ $students->firstItem() + $index }}</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:38px; height:38px; border-radius:50%; background:var(--o100); color:var(--o800); display:grid; place-items:center; font-weight:800; font-size:14px; border:1px solid var(--o200);">
                                        {{ substr($student->full_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('students.show', $student) }}" style="color:var(--o800); font-weight:800; display:block; font-size:14px; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                            {{ $student->full_name }}
                                        </a>
                                        <span class="badge badge-satdik" style="font-family:'Fira Code',monospace; font-size:11px; margin-top:2px;">
                                            {{ $student->nosik }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-satdik" style="background:#EFEFEA; font-weight:800;">
                                    <span class="ms" style="font-size:14px;">account_balance</span>
                                    {{ $student->satdik->code }}
                                </span>
                                <small style="display:block; color:var(--muted); font-size:11.5px; margin-top:2px;">
                                    {{ $student->satdik->location ?? 'Ksatrian Rindam' }}
                                </small>
                            </td>
                            <td>
                                <b style="font-size:13px; color:var(--text);">{{ $student->educationProgram->name ?? '-' }}</b>
                                <small style="display:block; color:var(--muted); font-size:11.5px;">
                                    {{ $student->classroom->name ?? 'Belum Ditentukan' }}
                                </small>
                            </td>
                            <td>
                                <span style="font-size:13px; font-weight:600;">{{ $student->origin_military_unit ?? '-' }}</span>
                            </td>
                            <td style="white-space:nowrap;">
                                <span class="masked-pill" title="Terenkripsi AES-256 di basis data">
                                    <span class="ms" style="font-size:14px; color:var(--gold);">lock</span>
                                    {{ $student->personalProfile->masked_nik ?? 'Belum Diisi' }}
                                </span>
                            </td>
                            <td style="white-space:nowrap;">
                                <span style="font-family:'Fira Code',monospace; font-size:12px; color:var(--muted);">
                                    {{ $student->personalProfile->masked_mother_name ?? '-' }}
                                </span>
                            </td>
                            <td style="white-space:nowrap;">
                                @php $u = $student->unified_status; @endphp
                                <span class="badge {{ $u['badge'] }}" title="{{ $student->is_counted ? 'Terhitung dalam Kuota Aktif' : 'Arsip (Tidak Terhitung)' }}">
                                    <span class="ms" style="font-size:13px;">{{ $u['icon'] }}</span> {{ $u['label'] }}
                                </span>
                                @if($student->is_counted)
                                    <small style="display:block; color:var(--green); font-size:10.5px; font-weight:700; margin-top:2px;">
                                        <span class="ms" style="font-size:11px;">check</span> Terhitung Aktif
                                    </small>
                                @else
                                    <small style="display:block; color:#64748B; font-size:10.5px; font-weight:600; margin-top:2px;">
                                        <span class="ms" style="font-size:11px;">archive</span> Arsip (Tidak Terhitung)
                                    </small>
                                @endif
                            </td>
                            <td style="text-align:right; white-space:nowrap;">
                                @php
                                    $studentJson = [
                                        'id' => $student->id,
                                        'satdik_id' => $student->satdik_id,
                                        'education_program_id' => $student->education_program_id,
                                        'classroom_id' => $student->classroom_id,
                                        'nosik' => $student->nosik,
                                        'full_name' => $student->full_name,
                                        'student_rank' => $student->student_rank,
                                        'company' => $student->company,
                                        'platoon' => $student->platoon,
                                        'origin_military_unit' => $student->origin_military_unit,
                                        'birth_place' => $student->birth_place,
                                        'birth_date' => $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('Y-m-d') : '',
                                        'gender' => $student->gender,
                                        'religion' => $student->religion,
                                        'blood_type' => $student->blood_type,
                                        'status' => $student->status,
                                        'mother_name' => $student->personalProfile?->mother_name ?? '',
                                        'emergency_contact_phone' => $student->personalProfile?->emergency_contact_phone ?? '',
                                        'home_address' => $student->personalProfile?->home_address ?? '',
                                        'height_cm' => $student->healthRecord?->height_cm ?? '',
                                        'weight_kg' => $student->healthRecord?->weight_kg ?? '',
                                        'blood_pressure' => $student->healthRecord?->blood_pressure ?? '',
                                        'daily_health_status' => $student->healthRecord?->daily_health_status ?? 'Siap Latih',
                                        'stakes_grade' => $student->healthRecord?->stakes_grade ?? 'Stakes I (Sangat Baik)',
                                        'doctor_notes' => $student->healthRecord?->doctor_notes ?? '',
                                    ];
                                @endphp
                                <a href="{{ route('health.show', $student) }}" class="btn btn-outline btn-sm" title="Rekam Medis Serdik" style="color:var(--green); padding:5px 8px;">
                                    <span class="ms">medical_services</span>
                                </a>
                                <a href="{{ route('students.show', $student) }}" class="btn btn-outline btn-sm" title="Dossier Lengkap" style="padding:5px 8px;">
                                    <span class="ms">visibility</span>
                                </a>
                                <button type="button" class="btn btn-outline btn-sm" onclick="openStatusModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', '{{ $student->status }}')" title="Ubah Status / Arsipkan Serdik" style="color:var(--o800); padding:5px 8px;">
                                    <span class="ms">swap_horiz</span> Status
                                </button>
                                <button type="button" class="btn btn-outline btn-sm" onclick="openEditStudentModal(this)" data-student="{{ json_encode($studentJson) }}" title="Edit Data Serdik (Modal)" style="color:#2563EB; border-color:#93C5FD; padding:5px 8px;">
                                    <span class="ms">edit</span> Edit
                                </button>
                                <button type="button" class="btn btn-outline btn-sm" onclick="openDeleteStudentModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', '{{ $student->nosik }}', '{{ addslashes($student->satdik->name ?? '') }}')" title="Hapus Data Serdik (Modal)" style="color:#DC2626; border-color:#FCA5A5; padding:5px 8px;">
                                    <span class="ms">delete</span>
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" onclick="openRevealModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', '{{ $student->nosik }}')" title="Buka Data Pribadi Terproteksi (SIPANDU-WBK)" style="padding:5px 10px;">
                                    <span class="ms">key</span> Buka Data
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:45px 20px; color:var(--muted);">
                                <span class="ms" style="font-size:48px; color:var(--o200); display:block; margin-bottom:8px;">folder_off</span>
                                <b>Tidak ada data peserta didik pada kriteria filter ini.</b>
                                <div style="font-size:12.5px; margin-top:4px;">Coba ubah filter Satdik, Program Pendidikan, atau kategori tab Aktif/Arsip di atas.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-pagination-footer">
            <div class="table-pagination-info">
                Menampilkan <b>{{ $students->firstItem() ?? 0 }}</b> - <b>{{ $students->lastItem() ?? 0 }}</b> dari <b>{{ $students->total() }}</b> serdik
                @if($tab === 'aktif')
                    (Kategori: <b>Siswa Aktif Terhitung</b>)
                @elseif($tab === 'arsip')
                    (Kategori: <b>Arsip Siswa Selesai</b>)
                @endif
            </div>
            <div>
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>

<!-- MODAL UBAH STATUS SERDIK (AKTIF vs ARSIP SELESAI) -->
<div class="modal-overlay" id="statusModal">
    <div class="modal-card" style="max-width:480px;">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:var(--gold2); font-size:22px;">swap_horiz</span>
                <b style="font-family:'Montserrat',sans-serif; font-size:15px;">Ubah Status Serdik & Pengarsipan</b>
            </div>
            <button type="button" onclick="closeStatusModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:20px;">&times;</button>
        </div>

        <form id="statusForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="modal-body" style="padding:20px;">
                <div style="background:var(--o50); border:1px solid var(--line); border-radius:10px; padding:12px 16px; margin-bottom:16px;">
                    <div style="font-size:11.5px; color:var(--muted); text-transform:uppercase; font-weight:700;">Peserta Didik:</div>
                    <div id="statusModalStudentName" style="font-size:15px; font-weight:800; color:var(--o800); margin-top:2px;">-</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Pilih Status Baru Serdik: <span style="color:var(--red);">*</span></label>
                    <select name="status" id="statusModalSelect" class="form-control" style="font-size:13.5px; font-weight:600;" onchange="updateStatusExplanation(this.value)">
                        <optgroup label="── STATUS TERHITUNG (PENDIDIKAN BERJALAN) ──">
                            <option value="Aktif">🟢 Aktif (Siap Latih — Terhitung)</option>
                            <option value="Sakit">🟡 Sakit (Dispen Medis — Terhitung)</option>
                            <option value="Dinas Luar">🔵 Dinas Luar (Terhitung)</option>
                        </optgroup>
                        <optgroup label="── STATUS ARSIP (TIDAK TERHITUNG LAGI) ──">
                            <option value="Selesai">📁 Selesai (Tamat Pendidikan — Masuk Arsip)</option>
                            <option value="Lulus">🎓 Lulus (Alumni — Masuk Arsip)</option>
                            <option value="DO / Dikeluarkan">🔴 DO / Dikeluarkan (Keluar — Masuk Arsip)</option>
                        </optgroup>
                    </select>
                </div>

                <div id="statusExplanation" style="background:#F1F5F9; border-left:4px solid var(--green); padding:10px 14px; border-radius:6px; font-size:12px; color:#334155; margin-top:14px; line-height:1.5;">
                    <!-- Diperbarui lewat JavaScript -->
                </div>
            </div>

            <div style="padding:14px 20px; background:#FAFBF9; border-top:1px solid var(--line); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeStatusModal()">Batal</button>
                <button type="submit" class="btn btn-gold">
                    <span class="ms">save</span> Simpan Perubahan Status
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL BUKA DATA PRIBADI TERPROTEKSI (SIPANDU-WBK) -->
<div class="modal-overlay" id="revealModal">
    <div class="modal-card">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:var(--gold2); font-size:22px;">verified_user</span>
                <b style="font-family:'Montserrat',sans-serif; font-size:15px;">Akses Data Pribadi Terproteksi (SIPANDU-WBK)</b>
            </div>
            <button type="button" onclick="closeRevealModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:20px;">&times;</button>
        </div>

        <div class="modal-body" id="modalFormSection">
            <div class="alert alert-warning" style="margin-bottom:16px; font-size:12.5px;">
                <span class="ms" style="font-size:22px;">shield</span>
                <div>
                    <b>Pemberitahuan Keamanan Sistem:</b> Sesuai standar operasional keamanan data SIPANDU-WBK, setiap pembukaan data pribadi terenkripsi wajib menyertakan alasan dinas yang sah dan <b>akan dicatat secara permanen dalam Audit Trail</b>.
                </div>
            </div>

            <div style="background:var(--o50); border:1px solid var(--line); border-radius:10px; padding:12px 16px; margin-bottom:16px;">
                <div style="font-size:11.5px; color:var(--muted); text-transform:uppercase; font-weight:700;">Peserta Didik:</div>
                <div id="modalStudentName" style="font-size:16px; font-weight:800; color:var(--o800); margin-top:2px;">-</div>
                <div id="modalStudentNosik" style="font-family:'Fira Code',monospace; font-size:12px; color:var(--o600);">-</div>
            </div>

            <form id="revealForm" onsubmit="submitReveal(event)">
                <input type="hidden" id="modalStudentId">

                <div class="form-group">
                    <label class="form-label">Alasan Akses / Keperluan Dinas <span style="color:var(--red);">*</span></label>
                    <select id="accessReasonSelect" class="form-control" onchange="handleReasonChange(this)" style="margin-bottom:8px;">
                        <option value="Verifikasi Kelengkapan Administrasi & Berkas Personel">Verifikasi Kelengkapan Administrasi & Berkas Personel</option>
                        <option value="Pemeriksaan Kesehatan & Rekam Medis Poliklinik Satdik">Pemeriksaan Kesehatan & Rekam Medis Poliklinik Satdik</option>
                        <option value="Konfirmasi Kontak Darurat Keluarga Serdik">Konfirmasi Kontak Darurat Keluarga Serdik</option>
                        <option value="Distribusi Uang Saku / Rekening Bank TNI AD">Distribusi Uang Saku / Rekening Bank TNI AD</option>
                        <option value="Pemeriksaan Wasrik / Audit Tim Zona Integritas">Pemeriksaan Wasrik / Audit Tim Zona Integritas</option>
                        <option value="Lainnya">Lainnya (Tulis Manual)...</option>
                    </select>
                    <textarea id="accessReasonText" class="form-control" rows="2" placeholder="Tuliskan keterangan detail keperluan dinas..." style="display:none;"></textarea>
                    <div class="form-hint">Alasan ini akan disimpan bersama User ID, Waktu Akses, dan IP Address Anda.</div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                    <button type="button" class="btn btn-outline" onclick="closeRevealModal()">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitReveal">
                        <span class="ms">lock_open</span> Dekripsi & Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <!-- RESULT CONTAINER AFTER DECRYPTION -->
        <div class="modal-body" id="modalResultSection" style="display:none;">
            <div class="alert alert-success" style="font-size:12px; margin-bottom:14px;">
                <span class="ms">check_circle</span>
                <span id="auditNotice">Akses data pribadi berhasil didekripsi dan dicatat pada audit trail.</span>
            </div>

            <table style="width:100%; border-collapse:collapse; font-size:13px;" id="decryptedTable">
                <!-- Populated via JS -->
            </table>

            <div style="margin-top:20px; display:flex; justify-content:flex-end;">
                <button type="button" class="btn btn-primary btn-sm" onclick="closeRevealModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- DATALISTS FOR AUTOCOMPLETE -->
<datalist id="companyDatalist">
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
<datalist id="platoonDatalist">
    <option value="Peleton 1">
    <option value="Peleton 2">
    <option value="Peleton 3">
    <option value="Peleton 4">
    <option value="Peleton Bantuan">
    <option value="Peleton Taktik">
    <option value="Peleton Senban">
    <option value="Peleton Runduk">
</datalist>
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

<!-- 1. MODAL TAMBAH SISWA BARU (CREATE) -->
<div class="modal-overlay" id="createStudentModal">
    <div class="modal-card" style="max-width:900px; max-height:92vh; display:flex; flex-direction:column;">
        <div class="modal-header" style="background:linear-gradient(135deg, var(--o900), var(--o800)); border-bottom:2px solid var(--gold);">
            <div style="display:flex; align-items:center; gap:10px;">
                <span class="ms" style="color:var(--gold2); font-size:24px;">person_add</span>
                <div>
                    <b style="font-family:'Montserrat',sans-serif; font-size:16px;">Tambah Prajurit Siswa Baru</b>
                    <div style="font-size:11.5px; color:#E2E8F0; opacity:0.85;">Pendaftaran Siswa per Satuan Pendidikan — Sistem SIPANDU-WBK</div>
                </div>
            </div>
            <button type="button" onclick="closeCreateStudentModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:24px; line-height:1;">&times;</button>
        </div>

        <form id="createStudentForm" method="POST" action="{{ route('students.store') }}" onsubmit="handleAjaxSubmit(event, 'create')" style="display:flex; flex-direction:column; overflow:hidden; flex:1;">
            @csrf
            <div class="modal-body" style="padding:22px 26px; overflow-y:auto; flex:1;">
                <div id="create_error_alert" class="alert alert-warning" style="display:none; background:#FFEBEE; border-color:#FFCDD2; color:var(--red); margin-bottom:16px; font-size:12.5px;">
                    <span class="ms" style="font-size:22px; color:var(--red);">error</span>
                    <div id="create_error_list"></div>
                </div>

                <div style="background:rgba(201,162,39,0.08); border:1px solid rgba(201,162,39,0.25); border-radius:10px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:center; gap:10px;">
                    <span class="ms" style="color:var(--gold); font-size:22px;">shield</span>
                    <div style="font-size:12px; color:var(--o900); line-height:1.4;">
                        <b>Keamanan Sistem SIPANDU & Anti-Duplikasi:</b> NOSIK dibentuk otomatis. Validasi NIK KTP (16 Digit) mencegah data ganda. Data pribadi tersimpan terenkripsi <b>AES-256-CBC</b>.
                    </div>
                </div>

                <!-- BAGIAN A: DATA KEMILITERAN & SATDIK -->
                <div style="background:#FAFBF9; border:1px solid var(--line); border-radius:12px; padding:16px 18px; margin-bottom:18px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; border-bottom:1px solid var(--line); padding-bottom:8px;">
                        <b style="color:var(--o800); font-size:13.5px; display:flex; align-items:center; gap:6px;">
                            <span class="ms" style="color:var(--gold); font-size:18px;">military_tech</span> Bagian A: Data Kemiliteran & Satuan Pendidikan
                        </b>
                        <span class="badge badge-satdik" style="font-size:10.5px;">Wajib Dilengkapi</span>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Satuan Pendidikan (Satdik) <span style="color:var(--red);">*</span></label>
                            @if(auth()->check() && !auth()->user()->isPimpinan() && auth()->user()->satdik_id)
                                <input type="hidden" name="satdik_id" id="create_satdik_id" value="{{ auth()->user()->satdik_id }}">
                                <div style="background:#F1F5F9; border:1.5px solid #CBD5E1; border-radius:8px; padding:9px 13px; font-weight:700; color:#0F172A; display:flex; align-items:center; justify-content:space-between;">
                                    <span style="font-size:13px;">{{ auth()->user()->satdik?->code }} — {{ auth()->user()->satdik?->name }}</span>
                                    <span class="badge" style="background:#DCFCE7; color:#166534; font-size:11px; font-weight:800;">🔒 Terkunci (Satdik Anda)</span>
                                </div>
                            @else
                                <select name="satdik_id" id="create_satdik_id" class="form-control" required onchange="handleSatdikSelectChange(this.value, 'create')">
                                    <option value="">-- Pilih Satdik --</option>
                                    @foreach($satdiks as $s)
                                        <option value="{{ $s->id }}" {{ $selectedSatdikId == $s->id ? 'selected' : '' }}>
                                            {{ $s->code }} — {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Program Pendidikan <span style="color:var(--red);">*</span></label>
                            <select name="education_program_id" id="create_education_program_id" class="form-control" required onchange="handleProgramSelectChange(this.value, 'create')">
                                <option value="">-- Pilih Satdik Terlebih Dahulu --</option>
                            </select>
                        </div>
                    </div>

                    <!-- KOMPI & PELETON -->
                    <div class="form-group" style="margin-bottom:14px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                            <label class="form-label" style="margin-bottom:0;">Peleton / Kompi Siswa</label>
                            <a href="javascript:void(0)" id="create_toggle_manual_btn" onclick="toggleClassroomMode('create')" style="font-size:11.5px; color:var(--o800); font-weight:700; text-decoration:underline;">
                                + Ketik Manual Kompi & Peleton
                            </a>
                        </div>
                        
                        <div id="create_classroom_dropdown_wrapper">
                            <select name="classroom_id" id="create_classroom_id" class="form-control">
                                <option value="">-- Pilih Peleton / Kompi (Opsional) --</option>
                            </select>
                        </div>

                        <div id="create_classroom_manual_wrapper" style="display:none; background:#F1F5F9; border:1px dashed #CBD5E1; padding:10px 14px; border-radius:8px; margin-top:6px;">
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                <div>
                                    <label style="font-size:11px; font-weight:700; color:var(--muted); display:block; margin-bottom:2px;">Kompi Siswa</label>
                                    <input type="text" list="companyDatalist" name="company" id="create_manual_company" class="form-control" placeholder="Contoh: Kompi A">
                                </div>
                                <div>
                                    <label style="font-size:11px; font-weight:700; color:var(--muted); display:block; margin-bottom:2px;">Peleton Siswa</label>
                                    <input type="text" list="platoonDatalist" name="platoon" id="create_manual_platoon" class="form-control" placeholder="Contoh: Peleton 1">
                                </div>
                            </div>
                            <small style="color:var(--muted); font-size:11px; margin-top:4px; display:block;">Sistem otomatis membuat rombongan belajar baru jika belum tersedia.</small>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 2fr 1fr 1fr; gap:12px; margin-bottom:14px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Nama Lengkap Siswa <span style="color:var(--red);">*</span></label>
                            <input type="text" name="full_name" id="create_full_name" class="form-control" placeholder="Contoh: Muhammad Rizky Pratama" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Pangkat Siswa <span style="color:var(--red);">*</span></label>
                            <select name="student_rank" id="create_student_rank" class="form-control" required>
                                <option value="Siswa Secaba">Siswa Secaba</option>
                                <option value="Siswa Secata">Siswa Secata</option>
                                <option value="Prada Siswa">Prada Siswa</option>
                                <option value="Serda Siswa">Serda Siswa</option>
                                <option value="Kader Bela Negara">Kader Bela Negara</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Status Siswa <span style="color:var(--red);">*</span></label>
                            <select name="status" id="create_status" class="form-control" required>
                                <option value="Aktif">🟢 Aktif (Terhitung)</option>
                                <option value="Sakit">🟡 Sakit (Terhitung)</option>
                                <option value="Dinas Luar">🔵 Dinas Luar (Terhitung)</option>
                                <option value="Selesai">📁 Selesai (Arsip)</option>
                                <option value="Lulus">🎓 Lulus (Arsip)</option>
                                <option value="DO / Dikeluarkan">🔴 DO / Dikeluarkan (Arsip)</option>
                            </select>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap:12px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Kodam / Kodim Asal</label>
                            <input type="text" list="kodimList" name="origin_military_unit" id="create_origin_military_unit" class="form-control" placeholder="Pilih / ketik asal kodim">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="birth_place" id="create_birth_place" class="form-control" placeholder="Contoh: Bandung">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="birth_date" id="create_birth_date" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Jenis Kelamin <span style="color:var(--red);">*</span></label>
                            <select name="gender" id="create_gender" class="form-control" required>
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN B: DATA PRIBADI SENSITIF TERPROTEKSI -->
                <div style="background:#FFFDF5; border:1.5px solid var(--gold); border-radius:12px; padding:16px 18px; margin-bottom:18px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; border-bottom:1px solid #E8D9A8; padding-bottom:8px;">
                        <b style="color:#7A5C07; font-size:13.5px; display:flex; align-items:center; gap:6px;">
                            <span class="ms" style="color:var(--gold); font-size:18px;">lock</span> Bagian B: Data Pribadi Sensitif Terproteksi
                        </b>
                        <span class="badge" style="background:#FEF3C7; color:#92400E; font-size:10.5px;">Enkripsi AES-256</span>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:12px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">NIK (16 Digit KTP) <span style="color:var(--red);">*</span></label>
                            <input type="text" name="nik" id="create_nik" maxlength="16" minlength="16" class="form-control" placeholder="Contoh: 3201012345670001" required>
                            <div class="form-hint">Kunci validasi anti-duplikasi serdik.</div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">No. Kartu Keluarga (KK)</label>
                            <input type="text" name="family_card_number" id="create_family_card_number" maxlength="16" class="form-control" placeholder="Contoh: 3201012345670002">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:12px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Nama Ibu Kandung <span style="color:var(--red);">*</span></label>
                            <input type="text" name="mother_name" id="create_mother_name" class="form-control" placeholder="Contoh: Siti Fatimah" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Nama Ayah Kandung</label>
                            <input type="text" name="father_name" id="create_father_name" class="form-control" placeholder="Contoh: Bambang Irawan">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">No HP Kontak Darurat / Orang Tua <span style="color:var(--red);">*</span></label>
                            <input type="text" name="emergency_contact_phone" id="create_emergency_contact_phone" class="form-control" placeholder="Contoh: 081234567890" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Alamat Asal KTP</label>
                        <textarea name="home_address" id="create_home_address" class="form-control" rows="2" placeholder="Alamat domisili lengkap"></textarea>
                    </div>
                </div>

                <!-- BAGIAN C: DATA FISIK & STATUS KESEHATAN AWAL -->
                <div style="background:#F6FBF7; border:1px solid #DCE7DD; border-left:4px solid var(--green); border-radius:12px; padding:16px 18px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; border-bottom:1px solid #DCE7DD; padding-bottom:8px;">
                        <b style="color:var(--green); font-size:13.5px; display:flex; align-items:center; gap:6px;">
                            <span class="ms" style="font-size:18px;">monitor_heart</span> Bagian C: Fisik & Kesiapan Medis Awal
                        </b>
                        <span class="badge badge-green" style="font-size:10.5px;">Format Rekam Medis</span>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr 1.2fr 1fr 1fr; gap:12px; margin-bottom:12px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">TB (cm)</label>
                            <input type="number" name="height_cm" id="create_height_cm" class="form-control" placeholder="172" min="100" max="250" oninput="calcBmi('create')">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">BB (kg)</label>
                            <input type="number" step="0.5" name="weight_kg" id="create_weight_kg" class="form-control" placeholder="68" min="30" max="200" oninput="calcBmi('create')">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">BMI & Kategori</label>
                            <div id="create_bmi_badge" style="padding:8px 10px; background:#fff; border:1px solid var(--line); border-radius:8px; font-size:12px; font-weight:700; color:var(--muted); text-align:center;">
                                -
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Tensi Darah</label>
                            <input type="text" name="blood_pressure" id="create_blood_pressure" class="form-control" placeholder="120/80" value="120/80">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Gol. Darah</label>
                            <select name="blood_type" id="create_blood_type" class="form-control">
                                <option value="O">O</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:12px;">
                        <label class="form-label">Kondisi Fisik / Kesiapan Medis</label>
                        <select name="daily_health_status" id="create_daily_health_status" class="form-control">
                            <option value="Siap Latih">🟢 Siap Latih Penuh (Normal & Prima)</option>
                            <option value="Berobat Jalan">🟡 Berobat Jalan / Dispen (Keluhan Ringan)</option>
                            <option value="Rawat Inap Poliklinik">🔴 Rawat Inap Poliklinik Satdik (Bed Rest)</option>
                            <option value="Rujuk Rumkit">🔵 Rujuk Rumah Sakit (Rumkit Tk. II Soedjono / Dinas)</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Catatan Medis Awal & Riwayat Alergi</label>
                        <input type="text" name="doctor_notes" id="create_doctor_notes" class="form-control" placeholder="Catatan kondisi awal fisik serdik...">
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding:14px 24px; background:#FAFBF9; border-top:1px solid var(--line); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeCreateStudentModal()">Batal</button>
                <button type="submit" class="btn btn-gold" id="btnSubmitCreateStudent">
                    <span class="ms">save</span> Daftarkan Siswa Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL EDIT DATA SISWA (UPDATE) -->
<div class="modal-overlay" id="editStudentModal">
    <div class="modal-card" style="max-width:900px; max-height:92vh; display:flex; flex-direction:column;">
        <div class="modal-header" style="background:linear-gradient(135deg, #1E3A8A, #1E40AF); border-bottom:2px solid #60A5FA;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span class="ms" style="color:#93C5FD; font-size:24px;">edit</span>
                <div>
                    <b style="font-family:'Montserrat',sans-serif; font-size:16px;">Edit Data Prajurit Siswa</b>
                    <div style="display:flex; align-items:center; gap:8px; margin-top:2px;">
                        <span id="edit_nosik_badge" class="badge" style="background:rgba(255,255,255,0.2); color:#fff; font-family:'Fira Code'; font-size:11px;">NOSIK: -</span>
                        <span id="edit_student_title" style="font-size:12px; color:#DBEAFE; font-weight:600;">-</span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="closeEditStudentModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:24px; line-height:1;">&times;</button>
        </div>

        <form id="editStudentForm" method="POST" action="" onsubmit="handleAjaxSubmit(event, 'edit')" style="display:flex; flex-direction:column; overflow:hidden; flex:1;">
            @csrf
            @method('PUT')
            <div class="modal-body" style="padding:22px 26px; overflow-y:auto; flex:1;">
                <div id="edit_error_alert" class="alert alert-warning" style="display:none; background:#FFEBEE; border-color:#FFCDD2; color:var(--red); margin-bottom:16px; font-size:12.5px;">
                    <span class="ms" style="font-size:22px; color:var(--red);">error</span>
                    <div id="edit_error_list"></div>
                </div>

                <!-- BAGIAN A: DATA KEMILITERAN & SATDIK -->
                <div style="background:#FAFBF9; border:1px solid var(--line); border-radius:12px; padding:16px 18px; margin-bottom:18px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; border-bottom:1px solid var(--line); padding-bottom:8px;">
                        <b style="color:var(--o800); font-size:13.5px; display:flex; align-items:center; gap:6px;">
                            <span class="ms" style="color:var(--gold); font-size:18px;">military_tech</span> Bagian A: Data Kemiliteran & Satuan Pendidikan
                        </b>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Satuan Pendidikan (Satdik) <span style="color:var(--red);">*</span></label>
                            @if(auth()->check() && !auth()->user()->isPimpinan() && auth()->user()->satdik_id)
                                <input type="hidden" name="satdik_id" id="edit_satdik_id" value="{{ auth()->user()->satdik_id }}">
                                <div style="background:#F1F5F9; border:1.5px solid #CBD5E1; border-radius:8px; padding:9px 13px; font-weight:700; color:#0F172A; display:flex; align-items:center; justify-content:space-between;">
                                    <span style="font-size:13px;">{{ auth()->user()->satdik?->code }} — {{ auth()->user()->satdik?->name }}</span>
                                    <span class="badge" style="background:#DCFCE7; color:#166534; font-size:11px; font-weight:800;">🔒 Terkunci (Satdik Anda)</span>
                                </div>
                            @else
                                <select name="satdik_id" id="edit_satdik_id" class="form-control" required onchange="handleSatdikSelectChange(this.value, 'edit')">
                                    @foreach($satdiks as $s)
                                        <option value="{{ $s->id }}">{{ $s->code }} — {{ $s->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Program Pendidikan <span style="color:var(--red);">*</span></label>
                            <select name="education_program_id" id="edit_education_program_id" class="form-control" required onchange="handleProgramSelectChange(this.value, 'edit')">
                            </select>
                        </div>
                    </div>

                    <!-- KOMPI & PELETON -->
                    <div class="form-group" style="margin-bottom:14px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                            <label class="form-label" style="margin-bottom:0;">Peleton / Kompi Siswa</label>
                            <a href="javascript:void(0)" id="edit_toggle_manual_btn" onclick="toggleClassroomMode('edit')" style="font-size:11.5px; color:var(--o800); font-weight:700; text-decoration:underline;">
                                + Ketik Manual Kompi & Peleton
                            </a>
                        </div>
                        
                        <div id="edit_classroom_dropdown_wrapper">
                            <select name="classroom_id" id="edit_classroom_id" class="form-control">
                                <option value="">-- Pilih Peleton / Kompi (Opsional) --</option>
                            </select>
                        </div>

                        <div id="edit_classroom_manual_wrapper" style="display:none; background:#F1F5F9; border:1px dashed #CBD5E1; padding:10px 14px; border-radius:8px; margin-top:6px;">
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                <div>
                                    <label style="font-size:11px; font-weight:700; color:var(--muted); display:block; margin-bottom:2px;">Kompi Siswa</label>
                                    <input type="text" list="companyDatalist" name="company" id="edit_manual_company" class="form-control" placeholder="Contoh: Kompi A">
                                </div>
                                <div>
                                    <label style="font-size:11px; font-weight:700; color:var(--muted); display:block; margin-bottom:2px;">Peleton Siswa</label>
                                    <input type="text" list="platoonDatalist" name="platoon" id="edit_manual_platoon" class="form-control" placeholder="Contoh: Peleton 1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 2fr 1fr 1fr; gap:12px; margin-bottom:14px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Nama Lengkap Siswa <span style="color:var(--red);">*</span></label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Pangkat Siswa <span style="color:var(--red);">*</span></label>
                            <select name="student_rank" id="edit_student_rank" class="form-control" required>
                                <option value="Siswa Secaba">Siswa Secaba</option>
                                <option value="Siswa Secata">Siswa Secata</option>
                                <option value="Prada Siswa">Prada Siswa</option>
                                <option value="Serda Siswa">Serda Siswa</option>
                                <option value="Kader Bela Negara">Kader Bela Negara</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Status Siswa <span style="color:var(--red);">*</span></label>
                            <select name="status" id="edit_status" class="form-control" required>
                                <option value="Aktif">🟢 Aktif (Terhitung)</option>
                                <option value="Sakit">🟡 Sakit (Terhitung)</option>
                                <option value="Dinas Luar">🔵 Dinas Luar (Terhitung)</option>
                                <option value="Selesai">📁 Selesai (Arsip)</option>
                                <option value="Lulus">🎓 Lulus (Arsip)</option>
                                <option value="DO / Dikeluarkan">🔴 DO / Dikeluarkan (Arsip)</option>
                            </select>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap:12px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Kodam / Kodim Asal</label>
                            <input type="text" list="kodimList" name="origin_military_unit" id="edit_origin_military_unit" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="birth_place" id="edit_birth_place" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="birth_date" id="edit_birth_date" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Jenis Kelamin <span style="color:var(--red);">*</span></label>
                            <select name="gender" id="edit_gender" class="form-control" required>
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN B: DATA PRIBADI & KONTAK -->
                <div style="background:#FFFDF5; border:1.5px solid var(--gold); border-radius:12px; padding:16px 18px; margin-bottom:18px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; border-bottom:1px solid #E8D9A8; padding-bottom:8px;">
                        <b style="color:#7A5C07; font-size:13.5px; display:flex; align-items:center; gap:6px;">
                            <span class="ms" style="color:var(--gold); font-size:18px;">lock</span> Bagian B: Data Pribadi & Kontak Darurat (SIPANDU-WBK)
                        </b>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:12px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Revisi NIK (16 Digit KTP)</label>
                            <input type="text" name="nik" id="edit_nik" maxlength="16" minlength="16" class="form-control" placeholder="Kosongkan jika tidak ada perubahan NIK">
                            <div class="form-hint">Hanya diisi jika terdapat perbaikan NIK KTP Serdik.</div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Nama Ibu Kandung</label>
                            <input type="text" name="mother_name" id="edit_mother_name" class="form-control" placeholder="Nama Ibu Kandung">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:12px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">No HP Kontak Darurat / Orang Tua</label>
                            <input type="text" name="emergency_contact_phone" id="edit_emergency_contact_phone" class="form-control" placeholder="Nomor Telepon Darurat">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Alamat Domisili KTP</label>
                            <input type="text" name="home_address" id="edit_home_address" class="form-control" placeholder="Alamat asal...">
                        </div>
                    </div>
                </div>

                <!-- BAGIAN C: DATA FISIK & STATUS KESEHATAN -->
                <div style="background:#F6FBF7; border:1px solid #DCE7DD; border-left:4px solid var(--green); border-radius:12px; padding:16px 18px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; border-bottom:1px solid #DCE7DD; padding-bottom:8px;">
                        <b style="color:var(--green); font-size:13.5px; display:flex; align-items:center; gap:6px;">
                            <span class="ms" style="font-size:18px;">monitor_heart</span> Bagian C: Fisik & Kesiapan Medis
                        </b>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr 1.2fr 1fr 1fr; gap:12px; margin-bottom:12px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">TB (cm)</label>
                            <input type="number" name="height_cm" id="edit_height_cm" class="form-control" min="100" max="250" oninput="calcBmi('edit')">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">BB (kg)</label>
                            <input type="number" step="0.5" name="weight_kg" id="edit_weight_kg" class="form-control" min="30" max="200" oninput="calcBmi('edit')">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">BMI & Kategori</label>
                            <div id="edit_bmi_badge" style="padding:8px 10px; background:#fff; border:1px solid var(--line); border-radius:8px; font-size:12px; font-weight:700; color:var(--muted); text-align:center;">
                                -
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Tensi Darah</label>
                            <input type="text" name="blood_pressure" id="edit_blood_pressure" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Gol. Darah</label>
                            <select name="blood_type" id="edit_blood_type" class="form-control">
                                <option value="O">O</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:12px;">
                        <label class="form-label">Kondisi Fisik / Kesiapan Medis</label>
                        <select name="daily_health_status" id="edit_daily_health_status" class="form-control">
                            <option value="Siap Latih">🟢 Siap Latih Penuh (Normal & Prima)</option>
                            <option value="Berobat Jalan">🟡 Berobat Jalan / Dispen (Keluhan Ringan)</option>
                            <option value="Rawat Inap Poliklinik">🔴 Rawat Inap Poliklinik Satdik (Bed Rest)</option>
                            <option value="Rujuk Rumkit">🔵 Rujuk Rumah Sakit (Rumkit Tk. II Soedjono / Dinas)</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Catatan Medis & Riwayat</label>
                        <input type="text" name="doctor_notes" id="edit_doctor_notes" class="form-control">
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding:14px 24px; background:#FAFBF9; border-top:1px solid var(--line); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeEditStudentModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitEditStudent">
                    <span class="ms">save</span> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. MODAL HAPUS DATA SISWA (DELETE) -->
<div class="modal-overlay" id="deleteStudentModal">
    <div class="modal-card" style="max-width:480px;">
        <div class="modal-header" style="background:linear-gradient(135deg, #991B1B, #DC2626); color:#fff;">
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="ms" style="font-size:22px;">delete_forever</span>
                <b style="font-family:'Montserrat',sans-serif; font-size:15px;">Konfirmasi Hapus Data Siswa</b>
            </div>
            <button type="button" onclick="closeDeleteStudentModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:22px;">&times;</button>
        </div>

        <form id="deleteStudentForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="modal-body" style="padding:20px;">
                <div class="alert alert-warning" style="background:#FEF2F2; border:1px solid #FECACA; color:#991B1B; margin-bottom:16px; font-size:12.5px;">
                    <span class="ms" style="font-size:22px; color:#DC2626;">warning</span>
                    <div>
                        <b>Peringatan:</b> Tindakan ini akan menghapus data prajurit siswa secara permanen dari sistem beserta seluruh rekam medis dan profil terkait.
                    </div>
                </div>

                <div style="background:var(--o50); border:1px solid var(--line); border-radius:10px; padding:14px 16px;">
                    <div style="font-size:11px; color:var(--muted); text-transform:uppercase; font-weight:700;">Data Siswa yang Akan Dihapus:</div>
                    <div id="deleteModalStudentName" style="font-size:16px; font-weight:800; color:var(--o900); margin-top:2px;">-</div>
                    <div style="margin-top:4px; display:flex; gap:8px; align-items:center;">
                        <span id="deleteModalStudentNosik" class="badge badge-satdik" style="font-family:'Fira Code'; font-size:11px;">-</span>
                        <span id="deleteModalStudentSatdik" style="font-size:12px; color:var(--muted);">-</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding:14px 20px; background:#FAFBF9; border-top:1px solid var(--line); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeDeleteStudentModal()">Batal</button>
                <button type="submit" class="btn btn-danger" style="background:#DC2626; color:#fff; border:none; padding:8px 16px; border-radius:8px; font-weight:700;">
                    <span class="ms">delete</span> Ya, Hapus Data
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentStudentId = null;

const allSatdiksData = @json($satdiks);
const allProgramsData = @json($allPrograms);
const allClassroomsData = @json($allClassrooms);

// Handle cascading dropdowns Satdik -> Program -> Peleton
function handleSatdikSelectChange(satdikId, prefix, targetProgramId = null, targetClassroomId = null) {
    const progSelect = document.getElementById(`${prefix}_education_program_id`);
    progSelect.innerHTML = '<option value="">-- Pilih Program Pendidikan --</option>';
    
    if (!satdikId) {
        handleProgramSelectChange(null, prefix);
        return;
    }
    
    const filteredPrograms = allProgramsData.filter(p => p.satdik_id == satdikId);
    filteredPrograms.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = `${p.name} (TA ${p.academic_year || '2026'})`;
        if (targetProgramId && p.id == targetProgramId) {
            opt.selected = true;
        }
        progSelect.appendChild(opt);
    });
    
    const selectedProgId = targetProgramId || progSelect.value;
    handleProgramSelectChange(selectedProgId, prefix, targetClassroomId);
}

function handleProgramSelectChange(programId, prefix, targetClassroomId = null) {
    const clsSelect = document.getElementById(`${prefix}_classroom_id`);
    clsSelect.innerHTML = '<option value="">-- Pilih Peleton / Kompi (Opsional) --</option>';
    
    if (!programId) return;
    
    const filteredClassrooms = allClassroomsData.filter(c => c.education_program_id == programId);
    filteredClassrooms.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = `${c.name} (Kapasitas: ${c.capacity || 35})`;
        if (targetClassroomId && c.id == targetClassroomId) {
            opt.selected = true;
        }
        clsSelect.appendChild(opt);
    });
}

function toggleClassroomMode(prefix) {
    const dropWrapper = document.getElementById(`${prefix}_classroom_dropdown_wrapper`);
    const manualWrapper = document.getElementById(`${prefix}_classroom_manual_wrapper`);
    const toggleBtn = document.getElementById(`${prefix}_toggle_manual_btn`);
    
    if (manualWrapper.style.display === 'none') {
        manualWrapper.style.display = 'block';
        dropWrapper.style.display = 'none';
        toggleBtn.textContent = '✕ Gunakan Pilihan Dropdown Peleton';
        toggleBtn.style.color = 'var(--red)';
    } else {
        manualWrapper.style.display = 'none';
        dropWrapper.style.display = 'block';
        toggleBtn.textContent = '+ Ketik Manual Kompi & Peleton';
        toggleBtn.style.color = 'var(--o800)';
    }
}

function calcBmi(prefix) {
    const h = parseFloat(document.getElementById(`${prefix}_height_cm`).value);
    const w = parseFloat(document.getElementById(`${prefix}_weight_kg`).value);
    const badge = document.getElementById(`${prefix}_bmi_badge`);
    
    if (!h || !w || h <= 0 || w <= 0) {
        badge.innerHTML = '-';
        badge.style.background = '#fff';
        badge.style.color = 'var(--muted)';
        return;
    }
    
    const hm = h / 100;
    const bmi = (w / (hm * hm)).toFixed(1);
    let category = 'Normal';
    let bg = '#E8F5E9';
    let col = '#2E7D32';
    
    if (bmi < 18.5) {
        category = 'Kurang (Underweight)';
        bg = '#FFF3CD';
        col = '#856404';
    } else if (bmi <= 25.0) {
        category = 'Ideal / Normal';
        bg = '#E8F5E9';
        col = '#2E7D32';
    } else if (bmi <= 27.0) {
        category = 'Kelebihan (Overweight)';
        bg = '#FFF3CD';
        col = '#856404';
    } else {
        category = 'Obesitas';
        bg = '#FFEBEE';
        col = '#C62828';
    }
    badge.innerHTML = `${bmi} — <span style="font-size:11px;">${category}</span>`;
    badge.style.background = bg;
    badge.style.color = col;
}

// Modal open/close functions
function openCreateStudentModal(defaultSatdikId = null, defaultProgramId = null) {
    const operatorSatdikId = '{{ (!auth()->user()?->isPimpinan() && auth()->user()?->satdik_id) ? auth()->user()->satdik_id : '' }}';
    const sId = operatorSatdikId || defaultSatdikId || '{{ $selectedSatdikId }}' || (allSatdiksData.length > 0 ? allSatdiksData[0].id : '');
    const pId = defaultProgramId || '{{ $selectedProgramId }}' || '';
    
    if (sId) {
        const satdikEl = document.getElementById('create_satdik_id');
        if (satdikEl) satdikEl.value = sId;
        handleSatdikSelectChange(sId, 'create', pId);
    }
    
    document.getElementById('create_error_alert').style.display = 'none';
    document.getElementById('createStudentModal').classList.add('active');
}

function closeCreateStudentModal() {
    document.getElementById('createStudentModal').classList.remove('active');
}

function openEditStudentModal(btnOrData) {
    let s;
    if (typeof btnOrData === 'object' && btnOrData.getAttribute) {
        s = JSON.parse(btnOrData.getAttribute('data-student'));
    } else {
        s = btnOrData;
    }
    
    document.getElementById('editStudentForm').action = `/students/${s.id}`;
    document.getElementById('edit_nosik_badge').textContent = 'NOSIK: ' + (s.nosik || '-');
    document.getElementById('edit_student_title').textContent = s.full_name;
    
    const operatorSatdikId = '{{ (!auth()->user()?->isPimpinan() && auth()->user()?->satdik_id) ? auth()->user()->satdik_id : '' }}';
    const satdikId = operatorSatdikId || s.satdik_id;
    const satdikEl = document.getElementById('edit_satdik_id');
    if (satdikEl) satdikEl.value = satdikId;
    handleSatdikSelectChange(satdikId, 'edit', s.education_program_id, s.classroom_id);
    
    document.getElementById('edit_full_name').value = s.full_name;
    document.getElementById('edit_student_rank').value = s.student_rank;
    document.getElementById('edit_status').value = s.status;
    document.getElementById('edit_origin_military_unit').value = s.origin_military_unit || '';
    document.getElementById('edit_birth_place').value = s.birth_place || '';
    document.getElementById('edit_birth_date').value = s.birth_date || '';
    document.getElementById('edit_gender').value = s.gender || 'L';
    
    document.getElementById('edit_nik').value = '';
    document.getElementById('edit_mother_name').value = s.mother_name || '';
    document.getElementById('edit_emergency_contact_phone').value = s.emergency_contact_phone || '';
    document.getElementById('edit_home_address').value = s.home_address || '';
    
    document.getElementById('edit_height_cm').value = s.height_cm || '';
    document.getElementById('edit_weight_kg').value = s.weight_kg || '';
    calcBmi('edit');
    document.getElementById('edit_blood_pressure').value = s.blood_pressure || '120/80';
    document.getElementById('edit_blood_type').value = s.blood_type || 'O';
    document.getElementById('edit_daily_health_status').value = s.daily_health_status || 'Siap Latih';
    document.getElementById('edit_doctor_notes').value = s.doctor_notes || '';
    
    if (s.company || s.platoon) {
        document.getElementById('edit_manual_company').value = s.company || '';
        document.getElementById('edit_manual_platoon').value = s.platoon || '';
    }
    
    document.getElementById('edit_error_alert').style.display = 'none';
    document.getElementById('editStudentModal').classList.add('active');
}

function closeEditStudentModal() {
    document.getElementById('editStudentModal').classList.remove('active');
}

function openDeleteStudentModal(id, name, nosik, satdikName) {
    document.getElementById('deleteStudentForm').action = `/students/${id}`;
    document.getElementById('deleteModalStudentName').textContent = name;
    document.getElementById('deleteModalStudentNosik').textContent = 'NOSIK: ' + nosik;
    document.getElementById('deleteModalStudentSatdik').textContent = satdikName || 'Satdik Rindam III/Slw';
    document.getElementById('deleteStudentModal').classList.add('active');
}

function closeDeleteStudentModal() {
    document.getElementById('deleteStudentModal').classList.remove('active');
}

// Ajax submission handler for modals
function handleAjaxSubmit(event, type) {
    event.preventDefault();
    const form = event.target;
    const btn = document.getElementById(type === 'create' ? 'btnSubmitCreateStudent' : 'btnSubmitEditStudent');
    const errBox = document.getElementById(`${type}_error_alert`);
    const errList = document.getElementById(`${type}_error_list`);
    
    errBox.style.display = 'none';
    errList.innerHTML = '';
    btn.disabled = true;
    const origBtnHtml = btn.innerHTML;
    btn.innerHTML = '<span class="ms">sync</span> Menyimpan...';
    
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async res => {
        btn.disabled = false;
        btn.innerHTML = origBtnHtml;
        
        if (res.ok) {
            window.location.reload();
        } else if (res.status === 422) {
            const errData = await res.json();
            let msg = '<b>Terdapat kesalahan pengisian formulir:</b><ul style="margin:4px 0 0 16px; padding:0;">';
            if (errData.errors) {
                for (let key in errData.errors) {
                    errData.errors[key].forEach(m => {
                        msg += `<li>${m}</li>`;
                    });
                }
            } else if (errData.message) {
                msg += `<li>${errData.message}</li>`;
            }
            msg += '</ul>';
            errList.innerHTML = msg;
            errBox.style.display = 'flex';
            form.querySelector('.modal-body').scrollTop = 0;
        } else {
            const errData = await res.json().catch(() => null);
            errList.innerHTML = `<b>Terjadi kesalahan pada server:</b> ${errData?.message || 'Silakan periksa kembali input formulir.'}`;
            errBox.style.display = 'flex';
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = origBtnHtml;
        errList.innerHTML = '<b>Gagal menghubungi server.</b> Periksa koneksi jaringan Anda.';
        errBox.style.display = 'flex';
    });
}

// Status & Reveal modal functions
function openStatusModal(studentId, fullName, currentStatus) {
    document.getElementById('statusModalStudentName').textContent = fullName;
    const form = document.getElementById('statusForm');
    form.action = `/students/${studentId}/status`;
    const sel = document.getElementById('statusModalSelect');
    sel.value = currentStatus;
    updateStatusExplanation(currentStatus);
    document.getElementById('statusModal').classList.add('active');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.remove('active');
}

function updateStatusExplanation(status) {
    const box = document.getElementById('statusExplanation');
    if (status === 'Selesai' || status === 'Lulus' || status === 'DO / Dikeluarkan') {
        box.style.borderLeftColor = '#64748B';
        box.style.background = '#F8FAFC';
        box.innerHTML = `<span class="ms" style="font-size:16px; vertical-align:middle; color:#475569;">archive</span> <b>Status Arsip:</b> Siswa ini akan dipindahkan ke kategori <b>Arsip</b> dan <b>TIDAK TERHITUNG LAGI</b> dalam kuota/kekuatan siswa aktif berjalan. Data tetap tersimpan aman untuk penelusuran riwayat/alumni.`;
    } else {
        box.style.borderLeftColor = 'var(--green)';
        box.style.background = '#F0FDF4';
        box.innerHTML = `<span class="ms" style="font-size:16px; vertical-align:middle; color:var(--green);">how_to_reg</span> <b>Status Aktif:</b> Siswa ini sedang menjalani pendidikan dan <b>TERHITUNG</b> dalam kuota/kekuatan aktif harian Satdik & Program Pendidikan.`;
    }
}

function openRevealModal(studentId, fullName, nosik) {
    currentStudentId = studentId;
    document.getElementById('modalStudentId').value = studentId;
    document.getElementById('modalStudentName').textContent = fullName;
    document.getElementById('modalStudentNosik').textContent = 'NOSIK: ' + nosik;

    document.getElementById('modalFormSection').style.display = 'block';
    document.getElementById('modalResultSection').style.display = 'none';
    document.getElementById('accessReasonSelect').value = 'Verifikasi Kelengkapan Administrasi & Berkas Personel';
    document.getElementById('accessReasonText').style.display = 'none';

    document.getElementById('revealModal').classList.add('active');
}

function closeRevealModal() {
    document.getElementById('revealModal').classList.remove('active');
}

function handleReasonChange(select) {
    const customText = document.getElementById('accessReasonText');
    if (select.value === 'Lainnya') {
        customText.style.display = 'block';
        customText.required = true;
        customText.focus();
    } else {
        customText.style.display = 'none';
        customText.required = false;
    }
}

function submitReveal(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitReveal');
    btn.disabled = true;
    btn.innerHTML = '<span class="ms">sync</span> Mendekripsi...';

    const selectVal = document.getElementById('accessReasonSelect').value;
    const textVal = document.getElementById('accessReasonText').value;
    const reason = (selectVal === 'Lainnya') ? textVal : selectVal;

    fetch(`/students/${currentStudentId}/reveal-sensitive`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ access_reason: reason })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<span class="ms">lock_open</span> Dekripsi & Tampilkan';

        if (data.status === 'success') {
            document.getElementById('modalFormSection').style.display = 'none';
            document.getElementById('modalResultSection').style.display = 'block';
            document.getElementById('auditNotice').innerHTML = `<b>Audit Log:</b> Diakses oleh ${data.data.accessed_by} pada ${data.data.unmasked_at}`;

            const d = data.data;
            const rows = [
                { label: 'Nomor Induk Kependudukan (NIK)', val: `<b style="font-family:'Fira Code'; color:var(--o800); font-size:14px;">${d.nik}</b>` },
                { label: 'Nomor Kartu Keluarga (KK)', val: `<span style="font-family:'Fira Code';">${d.family_card_number}</span>` },
                { label: 'Nama Ibu Kandung', val: `<b>${d.mother_name}</b>` },
                { label: 'Nama Ayah', val: `${d.father_name}` },
                { label: 'No. HP Darurat / Orang Tua', val: `<b style="color:var(--green); font-family:'Fira Code';">${d.emergency_contact_phone || d.emergency_contact}</b>` },
                { label: 'Alamat Asal KTP', val: `${d.home_address}` },
            ];

            let html = '';
            rows.forEach((r, idx) => {
                const bg = idx % 2 === 0 ? 'background:#F7F9F4;' : 'background:#fff;';
                html += `
                    <tr style="${bg} border-bottom:1px solid var(--line);">
                        <td style="padding:8px 12px; font-weight:600; width:40%; color:var(--o700);">${r.label}</td>
                        <td style="padding:8px 12px;">${r.val}</td>
                    </tr>
                `;
            });

            document.getElementById('decryptedTable').innerHTML = html;
        } else {
            alert(data.message || 'Gagal mendekripsi data.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<span class="ms">lock_open</span> Dekripsi & Tampilkan';
        alert('Terjadi kesalahan saat memproses dekripsi data.');
    });
}

// Global modal triggers (ESC and Backdrop Click)
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});

window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
    }
});

// Auto-open modal if URL query param action=create exists
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'create') {
        const satdikParam = urlParams.get('satdik_id');
        const progParam = urlParams.get('program_id');
        openCreateStudentModal(satdikParam, progParam);
    }
});
</script>
@endsection
