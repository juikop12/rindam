@extends('layouts.app')

@section('title', 'Pengosongan Data Sistem — SIPANDU')

@section('content')

<!-- BREADCRUMB & HEADER -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">Dashboard</a>
        <span class="ms text-slate-400 text-[16px]">chevron_right</span>
        <a href="{{ route('settings.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">Pengaturan Sistem</a>
        <span class="ms text-slate-400 text-[16px]">chevron_right</span>
        <span class="text-slate-900 font-bold">Pengosongan Data</span>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">
            <span class="ms text-[16px]">arrow_back</span> Kembali ke Pengaturan
        </a>
    </div>
</div>

<!-- DANGER ZONE BANNER HERO -->
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-rose-950 text-white p-6 sm:p-8 shadow-xl border border-rose-900/40 mb-8">
    <div class="absolute -right-8 -top-8 w-64 h-64 bg-rose-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="relative z-10">
        <div class="flex flex-wrap items-center gap-2.5 mb-3">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                <span class="ms text-[14px]">security</span> FITUR KHUSUS SUPER ADMIN
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                <span class="ms text-[14px]">warning</span> ZONA PEMELIHARAAN SISTEM
            </span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2 flex items-center gap-2.5">
            <span class="ms text-rose-500 text-[32px]">delete_sweep</span>
            Pengosongan & Reset Data Sistem
        </h1>
        <p class="text-slate-300 text-xs sm:text-sm max-w-3xl leading-relaxed">
            Fasilitas pembersihan dan pengosongan data operasional (Data Siswa, Profil Sensitif AES-256, Rekam Medis, Rombel, & Log Audit) secara langsung oleh sistem. Fitur ini dirancang khusus untuk peralihan tahun ajaran baru atau pembersihan data simulasi tanpa perlu mengakses database secara manual.
        </p>
    </div>
</div>

<!-- ALERT SUCCESS / ERROR -->
@if(session('success'))
<div class="p-4 mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-sm flex items-start gap-3">
    <span class="ms text-emerald-600 text-[24px] shrink-0">check_circle</span>
    <div class="text-xs sm:text-sm">
        <b class="font-bold block mb-1">Operasi Pengosongan Data Berhasil!</b>
        <p class="text-emerald-800 leading-relaxed">{{ session('success') }}</p>
    </div>
</div>
@endif

@if($errors->any())
<div class="p-4 mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 shadow-sm flex items-start gap-3">
    <span class="ms text-rose-600 text-[24px] shrink-0">report</span>
    <div class="text-xs sm:text-sm">
        <b class="font-bold block mb-1">Operasi Ditolak / Terjadi Kesalahan:</b>
        <ul class="list-disc list-inside space-y-1 text-rose-800">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<!-- LIVE SYSTEM DATA STATS -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="ms text-blue-600 text-[20px]">analytics</span>
                Statistik Data Operasional Saat Ini
            </h2>
            <p class="text-xs text-slate-500">Jumlah data yang tersimpan aktif dalam basis data sistem SIPANDU Rindam III/Siliwangi</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-slate-100 text-slate-700">Live Real-time</span>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- STAT 1: TOTAL SISWA -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Siswa</span>
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center ms text-[16px]">school</span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['total_students'], 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-500 mt-1 font-medium">{{ $stats['active_students'] }} Siswa Aktif</div>
        </div>

        <!-- STAT 2: REKAM MEDIS -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rekam Medis</span>
                <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center ms text-[16px]">medical_services</span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['health_records'], 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-500 mt-1 font-medium">Fisik & Stakes</div>
        </div>

        <!-- STAT 3: PROFIL AES-256 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Profil NIK/AES</span>
                <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center ms text-[16px]">lock</span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['personal_profiles'], 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-500 mt-1 font-medium">UU PDP Terenkripsi</div>
        </div>

        <!-- STAT 4: PROGRAM PENDIDIKAN -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Program Diklat</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center ms text-[16px]">menu_book</span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['education_programs'], 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-500 mt-1 font-medium">Kurikulum Satdik</div>
        </div>

        <!-- STAT 5: KOMPI & PELETON -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kompi / Rombel</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center ms text-[16px]">groups</span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['classrooms'], 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-500 mt-1 font-medium">Peleton Terdaftar</div>
        </div>

        <!-- STAT 6: AUDIT TRAIL LOG -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jejak Audit</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center ms text-[16px]">policy</span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['audit_logs'], 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-500 mt-1 font-medium">Log Akses Sensitif</div>
        </div>
    </div>
