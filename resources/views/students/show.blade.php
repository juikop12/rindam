@extends('layouts.app')

@section('title', 'Dossier Serdik: ' . $student->full_name . ' — SIPANDU-WBK')

@section('content')

<!-- BREADCRUMB & BACK BUTTON -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500">
        <a href="{{ route('students.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">Data Serdik</a>
        <span class="ms text-slate-400 text-[16px]">chevron_right</span>
        <a href="{{ route('students.index', ['satdik_id' => $student->satdik_id]) }}" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">{{ $student->satdik->code }}</a>
        <span class="ms text-slate-400 text-[16px]">chevron_right</span>
        <span class="text-slate-900 font-bold">NOSIK: {{ $student->nosik }}</span>
    </div>
    <div class="flex flex-wrap items-center gap-2.5">
        <a href="{{ route('students.index', ['satdik_id' => $student->satdik_id]) }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-sm transition-colors w-full sm:w-auto">
            <span class="ms text-[18px]">arrow_back</span> Kembali ke Daftar
        </a>
        @if(auth()->user()?->isSuperAdmin())
        <a href="{{ route('students.audit-logs') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-sm transition-colors w-full sm:w-auto">
            <span class="ms text-[18px]">policy</span> Lihat Seluruh Audit Trail
        </a>
        @endif
    </div>
</div>

