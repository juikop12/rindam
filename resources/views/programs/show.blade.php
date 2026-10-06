@extends('layouts.app')

@section('title', $program->name . ' — SIPANDU-WBK')

@section('content')

<!-- BREADCRUMB / HEADER BANNER -->
<div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 border-l-4 border-l-amber-500 rounded-xl p-6 sm:p-8 shadow-sm mb-6 text-white relative overflow-hidden">
    <div class="relative z-10">
        <div class="flex flex-wrap items-center gap-2.5 mb-4">
            <a href="{{ route('programs.index') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-md text-[10px] font-bold tracking-wide transition-colors">
                <span class="ms text-[14px]">arrow_back</span> Kembali ke Menu Program
            </a>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-md text-[10px] font-bold tracking-wide">
                <span class="ms text-[14px]">domain</span> {{ $program->satdik->code }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/10 text-white border border-white/20 rounded-md text-[10px] font-bold tracking-wide">
                TA {{ $program->academic_year }}
            </span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold mb-3 tracking-tight">
            {{ $program->name }}
        </h1>
        <p class="text-sm text-slate-300 leading-relaxed max-w-3xl mb-6">
            Satuan Pendidikan: <b class="text-white">{{ $program->satdik->name }}</b> ({{ $program->satdik->location ?? 'Ksatrian Rindam' }}). 
            Kode Program: <code class="bg-slate-800 px-1.5 py-0.5 rounded">{{ $program->code }}</code> &bull; Status: <b class="text-white">{{ $program->status }}</b>.
        </p>
        
        <div class="flex flex-col sm:flex-row items-center gap-3">
            @if(auth()->user()?->canModifyData())
                <a href="{{ route('students.index', ['satdik_id' => $program->satdik_id, 'program_id' => $program->id, 'action' => 'create']) }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm shadow-amber-500/20">
                    <span class="ms text-[18px]">person_add</span> Tambah Siswa ke Program
                </a>
            @endif
            <a href="{{ route('students.index', ['program_id' => $program->id]) }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-white/10 hover:bg-white/20 text-white border border-white/20 px-4 py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm">
                <span class="ms text-[18px]">groups</span> Buka di Buku Induk
            </a>
        </div>
    </div>
</div>

<!-- STATS PROGRAM INI: AKTIF TERHITUNG VS ARSIP SELESAI -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center justify-between">
            <span>Siswa Aktif</span>
            <span class="ms text-emerald-500 text-[18px]">how_to_reg</span>
        </div>
        <div>
            <div class="text-2xl font-bold text-emerald-600 mb-1">{{ $countedCount }}</div>
            <div class="text-[10px] font-semibold text-slate-500">
                {{ $readyCount }} Sehat &bull; {{ $sickCount }} Sakit
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center justify-between">
            <span>Arsip Siswa</span>
            <span class="ms text-slate-500 text-[18px]">archive</span>
        </div>
        <div>
            <div class="text-2xl font-bold text-slate-700 mb-1">{{ $archivedCount }}</div>
            <div class="text-[10px] font-semibold text-slate-500">
                {{ $finishedCount }} Tamat / Lulus (Tak Terhitung)
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center justify-between">
            <span>Peleton/Rombel</span>
            <span class="ms text-amber-500 text-[18px]">groups_2</span>
        </div>
        <div>
            <div class="text-2xl font-bold text-slate-900 mb-1">{{ $program->classrooms->count() }}</div>
            <div class="text-[10px] font-semibold text-slate-500">
                Kapasitas: {{ $program->classrooms->sum('capacity') }} Serdik
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide mb-3 flex items-center justify-between">
            <span>Total Rekam Data</span>
            <span class="ms text-blue-500 text-[18px]">school</span>
        </div>
        <div>
            <div class="text-2xl font-bold text-slate-900 mb-1">{{ $program->students->count() }}</div>
            <div class="text-[10px] font-semibold text-slate-500">
                Aktif + Arsip Selesai
            </div>
        </div>
    </div>
</div>

<!-- PELETON & INFORMASI PROGRAM -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- KARTU 1: DETAIL KURIKULUM & JADWAL -->
    <div class="lg:col-span-1 bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col h-full">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="ms text-amber-500 text-[20px]">info</span> Data Program
            </h3>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">{{ $program->status }}</span>
        </div>
        <div class="p-5 flex-1">
            <dl class="space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <dt class="text-[11px] font-semibold text-slate-500 mb-0.5">Satuan Pendidikan</dt>
                    <dd class="text-sm font-bold text-slate-900">{{ $program->satdik->name }}</dd>
                </div>
                <div class="border-b border-slate-100 pb-3">
                    <dt class="text-[11px] font-semibold text-slate-500 mb-0.5">Kode Program</dt>
                    <dd class="text-sm font-mono font-bold text-slate-700">{{ $program->code }}</dd>
                </div>
                <div class="border-b border-slate-100 pb-3">
                    <dt class="text-[11px] font-semibold text-slate-500 mb-0.5">Tahun Anggaran</dt>
                    <dd class="text-sm font-bold text-slate-900">TA {{ $program->academic_year }} (Gelombang {{ $program->batch_number }})</dd>
                </div>
                <div class="grid grid-cols-2 gap-3 border-b border-slate-100 pb-3">
                    <div>
                        <dt class="text-[11px] font-semibold text-slate-500 mb-0.5">Tanggal Mulai</dt>
                        <dd class="text-xs font-semibold text-slate-800">{{ $program->start_date ? $program->start_date->format('d M Y') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold text-slate-500 mb-0.5">Tanggal Selesai</dt>
                        <dd class="text-xs font-semibold text-slate-800">{{ $program->end_date ? $program->end_date->format('d M Y') : '-' }}</dd>
                    </div>
                </div>
                <div>
                    <dt class="text-[11px] font-semibold text-slate-500 mb-1">Status Keaktifan</dt>
                    <dd>
                        @if($program->status == 'Berjalan')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800">Berjalan (Aktif)</span>
                        @elseif($program->status == 'Selesai')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-slate-200 text-slate-700">Selesai (Arsip)</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-800">{{ $program->status }}</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- KARTU 2: ROMBONGAN BELAJAR / KOMPI & PELETON -->
    <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col h-full">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
            <div class="flex items-center gap-3">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="ms text-blue-500 text-[20px]">groups_2</span> Kompi & Peleton
                </h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">{{ $program->classrooms->count() }} Rombel</span>
            </div>
            @if(auth()->user()?->canModifyData())
                <button type="button" class="inline-flex justify-center items-center gap-1.5 px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold shadow-sm transition-colors w-full sm:w-auto" onclick="openCreateClassroomModal()">
                    <span class="ms text-[16px]">add_circle</span> Buat Peleton Baru
                </button>
            @endif
        </div>
        <div class="p-0 flex-1 overflow-x-auto">
            <!-- Desktop view -->
            <div class="hidden sm:block">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3 border-b border-slate-200">Kompi & Peleton</th>
                            <th class="px-4 py-3 border-b border-slate-200">Kode</th>
                            <th class="px-4 py-3 border-b border-slate-200">Danton / Danki</th>
                            <th class="px-4 py-3 border-b border-slate-200 w-48 text-center">Kapasitas</th>
                            <th class="px-5 py-3 border-b border-slate-200 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($program->classrooms as $cls)
                            @php
                                $stuCount = $cls->students()->count();
                                $pct = $cls->capacity > 0 ? min(100, round(($stuCount / $cls->capacity) * 100)) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-3 align-middle">
                                    <div class="text-sm font-bold text-slate-900">{{ $cls->name }}</div>
                                    <div class="flex items-center gap-1 mt-1">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600">{{ $cls->company ?? 'Kompi' }}</span>
                                        @if($cls->platoon)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-700">{{ $cls->platoon }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <span class="font-mono text-[11px] font-bold text-slate-500">{{ $cls->code }}</span>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="text-xs font-bold text-slate-800">{{ $cls->platoon_leader_name ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="flex items-center justify-between text-[11px] font-semibold mb-1">
                                        <span class="{{ $stuCount >= $cls->capacity ? 'text-rose-600' : 'text-slate-700' }}">{{ $stuCount }} Serdik</span>
                                        <span class="text-slate-400">Maks {{ $cls->capacity }}</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-rose-500' : ($pct >= 80 ? 'bg-amber-400' : 'bg-emerald-500') }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 align-middle text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        @if(auth()->user()?->canModifyData())
                                            <button type="button" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition-colors" onclick='openEditClassroomModal(@json($cls))' title="Edit Peleton">
                                                <span class="ms text-[16px]">edit</span>
                                            </button>
                                            @if($stuCount == 0)
                                                <form method="POST" action="{{ route('classrooms.destroy', $cls->id) }}" onsubmit="return confirm('Hapus peleton [{{ $cls->name }}]?')" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Peleton">
                                                        <span class="ms text-[16px]">delete</span>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="p-1.5 rounded-lg text-slate-300 cursor-not-allowed" title="Tidak dapat dihapus karena ada {{ $stuCount }} siswa" disabled>
                                                    <span class="ms text-[16px]">delete</span>
                                                </button>
                                            @endif
                                        @else
                                            <span class="px-2 py-1 rounded text-[10px] font-bold bg-slate-100 text-slate-500">View Only</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-500">
                                    <span class="ms text-[32px] text-slate-300 block mb-2">groups_2</span>
                                    <div class="text-sm font-semibold text-slate-700">Belum ada Kompi / Peleton</div>
                                    @if(auth()->user()?->canModifyData())
                                        <button type="button" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-[11px] font-bold transition-colors" onclick="openCreateClassroomModal()">
                                            <span class="ms text-[14px]">add_circle</span> Buat Peleton
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="block sm:hidden divide-y divide-slate-100">
                @forelse($program->classrooms as $cls)
                    @php
                        $stuCount = $cls->students()->count();
                        $pct = $cls->capacity > 0 ? min(100, round(($stuCount / $cls->capacity) * 100)) : 0;
                    @endphp
                    <div class="p-4 flex flex-col gap-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-sm font-bold text-slate-900">{{ $cls->name }}</div>
                                <div class="flex items-center gap-1 mt-1">
                                    <span class="font-mono text-[10px] font-bold text-slate-400 border border-slate-200 px-1 rounded">{{ $cls->code }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600">{{ $cls->company ?? 'Kompi' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                @if(auth()->user()?->canModifyData())
                                    <button type="button" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100" onclick='openEditClassroomModal(@json($cls))'>
                                        <span class="ms text-[16px]">edit</span>
                                    </button>
                                    @if($stuCount == 0)
                                        <form method="POST" action="{{ route('classrooms.destroy', $cls->id) }}" onsubmit="return confirm('Hapus peleton?')" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50">
                                                <span class="ms text-[16px]">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                            <div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Komandan</div>
                                <div class="text-[11px] font-bold text-slate-800">{{ $cls->platoon_leader_name ?: '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wide flex justify-between">
                                    <span>Siswa</span>
                                    <span>Maks {{ $cls->capacity }}</span>
                                </div>
                                <div class="text-[11px] font-bold text-slate-800 mb-1">{{ $stuCount }} Serdik</div>
                                <div class="w-full h-1 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-rose-500' : ($pct >= 80 ? 'bg-amber-400' : 'bg-emerald-500') }}" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500">
                        <span class="ms text-[32px] text-slate-300 block mb-2">groups_2</span>
                        <div class="text-xs font-semibold text-slate-700">Belum ada Peleton</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- SEGMENTED TABS & FILTERS -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
    <div class="flex p-1 bg-slate-100 border border-slate-200 rounded-xl overflow-x-auto scrollbar-hide shrink-0">
        <a href="{{ route('programs.show', array_merge(['program' => $program->id], request()->except(['page', 'tab']), ['tab' => 'all'])) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-[11px] sm:text-xs font-bold transition-colors whitespace-nowrap {{ $tab == 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50' }}">
            <span class="ms text-[16px] {{ $tab == 'all' ? 'text-slate-700' : 'text-slate-400' }}">people</span> 
            Semua
            <span class="px-1.5 py-0.5 rounded text-[9px] bg-slate-100 text-slate-600 border border-slate-200">{{ $program->students->count() }}</span>
        </a>
        <a href="{{ route('programs.show', array_merge(['program' => $program->id], request()->except(['page', 'tab']), ['tab' => 'aktif'])) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-[11px] sm:text-xs font-bold transition-colors whitespace-nowrap {{ $tab == 'aktif' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50' }}">
            <span class="ms text-[16px] {{ $tab == 'aktif' ? 'text-emerald-500' : 'text-slate-400' }}">how_to_reg</span> 
            Aktif
            <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-50 text-emerald-600 border border-emerald-100">{{ $countedCount }}</span>
        </a>
        <a href="{{ route('programs.show', array_merge(['program' => $program->id], request()->except(['page', 'tab']), ['tab' => 'arsip'])) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-[11px] sm:text-xs font-bold transition-colors whitespace-nowrap {{ $tab == 'arsip' ? 'bg-white text-slate-700 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50' }}">
            <span class="ms text-[16px] {{ $tab == 'arsip' ? 'text-slate-500' : 'text-slate-400' }}">archive</span> 
            Arsip Selesai
            <span class="px-1.5 py-0.5 rounded text-[9px] bg-slate-100 text-slate-600 border border-slate-200">{{ $archivedCount }}</span>
        </a>
    </div>

    <!-- FILTER PELETON -->
    <form method="GET" action="{{ route('programs.show', $program->id) }}" class="flex items-center gap-2 w-full md:w-auto">
        @if($tab && $tab !== 'all')
            <input type="hidden" name="tab" value="{{ $tab }}">
        @endif
        <select name="classroom_id" class="w-1/2 md:w-44 h-9 px-2.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
            <option value="">Semua Peleton</option>
            @foreach($program->classrooms as $c)
                <option value="{{ $c->id }}" {{ $classroomId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <div class="relative w-1/2 md:w-44">
            <span class="ms text-[16px] text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
            <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari Nosik/Nama" class="w-full h-9 pl-8 pr-2.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400">
        </div>
        @if($classroomId || $keyword)
            <a href="{{ route('programs.show', ['program' => $program->id, 'tab' => $tab]) }}" class="h-9 px-2 flex items-center justify-center text-[10px] font-bold text-slate-500 hover:text-slate-700 transition-colors">Reset</a>
        @endif
    </form>
</div>

<!-- TABEL SISWA DI PROGRAM INI -->
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden flex flex-col mb-8">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <span class="ms text-blue-500 text-[20px]">badge</span>
            Daftar Prajurit Siswa
        </h3>
        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">{{ $students->total() }} Serdik</span>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-3 border-b border-slate-200 w-12 text-center">No</th>
                    <th class="px-4 py-3 border-b border-slate-200">Siswa / Serdik</th>
                    <th class="px-4 py-3 border-b border-slate-200">Peleton & Kompi</th>
                    <th class="px-4 py-3 border-b border-slate-200">Asal Kesatuan</th>
                    <th class="px-4 py-3 border-b border-slate-200 text-center">Medis</th>
                    <th class="px-4 py-3 border-b border-slate-200 text-center">Status</th>
                    <th class="px-4 py-3 border-b border-slate-200 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($students as $idx => $st)
                    <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer group" onclick="if(!event.target.closest('a') && !event.target.closest('button')) window.location='{{ route('students.show', $st) }}'">
                        <td class="px-4 py-3.5 text-center text-xs font-semibold text-slate-500 align-middle">{{ $students->firstItem() + $idx }}</td>
                        <td class="px-4 py-3.5 align-middle">
                            <a href="{{ route('students.show', $st) }}" class="font-bold text-sm text-slate-900 group-hover:text-blue-600 transition-colors block leading-snug">
                                {{ $st->full_name }}
                            </a>
                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-slate-100 text-slate-600 mt-1 border border-slate-200">
                                {{ $st->nosik }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 align-middle">
                            <div class="text-xs font-bold text-slate-800">{{ $st->classroom->name ?? 'Belum Ditentukan' }}</div>
                        </td>
                        <td class="px-4 py-3.5 align-middle">
                            <div class="text-[11px] font-semibold text-slate-600">{{ $st->origin_military_unit ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-center align-middle">
                            @php $u = $st->unified_status; @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold {{ $u['badge'] == 'badge-green' ? 'bg-emerald-100 text-emerald-800' : ($u['badge'] == 'badge-gold' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                <span class="ms text-[12px]">{{ $u['icon'] }}</span> {{ $u['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center align-middle">
                            @if($st->is_counted)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="ms text-[12px]">check_circle</span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="ms text-[12px]">archive</span> Arsip
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap align-middle">
                            <div class="inline-flex items-center gap-1">
                                <a href="{{ route('students.show', $st) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Dossier Lengkap">
                                    <span class="ms text-[18px]">visibility</span>
                                </a>
                                <a href="{{ route('health.show', $st) }}" class="p-1.5 rounded-lg text-emerald-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors" title="Rekam Medis">
                                    <span class="ms text-[18px]">medical_services</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                            <span class="ms text-[40px] text-slate-300 block mb-2">folder_off</span>
                            <b class="text-sm text-slate-700">Tidak ada serdik ditemukan</b>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Table (Card List) -->
    <div class="block md:hidden divide-y divide-slate-100">
        @forelse($students as $st)
            @php $u = $st->unified_status; @endphp
            <div class="p-4 flex flex-col gap-3">
                <div class="flex items-start justify-between">
                    <div>
                        <a href="{{ route('students.show', $st) }}" class="text-sm font-bold text-slate-900 block mb-1">
                            {{ $st->full_name }}
                        </a>
                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $st->nosik }}
                        </span>
                    </div>
                    <div>
                        @if($st->is_counted)
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200">Arsip</span>
                        @endif
                    </div>
                </div>

                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 flex flex-col gap-1.5">
                    <div class="flex justify-between items-center text-[11px]">
                        <span class="font-bold text-slate-400 uppercase">Peleton</span>
                        <span class="font-bold text-slate-800">{{ $st->classroom->name ?? 'Belum ada' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[11px]">
                        <span class="font-bold text-slate-400 uppercase">Kesehatan</span>
                        <span class="inline-flex items-center gap-1 font-bold {{ $u['badge'] == 'badge-green' ? 'text-emerald-700' : ($u['badge'] == 'badge-gold' ? 'text-amber-600' : 'text-rose-600') }}">
                            <span class="ms text-[12px]">{{ $u['icon'] }}</span> {{ $u['label'] }}
                        </span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('students.show', $st) }}" class="flex-1 inline-flex justify-center items-center gap-1 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-bold transition-colors">
                        <span class="ms text-[14px]">visibility</span> Dossier
                    </a>
                    <a href="{{ route('health.show', $st) }}" class="flex-1 inline-flex justify-center items-center gap-1 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-[10px] font-bold transition-colors">
                        <span class="ms text-[14px]">medical_services</span> Medis
                    </a>
                </div>
            </div>
        @empty
            <div class="p-10 text-center text-slate-500">
                <span class="ms text-[40px] text-slate-300 block mb-2">folder_off</span>
                <b class="text-sm text-slate-700">Tidak ada serdik</b>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($students->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="text-[11px] font-semibold text-slate-500 text-center sm:text-left">
                Menampilkan <b class="text-slate-900">{{ $students->firstItem() ?? 0 }}</b> - <b class="text-slate-900">{{ $students->lastItem() ?? 0 }}</b> dari <b class="text-slate-900">{{ $students->total() }}</b> serdik
            </div>
            <div>
                {{ $students->links() }}
            </div>
        </div>
    @endif
</div>

<!-- =====================================================================
     MODAL TAMBAH KOMPI & PELETON BARU
     ===================================================================== -->
@if(auth()->user()?->canModifyData())
<div class="modal-overlay" id="createClassroomModal">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col" style="max-width:540px; width:95%;">
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="ms text-blue-400 text-[22px]">add_circle</span>
                <b class="text-sm font-bold tracking-wide">Buat Kompi & Peleton</b>
            </div>
            <button type="button" onclick="closeCreateClassroomModal()" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('classrooms.store', $program->id) }}">
            @csrf
            <input type="hidden" name="education_program_id" value="{{ $program->id }}">
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div class="bg-blue-50/50 border border-blue-100 p-3 rounded-lg text-xs font-semibold text-slate-700 flex items-center gap-2">
                    <span class="ms text-blue-500 text-[18px]">info</span>
                    Program: {{ $program->name }} ({{ $program->satdik->code }})
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Kompi: <span class="text-rose-500">*</span></label>
                        <input type="text" list="createCompanyList" name="company" id="create_company" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Cth: Kompi A" oninput="updateCreateClassName()" required>
                        <datalist id="createCompanyList">
                            <option value="Kompi A">
                            <option value="Kompi B">
                            <option value="Kompi C">
                            <option value="Kompi Senapan A">
                            <option value="Kompi Senapan B">
                            <option value="Kompi Markas">
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Peleton: <span class="text-rose-500">*</span></label>
                        <input type="text" list="createPlatoonList" name="platoon" id="create_platoon" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Cth: Peleton 1" oninput="updateCreateClassName()" required>
                        <datalist id="createPlatoonList">
                            <option value="Peleton 1">
                            <option value="Peleton 2">
                            <option value="Peleton 3">
                            <option value="Peleton 4">
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap Peleton: <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="create_name" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none" placeholder="Kompi A Peleton 1" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kode Kelas (Opsional):</label>
                        <input type="text" name="code" id="create_code" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Otomatis jika kosong">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kapasitas (Orang):</label>
                        <input type="number" name="capacity" id="create_capacity" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none" value="35" min="1" max="250">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Komandan Peleton (Danton):</label>
                    <input type="text" name="platoon_leader_name" id="create_danton" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Cth: Lettu Inf Suryadi">
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-end gap-3 shrink-0">
                <button type="button" class="w-full sm:w-auto px-4 py-2.5 border border-slate-300 bg-white rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors" onclick="closeCreateClassroomModal()">Batal</button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition-colors shadow-md shadow-blue-500/20">
                    Simpan Peleton
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================
     MODAL EDIT KOMPI & PELETON
     ===================================================================== -->
<div class="modal-overlay" id="editClassroomModal">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col" style="max-width:540px; width:95%;">
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="ms text-amber-400 text-[22px]">edit</span>
                <b class="text-sm font-bold tracking-wide">Edit Kompi & Peleton</b>
            </div>
            <button type="button" onclick="closeEditClassroomModal()" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <form method="POST" id="editClassroomForm">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Kompi:</label>
                        <input type="text" list="editCompanyList" name="company" id="edit_company" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none" oninput="updateEditClassName()">
                        <datalist id="editCompanyList">
                            <option value="Kompi A">
                            <option value="Kompi B">
                            <option value="Kompi C">
                            <option value="Kompi Senapan A">
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Peleton:</label>
                        <input type="text" list="editPlatoonList" name="platoon" id="edit_platoon" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none" oninput="updateEditClassName()">
                        <datalist id="editPlatoonList">
                            <option value="Peleton 1">
                            <option value="Peleton 2">
                            <option value="Peleton 3">
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap Peleton: <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="edit_name" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm font-bold text-slate-900 focus:ring-2 focus:ring-amber-500 focus:bg-white focus:outline-none" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kode Kelas: <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" id="edit_code" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kapasitas (Orang): <span class="text-rose-500">*</span></label>
                        <input type="number" name="capacity" id="edit_capacity" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none" min="1" max="250" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Komandan Peleton (Danton):</label>
                    <input type="text" name="platoon_leader_name" id="edit_danton" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-end gap-3 shrink-0">
                <button type="button" class="w-full sm:w-auto px-4 py-2.5 border border-slate-300 bg-white rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors" onclick="closeEditClassroomModal()">Batal</button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-bold transition-colors shadow-md shadow-amber-500/20">
                    Perbarui Peleton
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
function openCreateClassroomModal() {
    if (!document.getElementById('createClassroomModal')) return;
    document.getElementById('createClassroomModal').classList.add('active');
    setTimeout(() => {
        const comp = document.getElementById('create_company');
        if (comp) comp.focus();
    }, 100);
}

function closeCreateClassroomModal() {
    if (!document.getElementById('createClassroomModal')) return;
    document.getElementById('createClassroomModal').classList.remove('active');
}

function updateCreateClassName() {
    const comp = document.getElementById('create_company').value.trim();
    const plt = document.getElementById('create_platoon').value.trim();
    const nameField = document.getElementById('create_name');
    const parts = [comp, plt].filter(Boolean);
    if (parts.length > 0) {
        nameField.value = parts.join(' ');
    }
}

function openEditClassroomModal(cls) {
    if (!document.getElementById('editClassroomModal')) return;
    const form = document.getElementById('editClassroomForm');
    form.action = `/classrooms/${cls.id}`;
    document.getElementById('edit_company').value = cls.company || '';
    document.getElementById('edit_platoon').value = cls.platoon || '';
    document.getElementById('edit_name').value = cls.name || '';
    document.getElementById('edit_code').value = cls.code || '';
    document.getElementById('edit_capacity').value = cls.capacity || 35;
    document.getElementById('edit_danton').value = cls.platoon_leader_name || '';
    document.getElementById('editClassroomModal').classList.add('active');
}

function closeEditClassroomModal() {
    if (!document.getElementById('editClassroomModal')) return;
    document.getElementById('editClassroomModal').classList.remove('active');
}

function updateEditClassName() {
    const comp = document.getElementById('edit_company').value.trim();
    const plt = document.getElementById('edit_platoon').value.trim();
    const nameField = document.getElementById('edit_name');
    const parts = [comp, plt].filter(Boolean);
    if (parts.length > 0) {
        nameField.value = parts.join(' ');
    }
}

// Tutup modal jika klik di luar modal
window.addEventListener('click', function(e) {
    const cModal = document.getElementById('createClassroomModal');
    const eModal = document.getElementById('editClassroomModal');
    if (e.target === cModal) closeCreateClassroomModal();
    if (e.target === eModal) closeEditClassroomModal();
});
</script>
@endsection
