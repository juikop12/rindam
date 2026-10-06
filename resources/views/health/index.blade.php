@extends('layouts.app')

@section('title', 'Kesehatan & Rekam Medis Serdik — SIPANDU-WBK')

@section('content')

<!-- HEADER BANNER -->
<div class="header-banner">
    <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <span class="pdp-badge" style="background:#E8F5E9; color:#1B5E20; border-color:#81C784;">
                <span class="ms" style="font-size:16px;">medical_services</span> POLIKLINIK & TONKES SATDIK
            </span>
            <span class="pdp-badge" style="background:rgba(255,255,255,0.15);color:#fff;border-color:rgba(255,255,255,0.25);">
                <span class="ms" style="font-size:16px;">health_and_safety</span> KESIAPAN KESEHATAN SISWA
            </span>
        </div>
        <h1>Pengelolaan Kesehatan & Rekam Medis Serdik per Satdik</h1>
        <p>
            Modul pemantauan kesehatan fisik serdik terpadu: status kesiapan latihan lapangan, pemantauan program pendidikan, verifikasi Kodam/Kodim asal, pemisahan siswa aktif berjalan dengan arsip lulusan, serta penanganan rujukan Rumkit dinas.
        </p>
    </div>
    <div style="display:flex; gap:10px; align-items:flex-start; flex-wrap:wrap;">
        <button type="button" onclick="window.print()" class="btn btn-outline btn-sm" style="background:rgba(255,255,255,0.1); color:#fff; border-color:rgba(255,255,255,0.3);">
            <span class="ms">print</span> Cetak Rekap Medis
        </button>
        <a href="{{ route('students.index') }}" class="btn btn-gold btn-sm">
            <span class="ms">school</span> Buku Induk Siswa
        </a>
    </div>
</div>

<!-- STATS SUMMARY (IDENTIK & TERPADU DENGAN DATA SISWA & PROGRAM) -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--green), #2E7D32);">
            <span class="ms">how_to_reg</span>
        </div>
        <div>
            <div class="stat-val" style="color:var(--green);">{{ $stats['overall_counted'] }}</div>
            <div class="stat-lbl">Siswa Aktif Terhitung (Pendidikan Berjalan)</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                {{ $stats['siap_latih'] }} Siap Latih • {{ $stats['berobat_jalan'] }} Berobat/Dispen
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
                {{ $stats['overall_finished'] }} Rekam Medis Historis Lulusan
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
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--red), #C62828);">
            <span class="ms">local_hospital</span>
        </div>
        <div>
            <div class="stat-val" style="color:var(--red);">{{ $stats['perawatan_khusus'] }}</div>
            <div class="stat-lbl">Perawatan Poliklinik & Rujuk Rumkit</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                {{ $stats['rawat_inap'] }} Rawat Inap • {{ $stats['rujuk_rumkit'] }} Rujuk Rumkit
            </small>
        </div>
    </div>
</div>

<!-- SATDIK NAVIGATION TABS (MENAMPILKAN KUOTA AKTIF SERUPA DATA SISWA) -->
@if(auth()->check() && !auth()->user()->isPimpinan() && auth()->user()->satdik_id)
    <div class="satdik-nav" style="margin-bottom:20px;">
        <div class="satdik-tab active" style="cursor:default; background:var(--o800); color:var(--gold2); border-color:var(--gold); box-shadow:0 4px 12px rgba(29,42,22,0.15);">
            <span class="ms" style="color:var(--gold);">lock</span> 
            <span>SATDIK ANDA: <b>{{ auth()->user()->satdik?->code }}</b> &mdash; {{ auth()->user()->satdik?->name }}</span>
            <span class="tab-badge" style="background:var(--gold); color:var(--o900); font-weight:800;">
                🔒 Terkunci Sesuai Wewenang Akun
            </span>
        </div>
    </div>