<!-- STUDENT PROFILE HEADER HERO -->
<div class="bg-white border border-slate-200 border-l-4 border-l-blue-600 rounded-xl shadow-sm mb-6 p-5 sm:p-7">
    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
            <!-- AVATAR / PHOTO -->
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-blue-700 to-blue-500 text-white flex items-center justify-center text-2xl sm:text-3xl font-black shadow-lg shadow-blue-500/30 border-2 border-white shrink-0">
                {{ substr($student->full_name, 0, 2) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-blue-50 border border-blue-100 text-blue-700 inline-flex items-center gap-1">
                        <span class="ms text-[14px]">account_balance</span> {{ $student->satdik->code }}
                    </span>
                    <span class="px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        NOSIK: {{ $student->nosik }}
                    </span>
                    @php 
                        $u = $student->unified_status; 
                        $bgClass = 'bg-slate-100 text-slate-700 border-slate-200';
                        if($student->status === 'Aktif') $bgClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        if($student->status === 'Sakit') $bgClass = 'bg-amber-50 text-amber-700 border-amber-200';
                        if(in_array($student->status, ['Lulus', 'Selesai'])) $bgClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                        if(str_contains($student->status, 'DO')) $bgClass = 'bg-rose-50 text-rose-700 border-rose-200';
                    @endphp
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold border {{ $bgClass }} inline-flex items-center gap-1">
                        <span class="ms text-[14px]">{{ $u['icon'] }}</span> {{ $u['label'] }}
                    </span>
                </div>
                <h1 class="m-0 text-xl sm:text-2xl font-bold text-slate-900 mb-1.5 tracking-tight">
                    {{ $student->full_name }}
                </h1>
                <div class="text-[11px] sm:text-xs text-slate-500 flex flex-wrap items-center gap-x-3 gap-y-1">
                    <span>Pangkat: <b class="text-slate-800">{{ $student->student_rank }}</b></span>
                    <span class="text-slate-300">•</span>
                    <span>Program: <b class="text-slate-800">{{ $student->educationProgram->name ?? '-' }}</b></span>
                    <span class="text-slate-300">•</span>
                    <span>Kelas: <b class="text-slate-800">{{ $student->classroom->name ?? 'Belum Ditentukan' }}</b></span>
                </div>
            </div>
        </div>

        <div class="lg:text-right shrink-0">
            @if($student->is_counted)
                <div class="mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-md text-xs font-bold">
                        <span class="ms text-[16px]">how_to_reg</span> Siswa Aktif Terhitung
                    </span>
                </div>
            @else
                <div class="mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 border border-slate-200 text-slate-600 rounded-md text-xs font-bold">
                        <span class="ms text-[16px]">archive</span> Status Arsip (Tidak Terhitung)
                    </span>
                </div>
            @endif
            <div class="mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-700 rounded-md text-xs font-bold shadow-sm">
                    <span class="ms text-[16px]">enhanced_encryption</span> Data Pribadi Terproteksi Sistem
                </span>
            </div>
            <div class="text-[11px] text-slate-400 mt-2 font-medium">
                Terdaftar sejak: {{ $student->created_at->format('d M Y, H:i') }} WIB
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- KARTU 1: PROFIL KEMILITERAN & PENDIDIKAN -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 rounded-t-xl">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="ms text-blue-600 text-[20px]">military_tech</span> Data Kemiliteran & Satdik
            </h3>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">{{ $student->satdik->code }}</span>
        </div>
        <div class="p-0 sm:p-5 flex-1">
            <div class="flex flex-col sm:gap-2">
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-slate-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Satuan Pendidikan</div>
                    <div class="text-sm font-semibold text-slate-900 sm:w-3/5">{{ $student->satdik->name }} ({{ $student->satdik->code }})</div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-slate-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Nomor Siswa (NOSIK)</div>
                    <div class="text-sm font-bold font-mono text-slate-800 sm:w-3/5">{{ $student->nosik }}</div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-slate-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Program Pendidikan</div>
                    <div class="text-sm font-semibold text-slate-900 sm:w-3/5">{{ $student->educationProgram->name ?? '-' }} (TA {{ $student->educationProgram->academic_year ?? '-' }})</div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-slate-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Kompi & Peleton Siswa</div>
                    <div class="text-sm font-semibold text-slate-900 sm:w-3/5">
                        {{ $student->classroom->name ?? ($student->company ? $student->company . ' ' . $student->platoon : 'Belum Ditentukan') }}
                        @if($student->company || $student->platoon)
                            <div class="text-[11px] text-slate-500 font-medium mt-1">
                                Kompi: <b class="text-slate-800">{{ $student->company ?? '-' }}</b> &bull; Peleton: <b class="text-slate-800">{{ $student->platoon ?? '-' }}</b>
                                @if($student->classroom?->platoon_leader_name)
                                    &bull; Danton: <b class="text-slate-800">{{ $student->classroom->platoon_leader_name }}</b>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-slate-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Kodam / Kodim Asal</div>
                    <div class="text-sm font-semibold text-slate-900 sm:w-3/5">{{ $student->origin_military_unit ?? '-' }}</div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-slate-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Tempat, Tanggal Lahir</div>
                    <div class="text-sm font-semibold text-slate-900 sm:w-3/5">
                        {{ $student->birth_place ?? '-' }}, 
                        {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-' }}
                        @if($student->birth_date)
                            <span class="text-xs text-slate-500 font-normal">({{ \Carbon\Carbon::parse($student->birth_date)->age }} tahun)</span>
                        @endif
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-slate-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Jenis Kelamin & Agama</div>
                    <div class="text-sm font-semibold text-slate-900 sm:w-3/5">
                        {{ $student->gender == 'L' ? 'Laki-Laki' : 'Perempuan' }} &bull; {{ $student->religion ?? '-' }}
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-slate-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Golongan Darah</div>
                    <div class="text-sm font-bold text-rose-600 sm:w-3/5">{{ $student->blood_type ?? '-' }}</div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-slate-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-2 sm:mb-0 mt-1">Status Keaktifan</div>
                    <div class="sm:w-3/5">
                        @if(auth()->user()?->canModifyData())
                            <form method="POST" action="{{ route('students.update-status', $student) }}" class="flex flex-col sm:flex-row sm:items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="w-full sm:w-auto h-9 bg-slate-50 border border-slate-200 text-slate-800 text-xs font-bold rounded-lg px-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <optgroup label="STATUS TERHITUNG">
                                        <option value="Aktif" {{ $student->status == 'Aktif' ? 'selected' : '' }}>🟢 Aktif (Siap Latih)</option>
                                        <option value="Sakit" {{ $student->status == 'Sakit' ? 'selected' : '' }}>🟡 Sakit (Dispen Medis)</option>
                                        <option value="Dinas Luar" {{ $student->status == 'Dinas Luar' ? 'selected' : '' }}>🔵 Dinas Luar (Terhitung)</option>
                                    </optgroup>
                                    <optgroup label="STATUS ARSIP">
                                        <option value="Selesai" {{ $student->status == 'Selesai' ? 'selected' : '' }}>📁 Selesai (Tamat Arsip)</option>
                                        <option value="Lulus" {{ $student->status == 'Lulus' ? 'selected' : '' }}>🎓 Lulus (Alumni)</option>
                                        <option value="DO / Dikeluarkan" {{ $student->status == 'DO / Dikeluarkan' ? 'selected' : '' }}>🔴 DO / Dikeluarkan</option>
                                    </optgroup>
                                </select>
                                <button type="submit" class="h-9 bg-white border border-slate-300 text-slate-700 px-3 rounded-lg text-xs font-semibold hover:bg-slate-50 transition-colors w-full sm:w-auto text-center">Simpan</button>
                            </form>
                        @else
                            <div class="inline-flex items-center gap-2">
                                <span class="font-bold text-slate-900 text-sm">{{ $student->status }}</span>
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 text-[10px] font-bold">Hanya Lihat</span>
                            </div>
                        @endif
                        <div class="text-[10px] text-slate-500 mt-2 leading-relaxed">
                            @if($student->is_counted)
                                <b class="text-emerald-600">✓ Terhitung:</b> Prajurit siswa aktif menjalani program pendidikan.
                            @else
                                <b class="text-slate-600">ℹ Masuk Arsip:</b> Prajurit siswa telah selesai pendidikan dan tidak terhitung dalam kuota aktif.
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- KARTU 2: DATA PRIBADI SENSITIF TERPROTEKSI -->
    <div class="bg-amber-50/30 border-2 border-amber-200 rounded-xl shadow-sm flex flex-col relative overflow-hidden">
        <div class="px-5 py-4 border-b border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between bg-amber-100/50 gap-3">
            <div class="flex items-center gap-2">
                <span class="ms text-amber-700 text-[20px]">enhanced_encryption</span>
                <div>
                    <h3 class="text-sm font-bold text-amber-900">Informasi Pribadi Sensitif</h3>
                    <div class="text-[10px] font-semibold text-amber-700/80 mt-0.5">Terenkripsi AES-256 & Tersamar</div>
                </div>
            </div>
            
            <button id="btnTriggerReveal" onclick="openRevealModal()" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition-colors shadow-sm shadow-amber-200 shrink-0 w-full sm:w-auto">
                <span class="ms text-[16px]">visibility</span> Buka Data Sensitif
            </button>
        </div>

        <div class="p-0 sm:p-5 flex-1">
            <!-- NOTIFIKASI STATUS DEKRIPSI -->
            <div id="decryptedNotice" style="display:none;" class="mx-5 my-4 sm:mx-0 sm:mt-0 sm:mb-4 bg-emerald-50 border border-emerald-200 p-3 rounded-lg text-xs text-emerald-800">
                <div class="flex items-start gap-2">
                    <span class="ms text-emerald-600 text-[18px]">lock_open</span>
                    <div>
                        <b class="text-emerald-700">Mode Dekripsi Terbuka:</b> Data telah didekripsi untuk sesi ini dan direkam ke Audit Trail.
                        <div id="decryptedReasonText" class="italic mt-1 text-[11px] text-emerald-600/80"></div>
                    </div>
                </div>
            </div>

            @php $profile = $student->personalProfile; @endphp

            <div class="flex flex-col sm:gap-2">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-amber-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Nomor Induk Kependudukan (NIK)</div>
                    <div class="text-sm sm:w-3/5 font-mono font-medium">
                        <span id="field_nik" class="px-2 py-1 rounded bg-slate-100 text-slate-600 text-xs border border-slate-200">{{ $profile ? $profile->masked_nik : 'N/A' }}</span>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-amber-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Nomor Kartu Keluarga (No KK)</div>
                    <div class="text-sm sm:w-3/5 font-mono font-medium">
                        <span id="field_kk" class="px-2 py-1 rounded bg-slate-100 text-slate-600 text-xs border border-slate-200">{{ $profile && $profile->family_card_number ? substr($profile->family_card_number, 0, 4) . '********' . substr($profile->family_card_number, -4) : 'N/A' }}</span>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-amber-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Nama Ibu Kandung</div>
                    <div class="text-sm sm:w-3/5 font-medium">
                        <span id="field_mother" class="px-2 py-1 rounded bg-slate-100 text-slate-600 text-xs border border-slate-200">{{ $profile ? $profile->masked_mother_name : 'N/A' }}</span>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-amber-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Nama Ayah Kandung</div>
                    <div class="text-sm sm:w-3/5 font-medium">
                        <span id="field_father" class="px-2 py-1 rounded bg-slate-100 text-slate-600 text-xs border border-slate-200">{{ $profile && $profile->father_name ? substr($profile->father_name, 0, 2) . '*****' : 'N/A' }}</span>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-amber-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Kontak Darurat</div>
                    <div class="text-sm sm:w-3/5 font-medium">
                        <span id="field_emergency" class="px-2 py-1 rounded bg-slate-100 text-slate-600 text-xs border border-slate-200">{{ $profile ? $profile->masked_emergency_phone : 'N/A' }}</span>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start justify-between py-3 px-5 sm:px-0 border-b border-amber-100 last:border-0">
                    <div class="text-xs text-slate-500 sm:w-2/5 mb-1 sm:mb-0">Alamat Domisili KTP</div>
                    <div class="text-sm sm:w-3/5 font-medium">
                        <span id="field_address" class="px-2 py-1 rounded bg-slate-100 text-slate-600 text-xs border border-slate-200">{{ $profile && $profile->home_address ? substr($profile->home_address, 0, 15) . '... [Disamarkan]' : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Notice Rekam Medis -->
            <div class="mt-5 mx-5 sm:mx-0 mb-5 sm:mb-0 bg-emerald-50/50 border border-emerald-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="ms text-emerald-600 text-[24px]">medical_services</span>
                    <div>
                        <div class="text-xs font-bold text-emerald-800">Data Kesehatan Dikelola Terpisah</div>
                        <div class="text-[11px] text-emerald-600/80 mt-0.5 leading-relaxed">Rekam medis fisik dan riwayat alergi dipisahkan pada sistem Rekam Medis.</div>
                    </div>
                </div>
                <a href="{{ route('health.show', $student) }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-bold transition-colors shadow-sm shrink-0">
                    <span class="ms text-[16px]">arrow_forward</span> Buka Rekam Medis
                </a>
            </div>

        </div>
    </div>
</div>

<!-- AUDIT TRAIL CARD -->
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
        <div>
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="ms text-slate-500 text-[20px]">history_edu</span> Riwayat Akses Data Sensitif
            </h3>
            <div class="text-[11px] text-slate-500 mt-0.5">Log permanen immutable (*append-only*) mencatat setiap pembukaan data.</div>
        </div>
        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-200 text-slate-700 self-start sm:self-auto shrink-0">{{ $student->accessLogs->count() }} Kali Diakses</span>
    </div>
    <div class="p-0">
        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="py-3 px-5 text-xs font-bold text-slate-600 w-[60px]">No</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600">Waktu Akses (WIB)</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600">Personel Pengakses</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600">Otoritas</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600">Alasan Akses Terbuka</th>
                        <th class="py-3 px-5 text-xs font-bold text-slate-600">IP Address</th>
                    </tr>
                </thead>
                <tbody id="auditTrailTableBody">
                    @forelse($student->accessLogs->sortByDesc('accessed_at') as $index => $log)
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors last:border-0">
                            <td class="py-3 px-5 text-xs text-slate-500">{{ $loop->iteration }}</td>
                            <td class="py-3 px-5 text-xs font-mono font-semibold text-slate-700">
                                {{ $log->accessed_at ? \Carbon\Carbon::parse($log->accessed_at)->format('d/m/Y H:i:s') : '-' }}
                            </td>
                            <td class="py-3 px-5">
                                <div class="text-xs font-bold text-slate-900">{{ $log->accessedByUser->name ?? 'User #' . $log->user_id }}</div>
                                <div class="text-[11px] text-slate-500">{{ $log->accessedByUser->email ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">{{ $log->accessedByUser->role_code ?? 'operator' }}</span>
                            </td>
                            <td class="py-3 px-5 text-xs font-semibold text-slate-800">
                                {{ $log->access_reason }}
                            </td>
                            <td class="py-3 px-5 text-xs font-mono text-slate-400">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyLogNotice">
                            <td colspan="6" class="py-12 px-5 text-center text-slate-400">
                                <span class="ms text-[36px] block mb-2 opacity-50">verified_user</span>
                                <div class="text-sm font-medium">Belum ada riwayat pembukaan data.</div>
                                <div class="text-xs mt-1">Data sensitif belum pernah diakses dan tetap aman.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block sm:hidden divide-y divide-slate-100">
            @forelse($student->accessLogs->sortByDesc('accessed_at') as $index => $log)
                <div class="p-4 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $log->accessed_at ? \Carbon\Carbon::parse($log->accessed_at)->format('d/m/y H:i') : '-' }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">{{ $log->accessedByUser->role_code ?? 'operator' }}</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-900 mb-0.5">{{ $log->accessedByUser->name ?? 'User #' . $log->user_id }}</div>
                        <div class="text-[11px] text-slate-500">{{ $log->accessedByUser->email ?? '-' }} (IP: {{ $log->ip_address ?? '127.0.0.1' }})</div>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-100 text-xs font-semibold text-slate-700">
                        "{{ $log->access_reason }}"
                    </div>
                </div>
            @empty
                <div class="py-10 px-5 text-center text-slate-400">
                    <span class="ms text-[32px] block mb-2 opacity-50">verified_user</span>
                    <div class="text-xs font-medium">Belum ada riwayat pembukaan data.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- MODAL AUDIT TRAIL FORM -->
<div id="revealModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white w-full max-w-md mx-4 rounded-2xl shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300" id="revealModalCard">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2.5">
                <span class="ms text-amber-500 text-[22px]">lock_open</span>
                <h4 class="m-0 font-bold text-slate-900 text-[15px]">Buka Data Pribadi Sensitif</h4>
            </div>
            <button onclick="closeRevealModal()" class="text-slate-400 hover:text-slate-700 transition-colors bg-transparent border-none cursor-pointer">
                <span class="ms text-[20px]">close</span>
            </button>
        </div>
        <div class="p-5">
            <div class="mb-4 bg-amber-50 border border-amber-200 rounded-lg p-3 flex gap-3 text-amber-800 text-xs leading-relaxed">
                <span class="ms text-[20px] text-amber-600 shrink-0">security</span>
                <div>
                    <b class="block mb-0.5">PEMBERITAHUAN KEAMANAN:</b>
                    Pembukaan informasi identitas pribadi (NIK, No KK, dll) akan dicatat permanen dalam Audit Trail beserta identitas akun, alamat IP, dan waktu akses Anda.
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Siswa yang akan dibuka:</label>
                <div class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                    <b class="text-slate-900">{{ $student->full_name }}</b> <span class="text-slate-500">(NOSIK: {{ $student->nosik }})</span>
                </div>
            </div>

            <div class="mb-2">
                <label for="accessReason" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alasan Pembukaan Data Pribadi <span class="text-rose-500">*</span>
                </label>
                <select id="quickReasonSelect" class="w-full h-9 mb-2 bg-white border border-slate-300 text-slate-700 text-xs rounded-lg px-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" onchange="applyQuickReason(this.value)">
                    <option value="">-- Pilih Format Alasan Resmi --</option>
                    <option value="Verifikasi keabsahan NIK dan data kependudukan Disdukcapil">Verifikasi Keabsahan NIK & Data Disdukcapil</option>
                    <option value="Konfirmasi kontak darurat orang tua / wali serdik">Konfirmasi Kontak Darurat Orang Tua / Wali Serdik</option>
                    <option value="Pemeriksaan berkas administrasi personel & ijazah serdik">Pemeriksaan Berkas Administrasi Personel & Ijazah</option>
                    <option value="Pemeriksaan integritas serdik oleh Komite Pengawas Pendidikan">Pemeriksaan Integritas Serdik Komite Pendidikan</option>
                </select>
                <textarea id="accessReason" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-xs text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:outline-none" rows="3" placeholder="Tuliskan alasan operasional resmi pembukaan data pribadi..."></textarea>
                <div class="text-[10px] text-slate-500 mt-1">Minimal 5 karakter. Wajib mencerminkan kebutuhan dinas.</div>
            </div>

            <div id="modalAlertError" style="display:none;" class="text-xs font-semibold text-rose-600 mt-2 bg-rose-50 border border-rose-100 p-2 rounded-lg"></div>
        </div>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-2 bg-slate-50/50">
            <button type="button" onclick="closeRevealModal()" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-50 transition-colors">Batal</button>
            <button type="button" id="btnSubmitReveal" onclick="executeReveal()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition-colors shadow-sm shadow-amber-200">
                <span class="ms text-[16px]">lock_open</span> Dekripsi & Rekam Log
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openRevealModal() {
        const modal = document.getElementById('revealModal');
        const card = document.getElementById('revealModalCard');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        card.classList.remove('scale-95');
        document.getElementById('accessReason').focus();
    }

    function closeRevealModal() {
        const modal = document.getElementById('revealModal');
        const card = document.getElementById('revealModalCard');
        modal.classList.add('opacity-0', 'pointer-events-none');
        card.classList.add('scale-95');
        document.getElementById('modalAlertError').style.display = 'none';
    }

    function applyQuickReason(val) {
        if(val) {
            document.getElementById('accessReason').value = val;
        }
    }

    function executeReveal() {
        const reason = document.getElementById('accessReason').value.trim();
        const errDiv = document.getElementById('modalAlertError');
        const btn = document.getElementById('btnSubmitReveal');

        if(reason.length < 5) {
            errDiv.textContent = 'Alasan pembukaan data pribadi wajib diisi minimal 5 karakter!';
            errDiv.style.display = 'block';
            return;
        }

        errDiv.style.display = 'none';
        btn.disabled = true;
        btn.innerHTML = '<span class="ms text-[16px]">hourglass_empty</span> Mendekripsi...';

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch("{{ route('students.reveal-sensitive', $student->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ access_reason: reason })
        })
        .then(response => {
            if(!response.ok) {
                return response.json().then(json => { throw new Error(json.message || 'Gagal mendekripsi data'); });
            }
            return response.json();
        })
        .then(result => {
            closeRevealModal();
            const data = result.data;

            // Update UI fields with plain decrypted text
            setDecryptedField('field_nik', data.nik);
            setDecryptedField('field_kk', data.family_card_number || 'Tidak Ada');
            setDecryptedField('field_mother', data.mother_name);
            setDecryptedField('field_father', data.father_name || 'Tidak Ada');
            setDecryptedField('field_emergency', (data.emergency_contact_name ? data.emergency_contact_name + ' — ' : '') + data.emergency_contact_phone);
            setDecryptedField('field_address', data.home_address || 'Tidak Ada');

            // Show decryption notice banner
            document.getElementById('decryptedNotice').style.display = 'block';
            document.getElementById('decryptedReasonText').textContent = 'Alasan: "' + reason + '" (Dicatat ke Audit Trail)';
            
            // Disable button trigger
            const triggerBtn = document.getElementById('btnTriggerReveal');
            triggerBtn.disabled = true;
            triggerBtn.innerHTML = '<span class="ms text-[16px]">lock_open</span> Terbuka (Audit Logged)';
            triggerBtn.className = 'inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-500 rounded-lg text-xs font-bold border border-slate-200 cursor-not-allowed w-full sm:w-auto';

            // Prepend new row in audit log table dynamically
            const tableBody = document.getElementById('auditTrailTableBody');
            const emptyNotice = document.getElementById('emptyLogNotice');
            if (emptyNotice) { emptyNotice.remove(); }

            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID') + ' ' + now.toLocaleTimeString('id-ID');

            const newRow = document.createElement('tr');
            newRow.className = 'border-b border-slate-100 bg-amber-50/50';
            newRow.innerHTML = `
                <td class="py-3 px-5 text-xs text-amber-700 font-bold">BARU</td>
                <td class="py-3 px-5 text-xs font-mono font-bold text-slate-800">${dateStr}</td>
                <td class="py-3 px-5">
                    <div class="text-xs font-bold text-slate-900">{{ auth()->user()->name ?? 'Operator Rindam' }}</div>
                    <div class="text-[11px] text-slate-500">{{ auth()->user()->email ?? 'op@rindam.mil.id' }}</div>
                </td>
                <td class="py-3 px-5"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">{{ auth()->user()->role_code ?? 'pimpinan' }}</span></td>
                <td class="py-3 px-5 text-xs font-bold text-slate-800">${reason}</td>
                <td class="py-3 px-5 text-xs font-mono text-slate-400">127.0.0.1 (Current)</td>
            `;
            tableBody.insertBefore(newRow, tableBody.firstChild);
            
            // Note: Didn't inject into mobile card view dynamically for simplicity, but it updates on refresh
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<span class="ms text-[16px]">lock_open</span> Dekripsi & Rekam Log';
            errDiv.textContent = err.message || 'Terjadi kesalahan saat otentikasi dekripsi.';
            errDiv.style.display = 'block';
        });
    }

    function setDecryptedField(elementId, value) {
        const el = document.getElementById(elementId);
        if(el) {
            el.textContent = value;
            el.className = 'px-2 py-1 rounded bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200';
        }
    }
</script>
@endsection
