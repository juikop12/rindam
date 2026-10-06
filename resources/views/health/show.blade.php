@extends('layouts.app')

@section('title', 'Rekam Medis: ' . $student->full_name . ' — SIPANDU-WBK')

@section('content')

<!-- BREADCRUMB -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted);">
        <a href="{{ route('health.index') }}" style="color:var(--o700); text-decoration:none; font-weight:600;">Kesehatan Serdik</a>
        <span class="ms" style="font-size:16px;">chevron_right</span>
        <a href="{{ route('health.index', ['satdik_id' => $student->satdik_id]) }}" style="color:var(--o700); text-decoration:none; font-weight:600;">{{ $student->satdik->code }}</a>
        <span class="ms" style="font-size:16px;">chevron_right</span>
        <span style="color:var(--text); font-weight:700;">Rekam Medis: {{ $student->nosik }}</span>
    </div>
    <div style="display:flex; gap:10px;">
        <button onclick="window.print()" class="btn btn-outline btn-sm">
            <span class="ms">print</span> Cetak Lembar Medis
        </button>
        <a href="{{ route('students.show', $student) }}" class="btn btn-outline btn-sm">
            <span class="ms">badge</span> Lihat Data Pribadi
        </a>
        <a href="{{ route('health.edit', $student) }}" class="btn btn-gold btn-sm">
            <span class="ms">edit</span> Perbarui Status Kesehatan
        </a>
    </div>
</div>

