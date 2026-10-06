@extends('layouts.app')

@section('title', 'Perbarui Kesehatan Serdik: ' . $student->full_name . ' — SIPANDU-WBK')

@section('content')
<div class="px-2 py-4">

    <!-- BREADCRUMB & TOP ACTIONS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
            <a href="{{ route('health.index') }}" class="text-slate-600 hover:text-slate-900 font-semibold transition-colors">Kesehatan Serdik</a>
            <span class="ms text-slate-400 text-[16px]">chevron_right</span>
            <a href="{{ route('health.show', $student) }}" class="text-slate-600 hover:text-slate-900 font-semibold transition-colors">{{ $student->full_name }}</a>
            <span class="ms text-slate-400 text-[16px]">chevron_right</span>
            <span class="text-slate-900 font-bold">Update Status Kesehatan</span>
        </div>
        <a href="{{ route('health.show', $student) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-sm transition-colors">
            <span class="ms text-[18px]">arrow_back</span> Batal & Kembali
        </a>
    </div>

    <!-- PATIENT HEADER CARD -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $student->satdik->code }}
                    </span>
                    <span class="font-mono text-xs text-slate-500">
                        NOSIK: {{ $student->nosik }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                        {{ $student->educationProgram->name ?? 'Program Pendidikan' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                        {{ $student->origin_military_unit ?? '-' }}
                    </span>
                    @php $u = $student->unified_status; @endphp
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold {{ $u['badge'] }}">
                        <span class="ms text-[13px] mr-0.5">{{ $u['icon'] }}</span> {{ $u['label'] }}
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight mb-1">
                    Pembaruan Rekam Medis: {{ $student->full_name }}
                </h1>
                <p class="text-xs text-slate-500">
                    Entri hasil pemeriksaan berkala, tanda vital harian, penentuan stakes kesiapan latihan, catatan alergi, dan instruksi penanganan medis oleh Tim Kesehatan Poliklinik Satdik.
                </p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 flex items-start gap-3 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">
            <span class="ms text-[22px] text-rose-500 shrink-0">error</span>
            <div>
                <b class="font-semibold text-rose-900">Terdapat kesalahan pengisian data medis:</b>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-rose-700">
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            <!-- KOLOM 1: STATUS KESIAPAN & TANDA VITAL -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="ms text-emerald-600 text-[20px]">monitor_heart</span>
                        Status Kebugaran & Tanda Vital
                    </h3>
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $student->satdik->code }}
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="daily_health_status">
                            Kondisi Fisik & Kesiapan Latihan Serdik <span class="text-rose-500">*</span>
                        </label>
                        <select name="daily_health_status" id="daily_health_status" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" required onchange="handleHealthStatusChange(this.value)">
                            <option value="Siap Latih" {{ old('daily_health_status', $healthRecord->daily_health_status) == 'Siap Latih' ? 'selected' : '' }}>
                                🟢 Sehat (Kondisi Prima & Siap Latihan Penuh)
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
                        <div class="text-[11px] text-slate-400 mt-1">Kondisi ini otomatis menyinkronkan status keaktifan prajurit siswa di Satdik.</div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="blood_pressure">
                                Tekanan Darah (mmHg)
                            </label>
                            <input type="text" name="blood_pressure" id="blood_pressure" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" value="{{ old('blood_pressure', $healthRecord->blood_pressure) }}" placeholder="120/80">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="pulse_rate">
                                Denyut Nadi (bpm)
                            </label>
                            <input type="number" name="pulse_rate" id="pulse_rate" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" value="{{ old('pulse_rate', $healthRecord->pulse_rate) }}" placeholder="72">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="height_cm">
                                Tinggi Badan (cm)
                            </label>
                            <input type="number" name="height_cm" id="height_cm" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" value="{{ old('height_cm', $healthRecord->height_cm) }}" placeholder="172">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="weight_kg">
                                Berat Badan (kg)
                            </label>
                            <input type="number" step="0.1" name="weight_kg" id="weight_kg" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" value="{{ old('weight_kg', $healthRecord->weight_kg) }}" placeholder="68.5">
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM 2: REKAM MEDIS & RUJUKAN -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="ms text-slate-700 text-[20px]">clinical_notes</span>
                        Riwayat Medis, Alergi & Rujukan
                    </h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Poliklinik Satdik
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="allergies">
                            Riwayat Alergi (Obat / Makanan / Debu)
                        </label>
                        <textarea name="allergies" id="allergies" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" rows="2" placeholder="Contoh: Alergi Penisilin, Alergi Makanan Laut (Udang)">{{ old('allergies', $healthRecord->allergies) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="medical_history">
                            Riwayat Sakit Berat / Cedera Terdahulu
                        </label>
                        <textarea name="medical_history" id="medical_history" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" rows="2" placeholder="Contoh: Pernah operasi apendisitis thn 2023, riwayat dislokasi engkel">{{ old('medical_history', $healthRecord->medical_history) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="psychological_record">
                            Catatan Evaluasi Psikologi / Keswa
                        </label>
                        <textarea name="psychological_record" id="psychological_record" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" rows="2" placeholder="Contoh: Stakes Keswa Bintal Kategori B, Emosi Stabil, Adaptif">{{ old('psychological_record', $healthRecord->psychological_record) }}</textarea>
                    </div>

                    <div id="groupReferral">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="referral_hospital">
                            Faskes / Rumah Sakit Rujukan
                        </label>
                        <input type="text" name="referral_hospital" id="referral_hospital" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" value="{{ old('referral_hospital', $healthRecord->referral_hospital) }}" placeholder="Contoh: Rumkit Tk. II dr. Soedjono Magelang">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="doctor_notes">
                            Instruksi & Catatan Dokter Poliklinik Satdik
                        </label>
                        <textarea name="doctor_notes" id="doctor_notes" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" rows="2" placeholder="Tuliskan terapi obat, masa istirahat/dispen, jadwal kontrol poliklinik...">{{ old('doctor_notes', $healthRecord->doctor_notes) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="examined_by">
                            Dokter / Petugas Pemeriksa
                        </label>
                        <input type="text" name="examined_by" id="examined_by" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900" value="{{ old('examined_by', $healthRecord->examined_by ?? 'dr. Kapten Ckm Hendra Gunawan, Sp.KO') }}">
                    </div>
                </div>
            </div>

        </div>

        <!-- FORM FOOTER -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
            <div class="text-xs text-slate-500 flex items-center gap-2">
                <span class="ms text-emerald-600 text-[20px]">verified</span>
                <span>Pembaruan status kesehatan akan langsung tersinkronisasi ke data serdik Satdik terkait.</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('health.show', $student) }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors">
                    <span class="ms text-[16px]">save</span> Simpan Rekam Medis
                </button>
            </div>
        </div>
    </form>

</div>
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