</div>

<!-- BREAKDOWN PER SATDIK TABLE -->
<div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm mb-8">
    <div class="flex items-center justify-between mb-3">
        <b class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
            <span class="ms text-slate-500 text-[18px]">account_balance</span>
            Rincian Data Siswa per Satuan Pendidikan (5 Satdik)
        </b>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-slate-600">
                    <th class="py-2.5 px-3 font-semibold">Kode Satdik</th>
                    <th class="py-2.5 px-3 font-semibold">Nama Satuan Pendidikan</th>
                    <th class="py-2.5 px-3 font-semibold text-center">Program Diklat</th>
                    <th class="py-2.5 px-3 font-semibold text-center">Jumlah Siswa</th>
                    <th class="py-2.5 px-3 font-semibold text-right">Status Data</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($stats['satdiks'] as $s)
                <tr class="hover:bg-slate-50/60">
                    <td class="py-2.5 px-3 font-bold font-mono text-slate-800">{{ $s['code'] }}</td>
                    <td class="py-2.5 px-3 font-medium text-slate-700">{{ $s['name'] }}</td>
                    <td class="py-2.5 px-3 text-center text-slate-600">{{ $s['programs_count'] }} Diklat</td>
                    <td class="py-2.5 px-3 text-center font-bold font-mono text-slate-900">{{ number_format($s['students_count'], 0, ',', '.') }} Siswa</td>
                    <td class="py-2.5 px-3 text-right">
                        @if($s['students_count'] > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Ada Data</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Kosong (0)</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- FORMULIR PILIHAN PENGOSONGAN DATA -->
<form id="wipeDataForm" action="{{ route('settings.cleanup.process') }}" method="POST">
    @csrf

    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-7 shadow-sm mb-8">
        <div class="border-b border-slate-200 pb-4 mb-6">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="ms text-rose-600 text-[22px]">tune</span>
                Pilih Cakupan & Opsi Pengosongan Data
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Tentukan jenis data mana yang ingin dikosongkan dari sistem.</p>
        </div>

        <div class="space-y-3.5 mb-6">
            <!-- OPTION 1: STUDENTS ONLY -->
            <label class="relative flex items-start p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-slate-50/80 option-card" id="card_students_only">
                <input type="radio" name="wipe_target" value="students_only" class="mt-1 h-4 w-4 text-rose-600 border-slate-300 focus:ring-rose-500" checked onchange="handleTargetChange(this.value)">
                <div class="ml-3.5 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                            <span class="ms text-rose-600 text-[18px]">group_remove</span>
                            Pengosongan Data Seluruh Siswa & Rekam Medis (Direkomendasikan untuk Tahun Ajaran Baru)
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded bg-emerald-100 text-emerald-800">Paling Sering Digunakan</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Menghapus seluruh prajurit siswa (aktif, sakit, DO, lulus), profil data pribadi terenkripsi AES-256, rekam medis harian/stakes, dan jejak log audit akses. <b class="text-slate-700">Master Satdik, Program Pendidikan, dan Kompi/Peleton tetap utuh</b> sehingga siap menerima batch siswa baru.
                    </p>
                </div>
            </label>

            <!-- OPTION 2: STUDENTS AND PROGRAMS -->
            <label class="relative flex items-start p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-slate-50/80 option-card" id="card_students_and_programs">
                <input type="radio" name="wipe_target" value="students_and_programs" class="mt-1 h-4 w-4 text-rose-600 border-slate-300 focus:ring-rose-500" onchange="handleTargetChange(this.value)">
                <div class="ml-3.5 flex-1">
                    <span class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                        <span class="ms text-amber-600 text-[18px]">folder_delete</span>
                        Pengosongan Data Siswa + Rombel & Program Pendidikan Diklat
                    </span>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Menghapus data siswa beserta seluruh data turunan kurikulumnya (Kompi/Peleton dan Program Pendidikan Diklat). <b class="text-slate-700">Master 5 Satdik, Akun Pengguna, dan Konfigurasi Pejabat Sistem tetap aman</b>.
                    </p>
                </div>
            </label>

            <!-- OPTION 3: SPECIFIC SATDIK -->
            <label class="relative flex items-start p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-slate-50/80 option-card" id="card_specific_satdik">
                <input type="radio" name="wipe_target" value="specific_satdik" class="mt-1 h-4 w-4 text-rose-600 border-slate-300 focus:ring-rose-500" onchange="handleTargetChange(this.value)">
                <div class="ml-3.5 flex-1">
                    <span class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                        <span class="ms text-blue-600 text-[18px]">domain</span>
                        Pengosongan Khusus 1 Satuan Pendidikan (Satdik Tertentu Saja)
                    </span>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Hanya mengosongkan data siswa pada Satdik yang Anda pilih (misal hanya Secata atau Secaba saja), tanpa mempengaruhi data siswa di Satdik lainnya.
                    </p>

                    <!-- CASCADING SELECTOR UNTUK SATDIK -->
                    <div id="satdikSelectorSection" class="mt-3 p-3.5 bg-slate-50 border border-slate-200 rounded-xl" style="display:none;">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Pilih Satdik Sasaran Pengosongan: <span class="text-rose-500">*</span>
                        </label>
                        <select name="satdik_id" id="satdik_id" class="w-full sm:w-80 h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-rose-500 focus:outline-none mb-2">
                            <option value="">-- Pilih Satdik Sasaran --</option>
                            @foreach($satdiks as $satdik)
                                <option value="{{ $satdik->id }}">{{ $satdik->code }} — {{ $satdik->name }} ({{ number_format($satdik->students()->withTrashed()->count(), 0, ',', '.') }} Siswa)</option>
                            @endforeach
                        </select>
                        <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer mt-1">
                            <input type="checkbox" name="wipe_satdik_programs" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            <span>Hapus juga seluruh Program Diklat dan Kompi/Peleton di Satdik ini</span>
                        </label>
                    </div>
                </div>
            </label>

            <!-- OPTION 4: AUDIT LOGS ONLY -->
            <label class="relative flex items-start p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-slate-50/80 option-card" id="card_audit_logs_only">
                <input type="radio" name="wipe_target" value="audit_logs_only" class="mt-1 h-4 w-4 text-rose-600 border-slate-300 focus:ring-rose-500" onchange="handleTargetChange(this.value)">
                <div class="ml-3.5 flex-1">
                    <span class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                        <span class="ms text-purple-600 text-[18px]">history</span>
                        Hanya Bersihkan Jejak Audit Trail (Log Akses Data Pribadi)
                    </span>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Hanya mengosongkan riwayat log akses data sensitif. Seluruh data siswa, rekam medis, dan program diklat tetap utuh tanpa perubahan.
                    </p>
                </div>
            </label>

            <!-- OPTION 5: FULL RESET -->
            <label class="relative flex items-start p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-slate-50/80 option-card" id="card_full_reset">
                <input type="radio" name="wipe_target" value="full_reset" class="mt-1 h-4 w-4 text-rose-600 border-slate-300 focus:ring-rose-500" onchange="handleTargetChange(this.value)">
                <div class="ml-3.5 flex-1">
                    <span class="text-xs sm:text-sm font-bold text-rose-700 flex items-center gap-1.5">
                        <span class="ms text-rose-600 text-[18px]">restart_alt</span>
                        Reset Total Seluruh Sistem (Kembali ke Kondisi Bersih / Standar Instalasi)
                    </span>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Mengosongkan seluruh data siswa, rekam medis, kurikulum, rombel, dan menghapus akun pengguna tambahan kecuali akun resmi sistem. Mengosongkan seluruh cache sistem kembali ke nol.
                    </p>
                </div>
            </label>
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-slate-100">
            <button type="button" onclick="openConfirmationModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-rose-200 transition-all transform active:scale-95">
                <span class="ms text-[20px]">delete_forever</span>
                Lanjutkan ke Konfirmasi Pengosongan Data
            </button>
        </div>
    </div>

    <!-- MODAL KONFIRMASI KEAMANAN TINGKAT TINGGI -->
    <div id="confirmationModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm opacity-0 invisible transition-all duration-300">
        <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl border border-rose-300 transform scale-95 transition-transform duration-300" id="confirmationModalBox">
            <!-- MODAL HEADER -->
            <div class="bg-rose-600 text-white p-5 flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <span class="ms text-[24px]">warning</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm sm:text-base leading-tight">Konfirmasi Pengosongan Data</h4>
                        <span class="text-[11px] text-rose-100 font-medium">Tindakan ini permanen dan tidak dapat dibatalkan</span>
                    </div>
                </div>
                <button type="button" onclick="closeConfirmationModal()" class="text-white/80 hover:text-white text-2xl leading-none">&times;</button>
            </div>

            <!-- MODAL BODY -->
            <div class="p-6">
                <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-900 mb-5 leading-relaxed">
                    <b>PERINGATAN MILITER:</b> Anda sedang melakukan tindakan pembersihan data operasional sistem. Seluruh data yang dipilih akan dihapus secara permanen dari basis data dan cache sistem akan di-flush kembali ke nol.
                </div>

                <div class="space-y-4">
                    <!-- VERIFIKASI 1: PASSWORD SUPERADMIN -->
                    <div>
                        <label for="admin_password" class="block text-xs font-bold text-slate-700 mb-1.5">
                            1. Masukkan Password Akun Super Administrator Anda: <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password" id="admin_password" required placeholder="Ketik password akun Anda saat ini..." class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>

                    <!-- VERIFIKASI 2: FRASA KONFIRMASI -->
                    <div>
                        <label for="admin_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">
                            2. Ketik frasa konfirmasi: <span class="font-mono text-rose-700 bg-rose-100 px-1.5 py-0.5 rounded font-black">KOSONGKAN DATA</span> <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="confirmation" id="admin_confirmation" required placeholder="KOSONGKAN DATA" autocomplete="off" class="w-full h-10 px-3 font-mono uppercase bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <span class="text-[10px] text-slate-400 mt-1 block">Wajib diketik sama persis dengan huruf kapital.</span>
                    </div>
                </div>
            </div>

            <!-- MODAL FOOTER -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeConfirmationModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-lg hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSubmitWipe" class="inline-flex items-center gap-1.5 px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold shadow-md shadow-rose-300 transition-all">
                    <span class="ms text-[18px]">verified</span>
                    Saya Paham & Eksekusi Sekarang
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    function updateCardHighlights() {
        document.querySelectorAll('.option-card').forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            if (radio && radio.checked) {
                card.classList.add('border-rose-500', 'bg-rose-50/30', 'ring-1', 'ring-rose-500');
                card.classList.remove('border-slate-200');
            } else {
                card.classList.remove('border-rose-500', 'bg-rose-50/30', 'ring-1', 'ring-rose-500');
                card.classList.add('border-slate-200');
            }
        });
    }

    function handleTargetChange(val) {
        updateCardHighlights();
        const satdikSec = document.getElementById('satdikSelectorSection');
        if (val === 'specific_satdik') {
            satdikSec.style.display = 'block';
            document.getElementById('satdik_id').required = true;
        } else {
            satdikSec.style.display = 'none';
            document.getElementById('satdik_id').required = false;
        }
    }

    function openConfirmationModal() {
        const selectedMode = document.querySelector('input[name="wipe_target"]:checked').value;
        if (selectedMode === 'specific_satdik') {
            const satdikVal = document.getElementById('satdik_id').value;
            if (!satdikVal) {
                alert('Peringatan: Harap pilih Satuan Pendidikan sasaran terlebih dahulu!');
                document.getElementById('satdik_id').focus();
                return;
            }
        }

        const modal = document.getElementById('confirmationModal');
        const box = document.getElementById('confirmationModalBox');
        modal.classList.remove('opacity-0', 'invisible');
        modal.classList.add('opacity-100', 'visible');
        box.classList.remove('scale-95');
        box.classList.add('scale-100');
        document.getElementById('admin_password').focus();
    }

    function closeConfirmationModal() {
        const modal = document.getElementById('confirmationModal');
        const box = document.getElementById('confirmationModalBox');
        modal.classList.add('opacity-0', 'invisible');
        modal.classList.remove('opacity-100', 'visible');
        box.classList.add('scale-95');
        box.classList.remove('scale-100');
    }

    // Initialize highlights on page load
    document.addEventListener('DOMContentLoaded', () => {
        updateCardHighlights();
    });
</script>

@endsection
