@extends('layouts.app')

@section('title', 'Pusat Komando & Dashboard Eksekutif Data Siswa & Kesehatan')

@section('content')

<!-- HEADER BANNER PUSAT KOMANDO -->
<div class="header-banner" style="background: linear-gradient(135deg, #10170C 0%, #1D2A16 50%, #2A3B1F 100%); border-left: 5px solid var(--gold);">
    <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px; flex-wrap:wrap;">
            <span class="pdp-badge" style="background:#E8F5E9; color:#1B5E20; border-color:#81C784;">
                <span class="ms" style="font-size:16px;">dashboard</span> EXECUTIVE COMMAND CENTER
            </span>
            <span class="pdp-badge" style="background:rgba(201,162,39,0.18); color:var(--gold2); border-color:var(--gold);">
                <span class="ms" style="font-size:16px;">school</span> DATA SISWA & KESEHATAN
            </span>
            <span class="pdp-badge" style="background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.25);">
                <span class="ms" style="font-size:16px;">verified_user</span> ZONA INTEGRITAS WBK
            </span>
        </div>
        <h1 style="font-size:26px; margin:0 0 8px; letter-spacing:-0.02em;">
            Pusat Komando & Dashboard Eksekutif Data Siswa & Kesehatan — Rindam III/Siliwangi
        </h1>
        <p style="margin:0; font-size:14px; max-width:820px; line-height:1.6;">
            Monitoring terpadu kekuatan serdik lintas 5 Satdik jajaran Rindam III/Siliwangi, status kesiapan fisik & latihan lapangan, rekam medis poliklinik siswa, serta pengawasan keamanan & integritas data personel pada sistem SIPANDU-WBK.
        </p>
    </div>

    <div style="text-align:right; flex-shrink:0;">
        <div style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); padding:10px 16px; border-radius:12px; backdrop-filter:blur(5px);">
            <div style="font-size:11px; color:var(--o200); text-transform:uppercase; letter-spacing:0.08em; font-weight:700;">WAKTU SISTEM</div>
            <div style="font-size:15px; font-weight:800; color:#fff; font-family:'Montserrat',sans-serif; margin-top:2px;">
                {{ now()->translatedFormat('d F Y') }}
            </div>
            <div style="font-size:11.5px; color:var(--gold2); font-weight:600; display:flex; align-items:center; justify-content:flex-end; gap:5px; margin-top:3px;">
                <span class="dot-pulse"></span> Sinkronisasi Real-Time
            </div>
        </div>
    </div>
</div>

<!-- SATDIK FILTER TABS -->
<div class="satdik-nav">
    <a href="{{ route('dashboard') }}" class="satdik-tab {{ is_null($selectedSatdik) ? 'active' : '' }}">
        <span class="ms">apps</span>
        <span>Semua Satdik (Rindam III/Siliwangi)</span>
        <span class="tab-badge">{{ \App\Models\Student::count() }} Serdik</span>
    </a>
    @foreach($satdiks as $s)
        <a href="{{ route('dashboard', ['satdik_id' => $s->id]) }}" class="satdik-tab {{ $selectedSatdik?->id === $s->id ? 'active' : '' }}">
            <span class="ms">school</span>
            <span>{{ $s->code }}</span>
            <span class="tab-badge" title="Siswa Aktif Terhitung">{{ $s->students()->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count() }}</span>
        </a>
    @endforeach
</div>

