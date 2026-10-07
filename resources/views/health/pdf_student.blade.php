@extends('layouts.print_military')

@section('title', 'Lembar Rekam Medis — ' . $student->full_name . ' (' . $student->nosik . ')')

@section('kop_unit')
KOMANDO DAERAH MILITER III/SILIWANGI<br>
RESIMEN INDUK<br>
KESEHATAN
@endsection

@section('classification', 'RAHASIA MEDIS')

@section('content')
<div class="doc-title-box">
    <div class="doc-title">LEMBAR REKAM MEDIS & STATUS KESIAPAN FISIK SISWA</div>
    <div class="doc-number">Nomor: B / MED-{{ str_replace(['/', ' '], '-', $student->nosik) }} / {{ now()->format('m/Y') }}</div>
</div>

<!-- BAGIAN I: IDENTITAS PRAJURIT SISWA -->
<div style="font-weight:bold; margin-bottom:6px; font-size:10.5pt;">I. IDENTITAS PRAJURIT SISWA</div>
<table class="info-table" style="margin-bottom:12px;">
    <tr>
        <td style="width:28%;">1. Nama Lengkap</td>
        <td style="width:2%;">:</td>
        <td style="width:70%;"><b>{{ strtoupper($student->full_name) }}</b></td>
    </tr>
    <tr>
        <td>2. Nomor Siswa (NOSIS)</td>
        <td>:</td>
        <td><span style="font-family:'Courier New', monospace; font-weight:bold;">{{ $student->nosik }}</span></td>
    </tr>
    <tr>
        <td>3. Satuan Pendidikan (Satdik)</td>
        <td>:</td>
        <td>{{ $student->satdik->name ?? '-' }} ({{ $student->satdik->code ?? '-' }})</td>
    </tr>
    <tr>
        <td>4. Program Pendidikan</td>
        <td>:</td>
        <td>{{ $student->educationProgram->name ?? '-' }} (TA {{ $student->educationProgram->academic_year ?? '-' }})</td>
    </tr>
    <tr>
        <td>5. Kompi / Peleton</td>
        <td>:</td>
        <td>{{ $student->classroom->name ?? '-' }} ({{ $student->platoon ?? '-' }})</td>
    </tr>
    <tr>
        <td>6. Pangkat / Matra</td>
        <td>:</td>
        <td>{{ $student->student_rank ?? 'Siswa' }} / TNI Angkatan Darat</td>
    </tr>
    <tr>
        <td>7. Golongan Darah / Rhesus</td>
        <td>:</td>
        <td><b>{{ $student->blood_type ?? '-' }}</b></td>
    </tr>
    <tr>
        <td>8. Postur Fisik</td>
        <td>:</td>
        <td>Tinggi: {{ $student->height_cm ?? '-' }} cm &bull; Berat: {{ $student->weight_kg ?? '-' }} kg &bull; IMT: {{ $student->bmi ?? '-' }}</td>
    </tr>
</table>

