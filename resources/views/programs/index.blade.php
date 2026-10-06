@extends('layouts.app')

@section('title', 'Program Pendidikan per Satdik — SIPANDU-WBK')

@section('content')
<div class="px-2 py-4">

    <!-- HEADER TITLE & ACTIONS -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pengelolaan Program Pendidikan per Satdik</h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="ms text-[13px]">school</span> RINDAM III/SILIWANGI
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    <span class="ms text-[13px]">calendar_today</span> TA 2026
                </span>
            </div>
            <p class="text-sm text-slate-500 max-w-4xl">
                Menu tersendiri untuk pembinaan dan monitoring kurikulum program pendidikan di seluruh Satdik Rindam III/Siliwangi (Secaba, Secata, Dodikjur, Dodiklatpur, Dodik Bela Negara).
                Hanya prajurit siswa berstatus <b>Aktif</b> yang terhitung dalam kekuatan program, sedangkan status <b>Selesai</b> otomatis masuk ke <b>Arsip</b>.
            </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 shrink-0">
            @if(auth()->user()?->canModifyData())
            <button type="button" onclick="openCreateProgramModal()" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm shadow-blue-500/20">
                <span class="ms text-[18px]">add_circle</span> Tambah Program Pendidikan
            </button>
            @endif
            <a href="{{ route('students.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                <span class="ms text-[18px]">groups</span> Buku Induk Serdik
            </a>
        </div>
    </div>

    <!-- NOTIFIKASI SUKSES -->
    @if(session('success'))
        <div class="mb-6 flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm">
            <span class="ms text-[22px] text-emerald-600 shrink-0">check_circle</span>
            <div class="font-medium">{{ session('success') }}</div>
        </div>
    @endif

    <!-- NOTIFIKASI ERROR -->
    @if($errors->any())
        <div class="mb-6 flex items-start gap-3 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">
            <span class="ms text-[22px] text-rose-500 shrink-0">error</span>
            <div>
                <b class="font-semibold text-rose-900">Terjadi Kesalahan:</b>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-rose-700">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

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
            
            <a href="{{ route('programs.index', array_merge(request()->except(['satdik_id', 'page']))) }}" 
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
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">{{ $totalPrograms }} Total</span>
                @endif
            </a>

            <div>
                @foreach($satdiks as $satdik)
                    @php
                        $pCount = $satdik->educationPrograms()->count();
                        $isActive = ($selectedSatdikId == $satdik->id);
                    @endphp
                    <a href="{{ route('programs.index', array_merge(request()->except(['page']), ['satdik_id' => $satdik->id])) }}" 
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
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold shrink-0">{{ $pCount }} Program</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>


    <!-- 4 STAT CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Program Terdaftar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center justify-between">
                <span>Total Program</span>
                <span class="ms text-blue-500 text-[18px]">layers</span>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900 mb-1">{{ $totalPrograms }}</div>
                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 font-semibold">
                    <span class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded">{{ $activePrograms }} Aktif</span>
                    <span>&bull; TA 2026</span>
                </div>
            </div>
        </div>

        <!-- Siswa Aktif Terhitung -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center justify-between">
                <span>Siswa Aktif</span>
                <span class="ms text-emerald-500 text-[18px]">how_to_reg</span>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900 mb-1">{{ number_format($totalCountedStudents) }}</div>
                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 font-semibold">
                    <span class="bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded">Operasional</span>
                </div>
            </div>
        </div>

        <!-- Arsip Siswa Selesai -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center justify-between">
                <span>Arsip Selesai</span>
                <span class="ms text-slate-400 text-[18px]">archive</span>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900 mb-1">{{ number_format($totalArchivedStudents) }}</div>
                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 font-semibold">
                    <span class="bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">Tidak Terhitung</span>
                </div>
            </div>
        </div>

        <!-- Satdik Penyelenggara -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center justify-between">
                <span>Satdik Penyelenggara</span>
                <span class="ms text-amber-500 text-[18px]">domain</span>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900 mb-1">5</div>
                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 font-semibold">
                    <span class="bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded">Rindam III/Slw</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN PROGRAM DATA CARD -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-8 overflow-hidden flex flex-col">
        <!-- Table Header & Actions/Filter -->
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-50/50">
            <div class="min-w-0">
                <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2 truncate">
                    <span class="ms text-amber-500 text-[20px] shrink-0">school</span>
                    <span>Daftar Program {{ $selectedSatdik ? '— ' . $selectedSatdik->name : 'Rindam III/Slw' }}</span>
                </h2>
                <p class="text-[11px] text-slate-500 mt-0.5 truncate">Monitoring program, kurikulum diklat, dan rombel peleton</p>
            </div>

            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap shrink-0 w-full lg:w-auto">
                <form method="GET" action="{{ route('programs.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                    @if($selectedSatdikId)
                        <input type="hidden" name="satdik_id" value="{{ $selectedSatdikId }}">
                    @endif

                    <select name="status" class="w-28 sm:w-36 h-9 bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg px-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Berjalan" {{ $statusFilter == 'Berjalan' ? 'selected' : '' }}>Berjalan</option>
                        <option value="Perencanaan" {{ $statusFilter == 'Perencanaan' ? 'selected' : '' }}>Perencanaan</option>
                        <option value="Selesai" {{ $statusFilter == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Ditutup" {{ $statusFilter == 'Ditutup' ? 'selected' : '' }}>Ditutup</option>
                    </select>

                    <div class="relative w-full sm:w-44 shrink-0">
                        <span class="ms text-slate-400 text-[18px] absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
                        <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari Program..." class="w-full h-9 pl-8 pr-2.5 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <button type="submit" class="h-9 bg-slate-800 hover:bg-slate-700 text-white px-3.5 rounded-lg text-xs font-bold transition-colors shrink-0 flex items-center justify-center">
                        Filter
                    </button>
                    @if($keyword || $statusFilter)
                        <a href="{{ route('programs.index', ['satdik_id' => $selectedSatdikId]) }}" class="h-9 text-[11px] font-bold text-slate-500 hover:text-slate-800 px-2 rounded-lg flex items-center justify-center shrink-0">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- 1. TAMPILAN TABEL RESMI PROGRAM (Desktop) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                    <tr>
                        <th class="px-4 py-3 border-b border-slate-200 w-12 text-center">No</th>
                        <th class="px-4 py-3 border-b border-slate-200">Satdik</th>
                        <th class="px-5 py-3 border-b border-slate-200">Nama Program Pendidikan</th>
                        <th class="px-4 py-3 border-b border-slate-200">Kode</th>
                        <th class="px-4 py-3 border-b border-slate-200">Periode</th>
                        <th class="px-4 py-3 border-b border-slate-200 text-center">Aktif</th>
                        <th class="px-4 py-3 border-b border-slate-200 text-center">Arsip</th>
                        <th class="px-4 py-3 border-b border-slate-200">Peleton</th>
                        <th class="px-4 py-3 border-b border-slate-200 text-center">Status</th>
                        <th class="px-4 py-3 border-b border-slate-200 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($programs as $idx => $p)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3.5 text-center text-xs font-semibold text-slate-500 align-middle">
                                {{ $idx + 1 }}
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-slate-900 text-white">
                                    {{ $p['satdik_code'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 align-middle">
                                <a href="{{ route('programs.show', $p['id']) }}" class="font-bold text-sm text-blue-600 hover:text-blue-800 transition-colors block leading-snug">
                                    {{ $p['name'] }}
                                </a>
                                <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-500 mt-1">
                                    <span class="ms text-[13px] text-slate-400">location_on</span>
                                    <span>{{ $p['location'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $p['code'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                <span class="font-bold text-xs text-slate-800 block">TA {{ $p['academic_year'] }}</span>
                                <span class="text-[11px] font-semibold text-slate-500">Gel. {{ $p['batch_number'] }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-center align-middle">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Terhitung dalam kekuatan aktif">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ $p['counted_students'] }} Serdik
                                </span>
                                <div class="text-[10px] font-semibold text-slate-400 mt-1">
                                    {{ $p['ready_students'] }} Siap &bull; {{ $p['sick_students'] }} Sakit
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center align-middle">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200" title="Alumni selesai pendidikan (tidak terhitung)">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    {{ $p['archived_students'] }} Serdik
                                </span>
                                <div class="text-[10px] font-semibold text-slate-400 mt-1">
                                    Lulus / Tamat
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                <span class="font-bold text-xs text-slate-800 block">{{ $p['classrooms']->count() }} Peleton</span>
                                <div class="text-[10px] font-semibold text-slate-500 mt-0.5">
                                    Kapasitas: {{ $p['classrooms']->sum('capacity') }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center align-middle">
                                @if($p['status'] == 'Berjalan')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Berjalan
                                    </span>
                                @elseif($p['status'] == 'Perencanaan')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Perencanaan
                                    </span>
                                @elseif($p['status'] == 'Selesai')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">
                                        {{ $p['status'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap align-middle">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('students.index', ['program_id' => $p['id']]) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Lihat Siswa Program">
                                        <span class="ms text-[18px]">groups</span>
                                    </a>
                                    <a href="{{ route('programs.show', $p['id']) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Detail & Peleton">
                                        <span class="ms text-[18px]">visibility</span>
                                    </a>
                                    @if(auth()->user()?->canModifyData())
                                    <button type="button" onclick="openEditProgramModal({{ json_encode($p['model']) }})" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="Edit Data Program">
                                        <span class="ms text-[18px]">edit</span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-12 text-center text-slate-500">
                                <span class="ms text-slate-300 text-[48px] block mb-2">school</span>
                                <b class="text-sm text-slate-700">Tidak Ada Program Pendidikan Ditemukan</b>
                                <div class="text-xs text-slate-400 mt-1">Coba ganti filter Satdik atau kata kunci pencarian.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 2. TAMPILAN KARTU GRID PROGRAM (Mobile/Tablet) -->
        <div class="block lg:hidden p-4 bg-slate-50/30">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($programs as $p)
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col overflow-hidden">
                        <div class="p-4 flex-1">
                            <!-- CARD TOP -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-900 text-white">
                                        {{ $p['satdik_code'] }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $p['code'] }}
                                    </span>
                                </div>
                                <div>
                                    @if($p['status'] == 'Berjalan')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Berjalan
                                        </span>
                                    @elseif($p['status'] == 'Perencanaan')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Rencana
                                        </span>
                                    @elseif($p['status'] == 'Selesai')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">
                                            {{ $p['status'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- CARD TITLE & LOCATION -->
                            <h3 class="text-sm font-bold text-slate-900 mb-1 leading-snug">
                                <a href="{{ route('programs.show', $p['id']) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $p['name'] }}
                                </a>
                            </h3>
                            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 mb-4 flex-wrap">
                                <span class="flex items-center gap-0.5">
                                    <span class="ms text-[14px] text-slate-400">location_on</span>
                                    {{ $p['location'] }}
                                </span>
                                <span>&bull;</span>
                                <span>TA {{ $p['academic_year'] }}</span>
                                <span>&bull;</span>
                                <span>Gel. {{ $p['batch_number'] }}</span>
                            </div>

                            <!-- BOX KEKUATAN SISWA -->
                            <div class="grid grid-cols-2 gap-2 p-2 bg-slate-50 border border-slate-200 rounded-lg mb-3">
                                <div class="bg-white p-2 rounded border border-slate-100 text-center">
                                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Aktif</div>
                                    <div class="text-lg font-bold text-emerald-600 leading-tight">
                                        {{ $p['counted_students'] }}
                                    </div>
                                    <div class="text-[9px] font-semibold text-slate-500 mt-0.5">
                                        {{ $p['ready_students'] }} Siap / {{ $p['sick_students'] }} Sakit
                                    </div>
                                </div>
                                <div class="bg-white p-2 rounded border border-slate-100 text-center">
                                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Arsip</div>
                                    <div class="text-lg font-bold text-slate-700 leading-tight">
                                        {{ $p['archived_students'] }}
                                    </div>
                                    <div class="text-[9px] font-semibold text-slate-500 mt-0.5">
                                        {{ $p['finished_students'] }} Tamat
                                    </div>
                                </div>
                            </div>

                            <!-- PELETON & ROMBEL -->
                            <div>
                                <div class="text-[11px] font-bold text-slate-700 mb-1.5 flex items-center gap-1">
                                    <span class="ms text-[14px] text-slate-400">groups_2</span>
                                    <span>Peleton ({{ $p['classrooms']->count() }}):</span>
                                </div>
                                @if($p['classrooms']->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($p['classrooms'] as $cls)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-white text-slate-600 border border-slate-200">
                                                {{ $cls->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-[10px] font-semibold text-slate-400 italic">
                                        Belum ada peleton
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                            <a href="{{ route('students.index', ['program_id' => $p['id']]) }}" class="inline-flex flex-1 justify-center items-center gap-1 bg-slate-800 hover:bg-slate-900 text-white text-[11px] font-bold px-2 py-2 rounded-lg transition-colors shadow-sm">
                                <span class="ms text-[14px]">groups</span> Siswa
                            </a>
                            <a href="{{ route('programs.show', $p['id']) }}" class="inline-flex justify-center items-center gap-1 border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-[11px] font-bold px-3 py-2 rounded-lg transition-colors">
                                <span class="ms text-[14px]">visibility</span> Detail
                            </a>
                            @if(auth()->user()?->canModifyData())
                            <button type="button" onclick="openEditProgramModal({{ json_encode($p['model']) }})" class="p-1.5 border border-slate-300 bg-white hover:bg-slate-50 text-amber-600 rounded-lg transition-colors">
                                <span class="ms text-[16px]">edit</span>
                            </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-500">
                        <span class="ms text-slate-300 text-[48px] block mb-2">school</span>
                        <b class="text-sm text-slate-700">Tidak Ada Program</b>
                        <p class="text-xs text-slate-400 mt-1 mb-4">Belum ada program untuk filter ini.</p>
                        @if(auth()->user()?->canModifyData())
                        <button type="button" onclick="openCreateProgramModal()" class="inline-flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs font-bold transition-colors w-full">
                            <span class="ms text-[16px]">add_circle</span> Tambah Program
                        </button>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- =====================================================================
     MODAL TAMBAH PROGRAM PENDIDIKAN BARU
     ===================================================================== -->
@if(auth()->user()?->canModifyData())
<div class="modal-overlay" id="createProgramModal">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col" style="max-width:540px; width:95%;">
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="ms text-blue-400 text-[22px]">add_circle</span>
                <b class="text-sm font-bold tracking-wide">Tambah Program Pendidikan</b>
            </div>
            <button type="button" onclick="closeCreateProgramModal()" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('programs.store') }}">
            @csrf
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Satuan Pendidikan (Satdik): <span class="text-rose-500">*</span></label>
                    <select name="satdik_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition-colors" required>
                        @foreach($satdiks as $s)
                            <option value="{{ $s->id }}" {{ $selectedSatdikId == $s->id ? 'selected' : '' }}>
                                {{ $s->code }} — {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kode Program: <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-shadow" placeholder="Cth: DIKMABA-2026-II" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tahun Anggaran (TA): <span class="text-rose-500">*</span></label>
                        <input type="text" name="academic_year" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-shadow" value="2026" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Resmi Program: <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-shadow" placeholder="Cth: DIKMABA TA 2026 Gel II" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Gelombang / Batch:</label>
                        <input type="number" name="batch_number" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-shadow" value="1" min="1">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Program: <span class="text-rose-500">*</span></label>
                        <select name="status" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-shadow" required>
                            <option value="Berjalan" selected>Berjalan (Aktif)</option>
                            <option value="Perencanaan">Perencanaan</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Ditutup">Ditutup</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Mulai:</label>
                        <input type="date" name="start_date" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Selesai:</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-shadow">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Peleton / Rombel Awal:</label>
                    <input type="text" name="initial_classrooms" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-shadow" placeholder="Cth: Kompi A Peleton 1, Kompi A Peleton 2">
                    <small class="text-slate-500 font-medium text-[10px] mt-1.5 block">Pisahkan dengan koma jika membuat beberapa peleton sekaligus.</small>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-end gap-3 shrink-0">
                <button type="button" class="w-full sm:w-auto px-4 py-2.5 border border-slate-300 bg-white rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors" onclick="closeCreateProgramModal()">
                    Batal
                </button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition-colors shadow-md shadow-blue-500/20">
                    Simpan Program
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL EDIT PROGRAM PENDIDIKAN
     ===================================================================== -->
<div class="modal-overlay" id="editProgramModal">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col" style="max-width:540px; width:95%;">
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="ms text-amber-400 text-[22px]">edit</span>
                <b class="text-sm font-bold tracking-wide">Edit Data Program</b>
            </div>
            <button type="button" onclick="closeEditProgramModal()" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <form method="POST" id="editProgramForm">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Satuan Pendidikan:</label>
                    <select name="satdik_id" id="edit_satdik_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:bg-white focus:outline-none transition-colors" required>
                        @foreach($satdiks as $s)
                            <option value="{{ $s->id }}">{{ $s->code }} — {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kode Program:</label>
                        <input type="text" name="code" id="edit_code" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none transition-shadow" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">TA:</label>
                        <input type="text" name="academic_year" id="edit_academic_year" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none transition-shadow" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Resmi Program:</label>
                    <input type="text" name="name" id="edit_name" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none transition-shadow" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Gelombang:</label>
                        <input type="number" name="batch_number" id="edit_batch_number" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none transition-shadow" min="1">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Status:</label>
                        <select name="status" id="edit_status" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none transition-shadow" required>
                            <option value="Berjalan">Berjalan (Aktif)</option>
                            <option value="Perencanaan">Perencanaan</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Ditutup">Ditutup</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tgl Mulai:</label>
                        <input type="date" name="start_date" id="edit_start_date" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tgl Selesai:</label>
                        <input type="date" name="end_date" id="edit_end_date" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none transition-shadow">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-between gap-3 shrink-0 items-center">
                <button type="button" class="w-full sm:w-auto px-4 py-2.5 border border-slate-300 bg-white rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors" onclick="closeEditProgramModal()">
                    Batal
                </button>
                <div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">
                    <button type="button" onclick="confirmDeleteProgram()" class="w-full sm:w-auto px-4 py-2.5 border border-rose-300 bg-white hover:bg-rose-50 text-rose-600 rounded-lg text-sm font-bold transition-colors">
                        Hapus
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-bold transition-colors shadow-md shadow-amber-500/20">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
        
        <form id="deleteProgramForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    // Create Modal
    function openCreateProgramModal() {
        if(!document.getElementById('createProgramModal')) return;
        document.getElementById('createProgramModal').classList.add('active');
    }
    
    function closeCreateProgramModal() {
        if(!document.getElementById('createProgramModal')) return;
        document.getElementById('createProgramModal').classList.remove('active');
    }

    // Edit Modal
    function openEditProgramModal(program) {
        if(!document.getElementById('editProgramModal')) return;
        const form = document.getElementById('editProgramForm');
        form.action = `/programs/${program.id}`;
        
        document.getElementById('deleteProgramForm').action = `/programs/${program.id}`;

        document.getElementById('edit_satdik_id').value = program.satdik_id;
        document.getElementById('edit_code').value = program.code || '';
        document.getElementById('edit_name').value = program.name || '';
        document.getElementById('edit_academic_year').value = program.academic_year || '';
        document.getElementById('edit_batch_number').value = program.batch_number || '';
        document.getElementById('edit_status').value = program.status || 'Berjalan';
        
        if (program.start_date) {
            document.getElementById('edit_start_date').value = program.start_date.split('T')[0];
        } else {
            document.getElementById('edit_start_date').value = '';
        }
        
        if (program.end_date) {
            document.getElementById('edit_end_date').value = program.end_date.split('T')[0];
        } else {
            document.getElementById('edit_end_date').value = '';
        }

        document.getElementById('editProgramModal').classList.add('active');
    }
    
    function closeEditProgramModal() {
        if(!document.getElementById('editProgramModal')) return;
        document.getElementById('editProgramModal').classList.remove('active');
    }

    function confirmDeleteProgram() {
        if(confirm('Peringatan: Menghapus Program Pendidikan akan menghapus semua rombel di dalamnya (siswa tidak terhapus, hanya rombel-nya dikosongkan). Lanjutkan hapus program?')) {
            document.getElementById('deleteProgramForm').submit();
        }
    }

    // Close Modals on outside click
    window.addEventListener('click', function(e) {
        const createModal = document.getElementById('createProgramModal');
        const editModal = document.getElementById('editProgramModal');
        
        if (e.target === createModal) {
            closeCreateProgramModal();
        }
        if (e.target === editModal) {
            closeEditProgramModal();
        }
    });
</script>
@endsection