<!-- PATIENT HEADER CARD -->
<div class="card" style="margin-bottom:24px; border-left:5px solid #2E7D32;">
    <div class="card-body" style="padding:24px 28px;">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:20px;">
            <div style="display:flex; align-items:center; gap:20px;">
                <div style="width:72px; height:72px; border-radius:16px; background:linear-gradient(135deg, #1B5E20, #388E3C); color:#fff; display:grid; place-items:center; font-size:30px; font-weight:800;">
                    <span class="ms">medical_information</span>
                </div>
                <div>
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px; flex-wrap:wrap;">
                        <span class="badge badge-satdik">{{ $student->satdik->code }}</span>
                        <span style="font-family:'Fira Code',monospace; font-size:12px; color:var(--muted);">NOSIK: {{ $student->nosik }}</span>
                        
                        @php $u = $student->unified_status; @endphp
                        <span class="badge {{ $u['badge'] }}" title="{{ $student->is_counted ? 'Terhitung dalam Kuota Aktif' : 'Arsip (Tidak Terhitung)' }}">
                            <span class="ms" style="font-size:13px;">{{ $u['icon'] }}</span> {{ $u['label'] }}
                        </span>

                        @if($healthRecord->daily_health_status == 'Siap Latih')
                            <span class="badge badge-green"><span class="ms" style="font-size:13px;">check_circle</span> Siap Latih Penuh</span>
                        @elseif($healthRecord->daily_health_status == 'Berobat Jalan')
                            <span class="badge badge-amber"><span class="ms" style="font-size:13px;">healing</span> Berobat Jalan / Dispen</span>
                        @elseif($healthRecord->daily_health_status == 'Rawat Inap Poliklinik')
                            <span class="badge badge-red"><span class="ms" style="font-size:13px;">local_hospital</span> Rawat Inap Poliklinik</span>
                        @else
                            <span class="badge badge-blue"><span class="ms" style="font-size:13px;">emergency</span> Rujuk Rumah Sakit</span>
                        @endif
                    </div>
                    <h1 style="margin:0 0 6px; font-family:'Montserrat',sans-serif; font-size:22px; font-weight:800; color:var(--o900);">
                        {{ $student->full_name }}
                    </h1>
                    <div style="font-size:13px; color:var(--muted); display:flex; gap:16px; flex-wrap:wrap;">
                        <span>Program: <b style="color:var(--o800);">{{ $student->educationProgram->name ?? 'Program Pendidikan' }}</b></span>
                        <span>•</span>
                        <span>Kodam/Kodim Asal: <b style="color:var(--o800);">{{ $student->origin_military_unit ?? '-' }}</b></span>
                        <span>•</span>
                        <span>Pangkat: <b>{{ $student->student_rank }}</b></span>
                        <span>•</span>
                        <span>Satdik: <b>{{ $student->satdik->name }}</b></span>
                        <span>•</span>
                        <span>Peleton: <b>{{ $student->classroom->name ?? 'Belum Ditentukan' }}</b></span>
                    </div>
                </div>
            </div>

            <div style="text-align:right;">
                <div style="font-size:12px; color:var(--muted);">Pemeriksaan Terakhir:</div>
                <div style="font-weight:700; font-size:14px; color:var(--o800);">
                    {{ $healthRecord->last_examined_at ? $healthRecord->last_examined_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Belum Pernah' }}
                </div>
                <div style="font-size:11.5px; color:var(--muted); margin-top:2px;">
                    Oleh: {{ $healthRecord->examined_by ?? 'Dokter Satdik' }}
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">

    <!-- KARTU 1: TANDA VITAL & FISIK -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-header" style="background:var(--o50);">
            <h3 class="card-title">
                <span class="ms" style="color:var(--green);">vital_signs</span> Tanda Vital & Antropometri
            </h3>
            <span class="badge badge-green">Fisik Standar TNI AD</span>
        </div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; margin-bottom:16px;">
                <div style="background:#F9FAF7; border:1px solid var(--line); border-radius:10px; padding:12px; text-align:center;">
                    <div style="font-size:11px; color:var(--muted); text-transform:uppercase;">Tekanan Darah</div>
                    <div style="font-family:'Montserrat',sans-serif; font-size:18px; font-weight:800; color:var(--o800); margin-top:2px;">
                        {{ $healthRecord->blood_pressure ?? '120/80' }}
                    </div>
                    <div style="font-size:10px; color:var(--muted);">mmHg</div>
                </div>

                <div style="background:#F9FAF7; border:1px solid var(--line); border-radius:10px; padding:12px; text-align:center;">
                    <div style="font-size:11px; color:var(--muted); text-transform:uppercase;">Denyut Nadi</div>
                    <div style="font-family:'Montserrat',sans-serif; font-size:18px; font-weight:800; color:var(--green); margin-top:2px;">
                        {{ $healthRecord->pulse_rate ?? '72' }}
                    </div>
                    <div style="font-size:10px; color:var(--muted);">bpm (Istirahat)</div>
                </div>

                <div style="background:#F9FAF7; border:1px solid var(--line); border-radius:10px; padding:12px; text-align:center;">
                    <div style="font-size:11px; color:var(--muted); text-transform:uppercase;">Indeks Massa (BMI)</div>
                    <div style="font-family:'Montserrat',sans-serif; font-size:18px; font-weight:800; color:var(--o800); margin-top:2px;">
                        {{ $healthRecord->bmi ?? '22.0' }}
                    </div>
                    <div style="font-size:10px; color:var(--muted);">Ideal / Proporsional</div>
                </div>
            </div>

            <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:9px 0; color:var(--muted); width:40%;">Tinggi Badan</td>
                    <td style="padding:9px 0; font-weight:700;">{{ $healthRecord->height_cm ?? '-' }} cm</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:9px 0; color:var(--muted);">Berat Badan</td>
                    <td style="padding:9px 0; font-weight:700;">{{ $healthRecord->weight_kg ?? '-' }} kg</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:9px 0; color:var(--muted);">Golongan Darah</td>
                    <td style="padding:9px 0; font-weight:800; color:var(--red);">{{ $student->blood_type ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:9px 0; color:var(--muted);">Kondisi Fisik</td>
                    <td style="padding:9px 0; font-weight:700;">
                        @php $b = $healthRecord->status_badge; @endphp
                        <span class="badge {{ $b['class'] }}">
                            <span class="ms" style="font-size:13px;">{{ $b['icon'] }}</span> {{ $b['label'] }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- KARTU 2: PELAYANAN & TINDAKAN POLIKLINIK SATDIK -->
    <div class="card" style="margin-bottom:0; border:1.5px solid #81C784;">
        <div class="card-header" style="background:#E8F5E9; border-bottom:1px solid #C8E6C9;">
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:#1B5E20;">local_hospital</span>
                <div>
                    <h3 class="card-title" style="color:#1B5E20; font-size:15px;">Pelayanan & Perawatan Medis Satdik</h3>
                    <div style="font-size:11px; color:#2E7D32;">Status Rawat Inap & Rujukan Kesehatan Siswa</div>
                </div>
            </div>
            <a href="{{ route('health.edit', $student) }}" class="btn btn-outline btn-sm" style="background:#fff;">
                <span class="ms">edit</span> Update Perawatan
            </a>
        </div>
        <div class="card-body">
            <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted); width:45%;">Status Perawatan</td>
                    <td style="padding:10px 0;">
                        @php $u = $student->unified_status; @endphp
                        <span class="badge {{ $u['badge'] }}">
                            <span class="ms" style="font-size:13px;">{{ $u['icon'] }}</span> {{ $u['label'] }}
                        </span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Fasilitas Kesehatan Satdik</td>
                    <td style="padding:10px 0; font-weight:600;">Poliklinik Kesehatan Rindam</td>
                </tr>
                <tr style="border-bottom:1px solid #EEF2EB;">
                    <td style="padding:10px 0; color:var(--muted);">Rumah Sakit Rujukan</td>
                    <td style="padding:10px 0; font-weight:600;">
                        {{ $healthRecord->referral_hospital ?: 'Rumkit Tk. II dr. Soedjono Magelang (Standby)' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:10px 0; color:var(--muted);">Tanggal Masuk Poliklinik</td>
                    <td style="padding:10px 0; font-weight:600;">
                        {{ $healthRecord->polyclinic_admission_date ? \Carbon\Carbon::parse($healthRecord->polyclinic_admission_date)->translatedFormat('d F Y') : 'Tidak sedang rawat inap' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>

<!-- KARTU 3: CATATAN ALERGI, REKAM MEDIS & KESWA -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <span class="ms" style="color:var(--o700);">clinical_notes</span> Riwayat Alergi, Rekam Medis Khusus & Evaluasi Keswa
        </h3>
        <span class="badge badge-satdik">Poliklinik Satdik</span>
    </div>
    <div class="card-body">
        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:18px;">
            <div style="background:#FFF9C4; border:1px solid #FFF176; border-radius:12px; padding:16px;">
                <div style="display:flex; align-items:center; gap:6px; font-weight:800; font-size:13px; color:#F57F17; margin-bottom:8px;">
                    <span class="ms">warning</span> RIWAYAT ALERGI OBAT / MAKANAN
                </div>
                <div style="font-size:13px; color:#5D4037; line-height:1.4;">
                    {{ $healthRecord->allergies ?: 'Tidak ada riwayat alergi yang dilaporkan.' }}
                </div>
            </div>

            <div style="background:#E3F2FD; border:1px solid #90CAF9; border-radius:12px; padding:16px;">
                <div style="display:flex; align-items:center; gap:6px; font-weight:800; font-size:13px; color:#1565C0; margin-bottom:8px;">
                    <span class="ms">history_edu</span> RIWAYAT SAKIT & CEDERA
                </div>
                <div style="font-size:13px; color:#1A237E; line-height:1.4;">
                    {{ $healthRecord->medical_history ?: 'Tidak memiliki catatan sakit keras / operasi terdahulu.' }}
                </div>
            </div>

            <div style="background:#F3E5F5; border:1px solid #CE93D8; border-radius:12px; padding:16px;">
                <div style="display:flex; align-items:center; gap:6px; font-weight:800; font-size:13px; color:#6A1B9A; margin-bottom:8px;">
                    <span class="ms">psychology</span> EVALUASI KESWA & BINTAL
                </div>
                <div style="font-size:13px; color:#4A148C; line-height:1.4;">
                    {{ $healthRecord->psychological_record ?: 'Stakes Keswa Bintal Baik (B), mental stabil.' }}
                </div>
            </div>
        </div>

        @if($healthRecord->doctor_notes)
            <div style="margin-top:20px; background:#F7F9F4; border:1px solid var(--line); border-radius:12px; padding:16px;">
                <div style="font-weight:800; font-size:13px; color:var(--o800); margin-bottom:4px; display:flex; align-items:center; gap:6px;">
                    <span class="ms">stethoscope</span> Catatan Instruksi Dokter Poliklinik Satdik:
                </div>
                <div style="font-size:13.5px; color:var(--text); line-height:1.5;">
                    {{ $healthRecord->doctor_notes }}
                </div>
                <div style="margin-top:8px; font-size:12px; color:var(--muted);">
                    Penanggung Jawab Medis: <b>{{ $healthRecord->examined_by }}</b>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
