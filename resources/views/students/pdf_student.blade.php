@extends('layouts.print_military')

@section('page_size', 'A4 portrait')
@section('container_width', '210mm')

@section('title', 'Dossier Serdik — ' . $student->full_name . ' (' . $student->nosik . ')')

@section('kop_unit')
KOMANDO DAERAH MILITER III/SILIWANGI<br>
RESIMEN INDUK<br>
{{ $student->satdik ? strtoupper($student->satdik->name) : 'BAGIAN OPERASI DAN PENDIDIKAN' }}
@endsection

@section('classification', 'RAHASIA')

@section('content')
@php
    $profile = $student->personalProfile;
    $health = $student->healthRecord;
    $canViewSensitive = auth()->user()?->isSuperAdmin() || (auth()->user()?->isPimpinan() && !auth()->user()?->isDanrindam());
    // Ambil display NIK sesuai aturan otorisasi
    $displayNik = $canViewSensitive && $profile ? $profile->nik : ($profile ? $profile->masked_nik : '-');
    $displayKk = $canViewSensitive && $profile ? $profile->family_card_number : ($profile ? $profile->masked_family_card : '-');
    $displayMother = $canViewSensitive && $profile ? $profile->mother_name : ($profile ? $profile->masked_mother_name : '-');
    $displayFather = $profile ? ($profile->father_name ?: '-') : '-';
    $displayPhone = $canViewSensitive && $profile ? $profile->emergency_contact_phone : ($profile ? $profile->masked_emergency_phone : '-');
@endphp

<div class="doc-title-box">
    <div class="doc-title">LEMBAR DOSSIER PRIBADI & BUKU INDUK PRAJURIT SISWA</div>
    <div class="doc-number">Nomor: B / DOS-{{ str_replace(['/', ' '], '-', $student->nosik) }} / {{ now()->format('m/Y') }}</div>
</div>

<!-- HEADER IDENTITAS CEPAT & PAS FOTO -->
<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; gap:16px;">
    <div style="flex:1;">
        <table class="info-table" style="margin-bottom:0;">
            <tr>
                <td style="width:30%;">Nama Lengkap</td>
                <td style="width:2%;">:</td>
                <td style="width:68%;"><b style="font-size:11.5pt;">{{ strtoupper($student->full_name) }}</b></td>
            </tr>
            <tr>
                <td>Nomor Siswa (NOSIK)</td>
                <td>:</td>
                <td><span class="font-mono font-bold" style="font-size:11pt;">{{ $student->nosik }}</span></td>
            </tr>
            <tr>
                <td>Satuan Pendidikan</td>
                <td>:</td>
                <td><b>{{ $student->satdik->name ?? '-' }}</b> ({{ $student->satdik->code ?? '-' }})</td>
            </tr>
            <tr>
                <td>Program Pendidikan</td>
                <td>:</td>
                <td>{{ $student->educationProgram->name ?? '-' }} (TA {{ $student->educationProgram->academic_year ?? '-' }})</td>
            </tr>
            <tr>
                <td>Pangkat Siswa / Matra</td>
                <td>:</td>
                <td>{{ $student->student_rank ?? 'Siswa' }} / TNI Angkatan Darat</td>
            </tr>
            <tr>
                <td>Status Kemiliteran</td>
                <td>:</td>
                <td>
                    <span class="badge-print">{{ strtoupper($student->status ?? 'AKTIF') }}</span>
                    <span style="font-size:9pt; margin-left:6px; color:#475569;">
                        ({{ $student->is_counted ? 'Terhitung Kekuatan Aktif' : 'Arsip Pendidikan' }})
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- KOTAK PAS FOTO RESMI (3X4 / 4X6 MILITER) -->
    <div class="student-photo-box">
        @if($student->photo_path && file_exists(public_path('storage/' . $student->photo_path)))
            <img src="{{ asset('storage/' . $student->photo_path) }}" alt="Foto {{ $student->full_name }}">
        @else
            <div>
                <div style="font-weight:bold; font-size:11pt; color:#1E293B;">PAS FOTO</div>
                <div style="font-size:8pt; color:#64748B; margin-top:2px;">3 x 4 CM</div>
                <div style="font-size:7.5pt; color:#94A3B8; margin-top:4px;">RESMI DINAS</div>
            </div>
        @endif
    </div>
</div>

<!-- BAGIAN I: DATA POKOK KEMILITERAN & PENDIDIKAN -->
<div class="section-header">I. DATA POKOK KEMILITERAN & PENDIDIKAN</div>
<table class="mil-table">
    <tr>
        <td style="width:25%; background:#F8FAFC;"><b>Kompi / Peleton</b></td>
        <td style="width:25%;">{{ $student->company ?? ($student->classroom->name ?? '-') }} / {{ $student->platoon ?? '-' }}</td>
        <td style="width:25%; background:#F8FAFC;"><b>Satuan Asal / Pengiriman</b></td>
        <td style="width:25%;">{{ $student->origin_military_unit ?: 'Penerimaan / Sipil' }}</td>
    </tr>
    <tr>
        <td style="background:#F8FAFC;"><b>Pendidikan Umum</b></td>
        <td>{{ $student->education_level ?: 'SMA / Sederajat' }}</td>
        <td style="background:#F8FAFC;"><b>Ruang Kelas / Ton</b></td>
        <td>{{ $student->classroom->name ?? '-' }}</td>
    </tr>
