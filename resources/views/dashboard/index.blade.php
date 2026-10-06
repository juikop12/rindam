@extends('layouts.app')

@section('title', 'Pusat Komando & Dashboard Eksekutif Data Siswa & Kesehatan')

@section('content')
<!-- KONTEN DASHBOARD (SaaS Style, Clean, Minimalist with Tailwind CSS) -->
<div class="px-2 py-4">

    <!-- HEADER TITLE -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pusat Komando & Dashboard Eksekutif</h1>
            <p class="text-sm text-slate-500 mt-1">Data Siswa & Kesehatan &mdash; {{ auth()->user()?->isOperator() && auth()->user()?->satdik ? (auth()->user()->satdik->code . ' (' . auth()->user()->satdik->name . ')') : 'Rindam III/Siliwangi' }}</p>
        </div>
        
        <!-- Waktu Sistem -->
        <div class="flex items-center gap-2 bg-white border border-slate-200 px-4 py-2 rounded-lg shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-sm font-semibold text-slate-700">{{ now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    <!-- TABS FILTER SATDIK -->
    @if(auth()->check() && !auth()->user()->isPimpinan() && auth()->user()->satdik_id)
        <!-- OPERATOR VIEW: HANYA TAMPILKAN SATDIK OPERATOR (TERKUNCI & TIDAK ADA SATDIK LAIN) -->
        <div class="flex items-center gap-3 mb-6">
            <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-bold bg-slate-900 text-white border border-slate-800 shadow-sm">
                <span class="ms text-[18px] text-amber-400">lock</span>
                <span>Satdik: {{ auth()->user()->satdik?->code }} &mdash; {{ auth()->user()->satdik?->name }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                    Wewenang Satuan Anda (Terkunci)
                </span>
            </div>
        </div>
    @else
        <!-- PIMPINAN / SUPER ADMIN: BISA MEMILIH SELURUH SATDIK -->
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors border {{ is_null($selectedSatdik) ? 'bg-slate-100 text-slate-900 border-slate-300 shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                <span class="ms text-[18px]">list</span>
                Semua Satdik
                <span class="px-2 py-0.5 rounded text-xs bg-slate-200 text-slate-700">{{ \App\Models\Student::withoutGlobalScopes()->count() }}</span>
            </a>
            @foreach($satdiks as $s)
                <a href="{{ route('dashboard', ['satdik_id' => $s->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors border {{ $selectedSatdik?->id === $s->id ? 'bg-slate-100 text-slate-900 border-slate-300 shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    <span class="ms text-[18px]">filter_list</span>
                    {{ $s->code }}
                </a>
            @endforeach
        </div>
    @endif

    <!-- 4 STAT CARDS (Clean Minimalist) -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        <!-- Kekuatan Serdik -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="text-sm font-medium text-slate-500 mb-4 flex items-center gap-2">
                Total Siswa Aktif 
                <span class="ms text-sm text-slate-400">info</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-900 mb-2">{{ number_format($countedActiveStudents) }}</div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded font-semibold">+ {{ $activeStudents }} siap latih</span>
                    <span>&bull; {{ $sickStudents }} dispen</span>
                </div>
            </div>
        </div>

        <!-- Kesiapan Latihan -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="text-sm font-medium text-slate-500 mb-4 flex items-center gap-2">
                Kesiapan Latihan 
                <span class="ms text-sm text-slate-400">fitness_center</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-900 mb-2">{{ $siapLatihPercent }}%</div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded font-semibold">+ {{ $siapLatih }} siap penuh</span>
                    <span>dari {{ $totalHealth }}</span>
                </div>
            </div>
        </div>

        <!-- Perawatan Medis -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="text-sm font-medium text-slate-500 mb-4 flex items-center gap-2">
                Perawatan Poliklinik 
                <span class="ms text-sm text-slate-400">medical_services</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-900 mb-2">{{ number_format($perawatanCount) }}</div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded font-semibold">{{ $berobatJalan }} rawat jalan</span>
                    <span>&bull; {{ $rawatPoliklinik }} inap</span>
                </div>
            </div>
        </div>

        <!-- Rujuk Rumkit -->
        <a href="{{ route('health.index', ['status' => 'Rujuk Rumkit']) }}" class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between hover:bg-slate-50 transition-colors">
            <div class="text-sm font-medium text-slate-500 mb-4 flex items-center gap-2">
                Rujuk Rumah Sakit 
                <span class="ms text-sm text-slate-400">local_hospital</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-900 mb-2">{{ number_format($rujukRumkit) }}</div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-semibold">rujukan tingkat lanjut</span>
                </div>
            </div>
        </a>
    </div>

    <!-- MAIN TABLE SECTION -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-8 overflow-hidden">
        <!-- Table Header & Actions -->
        <div class="px-5 py-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Daftar Serdik Terkini</h2>
                <p class="text-sm text-slate-500 mt-0.5">Catatan mutasi data siswa dan status serdik (Real-Time Feed)</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('students.index', ['action' => 'create']) }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    <span class="ms text-[18px]">add</span> Tambah Siswa Baru
                </a>
                <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    <span class="ms text-[18px]">groups</span> Semua Siswa
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider">
                    <tr>
                        <th class="px-5 py-3 border-b border-slate-200 w-12 text-center">
                            <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                        </th>
                        <th class="px-5 py-3 border-b border-slate-200">Siswa</th>
                        <th class="px-5 py-3 border-b border-slate-200">Pendidikan</th>
                        <th class="px-5 py-3 border-b border-slate-200">Kompi / Ton</th>
                        <th class="px-5 py-3 border-b border-slate-200">Rekam Medis</th>
                        <th class="px-5 py-3 border-b border-slate-200">Status</th>
                        <th class="px-5 py-3 border-b border-slate-200">Stakes</th>
                        <th class="px-5 py-3 border-b border-slate-200 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentStudents as $student)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 text-center align-middle">
                                <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <div class="font-semibold text-slate-900">{{ $student->full_name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $student->nosik }}</div>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <div class="font-medium text-slate-700">{{ $student->satdik?->code }}</div>
                                <div class="text-xs text-slate-500 mt-0.5 truncate max-w-[150px]">{{ $student->educationProgram?->name ?? 'Pendidikan Pembentukan' }}</div>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <div class="font-medium text-slate-700">{{ $student->company ?? '-' }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $student->platoon ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <span class="text-slate-600 font-medium">{{ $student->healthRecord?->daily_health_status ?? 'Tidak ada' }}</span>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                @php
                                    $bgClass = 'bg-slate-100 text-slate-700';
                                    if($student->status === 'Aktif') $bgClass = 'bg-indigo-50 text-indigo-700';
                                    if($student->status === 'Sakit') $bgClass = 'bg-rose-50 text-rose-700';
                                    if(in_array($student->status, ['Lulus', 'Selesai'])) $bgClass = 'bg-emerald-50 text-emerald-700';
                                    if(str_contains($student->status, 'DO')) $bgClass = 'bg-amber-50 text-amber-700';
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold {{ $bgClass }}">
                                    {{ $student->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <div class="flex items-center gap-1.5 text-slate-600 font-medium">
                                    <span class="ms text-[16px] text-amber-400">star</span> 
                                    {{ Str::limit($student->healthRecord?->stakes_grade ?? '-', 8, '') }}
                                </div>
                            </td>
                            <td class="px-5 py-3 align-middle text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('students.show', $student) }}" class="p-1.5 text-slate-400 hover:text-slate-900 transition-colors" title="Lihat Dossier">
                                        <span class="ms">visibility</span>
                                    </a>
                                    <a href="{{ route('health.show', $student) }}" class="p-1.5 text-slate-400 hover:text-emerald-600 transition-colors" title="Rekam Medis">
                                        <span class="ms">medical_services</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-500">
                                Belum ada data serdik yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer / Pagination Controls -->
        <div class="px-5 py-4 border-t border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 text-sm text-slate-500 bg-slate-50">
            <div>Menampilkan <span class="font-semibold text-slate-900">{{ count($recentStudents) }}</span> dari <span class="font-semibold text-slate-900">{{ $totalStudents }}</span> data</div>
            
            <div class="flex gap-1">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-600">&laquo;</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-600">&lsaquo;</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-900 text-white font-semibold">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-600">2</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-600">3</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-600">&rsaquo;</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-600">&raquo;</button>
            </div>
        </div>
    </div>

    @if(auth()->user()?->isSuperAdmin())
    <!-- SECONDARY LINKS (Audit Logs - Khusus Superadmin) -->
    <div class="flex justify-end">
        <a href="{{ route('students.audit-logs') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            Lihat Seluruh Audit Log Keamanan <span class="ms text-[18px]">arrow_forward</span>
        </a>
    </div>
    @endif

</div>
@endsection