<!-- BAGIAN II: EVALUASI KESEHATAN & STATUS LATIHAN -->
<div style="font-weight:bold; margin-bottom:6px; font-size:10.5pt; margin-top:14px;">II. STATUS KESIAPAN LATIHAN & KESEHATAN FISIK</div>
<table class="mil-table">
    <thead>
        <tr>
            <th style="width:30%;">Parameter Pemeriksaan</th>
            <th style="width:35%;">Hasil Evaluasi Poliklinik</th>
            <th style="width:35%;">Keterangan Kedinasan</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>Status Harian Latihan</b></td>
            <td style="text-align:center;">
                <b style="font-size:11pt;">{{ strtoupper($healthRecord?->daily_health_status ?? 'SIAP LATIH') }}</b>
            </td>
            <td>
                @if(($healthRecord?->daily_health_status ?? '') === 'Siap Latih')
                    Memenuhi syarat fisik untuk mengikuti materi latihan fisik, taktik, dan pertempuran lapangan.
                @elseif(($healthRecord?->daily_health_status ?? '') === 'Berobat Jalan')
                    Diberikan dispensasi latihan berat / istirahat terbatas dengan pemantauan obat poliklinik.
                @elseif(($healthRecord?->daily_health_status ?? '') === 'Rawat Inap Poliklinik')
                    Dirawat intensif di Poliklinik Satdik Rindam III/Siliwangi (Bebas dinas sementara).
                @elseif(($healthRecord?->daily_health_status ?? '') === 'Rujuk Rumkit')
                    Dirujuk ke Rumah Sakit Tk. II/Tk. IV TNI AD untuk penanganan spesialis lanjutan.
                @else
                    Tercatat dalam pemantauan medis dinas.
                @endif
            </td>
        </tr>
        <tr>
            <td><b>Klasifikasi Stakes (Standar Kesehatan)</b></td>
            <td style="text-align:center;">
                <b>{{ $healthRecord?->stakes_grade ?? 'Stakes I (Sangat Baik)' }}</b>
            </td>
            <td>
                @if(str_contains($healthRecord?->stakes_grade ?? '', 'Stakes I'))
                    Kondisi fisik dan organ prima, tanpa kelainan medis.
                @elseif(str_contains($healthRecord?->stakes_grade ?? '', 'Stakes II'))
                    Kelainan ringan tanpa mengganggu fungsi kedinasan militer.
                @elseif(str_contains($healthRecord?->stakes_grade ?? '', 'Stakes III'))
                    Pembatasan aktivitas jasmani tertentu di bawah pengawasan dokter.
                @else
                    Tidak Memenuhi Syarat Sementara (TMS) untuk latihan berat.
                @endif
            </td>
        </tr>
        <tr>
            <td><b>Pemeriksaan Terakhir</b></td>
            <td style="text-align:center;">
                {{ $healthRecord?->last_examined_at ? \Carbon\Carbon::parse($healthRecord->last_examined_at)->translatedFormat('d F Y') : '-' }}
            </td>
            <td>Pemeriksaan fisik berkala Poliklinik Satdik</td>
        </tr>
    </tbody>
</table>

<!-- BAGIAN III: RIWAYAT KLINIS & REKOMENDASI DOKTER -->
<div style="font-weight:bold; margin-bottom:6px; font-size:10.5pt; margin-top:14px;">III. RIWAYAT KLINIS & CATATAN DOKTER POLIKLINIK</div>
<table class="mil-table">
    <tr>
        <td style="width:30%; background:#FAFBF9;"><b>Riwayat Alergi Obat / Makanan</b></td>
        <td>{{ $healthRecord?->allergies ?: 'Tidak ada riwayat alergi yang dilaporkan (Aman).' }}</td>
    </tr>
    <tr>
        <td style="background:#FAFBF9;"><b>Riwayat Penyakit Kronis / Dahulu</b></td>
        <td>{{ ($healthRecord?->medical_history ?? $healthRecord?->chronic_diseases) ?: 'Tidak ada riwayat penyakit kronis atau bawaan.' }}</td>
    </tr>
    <tr>
        <td style="background:#FAFBF9;"><b>Catatan Rekomendasi Dokter</b></td>
        <td style="min-height:40px;">{{ $healthRecord?->doctor_notes ?: 'Kondisi kesehatan serdik dalam batas normal. Lanjutkan pembinaan jasmani terjadwal.' }}</td>
    </tr>
</table>

<!-- PENGESAHAN DOKTER & KAKES -->
<div class="signature-wrapper" style="margin-top:28px;">
    <div class="signature-box">
        <div>Bandung, {{ now()->translatedFormat('d F Y') }}</div>
        <div style="margin-top:2px; font-weight:bold;">Perwira Kesehatan / Dokter Poliklinik,</div>
        <div class="signature-space"></div>
        <div class="signature-name">dr. Agus Budiman, Sp.Ok.</div>
        <div class="signature-rank">Mayor Ckm NRP 11080029340582</div>
    </div>
</div>
@endsection
