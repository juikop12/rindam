@extends('layouts.app')

@section('title', 'Pusat Komando & Dashboard Eksekutif Data Siswa & Kesehatan')

@section('content')
<!-- KONTEN DASHBOARD (SaaS Style, Clean, Minimalist with Tailwind CSS) -->
<div class="px-2 py-4">

    <!-- HEADER TITLE -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pusat Komando & Dashboard Eksekutif</h1>
            <p class="text-sm text-slate-500 mt-1">Data Siswa & Kesehatan &mdash; {{ auth()->user()?->isOperator() && auth()->user()?->satdik ? (auth()->user()->satdik->code . ' (' . auth()->user()->satdik->name . ')') : 'Rindam III/Siliwangi' }}</p>
        </div>
        
        <!-- Waktu Sistem -->
        <div class="flex items-center gap-2 bg-white border border-slate-200 px-4 py-2 rounded-lg shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-sm font-semibold text-slate-700">{{ now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    <!-- TABS FILTER SATDIK -->
    @if(auth()->check() && !auth()->user()->isPimpinan() && auth()->user()->satdik_id)
        <!-- OPERATOR VIEW: HANYA TAMPILKAN SATDIK OPERATOR (TERKUNCI & TIDAK ADA SATDIK LAIN) -->
        <div class="flex items-center gap-3 mb-6">
            <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-bold bg-slate-900 text-white border border-slate-800 shadow-sm">
                <span class="ms text-[18px] text-amber-400">lock</span>
                <span>Satdik: {{ auth()->user()->satdik?->code }} &mdash; {{ auth()->user()->satdik?->name }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                    Wewenang Satuan Anda (Terkunci)
                </span>
            </div>
        </div>
    @else
        <!-- PIMPINAN / SUPER ADMIN: BISA MEMILIH SELURUH SATDIK -->
        <div class="mb-6">
            <!-- Mobile View (Dropdown) -->
            <div class="block md:hidden">
                <select onchange="window.location.href=this.value" class="w-full bg-white border border-slate-300 text-slate-700 text-sm rounded-lg focus:ring-slate-900 focus:border-slate-900 block p-2.5">
                    <option value="{{ route('dashboard') }}" {{ is_null($selectedSatdik) ? 'selected' : '' }}>Semua Satdik ({{ \App\Models\Student::withoutGlobalScopes()->count() }})</option>
                    @foreach($satdiks as $s)
                        <option value="{{ route('dashboard', ['satdik_id' => $s->id]) }}" {{ $selectedSatdik?->id === $s->id ? 'selected' : '' }}>{{ $s->code }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Desktop View (Buttons) -->
            <div class="hidden md:flex flex-wrap items-center gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors border {{ is_null($selectedSatdik) ? 'bg-slate-100 text-slate-900 border-slate-300 shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    <span class="ms text-[18px]">list</span>
                    Semua Satdik
                    <span class="px-2 py-0.5 rounded text-xs bg-slate-200 text-slate-700">{{ \App\Models\Student::withoutGlobalScopes()->count() }}</span>
                </a>
                @foreach($satdiks as $s)
                    <a href="{{ route('dashboard', ['satdik_id' => $s->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors border {{ $selectedSatdik?->id === $s->id ? 'bg-slate-100 text-slate-900 border-slate-300 shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                        <span class="ms text-[18px]">filter_list</span>
                        {{ $s->code }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4 STAT CARDS (Clean Minimalist) -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 md:gap-5 mb-8">
        <!-- Kekuatan Serdik -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 md:p-5 shadow-sm flex flex-col justify-between">
            <div class="text-xs md:text-sm font-medium text-slate-500 mb-2 md:mb-4 flex items-center gap-2">
                Total Siswa Aktif 
                <span class="ms text-sm text-slate-400 hidden sm:inline">info</span>
            </div>
            <div>
                <div class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">{{ number_format($countedActiveStudents) }}</div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 text-[10px] md:text-xs text-slate-500">
                    <span class="bg-emerald-100 text-emerald-700 px-1.5 md:px-2 py-0.5 rounded font-semibold w-fit">+ {{ $activeStudents }} sehat</span>
                    <span class="hidden sm:inline">&bull; {{ $sickStudents }} dispen</span>
                </div>
            </div>
        </div>

        <!-- Kesiapan Latihan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 md:p-5 shadow-sm flex flex-col justify-between">
            <div class="text-xs md:text-sm font-medium text-slate-500 mb-2 md:mb-4 flex items-center gap-2">
                Kesiapan Latihan 
                <span class="ms text-sm text-slate-400 hidden sm:inline">fitness_center</span>
            </div>
            <div>
                <div class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">{{ $siapLatihPercent }}%</div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 text-[10px] md:text-xs text-slate-500">
                    <span class="bg-emerald-100 text-emerald-700 px-1.5 md:px-2 py-0.5 rounded font-semibold w-fit">+ {{ $siapLatih }} siap penuh</span>
                    <span class="hidden sm:inline">dari {{ $totalHealth }}</span>
                </div>
            </div>
        </div>

        <!-- Perawatan Medis -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 md:p-5 shadow-sm flex flex-col justify-between">
            <div class="text-xs md:text-sm font-medium text-slate-500 mb-2 md:mb-4 flex items-center gap-2">
                Rawat Poliklinik 
                <span class="ms text-sm text-slate-400 hidden sm:inline">medical_services</span>
            </div>
            <div>
                <div class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">{{ number_format($perawatanCount) }}</div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 text-[10px] md:text-xs text-slate-500">
                    <span class="bg-amber-100 text-amber-700 px-1.5 md:px-2 py-0.5 rounded font-semibold w-fit">{{ $berobatJalan }} rawat jalan</span>
                    <span class="hidden sm:inline">&bull; {{ $rawatPoliklinik }} inap</span>
                </div>
            </div>
        </div>

        <!-- Rujuk Rumkit -->
        <a href="{{ route('health.index', ['status' => 'Rujuk Rumkit']) }}" class="bg-white border border-slate-200 rounded-xl p-4 md:p-5 shadow-sm flex flex-col justify-between hover:bg-slate-50 transition-colors">
            <div class="text-xs md:text-sm font-medium text-slate-500 mb-2 md:mb-4 flex items-center gap-2">
                Rujuk Rumah Sakit 
                <span class="ms text-sm text-slate-400 hidden sm:inline">local_hospital</span>
            </div>
            <div>
                <div class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">{{ number_format($rujukRumkit) }}</div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 text-[10px] md:text-xs text-slate-500">
                    <span class="bg-blue-100 text-blue-700 px-1.5 md:px-2 py-0.5 rounded font-semibold w-fit">rujukan lanjut</span>
                </div>
            </div>
        </a>
    </div>

    <!-- CHARTS SECTION (6 Interactive Charts) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        
        <!-- Chart 1: Status Kedudukan Siswa -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="ms text-slate-400">donut_large</span> Status Kedudukan Serdik
            </h3>
            <div class="relative h-56">
                <canvas id="chartStudentStatus"></canvas>
            </div>
        </div>

        <!-- Chart 2: Status Kesehatan Harian -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="ms text-slate-400">monitor_heart</span> Status Kesehatan Harian
            </h3>
            <div class="relative h-56">
                <canvas id="chartHealthStatus"></canvas>
            </div>
        </div>

        <!-- Chart 3: Distribusi Stakes (Kondisi Militer) -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="ms text-slate-400">military_tech</span> Distribusi Stakes Medis
            </h3>
            <div class="relative h-56">
                <canvas id="chartStakes"></canvas>
            </div>
        </div>

        <!-- Chart 4: Perbandingan Kekuatan per Satdik -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="ms text-slate-400">bar_chart</span> Kekuatan Serdik per Satdik
            </h3>
            <div class="relative h-56">
                <canvas id="chartSatdik"></canvas>
            </div>
        </div>

        <!-- Chart 5: Distribusi Golongan Darah -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="ms text-slate-400">bloodtype</span> Distribusi Golongan Darah
            </h3>
            <div class="relative h-56">
                <canvas id="chartBloodType"></canvas>
            </div>
        </div>

        <!-- Chart 6: Distribusi Agama -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="ms text-slate-400">diversity_3</span> Distribusi Agama
            </h3>
            <div class="relative h-56">
                <canvas id="chartReligion"></canvas>
            </div>
        </div>

    </div>

    <!-- MAIN TABLE SECTION -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-8 overflow-hidden">
        <!-- Table Header & Actions -->
        <div class="px-5 py-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Daftar Serdik Terkini</h2>
                <p class="text-sm text-slate-500 mt-0.5">Catatan mutasi data siswa dan status serdik (Real-Time Feed)</p>
            </div>
            <div class="flex gap-2">
                @if(auth()->check() && !auth()->user()->isPimpinan())
                <a href="{{ route('students.index', ['action' => 'create']) }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    <span class="ms text-[18px]">add</span> Input Data Baru
                </a>
                @endif
                <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    <span class="ms text-[18px]">folder_open</span> Lihat Seluruh Data
                </a>
            </div>
        </div>

        <!-- MOBILE CARDS (Visible only on small screens) -->
        <div class="block lg:hidden">
            @forelse($recentStudents as $student)
                <div class="p-4 border-b border-slate-200 hover:bg-slate-50 transition-colors last:border-b-0">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="font-bold text-slate-900 text-base">{{ $student->full_name }}</div>
                            <div class="text-xs font-mono text-slate-500 mt-1">{{ $student->nosik }}</div>
                        </div>
                        @php
                            $bgClass = 'bg-slate-100 text-slate-700';
                            if($student->status === 'Aktif') $bgClass = 'bg-indigo-50 text-indigo-700';
                            if($student->status === 'Sakit') $bgClass = 'bg-rose-50 text-rose-700';
                            if(in_array($student->status, ['Lulus', 'Selesai'])) $bgClass = 'bg-emerald-50 text-emerald-700';
                            if(str_contains($student->status, 'DO')) $bgClass = 'bg-amber-50 text-amber-700';
                        @endphp
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide {{ $bgClass }}">
                            {{ $student->status }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Satdik & Program</div>
                            <div class="font-semibold text-slate-800 mt-0.5">{{ $student->satdik?->code }}</div>
                            <div class="text-xs text-slate-600 truncate" title="{{ $student->educationProgram?->name }}">{{ $student->educationProgram?->name ?? 'Pendidikan Pembentukan' }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Kesehatan</div>
                            <div class="font-semibold text-slate-800 mt-0.5">{{ $student->healthRecord?->daily_health_status ?? '-' }}</div>
                            <div class="text-xs text-amber-600 font-medium flex items-center gap-1 mt-0.5">
                                <span class="ms text-[14px]">star</span> {{ Str::limit($student->healthRecord?->stakes_grade ?? '-', 10, '') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                        <a href="{{ route('students.show', $student) }}" class="flex-1 flex items-center justify-center gap-2 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                            <span class="ms text-[16px]">visibility</span> Data Detail
                        </a>
                        <a href="{{ route('health.show', $student) }}" class="flex-1 flex items-center justify-center gap-2 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold transition-colors">
                            <span class="ms text-[16px]">medical_services</span> Rekam Medis
                        </a>
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center text-slate-500 text-sm">
                    Belum ada data serdik yang terdaftar.
                </div>
            @endforelse
        </div>

        <!-- DESKTOP TABLE (Visible only on large screens) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider">
                    <tr>
                        <th class="px-5 py-3 border-b border-slate-200">Siswa</th>
                        <th class="px-5 py-3 border-b border-slate-200">Pendidikan</th>
                        <th class="px-5 py-3 border-b border-slate-200">Kompi / Ton</th>
                        <th class="px-5 py-3 border-b border-slate-200">Rekam Medis</th>
                        <th class="px-5 py-3 border-b border-slate-200">Status</th>
                        <th class="px-5 py-3 border-b border-slate-200">Stakes</th>
                        <th class="px-5 py-3 border-b border-slate-200 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentStudents as $student)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 align-middle">
                                <div class="font-semibold text-slate-900">{{ $student->full_name }}</div>
                                <div class="text-xs font-mono text-slate-500 mt-0.5">{{ $student->nosik }}</div>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <div class="font-medium text-slate-700">{{ $student->satdik?->code }}</div>
                                <div class="text-xs text-slate-500 mt-0.5 truncate max-w-[150px]" title="{{ $student->educationProgram?->name }}">{{ $student->educationProgram?->name ?? 'Pendidikan Pembentukan' }}</div>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <div class="font-medium text-slate-700">{{ $student->company ?? '-' }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $student->platoon ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <span class="text-slate-600 font-medium">{{ $student->healthRecord?->daily_health_status ?? 'Tidak ada' }}</span>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                @php
                                    $bgClass = 'bg-slate-100 text-slate-700';
                                    if($student->status === 'Aktif') $bgClass = 'bg-indigo-50 text-indigo-700';
                                    if($student->status === 'Sakit') $bgClass = 'bg-rose-50 text-rose-700';
                                    if(in_array($student->status, ['Lulus', 'Selesai'])) $bgClass = 'bg-emerald-50 text-emerald-700';
                                    if(str_contains($student->status, 'DO')) $bgClass = 'bg-amber-50 text-amber-700';
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold tracking-wide {{ $bgClass }}">
                                    {{ $student->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <div class="flex items-center gap-1.5 text-amber-600 font-medium">
                                    <span class="ms text-[16px] text-amber-400">star</span> 
                                    {{ Str::limit($student->healthRecord?->stakes_grade ?? '-', 8, '') }}
                                </div>
                            </td>
                            <td class="px-5 py-3 align-middle text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('students.show', $student) }}" class="flex items-center gap-1.5 px-2.5 py-1.5 text-slate-600 hover:text-slate-900 transition-colors bg-slate-100 hover:bg-slate-200 rounded font-semibold text-[11px]" title="Data Detail">
                                        <span class="ms text-[16px]">visibility</span> Data Detail
                                    </a>
                                    <a href="{{ route('health.show', $student) }}" class="flex items-center gap-1.5 px-2.5 py-1.5 text-emerald-600 hover:text-emerald-700 transition-colors bg-emerald-50 hover:bg-emerald-100 rounded font-semibold text-[11px]" title="Rekam Medis">
                                        <span class="ms text-[16px]">medical_services</span> Rekam Medis
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                                Belum ada data serdik yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="px-5 py-4 border-t border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 text-sm text-slate-500 bg-slate-50">
            <div>Menampilkan <span class="font-semibold text-slate-900">{{ count($recentStudents) }}</span> dari <span class="font-semibold text-slate-900">{{ $totalStudents }}</span> data serdik.</div>
            
            <a href="{{ route('students.index') }}" class="text-slate-600 font-semibold hover:text-slate-900 transition-colors inline-flex items-center gap-1">
                Buka Tabel Lengkap <span class="ms text-sm">arrow_forward</span>
            </a>
        </div>
    </div>

    @if(auth()->user()?->isSuperAdmin())
    <!-- SECONDARY LINKS (Audit Logs - Khusus Superadmin) -->
    <div class="flex justify-end">
        <a href="{{ route('students.audit-logs') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            Lihat Seluruh Audit Log Keamanan <span class="ms text-[18px]">arrow_forward</span>
        </a>
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = @json($chartData);
    
    // Helper to append counts to labels
    const formatLabels = (labels, counts) => labels.map((label, i) => `${label} (${counts[i]})`);

    // Common Chart.js Options for minimal clean look
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { family: 'Inter', size: 11 } } }
        },
        cutout: '70%',
        borderWidth: 0
    };
    const barOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, border: { display: false }, ticks: { font: { family: 'Inter', size: 11 }, precision: 0 } },
            x: { grid: { display: false }, border: { display: false }, ticks: { font: { family: 'Inter', size: 11 } } }
        },
        borderRadius: 4
    };

    // 1. Status Siswa (Doughnut)
    if(document.getElementById('chartStudentStatus')) {
        new Chart(document.getElementById('chartStudentStatus'), {
            type: 'doughnut',
            data: {
                labels: formatLabels(data.student_status.labels, data.student_status.counts),
                datasets: [{
                    data: data.student_status.counts,
                    backgroundColor: ['#4f46e5', '#f43f5e', '#10b981', '#f59e0b']
                }]
            },
            options: commonOptions
        });
    }

    // 2. Status Kesehatan Harian (Doughnut)
    if(document.getElementById('chartHealthStatus')) {
        new Chart(document.getElementById('chartHealthStatus'), {
            type: 'doughnut',
            data: {
                labels: formatLabels(data.health_status.labels, data.health_status.counts),
                datasets: [{
                    data: data.health_status.counts,
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6']
                }]
            },
            options: commonOptions
        });
    }

    // 3. Distribusi Stakes (Pie)
    if(document.getElementById('chartStakes')) {
        new Chart(document.getElementById('chartStakes'), {
            type: 'pie',
            data: {
                labels: formatLabels(data.stakes_distribution.labels, data.stakes_distribution.counts),
                datasets: [{
                    data: data.stakes_distribution.counts,
                    backgroundColor: ['#3b82f6', '#0ea5e9', '#64748b', '#ef4444']
                }]
            },
            options: { ...commonOptions, cutout: '0%' }
        });
    }

    // 4. Perbandingan Kekuatan per Satdik (Bar)
    if(document.getElementById('chartSatdik')) {
        new Chart(document.getElementById('chartSatdik'), {
            type: 'bar',
            data: {
                labels: formatLabels(data.satdik_comparison.labels, data.satdik_comparison.total),
                datasets: [{
                    label: 'Siswa',
                    data: data.satdik_comparison.total,
                    backgroundColor: '#3b82f6'
                }]
            },
            options: barOptions
        });
    }

    // 5. Golongan Darah (Bar)
    if(document.getElementById('chartBloodType')) {
        new Chart(document.getElementById('chartBloodType'), {
            type: 'bar',
            data: {
                labels: formatLabels(data.blood_type.labels, data.blood_type.counts),
                datasets: [{
                    label: 'Siswa',
                    data: data.blood_type.counts,
                    backgroundColor: '#ef4444'
                }]
            },
            options: barOptions
        });
    }

    // 6. Distribusi Agama (Doughnut)
    if(document.getElementById('chartReligion')) {
        new Chart(document.getElementById('chartReligion'), {
            type: 'doughnut',
            data: {
                labels: formatLabels(data.religion.labels, data.religion.counts),
                datasets: [{
                    data: data.religion.counts,
                    backgroundColor: ['#14b8a6', '#8b5cf6', '#f59e0b', '#ec4899', '#64748b', '#06b6d4']
                }]
            },
            options: commonOptions
        });
    }
});
</script>
@endsection
