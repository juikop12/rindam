@extends('layouts.print_military')

@section('page_size', 'A4 landscape')
@section('container_width', '297mm')

@section('title', 'Daftar Nominatif Prajurit Siswa — Rindam III/Siliwangi')

@section('kop_unit')
KOMANDO DAERAH MILITER III/SILIWANGI<br>
RESIMEN INDUK<br>
{{ $selectedSatdik ? strtoupper($selectedSatdik->name) : 'BAGIAN OPERASI DAN PENDIDIKAN' }}
@endsection

@section('classification', 'TERBATAS')

@section('content')
<div class="doc-title-box">
    <div class="doc-title">DAFTAR NOMINATIF PRAJURIT SISWA PENDIDIKAN</div>
    <div class="doc-number">Nomor: B / NOM-{{ $selectedSatdik ? $selectedSatdik->code : 'RINDAM' }} / {{ now()->format('m/Y') }}</div>
</div>

<!-- DATA PARAMETER SATDIK & PROGRAM -->
<table class="info-table" style="margin-bottom:12px; font-size:9.5pt;">
    <tr>
        <td style="width:18%;">Satuan Pendidikan (Satdik)</td>
        <td style="width:2%;">:</td>
        <td style="width:40%;"><b>{{ $selectedSatdik ? $selectedSatdik->name : 'Seluruh Satdik Jajaran Rindam III/Siliwangi' }}</b></td>
        <td style="width:16%;">Tanggal Pelaporan</td>
        <td style="width:2%;">:</td>
        <td style="width:22%;">{{ now()->translatedFormat('d F Y') }}</td>
    </tr>
    <tr>
        <td>Program Pendidikan</td>
        <td>:</td>
        <td>{{ $selectedProgram ? $selectedProgram->name . ' (TA ' . $selectedProgram->academic_year . ')' : 'Semua Program Pendidikan' }}</td>
        <td>Status Kekuatan</td>
        <td>:</td>
        <td>{{ $tab === 'aktif' ? 'Kekuatan Aktif Terhitung' : ($tab === 'arsip' ? 'Arsip Selesai/Lulus' : 'Seluruh Prajurit Siswa') }}</td>
    </tr>
</table>

<!-- KOTAK REKAPITULASI KEKUATAN NOMINATIF -->
<div class="summary-box-grid">
    <div class="summary-box-item">
        <div class="summary-box-num">{{ $students->count() }}</div>
        <div class="summary-box-label">Total Nominatif Tercatat</div>
    </div>
    <div class="summary-box-item" style="border-left: 3px solid #16A34A;">
        <div class="summary-box-num" style="color:#16A34A;">{{ $stats['aktif'] ?? 0 }}</div>
        <div class="summary-box-label">Aktif Terhitung</div>
    </div>
    <div class="summary-box-item" style="border-left: 3px solid #D97706;">
        <div class="summary-box-num" style="color:#D97706;">{{ $stats['sakit'] ?? 0 }}</div>
        <div class="summary-box-label">Sakit / Dispen</div>
    </div>
    <div class="summary-box-item" style="border-left: 3px solid #2563EB;">
        <div class="summary-box-num" style="color:#2563EB;">{{ $stats['dinas_luar'] ?? 0 }}</div>
        <div class="summary-box-label">Dinas Luar</div>
    </div>
    <div class="summary-box-item" style="border-left: 3px solid #475569;">
        <div class="summary-box-num" style="color:#475569;">{{ ($stats['selesai'] ?? 0) + ($stats['lulus'] ?? 0) }}</div>
        <div class="summary-box-label">Selesai / Lulus (Arsip)</div>
    </div>
</div>

<!-- TABEL DAFTAR NOMINATIF LENGKAP -->
<table class="mil-table" style="font-size:9pt;">
    <thead>
        <tr>
            <th style="width:28px;">No</th>
            <th style="width:75px;">NOSIK</th>
            <th style="width:160px;">Nama Prajurit Siswa</th>
            <th style="width:75px;">Pangkat</th>
            <th style="width:95px;">Satdik / Kompi</th>
            <th style="width:130px;">Tempat, Tgl Lahir</th>
            <th style="width:65px;">Agama / Darah</th>
            <th style="width:105px;">Satuan Asal / Pend</th>
            <th style="width:85px;">Status</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($students as $index => $std)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center font-mono font-bold">{{ $std->nosik }}</td>
                <td>
                    <b>{{ strtoupper($std->full_name) }}</b>
                    <div style="font-size:8pt; color:#475569;">{{ $std->gender === 'L' ? 'Pria' : 'Wanita' }}</div>
                </td>
                <td class="text-center">{{ $std->student_rank ?? 'Siswa' }}</td>
                <td>
                    <div><b>{{ $std->satdik->code ?? '-' }}</b></div>
                    <div style="font-size:8pt; color:#475569;">{{ $std->classroom->name ?? ($std->company ?? '-') }} ({{ $std->platoon ?? '-' }})</div>
                </td>
                <td>
                    {{ $std->birth_place ?? '-' }},
                    {{ $std->birth_date ? \Carbon\Carbon::parse($std->birth_date)->translatedFormat('d/m/Y') : '-' }}
                </td>
                <td class="text-center">
                    <div>{{ $std->religion ?? '-' }}</div>
                    <div style="font-size:8pt; font-weight:bold; color:#475569;">Gol. {{ $std->blood_type ?? '-' }}</div>
                </td>
                <td>
                    <div>{{ $std->origin_military_unit ?: 'Sipil' }}</div>
                    <div style="font-size:8pt; color:#475569;">{{ $std->education_level ?: 'SMA' }}</div>
                </td>
                <td class="text-center">
                    <span class="badge-print">
                        {{ strtoupper($std->status ?? 'AKTIF') }}
                    </span>
                </td>
                <td style="font-size:8pt; color:#334155;">
                    {{ $std->is_counted ? 'Kekuatan Aktif' : 'Arsip Pendidikan' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center" style="padding:16px;">
                    <i>Tidak ada data prajurit siswa yang sesuai dengan kriteria filter.</i>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- PENGESAHAN LAPORAN NOMINATIF -->
<div class="signature-grid">
    <div class="signature-box">
        <div>Mengetahui,</div>
        <div style="margin-top:2px; font-weight:bold;">Perwira Seksi Administrasi & Personel,</div>
        <div class="signature-space"></div>
        <div class="signature-name">Kapten Inf Deni Kusuma</div>
        <div class="signature-rank">Pasipers Satdik Rindam III/Slw</div>
    </div>

    <div class="signature-box">
        <div>Bandung, {{ now()->translatedFormat('d F Y') }}</div>
        <div style="margin-top:2px; font-weight:bold;">
            {{ $selectedSatdik ? ($selectedSatdik->commander_title ?? 'Komandan Satdik') : 'Komandan Rindam III/Siliwangi' }}
        </div>
        <div class="signature-space"></div>
        <div class="signature-name">
            {{ $selectedSatdik ? ($selectedSatdik->commander_name ?? 'Letkol Inf Hendra Prasetyo, S.I.P.') : (\App\Models\SystemSetting::get('danrindam_name', 'Kolonel Inf Muhammad Iwan, S.I.P.')) }}
        </div>
        <div class="signature-rank">
            {{ $selectedSatdik ? 'Letnan Kolonel Inf' : (\App\Models\SystemSetting::get('danrindam_rank', 'Kolonel Inf')) }}
        </div>
    </div>
</div>
@endsection