<!-- STATS STRATEGIS MAKRO -->
<div class="stats-grid">
    <!-- KEKUATAN SERDIK AKTIF TERHITUNG -->
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--green), #2E7D32);">
            <span class="ms">how_to_reg</span>
        </div>
        <div style="flex:1;">
            <div class="stat-val" style="color:var(--green);">{{ $countedActiveStudents }}</div>
            <div class="stat-lbl">{{ $selectedSatdik ? 'Siswa Aktif ' . $selectedSatdik->code : 'Siswa Aktif Terhitung' }}</div>
            <div style="font-size:11px; color:var(--muted); margin-top:4px;">
                <b style="color:var(--green);">{{ $activeStudents }}</b> Siap Latih · 
                <b style="color:var(--amber);">{{ $sickStudents }}</b> Dispen · 
                <span style="color:#64748B;"><b>{{ $archivedStudents }}</b> Arsip Selesai</span>
            </div>
        </div>
    </div>

    <!-- KESIAPAN LATIHAN -->
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, #1B5E20, #2E7D32);">
            <span class="ms">fitness_center</span>
        </div>
        <div style="flex:1;">
            <div class="stat-val" style="color:#1B5E20;">{{ $siapLatihPercent }}%</div>
            <div class="stat-lbl">Tingkat Kesiapan Latihan</div>
            <div style="font-size:11px; color:var(--muted); margin-top:4px;">
                <b>{{ $siapLatih }}</b> dari {{ $totalHealth }} Serdik Siap Latih Penuh
            </div>
        </div>
    </div>

    <!-- PEMANTAUAN MEDIS -->
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, #D32F2F, #C62828);">
            <span class="ms">medical_services</span>
        </div>
        <div style="flex:1;">
            <div class="stat-val" style="color:var(--red);">{{ $perawatanCount }}</div>
            <div class="stat-lbl">Perawatan Medis Poliklinik</div>
            <div style="font-size:11px; color:var(--muted); margin-top:4px;">
                {{ $berobatJalan }} Jalan · {{ $rawatPoliklinik }} Inap · {{ $rujukRumkit }} Rujuk
            </div>
        </div>
    </div>

    <!-- RAWAT DI RUMAH SAKIT / RUJUK RUMKIT -->
    <a href="{{ route('health.index', ['status' => 'Rujuk Rumkit']) }}" class="stat-card" style="text-decoration:none; color:inherit;" title="Klik untuk memantau data siswa rawat di Rumah Sakit / Rujuk Rumkit">
        <div class="stat-icon" style="background:linear-gradient(135deg, #0284C7, #0369A1);">
            <span class="ms">local_hospital</span>
        </div>
        <div style="flex:1;">
            <div class="stat-val" style="color:#0284C7;">{{ $rujukRumkit }}</div>
            <div class="stat-lbl">Rawat di Rumah Sakit</div>
            <div style="font-size:11px; color:var(--muted); margin-top:4px;">
                @if($rujukRumkit > 0)
                    <b style="color:#0284C7;">{{ $rujukRumkit }} Siswa</b> Rujuk Rumkit Tk. II/IV · {{ $rawatPoliklinik }} Inap Poliklinik
                @else
                    <b>{{ $rujukRumkit }}</b> Rujuk Rumkit · <b>{{ $rawatPoliklinik }}</b> Rawat Inap Poliklinik
                @endif
            </div>
        </div>
    </a>
</div>

<!-- =====================================================================
     DISTRIBUSI SERDIK PER SATDIK (5 LEMDIK) GRID
     ===================================================================== -->
