@extends('layouts.app')

@section('title', 'Program Pendidikan per Satdik — SIPANDU-WBK')

@section('content')

<!-- HEADER BANNER -->
<div class="header-banner">
    <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <span class="pdp-badge" style="background:var(--gold-bg); color:#7A5C07;">
                <span class="ms" style="font-size:16px;">school</span> PROGRAM PENDIDIKAN RESMI RINDAM III/SILIWANGI
            </span>
            <span class="pdp-badge" style="background:rgba(255,255,255,0.15);color:#fff;border-color:rgba(255,255,255,0.25);">
                <span class="ms" style="font-size:16px;">calendar_today</span> TAHUN ANGGARAN 2026
            </span>
        </div>
        <h1>Pengelolaan Program Pendidikan per Satdik</h1>
        <p>
            Menu tersendiri untuk pembinaan dan monitoring kurikulum program pendidikan di seluruh Satdik Rindam III/Siliwangi (Secaba, Secata, Dodikjur, Dodiklatpur, Dodik Bela Negara).
            Hanya prajurit siswa berstatus <b>Aktif</b> yang terhitung dalam kekuatan program, sedangkan status <b>Selesai</b> otomatis masuk ke <b>Arsip</b>.
        </p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        @if(auth()->user()?->canModifyData())
            <button type="button" class="btn btn-gold" onclick="openCreateProgramModal()">
                <span class="ms">add_circle</span> Tambah Program Pendidikan
            </button>
        @endif
        <a href="{{ route('students.index') }}" class="btn btn-outline" style="background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.3);">
            <span class="ms">groups</span> Buka Buku Induk Serdik
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="background:#E8F5E9; border-color:#C8E6C9; color:#2E7D32; margin-bottom:20px;">
        <span class="ms" style="font-size:24px;">check_circle</span>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-warning" style="background:#FFEBEE; border-color:#FFCDD2; color:#C62828; margin-bottom:20px;">
        <span class="ms" style="font-size:24px;">error</span>
        <div>
            <b>Terjadi Kesalahan:</b>
            <ul style="margin:4px 0 0 16px; padding:0; font-size:13px;">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!-- STATS STRATEGIS MAKRO PROGRAM -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--o800), var(--o600));">
            <span class="ms">layers</span>
        </div>
        <div>
            <div class="stat-val">{{ $totalPrograms }}</div>
            <div class="stat-lbl">Total Program Terdaftar</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                <b>{{ $activePrograms }}</b> Berjalan Aktif · TA 2026
            </small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--green), #2E7D32);">
            <span class="ms">how_to_reg</span>
        </div>
        <div>
            <div class="stat-val" style="color:var(--green);">{{ $totalCountedStudents }}</div>
            <div class="stat-lbl">Siswa Aktif Terhitung</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                Kekuatan Operasional Pendidikan
            </small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, #475569, #1E293B);">
            <span class="ms">archive</span>
        </div>
        <div>
            <div class="stat-val" style="color:#475569;">{{ $totalArchivedStudents }}</div>
            <div class="stat-lbl">Arsip Siswa Selesai</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                Selesai Pendidikan (Tidak Terhitung Lagi)
            </small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, #1565C0, #1E88E5);">
            <span class="ms">domain</span>
        </div>
        <div>
            <div class="stat-val">{{ auth()->check() && !auth()->user()->isPimpinan() && auth()->user()->satdik_id ? 1 : count($satdiks) }}</div>
            <div class="stat-lbl">Satdik Penyelenggara</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                {{ auth()->check() && !auth()->user()->isPimpinan() && auth()->user()->satdik_id ? (auth()->user()->satdik?->code ?? 'Satuan Anda') : 'Secaba, Secata, Dodikjur, Latpur, Belneg' }}
            </small>
        </div>
    </div>
</div>

<!-- SATDIK NAVIGATION TABS -->
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
        <a href="{{ route('programs.index', array_merge(request()->except(['satdik_id']))) }}" class="satdik-tab {{ empty($selectedSatdikId) ? 'active' : '' }}">
            <span class="ms">domain</span> Semua Satdik
            <span class="tab-badge">{{ $totalPrograms }} Program</span>
        </a>

        @foreach($satdiks as $satdik)
            @php
                $pCount = $satdik->educationPrograms()->count();
            @endphp
            <a href="{{ route('programs.index', array_merge(request()->except(['page']), ['satdik_id' => $satdik->id])) }}" class="satdik-tab {{ $selectedSatdikId == $satdik->id ? 'active' : '' }}">
                <span class="ms">military_tech</span> {{ $satdik->code }}
                <span class="tab-badge">{{ $pCount }} Program</span>
            </a>
        @endforeach
    </div>
