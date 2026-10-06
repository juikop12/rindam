@extends('layouts.app')

@section('title', $program->name . ' — SIPANDU-WBK')

@section('content')

<!-- HEADER BANNER -->
<div class="header-banner">
    <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <a href="{{ route('programs.index') }}" style="color:var(--gold2); text-decoration:none; font-size:12px; font-weight:700; display:flex; align-items:center; gap:4px;">
                <span class="ms" style="font-size:16px;">arrow_back</span> Kembali ke Menu Program
            </a>
            <span class="pdp-badge" style="background:var(--gold-bg); color:#7A5C07;">
                {{ $program->satdik->code }}
            </span>
            <span class="pdp-badge" style="background:rgba(255,255,255,0.15);color:#fff;border-color:rgba(255,255,255,0.25);">
                TA {{ $program->academic_year }}
            </span>
        </div>
        <h1>{{ $program->name }}</h1>
        <p>
            Satuan Pendidikan: <b>{{ $program->satdik->name }}</b> ({{ $program->satdik->location ?? 'Ksatrian Rindam' }}). 
            Kode Program: <code>{{ $program->code }}</code> · Status: <b>{{ $program->status }}</b>.
        </p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('students.index', ['satdik_id' => $program->satdik_id, 'program_id' => $program->id, 'action' => 'create']) }}" class="btn btn-gold">
            <span class="ms">person_add</span> Tambah Siswa ke Program
        </a>
        <a href="{{ route('students.index', ['program_id' => $program->id]) }}" class="btn btn-outline" style="background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.3);">
            <span class="ms">groups</span> Buka di Buku Induk
        </a>
    </div>
</div>

<!-- STATS PROGRAM INI: AKTIF TERHITUNG VS ARSIP SELESAI -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--green), #2E7D32);">
            <span class="ms">how_to_reg</span>
        </div>
        <div>
            <div class="stat-val" style="color:var(--green);">{{ $countedCount }}</div>
            <div class="stat-lbl">Siswa Aktif Terhitung</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                {{ $readyCount }} Siap Latih · {{ $sickCount }} Sakit
            </small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, #475569, #1E293B);">
            <span class="ms">archive</span>
        </div>
        <div>
            <div class="stat-val" style="color:#475569;">{{ $archivedCount }}</div>
            <div class="stat-lbl">Arsip Siswa Selesai</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                {{ $finishedCount }} Tamat / Lulus (Tidak Terhitung)
            </small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--o800), var(--o600));">
            <span class="ms">groups_2</span>
        </div>
        <div>
            <div class="stat-val">{{ $program->classrooms->count() }}</div>
            <div class="stat-lbl">Peleton / Rombel</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                Kapasitas: {{ $program->classrooms->sum('capacity') }} Serdik
            </small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, #1565C0, #1E88E5);">
            <span class="ms">school</span>
        </div>
        <div>
            <div class="stat-val">{{ $program->students->count() }}</div>
            <div class="stat-lbl">Total Rekam Serdik</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                Aktif + Arsip Selesai
            </small>
        </div>
    </div>
</div>

