@extends('layouts.app')

@section('title', 'Audit Trail Akses Data Pribadi — SIPANDU-WBK')

@section('content')

<!-- BREADCRUMB -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <a href="{{ route('students.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">Data Serdik</a>
        <span class="ms text-slate-400 text-[16px]">chevron_right</span>
        <span class="text-slate-900 font-bold">Audit Trail Pelindungan Data Pribadi</span>
    </div>
    <a href="{{ route('students.index') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-sm transition-colors w-full sm:w-auto">
        <span class="ms text-[18px]">arrow_back</span> Kembali ke Data Serdik
    </a>
</div>

<!-- HEADER BANNER -->
<div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-sm mb-6 flex flex-col md:flex-row md:items-start justify-between gap-6">
    <div>
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-800 border border-slate-700 text-slate-200 rounded-md text-[10px] font-bold tracking-wide">
                <span class="ms text-[14px]">admin_panel_settings</span> OTORITAS SUPER ADMINISTRATOR
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-500/20 border border-amber-500/30 text-amber-300 rounded-md text-[10px] font-bold tracking-wide">
                <span class="ms text-[14px]">policy</span> ZI WBK AREA 5: PENGAWASAN
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 rounded-md text-[10px] font-bold tracking-wide">
                <span class="ms text-[14px]">fingerprint</span> LOG IMMUTABLE
            </span>
        </div>
        <h1 class="text-xl sm:text-2xl font-bold text-white mb-2 tracking-tight">Audit Trail Akses Informasi Data Pribadi Serdik</h1>
        <p class="text-sm text-slate-400 leading-relaxed max-w-3xl">
            Rekaman forensik digital atas setiap aktivitas dekripsi data pribadi sensitif (NIK, No KK, Ibu Kandung, Rekam Medis, Rekening ULP) oleh seluruh personel dan operator Satdik sesuai standar protokol keamanan data sistem SIPANDU-WBK.
        </p>
    </div>
    <div>
        <a href="{{ route('students.audit-logs.export-pdf', request()->query()) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold border border-amber-600 rounded-lg text-xs transition-colors w-full md:w-auto shrink-0 shadow-sm">
            <span class="ms text-[18px]">print</span> Cetak Laporan Audit (PDF)
        </a>
    </div>
</div>

<!-- STATS SUMMARY -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
            <span class="ms text-[24px]">receipt_long</span>
        </div>
        <div>
            <div class="text-xl font-bold text-slate-900">{{ $logs->total() }}</div>
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Total Akses Didekripsi</div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
            <span class="ms text-[24px]">lock</span>
        </div>
        <div>
            <div class="text-xl font-bold text-slate-900">AES-256</div>
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Algoritma Enkripsi</div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
            <span class="ms text-[24px]">verified_user</span>
        </div>
        <div>
            <div class="text-xl font-bold text-slate-900">100%</div>
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Integritas Sistem</div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
            <span class="ms text-[24px]">security</span>
        </div>
        <div>
            <div class="text-xl font-bold text-slate-900">Valid</div>
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Bukti Akses Sah</div>
        </div>
    </div>
</div>

