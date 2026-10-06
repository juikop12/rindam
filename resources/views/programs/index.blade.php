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
            <p class="text-sm text-slate-500">
                Menu tersendiri untuk pembinaan dan monitoring kurikulum program pendidikan di seluruh Satdik Rindam III/Siliwangi (Secaba, Secata, Dodikjur, Dodiklatpur, Dodik Bela Negara).
                Hanya prajurit siswa berstatus <b>Aktif</b> yang terhitung dalam kekuatan program, sedangkan status <b>Selesai</b> otomatis masuk ke <b>Arsip</b>.
            </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center gap-2.5 shrink-0">
            @if(auth()->user()?->canModifyData())
            <button type="button" onclick="openCreateProgramModal()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                <span class="ms text-[18px]">add_circle</span> Tambah Program Pendidikan
            </button>
            @endif
            <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                <span class="ms text-[18px]">groups</span> Buka Buku Induk Serdik
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

    <!-- 4 STAT CARDS (Clean Minimalist Dashboard Style) -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        <!-- Total Program Terdaftar -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="text-sm font-medium text-slate-500 mb-4 flex items-center justify-between">
                <span>Total Program Terdaftar</span>
                <span class="ms text-sm text-slate-400">layers</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-900 mb-2">{{ $totalPrograms }}</div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-semibold">{{ $activePrograms }} Berjalan Aktif</span>
                    <span>&bull; TA 2026</span>
                </div>
            </div>
        </div>

        <!-- Siswa Aktif Terhitung -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="text-sm font-medium text-slate-500 mb-4 flex items-center justify-between">
                <span>Siswa Aktif Terhitung</span>
                <span class="ms text-sm text-slate-400">how_to_reg</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-900 mb-2">{{ number_format($totalCountedStudents) }}</div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded font-semibold">Kekuatan Operasional</span>
                    <span>Pendidikan</span>
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
                <div class="text-3xl font-bold text-slate-900 mb-2">{{ number_format($totalArchivedStudents) }}</div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-semibold">Selesai Pendidikan</span>
                    <span>&bull; Tidak Terhitung</span>
                </div>
            </div>
        </div>

        <!-- Satdik Penyelenggara -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="text-sm font-medium text-slate-500 mb-4 flex items-center justify-between">
                <span>Satdik Penyelenggara</span>
                <span class="ms text-sm text-slate-400">domain</span>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-900 mb-2">5</div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-semibold">Secaba, Secata, Dodikjur</span>
                    <span>Latpur, Belneg</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS FILTER SATDIK -->
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('programs.index', array_merge(request()->except(['satdik_id', 'page']))) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors border {{ empty($selectedSatdikId) ? 'bg-slate-100 text-slate-900 border-slate-300 shadow-sm font-semibold' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
            <span class="ms text-[18px]">domain</span>
            Semua Satdik
            <span class="px-2 py-0.5 rounded text-xs {{ empty($selectedSatdikId) ? 'bg-slate-200 text-slate-800' : 'bg-slate-100 text-slate-600' }}">{{ $totalPrograms }} Program</span>
        </a>
        @foreach($satdiks as $satdik)
            @php
                $pCount = $satdik->educationPrograms()->count();
                $isActive = ($selectedSatdikId == $satdik->id);
            @endphp
            <a href="{{ route('programs.index', array_merge(request()->except(['page']), ['satdik_id' => $satdik->id])) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors border {{ $isActive ? 'bg-slate-100 text-slate-900 border-slate-300 shadow-sm font-semibold' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                <span class="ms text-[18px]">military_tech</span>
                {{ $satdik->code }}
                <span class="px-2 py-0.5 rounded text-xs {{ $isActive ? 'bg-slate-200 text-slate-800' : 'bg-slate-100 text-slate-600' }}">{{ $pCount }} Program</span>
            </a>
        @endforeach
    </div>

    <!-- MAIN PROGRAM DATA CARD -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-8 overflow-hidden">
        <!-- Table Header & Actions/Filter -->
        <div class="px-5 py-4 border-b border-slate-200 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <div class="min-w-0">
                <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2 truncate">
                    <span class="ms text-slate-700 text-[22px] shrink-0">school</span>
                    <span>Daftar Program Pendidikan {{ $selectedSatdik ? '— ' . $selectedSatdik->name : 'Seluruh Satdik Rindam III/Siliwangi' }}</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5 truncate">Monitoring program, kurikulum diklat, dan alokasi rombel peleton</p>
            </div>

            <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap shrink-0">
                <!-- VIEW SWITCHER: TABEL vs KARTU -->
                <div class="inline-flex p-1 bg-slate-100 border border-slate-200 rounded-lg gap-1 shrink-0">
                    <button type="button" id="btnViewTable" onclick="switchProgramView('table')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-white text-slate-900 shadow-sm transition-all">
                        <span class="ms text-[16px]">table_rows</span> Tabel
                    </button>
                    <button type="button" id="btnViewCard" onclick="switchProgramView('card')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
                        <span class="ms text-[16px]">grid_view</span> Kartu
                    </button>
                </div>

                <!-- SEARCH & FILTER FORM -->
                <form method="GET" action="{{ route('programs.index') }}" class="flex items-center gap-2 flex-nowrap shrink-0">
                    @if($selectedSatdikId)
                        <input type="hidden" name="satdik_id" value="{{ $selectedSatdikId }}">
                    @endif

                    <select name="status" class="w-32 sm:w-36 h-9 bg-white border border-slate-200 text-slate-700 text-xs font-medium rounded-lg px-2.5 focus:ring-2 focus:ring-slate-900 focus:outline-none shrink-0" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Berjalan" {{ $statusFilter == 'Berjalan' ? 'selected' : '' }}>Berjalan</option>
                        <option value="Perencanaan" {{ $statusFilter == 'Perencanaan' ? 'selected' : '' }}>Perencanaan</option>
                        <option value="Selesai" {{ $statusFilter == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Ditutup" {{ $statusFilter == 'Ditutup' ? 'selected' : '' }}>Ditutup</option>
                    </select>

                    <div class="relative w-36 sm:w-44 shrink-0">
                        <span class="ms text-slate-400 text-[18px] absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
                        <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari Program, Kode..." class="w-full h-9 pl-8 pr-2.5 border border-slate-200 rounded-lg text-xs text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                    </div>

                    <button type="submit" class="h-9 bg-slate-900 hover:bg-slate-800 text-white px-3.5 rounded-lg text-xs font-semibold transition-colors shrink-0 flex items-center justify-center">
                        Filter
                    </button>
                    @if($keyword || $statusFilter)
                        <a href="{{ route('programs.index', ['satdik_id' => $selectedSatdikId]) }}" class="h-9 text-xs font-medium text-slate-500 hover:text-slate-800 px-2 rounded-lg flex items-center justify-center shrink-0">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- =====================================================================
             1. TAMPILAN TABEL RESMI PROGRAM
             ===================================================================== -->
        <div id="programTableView">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider">
                        <tr>
                            <th class="px-4 py-3 border-b border-slate-200 w-12 text-center">No</th>
                            <th class="px-4 py-3 border-b border-slate-200">Satdik</th>
                            <th class="px-5 py-3 border-b border-slate-200">Nama Program Pendidikan</th>
                            <th class="px-4 py-3 border-b border-slate-200">Kode Program</th>
                            <th class="px-4 py-3 border-b border-slate-200">TA & Gelombang</th>
                            <th class="px-4 py-3 border-b border-slate-200 text-center">Siswa Aktif</th>
                            <th class="px-4 py-3 border-b border-slate-200 text-center">Arsip Selesai</th>
                            <th class="px-4 py-3 border-b border-slate-200">Peleton</th>
                            <th class="px-4 py-3 border-b border-slate-200 text-center">Status</th>
                            <th class="px-4 py-3 border-b border-slate-200 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($programs as $idx => $p)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3.5 text-center text-xs font-semibold text-slate-500 align-middle">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-4 py-3.5 align-middle">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-slate-900 text-white">
                                        {{ $p['satdik_code'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 align-middle">
                                    <a href="{{ route('programs.show', $p['id']) }}" class="font-bold text-sm text-slate-900 hover:text-blue-600 transition-colors block leading-snug">
                                        {{ $p['name'] }}
                                    </a>
                                    <div class="flex items-center gap-1 text-[11px] text-slate-500 mt-1">
                                        <span class="ms text-[13px] text-slate-400">location_on</span>
                                        <span>{{ $p['location'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 align-middle">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $p['code'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 align-middle">
                                    <span class="font-bold text-xs text-slate-800">TA {{ $p['academic_year'] }}</span>
                                    <span class="block text-[11px] text-slate-500">Gel. {{ $p['batch_number'] }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-center align-middle">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Terhitung dalam kekuatan aktif">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $p['counted_students'] }} Serdik
                                    </span>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $p['ready_students'] }} Siap &bull; {{ $p['sick_students'] }} Sakit
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center align-middle">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200" title="Alumni selesai pendidikan (tidak terhitung)">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        {{ $p['archived_students'] }} Serdik
                                    </span>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Lulus / Tamat
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 align-middle">
                                    <span class="font-bold text-xs text-slate-800">{{ $p['classrooms']->count() }} Peleton</span>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Kapasitas: {{ $p['classrooms']->sum('capacity') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center align-middle">
                                    @if($p['status'] == 'Berjalan')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Berjalan
                                        </span>
                                    @elseif($p['status'] == 'Perencanaan')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Perencanaan
                                        </span>
                                    @elseif($p['status'] == 'Selesai')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> {{ $p['status'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap align-middle">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('students.index', ['program_id' => $p['id']]) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Lihat Siswa Program">
                                            <span class="ms text-[18px]">groups</span>
                                        </a>
                                        <a href="{{ route('programs.show', $p['id']) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Detail & Peleton">
                                            <span class="ms text-[18px]">visibility</span>
                                        </a>
                                        @if(auth()->user()?->canModifyData())
                                        <button type="button" onclick="openEditProgramModal({{ json_encode($p['model']) }})" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Edit Data Program">
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
        </div>

        <!-- =====================================================================
             2. TAMPILAN KARTU GRID PROGRAM (OPSIONAL DAPAT DIBUKA LEWAT TOGGLE)
             ===================================================================== -->
        <div id="programCardView" class="hidden p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse($programs as $p)
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between overflow-hidden p-5">
                        <div>
                            <!-- CARD TOP -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-slate-900 text-white">
                                        {{ $p['satdik_code'] }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $p['code'] }}
                                    </span>
                                </div>
                                <div>
                                    @if($p['status'] == 'Berjalan')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Berjalan
                                        </span>
                                    @elseif($p['status'] == 'Perencanaan')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Rencana
                                        </span>
                                    @elseif($p['status'] == 'Selesai')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> {{ $p['status'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- CARD TITLE & LOCATION -->
                            <h3 class="text-base font-bold text-slate-900 mb-1 leading-snug">
                                <a href="{{ route('programs.show', $p['id']) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $p['name'] }}
                                </a>
                            </h3>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mb-4 flex-wrap">
                                <span class="flex items-center gap-1">
                                    <span class="ms text-[14px] text-slate-400">location_on</span>
                                    {{ $p['location'] }}
                                </span>
                                <span>&bull;</span>
                                <span>TA {{ $p['academic_year'] }}</span>
                                <span>&bull;</span>
                                <span>Gel. {{ $p['batch_number'] }}</span>
                            </div>

                            <!-- BOX KEKUATAN SISWA -->
                            <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl mb-4">
                                <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                                    <div class="text-[11px] font-semibold text-slate-500">Siswa Aktif</div>
                                    <div class="text-xl font-bold text-emerald-600 mt-0.5">
                                        {{ $p['counted_students'] }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ $p['ready_students'] }} Siap &bull; {{ $p['sick_students'] }} Sakit
                                    </div>
                                </div>
                                <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                                    <div class="text-[11px] font-semibold text-slate-500">Arsip Selesai</div>
                                    <div class="text-xl font-bold text-slate-700 mt-0.5">
                                        {{ $p['archived_students'] }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ $p['finished_students'] }} Tamat Diklat
                                    </div>
                                </div>
                            </div>

                            <!-- PELETON & ROMBEL -->
                            <div class="mb-4">
                                <div class="text-xs font-semibold text-slate-700 mb-1.5 flex items-center gap-1.5">
                                    <span class="ms text-[15px] text-slate-400">groups_2</span>
                                    <span>Peleton ({{ $p['classrooms']->count() }} Kelas):</span>
                                </div>
                                @if($p['classrooms']->count() > 0)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($p['classrooms'] as $cls)
                                            <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-white text-slate-700 border border-slate-200">
                                                {{ $cls->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-xs text-slate-400 italic">
                                        Belum ada peleton yang dialokasikan.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                            <a href="{{ route('students.index', ['program_id' => $p['id']]) }}" class="inline-flex items-center justify-center gap-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors flex-1 shadow-sm">
                                <span class="ms text-[16px]">groups</span> Lihat Siswa
                            </a>
                            <a href="{{ route('programs.show', $p['id']) }}" class="inline-flex items-center justify-center gap-1 border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold px-3 py-2 rounded-lg transition-colors" title="Detail & Kelola Program">
                                <span class="ms text-[16px]">visibility</span> Detail
                            </a>
                            @if(auth()->user()?->canModifyData())
                            <button type="button" onclick="openEditProgramModal({{ json_encode($p['model']) }})" class="p-2 border border-slate-300 hover:bg-slate-50 text-slate-600 rounded-lg transition-colors" title="Edit Data Program">
                                <span class="ms text-[16px]">edit</span>
                            </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white border border-slate-200 rounded-xl p-12 text-center text-slate-500">
                        <span class="ms text-slate-300 text-[52px] block mb-2">school</span>
                        <b class="text-base text-slate-700">Tidak Ada Program Pendidikan Ditemukan</b>
                        <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto mb-4">
                            Belum ada program pendidikan yang terdaftar untuk filter Satdik atau kata kunci pencarian ini.
                        </p>
                        @if(auth()->user()?->canModifyData())
                        <button type="button" onclick="openCreateProgramModal()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-xs font-semibold transition-colors">
                            <span class="ms text-[16px]">add_circle</span> Tambah Program Baru
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
                <span class="ms text-amber-400 text-[22px]">add_circle</span>
                <b class="text-sm font-bold tracking-wide">Tambah Program Pendidikan Baru</b>
            </div>
            <button type="button" onclick="closeCreateProgramModal()" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('programs.store') }}">
            @csrf
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan Pendidikan (Satdik): <span class="text-red-500">*</span></label>
                    <select name="satdik_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" required>
                        @foreach($satdiks as $s)
                            <option value="{{ $s->id }}" {{ $selectedSatdikId == $s->id ? 'selected' : '' }}>
                                {{ $s->code }} — {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Program: <span class="text-red-500">*</span></label>
                        <input type="text" name="code" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" placeholder="Contoh: DIKMABA-2026-II" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Anggaran (TA): <span class="text-red-500">*</span></label>
                        <input type="text" name="academic_year" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" value="2026" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Resmi Program Pendidikan: <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" placeholder="Contoh: DIKMABA TA 2026 Gel II" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Gelombang / Batch:</label>
                        <input type="number" name="batch_number" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" value="1" min="1">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status Program: <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" required>
                            <option value="Berjalan" selected>Berjalan (Aktif)</option>
                            <option value="Perencanaan">Perencanaan</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Ditutup">Ditutup</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai:</label>
                        <input type="date" name="start_date" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai:</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Peleton / Rombel Awal (Opsional):</label>
                    <input type="text" name="initial_classrooms" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" placeholder="Contoh: Kompi A Peleton 1, Kompi A Peleton 2">
                    <small class="text-slate-400 text-[11px] mt-0.5 block">Pisahkan dengan tanda koma untuk membuat beberapa peleton sekaligus.</small>
                </div>
            </div>

            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex justify-end gap-2.5 shrink-0">
                <button type="button" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors" onclick="closeCreateProgramModal()">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-sm">
                    <span class="ms text-[16px]">save</span> Simpan Program Baru
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
                <b class="text-sm font-bold tracking-wide">Edit Program Pendidikan Satdik</b>
            </div>
            <button type="button" onclick="closeEditProgramModal()" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <form id="editProgramForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan Pendidikan (Satdik): <span class="text-red-500">*</span></label>
                    <select name="satdik_id" id="editSatdikId" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" required>
                        @foreach($satdiks as $s)
                            <option value="{{ $s->id }}">{{ $s->code }} — {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Program: <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="editCode" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Anggaran (TA): <span class="text-red-500">*</span></label>
                        <input type="text" name="academic_year" id="editYear" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Resmi Program: <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="editName" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Gelombang / Batch:</label>
                        <input type="number" name="batch_number" id="editBatch" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" min="1">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status Program: <span class="text-red-500">*</span></label>
                        <select name="status" id="editStatus" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none" required>
                            <option value="Berjalan">Berjalan (Aktif)</option>
                            <option value="Perencanaan">Perencanaan</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Ditutup">Ditutup</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai:</label>
                        <input type="date" name="start_date" id="editStartDate" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai:</label>
                        <input type="date" name="end_date" id="editEndDate" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex justify-end gap-2.5 shrink-0">
                <button type="button" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors" onclick="closeEditProgramModal()">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-sm">
                    <span class="ms text-[16px]">save</span> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
const PROGRAM_VIEW_KEY = 'sipandu_program_view_mode';

function switchProgramView(mode) {
    const tblView = document.getElementById('programTableView');
    const crdView = document.getElementById('programCardView');
    const btnTbl = document.getElementById('btnViewTable');
    const btnCrd = document.getElementById('btnViewCard');

    if (!tblView || !crdView) return;

    if (mode === 'card') {
        tblView.classList.add('hidden');
        crdView.classList.remove('hidden');
        if (btnTbl) {
            btnTbl.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all';
        }
        if (btnCrd) {
            btnCrd.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-white text-slate-900 shadow-sm transition-all';
        }
        localStorage.setItem(PROGRAM_VIEW_KEY, 'card');
    } else {
        tblView.classList.remove('hidden');
        crdView.classList.add('hidden');
        if (btnTbl) {
            btnTbl.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-white text-slate-900 shadow-sm transition-all';
        }
        if (btnCrd) {
            btnCrd.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all';
        }
        localStorage.setItem(PROGRAM_VIEW_KEY, 'table');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem(PROGRAM_VIEW_KEY) || 'table';
    switchProgramView(saved);
});

function openCreateProgramModal() {
    if (!document.getElementById('createProgramModal')) return;
    document.getElementById('createProgramModal').classList.add('active');
}
function closeCreateProgramModal() {
    if (!document.getElementById('createProgramModal')) return;
    document.getElementById('createProgramModal').classList.remove('active');
}

function openEditProgramModal(prog) {
    if (!document.getElementById('editProgramModal')) return;
    const form = document.getElementById('editProgramForm');
    form.action = `/programs/${prog.id}`;
    document.getElementById('editSatdikId').value = prog.satdik_id;
    document.getElementById('editCode').value = prog.code;
    document.getElementById('editName').value = prog.name;
    document.getElementById('editYear').value = prog.academic_year;
    document.getElementById('editBatch').value = prog.batch_number || 1;
    document.getElementById('editStatus').value = prog.status || 'Berjalan';
    document.getElementById('editStartDate').value = prog.start_date ? prog.start_date.split('T')[0] : '';
    document.getElementById('editEndDate').value = prog.end_date ? prog.end_date.split('T')[0] : '';

    document.getElementById('editProgramModal').classList.add('active');
}
function closeEditProgramModal() {
    document.getElementById('editProgramModal').classList.remove('active');
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
</script>
@endsection
