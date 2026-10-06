@extends('layouts.app')

@section('title', 'Kesehatan & Rekam Medis Serdik — SIPANDU-WBK')

@section('content')
<div class="px-2 py-4">

    <!-- HEADER TITLE & ACTIONS -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kesehatan & Rekam Medis Serdik</h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="ms text-[13px]">medical_services</span> POLIKLINIK & TONKES
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    <span class="ms text-[13px]">health_and_safety</span> KESIAPAN KESEHATAN
                </span>
            </div>
            <p class="text-sm text-slate-500">Pemantauan kesiapan fisik latihan lapangan & perawatan medis per Satdik — Rindam III/Siliwangi</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center gap-2.5">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                <span class="ms text-[18px]">print</span> Cetak Rekap Medis
            </button>
            <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                <span class="ms text-[18px]">groups</span> Buku Induk Siswa
            </a>
        </div>
    </div>

    <!-- FILTER SATDIK DROPDOWN -->
    <div class="relative group z-30 mb-6" x-data="{ open: false }">
        <button @click="open = !open" @click.away="open = false" type="button" class="w-full sm:w-[280px] flex items-center justify-between px-4 py-3 bg-white border border-slate-200 hover:border-blue-300 rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-100 group-hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ empty($selectedSatdikId) ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-600' }}">
                    <span class="ms text-[20px]">{{ empty($selectedSatdikId) ? 'list' : 'account_balance' }}</span>
                </div>
                <div class="text-left">
                    <div class="text-xs font-semibold text-slate-500 mb-0.5">Filter Satuan Pendidikan</div>
                    <div class="text-sm font-bold text-slate-900 leading-none truncate w-[140px] sm:w-[160px] sm:w-[220px]">
                        {{ $selectedSatdik ? $selectedSatdik->name : 'Semua Satdik' }}
                    </div>
                </div>
            </div>
            <span class="ms text-slate-400 text-[20px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
        </button>

        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             style="display: none;"
             class="absolute top-full left-0 mt-2 w-full sm:w-[380px] bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden">
            
            <a href="{{ route('health.index', array_merge(request()->except(['satdik_id', 'page']))) }}" 
               class="flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 transition-colors {{ empty($selectedSatdikId) ? 'bg-blue-50/50' : '' }} border-b border-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ empty($selectedSatdikId) ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-500' }}">
                        <span class="ms text-[18px]">list</span>
                    </div>
                    <div>
                        <div class="text-sm font-bold {{ empty($selectedSatdikId) ? 'text-blue-900' : 'text-slate-700' }}">Semua Satdik</div>
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
                        $satdikCounted = $satdik->students()->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count();
                        $isActive = ($selectedSatdikId == $satdik->id);
                    @endphp
                    <a href="{{ route('health.index', array_merge(request()->except(['page']), ['satdik_id' => $satdik->id])) }}" 
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
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        <!-- Siswa Aktif Terhitung -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="text-sm font-medium text-slate-500 mb-4 flex items-center justify-between">
                <span>Siswa Aktif Terhitung</span>
                <span class="ms text-sm text-slate-400">how_to_reg</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-900 mb-2">{{ number_format($stats['overall_counted']) }}</div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded font-semibold">{{ $stats['siap_latih'] }} sehat</span>
                    <span>&bull; {{ $stats['berobat_jalan'] }} dispen/jalan</span>
                </div>
            </div>
        </div>

        <!-- Arsip Siswa Selesai -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="text-sm font-medium text-slate-500 mb-4 flex items-center justify-between">
                <span>Arsip Siswa Selesai</span>
                <span class="ms text-sm text-slate-400">archive</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-900 mb-2">{{ number_format($stats['overall_archived']) }}</div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-semibold">{{ $stats['overall_finished'] }} data historis</span>
                    <span>&bull; tidak terhitung</span>
                </div>
            </div>
        </div>

        <!-- Program Pendidikan -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="text-sm font-medium text-slate-500 mb-4 flex items-center justify-between">
                <span>Program Diklat</span>
                <span class="ms text-sm text-slate-400">school</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-900 mb-2">{{ count($stats['program_list'] ?? []) }}</div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-semibold">Tahun Anggaran 2026</span>
                    <span>DIKMABA &bull; DIKMATA</span>
                </div>
            </div>
        </div>

        <!-- Perawatan Medis & Rujukan -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="text-sm font-medium text-slate-500 mb-4 flex items-center justify-between">
                <span>Perawatan & Rujuk Rumkit</span>
                <span class="ms text-sm text-slate-400">local_hospital</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-900 mb-2">{{ number_format($stats['perawatan_khusus']) }}</div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="bg-rose-100 text-rose-700 px-2 py-0.5 rounded font-semibold">{{ $stats['rawat_inap'] }} rawat inap</span>
                    <span>&bull; {{ $stats['rujuk_rumkit'] }} rujuk rumkit</span>
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
                        Kesehatan per Program Pendidikan (TA 2026)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Klik salah satu program untuk menyaring rekam medis serdik pada program tersebut
                    </p>
                </div>
            </div>
            @if($selectedProgramId)
                <a href="{{ route('health.index', array_merge(request()->except(['program_id', 'page']))) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                    <span class="ms text-[16px]">close</span> Tampilkan Semua Program
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            @foreach($stats['program_list'] as $prog)
                @php
                    $isProgActive = ($selectedProgramId == $prog['id']);
                @endphp
                <a href="{{ route('health.index', array_merge(request()->except(['page']), ['program_id' => $isProgActive ? null : $prog['id']])) }}"
                   class="group block p-3.5 rounded-xl border transition-all {{ $isProgActive ? 'border-slate-900 bg-slate-50 ring-2 ring-slate-900 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50' }}">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <span class="font-bold text-xs {{ $isProgActive ? 'text-slate-900' : 'text-slate-800 group-hover:text-slate-900' }} leading-snug line-clamp-2">
                            {{ $prog['name'] }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 shrink-0">
                            {{ $prog['satdik_code'] }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                        <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold text-[11px]" title="Terhitung dalam kekuatan pendidikan">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <b>{{ $prog['counted_students'] }}</b> Terhitung
                        </span>
                        <span class="inline-flex items-center gap-1 text-slate-500 font-medium text-[11px]" title="Selesai Pendidikan (Arsip)">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            <b>{{ $prog['archived_students'] }}</b> Arsip
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- SEGMENTED TABS: SEMUA vs SISWA AKTIF vs ARSIP SELESAI -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex sm:inline-flex p-1 bg-slate-100 border border-slate-200 rounded-xl gap-1 overflow-x-auto w-full sm:w-auto scrollbar-hide">
            <a href="{{ route('health.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'all'])) }}"
               class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $tab == 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                <span class="ms text-[16px]">people</span> Semua Serdik
                <span class="px-2 py-0.5 rounded text-[11px] {{ $tab == 'all' ? 'bg-slate-100 text-slate-800' : 'bg-slate-200 text-slate-600' }}">{{ $stats['overall_total'] }}</span>
            </a>
            <a href="{{ route('health.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'aktif'])) }}"
               class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $tab == 'aktif' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                <span class="ms text-[16px] text-emerald-600">how_to_reg</span> Siswa Aktif Terhitung
                <span class="px-2 py-0.5 rounded text-[11px] {{ $tab == 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $stats['overall_counted'] }}</span>
            </a>
            <a href="{{ route('health.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'arsip'])) }}"
               class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $tab == 'arsip' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                <span class="ms text-[16px] text-slate-500">archive</span> Arsip Siswa Selesai
                <span class="px-2 py-0.5 rounded text-[11px] {{ $tab == 'arsip' ? 'bg-slate-200 text-slate-800' : 'bg-slate-200 text-slate-600' }}">{{ $stats['overall_archived'] }}</span>
            </a>
        </div>

        @if($selectedProgram)
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-xs font-medium">
                <span class="ms text-[16px] text-amber-600">filter_alt</span>
                <span>Menyaring Program: <b>{{ $selectedProgram->name }} ({{ $selectedProgram->satdik->code }})</b></span>
                <a href="{{ route('health.index', array_merge(request()->except(['program_id', 'page']))) }}" class="ml-1 text-amber-700 hover:text-red-600 font-bold" title="Hapus filter">✕</a>
            </div>
        @endif
    </div>

    <!-- MAIN HEALTH DATA CARD -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-8 overflow-hidden">
        <!-- Table Header & Actions/Filter -->
        <div class="px-5 py-4 border-b border-slate-200 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <div class="min-w-0">
                <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2 truncate">
                    <span class="ms text-emerald-600 text-[22px] shrink-0">monitor_heart</span>
                    <span>Daftar Rekam Medis & Kesehatan Serdik {{ $selectedSatdik ? '— ' . $selectedSatdik->name : '' }}</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5 truncate">Catatan riwayat tanda vital, rekam medis harian, dan kesiapan fisik prajurit siswa</p>
            </div>

            <!-- SEARCH & FILTER FORM -->
            <form method="GET" action="{{ route('health.index') }}" class="flex items-center gap-2 flex-nowrap overflow-x-auto w-full xl:w-auto pb-2 xl:pb-0 scrollbar-hide shrink-0">
                @if($tab)
                    <input type="hidden" name="tab" value="{{ $tab }}">
                @endif

                <!-- PILIH SATUAN PENDIDIKAN (SATDIK) -->
                <select name="satdik_id" class="w-32 sm:w-36 h-9 bg-white border border-slate-200 text-slate-700 text-xs font-medium rounded-lg px-2.5 focus:ring-2 focus:ring-slate-900 focus:outline-none shrink-0" onchange="if(this.form.program_id) this.form.program_id.value=''; this.form.submit()">
                    <option value="">Semua Satdik</option>
                    @foreach($satdiks as $s)
                        <option value="{{ $s->id }}" {{ $selectedSatdikId == $s->id ? 'selected' : '' }}>
                            {{ $s->code }}
                        </option>
                    @endforeach
                </select>

                <!-- Filter Program Pendidikan -->
                <select name="program_id" class="w-32 sm:w-36 h-9 bg-white border border-slate-200 text-slate-700 text-xs font-medium rounded-lg px-2.5 focus:ring-2 focus:ring-slate-900 focus:outline-none truncate shrink-0" onchange="this.form.submit()">
                    <option value="">Semua Program</option>
                    @foreach($availablePrograms as $prog)
                        <option value="{{ $prog->id }}" {{ $selectedProgramId == $prog->id ? 'selected' : '' }}>
                            {{ $prog->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Filter Kondisi Fisik -->
                <select name="health_status" class="w-32 sm:w-36 h-9 bg-white border border-slate-200 text-slate-700 text-xs font-medium rounded-lg px-2.5 focus:ring-2 focus:ring-slate-900 focus:outline-none shrink-0" onchange="this.form.submit()">
                    <option value="">Semua Kondisi</option>
                    <option value="Siap Latih" {{ $healthStatus == 'Siap Latih' ? 'selected' : '' }}>Sehat</option>
                    <option value="Berobat Jalan" {{ $healthStatus == 'Berobat Jalan' ? 'selected' : '' }}>Berobat Jalan</option>
                    <option value="Rawat Inap Poliklinik" {{ $healthStatus == 'Rawat Inap Poliklinik' ? 'selected' : '' }}>Rawat Inap</option>
                    <option value="Rujuk Rumkit" {{ $healthStatus == 'Rujuk Rumkit' ? 'selected' : '' }}>Rujuk Rumkit</option>
                </select>

                <div class="relative w-32 sm:w-36 shrink-0">
                    <span class="ms text-slate-400 text-[18px] absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
                    <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari Siswa..." class="w-full h-9 pl-8 pr-2.5 border border-slate-200 rounded-lg text-xs text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                </div>

                <button type="submit" class="h-9 bg-slate-900 hover:bg-slate-800 text-white px-3.5 rounded-lg text-xs font-semibold transition-colors shrink-0 flex items-center justify-center">
                    Filter
                </button>
                @if($keyword || $healthStatus || $selectedProgramId || $selectedSatdikId || $tab !== 'all')
                    <a href="{{ route('health.index') }}" class="h-9 text-xs font-medium text-slate-500 hover:text-slate-800 px-2 rounded-lg flex items-center justify-center shrink-0">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Desktop Table View (Hidden on mobile < xl) -->
        <div class="hidden xl:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 min-w-[1000px]">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider">
                    <tr>
                        <th class="px-2 py-3 border-b border-slate-200 w-10 text-center">No</th>
                        <th class="px-2 py-3 border-b border-slate-200 w-48">Siswa / Serdik</th>
                        <th class="px-2 py-3 border-b border-slate-200 w-24">Satuan & Ton</th>
                        <th class="px-2 py-3 border-b border-slate-200 w-28">Program Diklat</th>
                        <th class="px-2 py-3 border-b border-slate-200 w-24">Asal</th>
                        <th class="px-2 py-3 border-b border-slate-200 w-20">Status</th>
                        <th class="px-2 py-3 border-b border-slate-200 w-32">Kondisi Fisik</th>
                        <th class="px-2 py-3 border-b border-slate-200 w-20">Vital</th>
                        <th class="px-2 py-3 border-b border-slate-200 w-36">Catatan Medis</th>
                        <th class="px-2 py-3 border-b border-slate-200 w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $index => $student)
                        @php
                            $hr = $student->healthRecord;
                            $isSakit = ($student->status === 'Sakit' || str_contains($student->status, 'Sakit') || in_array($hr?->daily_health_status, ['Berobat Jalan', 'Rawat Inap Poliklinik', 'Rujuk Rumkit']));
                        @endphp
                        <tr class="transition-colors {{ $isSakit ? 'bg-red-50/80 border-l-4 border-l-red-500 hover:bg-red-100/80' : 'border-l-4 border-l-transparent hover:bg-slate-50' }}">
                            <td class="px-2 py-2.5 text-center align-middle text-xs font-medium text-slate-500">
                                {{ $students->firstItem() + $index }}
                            </td>
                            <td class="px-2 py-2.5 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center border border-slate-200 shrink-0">
                                        {{ substr($student->full_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <b class="text-slate-900 block text-xs font-semibold leading-tight">{{ $student->full_name }}</b>
                                        <div class="font-mono text-xs text-slate-500 mt-0.5">{{ $student->nosik }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-2.5 align-middle">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $student->satdik->code }}
                                </span>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $student->classroom->name ?? ($student->company ? $student->company . ' ' . $student->platoon : 'Belum Ada Peleton') }}</div>
                            </td>
                            <td class="px-2 py-2.5 align-middle">
                                <div class="font-medium text-slate-800 text-xs truncate max-w-[170px]">{{ $student->educationProgram->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">TA {{ $student->educationProgram->fiscal_year ?? '2026' }}</div>
                            </td>
                            <td class="px-2 py-2.5 align-middle text-xs font-medium text-slate-700">
                                {{ $student->origin_military_unit ?? '-' }}
                            </td>
                            <td class="px-2 py-2.5 align-middle whitespace-nowrap">
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
                            <td class="px-2 py-2.5 align-middle whitespace-nowrap">
                                @php
                                    $b = $hr ? $hr->status_badge : ['class' => 'badge-green', 'icon' => 'check_circle', 'label' => 'Sehat'];
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-semibold {{ $b['class'] }}">
                                    <span class="ms text-[13px]">{{ $b['icon'] }}</span> {{ $b['label'] }}
                                </span>
                                <div class="text-[11px] text-slate-400 mt-1">
                                    Gol. Darah: <b class="text-rose-600">{{ $student->blood_type ?? '-' }}</b>
                                </div>
                            </td>
                            <td class="px-2 py-2.5 align-middle text-xs">
                                <div class="font-bold text-slate-800">{{ $hr->blood_pressure ?? '120/80' }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $hr->height_cm ?? '-' }}cm • {{ $hr->weight_kg ?? '-' }}kg</div>
                            </td>
                            <td class="px-2 py-2.5 align-middle text-xs text-slate-600 max-w-[220px]">
                                <div class="truncate">
                                    {{ $hr && $hr->allergies && $hr->allergies != 'Tidak ada riwayat alergi' ? '⚠️ ' . $hr->allergies : ($hr->doctor_notes ?? 'Kondisi fisik prima') }}
                                </div>
                            </td>
                            <td class="px-2 py-2.5 align-middle text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('health.show', $student) }}" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors" title="Lihat Rekam Medis">
                                        <span class="ms text-[15px]">visibility</span> Medis
                                    </a>
                                    @if(auth()->user()?->canModifyData())
                                    <a href="{{ route('health.edit', $student) }}" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 transition-colors" title="Perbarui Status Kesehatan">
                                        <span class="ms text-[15px]">edit_note</span> Update
                                    </a>
                                    @endif
                                    <a href="{{ route('students.show', $student) }}" class="p-1.5 text-slate-400 hover:text-slate-800 rounded-lg hover:bg-slate-100 transition-colors" title="Dossier Lengkap">
                                        <span class="ms text-[18px]">badge</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-16 text-center text-slate-500">
                                <span class="ms text-[44px] text-slate-300 block mb-2">folder_off</span>
                                <div class="font-bold text-slate-800 text-base">Tidak ada data rekam medis serdik yang sesuai filter ini.</div>
                                <p class="text-xs text-slate-400 mt-1">Coba ubah filter Satdik, Program Pendidikan, atau kategori tab Aktif/Arsip di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (Visible on mobile < md) -->
        <div class="block xl:hidden divide-y divide-slate-100">
            @forelse($students as $index => $student)
                @php
                    $hr = $student->healthRecord;
                    $isSakit = ($student->status === 'Sakit' || str_contains($student->status, 'Sakit') || in_array($hr?->daily_health_status, ['Berobat Jalan', 'Rawat Inap Poliklinik', 'Rujuk Rumkit']));
                    $bgClass = 'bg-slate-100 text-slate-700';
                    if($student->status === 'Aktif') $bgClass = 'bg-indigo-50 text-indigo-700';
                    if($student->status === 'Sakit') $bgClass = 'bg-rose-100 text-rose-700 font-bold';
                    if($student->status === 'Dinas Luar') $bgClass = 'bg-blue-50 text-blue-700';
                    if(in_array($student->status, ['Lulus', 'Selesai'])) $bgClass = 'bg-emerald-50 text-emerald-700';
                    if(str_contains($student->status, 'DO')) $bgClass = 'bg-amber-50 text-amber-700';

                    $b = $hr ? $hr->status_badge : ['class' => 'badge-green', 'icon' => 'check_circle', 'label' => 'Sehat'];
                @endphp
                <div class="p-4 transition-colors {{ $isSakit ? 'bg-red-50/80 border-l-4 border-l-red-500' : 'border-l-4 border-l-transparent bg-white hover:bg-slate-50' }}">
                    <!-- Card Top: Avatar, Name, No/Nosik, Status -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center border border-slate-200 shrink-0">
                                {{ substr($student->full_name, 0, 2) }}
                            </div>
                            <div class="min-w-0">
                                <b class="text-slate-900 block text-sm font-bold leading-tight truncate">{{ $student->full_name }}</b>
                                <div class="flex items-center gap-1.5 font-mono text-[11px] text-slate-500 mt-1">
                                    <span class="text-slate-400">#{{ $students->firstItem() + $index }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $student->nosik }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1 shrink-0">
                            <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold {{ $bgClass }}">
                                {{ $student->status }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold {{ $b['class'] }}">
                                <span class="ms text-[12px]">{{ $b['icon'] }}</span> {{ $b['label'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Health Details Grid -->
                    <div class="grid grid-cols-2 gap-2 text-xs mb-3 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Satdik & Ton</span>
                            <span class="font-semibold text-slate-800">{{ $student->satdik->code }}</span>
                            <span class="text-[10px] text-slate-400 block truncate">{{ $student->classroom->name ?? ($student->company ? $student->company . ' ' . $student->platoon : 'Belum Ada Peleton') }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Program</span>
                            <span class="font-semibold text-slate-800 truncate block">{{ $student->educationProgram->name ?? '-' }}</span>
                            <span class="text-[10px] text-slate-400 block">TA {{ $student->educationProgram->fiscal_year ?? '2026' }}</span>
                        </div>
                        <div class="pt-1.5 border-t border-slate-200/60">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Tanda Vital & Gol. Darah</span>
                            <span class="font-bold text-slate-800">{{ $hr->blood_pressure ?? '120/80' }}</span>
                            <span class="text-[10px] text-slate-500">Gol: <b class="text-rose-600">{{ $student->blood_type ?? '-' }}</b> &bull; {{ $hr->height_cm ?? '-' }}cm/{{ $hr->weight_kg ?? '-' }}kg</span>
                        </div>
                        <div class="pt-1.5 border-t border-slate-200/60">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Catatan Medis</span>
                            <span class="text-[11px] text-slate-600 truncate block">
                                {{ $hr && $hr->allergies && $hr->allergies != 'Tidak ada riwayat alergi' ? '⚠️ ' . $hr->allergies : ($hr->doctor_notes ?? 'Kondisi prima') }}
                            </span>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('students.show', $student) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs text-slate-600 hover:text-slate-900 border border-slate-200 rounded-lg transition-colors">
                            <span class="ms text-[15px]">badge</span> Dossier
                        </a>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('health.show', $student) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors">
                                <span class="ms text-[15px]">visibility</span> Medis
                            </a>
                            @if(auth()->user()?->canModifyData())
                            <a href="{{ route('health.edit', $student) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 transition-colors">
                                <span class="ms text-[15px]">edit_note</span> Update
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500">
                    <span class="ms text-[44px] text-slate-300 block mb-2">folder_off</span>
                    <div class="font-bold text-slate-800 text-base">Tidak ada data rekam medis serdik yang sesuai filter ini.</div>
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
                @if($selectedProgram)
                    <span class="text-xs text-slate-500 ml-1">• Program: <b>{{ $selectedProgram->name }}</b></span>
                @endif
            </div>
            <div>
                {{ $students->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