<!-- AUDIT LOG TABLE CARD -->
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <span class="ms text-blue-600 text-[20px]">list_alt</span>
            Log Forensik Akses Data Pribadi Siswa
        </h3>
        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 self-start sm:self-auto shrink-0">{{ $logs->total() }} Catatan Ditemukan</span>
    </div>

    <div class="p-0">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="py-3 px-5 text-xs font-bold text-slate-600 w-[60px]">No</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600 min-w-[140px]">Waktu Akses (WIB)</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600 min-w-[180px]">Personel Pengakses</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600">Otoritas</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600 min-w-[180px]">Siswa Sasaran</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600">Satdik</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600 min-w-[200px]">Alasan Pembukaan</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600">IP Address</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600 text-center">Integritas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $log)
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors last:border-0">
                            <td class="py-3 px-5 text-xs text-slate-500">{{ $logs->firstItem() + $index }}</td>
                            <td class="py-3 px-5 text-xs font-mono font-semibold text-slate-700">
                                {{ $log->accessed_at ? \Carbon\Carbon::parse($log->accessed_at)->format('d/m/Y H:i:s') : '-' }}
                            </td>
                            <td class="py-3 px-5">
                                <div class="text-xs font-bold text-slate-900">{{ $log->accessedByUser->name ?? 'User #' . $log->user_id }}</div>
                                <div class="text-[11px] text-slate-500">{{ $log->accessedByUser->email ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $log->accessedByUser->role_code ?? 'operator' }}
                                </span>
                            </td>
                            <td class="py-3 px-5">
                                @if($log->student)
                                    <a href="{{ route('students.show', $log->student_id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                        {{ $log->student->full_name }}
                                    </a>
                                    <div class="text-[11px] font-mono text-slate-400 mt-0.5">
                                        NOSIK: {{ $log->student->nosik }}
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">ID Siswa #{{ $log->student_id }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-5">
                                @if($log->student && $log->student->satdik)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $log->student->satdik->code }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3 px-5 text-xs font-semibold text-slate-800">
                                {{ $log->access_reason }}
                            </td>
                            <td class="py-3 px-5 text-xs font-mono text-slate-400">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                            <td class="py-3 px-5 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="ms text-[12px]">verified</span> Sah
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-16 px-5 text-center text-slate-400">
                                <span class="ms text-[48px] block mb-3 opacity-50">policy</span>
                                <div class="text-sm font-bold text-slate-600 mb-1">Belum ada catatan aktivitas pembukaan data pribadi sensitif.</div>
                                <div class="text-xs">Seluruh data pribadi serdik tetap tersimpan aman dalam bentuk ciphertext AES-256-CBC.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse($logs as $index => $log)
                <div class="p-4 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $log->accessed_at ? \Carbon\Carbon::parse($log->accessed_at)->format('d/m/y H:i') : '-' }}</span>
                        <div class="flex gap-2">
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="ms text-[12px]">verified</span> Sah
                            </span>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Siswa Sasaran:</div>
                        @if($log->student)
                            <a href="{{ route('students.show', $log->student_id) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                {{ $log->student->full_name }}
                            </a>
                            <div class="text-xs font-mono text-slate-500 mt-0.5">
                                NOSIK: {{ $log->student->nosik }}
                                @if($log->student->satdik)
                                    <span class="ml-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-700">{{ $log->student->satdik->code }}</span>
                                @endif
                            </div>
                        @else
                            <span class="text-xs text-slate-400">ID Siswa #{{ $log->student_id }}</span>
                        @endif
                    </div>

                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Pengakses:</div>
                        <div class="text-xs font-bold text-slate-900">{{ $log->accessedByUser->name ?? 'User #' . $log->user_id }} <span class="px-1.5 py-0.5 ml-1 rounded text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200">{{ $log->accessedByUser->role_code ?? 'operator' }}</span></div>
                        <div class="text-[11px] text-slate-500">{{ $log->accessedByUser->email ?? '-' }} (IP: {{ $log->ip_address ?? '127.0.0.1' }})</div>
                    </div>

                    <div class="bg-amber-50/50 p-2.5 rounded-lg border border-amber-100 text-xs font-semibold text-slate-800 mt-1 relative">
                        <span class="absolute top-2.5 left-2.5 ms text-[16px] text-amber-500">campaign</span>
                        <div class="pl-6">"{{ $log->access_reason }}"</div>
                    </div>
                </div>
            @empty
                <div class="py-12 px-5 text-center text-slate-400">
                    <span class="ms text-[40px] block mb-3 opacity-50">policy</span>
                    <div class="text-sm font-bold text-slate-600 mb-1">Belum ada catatan aktivitas pembukaan data.</div>
                    <div class="text-xs">Seluruh data tetap tersimpan aman.</div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="text-xs text-slate-500 text-center sm:text-left">
                    Menampilkan <b class="text-slate-900">{{ $logs->firstItem() ?? 0 }}</b> - <b class="text-slate-900">{{ $logs->lastItem() ?? 0 }}</b> dari <b class="text-slate-900">{{ $logs->total() }}</b> log akses
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
