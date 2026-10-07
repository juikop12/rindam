@extends('layouts.app')

@section('title', 'Pengaturan Sistem & Informasi Pejabat Pimpinan — SIPANDU')

@section('content')

<!-- BREADCRUMB & HEADER ACTION -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted);">
        <a href="{{ route('dashboard') }}" style="color:var(--o700); text-decoration:none; font-weight:600;">Dashboard</a>
        <span class="ms" style="font-size:16px;">chevron_right</span>
        <span style="color:var(--text); font-weight:700;">Pengaturan Sistem & Pejabat Pimpinan</span>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm">
            <span class="ms">arrow_back</span> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- HEADER BANNER PENGATURAN -->
<div class="header-banner" style="margin-bottom:24px; background:linear-gradient(135deg, #10170C 0%, #1D2A16 50%, #2A3B1F 100%); border-left:5px solid var(--gold);">
    <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px; flex-wrap:wrap;">
            <span class="pdp-badge" style="background:#E8F5E9; color:#1B5E20; border-color:#81C784;">
                <span class="ms" style="font-size:16px;">admin_panel_settings</span> STRUKTUR KOMANDO & KEPEMIMPINAN
            </span>
            <span class="pdp-badge" style="background:rgba(201,162,39,0.18); color:var(--gold2); border-color:var(--gold);">
                <span class="ms" style="font-size:16px;">military_tech</span> RINDAM III / SILIWANGI
            </span>
            <span class="pdp-badge" style="background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.25);">
                <span class="ms" style="font-size:16px;">verified_user</span> SISTEM TERINTEGRASI
            </span>
        </div>
        <h1 style="font-size:26px; margin:0 0 8px; letter-spacing:-0.02em;">
            Pengaturan Sistem & Informasi Pejabat Pimpinan
        </h1>
        <p style="margin:0; font-size:14px; max-width:840px; line-height:1.6; color:#DCE4D6;">
            Konfigurasi nama pejabat pimpinan Mako Rindam III/Siliwangi (Danrindam, Wadanrindam, Tim Kesehatan Poliklinik) serta para Komandan Satuan Pendidikan (Dansecaba, Dansecata, Dandodikjur, Dandodiklatpur, Dandodik Bela Negara).
        </p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-warning" style="background:#FFEBEE; border-color:#FFCDD2; color:var(--red); margin-bottom:24px;">
        <span class="ms" style="font-size:24px;">error</span>
        <div>
            <b>Terdapat kesalahan pada formulir pengaturan:</b>
            <ul style="margin:4px 0 0 16px; padding:0; font-size:12.5px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <!-- =====================================================================
         BAGIAN 1: PEJABAT KOMANDO PUSAT (MAKO RINDAM III/SILIWANGI)
         ===================================================================== -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header" style="background:var(--o50); border-bottom:1px solid var(--line);">
            <div>
                <h3 class="card-title">
                    <span class="ms" style="color:var(--gold); font-size:22px;">stars</span>
                    Pejabat Komando Pusat (Mako Rindam III/Siliwangi)
                </h3>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;">
                    Informasi nama pejabat komandan utama yang ditampilkan pada dossier, lembar disposisi, dan header pimpinan
                </div>
            </div>
            <span class="badge badge-gold" style="font-size:12px;">Mako Rindam</span>
        </div>
        <div class="card-body" style="padding:24px;">

            <!-- 1.1 DANRINDAM -->
            <div style="background:#FAFBF9; border:1px solid var(--line); border-radius:12px; padding:18px; margin-bottom:20px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px; padding-bottom:10px; border-bottom:1px dashed var(--line);">
                    <div style="width:36px; height:36px; border-radius:8px; background:linear-gradient(135deg, var(--gold2), var(--gold)); color:var(--o900); display:grid; place-items:center;">
                        <span class="ms" style="font-size:22px;">military_tech</span>
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:800; color:var(--o900);">KOMANDAN RINDAM (DANRINDAM)</div>
                        <div style="font-size:11.5px; color:var(--muted);">Pucuk pimpinan lembaga pendidikan militer Rindam III/Siliwangi</div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Nama Lengkap & Gelar Danrindam <span style="color:var(--red);">*</span></label>
                        <input type="text" name="danrindam_name" class="form-control" value="{{ old('danrindam_name', $settings['danrindam_name'] ?? 'Kolonel Inf Danrindam III/Siliwangi') }}" required placeholder="Contoh: Kolonel Inf Hendra Kusuma, S.Sos., M.Si.">
                        <div class="form-hint">Nama ini otomatis menyinkronkan profil akun Pimpinan sistem.</div>
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Pangkat / Korps</label>
                        <input type="text" name="danrindam_rank" class="form-control" value="{{ old('danrindam_rank', $settings['danrindam_rank'] ?? 'Kolonel Inf') }}" placeholder="Contoh: Kolonel Inf">
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">NRP / NIP</label>
                        <input type="text" name="danrindam_nrp" class="form-control" value="{{ old('danrindam_nrp', $settings['danrindam_nrp'] ?? '11980012340576') }}" placeholder="Contoh: 11980012340576">
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Sebutan Jabatan Resmi <span style="color:var(--red);">*</span></label>
                        <input type="text" name="danrindam_title" class="form-control" value="{{ old('danrindam_title', $settings['danrindam_title'] ?? 'Komandan Resimen Induk Kodam III/Siliwangi') }}" required placeholder="Contoh: Komandan Resimen Induk Kodam III/Siliwangi">
                    </div>
                </div>
            </div>

            <!-- 1.2 WADANRINDAM -->
            <div style="background:#FAFBF9; border:1px solid var(--line); border-radius:12px; padding:18px; margin-bottom:20px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px; padding-bottom:10px; border-bottom:1px dashed var(--line);">
                    <div style="width:36px; height:36px; border-radius:8px; background:var(--o100); color:var(--o800); display:grid; place-items:center;">
                        <span class="ms" style="font-size:22px;">shield</span>
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:800; color:var(--o900);">WAKIL KOMANDAN RINDAM (WADANRINDAM)</div>
                        <div style="font-size:11.5px; color:var(--muted);">Wakil pimpinan pengawasan operasional dan tata kelola satuan</div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Nama Lengkap & Gelar Wadanrindam</label>
                        <input type="text" name="wadanrindam_name" class="form-control" value="{{ old('wadanrindam_name', $settings['wadanrindam_name'] ?? 'Kolonel Inf Wadanrindam III/Siliwangi') }}" placeholder="Contoh: Kolonel Inf Bambang Triyono, S.I.P.">
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Pangkat / Korps</label>
                        <input type="text" name="wadanrindam_rank" class="form-control" value="{{ old('wadanrindam_rank', $settings['wadanrindam_rank'] ?? 'Kolonel Inf') }}" placeholder="Contoh: Kolonel Inf">
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">NRP / NIP</label>
                        <input type="text" name="wadanrindam_nrp" class="form-control" value="{{ old('wadanrindam_nrp', $settings['wadanrindam_nrp'] ?? '11990023450678') }}" placeholder="Contoh: 11990023450678">
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Sebutan Jabatan Resmi</label>
                        <input type="text" name="wadanrindam_title" class="form-control" value="{{ old('wadanrindam_title', $settings['wadanrindam_title'] ?? 'Wakil Komandan Rindam III/Siliwangi') }}" placeholder="Contoh: Wakil Komandan Rindam III/Siliwangi">
                    </div>
                </div>
            </div>

            <!-- 1.3 KAKES / DOKTER POLIKLINIK -->
            <div style="background:#FAFBF9; border:1px solid var(--line); border-radius:12px; padding:18px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px; padding-bottom:10px; border-bottom:1px dashed var(--line);">
                    <div style="width:36px; height:36px; border-radius:8px; background:#E8F5E9; color:var(--green); display:grid; place-items:center;">
                        <span class="ms" style="font-size:22px;">medical_services</span>
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:800; color:var(--o900);">KEPALA TIM KESEHATAN / DOKTER POLIKLINIK (KAKES)</div>
                        <div style="font-size:11.5px; color:var(--muted);">Penanggung jawab medis rekam kesehatan & kualifikasi stakes prajurit siswa</div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Nama Dokter / Kakes</label>
                        <input type="text" name="kakes_name" class="form-control" value="{{ old('kakes_name', $settings['kakes_name'] ?? 'Mayor Ckm dr. Hendra Irawan, Sp.KO') }}" placeholder="Contoh: Mayor Ckm dr. Hendra Irawan, Sp.KO">
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Pangkat / Korps Medis</label>
                        <input type="text" name="kakes_rank" class="form-control" value="{{ old('kakes_rank', $settings['kakes_rank'] ?? 'Mayor Ckm') }}" placeholder="Contoh: Mayor Ckm">
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">NRP Dokter</label>
                        <input type="text" name="kakes_nrp" class="form-control" value="{{ old('kakes_nrp', $settings['kakes_nrp'] ?? '11020034560789') }}" placeholder="Contoh: 11020034560789">
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Sebutan Jabatan Medis</label>
                        <input type="text" name="kakes_title" class="form-control" value="{{ old('kakes_title', $settings['kakes_title'] ?? 'Kepala Tim Kesehatan / Dokter Poliklinik Rindam') }}" placeholder="Contoh: Kepala Tim Kesehatan / Dokter Poliklinik Rindam">
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- =====================================================================
         BAGIAN 2: PEJABAT KOMANDAN 5 SATUAN PENDIDIKAN (SATDIK)
         ===================================================================== -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header" style="background:var(--o50); border-bottom:1px solid var(--line);">
            <div>
                <h3 class="card-title">
                    <span class="ms" style="color:var(--o700); font-size:22px;">domain</span>
                    Komandan Satuan Pendidikan Jajaran (5 Satdik)
                </h3>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;">
                    Pengaturan pejabat Komandan Secaba, Secata, Dodikjur, Dodiklatpur, dan Dodik Bela Negara
                </div>
            </div>
            <span class="badge badge-blue" style="font-size:12px;">5 Satuan Pendidikan</span>
        </div>
        <div class="card-body" style="padding:24px;">
            <div style="display:flex; flex-direction:column; gap:16px;">
                @foreach($satdiks as $index => $satdik)
                    <div style="background:#FAFBF9; border:1px solid var(--line); border-radius:12px; padding:18px;">
                        <input type="hidden" name="satdiks[{{ $index }}][id]" value="{{ $satdik->id }}">
                        
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; padding-bottom:8px; border-bottom:1px dashed var(--line); flex-wrap:wrap; gap:8px;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span class="badge badge-satdik" style="font-size:12px; padding:5px 10px;">{{ $satdik->code }}</span>
                                <span style="font-size:14px; font-weight:800; color:var(--o900);">{{ $satdik->name }}</span>
                            </div>
                            <span style="font-size:12px; color:var(--muted);">{{ $satdik->students_count ?? $satdik->students()->count() }} Serdik Terdaftar</span>
                        </div>

                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:14px;">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Nama Komandan Satdik <span style="color:var(--red);">*</span></label>
                                <input type="text" name="satdiks[{{ $index }}][commander_name]" class="form-control" value="{{ old("satdiks.{$index}.commander_name", $satdik->commander_name) }}" required placeholder="Contoh: Letkol Inf Hendra Prasetyo, S.I.P.">
                            </div>

                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Sebutan Jabatan Komandan <span style="color:var(--red);">*</span></label>
                                <input type="text" name="satdiks[{{ $index }}][commander_title]" class="form-control" value="{{ old("satdiks.{$index}.commander_title", $satdik->commander_title) }}" required placeholder="Contoh: Komandan Secaba Rindam III/Siliwangi">
                            </div>

                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Lokasi Ksatrian / Markas Satdik</label>
                                <input type="text" name="satdiks[{{ $index }}][location]" class="form-control" value="{{ old("satdiks.{$index}.location", $satdik->location) }}" placeholder="Contoh: Ksatrian Secaba Rindam III/Siliwangi (Bihbul, Bandung)">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- =====================================================================
         BAGIAN 3: IDENTITAS SATUAN PUSAT & PANGKALAN
         ===================================================================== -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header" style="background:var(--o50); border-bottom:1px solid var(--line);">
            <div>
                <h3 class="card-title">
                    <span class="ms" style="color:var(--o800); font-size:22px;">corporate_fare</span>
                    Identitas Satuan Pusat & Pangkalan (Mako)
                </h3>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;">
                    Nama induk satuan lembaga pendidikan militer dan alamat markas komando
                </div>
            </div>
            <span class="badge" style="background:#F0F4EC; color:var(--o800); font-size:12px;">Identitas Induk</span>
        </div>
        <div class="card-body" style="padding:24px;">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:18px;">
                <div class="form-group">
                    <label class="form-label">Nama Satuan Pusat <span style="color:var(--red);">*</span></label>
                    <input type="text" name="institution_name" class="form-control" value="{{ old('institution_name', $settings['institution_name'] ?? 'RINDAM III / SILIWANGI') }}" required placeholder="Contoh: RINDAM III / SILIWANGI">
                    <div class="form-hint">Muncul di header navbar, kop dossier siswa, dan laporan.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Sub-identitas / Deskripsi Kedinasan</label>
                    <input type="text" name="institution_sub" class="form-control" value="{{ old('institution_sub', $settings['institution_sub'] ?? 'Komando Pembinaan Pendidikan Militer Kodam III/Siliwangi') }}" placeholder="Contoh: Komando Pembinaan Pendidikan Militer Kodam III/Siliwangi">
                </div>

                <div class="form-group">
                    <label class="form-label">Slogan / Motto Satuan</label>
                    <input type="text" name="institution_slogan" class="form-control" value="{{ old('institution_slogan', $settings['institution_slogan'] ?? 'Esa Hilang Dua Terbilang') }}" placeholder="Contoh: Esa Hilang Dua Terbilang">
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Markas Komando (Mako Pusat)</label>
                    <input type="text" name="mako_location" class="form-control" value="{{ old('mako_location', $settings['mako_location'] ?? 'Ksatrian Mako Rindam III/Siliwangi, Jl. Menado No. 1 / Bihbul Bandung') }}" placeholder="Contoh: Ksatrian Mako Rindam III/Siliwangi, Jl. Menado No. 1 / Bihbul Bandung">
                </div>
            </div>
        </div>
    </div>

    <!-- FORM ACTION FOOTER -->
    <div class="card">
        <div class="card-body" style="display:flex; align-items:center; justify-content:space-between; padding:20px 24px; flex-wrap:wrap; gap:14px;">
            <div style="font-size:12.5px; color:var(--muted); display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:var(--green); font-size:20px;">verified</span>
                <span>Perubahan nama pejabat pimpinan langsung otomatis tersinkronisasi ke profil komando dan laporan satuan.</span>
            </div>
            <div style="display:flex; gap:12px;">
                <a href="{{ route('dashboard') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-gold" style="padding:10px 28px; font-size:14px;">
                    <span class="ms">save</span> Simpan Perubahan Pejabat & Pengaturan
                </button>
            </div>
        </div>
    </div>

