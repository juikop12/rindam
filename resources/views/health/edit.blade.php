@extends('layouts.app')

@section('title', 'Perbarui Kesehatan Serdik: ' . $student->full_name . ' — SIPANDU-WBK')

@section('content')

<!-- BREADCRUMB -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted);">
        <a href="{{ route('health.index') }}" style="color:var(--o700); text-decoration:none; font-weight:600;">Kesehatan Serdik</a>
        <span class="ms" style="font-size:16px;">chevron_right</span>
        <a href="{{ route('health.show', $student) }}" style="color:var(--o700); text-decoration:none; font-weight:600;">{{ $student->full_name }}</a>
        <span class="ms" style="font-size:16px;">chevron_right</span>
        <span style="color:var(--text); font-weight:700;">Update Status Kesehatan</span>
    </div>
    <a href="{{ route('health.show', $student) }}" class="btn btn-outline btn-sm">
        <span class="ms">arrow_back</span> Batal & Kembali
    </a>
</div>

<!-- PATIENT HEADER BANNER -->
<div class="header-banner" style="margin-bottom:24px;">
    <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px; flex-wrap:wrap;">
            <span class="pdp-badge" style="background:#E8F5E9; color:#1B5E20; border-color:#81C784;">
                <span class="ms" style="font-size:16px;">medical_services</span> POLIKLINIK & TONKES
            </span>
            <span class="pdp-badge" style="background:rgba(255,255,255,0.15);color:#fff;border-color:rgba(255,255,255,0.25);">
                {{ $student->satdik->code }} · {{ $student->nosik }}
            </span>
            <span class="pdp-badge" style="background:rgba(201,162,39,0.2); color:var(--gold2); border-color:var(--gold);">
                <span class="ms" style="font-size:15px;">school</span> {{ $student->educationProgram->name ?? 'Program Pendidikan' }}
            </span>
            <span class="pdp-badge" style="background:rgba(255,255,255,0.15);color:#fff;border-color:rgba(255,255,255,0.25);">
                <span class="ms" style="font-size:15px;">military_tech</span> {{ $student->origin_military_unit ?? '-' }}
            </span>
            @php $u = $student->unified_status; @endphp
            <span class="badge {{ $u['badge'] }}" style="padding:4px 10px;">
                <span class="ms" style="font-size:13px;">{{ $u['icon'] }}</span> {{ $u['label'] }}
            </span>
        </div>
        <h1>Pembaruan Rekam Medis: {{ $student->full_name }}</h1>
        <p>
            Entri hasil pemeriksaan berkala, tanda vital harian, penentuan stakes kesiapan latihan, catatan alergi, dan instruksi penanganan medis oleh Tim Kesehatan Poliklinik Satdik.
        </p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-warning" style="background:#FFEBEE; border-color:#FFCDD2; color:var(--red); margin-bottom:24px;">
        <span class="ms" style="font-size:24px;">error</span>
        <div>
            <b>Terdapat kesalahan pengisian data medis:</b>
            <ul style="margin:4px 0 0 16px; padding:0; font-size:12.5px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('health.update', $student) }}" method="POST">
    @csrf
    @method('PUT')

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">

        <!-- KOLOM 1: STATUS KESIAPAN & TANDA VITAL -->
        <div class="card" style="margin-bottom:0;">
            <div class="card-header" style="background:var(--o50);">
                <h3 class="card-title">
                    <span class="ms" style="color:var(--green);">monitor_heart</span> Status Kebugaran & Tanda Vital
                </h3>
                <span class="badge badge-satdik">{{ $student->satdik->code }}</span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="daily_health_status">
                        Kondisi Fisik & Kesiapan Latihan Serdik <span style="color:var(--red);">*</span>
                    </label>
                    <select name="daily_health_status" id="daily_health_status" class="form-control" required onchange="handleHealthStatusChange(this.value)">
                        <option value="Siap Latih" {{ old('daily_health_status', $healthRecord->daily_health_status) == 'Siap Latih' ? 'selected' : '' }}>
                            🟢 Siap Latih (Kondisi Prima & Siap Latihan Penuh)
                        </option>
                        <option value="Berobat Jalan" {{ old('daily_health_status', $healthRecord->daily_health_status) == 'Berobat Jalan' ? 'selected' : '' }}>
                            🟡 Berobat Jalan (Dispensasi Lari / Latihan Berat)
                        </option>
                        <option value="Rawat Inap Poliklinik" {{ old('daily_health_status', $healthRecord->daily_health_status) == 'Rawat Inap Poliklinik' ? 'selected' : '' }}>
                            🔴 Rawat Inap Poliklinik Satdik (Bed Rest)
                        </option>
                        <option value="Rujuk Rumkit" {{ old('daily_health_status', $healthRecord->daily_health_status) == 'Rujuk Rumkit' ? 'selected' : '' }}>
                            🔵 Rujuk Rumah Sakit (Rumkit Tk. II Soedjono)
                        </option>
                    </select>
                    <div class="form-hint">Kondisi ini otomatis menyinkronkan status keaktifan prajurit siswa di Satdik.</div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label" for="blood_pressure">Tekanan Darah (mmHg)</label>
                        <input type="text" name="blood_pressure" id="blood_pressure" class="form-control" value="{{ old('blood_pressure', $healthRecord->blood_pressure) }}" placeholder="120/80">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="pulse_rate">Denyut Nadi (bpm)</label>
                        <input type="number" name="pulse_rate" id="pulse_rate" class="form-control" value="{{ old('pulse_rate', $healthRecord->pulse_rate) }}" placeholder="72">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label" for="height_cm">Tinggi Badan (cm)</label>
                        <input type="number" name="height_cm" id="height_cm" class="form-control" value="{{ old('height_cm', $healthRecord->height_cm) }}" placeholder="172">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="weight_kg">Berat Badan (kg)</label>
                        <input type="number" step="0.1" name="weight_kg" id="weight_kg" class="form-control" value="{{ old('weight_kg', $healthRecord->weight_kg) }}" placeholder="68.5">
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM 2: REKAM MEDIS & RUJUKAN -->
        <div class="card" style="margin-bottom:0;">
            <div class="card-header" style="background:var(--o50);">
                <h3 class="card-title">
                    <span class="ms" style="color:var(--o700);">clinical_notes</span> Riwayat Medis, Alergi & Rujukan
                </h3>
                <span class="badge badge-green">Poliklinik Satdik</span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="allergies">
                        Riwayat Alergi (Obat / Makanan / Debu)
                    </label>
                    <textarea name="allergies" id="allergies" class="form-control" rows="2" placeholder="Contoh: Alergi Penisilin, Alergi Makanan Laut (Udang)">{{ old('allergies', $healthRecord->allergies) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="medical_history">
                        Riwayat Sakit Berat / Cedera Terdahulu
                    </label>
                    <textarea name="medical_history" id="medical_history" class="form-control" rows="2" placeholder="Contoh: Pernah operasi apendisitis thn 2023, riwayat dislokasi engkel">{{ old('medical_history', $healthRecord->medical_history) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="psychological_record">
                        Catatan Evaluasi Psikologi / Keswa
                    </label>
                    <textarea name="psychological_record" id="psychological_record" class="form-control" rows="2" placeholder="Contoh: Stakes Keswa Bintal Kategori B, Emosi Stabil, Adaptif">{{ old('psychological_record', $healthRecord->psychological_record) }}</textarea>
                </div>

                <div class="form-group" id="groupReferral">
                    <label class="form-label" for="referral_hospital">Faskes / Rumah Sakit Rujukan</label>
                    <input type="text" name="referral_hospital" id="referral_hospital" class="form-control" value="{{ old('referral_hospital', $healthRecord->referral_hospital) }}" placeholder="Contoh: Rumkit Tk. II dr. Soedjono Magelang">
                </div>

                <div class="form-group">
                    <label class="form-label" for="doctor_notes">
                        Instruksi & Catatan Dokter Poliklinik Satdik
                    </label>
                    <textarea name="doctor_notes" id="doctor_notes" class="form-control" rows="2" placeholder="Tuliskan terapi obat, masa istirahat/dispen, jadwal kontrol poliklinik...">{{ old('doctor_notes', $healthRecord->doctor_notes) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="examined_by">Dokter / Petugas Pemeriksa</label>
                    <input type="text" name="examined_by" id="examined_by" class="form-control" value="{{ old('examined_by', $healthRecord->examined_by ?? 'dr. Kapten Ckm Hendra Gunawan, Sp.KO') }}">
                </div>
            </div>
        </div>

    </div>

    <!-- FORM FOOTER -->
    <div class="card">
        <div class="card-body" style="display:flex; align-items:center; justify-content:space-between; padding:18px 24px;">
            <div style="font-size:12.5px; color:var(--muted); display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:var(--green); font-size:20px;">verified</span>
                <span>Pembaruan status kesehatan akan langsung tersinkronisasi ke data serdik Satdik terkait.</span>
            </div>
            <div style="display:flex; gap:12px;">
                <a href="{{ route('health.show', $student) }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-gold" style="padding:10px 24px; font-size:14px;">
                    <span class="ms">save</span> Simpan Rekam Medis
                </button>
            </div>
        </div>
    </div>
</form>

@endsection

@section('scripts')
<script>
    function handleHealthStatusChange(val) {
        const refGroup = document.getElementById('groupReferral');
        if (val === 'Rujuk Rumkit') {
            refGroup.style.display = 'block';
            document.getElementById('referral_hospital').focus();
        }
    }
</script>
@endsection
