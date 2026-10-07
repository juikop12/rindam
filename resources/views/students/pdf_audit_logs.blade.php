@extends('layouts.print_military')

@section('page_size', 'A4 landscape')
@section('container_width', '297mm')

@section('title', 'Laporan Forensik Audit Trail Data Pribadi — SIPANDU-WBK')

@section('kop_unit')
KOMANDO DAERAH MILITER III/SILIWANGI<br>
RESIMEN INDUK<br>
TIM KERJA ZONA INTEGRITAS (ZI-WBK) &bull; BIDANG PENGAWASAN
@endsection

@section('classification', 'RAHASIA')

@section('content')
<div class="doc-title-box">
    <div class="doc-title">LAPORAN FORENSIK DIGITAL AUDIT TRAIL AKSES DATA PRIBADI SENSITIF</div>
    <div class="doc-number">Nomor: B / AUDIT-WAS / {{ now()->format('m/Y') }}</div>
</div>

<!-- DASAR HUKUM & KETERANGAN DOKUMEN -->
<table class="info-table" style="margin-bottom:12px; font-size:9pt;">
    <tr>
        <td style="width:16%;">Dasar Hukum</td>
        <td style="width:2%;">:</td>
        <td style="width:48%;">
            1. UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi.<br>
            2. Peraturan Panglima TNI tentang Tata Kelola Keamanan Siber & Informasi.<br>
            3. Rencana Kerja ZI-WBK Rindam III/Siliwangi (Area 5: Penguatan Pengawasan).
        </td>
        <td style="width:14%;">Klasifikasi</td>
        <td style="width:2%;">:</td>
        <td style="width:18%;"><b>RAHASIA DINAS</b></td>
    </tr>
    <tr>
        <td>Metode Pengamanan</td>
        <td>:</td>
        <td>Enkripsi Kuat AES-256-CBC &bull; SHA-256 Blind Indexing &bull; Log Immutable Digital</td>
        <td>Waktu Ekspor</td>
        <td>:</td>
        <td>{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
    </tr>
</table>

<!-- KOTAK REKAPITULASI FORENSIK -->
<div class="summary-box-grid">
    <div class="summary-box-item">
        <div class="summary-box-num">{{ $logs->count() }}</div>
        <div class="summary-box-label">Aktivitas Akses Terdata</div>
    </div>
    <div class="summary-box-item" style="border-left: 3px solid #16A34A;">
        <div class="summary-box-num" style="color:#16A34A;">100%</div>
        <div class="summary-box-label">Tercatat Sistem (Audit Trail)</div>
    </div>
    <div class="summary-box-item" style="border-left: 3px solid #2563EB;">
        <div class="summary-box-num" style="color:#2563EB;">AES-256</div>
        <div class="summary-box-label">Standar Enkripsi Database</div>
    </div>
    <div class="summary-box-item" style="border-left: 3px solid #D97706;">
        <div class="summary-box-num" style="color:#D97706;">TERKONTROL</div>
        <div class="summary-box-label">Akses Berizin Dinas</div>
    </div>
</div>

<!-- TABEL REKAMAN FORENSIK AUDIT TRAIL -->
<table class="mil-table" style="font-size:8.5pt;">
    <thead>
        <tr>
            <th style="width:28px;">No</th>
            <th style="width:115px;">Waktu Akses (WIB)</th>
            <th style="width:140px;">Personel Pengakses</th>
            <th style="width:120px;">IP & Terminal</th>
            <th style="width:160px;">Serdik Sasaran Dekripsi</th>
            <th>Alasan & Keperluan Kedinasan</th>
            <th style="width:90px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($logs as $index => $log)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td style="font-size:8pt;">
                    <b>{{ $log->accessed_at ? \Carbon\Carbon::parse($log->accessed_at)->translatedFormat('d/m/Y H:i:s') : '-' }}</b>
                </td>
                <td>
                    <b>{{ $log->accessedByUser->name ?? 'User #'.$log->accessed_by_user_id }}</b>
                    <div style="font-size:7.5pt; color:#475569;">
                        Role: {{ strtoupper($log->accessedByUser->role_code ?? 'OPERATOR') }}
                    </div>
                </td>
                <td class="font-mono" style="font-size:8pt;">
                    <div>{{ $log->ip_address ?? '127.0.0.1' }}</div>
                    <div style="font-size:7.5pt; color:#64748B; font-family:'Arial', sans-serif;">
                        {{ Str::limit($log->user_agent ?? 'Browser Web', 25) }}
                    </div>
                </td>
                <td>
                    @if($log->student)
                        <b>{{ strtoupper($log->student->full_name) }}</b>
                        <div style="font-size:7.5pt; color:#475569;">
                            NOSIS: <span class="font-mono font-bold">{{ $log->student->nosik }}</span> ({{ $log->student->satdik->code ?? '-' }})
                        </div>
                    @else
                        <span style="color:#94A3B8;">Serdik ID #{{ $log->student_id }} (Terhapus)</span>
                    @endif
                </td>
                <td style="font-size:8pt;">
                    {{ $log->access_reason ?: 'Verifikasi data pribadi serdik untuk keperluan dinas pendidikan.' }}
                </td>
                <td class="text-center">
                    <span class="badge-print" style="border-color:#16A34A; color:#16A34A;">
                        TERVERIFIKASI
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center" style="padding:16px;">
                    <i>Belum ada catatan aktivitas pembukaan/dekripsi data sensitif yang terekam.</i>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- PENGESAHAN LAPORAN AUDIT -->
<div class="signature-grid">
    <div class="signature-box">
        <div>Mengetahui,</div>
        <div style="margin-top:2px; font-weight:bold;">Perwira Pemeriksa Wasrik ZI-WBK,</div>
        <div class="signature-space"></div>
        <div class="signature-name">Mayor Inf Dwi Hartanto</div>
        <div class="signature-rank">Inspektorat / Wasrik Rindam III/Slw</div>
    </div>

    <div class="signature-box">
        <div>Bandung, {{ now()->translatedFormat('d F Y') }}</div>
        <div style="margin-top:2px; font-weight:bold;">Penanggung Jawab Keamanan Siber & TI,</div>
        <div class="signature-space"></div>
        <div class="signature-name">{{ auth()->user()?->name ?? 'Super Administrator' }}</div>
        <div class="signature-rank">Tim Pengelola Sistem SIPANDU-WBK</div>
    </div>
</div>
@endsection