</form>

<!-- =====================================================================
     BAGIAN 4: ZONA PEMELIHARAAN SISTEM & PENGOSONGAN DATA
     ===================================================================== -->
<div class="card" style="margin-top:28px; border:1px solid #FECDD3; background:#FFF1F2; border-radius:16px; overflow:hidden;">
    <div class="card-body" style="padding:24px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
        <div style="display:flex; align-items:flex-start; gap:16px; max-width:720px;">
            <div style="width:48px; height:48px; border-radius:12px; background:#FFE4E6; color:#E11D48; display:grid; place-items:center; flex-shrink:0;">
                <span class="ms" style="font-size:28px;">delete_sweep</span>
            </div>
            <div>
                <div style="font-size:15px; font-weight:800; color:#9F1239; margin-bottom:4px;">
                    Zona Pemeliharaan: Pengosongan Data Sistem (Data Wipe)
                </div>
                <div style="font-size:12.5px; color:#BE123C; line-height:1.5;">
                    Pengosongan basis data operasional secara aman oleh sistem (Siswa, Rekam Medis, Kompi/Peleton, Program Diklat, atau Log Audit) untuk persiapan tahun ajaran baru atau pembersihan data simulasi tanpa perlu mengakses database secara manual.
                </div>
            </div>
        </div>
        <div>
            <a href="{{ route('settings.cleanup') }}" class="btn" style="background:#E11D48; color:#fff; border:none; padding:10px 22px; font-size:13px; font-weight:700; border-radius:10px; display:inline-flex; align-items:center; gap:8px; text-decoration:none; box-shadow:0 4px 12px rgba(225,29,72,0.25);">
                <span class="ms" style="font-size:18px;">delete_forever</span> Buka Pengosongan Data
            </a>
        </div>
    </div>
</div>

@endsection
