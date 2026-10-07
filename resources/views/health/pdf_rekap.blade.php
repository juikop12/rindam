@extends('layouts.print_military')

@section('page_size', 'A4 landscape')
@section('container_width', '297mm')

@section('title', 'Rekapitulasi Kesiapan Kesehatan Serdik — Rindam III/Siliwangi')

@section('kop_unit')
KOMANDO DAERAH MILITER III/SILIWANGI<br>
RESIMEN INDUK<br>
KESEHATAN
@endsection

@section('classification', 'TERBATAS')

@section('content')
<div class="doc-title-box">
    <div class="doc-title">REKAPITULASI STATUS KESIAPAN KESEHATAN DAN FISIK PRAJURIT SISWA</div>
    <div class="doc-number">Nomor: B / REKAP-MED / {{ now()->format('m/Y') }}</div>
</div>

<!-- DATA PARAMETER LAPORAN -->
<table class="info-table" style="margin-bottom:12px; font-size:9.5pt;">
    <tr>
        <td style="width:18%;">Satuan Pendidikan (Satdik)</td>
        <td style="width:2%;">:</td>
        <td style="width:40%;"><b>{{ $selectedSatdik ? $selectedSatdik->name : 'Seluruh Satdik (Secaba, Secata, Dodikjur, Dodiklatpur, Dodik Bela Negara)' }}</b></td>
        <td style="width:16%;">Tanggal Pelaporan</td>
        <td style="width:2%;">:</td>
        <td style="width:22%;">{{ now()->translatedFormat('d F Y') }}</td>
    </tr>
    <tr>
        <td>Program Pendidikan</td>
        <td>:</td>
        <td>{{ $selectedProgram ? $selectedProgram->name . ' (TA ' . $selectedProgram->academic_year . ')' : 'Semua Program Pendidikan Diklat' }}</td>
        <td>Klasifikasi Laporan</td>
        <td>:</td>
        <td>Kesiapan Latihan Harian</td>
    </tr>
    @if(!empty($healthStatus))
    <tr>
        <td>Filter Status Kesehatan</td>
        <td>:</td>
        <td colspan="4"><span class="badge-print">{{ strtoupper($healthStatus) }}</span></td>
    </tr>
    @endif
</table>

<!-- KOTAK REKAPITULASI KEKUATAN & KESEHATAN -->
<div class="summary-box-grid">
    <div class="summary-box-item">
        <div class="summary-box-num">{{ $students->count() }}</div>
        <div class="summary-box-label">Total Serdik</div>
    </div>
    <div class="summary-box-item" style="border-left: 3px solid #16A34A;">
        <div class="summary-box-num" style="color:#16A34A;">{{ $stats['siap_latih'] ?? 0 }}</div>
        <div class="summary-box-label">Siap Latih (Sehat)</div>
    </div>
    <div class="summary-box-item" style="border-left: 3px solid #D97706;">
        <div class="summary-box-num" style="color:#D97706;">{{ $stats['berobat_jalan'] ?? 0 }}</div>
        <div class="summary-box-label">Berobat Jalan</div>
    </div>
    <div class="summary-box-item" style="border-left: 3px solid #DC2626;">
        <div class="summary-box-num" style="color:#DC2626;">{{ $stats['rawat_inap'] ?? 0 }}</div>
        <div class="summary-box-label">Rawat Inap Poliklinik</div>
    </div>
    <div class="summary-box-item" style="border-left: 3px solid #2563EB;">
        <div class="summary-box-num" style="color:#2563EB;">{{ $stats['rujuk_rumkit'] ?? 0 }}</div>
        <div class="summary-box-label">Rujuk Rumkit TNI AD</div>
    </div>
</div>

<!-- TABEL REKAPITULASI NOMINATIF KESEHATAN -->
<table class="mil-table" style="font-size:9pt;">
    <thead>
        <tr>
            <th style="width:28px;">No</th>
            <th style="width:75px;">NOSIK</th>
            <th style="width:145px;">Nama Prajurit Siswa</th>
            <th style="width:95px;">Satdik / Kompi</th>
            <th style="width:65px;">Darah & IMT</th>
            <th style="width:95px;">Status Latihan</th>
            <th style="width:85px;">Stakes</th>
            <th style="width:140px;">Riwayat Medis / Alergi</th>
            <th>Catatan & Rekomendasi Dokter</th>
        </tr>
    </thead>
    <tbody>
        @forelse($students as $index => $std)
            @php $hr = $std->healthRecord; @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center font-mono font-bold">{{ $std->nosik }}</td>
                <td>
                    <b>{{ strtoupper($std->full_name) }}</b>
                    <div style="font-size:8pt; color:#475569;">{{ $std->student_rank ?? 'Siswa' }}</div>
                </td>
                <td>
                    <div>{{ $std->satdik->code ?? '-' }}</div>
                    <div style="font-size:8pt; color:#475569;">{{ $std->classroom->name ?? ($std->company ?? '-') }}</div>
                </td>
                <td class="text-center">
                    <b>{{ $std->blood_type ?? '-' }}</b>
                    <div style="font-size:8pt; color:#475569;">{{ $std->height_cm ? $std->height_cm.'cm' : '-' }}/{{ $std->weight_kg ? $std->weight_kg.'kg' : '-' }}</div>
                </td>
                <td class="text-center">
                    <span class="badge-print">
                        {{ strtoupper($hr?->daily_health_status ?? 'SIAP LATIH') }}
                    </span>
                </td>
                <td class="text-center">
                    {{ $hr?->stakes_grade ?? 'Stakes I' }}
                </td>
                <td style="font-size:8.5pt;">
                    @if(!empty($hr?->allergies))
                        <div><b>Alergi:</b> {{ $hr->allergies }}</div>
                    @endif
                    @if(!empty($hr?->medical_history))
                        <div><b>Riw:</b> {{ $hr->medical_history }}</div>
                    @endif
                    @if(empty($hr?->allergies) && empty($hr?->medical_history))
                        <span style="color:#64748B;">Nihil</span>
                    @endif
                </td>
                <td style="font-size:8.5pt;">
                    {{ $hr?->doctor_notes ?: 'Kondisi stabil, mengikuti rutinitas pendidikan.' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center" style="padding:16px;">
                    <i>Tidak ada data kesehatan prajurit siswa yang sesuai dengan kriteria filter.</i>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- PENGESAHAN LAPORAN KESEHATAN -->
<div class="signature-grid">
    <div class="signature-box">
        <div>Mengetahui,</div>
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

    <div class="signature-box">
        <div>Bandung, {{ now()->translatedFormat('d F Y') }}</div>
        <div style="margin-top:2px; font-weight:bold;">Kepala Kesehatan Rindam III/Slw / Tim Medis,</div>
        <div class="signature-space"></div>
        <div class="signature-name">{{ \App\Models\SystemSetting::get('kakes_name', 'dr. Agus Budiman, Sp.Ok.') }}</div>
        <div class="signature-rank">{{ \App\Models\SystemSetting::get('kakes_rank', 'Mayor Ckm NRP 11080029340582') }}</div>
    </div>
</div>
@endsection