@else
    <div class="satdik-nav">
        <a href="{{ route('health.index', array_merge(request()->except(['satdik_id', 'page']))) }}" class="satdik-tab {{ empty($selectedSatdikId) ? 'active' : '' }}">
            <span class="ms">domain</span> Semua Satdik
            <span class="tab-badge" title="Siswa Aktif Terhitung">{{ $stats['overall_counted'] }} Aktif</span>
        </a>

        @foreach($satdiks as $satdik)
            @php
                $satdikCounted = $satdik->students()->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count();
            @endphp
            <a href="{{ route('health.index', array_merge(request()->except(['page']), ['satdik_id' => $satdik->id])) }}" class="satdik-tab {{ $selectedSatdikId == $satdik->id ? 'active' : '' }}">
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
                    Kesehatan per Program Pendidikan (TA 2026)
                </h4>
                <div style="color:var(--muted); font-size:12px; margin-top:2px;">
                    Klik salah satu program untuk menyaring rekam medis serdik pada program tersebut
                </div>
            </div>
        </div>
        @if($selectedProgramId)
            <a href="{{ route('health.index', array_merge(request()->except(['program_id', 'page']))) }}" class="btn btn-outline btn-sm" style="font-size:12px; padding:6px 12px; border-color:var(--line); color:var(--muted);">
                <span class="ms" style="font-size:15px;">close</span> Tampilkan Semua Program
            </a>
        @endif
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(210px, 1fr)); gap:12px;">
        @foreach($stats['program_list'] as $prog)
            @php
                $isProgActive = ($selectedProgramId == $prog['id']);
            @endphp
            <a href="{{ route('health.index', array_merge(request()->except(['page']), ['program_id' => $isProgActive ? null : $prog['id']])) }}"
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
        <a href="{{ route('health.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'all'])) }}"
           style="padding:7px 16px; border-radius:7px; font-size:12.5px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px; transition:all 0.15s; {{ $tab == 'all' ? 'background:#fff; color:var(--o900); box-shadow:0 1px 3px rgba(0,0,0,0.1);' : 'color:var(--muted);' }}">
            <span class="ms" style="font-size:16px;">people</span> Semua Serdik
            <span class="badge" style="background:#E2E8F0; color:#334155; font-size:11px; padding:2px 7px;">{{ $stats['overall_total'] }}</span>
        </a>
        <a href="{{ route('health.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'aktif'])) }}"
           style="padding:7px 16px; border-radius:7px; font-size:12.5px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px; transition:all 0.15s; {{ $tab == 'aktif' ? 'background:var(--green); color:#fff; box-shadow:0 2px 6px rgba(46,125,50,0.3);' : 'color:var(--muted);' }}">
            <span class="ms" style="font-size:16px;">how_to_reg</span> Siswa Aktif Terhitung
            <span class="badge" style="background:{{ $tab == 'aktif' ? '#fff' : '#E8F5E9' }}; color:{{ $tab == 'aktif' ? 'var(--green)' : 'var(--green)' }}; font-size:11px; padding:2px 7px;">{{ $stats['overall_counted'] }}</span>
        </a>
        <a href="{{ route('health.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'arsip'])) }}"
           style="padding:7px 16px; border-radius:7px; font-size:12.5px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px; transition:all 0.15s; {{ $tab == 'arsip' ? 'background:#334155; color:#fff; box-shadow:0 2px 6px rgba(51,65,85,0.3);' : 'color:var(--muted);' }}">
            <span class="ms" style="font-size:16px;">archive</span> Arsip Siswa Selesai
            <span class="badge" style="background:{{ $tab == 'arsip' ? '#fff' : '#E2E8F0' }}; color:{{ $tab == 'arsip' ? '#334155' : '#475569' }}; font-size:11px; padding:2px 7px;">{{ $stats['overall_archived'] }}</span>
        </a>
    </div>

    @if($selectedProgram)
        <div style="font-size:13px; color:var(--o800); font-weight:700; background:#FFF8E1; padding:6px 14px; border-radius:8px; border:1px solid #FFE082;">
            <span class="ms" style="font-size:16px; color:var(--gold); vertical-align:middle;">filter_alt</span>
            Menyaring Program: <u>{{ $selectedProgram->name }}</u> ({{ $selectedProgram->satdik->code }})
        </div>
    @endif
