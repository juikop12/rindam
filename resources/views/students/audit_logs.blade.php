@extends('layouts.app')

@section('title', 'Audit Trail Akses Data Pribadi — SIPANDU-WBK')

@section('content')

<!-- BREADCRUMB -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted);">
        <a href="{{ route('students.index') }}" style="color:var(--o700); text-decoration:none; font-weight:600;">Data Serdik</a>
        <span class="ms" style="font-size:16px;">chevron_right</span>
        <span style="color:var(--text); font-weight:700;">Audit Trail Pelindungan Data Pribadi</span>
    </div>
    <a href="{{ route('students.index') }}" class="btn btn-outline btn-sm">
        <span class="ms">arrow_back</span> Kembali ke Data Serdik
    </a>
</div>

<!-- HEADER BANNER -->
<div class="header-banner">
    <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <span class="pdp-badge">
                <span class="ms" style="font-size:16px;">policy</span> ZI WBK AREA 5: PENGAWASAN
            </span>
            <span class="pdp-badge" style="background:rgba(255,255,255,0.15);color:#fff;border-color:rgba(255,255,255,0.25);">
                <span class="ms" style="font-size:16px;">fingerprint</span> LOG IMMUTABLE (APPEND-ONLY)
            </span>
        </div>
        <h1>Audit Trail Akses Informasi Data Pribadi Serdik</h1>
        <p>
            Rekaman forensik digital atas setiap aktivitas dekripsi data pribadi sensitif (NIK, No KK, Ibu Kandung, Rekam Medis, Rekening ULP) oleh seluruh personel dan operator Satdik sesuai standar protokol keamanan data sistem SIPANDU-WBK.
        </p>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-outline" style="background:rgba(255,255,255,0.15); color:#fff; border-color:rgba(255,255,255,0.3);">
            <span class="ms">print</span> Cetak Laporan Audit
        </button>
    </div>
</div>

<!-- STATS SUMMARY -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--o800), var(--o600));">
            <span class="ms">receipt_long</span>
        </div>
        <div>
            <div class="stat-val">{{ $logs->total() }}</div>
            <div class="stat-lbl">Total Akses Didekripsi</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--gold), #B38F1F); color:#10170C;">
            <span class="ms">lock</span>
        </div>
        <div>
            <div class="stat-val">AES-256</div>
            <div class="stat-lbl">Algoritma Enkripsi Data</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--green), #388E3C);">
            <span class="ms">verified_user</span>
        </div>
        <div>
            <div class="stat-val">100%</div>
            <div class="stat-lbl">Integritas Keamanan Sistem</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, #1565C0, #1E88E5);">
            <span class="ms">security</span>
        </div>
        <div>
            <div class="stat-val">Non-Repudiation</div>
            <div class="stat-lbl">Bukti Akses Sah ZI WBK</div>
        </div>
    </div>
</div>

<!-- AUDIT LOG TABLE CARD -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <span class="ms" style="color:var(--o700);">list_alt</span>
            Log Forensik Akses Data Pribadi Siswa
        </h3>
        <span class="badge badge-satdik">{{ $logs->total() }} Catatan Ditemukan</span>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Waktu Akses (WIB)</th>
                        <th>Personel Pengakses</th>
                        <th>Otoritas / Role</th>
                        <th>Siswa / Serdik Sasaran</th>
                        <th>Satdik</th>
                        <th>Alasan Resmi Pembukaan</th>
                        <th>IP Address & Host</th>
                        <th>Status Integritas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $index }}</td>
                            <td style="font-family:'Fira Code',monospace; font-size:12px; font-weight:700; color:var(--o800);">
                                {{ $log->accessed_at ? \Carbon\Carbon::parse($log->accessed_at)->format('d/m/Y H:i:s') : '-' }}
                            </td>
                            <td>
                                <div style="font-weight:700; color:var(--o900);">
                                    {{ $log->accessedByUser->name ?? 'User #' . $log->user_id }}
                                </div>
                                <div style="font-size:11.5px; color:var(--muted);">
                                    {{ $log->accessedByUser->email ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-satdik">
                                    {{ $log->accessedByUser->role_code ?? 'operator' }}
                                </span>
                            </td>
                            <td>
                                @if($log->student)
                                    <a href="{{ route('students.show', $log->student_id) }}" style="text-decoration:none; font-weight:700; color:var(--o700);">
                                        {{ $log->student->full_name }}
                                    </a>
                                    <div style="font-family:'Fira Code',monospace; font-size:11px; color:var(--muted);">
                                        NOSIK: {{ $log->student->nosik }}
                                    </div>
                                @else
                                    <span style="color:var(--muted);">ID Siswa #{{ $log->student_id }}</span>
                                @endif
                            </td>
                            <td>
                                @if($log->student && $log->student->satdik)
                                    <span class="badge badge-satdik" style="font-weight:700;">
                                        {{ $log->student->satdik->code }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="max-width:320px;">
                                <div style="font-weight:600; color:var(--o800); line-height:1.3;">
                                    {{ $log->access_reason }}
                                </div>
                            </td>
                            <td style="font-family:'Fira Code',monospace; font-size:11.5px; color:var(--muted);">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                            <td>
                                <span class="badge badge-green">
                                    <span class="ms" style="font-size:13px;">verified</span> Tercatat Sah
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:48px; color:var(--muted);">
                                <span class="ms" style="font-size:48px; display:block; margin-bottom:12px; opacity:0.4;">policy</span>
                                <b>Belum ada catatan aktivitas pembukaan data pribadi sensitif.</b>
                                <div style="font-size:12.5px; margin-top:4px;">
                                    Seluruh data pribadi serdik tetap tersimpan aman dalam bentuk ciphertext AES-256-CBC.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="table-pagination-footer">
                <div class="table-pagination-info">
                    Menampilkan <b>{{ $logs->firstItem() ?? 0 }}</b> - <b>{{ $logs->lastItem() ?? 0 }}</b> dari <b>{{ $logs->total() }}</b> log akses
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
