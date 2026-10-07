@extends('layouts.app')

@section('title', 'Buku Induk & Pengolahan Data Siswa — SIPANDU-WBK')

@section('content')
<div class="px-2 py-4">

    <!-- HEADER TITLE & ACTIONS -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Buku Induk & Pengolahan Data Siswa</h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="ms text-[13px]">shield</span> AES-256-CBC
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    <span class="ms text-[13px]">lock</span> SIPANDU-WBK
                </span>
            </div>
            <p class="text-sm text-slate-500">Pengelolaan prajurit siswa terpartisi per Satdik & Program Pendidikan — Rindam III/Siliwangi</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full md:w-auto">
            <a href="{{ route('students.export-pdf', request()->query()) }}" target="_blank" class="inline-flex justify-center items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2.5 sm:py-2 rounded-xl text-sm font-semibold transition-colors shadow-sm w-full sm:w-auto">
                <span class="ms text-[18px]">print</span> Cetak Nominatif (PDF)
            </a>
            @if(auth()->user()?->canModifyData())
            <a href="{{ route('students.import', ['satdik_id' => $selectedSatdikId]) }}" class="inline-flex justify-center items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2.5 sm:py-2 rounded-xl text-sm font-semibold transition-colors shadow-sm w-full sm:w-auto">
                <span class="ms text-[18px]">upload_file</span> Impor Format Excel
            </a>
            <button type="button" onclick="openCreateStudentModal()" class="inline-flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 sm:py-2 rounded-xl text-sm font-semibold transition-colors shadow-sm shadow-blue-200 w-full sm:w-auto">
                <span class="ms text-[18px]">person_add</span> Tambah Siswa Baru
            </button>
            @endif
        </div>
    </div>

    <!-- NOTIFIKASI ERROR VALIDASI -->
    @if ($errors->any())
        <div class="mb-6 flex items-start gap-3 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">
            <span class="ms text-[22px] text-rose-500 shrink-0">error</span>
            <div>
                <b class="font-semibold text-rose-900">Terdapat kesalahan penginputan data:</b>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- NOTIFIKASI PERINGATAN IMPOR EXCEL -->
    @if(session('import_warnings'))
        <div class="mb-6 flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm">
            <span class="ms text-[22px] text-amber-500 shrink-0">warning</span>
            <div>
                <b class="font-semibold text-amber-900">Catatan / Peringatan Penginputan Excel:</b>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-amber-700">
                    @foreach (session('import_warnings') as $w)
                        <li>{{ $w }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- DROPDOWN FILTER SATDIK -->
    <div class="relative w-full sm:w-80 mb-6 z-20" x-data="{ open: false }" @click.outside="open = false">
        <!-- Dropdown Toggle Button -->
        <button type="button" @click="open = !open" 
                class="w-full flex items-center justify-between gap-3 px-4 py-3 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 hover:border-slate-300 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-600/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <span class="ms text-[24px]">domain</span>
                </div>
                <div class="text-left flex flex-col overflow-hidden">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Satuan Pendidikan</span>
                    <span class="text-sm font-bold text-slate-900 truncate">
                        {{ $selectedSatdikId ? ($satdiks->firstWhere('id', $selectedSatdikId)->code ?? 'Semua Satdik') : 'Semua Satdik Rindam' }}
                    </span>
                </div>
            </div>
            <span class="ms text-slate-400 transition-transform duration-300 shrink-0" :class="open ? 'rotate-180' : ''">expand_more</span>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             style="display: none;"
             class="absolute top-full left-0 mt-2 w-full sm:w-[350px] bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden">
            
            <a href="{{ route('students.index', array_merge(request()->except(['satdik_id', 'page']))) }}" 
               class="flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 transition-colors {{ empty($selectedSatdikId) ? 'bg-blue-50/50' : '' }} border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ empty($selectedSatdikId) ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-500' }}">
                        <span class="ms text-[18px]">list</span>
                    </div>
                    <div>
                        <div class="text-sm font-bold {{ empty($selectedSatdikId) ? 'text-blue-900' : 'text-slate-700' }}">Tampilkan Semua Satdik</div>
                        <div class="text-[11px] text-slate-500">Seluruh Rindam III/Siliwangi</div>
                    </div>
                </div>
                @if(empty($selectedSatdikId))
                    <span class="ms text-blue-600 text-[20px]">check_circle</span>
                @else
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">{{ $stats['overall_total'] }} Total</span>
                @endif
            </a>

            <div>
                @foreach($satdiks as $satdik)
                    @php
                        $satdikCounted = $satdik->counted_students ?? $satdik->students()->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count();
                        $isActive = ($selectedSatdikId == $satdik->id);
                    @endphp
                    <a href="{{ route('students.index', array_merge(request()->except(['page']), ['satdik_id' => $satdik->id])) }}" 
                       class="flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 transition-colors {{ $isActive ? 'bg-blue-50/50' : '' }} border-b border-slate-50 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $isActive ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-500' }}">
                                <span class="ms text-[18px]">account_balance</span>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold {{ $isActive ? 'text-blue-900' : 'text-slate-700' }} truncate">{{ $satdik->code }}</div>
                                <div class="text-[11px] text-slate-500 whitespace-normal leading-tight" title="{{ $satdik->name }}">{{ $satdik->name }}</div>
                            </div>
                        </div>
                        @if($isActive)
                            <span class="ms text-blue-600 text-[20px] shrink-0">check_circle</span>
                        @else
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold shrink-0">{{ $satdikCounted }} Aktif</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 4 STAT CARDS (Clean Minimalist Dashboard Style) -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-5 mb-8">
        <!-- Siswa Aktif Terhitung -->
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 sm:p-5 shadow-sm flex flex-col justify-between">
            <div class="text-xs sm:text-sm font-medium text-slate-500 mb-2 sm:mb-4 flex items-center justify-between">
                <span class="truncate">Aktif Terhitung</span>
                <span class="ms text-sm sm:text-[18px] text-blue-500">how_to_reg</span>
            </div>
            <div>
                <div class="text-xl sm:text-3xl font-bold text-slate-900 mb-1 sm:mb-2">{{ number_format($stats['overall_counted']) }}</div>
                <div class="flex flex-wrap items-center gap-1 sm:gap-2 text-[10px] sm:text-xs text-slate-500">
                    <span class="bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded font-semibold">+ {{ $stats['overall_active'] }} aktif</span>
                    <span>&bull; {{ $stats['overall_sick'] }} sakit</span>
                </div>
            </div>
        </div>

        <!-- Arsip Siswa Selesai -->
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 sm:p-5 shadow-sm flex flex-col justify-between">
            <div class="text-xs sm:text-sm font-medium text-slate-500 mb-2 sm:mb-4 flex items-center justify-between">
                <span class="truncate">Arsip Selesai</span>
                <span class="ms text-sm sm:text-[18px] text-emerald-500">archive</span>
            </div>
            <div>
                <div class="text-xl sm:text-3xl font-bold text-slate-900 mb-1 sm:mb-2">{{ number_format($stats['overall_archived']) }}</div>
                <div class="flex flex-wrap items-center gap-1 sm:gap-2 text-[10px] sm:text-xs text-slate-500">
                    <span class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded font-semibold">{{ $stats['overall_finished'] }} lulus</span>
                    <span class="hidden sm:inline">&bull; tak terhitung</span>
                </div>
            </div>
        </div>

        <!-- Program Diklat -->
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 sm:p-5 shadow-sm flex flex-col justify-between">
            <div class="text-xs sm:text-sm font-medium text-slate-500 mb-2 sm:mb-4 flex items-center justify-between">
                <span class="truncate">Program Diklat</span>
                <span class="ms text-sm sm:text-[18px] text-amber-500">school</span>
            </div>
            <div>
                <div class="text-xl sm:text-3xl font-bold text-slate-900 mb-1 sm:mb-2">{{ count($stats['program_list'] ?? []) }}</div>
                <div class="flex flex-wrap items-center gap-1 sm:gap-2 text-[10px] sm:text-xs text-slate-500">
                    <span class="bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded font-semibold">TA 2026</span>
                    <span class="hidden sm:inline">DIKMABA &bull; DIKMATA</span>
                </div>
            </div>
        </div>

        <!-- Total Rekam Serdik -->
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 sm:p-5 shadow-sm flex flex-col justify-between">
            <div class="text-xs sm:text-sm font-medium text-slate-500 mb-2 sm:mb-4 flex items-center justify-between">
                <span class="truncate">Total Rekam</span>
                <span class="ms text-sm sm:text-[18px] text-indigo-500">domain</span>
            </div>
            <div>
                <div class="text-xl sm:text-3xl font-bold text-slate-900 mb-1 sm:mb-2">{{ number_format($stats['overall_total']) }}</div>
                <div class="flex flex-wrap items-center gap-1 sm:gap-2 text-[10px] sm:text-xs text-slate-500">
                    <span class="bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-semibold">5 Satdik</span>
                    <span class="hidden sm:inline">Rindam III/Slw</span>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION REKAPITULASI PROGRAM PENDIDIKAN -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                    <span class="ms text-[20px]">layers</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                        Rekapitulasi Kekuatan per Program Pendidikan
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Klik salah satu program untuk menyaring daftar prajurit siswa di bawah
                    </p>
                </div>
            </div>
            @if($selectedProgramId)
                <a href="{{ route('students.index', array_merge(request()->except(['program_id', 'page']))) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                    <span class="ms text-[16px]">close</span> Tampilkan Semua Program
                </a>
            @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3">
            @foreach($stats['program_list'] as $prog)
                @php
                    $isProgActive = ($selectedProgramId == $prog['id']);
                @endphp
                <a href="{{ route('students.index', array_merge(request()->except(['page']), ['program_id' => $isProgActive ? null : $prog['id']])) }}"
                   class="group block p-3.5 rounded-xl border transition-all {{ $isProgActive ? 'border-slate-900 bg-slate-50 ring-2 ring-slate-900 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50' }}">
                    <div class="flex flex-col gap-1.5 mb-3">
                        <span class="font-bold text-[11px] sm:text-xs {{ $isProgActive ? 'text-slate-900' : 'text-slate-800 group-hover:text-slate-900' }} leading-snug line-clamp-2 min-h-[32px]">
                            {{ $prog['name'] }}
                        </span>
                        <span class="w-fit px-2 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold bg-slate-100 text-slate-700 shrink-0">
                            {{ $prog['satdik_code'] }}
                        </span>
                    </div>
                    <div class="flex flex-col gap-1.5 pt-2 border-t border-slate-100 text-xs">
                        <span class="inline-flex items-center gap-1.5 text-emerald-700 font-semibold text-[10px] sm:text-[11px]" title="Terhitung dalam kekuatan pendidikan">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="truncate"><b>{{ $prog['counted_students'] }}</b> <span class="opacity-80">Terhitung</span></span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-slate-500 font-medium text-[10px] sm:text-[11px]" title="Selesai Pendidikan (Arsip)">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                            <span class="truncate"><b>{{ $prog['archived_students'] }}</b> <span class="opacity-80">Arsip</span></span>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- SEGMENTED TABS: SEMUA vs SISWA AKTIF vs ARSIP SELESAI -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col sm:flex-row p-1 bg-slate-100 border border-slate-200 rounded-xl gap-1">
            <a href="{{ route('students.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'all'])) }}"
               class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $tab == 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                <span class="ms text-[16px]">people</span> Semua Serdik
                <span class="px-2 py-0.5 rounded text-[11px] {{ $tab == 'all' ? 'bg-slate-100 text-slate-800' : 'bg-slate-200 text-slate-600' }}">{{ $stats['overall_total'] }}</span>
            </a>
            <a href="{{ route('students.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'aktif'])) }}"
               class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $tab == 'aktif' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                <span class="ms text-[16px] text-emerald-600">how_to_reg</span> Siswa Aktif (Terhitung)
                <span class="px-2 py-0.5 rounded text-[11px] {{ $tab == 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $stats['overall_counted'] }}</span>
            </a>
            <a href="{{ route('students.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'arsip'])) }}"
               class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $tab == 'arsip' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                <span class="ms text-[16px] text-slate-500">archive</span> Arsip Siswa Selesai
                <span class="px-2 py-0.5 rounded text-[11px] {{ $tab == 'arsip' ? 'bg-slate-200 text-slate-800' : 'bg-slate-200 text-slate-600' }}">{{ $stats['overall_archived'] }}</span>
            </a>
        </div>

        @if($selectedProgram)
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-xs font-medium">
                <span class="ms text-[16px] text-amber-600">filter_alt</span>
                <span>Filter Program: <b>{{ $selectedProgram->name }} ({{ $selectedProgram->satdik->code }})</b></span>
                <a href="{{ route('students.index', array_merge(request()->except(['program_id', 'page']))) }}" class="ml-1 text-amber-700 hover:text-red-600 font-bold" title="Hapus filter">✕</a>
            </div>
        @endif
    </div>

    <!-- MAIN TABLE SECTION -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-8 overflow-hidden">
        <!-- Table Header & Actions/Filter -->
        <div class="px-5 py-4 border-b border-slate-200 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <div class="min-w-0">
                <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2 truncate">
                    <span class="ms text-slate-700 text-[22px] shrink-0">badge</span>
                    <span>Daftar Peserta Didik {{ $selectedSatdik ? '— ' . $selectedSatdik->name : 'Seluruh Satdik Rindam' }}</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5 truncate">Buku induk data prajurit siswa dengan proteksi enkripsi data pribadi</p>
            </div>

            <!-- SEARCH & FILTER FORM -->
            <form method="GET" action="{{ route('students.index') }}" class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 w-full xl:w-auto mt-4 xl:mt-0">
                @if($tab && $tab !== 'all')
                    <input type="hidden" name="tab" value="{{ $tab }}">
                @endif
                @if($selectedSatdikId)
                    <input type="hidden" name="satdik_id" value="{{ $selectedSatdikId }}">
                @endif

                <!-- PILIH PROGRAM PENDIDIKAN -->
                <select name="program_id" class="col-span-1 w-full sm:w-36 h-10 sm:h-9 bg-white border border-slate-200 text-slate-700 text-xs font-medium rounded-lg px-2.5 focus:ring-2 focus:ring-slate-900 focus:outline-none truncate" onchange="this.form.submit()">
                    <option value="">Semua Program</option>
                    @foreach($availablePrograms as $ap)
                        <option value="{{ $ap->id }}" {{ $selectedProgramId == $ap->id ? 'selected' : '' }}>
                            {{ $ap->name }}
                        </option>
                    @endforeach
                </select>

                <!-- PILIH STATUS DETAIL -->
                <select name="status" class="col-span-1 w-full sm:w-32 h-10 sm:h-9 bg-white border border-slate-200 text-slate-700 text-xs font-medium rounded-lg px-2.5 focus:ring-2 focus:ring-slate-900 focus:outline-none" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ $status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Sakit" {{ $status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="Dinas Luar" {{ $status == 'Dinas Luar' ? 'selected' : '' }}>Dinas Luar</option>
                    <option value="Selesai" {{ $status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Lulus" {{ $status == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="DO / Dikeluarkan" {{ $status == 'DO / Dikeluarkan' ? 'selected' : '' }}>DO</option>
                </select>

                <div class="relative col-span-2 sm:col-span-1 w-full sm:w-40">
                    <span class="ms text-slate-400 text-[18px] absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
                    <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari Nosis, Nama..." class="w-full h-10 sm:h-9 pl-8 pr-2.5 border border-slate-200 rounded-lg text-xs text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                </div>

                <div class="col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 sm:flex-none h-10 sm:h-9 bg-slate-900 hover:bg-slate-800 text-white px-4 rounded-lg text-xs font-semibold transition-colors flex items-center justify-center">
                        Filter
                    </button>
                    @if($keyword || $status || $selectedProgramId || $selectedSatdikId || ($tab && $tab !== 'all'))
                        <a href="{{ route('students.index') }}" class="h-10 sm:h-9 text-xs font-medium text-slate-500 hover:text-slate-800 px-3 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Desktop Table View (Hidden on mobile < md) -->
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider">
                    <tr>
                        <th class="px-4 py-3 border-b border-slate-200 w-12 text-center">No</th>
                        <th class="px-5 py-3 border-b border-slate-200">Siswa / Serdik</th>
                        <th class="px-4 py-3 border-b border-slate-200">Satuan Pendidikan</th>
                        <th class="px-4 py-3 border-b border-slate-200">Program & Ton</th>
                        <th class="px-4 py-3 border-b border-slate-200">Kodam / Kodim Asal</th>
                        <th class="px-4 py-3 border-b border-slate-200">Status</th>
                        <th class="px-4 py-3 border-b border-slate-200 text-center">Aksi & Kelola Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $index => $student)
                        @php
                            $isSakit = ($student->status === 'Sakit' || str_contains($student->status, 'Sakit'));
                        @endphp
                        <tr class="transition-colors {{ $isSakit ? 'bg-red-50/80 border-l-4 border-l-red-500 hover:bg-red-100/80' : 'border-l-4 border-l-transparent hover:bg-slate-50' }}">
                            <td class="px-4 py-3.5 text-center align-middle text-xs font-medium text-slate-500">
                                {{ $students->firstItem() + $index }}
                            </td>
                            <td class="px-5 py-3.5 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center border border-slate-200 shrink-0">
                                        {{ substr($student->full_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('students.show', $student) }}" class="font-semibold text-slate-900 hover:underline block leading-tight">
                                            {{ $student->full_name }}
                                        </a>
                                        <div class="font-mono text-xs text-slate-500 mt-0.5">{{ $student->nosik }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $student->satdik->code }}
                                </span>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $student->satdik->location ?? 'Ksatrian Rindam' }}</div>
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                <div class="font-medium text-slate-800 text-xs truncate max-w-[180px]">{{ $student->educationProgram->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $student->classroom->name ?? ($student->company ? $student->company . ' ' . $student->platoon : 'Belum Ditentukan') }}</div>
                            </td>
                            <td class="px-4 py-3.5 align-middle text-xs font-medium text-slate-700">
                                {{ $student->origin_military_unit ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5 align-middle whitespace-nowrap">
                                @php
                                    $bgClass = 'bg-slate-100 text-slate-700';
                                    if($student->status === 'Aktif') $bgClass = 'bg-indigo-50 text-indigo-700';
                                    if($student->status === 'Sakit') $bgClass = 'bg-rose-100 text-rose-700 font-bold';
                                    if($student->status === 'Dinas Luar') $bgClass = 'bg-blue-50 text-blue-700';
                                    if(in_array($student->status, ['Lulus', 'Selesai'])) $bgClass = 'bg-emerald-50 text-emerald-700';
                                    if(str_contains($student->status, 'DO')) $bgClass = 'bg-amber-50 text-amber-700';
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold {{ $bgClass }}">
                                    {{ $student->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 align-middle text-center whitespace-nowrap">
                                @php
                                    $studentJson = [
                                        'id' => $student->id,
                                        'satdik_id' => $student->satdik_id,
                                        'education_program_id' => $student->education_program_id,
                                        'classroom_id' => $student->classroom_id,
                                        'nosik' => $student->nosik,
                                        'full_name' => $student->full_name,
                                        'student_rank' => $student->student_rank,
                                        'company' => $student->company,
                                        'platoon' => $student->platoon,
                                        'origin_military_unit' => $student->origin_military_unit,
                                        'birth_place' => $student->birth_place,
                                        'birth_date' => $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('Y-m-d') : '',
                                        'gender' => $student->gender,
                                        'religion' => $student->religion,
                                        'blood_type' => $student->blood_type,
                                        'status' => $student->status,
                                        'mother_name' => $student->personalProfile?->mother_name ?? '',
                                        'emergency_contact_phone' => $student->personalProfile?->emergency_contact_phone ?? '',
                                        'home_address' => $student->personalProfile?->home_address ?? '',
                                        'height_cm' => $student->healthRecord?->height_cm ?? '',
                                        'weight_kg' => $student->healthRecord?->weight_kg ?? '',
                                        'blood_pressure' => $student->healthRecord?->blood_pressure ?? '',
                                        'daily_health_status' => $student->healthRecord?->daily_health_status ?? 'Siap Latih',
                                        'stakes_grade' => $student->healthRecord?->stakes_grade ?? 'Stakes I (Sangat Baik)',
                                        'doctor_notes' => $student->healthRecord?->doctor_notes ?? '',
                                    ];
                                @endphp
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('health.show', $student) }}" class="p-1.5 text-slate-400 hover:text-emerald-600 rounded-lg hover:bg-slate-100 transition-colors" title="Rekam Medis">
                                        <span class="ms text-[18px]">medical_services</span>
                                    </a>
                                    <a href="{{ route('students.show', $student) }}" class="p-1.5 text-slate-400 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors" title="Lihat Dossier">
                                        <span class="ms text-[18px]">visibility</span>
                                    </a>
                                    <button type="button" class="p-1.5 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openStatusModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', '{{ $student->status }}')" title="Ubah Status">
                                        <span class="ms text-[18px]">swap_horiz</span>
                                    </button>
                                    <button type="button" class="p-1.5 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openEditStudentModal(this)" data-student="{{ json_encode($studentJson) }}" title="Edit Data">
                                        <span class="ms text-[18px]">edit</span>
                                    </button>
                                    <button type="button" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openDeleteStudentModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', '{{ $student->nosik }}', '{{ addslashes($student->satdik->name ?? '') }}')" title="Hapus Data">
                                        <span class="ms text-[18px]">delete</span>
                                    </button>
                                    <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white transition-colors ml-1 shadow-sm shadow-blue-200" onclick="openRevealModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', '{{ $student->nosik }}')" title="Buka Data Terproteksi">
                                        <span class="ms text-[14px]">key</span> Buka
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center text-slate-500">
                                <span class="ms text-[44px] text-slate-300 block mb-2">folder_off</span>
                                <div class="font-bold text-slate-800 text-base">Tidak ada data peserta didik pada kriteria filter ini.</div>
                                <p class="text-xs text-slate-400 mt-1">Coba ubah filter Satdik, Program Pendidikan, atau kategori tab Aktif/Arsip di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (Visible on mobile < md) -->
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse($students as $index => $student)
                @php
                    $isSakit = ($student->status === 'Sakit' || str_contains($student->status, 'Sakit'));
                    $bgClass = 'bg-slate-100 text-slate-700';
                    if($student->status === 'Aktif') $bgClass = 'bg-indigo-50 text-indigo-700';
                    if($student->status === 'Sakit') $bgClass = 'bg-rose-100 text-rose-700 font-bold';
                    if($student->status === 'Dinas Luar') $bgClass = 'bg-blue-50 text-blue-700';
                    if(in_array($student->status, ['Lulus', 'Selesai'])) $bgClass = 'bg-emerald-50 text-emerald-700';
                    if(str_contains($student->status, 'DO')) $bgClass = 'bg-amber-50 text-amber-700';

                    $studentJson = [
                        'id' => $student->id,
                        'satdik_id' => $student->satdik_id,
                        'education_program_id' => $student->education_program_id,
                        'classroom_id' => $student->classroom_id,
                        'nosik' => $student->nosik,
                        'full_name' => $student->full_name,
                        'student_rank' => $student->student_rank,
                        'company' => $student->company,
                        'platoon' => $student->platoon,
                        'origin_military_unit' => $student->origin_military_unit,
                        'birth_place' => $student->birth_place,
                        'birth_date' => $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('Y-m-d') : '',
                        'gender' => $student->gender,
                        'religion' => $student->religion,
                        'blood_type' => $student->blood_type,
                        'status' => $student->status,
                        'mother_name' => $student->personalProfile?->mother_name ?? '',
                        'emergency_contact_phone' => $student->personalProfile?->emergency_contact_phone ?? '',
                        'home_address' => $student->personalProfile?->home_address ?? '',
                        'height_cm' => $student->healthRecord?->height_cm ?? '',
                        'weight_kg' => $student->healthRecord?->weight_kg ?? '',
                        'blood_pressure' => $student->healthRecord?->blood_pressure ?? '',
                        'daily_health_status' => $student->healthRecord?->daily_health_status ?? 'Siap Latih',
                        'stakes_grade' => $student->healthRecord?->stakes_grade ?? 'Stakes I (Sangat Baik)',
                        'doctor_notes' => $student->healthRecord?->doctor_notes ?? '',
                    ];
                @endphp
                <div class="p-4 transition-colors {{ $isSakit ? 'bg-red-50/80 border-l-4 border-l-red-500' : 'border-l-4 border-l-transparent bg-white hover:bg-slate-50' }}">
                    <!-- Card Top: Avatar, Name, No/Nosik, Status -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center border border-slate-200 shrink-0">
                                {{ substr($student->full_name, 0, 2) }}
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('students.show', $student) }}" class="font-bold text-sm text-slate-900 hover:text-blue-600 block leading-tight truncate">
                                    {{ $student->full_name }}
                                </a>
                                <div class="flex items-center gap-1.5 font-mono text-[11px] text-slate-500 mt-1">
                                    <span class="text-slate-400">#{{ $students->firstItem() + $index }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $student->nosik }}</span>
                                </div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold shrink-0 {{ $bgClass }}">
                            {{ $student->status }}
                        </span>
                    </div>

                    <!-- Card Body Details -->
                    <div class="grid grid-cols-2 gap-2 text-xs mb-3 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Satdik</span>
                            <span class="font-semibold text-slate-800">{{ $student->satdik->code }}</span>
                            <span class="text-[10px] text-slate-400 block truncate">{{ $student->satdik->location ?? 'Ksatrian Rindam' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Program & Ton</span>
                            <span class="font-semibold text-slate-800 truncate block">{{ $student->educationProgram->name ?? '-' }}</span>
                            <span class="text-[10px] text-slate-400 block truncate">{{ $student->classroom->name ?? ($student->company ? $student->company . ' ' . $student->platoon : 'Belum Ditentukan') }}</span>
                        </div>
                        <div class="col-span-2 pt-1.5 border-t border-slate-200/60 flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Kodam / Kodim:</span>
                            <span class="font-medium text-slate-700">{{ $student->origin_military_unit ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Card Action Buttons -->
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100">
                        <div class="flex flex-wrap items-center gap-1">
                            <a href="{{ route('health.show', $student) }}" class="p-1.5 text-slate-400 hover:text-emerald-600 rounded-lg hover:bg-slate-100 transition-colors" title="Rekam Medis">
                                <span class="ms text-[18px]">medical_services</span>
                            </a>
                            <a href="{{ route('students.show', $student) }}" class="p-1.5 text-slate-400 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors" title="Lihat Dossier">
                                <span class="ms text-[18px]">visibility</span>
                            </a>
                            <button type="button" class="p-1.5 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openStatusModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', '{{ $student->status }}')" title="Ubah Status">
                                <span class="ms text-[18px]">swap_horiz</span>
                            </button>
                            <button type="button" class="p-1.5 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openEditStudentModal(this)" data-student="{{ json_encode($studentJson) }}" title="Edit Data">
                                <span class="ms text-[18px]">edit</span>
                            </button>
                            <button type="button" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openDeleteStudentModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', '{{ $student->nosik }}', '{{ addslashes($student->satdik->name ?? '') }}')" title="Hapus Data">
                                <span class="ms text-[18px]">delete</span>
                            </button>
                        </div>
                        <button type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white transition-colors shadow-sm shadow-blue-200" onclick="openRevealModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', '{{ $student->nosik }}')" title="Buka Data Terproteksi">
                            <span class="ms text-[14px]">key</span> Buka
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500">
                    <span class="ms text-[44px] text-slate-300 block mb-2">folder_off</span>
                    <div class="font-bold text-slate-800 text-base">Tidak ada data peserta didik pada kriteria filter ini.</div>
                    <p class="text-xs text-slate-400 mt-1">Coba ubah filter Satdik, Program Pendidikan, atau kategori tab Aktif/Arsip di atas.</p>
                </div>
            @endforelse
        </div>

        <!-- Table Footer / Pagination Controls -->
        <div class="px-5 py-4 border-t border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 text-sm text-slate-500 bg-slate-50">
            <div>
                Menampilkan <span class="font-semibold text-slate-900">{{ $students->firstItem() ?? 0 }}</span> - <span class="font-semibold text-slate-900">{{ $students->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-900">{{ $students->total() }}</span> serdik
                @if($tab === 'aktif')
                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded ml-1">Siswa Aktif Terhitung</span>
                @elseif($tab === 'arsip')
                    <span class="text-xs font-semibold text-slate-700 bg-slate-200 px-2 py-0.5 rounded ml-1">Arsip Siswa Selesai</span>
                @endif
            </div>
            
            <div>
                {{ $students->links() }}
            </div>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODALS                                                                    -->
<!-- ========================================================================= -->

@if(auth()->user()?->canModifyData())
<!-- 1. MODAL UBAH STATUS SERDIK (AKTIF vs ARSIP SELESAI) -->
<div class="modal-overlay" id="statusModal">
    <div class="modal-card max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="ms text-amber-400 text-[22px]">swap_horiz</span>
                <b class="text-sm font-bold tracking-wide">Ubah Status Serdik & Pengarsipan</b>
            </div>
            <button type="button" onclick="closeStatusModal()" class="text-slate-400 hover:text-white text-xl leading-none">&times;</button>
        </div>

        <form id="statusForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="p-6">
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 mb-4">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Peserta Didik:</div>
                    <div id="statusModalStudentName" class="text-sm font-bold text-slate-800 mt-0.5">-</div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pilih Status Baru Serdik: <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="statusModalSelect" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:ring-slate-900" onchange="updateStatusExplanation(this.value)">
                        <optgroup label="── STATUS TERHITUNG (PENDIDIKAN BERJALAN) ──">
                            <option value="Aktif">🟢 Aktif (Sehat — Terhitung)</option>
                            <option value="Sakit">🟡 Sakit (Dispen Medis — Terhitung)</option>
                            <option value="Dinas Luar">🔵 Dinas Luar (Terhitung)</option>
                        </optgroup>
                        <optgroup label="── STATUS ARSIP (TIDAK TERHITUNG LAGI) ──">
                            <option value="Selesai">📁 Selesai (Tamat Pendidikan — Masuk Arsip)</option>
                            <option value="Lulus">🎓 Lulus (Alumni — Masuk Arsip)</option>
                            <option value="DO / Dikeluarkan">🔴 DO / Dikeluarkan (Keluar — Masuk Arsip)</option>
                        </optgroup>
                    </select>
                </div>

                <div id="statusExplanation" class="p-3.5 bg-slate-50 border-l-4 border-emerald-500 rounded-lg text-xs text-slate-600 leading-relaxed">
                    <!-- Diperbarui lewat JavaScript -->
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2.5">
                <button type="button" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors" onclick="closeStatusModal()">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors">
                    <span class="ms text-[16px]">save</span> Simpan Perubahan Status
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- 2. MODAL BUKA DATA PRIBADI TERPROTEKSI (SIPANDU-WBK) -->
<div class="modal-overlay" id="revealModal">
    <div class="modal-card max-w-xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="ms text-amber-400 text-[22px]">verified_user</span>
                <b class="text-sm font-bold tracking-wide">Akses Data Pribadi Terproteksi (SIPANDU-WBK)</b>
            </div>
            <button type="button" onclick="closeRevealModal()" class="text-slate-400 hover:text-white text-xl leading-none">&times;</button>
        </div>

        <div class="p-6" id="modalFormSection">
            <div class="mb-4 flex items-start gap-2.5 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs leading-relaxed">
                <span class="ms text-amber-600 text-[20px] shrink-0">shield</span>
                <div>
                    <b>Pemberitahuan Keamanan Sistem:</b> Sesuai standar operasional keamanan data SIPANDU-WBK, setiap pembukaan data pribadi terenkripsi wajib menyertakan alasan dinas yang sah dan <b>akan dicatat secara permanen dalam Audit Trail</b>.
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 mb-4">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Peserta Didik:</div>
                <div id="modalStudentName" class="text-sm font-bold text-slate-900 mt-0.5">-</div>
                <div id="modalStudentNosik" class="font-mono text-xs text-slate-500 mt-0.5">-</div>
            </div>

            <form id="revealForm" onsubmit="submitReveal(event)">
                <input type="hidden" id="modalStudentId">

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alasan Akses / Keperluan Dinas <span class="text-red-500">*</span>
                    </label>
                    <select id="accessReasonSelect" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 mb-2 focus:outline-none focus:ring-2 focus:ring-slate-900" onchange="handleReasonChange(this)">
                        <option value="Verifikasi Kelengkapan Administrasi & Berkas Personel">Verifikasi Kelengkapan Administrasi & Berkas Personel</option>
                        <option value="Pemeriksaan Kesehatan & Rekam Medis Poliklinik Satdik">Pemeriksaan Kesehatan & Rekam Medis Poliklinik Satdik</option>
                        <option value="Konfirmasi Kontak Darurat Keluarga Serdik">Konfirmasi Kontak Darurat Keluarga Serdik</option>
                        <option value="Distribusi Uang Saku / Rekening Bank TNI AD">Distribusi Uang Saku / Rekening Bank TNI AD</option>
                        <option value="Pemeriksaan Wasrik / Audit Tim Zona Integritas">Pemeriksaan Wasrik / Audit Tim Zona Integritas</option>
                        <option value="Lainnya">Lainnya (Tulis Manual)...</option>
                    </select>
                    <textarea id="accessReasonText" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" rows="2" placeholder="Tuliskan keterangan detail keperluan dinas..." style="display:none;"></textarea>
                    <div class="text-[11px] text-slate-400 mt-1">Alasan ini akan disimpan bersama User ID, Waktu Akses, dan IP Address Anda.</div>
                </div>

                <div class="flex justify-end gap-2.5 pt-2">
                    <button type="button" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors" onclick="closeRevealModal()">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors" id="btnSubmitReveal">
                        <span class="ms text-[16px]">lock_open</span> Dekripsi & Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <!-- HASIL DEKRIPSI -->
        <div class="p-6" id="modalResultSection" style="display:none;">
            <div class="mb-4 flex items-center gap-2 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs">
                <span class="ms text-emerald-600 text-[18px]">check_circle</span>
                <span id="auditNotice">Akses data pribadi berhasil didekripsi dan dicatat pada audit trail.</span>
            </div>

            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-xs text-left" id="decryptedTable">
                    <!-- Diisi lewat JS -->
                </table>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold transition-colors" onclick="closeRevealModal()">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()?->canModifyData())
<!-- 3. MODAL TAMBAH SISWA BARU (CREATE) -->
<div class="modal-overlay" id="createStudentModal">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col" style="max-width:920px; max-height:92vh;">
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="ms text-amber-400 text-[24px]">person_add</span>
                <div>
                    <b class="text-sm font-bold tracking-wide">Tambah Prajurit Siswa Baru</b>
                    <div class="text-[11px] text-slate-400">Pendaftaran Siswa per Satuan Pendidikan — Sistem SIPANDU-WBK</div>
                </div>
            </div>
            <button type="button" onclick="closeCreateStudentModal()" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <form id="createStudentForm" method="POST" action="{{ route('students.store') }}" onsubmit="handleAjaxSubmit(event, 'create')" class="flex flex-col overflow-hidden flex-1">
            @csrf
            <div class="p-6 overflow-y-auto flex-1 space-y-5">
                <div id="create_error_alert" class="hidden p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs">
                    <div class="flex items-start gap-2">
                        <span class="ms text-rose-500 text-[18px] shrink-0">error</span>
                        <div id="create_error_list"></div>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                    <span class="ms text-amber-500 text-[22px] shrink-0">shield</span>
                    <div>
                        <b>Keamanan Sistem SIPANDU & Anti-Duplikasi:</b> NOSIS dibentuk otomatis. Validasi NIK KTP (16 Digit) mencegah data ganda. Data pribadi tersimpan terenkripsi <b>AES-256-CBC</b>.
                    </div>
                </div>

                <!-- BAGIAN A: DATA KEMILITERAN & SATDIK -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                        <b class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="ms text-amber-500 text-[18px]">military_tech</span> Bagian A: Data Kemiliteran & Satuan Pendidikan
                        </b>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">Wajib Dilengkapi</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 mb-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Satuan Pendidikan (Satdik) <span class="text-red-500">*</span></label>
                            <select name="satdik_id" id="create_satdik_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" required onchange="handleSatdikSelectChange(this.value, 'create')">
                                <option value="">-- Pilih Satdik --</option>
                                @foreach($satdiks as $s)
                                    <option value="{{ $s->id }}" {{ $selectedSatdikId == $s->id ? 'selected' : '' }}>
                                        {{ $s->code }} — {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Program Pendidikan <span class="text-red-500">*</span></label>
                            <select name="education_program_id" id="create_education_program_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" required onchange="handleProgramSelectChange(this.value, 'create')">
                                <option value="">-- Pilih Satdik Terlebih Dahulu --</option>
                            </select>
                        </div>
                    </div>

                    <!-- KOMPI & PELETON -->
                    <div class="mb-3.5">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700">Peleton / Kompi Siswa</label>
                            <a href="javascript:void(0)" id="create_toggle_manual_btn" onclick="toggleClassroomMode('create')" class="text-xs text-slate-600 hover:text-slate-900 font-semibold underline">
                                + Ketik Manual Kompi & Peleton
                            </a>
                        </div>
                        
                        <div id="create_classroom_dropdown_wrapper">
                            <select name="classroom_id" id="create_classroom_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                <option value="">-- Pilih Peleton / Kompi (Opsional) --</option>
                            </select>
                        </div>

                        <div id="create_classroom_manual_wrapper" style="display:none;" class="p-3 bg-white border border-dashed border-slate-300 rounded-lg mt-1.5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Kompi Siswa</label>
                                    <input type="text" list="companyDatalist" name="company" id="create_manual_company" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs" placeholder="Contoh: Kompi A">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Peleton Siswa</label>
                                    <input type="text" list="platoonDatalist" name="platoon" id="create_manual_platoon" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs" placeholder="Contoh: Peleton 1">
                                </div>
                            </div>
                            <small class="text-[10px] text-slate-400 mt-1 block">Sistem otomatis membuat rombongan belajar baru jika belum tersedia.</small>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3.5 mb-3.5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Siswa <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" id="create_full_name" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900" placeholder="Contoh: Muhammad Rizky Pratama" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pangkat Siswa <span class="text-red-500">*</span></label>
                            <select name="student_rank" id="create_student_rank" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" required>
                                <option value="Siswa Secaba">Siswa Secaba</option>
                                <option value="Siswa Secata">Siswa Secata</option>
                                <option value="Prada Siswa">Prada Siswa</option>
                                <option value="Serda Siswa">Serda Siswa</option>
                                <option value="Kader Bela Negara">Kader Bela Negara</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status Siswa <span class="text-red-500">*</span></label>
                            <select name="status" id="create_status" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" required>
                                <option value="Aktif">🟢 Aktif (Terhitung)</option>
                                <option value="Sakit">🟡 Sakit (Terhitung)</option>
                                <option value="Dinas Luar">🔵 Dinas Luar (Terhitung)</option>
                                <option value="Selesai">📁 Selesai (Arsip)</option>
                                <option value="Lulus">🎓 Lulus (Arsip)</option>
                                <option value="DO / Dikeluarkan">🔴 DO / Dikeluarkan (Arsip)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kodam / Kodim Asal</label>
                            <input type="text" list="kodimList" name="origin_military_unit" id="create_origin_military_unit" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900" placeholder="Pilih / ketik asal kodim">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="birth_place" id="create_birth_place" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900" placeholder="Contoh: Bandung">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="birth_date" id="create_birth_date" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="gender" id="create_gender" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" required>
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN B: DATA PRIBADI SENSITIF TERPROTEKSI -->
                <div class="bg-amber-50/50 border border-amber-200 rounded-xl p-4">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-amber-200">
                        <b class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="ms text-amber-600 text-[18px]">lock</span> Bagian B: Data Pribadi Sensitif Terproteksi
                        </b>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Enkripsi AES-256</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 mb-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">NIK (16 Digit KTP) <span class="text-red-500">*</span></label>
                            <input type="text" name="nik" id="create_nik" maxlength="16" minlength="16" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono focus:outline-none focus:ring-2 focus:ring-slate-900" placeholder="Contoh: 3201012345670001" required>
                            <div class="text-[10px] text-slate-500 mt-1">Kunci validasi anti-duplikasi serdik.</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">No. Kartu Keluarga (KK)</label>
                            <input type="text" name="family_card_number" id="create_family_card_number" maxlength="16" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono focus:outline-none focus:ring-2 focus:ring-slate-900" placeholder="Contoh: 3201012345670002">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 mb-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Ibu Kandung <span class="text-red-500">*</span></label>
                            <input type="text" name="mother_name" id="create_mother_name" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900" placeholder="Contoh: Siti Fatimah" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Ayah Kandung</label>
                            <input type="text" name="father_name" id="create_father_name" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900" placeholder="Contoh: Bambang Irawan">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">No HP Kontak Darurat / Ortu <span class="text-red-500">*</span></label>
                            <input type="text" name="emergency_contact_phone" id="create_emergency_contact_phone" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono focus:outline-none focus:ring-2 focus:ring-slate-900" placeholder="Contoh: 081234567890" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Asal KTP</label>
                        <textarea name="home_address" id="create_home_address" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900" rows="2" placeholder="Alamat domisili lengkap"></textarea>
                    </div>
                </div>

                <!-- BAGIAN C: DATA FISIK & STATUS KESEHATAN AWAL -->
                <div class="bg-emerald-50/50 border border-emerald-200 rounded-xl p-4">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-emerald-200">
                        <b class="text-xs font-bold text-emerald-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="ms text-emerald-600 text-[18px]">monitor_heart</span> Bagian C: Fisik & Kesiapan Medis Awal
                        </b>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Format Rekam Medis</span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3.5 mb-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">TB (cm)</label>
                            <input type="number" name="height_cm" id="create_height_cm" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs" placeholder="172" min="100" max="250" oninput="calcBmi('create')">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">BB (kg)</label>
                            <input type="number" step="0.5" name="weight_kg" id="create_weight_kg" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs" placeholder="68" min="30" max="200" oninput="calcBmi('create')">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">BMI & Kategori</label>
                            <div id="create_bmi_badge" class="px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-500 text-center">
                                -
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tensi Darah</label>
                            <input type="text" name="blood_pressure" id="create_blood_pressure" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs" placeholder="120/80" value="120/80">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Gol. Darah</label>
                            <select name="blood_type" id="create_blood_type" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                <option value="O">O</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3.5">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kondisi Fisik / Kesiapan Medis</label>
                        <select name="daily_health_status" id="create_daily_health_status" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                            <option value="Siap Latih">🟢 Sehat Penuh (Normal & Prima)</option>
                            <option value="Berobat Jalan">🟡 Berobat Jalan / Dispen (Keluhan Ringan)</option>
                            <option value="Rawat Inap Poliklinik">🔴 Rawat Inap Poliklinik Satdik (Bed Rest)</option>
                            <option value="Rujuk Rumkit">🔵 Rujuk Rumah Sakit (Rumkit Tk. II Soedjono / Dinas)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Medis Awal & Riwayat Alergi</label>
                        <input type="text" name="doctor_notes" id="create_doctor_notes" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs" placeholder="Catatan kondisi awal fisik serdik...">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2.5 shrink-0">
                <button type="button" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors" onclick="closeCreateStudentModal()">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors" id="btnSubmitCreateStudent">
                    <span class="ms text-[16px]">save</span> Daftarkan Siswa Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 4. MODAL EDIT DATA SISWA (UPDATE) -->
<div class="modal-overlay" id="editStudentModal">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col" style="max-width:920px; max-height:92vh;">
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="ms text-blue-400 text-[24px]">edit</span>
                <div>
                    <b class="text-sm font-bold tracking-wide">Edit Data Prajurit Siswa</b>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span id="edit_nosik_badge" class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-800 text-slate-300">NOSIS: -</span>
                        <span id="edit_student_title" class="text-xs text-slate-300 font-semibold">-</span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="closeEditStudentModal()" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <form id="editStudentForm" method="POST" action="" onsubmit="handleAjaxSubmit(event, 'edit')" class="flex flex-col overflow-hidden flex-1">
            @csrf
            @method('PUT')
            <div class="p-6 overflow-y-auto flex-1 space-y-5">
                <div id="edit_error_alert" class="hidden p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs">
                    <div class="flex items-start gap-2">
                        <span class="ms text-rose-500 text-[18px] shrink-0">error</span>
                        <div id="edit_error_list"></div>
                    </div>
                </div>

                <!-- BAGIAN A: DATA KEMILITERAN & SATDIK -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                        <b class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="ms text-amber-500 text-[18px]">military_tech</span> Bagian A: Data Kemiliteran & Satuan Pendidikan
                        </b>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 mb-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Satuan Pendidikan (Satdik) <span class="text-red-500">*</span></label>
                            <select name="satdik_id" id="edit_satdik_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" required onchange="handleSatdikSelectChange(this.value, 'edit')">
                                @foreach($satdiks as $s)
                                    <option value="{{ $s->id }}">{{ $s->code }} — {{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Program Pendidikan <span class="text-red-500">*</span></label>
                            <select name="education_program_id" id="edit_education_program_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" required onchange="handleProgramSelectChange(this.value, 'edit')">
                            </select>
                        </div>
                    </div>

                    <!-- KOMPI & PELETON -->
                    <div class="mb-3.5">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700">Peleton / Kompi Siswa</label>
                            <a href="javascript:void(0)" id="edit_toggle_manual_btn" onclick="toggleClassroomMode('edit')" class="text-xs text-slate-600 hover:text-slate-900 font-semibold underline">
                                + Ketik Manual Kompi & Peleton
                            </a>
                        </div>
                        
                        <div id="edit_classroom_dropdown_wrapper">
                            <select name="classroom_id" id="edit_classroom_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                <option value="">-- Pilih Peleton / Kompi (Opsional) --</option>
                            </select>
                        </div>

                        <div id="edit_classroom_manual_wrapper" style="display:none;" class="p-3 bg-white border border-dashed border-slate-300 rounded-lg mt-1.5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Kompi Siswa</label>
                                    <input type="text" list="companyDatalist" name="company" id="edit_manual_company" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs" placeholder="Contoh: Kompi A">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Peleton Siswa</label>
                                    <input type="text" list="platoonDatalist" name="platoon" id="edit_manual_platoon" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs" placeholder="Contoh: Peleton 1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3.5 mb-3.5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Siswa <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" id="edit_full_name" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pangkat Siswa <span class="text-red-500">*</span></label>
                            <select name="student_rank" id="edit_student_rank" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" required>
                                <option value="Siswa Secaba">Siswa Secaba</option>
                                <option value="Siswa Secata">Siswa Secata</option>
                                <option value="Prada Siswa">Prada Siswa</option>
                                <option value="Serda Siswa">Serda Siswa</option>
                                <option value="Kader Bela Negara">Kader Bela Negara</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status Siswa <span class="text-red-500">*</span></label>
                            <select name="status" id="edit_status" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" required>
                                <option value="Aktif">🟢 Aktif (Terhitung)</option>
                                <option value="Sakit">🟡 Sakit (Terhitung)</option>
                                <option value="Dinas Luar">🔵 Dinas Luar (Terhitung)</option>
                                <option value="Selesai">📁 Selesai (Arsip)</option>
                                <option value="Lulus">🎓 Lulus (Arsip)</option>
                                <option value="DO / Dikeluarkan">🔴 DO / Dikeluarkan (Arsip)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kodam / Kodim Asal</label>
                            <input type="text" list="kodimList" name="origin_military_unit" id="edit_origin_military_unit" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="birth_place" id="edit_birth_place" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="birth_date" id="edit_birth_date" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="gender" id="edit_gender" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" required>
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN B: DATA PRIBADI & KONTAK -->
                <div class="bg-amber-50/50 border border-amber-200 rounded-xl p-4">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-amber-200">
                        <b class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="ms text-amber-600 text-[18px]">lock</span> Bagian B: Data Pribadi & Kontak Darurat (SIPANDU-WBK)
                        </b>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 mb-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Revisi NIK (16 Digit KTP)</label>
                            <input type="text" name="nik" id="edit_nik" maxlength="16" minlength="16" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono focus:outline-none focus:ring-2 focus:ring-slate-900" placeholder="Kosongkan jika tidak ada perubahan NIK">
                            <div class="text-[10px] text-slate-500 mt-1">Hanya diisi jika terdapat perbaikan NIK KTP Serdik.</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Ibu Kandung</label>
                            <input type="text" name="mother_name" id="edit_mother_name" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900" placeholder="Nama Ibu Kandung">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">No HP Kontak Darurat / Orang Tua</label>
                            <input type="text" name="emergency_contact_phone" id="edit_emergency_contact_phone" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono focus:outline-none focus:ring-2 focus:ring-slate-900" placeholder="Nomor Telepon Darurat">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Domisili KTP</label>
                            <input type="text" name="home_address" id="edit_home_address" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-900" placeholder="Alamat asal...">
                        </div>
                    </div>
                </div>

                <!-- BAGIAN C: DATA FISIK & STATUS KESEHATAN -->
                <div class="bg-emerald-50/50 border border-emerald-200 rounded-xl p-4">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-emerald-200">
                        <b class="text-xs font-bold text-emerald-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="ms text-emerald-600 text-[18px]">monitor_heart</span> Bagian C: Fisik & Kesiapan Medis
                        </b>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3.5 mb-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">TB (cm)</label>
                            <input type="number" name="height_cm" id="edit_height_cm" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs" min="100" max="250" oninput="calcBmi('edit')">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">BB (kg)</label>
                            <input type="number" step="0.5" name="weight_kg" id="edit_weight_kg" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs" min="30" max="200" oninput="calcBmi('edit')">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">BMI & Kategori</label>
                            <div id="edit_bmi_badge" class="px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-500 text-center">
                                -
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tensi Darah</label>
                            <input type="text" name="blood_pressure" id="edit_blood_pressure" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Gol. Darah</label>
                            <select name="blood_type" id="edit_blood_type" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                <option value="O">O</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3.5">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kondisi Fisik / Kesiapan Medis</label>
                        <select name="daily_health_status" id="edit_daily_health_status" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                            <option value="Siap Latih">🟢 Sehat Penuh (Normal & Prima)</option>
                            <option value="Berobat Jalan">🟡 Berobat Jalan / Dispen (Keluhan Ringan)</option>
                            <option value="Rawat Inap Poliklinik">🔴 Rawat Inap Poliklinik Satdik (Bed Rest)</option>
                            <option value="Rujuk Rumkit">🔵 Rujuk Rumah Sakit (Rumkit Tk. II Soedjono / Dinas)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Medis & Riwayat</label>
                        <input type="text" name="doctor_notes" id="edit_doctor_notes" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2.5 shrink-0">
                <button type="button" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors" onclick="closeEditStudentModal()">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors" id="btnSubmitEditStudent">
                    <span class="ms text-[16px]">save</span> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 5. MODAL HAPUS DATA SISWA (DELETE) -->
<div class="modal-overlay" id="deleteStudentModal">
    <div class="modal-card max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-red-600 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="ms text-[22px]">delete_forever</span>
                <b class="text-sm font-bold tracking-wide">Konfirmasi Hapus Data Siswa</b>
            </div>
            <button type="button" onclick="closeDeleteStudentModal()" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>

        <form id="deleteStudentForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="p-6">
                <div class="mb-4 flex items-start gap-2.5 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-900 text-xs leading-relaxed">
                    <span class="ms text-rose-500 text-[20px] shrink-0">warning</span>
                    <div>
                        <b>Peringatan:</b> Tindakan ini akan menghapus data prajurit siswa secara permanen dari sistem beserta seluruh rekam medis dan profil terkait.
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Data Siswa yang Akan Dihapus:</div>
                    <div id="deleteModalStudentName" class="text-sm font-bold text-slate-900 mt-0.5">-</div>
                    <div class="mt-1.5 flex items-center gap-2">
                        <span id="deleteModalStudentNosik" class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-200 text-slate-700 font-bold">-</span>
                        <span id="deleteModalStudentSatdik" class="text-xs text-slate-500">-</span>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2.5">
                <button type="button" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors" onclick="closeDeleteStudentModal()">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors">
                    <span class="ms text-[16px]">delete</span> Ya, Hapus Data
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- DATALISTS FOR AUTOCOMPLETE -->
<datalist id="companyDatalist">
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
<datalist id="platoonDatalist">
    <option value="Peleton 1">
    <option value="Peleton 2">
    <option value="Peleton 3">
    <option value="Peleton 4">
    <option value="Peleton Bantuan">
    <option value="Peleton Taktik">
    <option value="Peleton Senban">
    <option value="Peleton Runduk">
</datalist>
<datalist id="kodimList">
    <option value="Kodam III/Slw - Kodim 0618/Kota Bandung">
    <option value="Kodam III/Slw - Kodim 0609/Cimahi">
    <option value="Kodam III/Slw - Kodim 0624/Kab. Bandung">
    <option value="Kodam III/Slw - Kodim 0607/Kota Sukabumi">
    <option value="Kodam III/Slw - Kodim 0611/Garut">
    <option value="Kodam III/Slw - Kodim 0606/Kota Bogor">
    <option value="Kodam III/Slw - Kodim 0612/Tasikmalaya">
    <option value="Kodam III/Slw - Kodim 0614/Kota Cirebon">
    <option value="Kodam Jaya - Kodim 0501/Jakarta Pusat">
    <option value="Kodam IV/Dip - Kodim 0733/Kota Semarang">
    <option value="Kodam V/Brw - Kodim 0832/Surabaya Selatan">
</datalist>

@endsection

@section('scripts')
<script>
let currentStudentId = null;

const allSatdiksData = @json($satdiks);
const allProgramsData = @json($allPrograms);
const allClassroomsData = @json($allClassrooms);

// Handle cascading dropdowns Satdik -> Program -> Peleton
function handleSatdikSelectChange(satdikId, prefix, targetProgramId = null, targetClassroomId = null) {
    const progSelect = document.getElementById(`${prefix}_education_program_id`);
    progSelect.innerHTML = '<option value="">-- Pilih Program Pendidikan --</option>';
    
    if (!satdikId) {
        handleProgramSelectChange(null, prefix);
        return;
    }
    
    const filteredPrograms = allProgramsData.filter(p => p.satdik_id == satdikId);
    filteredPrograms.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = `${p.name} (TA ${p.academic_year || '2026'})`;
        if (targetProgramId && p.id == targetProgramId) {
            opt.selected = true;
        }
        progSelect.appendChild(opt);
    });
    
    const selectedProgId = targetProgramId || progSelect.value;
    handleProgramSelectChange(selectedProgId, prefix, targetClassroomId);
}

function handleProgramSelectChange(programId, prefix, targetClassroomId = null) {
    const clsSelect = document.getElementById(`${prefix}_classroom_id`);
    clsSelect.innerHTML = '<option value="">-- Pilih Peleton / Kompi (Opsional) --</option>';
    
    if (!programId) return;
    
    const filteredClassrooms = allClassroomsData.filter(c => c.education_program_id == programId);
    filteredClassrooms.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = `${c.name} (Kapasitas: ${c.capacity || 35})`;
        if (targetClassroomId && c.id == targetClassroomId) {
            opt.selected = true;
        }
        clsSelect.appendChild(opt);
    });
}

function toggleClassroomMode(prefix) {
    const dropWrapper = document.getElementById(`${prefix}_classroom_dropdown_wrapper`);
    const manualWrapper = document.getElementById(`${prefix}_classroom_manual_wrapper`);
    const toggleBtn = document.getElementById(`${prefix}_toggle_manual_btn`);
    
    if (manualWrapper.style.display === 'none') {
        manualWrapper.style.display = 'block';
        dropWrapper.style.display = 'none';
        toggleBtn.textContent = '✕ Gunakan Pilihan Dropdown Peleton';
        toggleBtn.classList.remove('text-slate-600');
        toggleBtn.classList.add('text-rose-600');
    } else {
        manualWrapper.style.display = 'none';
        dropWrapper.style.display = 'block';
        toggleBtn.textContent = '+ Ketik Manual Kompi & Peleton';
        toggleBtn.classList.remove('text-rose-600');
        toggleBtn.classList.add('text-slate-600');
    }
}

function calcBmi(prefix) {
    const h = parseFloat(document.getElementById(`${prefix}_height_cm`).value);
    const w = parseFloat(document.getElementById(`${prefix}_weight_kg`).value);
    const badge = document.getElementById(`${prefix}_bmi_badge`);
    
    if (!h || !w || h <= 0 || w <= 0) {
        badge.innerHTML = '-';
        badge.className = 'px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-500 text-center';
        return;
    }
    
    const hm = h / 100;
    const bmi = (w / (hm * hm)).toFixed(1);
    let category = 'Normal';
    let cls = 'px-3 py-2 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-xs font-bold text-center';
    
    if (bmi < 18.5) {
        category = 'Kurang (Underweight)';
        cls = 'px-3 py-2 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg text-xs font-bold text-center';
    } else if (bmi <= 25.0) {
        category = 'Ideal / Normal';
        cls = 'px-3 py-2 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-xs font-bold text-center';
    } else if (bmi <= 27.0) {
        category = 'Kelebihan (Overweight)';
        cls = 'px-3 py-2 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg text-xs font-bold text-center';
    } else {
        category = 'Obesitas';
        cls = 'px-3 py-2 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-xs font-bold text-center';
    }
    badge.innerHTML = `${bmi} — <span class="text-[11px] font-semibold">${category}</span>`;
    badge.className = cls;
}

// Modal open/close functions
function openCreateStudentModal(defaultSatdikId = null, defaultProgramId = null) {
    if (!document.getElementById('createStudentModal')) return;
    const operatorSatdikId = '{{ (!auth()->user()?->isPimpinan() && auth()->user()?->satdik_id) ? auth()->user()->satdik_id : '' }}';
    const sId = operatorSatdikId || defaultSatdikId || '{{ $selectedSatdikId }}' || (allSatdiksData.length > 0 ? allSatdiksData[0].id : '');
    const pId = defaultProgramId || '{{ $selectedProgramId }}' || '';
    
    if (sId) {
        const satdikEl = document.getElementById('create_satdik_id');
        if (satdikEl) satdikEl.value = sId;
        handleSatdikSelectChange(sId, 'create', pId);
    }
    
    document.getElementById('create_error_alert').classList.add('hidden');
    document.getElementById('createStudentModal').classList.add('active');
}

function closeCreateStudentModal() {
    document.getElementById('createStudentModal').classList.remove('active');
}

function openEditStudentModal(btnOrData) {
    if (!document.getElementById('editStudentModal')) return;
    let s;
    if (typeof btnOrData === 'object' && btnOrData.getAttribute) {
        s = JSON.parse(btnOrData.getAttribute('data-student'));
    } else {
        s = btnOrData;
    }
    
    document.getElementById('editStudentForm').action = `/students/${s.id}`;
    document.getElementById('edit_nosik_badge').textContent = 'NOSIS: ' + (s.nosik || '-');
    document.getElementById('edit_student_title').textContent = s.full_name;
    
    const operatorSatdikId = '{{ (!auth()->user()?->isPimpinan() && auth()->user()?->satdik_id) ? auth()->user()->satdik_id : '' }}';
    const satdikId = operatorSatdikId || s.satdik_id;
    const satdikEl = document.getElementById('edit_satdik_id');
    if (satdikEl) satdikEl.value = satdikId;
    handleSatdikSelectChange(satdikId, 'edit', s.education_program_id, s.classroom_id);
    
    document.getElementById('edit_full_name').value = s.full_name;
    document.getElementById('edit_student_rank').value = s.student_rank;
    document.getElementById('edit_status').value = s.status;
    document.getElementById('edit_origin_military_unit').value = s.origin_military_unit || '';
    document.getElementById('edit_birth_place').value = s.birth_place || '';
    document.getElementById('edit_birth_date').value = s.birth_date || '';
    document.getElementById('edit_gender').value = s.gender || 'L';
    
    document.getElementById('edit_nik').value = '';
    document.getElementById('edit_mother_name').value = s.mother_name || '';
    document.getElementById('edit_emergency_contact_phone').value = s.emergency_contact_phone || '';
    document.getElementById('edit_home_address').value = s.home_address || '';
    
    document.getElementById('edit_height_cm').value = s.height_cm || '';
    document.getElementById('edit_weight_kg').value = s.weight_kg || '';
    calcBmi('edit');
    document.getElementById('edit_blood_pressure').value = s.blood_pressure || '120/80';
    document.getElementById('edit_blood_type').value = s.blood_type || 'O';
    document.getElementById('edit_daily_health_status').value = s.daily_health_status || 'Siap Latih';
    document.getElementById('edit_doctor_notes').value = s.doctor_notes || '';
    
    if (s.company || s.platoon) {
        document.getElementById('edit_manual_company').value = s.company || '';
        document.getElementById('edit_manual_platoon').value = s.platoon || '';
    }
    
    document.getElementById('edit_error_alert').classList.add('hidden');
    document.getElementById('editStudentModal').classList.add('active');
}

function closeEditStudentModal() {
    document.getElementById('editStudentModal').classList.remove('active');
}

function openDeleteStudentModal(id, name, nosik, satdikName) {
    if (!document.getElementById('deleteStudentModal')) return;
    document.getElementById('deleteStudentForm').action = `/students/${id}`;
    document.getElementById('deleteModalStudentName').textContent = name;
    document.getElementById('deleteModalStudentNosik').textContent = 'NOSIS: ' + nosik;
    document.getElementById('deleteModalStudentSatdik').textContent = satdikName || 'Satdik Rindam III/Slw';
    document.getElementById('deleteStudentModal').classList.add('active');
}

function closeDeleteStudentModal() {
    document.getElementById('deleteStudentModal').classList.remove('active');
}

// Ajax submission handler for modals
function handleAjaxSubmit(event, type) {
    event.preventDefault();
    const form = event.target;
    const btn = document.getElementById(type === 'create' ? 'btnSubmitCreateStudent' : 'btnSubmitEditStudent');
    const errBox = document.getElementById(`${type}_error_alert`);
    const errList = document.getElementById(`${type}_error_list`);
    
    errBox.classList.add('hidden');
    errList.innerHTML = '';
    btn.disabled = true;
    const origBtnHtml = btn.innerHTML;
    btn.innerHTML = '<span class="ms animate-spin">sync</span> Menyimpan...';
    
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async res => {
        btn.disabled = false;
        btn.innerHTML = origBtnHtml;
        
        if (res.ok) {
            window.location.reload();
        } else if (res.status === 422) {
            const errData = await res.json();
            let msg = '<b class="font-bold">Terdapat kesalahan pengisian formulir:</b><ul class="list-disc list-inside mt-1 space-y-0.5">';
            if (errData.errors) {
                for (let key in errData.errors) {
                    errData.errors[key].forEach(m => {
                        msg += `<li>${m}</li>`;
                    });
                }
            } else if (errData.message) {
                msg += `<li>${errData.message}</li>`;
            }
            msg += '</ul>';
            errList.innerHTML = msg;
            errBox.classList.remove('hidden');
            form.querySelector('.overflow-y-auto').scrollTop = 0;
        } else {
            const errData = await res.json().catch(() => null);
            errList.innerHTML = `<b class="font-bold">Terjadi kesalahan pada server:</b> ${errData?.message || 'Silakan periksa kembali input formulir.'}`;
            errBox.classList.remove('hidden');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = origBtnHtml;
        errList.innerHTML = '<b class="font-bold">Gagal menghubungi server.</b> Periksa koneksi jaringan Anda.';
        errBox.classList.remove('hidden');
    });
}

// Status & Reveal modal functions
function openStatusModal(studentId, fullName, currentStatus) {
    if (!document.getElementById('statusModal')) return;
    document.getElementById('statusModalStudentName').textContent = fullName;
    const form = document.getElementById('statusForm');
    form.action = `/students/${studentId}/status`;
    const sel = document.getElementById('statusModalSelect');
    sel.value = currentStatus;
    updateStatusExplanation(currentStatus);
    document.getElementById('statusModal').classList.add('active');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.remove('active');
}

function updateStatusExplanation(status) {
    const box = document.getElementById('statusExplanation');
    if (status === 'Selesai' || status === 'Lulus' || status === 'DO / Dikeluarkan') {
        box.className = 'p-3.5 bg-slate-100 border-l-4 border-slate-400 rounded-lg text-xs text-slate-700 leading-relaxed';
        box.innerHTML = `<span class="ms text-slate-500 align-middle mr-1">archive</span> <b>Status Arsip:</b> Siswa ini akan dipindahkan ke kategori <b>Arsip</b> dan <b>TIDAK TERHITUNG LAGI</b> dalam kuota/kekuatan siswa aktif berjalan. Data tetap tersimpan aman untuk penelusuran riwayat/alumni.`;
    } else {
        box.className = 'p-3.5 bg-emerald-50 border-l-4 border-emerald-500 rounded-lg text-xs text-emerald-800 leading-relaxed';
        box.innerHTML = `<span class="ms text-emerald-600 align-middle mr-1">how_to_reg</span> <b>Status Aktif:</b> Siswa ini sedang menjalani pendidikan dan <b>TERHITUNG</b> dalam kuota/kekuatan aktif harian Satdik & Program Pendidikan.`;
    }
}

function openRevealModal(studentId, fullName, nosik) {
    currentStudentId = studentId;
    document.getElementById('modalStudentId').value = studentId;
    document.getElementById('modalStudentName').textContent = fullName;
    document.getElementById('modalStudentNosik').textContent = 'NOSIS: ' + nosik;

    document.getElementById('modalFormSection').style.display = 'block';
    document.getElementById('modalResultSection').style.display = 'none';
    document.getElementById('accessReasonSelect').value = 'Verifikasi Kelengkapan Administrasi & Berkas Personel';
    document.getElementById('accessReasonText').style.display = 'none';

    document.getElementById('revealModal').classList.add('active');
}

function closeRevealModal() {
    document.getElementById('revealModal').classList.remove('active');
}

function handleReasonChange(select) {
    const customText = document.getElementById('accessReasonText');
    if (select.value === 'Lainnya') {
        customText.style.display = 'block';
        customText.required = true;
        customText.focus();
    } else {
        customText.style.display = 'none';
        customText.required = false;
    }
}

function submitReveal(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitReveal');
    btn.disabled = true;
    btn.innerHTML = '<span class="ms animate-spin">sync</span> Mendekripsi...';

    const selectVal = document.getElementById('accessReasonSelect').value;
    const textVal = document.getElementById('accessReasonText').value;
    const reason = (selectVal === 'Lainnya') ? textVal : selectVal;

    fetch(`/students/${currentStudentId}/reveal-sensitive`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ access_reason: reason })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<span class="ms">lock_open</span> Dekripsi & Tampilkan';

        if (data.status === 'success') {
            document.getElementById('modalFormSection').style.display = 'none';
            document.getElementById('modalResultSection').style.display = 'block';
            document.getElementById('auditNotice').innerHTML = `<b>Audit Log:</b> Diakses oleh ${data.data.accessed_by} pada ${data.data.unmasked_at}`;

            const d = data.data;
            const rows = [
                { label: 'Nomor Induk Kependudukan (NIK)', val: `<b class="font-mono text-slate-900 text-sm">${d.nik}</b>` },
                { label: 'Nomor Kartu Keluarga (KK)', val: `<span class="font-mono text-slate-700">${d.family_card_number}</span>` },
                { label: 'Nama Ibu Kandung', val: `<b class="text-slate-900">${d.mother_name}</b>` },
                { label: 'Nama Ayah', val: `<span class="text-slate-700">${d.father_name}</span>` },
                { label: 'No. HP Darurat / Orang Tua', val: `<b class="font-mono text-emerald-700">${d.emergency_contact_phone || d.emergency_contact}</b>` },
                { label: 'Alamat Asal KTP', val: `<span class="text-slate-700">${d.home_address}</span>` },
            ];

            let html = '';
            rows.forEach((r, idx) => {
                const bg = idx % 2 === 0 ? 'bg-slate-50' : 'bg-white';
                html += `
                    <tr class="${bg} border-b border-slate-100 last:border-0">
                        <td class="px-4 py-2.5 font-semibold text-slate-500 w-2/5">${r.label}</td>
                        <td class="px-4 py-2.5">${r.val}</td>
                    </tr>
                `;
            });

            document.getElementById('decryptedTable').innerHTML = html;
        } else {
            alert(data.message || 'Gagal mendekripsi data.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<span class="ms">lock_open</span> Dekripsi & Tampilkan';
        alert('Terjadi kesalahan saat memproses dekripsi data.');
    });
}

// Global modal triggers (ESC and Backdrop Click)
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});

window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
    }
});

// Auto-open modal if URL query param action=create exists
document.addEventListener('DOMContentLoaded', function() {
    @if(auth()->user()?->canModifyData())
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'create') {
        const satdikParam = urlParams.get('satdik_id');
        const progParam = urlParams.get('program_id');
        if (typeof openCreateStudentModal === 'function') {
            openCreateStudentModal(satdikParam, progParam);
        }
    }
    @endif
});
</script>
@endsection