</div>

<!-- MAIN HEALTH DATA CARD -->
<div class="card">
    <div class="card-header" style="flex-wrap:wrap; gap:12px;">
        <h3 class="card-title">
            <span class="ms" style="color:var(--green);">monitor_heart</span>
            Daftar Rekam Medis & Kesehatan Serdik {{ $selectedSatdik ? '— ' . $selectedSatdik->name : 'Seluruh Satdik Rindam III/Slw' }}
        </h3>

        <!-- SEARCH & FILTER FORM -->
        <form method="GET" action="{{ route('health.index') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            @if($selectedSatdikId)
                <input type="hidden" name="satdik_id" value="{{ $selectedSatdikId }}">
            @endif
            @if($tab)
                <input type="hidden" name="tab" value="{{ $tab }}">
            @endif

            <!-- Filter Program Pendidikan -->
            <select name="program_id" class="form-control" style="width:200px;" onchange="this.form.submit()">
                <option value="">Semua Program Pendidikan</option>
                @foreach($availablePrograms as $prog)
                    <option value="{{ $prog->id }}" {{ $selectedProgramId == $prog->id ? 'selected' : '' }}>
                        {{ $prog->name }}
                    </option>
                @endforeach
            </select>

            <!-- Filter Kondisi Fisik -->
            <select name="health_status" class="form-control" style="width:180px;" onchange="this.form.submit()">
                <option value="">Semua Kondisi Fisik</option>
                <option value="Siap Latih" {{ $healthStatus == 'Siap Latih' ? 'selected' : '' }}>Siap Latih</option>
                <option value="Berobat Jalan" {{ $healthStatus == 'Berobat Jalan' ? 'selected' : '' }}>Berobat Jalan</option>
                <option value="Rawat Inap Poliklinik" {{ $healthStatus == 'Rawat Inap Poliklinik' ? 'selected' : '' }}>Rawat Inap</option>
                <option value="Rujuk Rumkit" {{ $healthStatus == 'Rujuk Rumkit' ? 'selected' : '' }}>Rujuk Rumkit</option>
            </select>

            <div style="position:relative;">
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari Siswa, NOSIK, Kodam/Kodim..." class="form-control" style="width:240px; padding-left:32px;">
                <span class="ms" style="position:absolute; left:8px; top:10px; color:var(--muted); font-size:18px;">search</span>
            </div>

            <button type="submit" class="btn btn-outline btn-sm">Filter</button>
            @if($keyword || $healthStatus || $selectedProgramId || $tab !== 'all')
                <a href="{{ route('health.index', ['satdik_id' => $selectedSatdikId]) }}" class="btn btn-sm" style="color:var(--muted);text-decoration:none;">Reset</a>
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
                        <th style="white-space:nowrap;">Satuan (Satdik & Peleton)</th>
                        <th style="min-width:170px;">Program Pendidikan</th>
                        <th style="min-width:160px;">Kodam / Kodim Asal</th>
                        <th style="white-space:nowrap;">Status Serdik</th>
                        <th style="white-space:nowrap; min-width:160px;">Kondisi Fisik / Kesiapan</th>
                        <th style="white-space:nowrap; min-width:120px;">Tanda Vital</th>
                        <th style="min-width:180px;">Catatan Medis & Alergi</th>
                        <th style="white-space:nowrap; text-align:right; min-width:120px;">Aksi Rekam Medis</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        @php $hr = $student->healthRecord; @endphp
                        <tr>
                            <td>{{ $students->firstItem() + $index }}</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:36px; height:36px; border-radius:50%; background:var(--o100); color:var(--o800); display:grid; place-items:center; font-weight:800; font-size:13px; border:1px solid var(--o200);">
                                        {{ substr($student->full_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <b style="color:var(--o800); display:block; font-size:13.5px;">{{ $student->full_name }}</b>
                                        <span class="badge badge-satdik" style="font-family:'Fira Code',monospace; font-size:11px; margin-top:2px;">
                                            {{ $student->nosik }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-satdik" style="background:#EFEFEA; font-weight:800;">
                                    <span class="ms" style="font-size:13px;">account_balance</span>
                                    {{ $student->satdik->code }}
                                </span>
                                <div style="font-size:11.5px; color:var(--muted); margin-top:2px;">
                                    {{ $student->classroom->name ?? 'Belum Ada Peleton' }}
                                </div>
                            </td>
                            <td>
                                <b style="font-size:13px; color:var(--text);">{{ $student->educationProgram->name ?? '-' }}</b>
                                <small style="display:block; color:var(--muted); font-size:11px;">
                                    TA {{ $student->educationProgram->fiscal_year ?? '2026' }}
                                </small>
                            </td>
                            <td>
                                <span style="font-size:13px; font-weight:600; color:var(--o900);">
                                    {{ $student->origin_military_unit ?? '-' }}
                                </span>
                            </td>
                            <td>
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
                            <td>
                                @php
                                    $b = $hr ? $hr->status_badge : ['class' => 'badge-green', 'icon' => 'check_circle', 'label' => 'Siap Latih'];
                                @endphp
                                <span class="badge {{ $b['class'] }}" style="font-size:12px; font-weight:700;">
                                    <span class="ms" style="font-size:13px;">{{ $b['icon'] }}</span> {{ $b['label'] }}
                                </span>
                                <div style="font-size:11px; color:var(--muted); margin-top:3px;">
                                    Gol. Darah: <b style="color:var(--red);">{{ $student->blood_type ?? '-' }}</b>
                                </div>
                            </td>
                            <td>
                                <div style="font-size:12px;">
                                    <span>Tensi: <b>{{ $hr->blood_pressure ?? '120/80' }}</b></span><br>
                                    <span style="color:var(--muted); font-size:11px;">TB: {{ $hr->height_cm ?? '-' }}cm | BB: {{ $hr->weight_kg ?? '-' }}kg</span>
                                </div>
                            </td>
                            <td style="max-width:240px;">
                                <div style="font-size:12px; color:var(--o800); line-height:1.3;">
                                    {{ $hr && $hr->allergies && $hr->allergies != 'Tidak ada riwayat alergi' ? '⚠️ ' . $hr->allergies : ($hr->doctor_notes ?? 'Kondisi fisik prima') }}
                                </div>
                            </td>
                            <td style="text-align:right; white-space:nowrap;">
                                <a href="{{ route('health.show', $student) }}" class="btn btn-outline btn-sm" title="Lihat Rekam Medis" style="color:var(--green); padding:5px 8px;">
                                    <span class="ms">visibility</span> Medis
                                </a>
                                <a href="{{ route('health.edit', $student) }}" class="btn btn-gold btn-sm" title="Perbarui Status Kesehatan" style="padding:5px 8px;">
                                    <span class="ms">edit_note</span> Update
                                </a>
                                <a href="{{ route('students.show', $student) }}" class="btn btn-outline btn-sm" title="Dossier Lengkap Siswa" style="padding:5px 8px;">
                                    <span class="ms">badge</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center; padding:45px 20px; color:var(--muted);">
                                <span class="ms" style="font-size:48px; color:var(--o200); display:block; margin-bottom:8px;">folder_off</span>
                                <b>Tidak ada data rekam medis serdik yang sesuai filter ini.</b>
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
                @if($selectedProgram)
                    • Program: <b>{{ $selectedProgram->name }}</b>
                @endif
            </div>
            <div>
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
