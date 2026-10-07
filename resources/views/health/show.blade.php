@extends('layouts.app')

@section('title', 'Rekam Medis: ' . $student->full_name . ' — SIPANDU')

@section('content')
<div class="px-2 py-4">

    <!-- BREADCRUMB & TOP ACTIONS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
            <a href="{{ route('health.index') }}" class="text-slate-600 hover:text-slate-900 font-semibold transition-colors">Kesehatan Serdik</a>
            <span class="ms text-slate-400 text-[16px]">chevron_right</span>
            <a href="{{ route('health.index', ['satdik_id' => $student->satdik_id]) }}" class="text-slate-600 hover:text-slate-900 font-semibold transition-colors">{{ $student->satdik->code }}</a>
            <span class="ms text-slate-400 text-[16px]">chevron_right</span>
            <span class="text-slate-900 font-bold">Rekam Medis: {{ $student->nosik }}</span>
        </div>
        
        <div class="flex items-center gap-2.5">
            <a href="{{ route('health.export-student-pdf', $student) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                <span class="ms text-[18px]">print</span> Cetak Lembar Medis (PDF)
            </a>
            <a href="{{ route('students.show', $student) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                <span class="ms text-[18px]">badge</span> Lihat Data Pribadi
            </a>
            @if(auth()->user()?->canModifyData())
            <a href="{{ route('health.edit', $student) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors">
                <span class="ms text-[18px]">edit</span> Perbarui Status Kesehatan
            </a>
            @endif
        </div>
    </div>

    <!-- PATIENT HEADER CARD -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-start sm:items-center gap-4">
                <!-- Medical Avatar / Icon -->
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                    <span class="ms text-[28px]">medical_information</span>
                </div>
                
                <div>
                    <!-- Badges Row -->
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-700">
                            {{ $student->satdik->code }}
                        </span>
                        <span class="font-mono text-xs text-slate-500">
                            NOSIS: {{ $student->nosik }}
                        </span>
                        
                        @php $u = $student->unified_status; @endphp
                        @php
                            $bgClass = 'bg-slate-100 text-slate-700';
                            if($student->status === 'Aktif') $bgClass = 'bg-indigo-50 text-indigo-700';
                            if($student->status === 'Sakit') $bgClass = 'bg-rose-50 text-rose-700';
                            if(in_array($student->status, ['Lulus', 'Selesai'])) $bgClass = 'bg-emerald-50 text-emerald-700';
                            if(str_contains($student->status, 'DO')) $bgClass = 'bg-amber-50 text-amber-700';
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold {{ $bgClass }}" title="{{ $student->is_counted ? 'Terhitung dalam Kuota Aktif' : 'Arsip (Tidak Terhitung)' }}">
                            <span class="ms text-[13px] mr-0.5">{{ $u['icon'] }}</span> {{ $u['label'] }}
                        </span>

                        @if($healthRecord->daily_health_status == 'Siap Latih')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700">
                                <span class="ms text-[14px]">check_circle</span> Sehat Penuh
                            </span>
                        @elseif($healthRecord->daily_health_status == 'Berobat Jalan')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-700">
                                <span class="ms text-[14px]">healing</span> Berobat Jalan / Dispen
                            </span>
                        @elseif($healthRecord->daily_health_status == 'Rawat Inap Poliklinik')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-700">
                                <span class="ms text-[14px]">local_hospital</span> Rawat Inap Poliklinik
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700">
                                <span class="ms text-[14px]">emergency</span> Rujuk Rumah Sakit
                            </span>
                        @endif
                    </div>
                    
                    <!-- Patient Name -->
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight mb-2">
                        {{ $student->full_name }}
                    </h1>
                    
                    <!-- Meta info line -->
                    <div class="text-xs text-slate-500 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span>Program: <b class="text-slate-800">{{ $student->educationProgram->name ?? 'Program Pendidikan' }}</b></span>
                        <span class="text-slate-300">•</span>
                        <span>Kodam/Kodim Asal: <b class="text-slate-800">{{ $student->origin_military_unit ?? '-' }}</b></span>
                        <span class="text-slate-300">•</span>
                        <span>Pangkat: <b class="text-slate-800">{{ $student->student_rank }}</b></span>
                        <span class="text-slate-300">•</span>
                        <span>Satdik: <b class="text-slate-800">{{ $student->satdik->name }}</b></span>
                        <span class="text-slate-300">•</span>
                        <span>Peleton: <b class="text-slate-800">{{ $student->classroom->name ?? ($student->company ? $student->company . ' ' . $student->platoon : 'Belum Ditentukan') }}</b></span>
                    </div>
                </div>
            </div>

            <!-- Examination Timestamp on the right -->
            <div class="lg:text-right shrink-0 lg:border-l lg:border-slate-100 lg:pl-6">
                <div class="text-xs text-slate-400 font-medium">Pemeriksaan Terakhir:</div>
                <div class="text-sm font-bold text-slate-900 mt-0.5">
                    {{ $healthRecord->last_examined_at ? $healthRecord->last_examined_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Belum Pernah' }}
                </div>
                <div class="text-xs text-slate-500 mt-0.5">
                    Oleh: <span class="font-semibold text-slate-700">{{ $healthRecord->examined_by ?? 'Dokter Satdik' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2 MAIN CARDS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- KARTU 1: TANDA VITAL & FISIK -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="ms text-emerald-600 text-[20px]">vital_signs</span>
                        Tanda Vital & Antropometri
                    </h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Fisik Standar TNI AD
                    </span>
                </div>

                <!-- 3 Top Metric Tiles -->
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-center">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tekanan Darah</div>
                        <div class="text-xl font-bold text-slate-900 mt-1">
                            {{ $healthRecord->blood_pressure ?? '120/80' }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5">mmHg</div>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-center">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Denyut Nadi</div>
                        <div class="text-xl font-bold text-emerald-600 mt-1">
                            {{ $healthRecord->pulse_rate ?? '72' }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5">bpm (Istirahat)</div>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-center">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Indeks Massa (BMI)</div>
                        <div class="text-xl font-bold text-slate-900 mt-1">
                            {{ $healthRecord->bmi ?? '22.0' }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Ideal / Proporsional</div>
                    </div>
                </div>

                <!-- Detail List -->
                <div class="divide-y divide-slate-100 text-xs">
                    <div class="py-2.5 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Tinggi Badan</span>
                        <span class="font-bold text-slate-800">{{ $healthRecord->height_cm ?? '-' }} cm</span>
                    </div>
                    <div class="py-2.5 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Berat Badan</span>
                        <span class="font-bold text-slate-800">{{ $healthRecord->weight_kg ?? '-' }} kg</span>
                    </div>
                    <div class="py-2.5 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Golongan Darah</span>
                        <span class="font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded">{{ $student->blood_type ?? '-' }}</span>
                    </div>
                    <div class="py-2.5 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Kondisi Fisik</span>
                        <div>
                            @php $b = $healthRecord->status_badge; @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-semibold {{ $b['class'] }}">
                                <span class="ms text-[13px]">{{ $b['icon'] }}</span> {{ $b['label'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KARTU 2: PELAYANAN & TINDAKAN POLIKLINIK SATDIK -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="ms text-emerald-600 text-[20px]">local_hospital</span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 leading-tight">Pelayanan & Perawatan Medis Satdik</h3>
                            <div class="text-[11px] text-slate-400">Status Rawat Inap & Rujukan Kesehatan Siswa</div>
                        </div>
                    </div>
                    @if(auth()->user()?->canModifyData())
                    <a href="{{ route('health.edit', $student) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">
                        <span class="ms text-[15px]">edit</span> Update Perawatan
                    </a>
                    @endif
                </div>

                <div class="divide-y divide-slate-100 text-xs">
                    <div class="py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Status Perawatan</span>
                        <div>
                            @php $u = $student->unified_status; @endphp
                            <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $u['badge'] }}">
                                <span class="ms text-[13px] mr-0.5">{{ $u['icon'] }}</span> {{ $u['label'] }}
                            </span>
                        </div>
                    </div>
                    <div class="py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Fasilitas Kesehatan Satdik</span>
                        <span class="font-semibold text-slate-800">Poliklinik Kesehatan Rindam</span>
                    </div>
                    <div class="py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Rumah Sakit Rujukan</span>
                        <span class="font-semibold text-slate-800 text-right max-w-[280px]">
                            {{ $healthRecord->referral_hospital ?: 'Rumkit Tk. II dr. Soedjono Magelang (Standby)' }}
                        </span>
                    </div>
                    <div class="py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Tanggal Masuk Poliklinik</span>
                        <span class="font-semibold text-slate-800">
                            {{ $healthRecord->polyclinic_admission_date ? \Carbon\Carbon::parse($healthRecord->polyclinic_admission_date)->translatedFormat('d F Y') : 'Tidak sedang rawat inap' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- KARTU 3: CATATAN ALERGI, REKAM MEDIS & KESWA -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-8">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="ms text-slate-700 text-[20px]">clinical_notes</span>
                Riwayat Alergi, Rekam Medis Khusus & Evaluasi Keswa
            </h3>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                Poliklinik Satdik
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Box 1: Alergi -->
            <div class="bg-amber-50/60 border border-amber-200 rounded-xl p-4">
                <div class="flex items-center gap-1.5 font-bold text-xs text-amber-900 uppercase tracking-wider mb-2">
                    <span class="ms text-amber-600 text-[18px]">warning</span> RIWAYAT ALERGI OBAT / MAKANAN
                </div>
                <div class="text-xs text-amber-900/90 leading-relaxed">
                    {{ $healthRecord->allergies ?: 'Tidak ada riwayat alergi yang dilaporkan.' }}
                </div>
            </div>

            <!-- Box 2: Riwayat Sakit & Cedera -->
            <div class="bg-blue-50/60 border border-blue-200 rounded-xl p-4">
                <div class="flex items-center gap-1.5 font-bold text-xs text-blue-900 uppercase tracking-wider mb-2">
                    <span class="ms text-blue-600 text-[18px]">history_edu</span> RIWAYAT SAKIT & CEDERA
                </div>
                <div class="text-xs text-blue-900/90 leading-relaxed">
                    {{ $healthRecord->medical_history ?: 'Tidak memiliki catatan sakit keras / operasi terdahulu.' }}
                </div>
            </div>

            <!-- Box 3: Evaluasi Keswa & Bintal -->
            <div class="bg-purple-50/60 border border-purple-200 rounded-xl p-4">
                <div class="flex items-center gap-1.5 font-bold text-xs text-purple-900 uppercase tracking-wider mb-2">
                    <span class="ms text-purple-600 text-[18px]">psychology</span> EVALUASI KESWA & BINTAL
                </div>
                <div class="text-xs text-purple-900/90 leading-relaxed">
                    {{ $healthRecord->psychological_record ?: 'Stakes Keswa Bintal Baik (B), mental stabil.' }}
                </div>
            </div>
        </div>

        @if($healthRecord->doctor_notes)
            <div class="mt-5 bg-slate-50 border border-slate-200 rounded-xl p-4">
                <div class="font-bold text-xs text-slate-900 mb-1 flex items-center gap-1.5">
                    <span class="ms text-slate-600 text-[18px]">stethoscope</span> Catatan Instruksi Dokter Poliklinik Satdik:
                </div>
                <div class="text-xs text-slate-700 leading-relaxed">
                    {{ $healthRecord->doctor_notes }}
                </div>
                <div class="mt-2 text-[11px] text-slate-400">
                    Penanggung Jawab Medis: <b class="text-slate-700">{{ $healthRecord->examined_by }}</b>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
