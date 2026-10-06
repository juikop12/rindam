@extends('layouts.app')

@section('title', 'Penginputan Data Siswa Format Excel — SIPANDU-WBK')

@section('content')

<!-- BREADCRUMB -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <a href="{{ route('students.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">Data Serdik</a>
        <span class="ms text-slate-400 text-[16px]">chevron_right</span>
        <span class="text-slate-900 font-bold">Penginputan Format Excel</span>
    </div>
    <a href="{{ route('students.index') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-sm transition-colors w-full sm:w-auto">
        <span class="ms text-[18px]">arrow_back</span> Kembali ke Buku Induk
    </a>
</div>

<!-- HEADER BANNER PENGINPUTAN EXCEL -->
<div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 border-l-4 border-l-amber-500 rounded-xl p-6 sm:p-8 shadow-sm mb-6 text-white relative overflow-hidden">
    <div class="relative z-10">
        <div class="flex flex-wrap items-center gap-2.5 mb-4">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-md text-[10px] font-bold tracking-wide">
                <span class="ms text-[14px]">upload_file</span> INPUT FORMAT EXCEL
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-md text-[10px] font-bold tracking-wide">
                <span class="ms text-[14px]">school</span> 5 SATUAN PENDIDIKAN
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/10 text-white border border-white/20 rounded-md text-[10px] font-bold tracking-wide">
                <span class="ms text-[14px]">verified_user</span> ZONA INTEGRITAS WBK
            </span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold mb-3 tracking-tight">
            Penginputan Data Serdik Menggunakan Format Excel
        </h1>
        <p class="text-sm text-slate-300 leading-relaxed max-w-3xl">
            Fasilitas penginputan massal (batch upload) data prajurit siswa lintas Satdik menggunakan format berkas Excel (.xlsx / .xls) atau CSV (.csv). Sistem otomatis memetakan satuan, kompi/peleton, serta indikator rekam medis & status kesehatan awal.
        </p>
    </div>
</div>