<div class="card" style="margin-bottom:28px;">
    <div class="card-header">
        <div>
            <h3 class="card-title">
                <span class="ms" style="color:var(--o700); font-size:22px;">domain</span>
                Kekuatan Serdik Lintas 5 Satuan Pendidikan (Satdik)
            </h3>
            <div style="font-size:12.5px; color:var(--muted); margin-top:2px;">
                Rekapitulasi perbandingan siswa aktif, kesiapan latihan lapangan, dan penanganan poliklinik per satuan
            </div>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-outline btn-sm">
            <span class="ms">open_in_new</span> Buka Buku Induk Lengkap
        </a>
    </div>
    <div class="card-body" style="padding:20px;">
        <div style="display:grid; grid-template-columns:repeat(5, 1fr); gap:14px;">
            @foreach($satdikBreakdown as $sb)
                @php
                    $isSel = $selectedSatdik?->id === $sb['satdik']->id;
                @endphp
                <div style="background:{{ $isSel ? '#F4F7F2' : '#fff' }}; border:{{ $isSel ? '2px solid var(--o700)' : '1px solid var(--line)' }}; border-radius:14px; padding:16px; box-shadow:0 2px 8px rgba(0,0,0,0.03); display:flex; flex-direction:column; justify-content:space-between;">
                    <div>
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                            <span class="badge badge-satdik" style="font-size:11px; font-weight:800;">
                                {{ $sb['satdik']->code }}
                            </span>
                            <span class="badge {{ $sb['sick_count'] > 0 ? 'badge-amber' : 'badge-green' }}" style="font-size:10.5px;">
                                {{ $sb['sick_count'] > 0 ? $sb['sick_count'] . ' Sakit' : 'Semua Sehat' }}
                            </span>
                        </div>
                        <h4 style="margin:0 0 2px; font-family:'Montserrat',sans-serif; font-size:14px; color:var(--o800); font-weight:800;">
                            {{ $sb['satdik']->name }}
                        </h4>
                        <div style="font-size:11.5px; color:var(--muted); margin-bottom:12px;">
                            📍 {{ $sb['satdik']->location }}
                        </div>

                        <div style="display:flex; align-items:baseline; gap:6px; margin-bottom:8px;">
                            <span style="font-family:'Montserrat',sans-serif; font-size:28px; font-weight:900; color:var(--o900); line-height:1;">
                                {{ $sb['total'] }}
                            </span>
                            <span style="font-size:12px; color:var(--muted); font-weight:600;">Serdik Terdata</span>
                        </div>

                        <div style="font-size:11.5px; color:var(--muted); margin-bottom:6px; display:flex; justify-content:space-between;">
                            <span>Kesiapan Latihan:</span>
                            <b style="color:var(--green);">{{ $sb['ready_percent'] }}%</b>
                        </div>
                        <div style="width:100%; height:5px; background:var(--o100); border-radius:999px; overflow:hidden; margin-bottom:12px;">
                            <div style="width:{{ $sb['ready_percent'] }}%; height:100%; background:var(--green); border-radius:999px;"></div>
                        </div>
                    </div>

                    <div style="display:flex; gap:6px; margin-top:8px;">
                        <a href="{{ route('students.index', ['satdik_id' => $sb['satdik']->id]) }}" class="btn btn-outline btn-sm" style="flex:1; justify-content:center; font-size:11px; padding:5px 6px;" title="Buka Data Siswa">
                            <span class="ms" style="font-size:14px;">groups</span> Siswa
                        </a>
                        <a href="{{ route('health.index', ['satdik_id' => $sb['satdik']->id]) }}" class="btn btn-outline btn-sm" style="flex:1; justify-content:center; font-size:11px; padding:5px 6px; color:var(--green);" title="Buka Rekam Medis">
                            <span class="ms" style="font-size:14px;">medical_services</span> Medis
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- =====================================================================
     PANEL DUA KOLOM: KESIAPAN MEDIS & STAKES vs AUDIT LOG PDP
     ===================================================================== -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-bottom:28px;">
    
    <!-- KOLOM KIRI: STATUS POLIKLINIK & DISTRIBUSI STAKES -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <h3 class="card-title">
                <span class="ms" style="color:var(--green); font-size:22px;">health_and_safety</span>
                Kondisi & Kesiapan Fisik Serdik
            </h3>
            <a href="{{ route('health.index') }}" class="btn btn-outline btn-sm">
                Kelola Medis <span class="ms" style="font-size:15px;">arrow_forward</span>
            </a>
        </div>
        <div class="card-body">
            <!-- BREAKDOWN STATUS HARIAN -->
            <div style="margin-bottom:20px;">
                <div style="font-size:12.5px; font-weight:800; color:var(--o800); text-transform:uppercase; margin-bottom:10px; letter-spacing:0.04em;">
                    Status Kesiapan Fisik Serdik Harian
                </div>

                <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px; text-align:center;">
                    <div style="background:#E8F5E9; border:1px solid #C8E6C9; padding:12px 8px; border-radius:10px;">
                        <div style="font-size:22px; font-weight:900; color:var(--green); font-family:'Montserrat',sans-serif;">{{ $siapLatih }}</div>
                        <div style="font-size:11px; font-weight:700; color:var(--green); margin-top:2px;">Siap Latih</div>
                    </div>
                    <div style="background:#FFF8E1; border:1px solid #FFE082; padding:12px 8px; border-radius:10px;">
                        <div style="font-size:22px; font-weight:900; color:#8A680C; font-family:'Montserrat',sans-serif;">{{ $berobatJalan }}</div>
                        <div style="font-size:11px; font-weight:700; color:#8A680C; margin-top:2px;">Berobat Jalan</div>
                    </div>
                    <div style="background:#FFEBEE; border:1px solid #FFCDD2; padding:12px 8px; border-radius:10px;">
                        <div style="font-size:22px; font-weight:900; color:var(--red); font-family:'Montserrat',sans-serif;">{{ $rawatPoliklinik }}</div>
                        <div style="font-size:11px; font-weight:700; color:var(--red); margin-top:2px;">Rawat Inap</div>
                    </div>
                    <div style="background:#E3F2FD; border:1px solid #BBDEFB; padding:12px 8px; border-radius:10px;">
                        <div style="font-size:22px; font-weight:900; color:var(--blue); font-family:'Montserrat',sans-serif;">{{ $rujukRumkit }}</div>
                        <div style="font-size:11px; font-weight:700; color:var(--blue); margin-top:2px;">Rujuk Rumkit</div>
                    </div>
                </div>
            </div>

            <!-- DISTRIBUSI STAKES MILITER -->
            <div style="border-top:1px solid var(--line); pt:16px;">
                <div style="font-size:12.5px; font-weight:800; color:var(--o800); text-transform:uppercase; margin-bottom:12px; letter-spacing:0.04em;">
                    Kualifikasi Stakes Militer Serdik
                </div>

                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:3px;">
                            <span><b>Stakes I</b> · Sangat Baik (Tanpa Keluhan)</span>
                            <span style="font-weight:800; color:var(--green);">{{ $stakesI }} Siswa</span>
                        </div>
                        <div style="width:100%; height:6px; background:#EEF2EB; border-radius:999px; overflow:hidden;">
                            <div style="width:{{ $totalHealth > 0 ? ($stakesI / $totalHealth) * 100 : 0 }}%; height:100%; background:var(--green); border-radius:999px;"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:3px;">
                            <span><b>Stakes II</b> · Baik (Mampu Mengikuti Seluruh Materi)</span>
                            <span style="font-weight:800; color:#1565C0;">{{ $stakesII }} Siswa</span>
                        </div>
                        <div style="width:100%; height:6px; background:#EEF2EB; border-radius:999px; overflow:hidden;">
                            <div style="width:{{ $totalHealth > 0 ? ($stakesII / $totalHealth) * 100 : 0 }}%; height:100%; background:#1565C0; border-radius:999px;"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:3px;">
                            <span><b>Stakes III</b> · Cukup (Pemantauan Rutin / Dispensasi Khusus)</span>
                            <span style="font-weight:800; color:#E65100;">{{ $stakesIII }} Siswa</span>
                        </div>
                        <div style="width:100%; height:6px; background:#EEF2EB; border-radius:999px; overflow:hidden;">
                            <div style="width:{{ $totalHealth > 0 ? ($stakesIII / $totalHealth) * 100 : 0 }}%; height:100%; background:#E65100; border-radius:999px;"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:3px;">
                            <span><b>Stakes IV</b> · Tidak Memenuhi Syarat Latihan Sementara</span>
                            <span style="font-weight:800; color:var(--red);">{{ $stakesIV }} Siswa</span>
                        </div>
                        <div style="width:100%; height:6px; background:#EEF2EB; border-radius:999px; overflow:hidden;">
                            <div style="width:{{ $totalHealth > 0 ? ($stakesIV / $totalHealth) * 100 : 0 }}%; height:100%; background:var(--red); border-radius:999px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: AUDIT TRAIL FORENSIK KEAMANAN SISTEM (AREA 5 ZONA INTEGRITAS) -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <h3 class="card-title">
                <span class="ms" style="color:var(--gold); font-size:22px;">policy</span>
                Pengawasan ZI Area 5 (Log Audit SIPANDU)
            </h3>
            <a href="{{ route('students.audit-logs') }}" class="btn btn-outline btn-sm">
                Lihat Seluruh Log <span class="ms" style="font-size:15px;">arrow_forward</span>
            </a>
        </div>
        <div class="card-body" style="padding:16px 20px;">
            <div style="font-size:12px; color:var(--muted); margin-bottom:14px; background:var(--gold-bg); border:1px solid #FFE082; padding:8px 12px; border-radius:8px;">
                <span class="ms" style="font-size:16px; vertical-align:text-bottom; color:#8A680C;">security</span>
                Setiap dekripsi data kependudukan sensitif dicatat permanen demi akuntabilitas Wilayah Bebas dari Korupsi.
            </div>

            @if($recentAuditLogs->isEmpty())
                <div style="text-align:center; padding:30px 20px; color:var(--muted);">
                    <span class="ms" style="font-size:36px; opacity:0.4;">history_toggle_off</span>
                    <p style="margin:8px 0 0; font-size:13px;">Belum ada riwayat pembukaan data pribadi sensitif.</p>
                </div>
            @else
                <div style="display:flex; flex-direction:column; gap:10px;">
                    @foreach($recentAuditLogs as $log)
                        <div style="background:#fff; border:1px solid var(--line); border-radius:10px; padding:10px 14px; display:flex; align-items:center; justify-content:space-between;">
                            <div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span class="badge badge-satdik" style="font-size:10.5px;">
                                        ID #{{ $log->id }}
                                    </span>
                                    <b style="font-size:13px; color:var(--o900);">{{ $log->user?->name ?? 'Administrator' }}</b>
                                </div>
                                <div style="font-size:12px; color:var(--muted); margin-top:2px;">
                                    Siswa: <b style="color:var(--o800);">{{ $log->student?->full_name ?? 'Serdik #' . $log->student_id }}</b>
                                </div>
                                <div style="font-size:11.5px; color:#556E3B; margin-top:2px; font-style:italic;">
                                    "{{ Str::limit($log->access_reason, 55) }}"
                                </div>
                            </div>

                            <div style="text-align:right;">
                                <div style="font-size:11px; font-weight:700; color:var(--muted);">
                                    {{ \Carbon\Carbon::parse($log->accessed_at)->format('H:i') }} WIB
                                </div>
                                <div style="font-size:10px; color:var(--muted);">
                                    {{ \Carbon\Carbon::parse($log->accessed_at)->format('d/m/Y') }}
                                </div>
                                <span class="masked-pill" style="font-size:10px; margin-top:4px;">
                                    IP: {{ $log->ip_address }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

<!-- =====================================================================
     TABEL AKTIVITAS SERDIK TERKINI (REAL-TIME FEED)
     ===================================================================== -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">
                <span class="ms" style="color:var(--o700); font-size:22px;">format_list_bulleted</span>
                Daftar Serdik Terkini (Real-Time Feed)
            </h3>
            <div style="font-size:12.5px; color:var(--muted); margin-top:2px;">
                Catatan mutasi data siswa, penempatan kompi/peleton, serta status serdik terkini (klik pada baris untuk melihat data lengkap)
            </div>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('students.index', ['action' => 'create']) }}" class="btn btn-gold btn-sm">
                <span class="ms" style="font-size:16px;">person_add</span> Tambah Siswa Baru
            </a>
            <a href="{{ route('students.index') }}" class="btn btn-primary btn-sm">
                <span class="ms" style="font-size:16px;">groups</span> Buka Seluruh Siswa
            </a>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:120px;">NOSIK</th>
                    <th>NAMA LENGKAP SERDIK</th>
                    <th>SATDIK & PENDIDIKAN</th>
                    <th>KOMPI / TON</th>
                    <th>STATUS</th>
                    <th style="text-align:right; width:130px;">AKSI CEPAT</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentStudents as $student)
                    <tr style="cursor:pointer; transition:background-color 0.15s;" 
                        onclick="if(!event.target.closest('a') && !event.target.closest('button')) window.location='{{ route('students.show', $student) }}'" 
                        title="Klik untuk melihat data lengkap serdik {{ $student->full_name }}"
                        onmouseover="this.style.backgroundColor='#F9FAF7'" 
                        onmouseout="this.style.backgroundColor=''">
                        <td>
                            <span class="masked-pill" style="font-weight:700;">{{ $student->nosik }}</span>
                        </td>
                        <td>
                            <a href="{{ route('students.show', $student) }}" style="font-weight:700; color:var(--o900); font-size:14px; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                {{ $student->full_name }}
                            </a>
                            <div style="font-size:11.5px; color:var(--muted);">
                                Gelombang {{ $student->batch_year }} · Angkatan {{ $student->admission_year }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-satdik">{{ $student->satdik?->code }}</span>
                            <div style="font-size:11.5px; color:var(--muted); margin-top:2px;">
                                {{ $student->educationProgram?->name ?? 'Pendidikan Pembentukan' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:600; color:var(--o800);">
                                {{ $student->company ?? '-' }}
                            </div>
                            <div style="font-size:11.5px; color:var(--muted);">
                                {{ $student->platoon ?? '-' }}
                            </div>
                        </td>
                        <td>
                            @php $u = $student->unified_status; @endphp
                            <span class="badge {{ $u['badge'] }}" style="font-size:11.5px; padding:4px 9px;">
                                <span class="ms" style="font-size:14px;">{{ $u['icon'] }}</span>
                                {{ $u['label'] }}
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:4px;">
                                <a href="{{ route('students.show', $student) }}" class="btn btn-outline btn-sm" title="Dossier Lengkap Siswa">
                                    <span class="ms" style="font-size:15px;">visibility</span>
                                </a>
                                <a href="{{ route('health.show', $student) }}" class="btn btn-outline btn-sm" style="color:var(--green);" title="Rekam Medis">
                                    <span class="ms" style="font-size:15px;">medical_services</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:32px; color:var(--muted);">
                            Tidak ada data serdik yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- =====================================================================
     DIAGRAM GRAFIK ANALITIK & PERSENTASE (EXECUTIVE CHARTS - PALING BAWAH)
     ===================================================================== -->
<div style="margin-top: 36px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 38px; height: 38px; border-radius: 10px; background: var(--o100); color: var(--o800); display: grid; place-items: center;">
                <span class="ms" style="font-size: 22px;">analytics</span>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--o900); text-transform: uppercase; letter-spacing: 0.04em;">
                    Grafik & Visualisasi Analitik Eksekutif
                </h3>
                <div style="font-size: 12px; color: var(--muted); margin-top: 2px;">
                    Diagram proporsi kesiapan fisik, perbandingan kekuatan antar Satdik, status kesiswaan, dan kualifikasi stakes militer
                </div>
            </div>
        </div>
        <span class="badge badge-satdik" style="font-size: 12px; padding: 6px 12px;">
            <span class="ms" style="font-size: 14px; margin-right: 4px;">query_stats</span> 4 Indikator Analitik
        </span>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">

    <!-- GRAFIK 1: DIAGRAM DONUT STATUS KESIAPAN FISIK & MEDIS -->
    <div class="card" style="margin-bottom:0; display:flex; flex-direction:column;">
        <div class="card-header">
            <div>
                <h3 class="card-title">
                    <span class="ms" style="color:var(--green); font-size:22px;">pie_chart</span>
                    Grafik Persentase Kesiapan Fisik & Medis
                </h3>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;">
                    Komposisi serdik siap latihan vs dalam penanganan poliklinik
                </div>
            </div>
            <span class="badge badge-green" style="font-size:12px;">
                {{ $siapLatihPercent }}% Siap Latih
            </span>
        </div>
        <div class="card-body" style="padding:20px; flex:1; display:flex; flex-direction:column; justify-content:center;">
            <div style="position:relative; height:230px; display:flex; align-items:center; justify-content:center;">
                <canvas id="healthStatusChart"></canvas>
            </div>
            
            <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px; margin-top:16px; pt:14px; border-top:1px solid var(--line);">
                <div style="display:flex; align-items:center; justify-content:space-between; background:var(--o50); padding:8px 12px; border-radius:8px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="width:10px; height:10px; border-radius:50%; background:#2E7D32; display:inline-block;"></span>
                        <span style="font-size:12px; font-weight:600; color:var(--o900);">Siap Latih</span>
                    </div>
                    <b style="font-size:12.5px; color:#2E7D32;">{{ $chartData['health_status']['percentages'][0] }}% ({{ $siapLatih }})</b>
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; background:var(--o50); padding:8px 12px; border-radius:8px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="width:10px; height:10px; border-radius:50%; background:#D99A1E; display:inline-block;"></span>
                        <span style="font-size:12px; font-weight:600; color:var(--o900);">Berobat Jalan</span>
                    </div>
                    <b style="font-size:12.5px; color:#8A680C;">{{ $chartData['health_status']['percentages'][1] }}% ({{ $berobatJalan }})</b>
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; background:var(--o50); padding:8px 12px; border-radius:8px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="width:10px; height:10px; border-radius:50%; background:#C62828; display:inline-block;"></span>
                        <span style="font-size:12px; font-weight:600; color:var(--o900);">Rawat Poliklinik</span>
                    </div>
                    <b style="font-size:12.5px; color:var(--red);">{{ $chartData['health_status']['percentages'][2] }}% ({{ $rawatPoliklinik }})</b>
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; background:var(--o50); padding:8px 12px; border-radius:8px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="width:10px; height:10px; border-radius:50%; background:#1565C0; display:inline-block;"></span>
                        <span style="font-size:12px; font-weight:600; color:var(--o900);">Rujuk Rumkit</span>
                    </div>
                    <b style="font-size:12.5px; color:var(--blue);">{{ $chartData['health_status']['percentages'][3] }}% ({{ $rujukRumkit }})</b>
                </div>
            </div>
        </div>
    </div>

    <!-- GRAFIK 2: DIAGRAM BATANG PERBANDINGAN KEKUATAN & KESIAPAN PER SATDIK -->
    <div class="card" style="margin-bottom:0; display:flex; flex-direction:column;">
        <div class="card-header">
            <div>
                <h3 class="card-title">
                    <span class="ms" style="color:var(--o700); font-size:22px;">bar_chart</span>
                    Grafik Kekuatan & Kesiapan Latihan per Satdik
                </h3>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;">
                    Distribusi siswa siap latih vs perawatan medis di 5 satuan pendidikan
                </div>
            </div>
            <span class="badge badge-satdik" style="font-size:12px;">5 Satdik</span>
        </div>
        <div class="card-body" style="padding:20px; flex:1; display:flex; flex-direction:column; justify-content:center;">
            <div style="position:relative; height:230px;">
                <canvas id="satdikComparisonChart"></canvas>
            </div>
            <div style="display:flex; align-items:center; justify-content:center; gap:20px; margin-top:16px; pt:14px; border-top:1px solid var(--line); font-size:12px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span style="width:12px; height:12px; border-radius:3px; background:#2E7D32; display:inline-block;"></span>
                    <span style="font-weight:600; color:var(--o900);">Serdik Siap Latih</span>
                </div>
                <div style="display:flex; align-items:center; gap:6px;">
                    <span style="width:12px; height:12px; border-radius:3px; background:#C62828; display:inline-block;"></span>
                    <span style="font-weight:600; color:var(--o900);">Perawatan / Sakit</span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- BARIS KEDUA DIAGRAM: STATUS SERDIK & STAKES DISTRIBUTION -->
<div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px;">

    <!-- GRAFIK 3: DIAGRAM STATUS KESISWAAN (AKTIF, SAKIT, LULUS, DO) -->
    <div class="card" style="margin-bottom:0; display:flex; flex-direction:column;">
        <div class="card-header">
            <div>
                <h3 class="card-title">
                    <span class="ms" style="color:var(--gold); font-size:22px;">pie_chart</span>
                    Grafik Persentase Status Kesiswaan Serdik
                </h3>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;">
                    Proporsi status siswa aktif, sakit/perawatan, lulus, dan putus pendidikan (DO)
                </div>
            </div>
            <span class="badge badge-gold" style="font-size:12px;">Data Siswa</span>
        </div>
        <div class="card-body" style="padding:20px; flex:1; display:flex; flex-direction:column; justify-content:center;">
            <div style="position:relative; height:200px; display:flex; align-items:center; justify-content:center;">
                <canvas id="studentStatusChart"></canvas>
            </div>
            
            <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:8px; margin-top:14px; pt:12px; border-top:1px solid var(--line);">
                <div style="background:var(--o50); padding:7px 10px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:11.5px; font-weight:600; color:var(--green);">🟢 Siswa Aktif</span>
                    <b style="font-size:12px;">{{ $chartData['student_status']['percentages'][0] }}%</b>
                </div>
                <div style="background:var(--o50); padding:7px 10px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:11.5px; font-weight:600; color:var(--red);">🔴 Siswa Sakit</span>
                    <b style="font-size:12px;">{{ $chartData['student_status']['percentages'][1] }}%</b>
                </div>
                <div style="background:var(--o50); padding:7px 10px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:11.5px; font-weight:600; color:#1565C0;">🔵 Siswa Lulus</span>
                    <b style="font-size:12px;">{{ $chartData['student_status']['percentages'][2] }}%</b>
                </div>
                <div style="background:var(--o50); padding:7px 10px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:11.5px; font-weight:600; color:var(--muted);">⚪ DO / Keluar</span>
                    <b style="font-size:12px;">{{ $chartData['student_status']['percentages'][3] }}%</b>
                </div>
            </div>
        </div>
    </div>

    <!-- GRAFIK 4: DIAGRAM DONUT KUALIFIKASI STAKES MILITER -->
    <div class="card" style="margin-bottom:0; display:flex; flex-direction:column;">
        <div class="card-header">
            <div>
                <h3 class="card-title">
                    <span class="ms" style="color:#1565C0; font-size:22px;">donut_large</span>
                    Grafik Persentase Kualifikasi Stakes Militer
                </h3>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;">
                    Proporsi tingkat kesehatan fisik prajurit siswa (Stakes I s.d. IV)
                </div>
            </div>
            <span class="badge badge-blue" style="font-size:12px;">Stakes I-IV</span>
        </div>
        <div class="card-body" style="padding:20px; flex:1; display:flex; flex-direction:column; justify-content:center;">
            <div style="position:relative; height:200px; display:flex; align-items:center; justify-content:center;">
                <canvas id="stakesChart"></canvas>
            </div>
            
            <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:8px; margin-top:14px; pt:12px; border-top:1px solid var(--line);">
                <div style="background:var(--o50); padding:7px 10px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:11.5px; font-weight:600; color:var(--green);">🟢 Stakes I (Sangat Baik)</span>
                    <b style="font-size:12px;">{{ $chartData['stakes_distribution']['percentages'][0] }}%</b>
                </div>
                <div style="background:var(--o50); padding:7px 10px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:11.5px; font-weight:600; color:#1565C0;">🔵 Stakes II (Baik)</span>
                    <b style="font-size:12px;">{{ $chartData['stakes_distribution']['percentages'][1] }}%</b>
                </div>
                <div style="background:var(--o50); padding:7px 10px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:11.5px; font-weight:600; color:#E65100;">🟠 Stakes III (Cukup)</span>
                    <b style="font-size:12px;">{{ $chartData['stakes_distribution']['percentages'][2] }}%</b>
                </div>
                <div style="background:var(--o50); padding:7px 10px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:11.5px; font-weight:600; color:var(--red);">🔴 Stakes IV (TMS Sem.)</span>
                    <b style="font-size:12px;">{{ $chartData['stakes_distribution']['percentages'][3] }}%</b>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const data = @json($chartData);

    // 1. CHART STATUS KESEHATAN (DOUGHNUT)
    const ctxHealth = document.getElementById('healthStatusChart');
    if (ctxHealth) {
        new Chart(ctxHealth, {
            type: 'doughnut',
            data: {
                labels: data.health_status.labels,
                datasets: [{
                    data: data.health_status.counts,
                    backgroundColor: ['#2E7D32', '#D99A1E', '#C62828', '#1565C0'],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const val = context.raw || 0;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${val} Siswa (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. CHART PERBANDINGAN SATDIK (BAR)
    const ctxSatdik = document.getElementById('satdikComparisonChart');
    if (ctxSatdik) {
        new Chart(ctxSatdik, {
            type: 'bar',
            data: {
                labels: data.satdik_comparison.labels,
                datasets: [
                    {
                        label: 'Siap Latih',
                        data: data.satdik_comparison.ready,
                        backgroundColor: '#2E7D32',
                        borderRadius: 6
                    },
                    {
                        label: 'Perawatan/Sakit',
                        data: data.satdik_comparison.sick,
                        backgroundColor: '#C62828',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold' } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            afterTitle: function (items) {
                                const idx = items[0].dataIndex;
                                return data.satdik_comparison.names[idx] + ' (' + data.satdik_comparison.ready_percent[idx] + '% Siap)';
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. CHART STATUS KESISWAAN (DOUGHNUT)
    const ctxStudentStatus = document.getElementById('studentStatusChart');
    if (ctxStudentStatus) {
        new Chart(ctxStudentStatus, {
            type: 'doughnut',
            data: {
                labels: data.student_status.labels,
                datasets: [{
                    data: data.student_status.counts,
                    backgroundColor: ['#2E7D32', '#C62828', '#1565C0', '#6C757D'],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const val = context.raw || 0;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${val} Siswa (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 4. CHART STAKES MILITER (DOUGHNUT)
    const ctxStakes = document.getElementById('stakesChart');
    if (ctxStakes) {
        new Chart(ctxStakes, {
            type: 'doughnut',
            data: {
                labels: data.stakes_distribution.labels,
                datasets: [{
                    data: data.stakes_distribution.counts,
                    backgroundColor: ['#2E7D32', '#1565C0', '#E65100', '#C62828'],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const val = context.raw || 0;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${val} Siswa (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