<!-- PELETON & INFORMASI PROGRAM -->
<div style="display:grid; grid-template-columns: 1fr 2fr; gap:20px; margin-bottom:24px;">
    <!-- KARTU 1: DETAIL KURIKULUM & JADWAL -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <h3 class="card-title">
                <span class="ms" style="color:var(--o700);">info</span> Data Program Pendidikan
            </h3>
            <span class="badge badge-satdik">{{ $program->status }}</span>
        </div>
        <div class="card-body">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:8px 0; color:var(--muted); width:40%;">Satuan Pendidikan</td>
                    <td style="padding:8px 0; font-weight:700;">{{ $program->satdik->name }}</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:8px 0; color:var(--muted);">Kode Program</td>
                    <td style="padding:8px 0; font-family:'Fira Code'; font-weight:700; color:var(--o800);">{{ $program->code }}</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:8px 0; color:var(--muted);">Tahun Anggaran</td>
                    <td style="padding:8px 0; font-weight:600;">TA {{ $program->academic_year }} (Gelombang {{ $program->batch_number }})</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:8px 0; color:var(--muted);">Tanggal Mulai</td>
                    <td style="padding:8px 0;">{{ $program->start_date ? $program->start_date->format('d M Y') : '-' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:8px 0; color:var(--muted);">Tanggal Selesai</td>
                    <td style="padding:8px 0;">{{ $program->end_date ? $program->end_date->format('d M Y') : '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:var(--muted);">Status Keaktifan</td>
                    <td style="padding:8px 0;">
                        @if($program->status == 'Berjalan')
                            <span class="badge badge-green">Berjalan (Aktif)</span>
                        @elseif($program->status == 'Selesai')
                            <span class="badge" style="background:#E2E8F0; color:#334155;">Selesai (Arsip)</span>
                        @else
                            <span class="badge badge-amber">{{ $program->status }}</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- KARTU 2: ROMBONGAN BELAJAR / KOMPI & PELETON -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <h3 class="card-title" style="margin:0;">
                    <span class="ms" style="color:var(--o700);">groups_2</span> Kompi & Peleton Siswa
                </h3>
                <span class="badge badge-satdik">{{ $program->classrooms->count() }} Peleton</span>
            </div>
            <button type="button" class="btn btn-gold btn-sm" onclick="openCreateClassroomModal()" style="display:inline-flex; align-items:center; gap:6px;">
                <span class="ms" style="font-size:16px;">add_circle</span> Buat Kompi & Peleton
            </button>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="data-table" style="table-layout:fixed; width:100%; min-width:640px;">
                    <thead>
                        <tr>
                            <th style="width:25%;">Kompi & Peleton</th>
                            <th style="width:16%;">Kode</th>
                            <th style="width:23%;">Danton / Komandan</th>
                            <th style="width:20%; text-align:center;">Kapasitas & Kuota</th>
                            <th style="width:16%; text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($program->classrooms as $cls)
                            @php
                                $stuCount = $cls->students()->count();
                                $pct = $cls->capacity > 0 ? min(100, round(($stuCount / $cls->capacity) * 100)) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight:700; color:var(--o900); font-size:13.5px;">
                                        {{ $cls->name }}
                                    </div>
                                    <div style="font-size:11px; color:var(--muted); margin-top:2px;">
                                        <span class="badge" style="background:#EFEFEA; font-size:10.5px; padding:2px 6px;">
                                            {{ $cls->company ?? 'Kompi' }}
                                        </span>
                                        @if($cls->platoon)
                                            <span class="badge" style="background:#E8EAE6; font-size:10.5px; padding:2px 6px; margin-left:3px;">
                                                {{ $cls->platoon }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td><code style="font-size:11px;">{{ $cls->code }}</code></td>
                                <td>
                                    <div style="font-size:12.5px; font-weight:600; color:var(--o800);">
                                        {{ $cls->platoon_leader_name ?: '-' }}
                                    </div>
                                    <small style="color:var(--muted); font-size:11px;">Danton / Danki</small>
                                </td>
                                <td>
                                    <div style="display:flex; justify-content:space-between; font-size:11.5px; margin-bottom:3px;">
                                        <b>{{ $stuCount }} Serdik</b>
                                        <span style="color:var(--muted);">Maks {{ $cls->capacity }}</span>
                                    </div>
                                    <div style="width:100%; height:5px; background:#E5E7EB; border-radius:999px; overflow:hidden;">
                                        <div style="width:{{ $pct }}%; height:100%; background:{{ $pct >= 100 ? 'var(--red)' : ($pct >= 80 ? 'var(--gold)' : 'var(--green)') }}; border-radius:999px;"></div>
                                    </div>
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:inline-flex; gap:4px;">
                                        <button type="button" class="btn btn-outline btn-sm" onclick='openEditClassroomModal(@json($cls))' style="padding:4px 8px;" title="Edit Kompi & Peleton">
                                            <span class="ms" style="font-size:15px;">edit</span>
                                        </button>
                                        @if($stuCount == 0)
                                            <form method="POST" action="{{ route('classrooms.destroy', $cls->id) }}" onsubmit="return confirm('Hapus rombel/peleton [{{ $cls->name }}]?')" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" style="color:var(--red); padding:4px 8px;" title="Hapus Peleton">
                                                    <span class="ms" style="font-size:15px;">delete</span>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-outline btn-sm" style="color:#A0AEC0; cursor:not-allowed; padding:4px 8px;" title="Tidak dapat dihapus karena menaungi {{ $stuCount }} siswa" disabled>
                                                <span class="ms" style="font-size:15px;">delete</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding:28px; color:var(--muted);">
                                    <span class="ms" style="font-size:32px; color:var(--o200); display:block; margin-bottom:4px;">groups_2</span>
                                    Belum ada Kompi / Peleton yang dibuat untuk program ini.
                                    <div style="margin-top:10px;">
                                        <button type="button" class="btn btn-gold btn-sm" onclick="openCreateClassroomModal()">
                                            <span class="ms">add_circle</span> Buat Kompi & Peleton Pertama
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SEGMENTED TABS: SISWA DI PROGRAM INI -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; gap:12px; flex-wrap:wrap;">
    <div style="display:flex; background:#EAECE7; padding:4px; border-radius:10px; gap:4px;">
        <a href="{{ route('programs.show', array_merge(['program' => $program->id], request()->except(['page', 'tab']), ['tab' => 'all'])) }}"
           style="padding:7px 16px; border-radius:7px; font-size:12.5px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px; transition:all 0.15s; {{ $tab == 'all' ? 'background:#fff; color:var(--o900); box-shadow:0 1px 3px rgba(0,0,0,0.1);' : 'color:var(--muted);' }}">
            <span class="ms" style="font-size:16px;">people</span> Semua Serdik Program
            <span class="badge" style="background:#E2E8F0; color:#334155; font-size:11px; padding:2px 7px;">{{ $program->students->count() }}</span>
        </a>
        <a href="{{ route('programs.show', array_merge(['program' => $program->id], request()->except(['page', 'tab']), ['tab' => 'aktif'])) }}"
           style="padding:7px 16px; border-radius:7px; font-size:12.5px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px; transition:all 0.15s; {{ $tab == 'aktif' ? 'background:#fff; color:var(--green); box-shadow:0 1px 3px rgba(0,0,0,0.1);' : 'color:var(--muted);' }}">
            <span class="ms" style="font-size:16px; color:var(--green);">how_to_reg</span> Siswa Aktif (Terhitung)
            <span class="badge badge-green" style="font-size:11px; padding:2px 7px;">{{ $countedCount }}</span>
        </a>
        <a href="{{ route('programs.show', array_merge(['program' => $program->id], request()->except(['page', 'tab']), ['tab' => 'arsip'])) }}"
           style="padding:7px 16px; border-radius:7px; font-size:12.5px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px; transition:all 0.15s; {{ $tab == 'arsip' ? 'background:#fff; color:#475569; box-shadow:0 1px 3px rgba(0,0,0,0.1);' : 'color:var(--muted);' }}">
            <span class="ms" style="font-size:16px; color:#475569;">archive</span> Arsip Siswa Selesai (Tidak Terhitung)
            <span class="badge" style="background:#CBD5E1; color:#1E293B; font-size:11px; padding:2px 7px;">{{ $archivedCount }}</span>
        </a>
    </div>

    <!-- FILTER PELETON -->
    <form method="GET" action="{{ route('programs.show', $program->id) }}" style="display:flex; gap:8px; align-items:center;">
        @if($tab && $tab !== 'all')
            <input type="hidden" name="tab" value="{{ $tab }}">
        @endif
        <select name="classroom_id" class="form-control" style="width:180px; font-size:12.5px;" onchange="this.form.submit()">
            <option value="">Semua Peleton</option>
            @foreach($program->classrooms as $c)
                <option value="{{ $c->id }}" {{ $classroomId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <div style="position:relative;">
            <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari Nosik, Nama..." class="form-control" style="width:180px; font-size:12.5px; padding-left:28px;">
            <span class="ms" style="position:absolute; left:6px; top:8px; color:var(--muted); font-size:16px;">search</span>
        </div>
        @if($classroomId || $keyword)
            <a href="{{ route('programs.show', ['program' => $program->id, 'tab' => $tab]) }}" class="btn btn-sm" style="color:var(--muted); text-decoration:none;">Reset</a>
        @endif
    </form>
</div>

<!-- TABEL SISWA DI PROGRAM INI -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <span class="ms" style="color:var(--o700);">badge</span>
            Daftar Prajurit Siswa — {{ $program->name }}
        </h3>
        <span class="badge badge-satdik">{{ $students->total() }} Serdik Ditampilkan</span>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="data-table" style="table-layout:fixed; width:100%; min-width:1050px;">
                <thead>
                    <tr>
                        <th style="width:55px; text-align:center;">No</th>
                        <th style="width:260px;">Siswa / Serdik</th>
                        <th style="width:180px;">Peleton / Kompi</th>
                        <th style="width:190px;">Kodam / Kodim Asal</th>
                        <th style="width:170px;">Status Kesehatan</th>
                        <th style="width:170px;">Status Keaktifan</th>
                        <th style="width:120px; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $idx => $st)
                        <tr style="cursor:pointer; transition:background-color 0.15s;" 
                            onclick="if(!event.target.closest('a') && !event.target.closest('button')) window.location='{{ route('students.show', $st) }}'" 
                            title="Klik untuk melihat data lengkap serdik {{ $st->full_name }}"
                            onmouseover="this.style.backgroundColor='#F9FAF7'" 
                            onmouseout="this.style.backgroundColor=''">
                            <td>{{ $students->firstItem() + $idx }}</td>
                            <td>
                                <div>
                                    <a href="{{ route('students.show', $st) }}" style="color:var(--o800); font-weight:700; display:block; font-size:13.5px; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                        {{ $st->full_name }}
                                    </a>
                                    <span class="badge badge-satdik" style="font-family:'Fira Code'; font-size:11px; margin-top:2px;">
                                        {{ $st->nosik }}
                                    </span>
                                </div>
                            </td>
                            <td>{{ $st->classroom->name ?? 'Belum Ditentukan' }}</td>
                            <td>{{ $st->origin_military_unit ?? '-' }}</td>
                            <td>
                                @php $u = $st->unified_status; @endphp
                                <span class="badge {{ $u['badge'] }}" style="font-size:11.5px;">
                                    <span class="ms" style="font-size:13px;">{{ $u['icon'] }}</span> {{ $u['label'] }}
                                </span>
                            </td>
                            <td>
                                @if($st->is_counted)
                                    <span class="badge badge-green" style="font-size:11px;">
                                        <span class="ms" style="font-size:12px;">check</span> Terhitung Aktif
                                    </span>
                                @else
                                    <span class="badge" style="background:#E2E8F0; color:#475569; font-size:11px;">
                                        <span class="ms" style="font-size:12px;">archive</span> Arsip (Tidak Terhitung)
                                    </span>
                                @endif
                            </td>
                            <td style="text-align:right; white-space:nowrap;">
                                <a href="{{ route('students.show', $st) }}" class="btn btn-outline btn-sm" title="Dossier Lengkap" style="padding:5px 8px;">
                                    <span class="ms">visibility</span>
                                </a>
                                <a href="{{ route('health.show', $st) }}" class="btn btn-outline btn-sm" title="Rekam Medis" style="color:var(--green); padding:5px 8px;">
                                    <span class="ms">medical_services</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:36px; color:var(--muted);">
                                <span class="ms" style="font-size:40px; color:var(--o200); display:block; margin-bottom:6px;">folder_off</span>
                                Tidak ada prajurit siswa pada kriteria filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-pagination-footer">
            <div class="table-pagination-info">
                Menampilkan <b>{{ $students->firstItem() ?? 0 }}</b> - <b>{{ $students->lastItem() ?? 0 }}</b> dari <b>{{ $students->total() }}</b> serdik
            </div>
            <div>
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>

<!-- =====================================================================
     MODAL TAMBAH KOMPI & PELETON BARU
     ===================================================================== -->
<div class="modal-overlay" id="createClassroomModal">
    <div class="modal-card" style="max-width:540px;">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:var(--gold2); font-size:22px;">add_circle</span>
                <b style="font-family:'Montserrat',sans-serif; font-size:15px;">Buat Kompi & Peleton Baru</b>
            </div>
            <button type="button" onclick="closeCreateClassroomModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:20px;">&times;</button>
        </div>

        <form method="POST" action="{{ route('classrooms.store', $program->id) }}">
            @csrf
            <input type="hidden" name="education_program_id" value="{{ $program->id }}">
            <div class="modal-body" style="padding:22px;">
                <div style="background:#F4F6F2; border:1px solid #E2E6DF; padding:10px 14px; border-radius:8px; margin-bottom:16px; font-size:12.5px;">
                    Program Pendidikan: <b>{{ $program->name }} ({{ $program->satdik->code }})</b>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Nama Kompi: <span style="color:var(--red);">*</span></label>
                        <input type="text" list="createCompanyList" name="company" id="create_company" class="form-control" placeholder="Contoh: Kompi A" oninput="updateCreateClassName()" required>
                        <datalist id="createCompanyList">
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

                    <div class="form-group">
                        <label class="form-label">Nama Peleton: <span style="color:var(--red);">*</span></label>
                        <input type="text" list="createPlatoonList" name="platoon" id="create_platoon" class="form-control" placeholder="Contoh: Peleton 1" oninput="updateCreateClassName()" required>
                        <datalist id="createPlatoonList">
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

                <div class="form-group">
                    <label class="form-label">Nama Lengkap Rombel / Peleton: <span style="color:var(--red);">*</span></label>
                    <input type="text" name="name" id="create_name" class="form-control" placeholder="Kompi A Peleton 1" required>
                    <div class="form-hint">Otomatis terisi dari Kompi & Peleton di atas, dapat disesuaikan jika perlu.</div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Kode Kelas (Opsional):</label>
                        <input type="text" name="code" id="create_code" class="form-control" placeholder="Otomatis diisi jika kosong">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kapasitas Siswa (Orang):</label>
                        <input type="number" name="capacity" id="create_capacity" class="form-control" value="35" min="1" max="250">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Komandan Peleton (Danton) / Danki:</label>
                    <input type="text" name="platoon_leader_name" id="create_danton" class="form-control" placeholder="Contoh: Lettu Inf Suryadi">
                </div>
            </div>

            <div style="padding:14px 22px; background:#FAFBF9; border-top:1px solid var(--line); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeCreateClassroomModal()">Batal</button>
                <button type="submit" class="btn btn-gold">
                    <span class="ms">save</span> Simpan Kompi & Peleton
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL EDIT KOMPI & PELETON
     ===================================================================== -->
<div class="modal-overlay" id="editClassroomModal">
    <div class="modal-card" style="max-width:540px;">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:var(--gold2); font-size:22px;">edit</span>
                <b style="font-family:'Montserrat',sans-serif; font-size:15px;">Edit Kompi & Peleton</b>
            </div>
            <button type="button" onclick="closeEditClassroomModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:20px;">&times;</button>
        </div>

        <form method="POST" id="editClassroomForm">
            @csrf
            @method('PUT')
            <div class="modal-body" style="padding:22px;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Nama Kompi:</label>
                        <input type="text" list="editCompanyList" name="company" id="edit_company" class="form-control" placeholder="Contoh: Kompi A" oninput="updateEditClassName()">
                        <datalist id="editCompanyList">
                            <option value="Kompi A">
                            <option value="Kompi B">
                            <option value="Kompi C">
                            <option value="Kompi Senapan A">
                            <option value="Kompi Senapan B">
                            <option value="Kompi Bantuan">
                            <option value="Kompi Markas">
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Peleton:</label>
                        <input type="text" list="editPlatoonList" name="platoon" id="edit_platoon" class="form-control" placeholder="Contoh: Peleton 1" oninput="updateEditClassName()">
                        <datalist id="editPlatoonList">
                            <option value="Peleton 1">
                            <option value="Peleton 2">
                            <option value="Peleton 3">
                            <option value="Peleton Bantuan">
                        </datalist>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap Rombel / Peleton: <span style="color:var(--red);">*</span></label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Kode Kelas: <span style="color:var(--red);">*</span></label>
                        <input type="text" name="code" id="edit_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kapasitas Siswa (Orang): <span style="color:var(--red);">*</span></label>
                        <input type="number" name="capacity" id="edit_capacity" class="form-control" min="1" max="250" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Komandan Peleton (Danton) / Danki:</label>
                    <input type="text" name="platoon_leader_name" id="edit_danton" class="form-control" placeholder="Contoh: Lettu Inf Suryadi">
                </div>
            </div>

            <div style="padding:14px 22px; background:#FAFBF9; border-top:1px solid var(--line); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeEditClassroomModal()">Batal</button>
                <button type="submit" class="btn btn-gold">
                    <span class="ms">save</span> Perbarui Peleton
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openCreateClassroomModal() {
    document.getElementById('createClassroomModal').classList.add('active');
    setTimeout(() => document.getElementById('create_company').focus(), 100);
}

function closeCreateClassroomModal() {
    document.getElementById('createClassroomModal').classList.remove('active');
}

function updateCreateClassName() {
    const comp = document.getElementById('create_company').value.trim();
    const plt = document.getElementById('create_platoon').value.trim();
    const nameField = document.getElementById('create_name');
    const parts = [comp, plt].filter(Boolean);
    if (parts.length > 0) {
        nameField.value = parts.join(' ');
    }
}

function openEditClassroomModal(cls) {
    const form = document.getElementById('editClassroomForm');
    form.action = `/classrooms/${cls.id}`;
    document.getElementById('edit_company').value = cls.company || '';
    document.getElementById('edit_platoon').value = cls.platoon || '';
    document.getElementById('edit_name').value = cls.name || '';
    document.getElementById('edit_code').value = cls.code || '';
    document.getElementById('edit_capacity').value = cls.capacity || 35;
    document.getElementById('edit_danton').value = cls.platoon_leader_name || '';
    document.getElementById('editClassroomModal').classList.add('active');
}

function closeEditClassroomModal() {
    document.getElementById('editClassroomModal').classList.remove('active');
}

function updateEditClassName() {
    const comp = document.getElementById('edit_company').value.trim();
    const plt = document.getElementById('edit_platoon').value.trim();
    const nameField = document.getElementById('edit_name');
    const parts = [comp, plt].filter(Boolean);
    if (parts.length > 0) {
        nameField.value = parts.join(' ');
    }
}

// Tutup modal jika klik di luar card
window.addEventListener('click', function(e) {
    const cModal = document.getElementById('createClassroomModal');
    const eModal = document.getElementById('editClassroomModal');
    if (e.target === cModal) closeCreateClassroomModal();
    if (e.target === eModal) closeEditClassroomModal();
});
</script>
@endsection