@if ($errors->any())
    <div class="mb-6 flex items-start gap-3 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm shadow-sm">
        <span class="ms text-[24px] text-rose-500 shrink-0">error</span>
        <div>
            <b class="font-bold text-rose-900">Terjadi kendala saat membaca berkas Excel:</b>
            <ul class="list-disc list-inside mt-2 space-y-1 text-xs text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">

    <!-- KARTU 1: UNDUH TEMPLATE EXCEL -->
    <div class="lg:col-span-5 bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="ms text-amber-500 text-[22px]">download</span>
                    1. Unduh Format Template
                </h3>
                <div class="text-[11px] text-slate-500 mt-1">Unduh berkas acuan pengisian data serdik resmi</div>
            </div>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">Format Standar</span>
        </div>
        <div class="p-5 flex-1 flex flex-col justify-between">
            <div>
                <p class="text-xs text-slate-700 leading-relaxed mb-4">
                    Gunakan template resmi ini untuk menginput puluhan atau ratusan siswa sekaligus. Di dalam template telah disediakan:
                </p>
                <ul class="text-xs text-slate-600 leading-relaxed mb-6 list-disc pl-4 space-y-1.5">
                    <li>Header kolom terstandarisasi untuk 5 Satdik Rindam III/Slw.</li>
                    <li>Baris contoh pengisian untuk masing-masing Satdik.</li>
                    <li>Kolom indikator kesehatan fisik & medis (Stakes).</li>
                    <li>Mendukung formula otomatis dan format cell Excel.</li>
                </ul>

                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-2">Pilih Satdik Acuan:</label>
                    @if(auth()->check() && !auth()->user()->isPimpinan() && auth()->user()->satdik_id)
                        <input type="hidden" id="templateSatdikSelect" value="{{ auth()->user()->satdik_id }}">
                        <div class="flex items-center gap-2 text-sm font-bold text-slate-900">
                            <span class="ms text-emerald-600 text-[20px]">lock</span>
                            {{ auth()->user()->satdik?->code }} — {{ auth()->user()->satdik?->name }}
                        </div>
                    @else
                        <select id="templateSatdikSelect" class="w-full bg-white border border-slate-300 text-slate-700 text-xs rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium" onchange="updateTemplateDownloadUrls(this.value)">
                            <option value="">Semua Satdik (Rindam III/Siliwangi)</option>
                            @foreach($satdiks as $s)
                                <option value="{{ $s->id }}" {{ $selectedSatdikId == $s->id ? 'selected' : '' }}>
                                    {{ $s->code }} — {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-3 pt-5 border-t border-slate-100">
                <a id="btnDownloadXlsx" href="{{ route('students.import.template', ['format' => 'xlsx', 'satdik_id' => $selectedSatdikId]) }}" class="inline-flex items-center justify-center gap-2 w-full py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-bold shadow-sm transition-colors">
                    <span class="ms text-[20px]">table_view</span> Unduh Format Excel (.xlsx)
                </a>
                <a id="btnDownloadCsv" href="{{ route('students.import.template', ['format' => 'csv', 'satdik_id' => $selectedSatdikId]) }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-bold shadow-sm transition-colors">
                    <span class="ms text-[18px]">description</span> Unduh Format CSV (.csv)
                </a>
            </div>
        </div>
    </div>

    <!-- KARTU 2: FORM UNGGAH BERKAS EXCEL -->
    <div class="lg:col-span-7 bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="ms text-emerald-600 text-[22px]">cloud_upload</span>
                    2. Unggah & Proses Berkas
                </h3>
                <div class="text-[11px] text-slate-500 mt-1">Kirim berkas yang telah diisi untuk diproses ke database</div>
            </div>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Unggah Berkas</span>
        </div>
        <div class="p-5 flex-1">
            <form action="{{ route('students.import.process') }}" method="POST" enctype="multipart/form-data" class="flex flex-col h-full">
                @csrf

                <!-- TARGET SATDIK -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Satdik Tujuan Penginputan:
                    </label>
                    @if(auth()->check() && !auth()->user()->isPimpinan() && auth()->user()->satdik_id)
                        <input type="hidden" name="satdik_id" value="{{ auth()->user()->satdik_id }}">
                        <div class="bg-slate-100 border border-slate-300 rounded-lg px-4 py-3 flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-900">{{ auth()->user()->satdik?->code }} — {{ auth()->user()->satdik?->name }}</span>
                            <span class="inline-flex items-center px-2 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded">🔒 Terkunci Wewenang</span>
                        </div>
                        <div class="text-[11px] text-emerald-600 font-medium mt-2">
                            Seluruh baris serdik dari berkas Excel otomatis terikat pada <b>{{ auth()->user()->satdik?->name }}</b>.
                        </div>
                    @else
                        <select name="satdik_id" id="satdik_id" class="w-full bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Otomatis (Sesuai Kolom KODE_SATDIK di Berkas)</option>
                            @foreach($satdiks as $s)
                                <option value="{{ $s->id }}" {{ $selectedSatdikId == $s->id ? 'selected' : '' }}>
                                    {{ $s->code }} — {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-[11px] text-slate-500 mt-2">Jika dipilih spesifik, seluruh baris yang tidak mengisi kode satdik akan otomatis masuk ke Satdik ini.</div>
                    @endif
                </div>

                <!-- DRAG & DROP FILE ZONE -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Pilih Berkas Excel / CSV <span class="text-rose-500">*</span>
                    </label>
                    <div id="dropZone" class="border-2 border-dashed border-amber-400 bg-amber-50/50 rounded-xl p-8 text-center cursor-pointer hover:bg-amber-100/50 transition-colors" onclick="document.getElementById('excel_file').click()">
                        <span class="ms text-[48px] text-amber-500 block mb-2">upload_file</span>
                        <div id="dropFileName" class="text-sm font-bold text-slate-900 mb-1">
                            Klik di sini atau seret berkas Excel ke area ini
                        </div>
                        <div class="text-xs text-slate-500">
                            Mendukung berkas: <b>.xlsx</b>, <b>.xls</b>, atau <b>.csv</b> (Maks 10 MB)
                        </div>
                    </div>
                    <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls,.csv" required class="hidden" onchange="handleFileSelected(this)">
                </div>

                <!-- UPDATE EXISTING CHECKBOX -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-start gap-3 mb-6">
                    <input type="checkbox" name="update_existing" id="update_existing" value="1" checked class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer">
                    <label for="update_existing" class="text-xs text-slate-700 cursor-pointer leading-relaxed">
                        <b class="text-slate-900 text-[13px] block mb-0.5">Perbarui otomatis jika NIK KTP sudah terdaftar</b>
                        Jika dicentang, serdik yang memiliki NIK KTP (16 digit) persis sama akan diperbarui dengan baris data dari Excel. Jika tidak, data serdik dengan NIK duplikat akan dilewati.
                    </label>
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit" class="mt-auto w-full inline-flex items-center justify-center gap-2 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-md shadow-blue-500/20 transition-colors">
                    <span class="ms text-[20px]">save</span> Proses & Simpan Data Serdik ke Database
                </button>
            </form>
        </div>
    </div>

</div>

<!-- PANDUAN & FORMAT KOLOM EXCEL -->
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-8">
    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
        <div>
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="ms text-slate-500 text-[22px]">format_list_numbered</span>
                Format Kolom & Standar Pengisian Berkas
            </h3>
            <div class="text-[11px] text-slate-500 mt-1">Daftar urutan 21 kolom standar SIPANDU-WBK pada berkas template Excel</div>
        </div>
        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 self-start sm:self-auto shrink-0">21 Kolom Standar</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="py-3 px-4 text-xs font-bold text-slate-600 w-12 text-center">KOLOM</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-600 w-48">NAMA KOLOM (HEADER)</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-600 w-32">KATEGORI</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-600">PENJELASAN</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-600 w-64">CONTOH ISIAN VALID</th>
                </tr>
            </thead>
            <tbody class="text-xs">
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">A</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">NO</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Opsional</span></td>
                    <td class="py-3 px-4 text-slate-600">Nomor urut baris di Excel</td>
                    <td class="py-3 px-4 font-medium">1, 2, 3, ...</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">B</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">NIK_KTP</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">ID Unik Mutlak</span></td>
                    <td class="py-3 px-4 text-slate-600">Nomor Induk Kependudukan 16 digit KTP (<b class="text-slate-800">Pencegah duplikat</b>)</td>
                    <td class="py-3 px-4 font-mono">3204011503040001</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">C</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">NOSIK</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Auto Generate</span></td>
                    <td class="py-3 px-4 text-slate-600">Nomor Pokok Siswa Pendidikan (Bila kosong, dibuat otomatis)</td>
                    <td class="py-3 px-4 font-mono">2026-SECABA-011</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">D</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">NAMA_LENGKAP</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Wajib Diisi</span></td>
                    <td class="py-3 px-4 text-slate-600">Nama lengkap prajurit siswa tanpa gelar sipil</td>
                    <td class="py-3 px-4 font-medium">Ahmad Fauzi, Budi Santoso</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">E</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">KODE_SATDIK</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Wajib Diisi</span></td>
                    <td class="py-3 px-4 text-slate-600">Kode satuan pendidikan Rindam III/Siliwangi</td>
                    <td class="py-3 px-4 font-mono text-[11px] leading-relaxed">SECABA, SECATA, DODIKJUR, DODIKLATPUR, BELANEGARA</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">F</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">PROGRAM_PENDIDIKAN</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Opsional</span></td>
                    <td class="py-3 px-4 text-slate-600">Nama program pendidikan yang dijalani</td>
                    <td class="py-3 px-4 font-medium">DIKMABA TA 2026, dll</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">G</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">PANGKAT_SISWA</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Opsional</span></td>
                    <td class="py-3 px-4 text-slate-600">Pangkat kemiliteran (Siswa, Prada, Serda, dll)</td>
                    <td class="py-3 px-4 font-medium">Siswa</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">H</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">KOMPI</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Opsional</span></td>
                    <td class="py-3 px-4 text-slate-600">Kompi penempatan siswa</td>
                    <td class="py-3 px-4 font-medium">Kompi A, Kompi B</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">I</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">PELETON</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Opsional</span></td>
                    <td class="py-3 px-4 text-slate-600">Peleton binaan siswa</td>
                    <td class="py-3 px-4 font-medium">Peleton 1, Ton 2</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">J</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">GENDER_L_P</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Disarankan</span></td>
                    <td class="py-3 px-4 text-slate-600">Jenis Kelamin (L = Laki-laki, P = Perempuan)</td>
                    <td class="py-3 px-4 font-mono">L <span class="font-sans text-slate-400">atau</span> P</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">K</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">KODAM/KODIM_ASAL</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Opsional</span></td>
                    <td class="py-3 px-4 text-slate-600">Kesatuan/Daerah pengirim siswa</td>
                    <td class="py-3 px-4 font-medium">Kodam III/Slw - Kodim 0618</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">L</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">TEMPAT_LAHIR</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Opsional</span></td>
                    <td class="py-3 px-4 text-slate-600">Kota/Kabupaten kelahiran</td>
                    <td class="py-3 px-4 font-medium">Bandung, Garut</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">M</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">TANGGAL_LAHIR</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Opsional</span></td>
                    <td class="py-3 px-4 text-slate-600">Format tanggal (YYYY-MM-DD)</td>
                    <td class="py-3 px-4 font-mono">2004-05-14</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">N</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">GOL_DARAH</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Disarankan</span></td>
                    <td class="py-3 px-4 text-slate-600">Golongan darah dasar</td>
                    <td class="py-3 px-4 font-mono">A, B, AB, O</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">O</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">TB_CM</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Kesehatan</span></td>
                    <td class="py-3 px-4 text-slate-600">Tinggi Badan (cm)</td>
                    <td class="py-3 px-4 font-medium">172, 168</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">P</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">BB_KG</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Kesehatan</span></td>
                    <td class="py-3 px-4 text-slate-600">Berat Badan (kg)</td>
                    <td class="py-3 px-4 font-medium">68, 64.5</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">Q</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">TENSI_MMHG</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Kesehatan</span></td>
                    <td class="py-3 px-4 text-slate-600">Tekanan darah</td>
                    <td class="py-3 px-4 font-mono">120/80</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">R</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">STATUS_KESEHATAN</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Kesehatan</span></td>
                    <td class="py-3 px-4 text-slate-600">Status kesiapan harian fisik</td>
                    <td class="py-3 px-4 font-medium text-[11px]">Siap Latih, Berobat Jalan, Rawat Inap Poliklinik</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">S</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">STAKES_MILITER</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Kesehatan</span></td>
                    <td class="py-3 px-4 text-slate-600">Kualifikasi kebugaran Stakes I-IV</td>
                    <td class="py-3 px-4 font-medium text-[11px]">Stakes I, Stakes II, Stakes III</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">T</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">CATATAN_MEDIS</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Opsional</span></td>
                    <td class="py-3 px-4 text-slate-600">Riwayat alergi / catatan poliklinik</td>
                    <td class="py-3 px-4 font-medium">Alergi paracetamol</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-400">U</td>
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">STATUS_SISWA</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Disarankan</span></td>
                    <td class="py-3 px-4 text-slate-600">Status dinas pendidikan: Aktif (berjalan), Selesai/Lulus (arsip), Sakit</td>
                    <td class="py-3 px-4 font-medium">Aktif, Lulus, DO / Dikeluarkan</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function updateTemplateDownloadUrls(satdikId) {
        const baseUrlXlsx = "{{ route('students.import.template', ['format' => 'xlsx']) }}";
        const baseUrlCsv = "{{ route('students.import.template', ['format' => 'csv']) }}";
        
        const urlXlsx = satdikId ? `${baseUrlXlsx}&satdik_id=${satdikId}` : baseUrlXlsx;
        const urlCsv = satdikId ? `${baseUrlCsv}&satdik_id=${satdikId}` : baseUrlCsv;

        document.getElementById('btnDownloadXlsx').href = urlXlsx;
        document.getElementById('btnDownloadCsv').href = urlCsv;
    }

    function handleFileSelected(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
            document.getElementById('dropFileName').innerHTML = `
                <span class="text-emerald-600 text-base">📄 ${file.name}</span>
                <span class="text-xs text-slate-500 font-normal"> (${sizeMb} MB)</span>
            `;
            const dropZone = document.getElementById('dropZone');
            dropZone.classList.remove('border-amber-400', 'bg-amber-50/50');
            dropZone.classList.add('border-emerald-500', 'bg-emerald-50/50');
        }
    }

    // Drag and Drop Effects
    const dropZone = document.getElementById('dropZone');
    ['dragenter', 'dragover'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('bg-amber-100/80');
        }, false);
    });

    ['dragleave', 'drop'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('bg-amber-100/80');
        }, false);
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            document.getElementById('excel_file').files = files;
            handleFileSelected(document.getElementById('excel_file'));
        }
    }, false);
</script>
@endsection
