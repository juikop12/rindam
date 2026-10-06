@extends('layouts.app')

@section('title', 'Dossier Serdik: ' . $student->full_name . ' — SIPANDU-WBK')

@section('content')

<!-- BREADCRUMB & BACK BUTTON -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted);">
        <a href="{{ route('students.index') }}" style="color:var(--o700); text-decoration:none; font-weight:600;">Data Serdik</a>
        <span class="ms" style="font-size:16px;">chevron_right</span>
        <a href="{{ route('students.index', ['satdik_id' => $student->satdik_id]) }}" style="color:var(--o700); text-decoration:none; font-weight:600;">{{ $student->satdik->code }}</a>
        <span class="ms" style="font-size:16px;">chevron_right</span>
        <span style="color:var(--text); font-weight:700;">{{ $student->nosik }}</span>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('students.index', ['satdik_id' => $student->satdik_id]) }}" class="btn btn-outline btn-sm">
            <span class="ms">arrow_back</span> Kembali ke Daftar
        </a>
        <a href="{{ route('students.audit-logs') }}" class="btn btn-outline btn-sm">
            <span class="ms">policy</span> Lihat Seluruh Audit Trail
        </a>
    </div>
</div>

<!-- STUDENT PROFILE HEADER HERO -->
<div class="card" style="margin-bottom:24px; border-left: 5px solid var(--gold);">
    <div class="card-body" style="padding:28px;">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:20px;">
            <div style="display:flex; align-items:center; gap:22px;">
                <!-- AVATAR / PHOTO -->
                <div style="width:84px; height:84px; border-radius:18px; background:linear-gradient(135deg, var(--o800), var(--o600)); color:var(--gold2); display:grid; place-items:center; font-size:32px; font-weight:900; font-family:'Montserrat',sans-serif; border:3px solid var(--gold); box-shadow:0 8px 20px rgba(0,0,0,0.15);">
                    {{ substr($student->full_name, 0, 2) }}
                </div>
                <div>
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                        <span class="badge badge-satdik" style="font-size:12px; font-weight:800; background:var(--o800); color:var(--gold2);">
                            <span class="ms" style="font-size:14px;">account_balance</span> {{ $student->satdik->code }}
                        </span>
                        <span class="badge" style="font-family:'Fira Code',monospace; font-size:12px; background:var(--o100); color:var(--o800);">
                            NOSIK: {{ $student->nosik }}
                        </span>
                        @php $u = $student->unified_status; @endphp
                        <span class="badge {{ $u['badge'] }}" style="font-size:12px;">
                            <span class="ms" style="font-size:14px;">{{ $u['icon'] }}</span> {{ $u['label'] }}
                        </span>
                    </div>
                    <h1 style="margin:0 0 6px; font-family:'Montserrat',sans-serif; font-size:24px; font-weight:800; color:var(--o900);">
                        {{ $student->full_name }}
                    </h1>
                    <div style="color:var(--muted); font-size:13.5px; display:flex; align-items:center; gap:16px;">
                        <span><b style="color:var(--o800);">Pangkat:</b> {{ $student->student_rank }}</span>
                        <span>•</span>
                        <span><b style="color:var(--o800);">Program:</b> {{ $student->educationProgram->name ?? '-' }}</span>
                        <span>•</span>
                        <span><b style="color:var(--o800);">Kelas:</b> {{ $student->classroom->name ?? 'Belum Ditentukan' }}</span>
                    </div>
                </div>
            </div>

            <div style="text-align:right;">
                @if($student->is_counted)
                    <div style="margin-bottom:8px;">
                        <span class="badge badge-green" style="font-size:12px; padding:6px 12px;">
                            <span class="ms" style="font-size:15px;">how_to_reg</span> Siswa Aktif Terhitung
                        </span>
                    </div>
                @else
                    <div style="margin-bottom:8px;">
                        <span class="badge" style="background:#475569; color:#fff; font-size:12px; padding:6px 12px;">
                            <span class="ms" style="font-size:15px;">archive</span> Status Arsip (Tidak Terhitung)
                        </span>
                    </div>
                @endif
                <div class="pdp-badge" style="margin-bottom:8px;">
                    <span class="ms" style="font-size:15px;">enhanced_encryption</span> Data Pribadi Terproteksi Sistem
                </div>
                <div style="font-size:12px; color:var(--muted);">
                    Terdaftar sejak: {{ $student->created_at->format('d M Y, H:i') }} WIB
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">

    <!-- KARTU 1: PROFIL KEMILITERAN & PENDIDIKAN (PUBLIC TO OPERATORS) -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <h3 class="card-title">
                <span class="ms" style="color:var(--o700);">military_tech</span> Data Kemiliteran & Satdik
            </h3>
            <span class="badge badge-satdik">{{ $student->satdik->code }}</span>
        </div>
        <div class="card-body">
            <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted); width:40%;">Satuan Pendidikan</td>
                    <td style="padding:10px 0; font-weight:600; color:var(--o900);">{{ $student->satdik->name }} ({{ $student->satdik->code }})</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Nomor Siswa (NOSIK)</td>
                    <td style="padding:10px 0; font-family:'Fira Code',monospace; font-weight:700; color:var(--o800);">{{ $student->nosik }}</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Program Pendidikan</td>
                    <td style="padding:10px 0; font-weight:600;">{{ $student->educationProgram->name ?? '-' }} (TA {{ $student->educationProgram->academic_year ?? '-' }})</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Kompi & Peleton Siswa</td>
                    <td style="padding:10px 0; font-weight:600;">
                        {{ $student->classroom->name ?? ($student->company ? $student->company . ' ' . $student->platoon : 'Belum Ditentukan') }}
                        @if($student->company || $student->platoon)
                            <div style="font-size:11.5px; color:var(--muted); font-weight:500; margin-top:2px;">
                                Kompi: <b style="color:var(--o900);">{{ $student->company ?? '-' }}</b> · Peleton: <b style="color:var(--o900);">{{ $student->platoon ?? '-' }}</b>
                                @if($student->classroom?->platoon_leader_name)
                                    · Danton: <b>{{ $student->classroom->platoon_leader_name }}</b>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Kodam / Kodim Asal</td>
                    <td style="padding:10px 0; font-weight:600;">{{ $student->origin_military_unit ?? '-' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Tempat, Tanggal Lahir</td>
                    <td style="padding:10px 0; font-weight:600;">
                        {{ $student->birth_place ?? '-' }}, 
                        {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-' }}
                        @if($student->birth_date)
                            <span style="font-size:12px; color:var(--muted);">({{ \Carbon\Carbon::parse($student->birth_date)->age }} tahun)</span>
                        @endif
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Jenis Kelamin</td>
                    <td style="padding:10px 0; font-weight:600;">{{ $student->gender == 'L' ? 'Laki-Laki' : 'Perempuan' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Agama</td>
                    <td style="padding:10px 0; font-weight:600;">{{ $student->religion ?? '-' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Golongan Darah</td>
                    <td style="padding:10px 0; font-weight:700; color:var(--red);">{{ $student->blood_type ?? '-' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Status Fisik / Kesehatan</td>
                    <td style="padding:10px 0;">
                        <span class="badge {{ $u['badge'] }}">
                            <span class="ms" style="font-size:13px;">{{ $u['icon'] }}</span> {{ $u['label'] }}
                        </span>
                        <a href="{{ route('health.show', $student) }}" style="font-size:12px; margin-left:8px; color:var(--o700); font-weight:700; text-decoration:none;">
                            (Lihat Rekam Medis &rarr;)
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:10px 0; color:var(--muted);">Status Keaktifan Pendidikan</td>
                    <td style="padding:10px 0;">
                        @if(auth()->user()?->canModifyData())
                            <form method="POST" action="{{ route('students.update-status', $student) }}" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-control" style="width:auto; font-size:12.5px; padding:4px 8px; font-weight:700;">
                                    <optgroup label="── STATUS TERHITUNG (PENDIDIKAN BERJALAN) ──">
                                        <option value="Aktif" {{ $student->status == 'Aktif' ? 'selected' : '' }}>🟢 Aktif (Siap Latih — Terhitung)</option>
                                        <option value="Sakit" {{ $student->status == 'Sakit' ? 'selected' : '' }}>🟡 Sakit (Dispen Medis — Terhitung)</option>
                                        <option value="Dinas Luar" {{ $student->status == 'Dinas Luar' ? 'selected' : '' }}>🔵 Dinas Luar (Terhitung)</option>
                                    </optgroup>
                                    <optgroup label="── STATUS ARSIP (TIDAK TERHITUNG LAGI) ──">
                                        <option value="Selesai" {{ $student->status == 'Selesai' ? 'selected' : '' }}>📁 Selesai (Tamat Pendidikan — Arsip)</option>
                                        <option value="Lulus" {{ $student->status == 'Lulus' ? 'selected' : '' }}>🎓 Lulus (Alumni — Arsip)</option>
                                        <option value="DO / Dikeluarkan" {{ $student->status == 'DO / Dikeluarkan' ? 'selected' : '' }}>🔴 DO / Dikeluarkan (Arsip)</option>
                                    </optgroup>
                                </select>
                                <button type="submit" class="btn btn-outline btn-sm" style="font-size:12px; padding:4px 10px;">Simpan Status</button>
                            </form>
                        @else
                            <div style="font-weight:700; color:var(--o900); font-size:13.5px; display:inline-flex; align-items:center; gap:8px;">
                                <span>{{ $student->status }}</span>
                                <span class="badge" style="background:#F1F5F9; color:#475569; font-size:11px;">Hanya Lihat</span>
                            </div>
                        @endif
                        <small style="color:var(--muted); display:block; margin-top:5px; font-size:11.5px;">
                            @if($student->is_counted)
                                <b style="color:var(--green);">✓ Terhitung:</b> Prajurit siswa aktif menjalani program pendidikan.
                            @else
                                <b style="color:#475569;">ℹ Masuk Arsip:</b> Prajurit siswa telah selesai pendidikan dan tidak terhitung dalam kuota aktif.
                            @endif
                        </small>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- KARTU 2: DATA PRIBADI SENSITIF TERPROTEKSI SISTEM SIPANDU-WBK -->
    <div class="card" style="margin-bottom:0; border: 1.5px solid var(--gold);">
        <div class="card-header" style="background:var(--gold-bg); border-bottom:1px solid #E8D9A8;">
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:#7A5C07;">enhanced_encryption</span>
                <div>
                    <h3 class="card-title" style="color:#7A5C07; font-size:15px;">Informasi Pribadi Sensitif Terproteksi</h3>
                    <div style="font-size:11px; color:#8A680C;">Terenkripsi AES-256 pada Database & Tersamar (Masked)</div>
                </div>
            </div>
            
            <button id="btnTriggerReveal" onclick="openRevealModal()" class="btn btn-gold btn-sm">
                <span class="ms">visibility</span> Buka Data Sensitif
            </button>
        </div>

        <div class="card-body">
            <!-- NOTIFIKASI STATUS DEKRIPSI -->
            <div id="decryptedNotice" style="display:none; background:#E8F5E9; border:1px solid #A5D6A7; padding:10px 14px; border-radius:8px; margin-bottom:16px; font-size:12.5px; color:#1B5E20;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="ms" style="font-size:18px;">lock_open</span>
                    <div>
                        <b>Mode Dekripsi Terbuka:</b> Data telah didekripsi untuk sesi ini dan pencatatan audit telah direkam.
                        <div id="decryptedReasonText" style="font-style:italic; margin-top:2px;"></div>
                    </div>
                </div>
            </div>

            @php $profile = $student->personalProfile; @endphp

            <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted); width:42%;">Nomor Induk Kependudukan (NIK)</td>
                    <td style="padding:10px 0;">
                        <span id="field_nik" class="masked-pill">{{ $profile ? $profile->masked_nik : 'N/A' }}</span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Nomor Kartu Keluarga (No KK)</td>
                    <td style="padding:10px 0;">
                        <span id="field_kk" class="masked-pill">{{ $profile && $profile->family_card_number ? substr($profile->family_card_number, 0, 4) . '********' . substr($profile->family_card_number, -4) : 'N/A' }}</span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Nama Ibu Kandung</td>
                    <td style="padding:10px 0;">
                        <span id="field_mother" class="masked-pill">{{ $profile ? $profile->masked_mother_name : 'N/A' }}</span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Nama Ayah Kandung</td>
                    <td style="padding:10px 0;">
                        <span id="field_father" class="masked-pill">{{ $profile && $profile->father_name ? substr($profile->father_name, 0, 2) . '*****' : 'N/A' }}</span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Kontak Darurat</td>
                    <td style="padding:10px 0;">
                        <span id="field_emergency" class="masked-pill">{{ $profile ? $profile->masked_emergency_phone : 'N/A' }}</span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Alamat Domisili KTP</td>
                    <td style="padding:10px 0;">
                        <span id="field_address" class="masked-pill">{{ $profile && $profile->home_address ? substr($profile->home_address, 0, 15) . '... [Disamarkan]' : 'N/A' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding:14px 0 0;">
                        <div style="background:#E8F5E9; border:1px solid #C8E6C9; border-radius:10px; padding:12px 16px; display:flex; align-items:center; justify-content:space-between; gap:12px;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span class="ms" style="color:#1B5E20; font-size:22px;">medical_services</span>
                                <div>
                                    <div style="font-weight:700; color:#1B5E20; font-size:13px;">Data Kesehatan & Rekam Medis Dikelola Terpisah</div>
                                    <div style="font-size:11.5px; color:#2E7D32;">Informasi riwayat alergi, stakes keswa, dan rekam medis fisik dipisahkan pada Menu Kesehatan Serdik.</div>
                                </div>
                            </div>
                            <a href="{{ route('health.show', $student) }}" class="btn btn-sm" style="background:#1B5E20; color:#fff; text-decoration:none; white-space:nowrap;">
                                <span class="ms">arrow_forward</span> Buka Rekam Medis
                            </a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>

<!-- TABEL AUDIT TRAIL AKSES DATA PRIBADI SISWA INI -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">
                <span class="ms" style="color:var(--o700);">history_edu</span> Riwayat Audit Trail Akses Data Sensitif Serdik Ini
            </h3>
            <div style="font-size:12px; color:var(--muted); margin-top:2px;">
                Log permanen immutable (*append-only*) mencatat setiap pembukaan data pribadi sensitif sesuai prinsip akuntabilitas ZI WBK Area 5.
            </div>
        </div>
        <span class="badge badge-satdik">{{ $student->accessLogs->count() }} Kali Diakses</span>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Waktu Akses (WIB)</th>
                        <th>Personel / Operator Pengakses</th>
                        <th>Role / Otoritas</th>
                        <th>Alasan Akses Terbuka</th>
                        <th>IP Address & Host</th>
                    </tr>
                </thead>
                <tbody id="auditTrailTableBody">
                    @forelse($student->accessLogs->sortByDesc('accessed_at') as $index => $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td style="font-family:'Fira Code',monospace; font-size:12.5px; font-weight:600;">
                                {{ $log->accessed_at ? \Carbon\Carbon::parse($log->accessed_at)->format('d/m/Y H:i:s') : '-' }}
                            </td>
                            <td>
                                <b>{{ $log->accessedByUser->name ?? 'User #' . $log->user_id }}</b>
                                <div style="font-size:11.5px; color:var(--muted);">{{ $log->accessedByUser->email ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge badge-satdik">{{ $log->accessedByUser->role_code ?? 'operator' }}</span>
                            </td>
                            <td>
                                <span style="font-weight:600; color:var(--o800);">{{ $log->access_reason }}</span>
                            </td>
                            <td style="font-family:'Fira Code',monospace; font-size:12px; color:var(--muted);">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyLogNotice">
                            <td colspan="6" style="text-align:center; padding:32px; color:var(--muted);">
                                <span class="ms" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.5;">verified_user</span>
                                Belum ada riwayat pembukaan data pribadi untuk Serdik ini. Seluruh data tetap aman terenkripsi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL AUDIT TRAIL FORM -->
<div id="revealModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:10px;">
                <span class="ms" style="color:var(--gold2); font-size:24px;">lock_open</span>
                <h4 style="margin:0; font-family:'Montserrat',sans-serif; font-size:17px;">Buka Data Pribadi Sensitif</h4>
            </div>
            <button onclick="closeRevealModal()" style="background:transparent; border:none; color:#fff; cursor:pointer;">
                <span class="ms">close</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning" style="margin-bottom:16px;">
                <span class="ms" style="font-size:24px;">security</span>
                <div style="font-size:12.5px;">
                    <b>PEMBERITAHUAN KEAMANAN DATA SIPANDU-WBK:</b><br>
                    Pembukaan informasi identitas pribadi (NIK, No KK, Ibu Kandung, Rekam Medis, Rekening Bank) akan dicatat permanen dalam Audit Trail SIPANDU-WBK beserta identitas akun, alamat IP, dan waktu akses Anda.
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <label class="form-label">Siswa yang akan dibuka:</label>
                <div style="padding:10px 14px; background:var(--o50); border:1px solid var(--line); border-radius:8px; font-size:13.5px;">
                    <b>{{ $student->full_name }}</b> (NOSIK: {{ $student->nosik }}) — <i>{{ $student->satdik->name }}</i>
                </div>
            </div>

            <div class="form-group">
                <label for="accessReason" class="form-label">
                    Alasan Pembukaan Data Pribadi <span style="color:var(--red);">*</span>
                </label>
                <select id="quickReasonSelect" class="form-control" style="margin-bottom:8px;" onchange="applyQuickReason(this.value)">
                    <option value="">-- Pilih Format Alasan Resmi --</option>
                    <option value="Verifikasi keabsahan NIK dan data kependudukan Disdukcapil">Verifikasi Keabsahan NIK & Data Disdukcapil</option>
                    <option value="Konfirmasi kontak darurat orang tua / wali serdik">Konfirmasi Kontak Darurat Orang Tua / Wali Serdik</option>
                    <option value="Pemeriksaan berkas administrasi personel & ijazah serdik">Pemeriksaan Berkas Administrasi Personel & Ijazah</option>
                    <option value="Pemeriksaan integritas serdik oleh Komite Pengawas Pendidikan">Pemeriksaan Integritas Serdik Komite Pendidikan</option>
                </select>
                <textarea id="accessReason" class="form-control" rows="3" placeholder="Tuliskan alasan operasional resmi pembukaan data pribadi..."></textarea>
                <div class="form-hint">Minimal 5 karakter. Wajib mencerminkan kebutuhan dinas pendidikan militer yang sah.</div>
            </div>

            <div id="modalAlertError" style="display:none; color:var(--red); font-size:12.5px; margin-top:8px;"></div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeRevealModal()" class="btn btn-outline">Batal</button>
            <button type="button" id="btnSubmitReveal" onclick="executeReveal()" class="btn btn-gold">
                <span class="ms">lock_open</span> Dekripsi & Rekam Log
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openRevealModal() {
        document.getElementById('revealModal').classList.add('active');
        document.getElementById('accessReason').focus();
    }

    function closeRevealModal() {
        document.getElementById('revealModal').classList.remove('active');
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
        btn.innerHTML = '<span class="ms">hourglass_empty</span> Mendekripsi...';

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
            triggerBtn.innerHTML = '<span class="ms">lock_open</span> Terbuka (Audit Logged)';
            triggerBtn.classList.remove('btn-gold');
            triggerBtn.classList.add('btn-outline');

            // Prepend new row in audit log table dynamically
            const tableBody = document.getElementById('auditTrailTableBody');
            const emptyNotice = document.getElementById('emptyLogNotice');
            if (emptyNotice) { emptyNotice.remove(); }

            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID') + ' ' + now.toLocaleTimeString('id-ID');

            const newRow = document.createElement('tr');
            newRow.style.background = '#FBF6E5';
            newRow.innerHTML = `
                <td><b>BARU</b></td>
                <td style="font-family:'Fira Code',monospace; font-size:12.5px; font-weight:700; color:var(--o800);">${dateStr}</td>
                <td>
                    <b>{{ auth()->user()->name ?? 'Operator Rindam' }}</b>
                    <div style="font-size:11.5px; color:var(--muted);">{{ auth()->user()->email ?? 'op@rindam.mil.id' }}</div>
                </td>
                <td><span class="badge badge-satdik">{{ auth()->user()->role_code ?? 'pimpinan' }}</span></td>
                <td><span style="font-weight:700; color:var(--o800);">${reason}</span></td>
                <td style="font-family:'Fira Code',monospace; font-size:12px; color:var(--muted);">127.0.0.1 (Current)</td>
            `;
            tableBody.insertBefore(newRow, tableBody.firstChild);
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<span class="ms">lock_open</span> Dekripsi & Rekam Log';
            errDiv.textContent = err.message || 'Terjadi kesalahan saat otentikasi dekripsi.';
            errDiv.style.display = 'block';
        });
    }

    function setDecryptedField(elementId, value) {
        const el = document.getElementById(elementId);
        if(el) {
            el.textContent = value;
            el.style.background = '#E8F5E9';
            el.style.color = '#1B5E20';
            el.style.borderColor = '#81C784';
            el.style.fontWeight = '700';
        }
    }
</script>
@endsection