@endif

<!-- FILTER & SEARCH BAR -->
<div class="card" style="margin-bottom:20px; padding:16px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="ms" style="color:var(--o800); font-size:22px;">school</span>
            <b style="font-size:15px; color:var(--o900);">
                Daftar Program Pendidikan {{ $selectedSatdik ? '— ' . $selectedSatdik->name : 'Seluruh Satdik Rindam III/Siliwangi' }}
            </b>
            <span class="badge badge-satdik">{{ count($programs) }} Program</span>
        </div>

        <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <!-- VIEW SWITCHER: TABEL vs KARTU -->
            <div style="display:flex; background:#EAECE7; padding:3px; border-radius:8px; gap:2px;">
                <button type="button" id="btnViewTable" onclick="switchProgramView('table')" style="border:none; background:#fff; color:var(--o900); padding:5px 12px; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px; box-shadow:0 1px 3px rgba(0,0,0,0.1); transition:all 0.15s;">
                    <span class="ms" style="font-size:16px;">table_rows</span> Tabel
                </button>
                <button type="button" id="btnViewCard" onclick="switchProgramView('card')" style="border:none; background:transparent; color:var(--muted); padding:5px 12px; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px; transition:all 0.15s;">
                    <span class="ms" style="font-size:16px;">grid_view</span> Kartu
                </button>
            </div>

            <form method="GET" action="{{ route('programs.index') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                @if($selectedSatdikId)
                    <input type="hidden" name="satdik_id" value="{{ $selectedSatdikId }}">
                @endif

                <select name="status" class="form-control" style="width:150px; font-size:12.5px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Berjalan" {{ $statusFilter == 'Berjalan' ? 'selected' : '' }}>Berjalan (Aktif)</option>
                    <option value="Perencanaan" {{ $statusFilter == 'Perencanaan' ? 'selected' : '' }}>Perencanaan</option>
                    <option value="Selesai" {{ $statusFilter == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Ditutup" {{ $statusFilter == 'Ditutup' ? 'selected' : '' }}>Ditutup</option>
                </select>

                <div style="position:relative;">
                    <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari Nama Program, Kode, TA..." class="form-control" style="width:220px; font-size:12.5px; padding-left:30px;">
                    <span class="ms" style="position:absolute; left:7px; top:8px; color:var(--muted); font-size:18px;">search</span>
                </div>

                <button type="submit" class="btn btn-outline btn-sm">Filter</button>
                @if($keyword || $statusFilter)
                    <a href="{{ route('programs.index', ['satdik_id' => $selectedSatdikId]) }}" class="btn btn-sm" style="color:var(--muted); text-decoration:none;">Reset</a>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- =====================================================================
     1. TAMPILAN TABEL RESMI PROGRAM (DENGAN LEBAR KOLOM FIX & SESUAI)
     ===================================================================== -->
<div class="card" id="programTableView" style="margin-bottom:32px;">
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="data-table" style="table-layout:fixed; width:100%; min-width:1150px;">
                <thead>
                    <tr>
                        <th style="width:50px; text-align:center;">No</th>
                        <th style="width:110px;">Satdik</th>
                        <th style="width:250px;">Nama Program Pendidikan</th>
                        <th style="width:130px;">Kode Program</th>
                        <th style="width:130px;">TA & Gelombang</th>
                        <th style="width:140px; text-align:center;">Siswa Aktif</th>
                        <th style="width:130px; text-align:center;">Arsip Selesai</th>
                        <th style="width:130px;">Peleton</th>
                        <th style="width:120px; text-align:center;">Status</th>
                        <th style="width:130px; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $idx => $p)
                        <tr>
                            <td style="text-align:center; font-weight:700;">{{ $idx + 1 }}</td>
                            <td>
                                <span class="badge badge-satdik" style="background:var(--o800); color:var(--gold2); font-weight:800; font-size:11.5px;">
                                    {{ $p['satdik_code'] }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('programs.show', $p['id']) }}" style="text-decoration:none; color:var(--o900); font-weight:800; font-size:13.5px; display:block;">
                                    {{ $p['name'] }}
                                </a>
                                <small style="color:var(--muted); font-size:11.5px; display:flex; align-items:center; gap:4px; margin-top:2px;">
                                    <span class="ms" style="font-size:13px; color:var(--gold);">location_on</span> {{ $p['location'] }}
                                </small>
                            </td>
                            <td>
                                <span class="badge" style="font-family:'Fira Code',monospace; font-size:11px; background:#ECEEE9; color:var(--o800);">
                                    {{ $p['code'] }}
                                </span>
                            </td>
                            <td>
                                <b style="font-size:13px;">TA {{ $p['academic_year'] }}</b>
                                <small style="display:block; color:var(--muted); font-size:11px;">Gel. {{ $p['batch_number'] }}</small>
                            </td>
                            <td style="text-align:center;">
                                <span class="badge badge-green" style="font-size:12px; font-weight:800; padding:4px 10px;" title="Terhitung dalam kekuatan aktif">
                                    {{ $p['counted_students'] }} Serdik
                                </span>
                                <small style="display:block; color:var(--muted); font-size:10.5px; margin-top:2px;">
                                    {{ $p['ready_students'] }} Siap · {{ $p['sick_students'] }} Sakit
                                </small>
                            </td>
                            <td style="text-align:center;">
                                <span class="badge" style="background:#E2E8F0; color:#334155; font-size:12px; font-weight:800; padding:4px 10px;" title="Alumni selesai pendidikan (tidak terhitung)">
                                    {{ $p['archived_students'] }} Serdik
                                </span>
                                <small style="display:block; color:var(--muted); font-size:10.5px; margin-top:2px;">
                                    Lulus / Tamat
                                </small>
                            </td>
                            <td>
                                <span style="font-weight:700; font-size:12.5px;">{{ $p['classrooms']->count() }} Peleton</span>
                                <small style="display:block; color:var(--muted); font-size:11px;">
                                    Kap: {{ $p['classrooms']->sum('capacity') }}
                                </small>
                            </td>
                            <td style="text-align:center;">
                                @if($p['status'] == 'Berjalan')
                                    <span class="badge badge-green" style="font-size:11px;">
                                        <span class="ms" style="font-size:12px;">play_arrow</span> Berjalan
                                    </span>
                                @elseif($p['status'] == 'Perencanaan')
                                    <span class="badge badge-amber" style="font-size:11px;">
                                        <span class="ms" style="font-size:12px;">schedule</span> Rencana
                                    </span>
                                @elseif($p['status'] == 'Selesai')
                                    <span class="badge" style="background:#E2E8F0; color:#334155; font-size:11px;">
                                        <span class="ms" style="font-size:12px;">check</span> Selesai
                                    </span>
                                @else
                                    <span class="badge badge-red" style="font-size:11px;">{{ $p['status'] }}</span>
                                @endif
                            </td>
                            <td style="text-align:right; white-space:nowrap;">
                                <a href="{{ route('students.index', ['program_id' => $p['id']]) }}" class="btn btn-gold btn-sm" style="padding:4px 8px;" title="Lihat Siswa Program">
                                    <span class="ms" style="font-size:16px;">groups</span>
                                </a>
                                <a href="{{ route('programs.show', $p['id']) }}" class="btn btn-outline btn-sm" style="padding:4px 8px;" title="Detail & Peleton">
                                    <span class="ms" style="font-size:16px;">visibility</span>
                                </a>
                                @if(auth()->user()?->canModifyData())
                                    <button type="button" class="btn btn-outline btn-sm" onclick="openEditProgramModal({{ json_encode($p['model']) }})" style="padding:4px 8px;" title="Edit Data Program">
                                        <span class="ms" style="font-size:16px;">edit</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center; padding:45px 20px; color:var(--muted);">
                                <span class="ms" style="font-size:42px; color:var(--o200); display:block; margin-bottom:8px;">school</span>
                                <b>Tidak Ada Program Pendidikan Ditemukan</b>
                                <div style="font-size:12.5px; margin-top:4px;">Coba ganti filter Satdik atau kata kunci pencarian.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- =====================================================================
     2. TAMPILAN KARTU GRID PROGRAM (OPSIONAL DAPAT DIBUKA LEWAT TOGGLE)
     ===================================================================== -->
<div id="programCardView" style="display:none; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap:22px; margin-bottom:32px;">
    @forelse($programs as $p)
        <div class="card" style="margin-bottom:0; display:flex; flex-direction:column; border-top:4px solid {{ $p['status'] == 'Berjalan' ? 'var(--gold)' : '#94A3B8' }}; box-shadow:var(--shadow-sm); transition:all 0.2s ease;">
            <!-- CARD HEADER -->
            <div class="card-header" style="background:#FAFBF9; border-bottom:1px solid var(--line); padding:16px 20px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="badge badge-satdik" style="background:var(--o800); color:var(--gold2); font-weight:800; font-size:12px;">
                        {{ $p['satdik_code'] }}
                    </span>
                    <span class="badge" style="font-family:'Fira Code',monospace; font-size:11px; background:#ECEEE9; color:var(--o800);">
                        {{ $p['code'] }}
                    </span>
                </div>
                <div>
                    @if($p['status'] == 'Berjalan')
                        <span class="badge badge-green" style="font-size:11.5px;">
                            <span class="ms" style="font-size:13px;">play_arrow</span> Berjalan
                        </span>
                    @elseif($p['status'] == 'Perencanaan')
                        <span class="badge badge-amber" style="font-size:11.5px;">
                            <span class="ms" style="font-size:13px;">schedule</span> Perencanaan
                        </span>
                    @elseif($p['status'] == 'Selesai')
                        <span class="badge" style="background:#E2E8F0; color:#334155; font-size:11.5px;">
                            <span class="ms" style="font-size:13px;">check</span> Selesai
                        </span>
                    @else
                        <span class="badge badge-red" style="font-size:11.5px;">
                            <span class="ms" style="font-size:13px;">block</span> Ditutup
                        </span>
                    @endif
                </div>
            </div>

            <!-- CARD BODY -->
            <div class="card-body" style="padding:20px; flex:1; display:flex; flex-direction:column;">
                <h3 style="margin:0 0 6px; font-family:'Montserrat',sans-serif; font-size:18px; font-weight:800; color:var(--o900);">
                    {{ $p['name'] }}
                </h3>
                
                <div style="font-size:12.5px; color:var(--muted); margin-bottom:16px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <span><span class="ms" style="font-size:14px; vertical-align:text-top;">location_on</span> {{ $p['location'] }}</span>
                    <span>•</span>
                    <span>Tahun Anggaran: <b>TA {{ $p['academic_year'] }}</b></span>
                    <span>•</span>
                    <span>Gelombang: <b>{{ $p['batch_number'] }}</b></span>
                </div>

                <!-- BOX KEKUATAN SISWA -->
                <div style="background:#F7F9F5; border:1px solid #E2E8DC; border-radius:10px; padding:14px 16px; margin-bottom:16px;">
                    <div style="font-size:11px; font-weight:800; color:var(--o700); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:10px; display:flex; justify-content:space-between;">
                        <span>Status Kekuatan Peserta Didik</span>
                        <span style="color:var(--muted);">Total: {{ $p['total_students'] }} Serdik</span>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                        <div style="background:#fff; border:1px solid #C8E6C9; border-radius:8px; padding:10px; border-left:4px solid var(--green);">
                            <div style="font-size:11px; color:var(--muted); font-weight:600;">Siswa Aktif Terhitung</div>
                            <div style="font-size:20px; font-weight:900; color:var(--green); margin-top:2px;">
                                {{ $p['counted_students'] }}
                            </div>
                            <small style="color:var(--muted); font-size:10.5px; display:block; margin-top:2px;">
                                {{ $p['ready_students'] }} Siap · {{ $p['sick_students'] }} Sakit
                            </small>
                        </div>

                        <div style="background:#fff; border:1px solid #CBD5E1; border-radius:8px; padding:10px; border-left:4px solid #475569;">
                            <div style="font-size:11px; color:var(--muted); font-weight:600;">Arsip Selesai (Alumni)</div>
                            <div style="font-size:20px; font-weight:900; color:#475569; margin-top:2px;">
                                {{ $p['archived_students'] }}
                            </div>
                            <small style="color:var(--muted); font-size:10.5px; display:block; margin-top:2px;">
                                {{ $p['finished_students'] }} Tamat Pendidikan
                            </small>
                        </div>
                    </div>
                </div>

                <!-- DAFTAR ROMBONGAN BELAJAR / PELETON -->
                <div style="margin-bottom:18px; flex:1;">
                    <div style="font-size:12px; font-weight:700; color:var(--o800); margin-bottom:6px; display:flex; align-items:center; gap:6px;">
                        <span class="ms" style="font-size:16px;">groups_2</span>
                        Peleton & Rombel ({{ $p['classrooms']->count() }} Kelas):
                    </div>
                    @if($p['classrooms']->count() > 0)
                        <div style="display:flex; flex-wrap:wrap; gap:6px;">
                            @foreach($p['classrooms'] as $cls)
                                <span class="badge" style="background:#fff; border:1px solid var(--line); color:var(--text); font-size:11.5px; padding:4px 8px;">
                                    {{ $cls->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div style="font-size:12px; color:var(--muted); font-style:italic;">
                            Belum ada peleton yang dialokasikan.
                        </div>
                    @endif
                </div>

                <!-- ACTION BUTTONS -->
                <div style="display:flex; gap:8px; padding-top:14px; border-top:1px solid var(--line); margin-top:auto;">
                    <a href="{{ route('students.index', ['program_id' => $p['id']]) }}" class="btn btn-gold btn-sm" style="flex:1; justify-content:center; font-size:12.5px;">
                        <span class="ms">groups</span> Lihat Siswa
                    </a>
                    <a href="{{ route('programs.show', $p['id']) }}" class="btn btn-outline btn-sm" style="padding:6px 12px; font-size:12.5px;" title="Detail & Kelola Program">
                        <span class="ms">visibility</span> Detail
                    </a>
                    @if(auth()->user()?->canModifyData())
                        <button type="button" class="btn btn-outline btn-sm" onclick="openEditProgramModal({{ json_encode($p['model']) }})" style="padding:6px 10px;" title="Edit Data Program">
                            <span class="ms">edit</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; background:#fff; border:1px solid var(--line); border-radius:12px; padding:50px 20px; text-align:center;">
            <span class="ms" style="font-size:52px; color:var(--o200); display:block; margin-bottom:10px;">school</span>
            <b style="font-size:16px; color:var(--o800);">Tidak Ada Program Pendidikan Ditemukan</b>
            <p style="color:var(--muted); font-size:13px; max-width:480px; margin:6px auto 16px;">
                Belum ada program pendidikan yang terdaftar untuk filter Satdik atau kata kunci pencarian ini.
            </p>
            <button type="button" class="btn btn-gold" onclick="openCreateProgramModal()">
                <span class="ms">add_circle</span> Tambah Program Baru
            </button>
        </div>
    @endforelse
</div>

<!-- =====================================================================
     MODAL TAMBAH PROGRAM PENDIDIKAN BARU
     ===================================================================== -->
<div class="modal-overlay" id="createProgramModal">
    <div class="modal-card" style="max-width:540px;">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:var(--gold2); font-size:22px;">add_circle</span>
                <b style="font-family:'Montserrat',sans-serif; font-size:15px;">Tambah Program Pendidikan Satdik Baru</b>
            </div>
            <button type="button" onclick="closeCreateProgramModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:20px;">&times;</button>
        </div>

        <form method="POST" action="{{ route('programs.store') }}">
            @csrf
            <div class="modal-body" style="padding:22px;">
                <div class="form-group">
                    <label class="form-label">Satuan Pendidikan (Satdik): <span style="color:var(--red);">*</span></label>
                    <select name="satdik_id" class="form-control" required>
                        @foreach($satdiks as $s)
                            <option value="{{ $s->id }}" {{ $selectedSatdikId == $s->id ? 'selected' : '' }}>
                                {{ $s->code }} — {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Kode Program: <span style="color:var(--red);">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="Contoh: DIKMABA-2026-II" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tahun Anggaran (TA): <span style="color:var(--red);">*</span></label>
                        <input type="text" name="academic_year" class="form-control" value="2026" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Resmi Program Pendidikan: <span style="color:var(--red);">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: DIKMABA TA 2026 Gel II" required>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Gelombang / Batch:</label>
                        <input type="number" name="batch_number" class="form-control" value="1" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status Program: <span style="color:var(--red);">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="Berjalan" selected>Berjalan (Aktif)</option>
                            <option value="Perencanaan">Perencanaan</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Ditutup">Ditutup</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Tanggal Mulai:</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Selesai:</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Peleton / Rombongan Belajar Awal (Opsional):</label>
                    <input type="text" name="initial_classrooms" class="form-control" placeholder="Contoh: Kompi A Peleton 1, Kompi A Peleton 2">
                    <small style="color:var(--muted); font-size:11px;">Pisahkan dengan tanda koma untuk membuat beberapa peleton sekaligus.</small>
                </div>
            </div>

            <div style="padding:14px 22px; background:#FAFBF9; border-top:1px solid var(--line); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeCreateProgramModal()">Batal</button>
                <button type="submit" class="btn btn-gold">
                    <span class="ms">save</span> Simpan Program Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL EDIT PROGRAM PENDIDIKAN
     ===================================================================== -->
<div class="modal-overlay" id="editProgramModal">
    <div class="modal-card" style="max-width:540px;">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:var(--gold2); font-size:22px;">edit</span>
                <b style="font-family:'Montserrat',sans-serif; font-size:15px;">Edit Program Pendidikan Satdik</b>
            </div>
            <button type="button" onclick="closeEditProgramModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:20px;">&times;</button>
        </div>

        <form id="editProgramForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="modal-body" style="padding:22px;">
                <div class="form-group">
                    <label class="form-label">Satuan Pendidikan (Satdik): <span style="color:var(--red);">*</span></label>
                    <select name="satdik_id" id="editSatdikId" class="form-control" required>
                        @foreach($satdiks as $s)
                            <option value="{{ $s->id }}">{{ $s->code }} — {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Kode Program: <span style="color:var(--red);">*</span></label>
                        <input type="text" name="code" id="editCode" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tahun Anggaran (TA): <span style="color:var(--red);">*</span></label>
                        <input type="text" name="academic_year" id="editYear" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Resmi Program: <span style="color:var(--red);">*</span></label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Gelombang / Batch:</label>
                        <input type="number" name="batch_number" id="editBatch" class="form-control" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status Program: <span style="color:var(--red);">*</span></label>
                        <select name="status" id="editStatus" class="form-control" required>
                            <option value="Berjalan">Berjalan (Aktif)</option>
                            <option value="Perencanaan">Perencanaan</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Ditutup">Ditutup</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Tanggal Mulai:</label>
                        <input type="date" name="start_date" id="editStartDate" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Selesai:</label>
                        <input type="date" name="end_date" id="editEndDate" class="form-control">
                    </div>
                </div>
            </div>

            <div style="padding:14px 22px; background:#FAFBF9; border-top:1px solid var(--line); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeEditProgramModal()">Batal</button>
                <button type="submit" class="btn btn-gold">
                    <span class="ms">save</span> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
const PROGRAM_VIEW_KEY = 'sipandu_program_view_mode';

function switchProgramView(mode) {
    const tblView = document.getElementById('programTableView');
    const crdView = document.getElementById('programCardView');
    const btnTbl = document.getElementById('btnViewTable');
    const btnCrd = document.getElementById('btnViewCard');

    if (!tblView || !crdView) return;

    if (mode === 'card') {
        tblView.style.display = 'none';
        crdView.style.display = 'grid';
        if (btnTbl) {
            btnTbl.style.background = 'transparent';
            btnTbl.style.color = 'var(--muted)';
            btnTbl.style.boxShadow = 'none';
        }
        if (btnCrd) {
            btnCrd.style.background = '#fff';
            btnCrd.style.color = 'var(--o900)';
            btnCrd.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
        }
        localStorage.setItem(PROGRAM_VIEW_KEY, 'card');
    } else {
        tblView.style.display = 'block';
        crdView.style.display = 'none';
        if (btnTbl) {
            btnTbl.style.background = '#fff';
            btnTbl.style.color = 'var(--o900)';
            btnTbl.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
        }
        if (btnCrd) {
            btnCrd.style.background = 'transparent';
            btnCrd.style.color = 'var(--muted)';
            btnCrd.style.boxShadow = 'none';
        }
        localStorage.setItem(PROGRAM_VIEW_KEY, 'table');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem(PROGRAM_VIEW_KEY) || 'table';
    switchProgramView(saved);
});

function openCreateProgramModal() {
    document.getElementById('createProgramModal').classList.add('active');
}
function closeCreateProgramModal() {
    document.getElementById('createProgramModal').classList.remove('active');
}

function openEditProgramModal(prog) {
    const form = document.getElementById('editProgramForm');
    form.action = `/programs/${prog.id}`;
    document.getElementById('editSatdikId').value = prog.satdik_id;
    document.getElementById('editCode').value = prog.code;
    document.getElementById('editName').value = prog.name;
    document.getElementById('editYear').value = prog.academic_year;
    document.getElementById('editBatch').value = prog.batch_number || 1;
    document.getElementById('editStatus').value = prog.status || 'Berjalan';
    document.getElementById('editStartDate').value = prog.start_date ? prog.start_date.split('T')[0] : '';
    document.getElementById('editEndDate').value = prog.end_date ? prog.end_date.split('T')[0] : '';

    document.getElementById('editProgramModal').classList.add('active');
}
function closeEditProgramModal() {
    document.getElementById('editProgramModal').classList.remove('active');
}
</script>
@endsection
