@extends('layouts.app')

@section('title', 'Penginputan Data Siswa Format Excel — SIPANDU-WBK')

@section('content')

<!-- BREADCRUMB -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted);">
        <a href="{{ route('students.index') }}" style="color:var(--o700); text-decoration:none; font-weight:600;">Data Serdik</a>
        <span class="ms" style="font-size:16px;">chevron_right</span>
        <span style="color:var(--text); font-weight:700;">Penginputan Format Excel</span>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('students.index') }}" class="btn btn-outline btn-sm">
            <span class="ms">arrow_back</span> Kembali ke Buku Induk
        </a>
    </div>
</div>

<!-- HEADER BANNER PENGINPUTAN EXCEL -->
<div class="header-banner" style="margin-bottom:24px; background:linear-gradient(135deg, #10170C 0%, #1D2A16 50%, #2A3B1F 100%); border-left:5px solid var(--gold);">
    <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px; flex-wrap:wrap;">
            <span class="pdp-badge" style="background:#E8F5E9; color:#1B5E20; border-color:#81C784;">
                <span class="ms" style="font-size:16px;">upload_file</span> INPUT FORMAT EXCEL
            </span>
            <span class="pdp-badge" style="background:rgba(201,162,39,0.18); color:var(--gold2); border-color:var(--gold);">
                <span class="ms" style="font-size:16px;">school</span> 5 SATUAN PENDIDIKAN (SATDIK)
            </span>
            <span class="pdp-badge" style="background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.25);">
                <span class="ms" style="font-size:16px;">verified_user</span> ZONA INTEGRITAS WBK
            </span>
        </div>
        <h1 style="font-size:26px; margin:0 0 8px; letter-spacing:-0.02em;">
            Penginputan Data Serdik Menggunakan Format Excel
        </h1>
        <p style="margin:0; font-size:14px; max-width:840px; line-height:1.6; color:#DCE4D6;">
            Fasilitas penginputan massal (batch upload) data prajurit siswa lintas Satdik menggunakan format berkas Excel (.xlsx / .xls) atau CSV (.csv). Sistem otomatis memetakan satuan, kompi/peleton, serta indikator rekam medis & status kesehatan awal.
        </p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-warning" style="background:#FFEBEE; border-color:#FFCDD2; color:var(--red); margin-bottom:24px;">
        <span class="ms" style="font-size:24px;">error</span>
        <div>
            <b>Terjadi kendala saat membaca berkas Excel:</b>
            <ul style="margin:4px 0 0 16px; padding:0; font-size:12.5px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div style="display:grid; grid-template-columns: 1fr 1.2fr; gap:24px; margin-bottom:28px;">

    <!-- =====================================================================
         KARTU 1: UNDUH TEMPLATE EXCEL
         ===================================================================== -->
    <div class="card" style="margin-bottom:0; display:flex; flex-direction:column;">
        <div class="card-header" style="background:var(--o50); border-bottom:1px solid var(--line);">
            <div>
                <h3 class="card-title">
                    <span class="ms" style="color:var(--gold); font-size:22px;">download</span>
                    1. Unduh Format Template Excel
                </h3>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;">
                    Unduh berkas acuan pengisian data serdik resmi Rindam III/Siliwangi
                </div>
            </div>
            <span class="badge badge-gold" style="font-size:12px;">Format Standar</span>
        </div>
        <div class="card-body" style="padding:22px; flex:1; display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <p style="font-size:13.5px; color:var(--text); line-height:1.6; margin-top:0;">
                    Gunakan template resmi ini untuk menginput puluhan atau ratusan siswa sekaligus. Di dalam template telah disediakan:
                </p>
                <ul style="font-size:13px; color:var(--muted); line-height:1.8; margin-bottom:20px; padding-left:20px;">
                    <li>Header kolom terstandarisasi untuk 5 Satdik Rindam III/Siliwangi.</li>
                    <li>Baris contoh pengisian untuk Secaba, Secata, Dodikjur, Dodiklatpur, dan Bela Negara.</li>
                    <li>Kolom indikator kesehatan fisik (TB, BB, Tensi, Status Kesiapan, Stakes I-IV).</li>
                    <li>Mendukung formula otomatis dan format cell Excel.</li>
                </ul>

                <div style="background:#FAFBF9; border:1px solid var(--line); border-radius:10px; padding:14px; margin-bottom:20px;">
                    <label class="form-label" style="font-size:12px; text-transform:uppercase; letter-spacing:0.04em;">Pilih Satdik Acuan (Opsional):</label>
                    <select id="templateSatdikSelect" class="form-control" style="font-size:13px;" onchange="updateTemplateDownloadUrls(this.value)">
                        <option value="">Semua Satdik (Rindam III/Siliwangi)</option>
                        @foreach($satdiks as $s)
                            <option value="{{ $s->id }}" {{ $selectedSatdikId == $s->id ? 'selected' : '' }}>
                                {{ $s->code }} — {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:10px; padding-top:16px; border-top:1px solid var(--line);">
                <a id="btnDownloadXlsx" href="{{ route('students.import.template', ['format' => 'xlsx', 'satdik_id' => $selectedSatdikId]) }}" class="btn btn-gold" style="justify-content:center; padding:12px 18px; font-size:14px;">
                    <span class="ms" style="font-size:18px;">table_view</span> Unduh Template Format Excel (.xlsx)
                </a>
                <a id="btnDownloadCsv" href="{{ route('students.import.template', ['format' => 'csv', 'satdik_id' => $selectedSatdikId]) }}" class="btn btn-outline" style="justify-content:center; padding:10px 18px; font-size:13.5px;">
                    <span class="ms" style="font-size:18px;">description</span> Unduh Format CSV (.csv)
                </a>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         KARTU 2: FORM UNGGAH BERKAS EXCEL
         ===================================================================== -->
    <div class="card" style="margin-bottom:0; display:flex; flex-direction:column;">
        <div class="card-header" style="background:var(--o50); border-bottom:1px solid var(--line);">
            <div>
                <h3 class="card-title">
                    <span class="ms" style="color:var(--green); font-size:22px;">cloud_upload</span>
                    2. Unggah & Proses Berkas Excel
                </h3>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;">
                    Kirim berkas yang telah diisi untuk disimpan langsung ke basis data
                </div>
            </div>
            <span class="badge badge-green" style="font-size:12px;">Unggah Berkas</span>
        </div>
        <div class="card-body" style="padding:22px; flex:1;">
            <form action="{{ route('students.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- TARGET SATDIK -->
                <div class="form-group">
                    <label class="form-label" for="satdik_id">
                        Satdik Tujuan Penginputan:
                    </label>
                    <select name="satdik_id" id="satdik_id" class="form-control">
                        <option value="">Otomatis (Sesuai Kolom KODE_SATDIK di Berkas Excel)</option>
                        @foreach($satdiks as $s)
                            <option value="{{ $s->id }}" {{ $selectedSatdikId == $s->id ? 'selected' : '' }}>
                                {{ $s->code }} — {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Jika dipilih spesifik, seluruh baris yang tidak mengisi kode satdik akan otomatis masuk ke Satdik ini.</div>
                </div>

                <!-- DRAG & DROP FILE ZONE -->
                <div class="form-group">
                    <label class="form-label">
                        Pilih Berkas Excel / CSV <span style="color:var(--red);">*</span>
                    </label>
                    <div id="dropZone" style="border:2px dashed var(--gold); border-radius:12px; background:#FBF6E5; padding:28px 20px; text-align:center; cursor:pointer; transition:all 0.2s;" onclick="document.getElementById('excel_file').click()">
                        <span class="ms" style="font-size:44px; color:var(--gold); display:block; margin-bottom:8px;">upload_file</span>
                        <div style="font-weight:700; color:var(--o900); font-size:14.5px;" id="dropFileName">
                            Klik di sini atau seret berkas Excel ke area ini
                        </div>
                        <div style="font-size:12px; color:var(--muted); margin-top:4px;">
                            Mendukung berkas: <b>.xlsx</b>, <b>.xls</b>, atau <b>.csv</b> (Maksimal 10 MB)
                        </div>
                    </div>
                    <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls,.csv" required style="display:none;" onchange="handleFileSelected(this)">
                </div>

                <!-- UPDATE EXISTING CHECKBOX -->
                <div style="background:var(--o50); border:1px solid var(--line); border-radius:10px; padding:12px 16px; margin-bottom:22px; display:flex; align-items:flex-start; gap:10px;">
                    <input type="checkbox" name="update_existing" id="update_existing" value="1" checked style="margin-top:3px; cursor:pointer;">
                    <label for="update_existing" style="font-size:12.5px; color:var(--text); cursor:pointer; margin:0; line-height:1.5;">
                        <b>Perbarui data otomatis jika NIK KTP sudah terdaftar</b><br>
                        <span style="color:var(--muted);">Jika dicentang, data serdik dan rekam medis yang NIK KTP (16 digit)-nya sudah ada di database akan diperbarui dengan data terbaru dari Excel. Jika tidak dicentang, data dengan NIK yang sama akan dilewati demi mencegah duplikasi siswa.</span>
                    </label>
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:13px 24px; font-size:15px; font-weight:800;">
                    <span class="ms">save</span> Proses & Simpan Data Serdik dari Excel
                </button>
            </form>
        </div>
    </div>

</div>

<!-- =====================================================================
     PANDUAN & FORMAT KOLOM EXCEL
     ===================================================================== -->
<div class="card">
    <div class="card-header" style="background:#fff; border-bottom:1px solid var(--line);">
        <div>
            <h3 class="card-title">
                <span class="ms" style="color:var(--o800); font-size:22px;">format_list_numbered</span>
                Format Kolom & Standar Pengisian Berkas Excel
            </h3>
            <div style="font-size:12.5px; color:var(--muted); margin-top:2px;">
                Daftar urutan 21 kolom pada berkas template Excel beserta contoh nilai yang valid (NIK KTP sebagai acuan pencegah duplikasi)
            </div>
        </div>
        <span class="badge" style="background:#E8F5E9; color:#1B5E20; font-size:12px;">21 Kolom Standar</span>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:60px;">KOLOM</th>
                    <th style="width:170px;">NAMA KOLOM (HEADER)</th>
                    <th style="width:90px;">STATUS</th>
                    <th>PENJELASAN & CONTOH NILAI</th>
                    <th style="width:230px;">CONTOH ISIAN VALID</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>A</b></td>
                    <td><code>NO</code></td>
                    <td><span class="badge" style="background:#E0E0E0; color:#424242;">Opsional</span></td>
                    <td>Nomor urut baris di Excel</td>
                    <td>1, 2, 3, ...</td>
                </tr>
                <tr>
                    <td><b>B</b></td>
                    <td><code>NIK_KTP</code></td>
                    <td><span class="badge badge-red">ID Unik</span></td>
                    <td>Nomor Induk Kependudukan 16 digit KTP (<b>Pencegah data duplikat</b> & terenkripsi AES-256)</td>
                    <td><code>3204011503040001</code></td>
                </tr>
                <tr>
                    <td><b>C</b></td>
                    <td><code>NOSIK</code></td>
                    <td><span class="badge" style="background:#E0E0E0; color:#424242;">Otomatis</span></td>
                    <td>Nomor Pokok Siswa Pendidikan (Bila kosong, otomatis digenerate sistem)</td>
                    <td><code>2026-SECABA-011</code></td>
                </tr>
                <tr>
                    <td><b>D</b></td>
                    <td><code>NAMA_LENGKAP</code></td>
                    <td><span class="badge badge-red">Wajib</span></td>
                    <td>Nama lengkap prajurit siswa</td>
                    <td>Ahmad Fauzi, Budi Santoso</td>
                </tr>
                <tr>
                    <td><b>E</b></td>
                    <td><code>KODE_SATDIK</code></td>
                    <td><span class="badge badge-red">Wajib</span></td>
                    <td>Kode satuan pendidikan tempat siswa dididik</td>
                    <td><code>SECABA</code>, <code>SECATA</code>, <code>DODIKJUR</code>, <code>DODIKLATPUR</code>, <code>BELANEGARA</code></td>
                </tr>
                <tr>
                    <td><b>F</b></td>
                    <td><code>PROGRAM_PENDIDIKAN</code></td>
                    <td><span class="badge" style="background:#E0E0E0; color:#424242;">Opsional</span></td>
                    <td>Nama program pendidikan militer (misal: DIKMABA TA 2026, DIKJURBA TA 2026)</td>
                    <td><code>DIKMABA TA 2026</code>, <code>DIKJURBA TA 2026</code></td>
                </tr>
                <tr>
                    <td><b>G</b></td>
                    <td><code>PANGKAT_SISWA</code></td>
                    <td><span class="badge" style="background:#E0E0E0; color:#424242;">Opsional</span></td>
                    <td>Pangkat kemiliteran selama masa pendidikan</td>
                    <td>Siswa, Prada, Serda</td>
                </tr>
                <tr>
                    <td><b>H</b></td>
                    <td><code>KOMPI</code></td>
                    <td><span class="badge" style="background:#E0E0E0; color:#424242;">Opsional</span></td>
                    <td>Nama Kompi penempatan serdik</td>
                    <td>Kompi A, Kompi B, Kompi Senapan</td>
                </tr>
                <tr>
                    <td><b>I</b></td>
                    <td><code>PELETON</code></td>
                    <td><span class="badge" style="background:#E0E0E0; color:#424242;">Opsional</span></td>
                    <td>Peleton binaan siswa</td>
                    <td>Peleton 1, Peleton 2, Ton 3</td>
                </tr>
                <tr>
                    <td><b>J</b></td>
                    <td><code>GENDER_L_P</code></td>
                    <td><span class="badge badge-gold">Disarankan</span></td>
                    <td>Jenis Kelamin (L = Laki-laki, P = Perempuan)</td>
                    <td><code>L</code> atau <code>P</code></td>
                </tr>
                <tr>
                    <td><b>K</b></td>
                    <td><code>KODAM/KODIM_ASAL</code></td>
                    <td><span class="badge" style="background:#E0E0E0; color:#424242;">Opsional</span></td>
                    <td>Kodam / Kodim asal pengirim prajurit siswa</td>
                    <td><code>Kodam III/Slw - Kodim 0618/Kota Bandung</code></td>
                </tr>
                <tr>
                    <td><b>L</b></td>
                    <td><code>TEMPAT_LAHIR</code></td>
                    <td><span class="badge" style="background:#E0E0E0; color:#424242;">Opsional</span></td>
                    <td>Kota/Kabupaten tempat lahir</td>
                    <td>Bandung, Sukabumi, Garut</td>
                </tr>
                <tr>
                    <td><b>M</b></td>
                    <td><code>TANGGAL_LAHIR</code></td>
                    <td><span class="badge" style="background:#E0E0E0; color:#424242;">Opsional</span></td>
                    <td>Format tanggal lahir YYYY-MM-DD</td>
                    <td><code>2004-05-14</code></td>
                </tr>
                <tr>
                    <td><b>N</b></td>
                    <td><code>GOL_DARAH</code></td>
                    <td><span class="badge badge-gold">Disarankan</span></td>
                    <td>Golongan darah serdik</td>
                    <td><code>A</code>, <code>B</code>, <code>AB</code>, <code>O</code></td>
                </tr>
                <tr>
                    <td><b>O</b></td>
                    <td><code>TB_CM</code></td>
                    <td><span class="badge badge-green">Medis</span></td>
                    <td>Tinggi Badan dalam centimeter</td>
                    <td>172, 168, 175</td>
                </tr>
                <tr>
                    <td><b>P</b></td>
                    <td><code>BB_KG</code></td>
                    <td><span class="badge badge-green">Medis</span></td>
                    <td>Berat Badan dalam kilogram (BMI dihitung otomatis)</td>
                    <td>68, 64.5, 70</td>
                </tr>
                <tr>
                    <td><b>Q</b></td>
                    <td><code>TENSI_MMHG</code></td>
                    <td><span class="badge badge-green">Medis</span></td>
                    <td>Tekanan darah pemeriksaan awal</td>
                    <td><code>120/80</code>, <code>115/75</code></td>
                </tr>
                <tr>
                    <td><b>R</b></td>
                    <td><code>STATUS_KESEHATAN</code></td>
                    <td><span class="badge badge-green">Medis</span></td>
                    <td>Status fisik/latihan kesiapan serdik harian</td>
                    <td><code>Siap Latih</code>, <code>Berobat Jalan</code>, <code>Rawat Inap Poliklinik</code>, <code>Rujuk Rumkit</code></td>
                </tr>
                <tr>
                    <td><b>S</b></td>
                    <td><code>STAKES_MILITER</code></td>
                    <td><span class="badge badge-green">Medis</span></td>
                    <td>Kualifikasi kebugaran militer Stakes I s.d. IV</td>
                    <td><code>Stakes I</code>, <code>Stakes II</code>, <code>Stakes III</code>, <code>Stakes IV</code></td>
                </tr>
                <tr>
                    <td><b>T</b></td>
                    <td><code>CATATAN_MEDIS</code></td>
                    <td><span class="badge" style="background:#E0E0E0; color:#424242;">Opsional</span></td>
                    <td>Riwayat alergi atau catatan Poliklinik Satdik</td>
                    <td>Bebas alergi obat, siap latihan</td>
                </tr>
                <tr>
                    <td><b>U</b></td>
                    <td><code>STATUS_SISWA</code></td>
                    <td><span class="badge badge-gold">Disarankan</span></td>
                    <td>Status keaktifan serdik: <b>Aktif</b> (Terhitung dalam kuota pendidikan berjalan), <b>Selesai</b> / <b>Lulus</b> (Arsip siswa selesai pendidikan — tidak terhitung lagi), <b>Sakit</b>, atau <b>Dinas Luar</b></td>
                    <td><code>Aktif</code>, <code>Selesai</code>, <code>Lulus</code>, <code>Sakit</code></td>
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
                <span style="color:var(--green); font-size:16px;">📄 ${file.name}</span>
                <span style="font-size:12px; color:var(--muted); font-weight:normal;"> (${sizeMb} MB)</span>
            `;
            document.getElementById('dropZone').style.borderColor = 'var(--green)';
            document.getElementById('dropZone').style.background = '#E8F5E9';
        }
    }

    // Drag and Drop Effects
    const dropZone = document.getElementById('dropZone');
    ['dragenter', 'dragover'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.borderColor = 'var(--gold2)';
            dropZone.style.background = '#F5EDCE';
        }, false);
    });

    ['dragleave', 'drop'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
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