</table>

<!-- BAGIAN II: DATA PRIBADI & KEPENDUDUKAN -->
<div class="section-header">II. DATA PRIBADI & KEPENDUDUKAN (TERPROTEKSI ENKRIPSI)</div>
<table class="mil-table">
    <tr>
        <td style="width:25%; background:#F8FAFC;"><b>NIK KTP</b></td>
        <td style="width:25%;" class="font-mono">{{ $displayNik }}</td>
        <td style="width:25%; background:#F8FAFC;"><b>No. Kartu Keluarga</b></td>
        <td style="width:25%;" class="font-mono">{{ $displayKk }}</td>
    </tr>
    <tr>
        <td style="background:#F8FAFC;"><b>Tempat, Tanggal Lahir</b></td>
        <td>
            {{ $student->birth_place ?? '-' }}, 
            {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-' }}
            @if($student->birth_date)
                ({{ \Carbon\Carbon::parse($student->birth_date)->age }} Thn)
            @endif
        </td>
        <td style="background:#F8FAFC;"><b>Jenis Kelamin / Agama</b></td>
        <td>{{ $student->gender === 'L' ? 'Laki-laki' : ($student->gender === 'P' ? 'Perempuan' : '-') }} / {{ $student->religion ?? '-' }}</td>
    </tr>
    <tr>
        <td style="background:#F8FAFC;"><b>Alamat Domisili Asal</b></td>
        <td colspan="3">{{ $profile->home_address ?? 'Tercatat pada berkas werving satker' }}</td>
    </tr>
</table>

<!-- BAGIAN III: DATA KELUARGA & KONTAK DARURAT -->
<div class="section-header">III. DATA KELUARGA & KONTAK DARURAT (EMERGENCY CONTACT)</div>
<table class="mil-table">
    <tr>
        <td style="width:25%; background:#F8FAFC;"><b>Nama Ayah Kandung</b></td>
        <td style="width:25%;">{{ $displayFather }}</td>
        <td style="width:25%; background:#F8FAFC;"><b>Nama Ibu Kandung</b></td>
        <td style="width:25%;">{{ $displayMother }}</td>
    </tr>
    <tr>
        <td style="background:#F8FAFC;"><b>Kontak Darurat / No. HP</b></td>
        <td class="font-mono">{{ $displayPhone }}</td>
        <td style="background:#F8FAFC;"><b>Hubungan / Wali</b></td>
        <td>{{ $profile->emergency_contact_name ?? 'Orang Tua Kandung' }}</td>
    </tr>
</table>

<!-- BAGIAN IV: ADMINISTRASI FINANSIAL & KESEHATAN -->
<div class="section-header">IV. ADMINISTRASI FINANSIAL, LOGISTIK & KESEHATAN</div>
<table class="mil-table">
    <tr>
        <td style="width:25%; background:#F8FAFC;"><b>Rekening ULP / Bank</b></td>
        <td style="width:25%;">
            @if($profile && !empty($profile->bank_account_number))
                <span class="font-mono">{{ $profile->bank_account_number }}</span> ({{ $profile->bank_name ?: 'BRI' }})
            @else
                Belum Terdata
            @endif
        </td>
        <td style="width:25%; background:#F8FAFC;"><b>No. BPJS Kesehatan</b></td>
        <td style="width:25%;" class="font-mono">{{ $profile->bpjs_number ?: ($health->bpjs_number ?? '-') }}</td>
    </tr>
    <tr>
        <td style="background:#F8FAFC;"><b>Postur Fisik & Gol Darah</b></td>
        <td>
            Tinggi: {{ $student->height_cm ?: ($health->height_cm ?? '-') }} cm &bull; 
            Berat: {{ $student->weight_kg ?: ($health->weight_kg ?? '-') }} kg &bull; 
            Darah: <b>{{ $student->blood_type ?? '-' }}</b>
        </td>
        <td style="background:#F8FAFC;"><b>Status Stakes & Medis</b></td>
        <td>
            <b>{{ $health->stakes_grade ?? 'Stakes I' }}</b> / 
            <span class="badge-print">{{ strtoupper($health->daily_health_status ?? 'SIAP LATIH') }}</span>
        </td>
    </tr>
</table>

<!-- PENGESAHAN KEDINASAN -->
<div class="signature-grid">
    <div class="signature-box">
        <div>Mengetahui,</div>
        <div style="margin-top:2px; font-weight:bold;">Perwira Seksi Operasi & Pendidikan,</div>
        <div class="signature-space"></div>
        <div class="signature-name">Mayor Inf Hendri Kurniawan</div>
        <div class="signature-rank">Pasiops Satdik Rindam III/Slw</div>
    </div>

    <div class="signature-box">
        <div>Bandung, {{ now()->translatedFormat('d F Y') }}</div>
        <div style="margin-top:2px; font-weight:bold;">
            {{ $student->satdik ? ($student->satdik->commander_title ?? 'Komandan Satdik') : 'Komandan Satuan Pendidikan' }}
        </div>
        <div class="signature-space"></div>
        <div class="signature-name">
            {{ $student->satdik ? ($student->satdik->commander_name ?? 'Letkol Inf Hendra Prasetyo, S.I.P.') : 'Komandan Satdik' }}
        </div>
        <div class="signature-rank">
            Letnan Kolonel Inf
        </div>
    </div>
</div>
@endsection
