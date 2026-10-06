import re

file_path = "d:/laragon/www/rindam/resources/views/students/show.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace breadcrumbs and top buttons
old_breadcrumbs = """<!-- BREADCRUMB & BACK BUTTON -->
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
        @if(auth()->user()?->isSuperAdmin())
        <a href="{{ route('students.audit-logs') }}" class="btn btn-outline btn-sm">
            <span class="ms">policy</span> Lihat Seluruh Audit Trail
        </a>
        @endif
    </div>
</div>"""

new_breadcrumbs = """<!-- BREADCRUMB & BACK BUTTON -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500">
        <a href="{{ route('students.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">Data Serdik</a>
        <span class="ms text-slate-400 text-[16px]">chevron_right</span>
        <a href="{{ route('students.index', ['satdik_id' => $student->satdik_id]) }}" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">{{ $student->satdik->code }}</a>
        <span class="ms text-slate-400 text-[16px]">chevron_right</span>
        <span class="text-slate-900 font-bold">NOSIK: {{ $student->nosik }}</span>
    </div>
    <div class="flex flex-wrap items-center gap-2.5">
        <a href="{{ route('students.index', ['satdik_id' => $student->satdik_id]) }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-sm transition-colors w-full sm:w-auto">
            <span class="ms text-[18px]">arrow_back</span> Kembali ke Daftar
        </a>
        @if(auth()->user()?->isSuperAdmin())
        <a href="{{ route('students.audit-logs') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-sm transition-colors w-full sm:w-auto">
            <span class="ms text-[18px]">policy</span> Lihat Seluruh Audit Trail
        </a>
        @endif
    </div>
</div>"""
content = content.replace(old_breadcrumbs, new_breadcrumbs)

# Replace HERO card
old_hero = """<!-- STUDENT PROFILE HEADER HERO -->
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
</div>"""

new_hero = """<!-- STUDENT PROFILE HEADER HERO -->
<div class="bg-white border border-slate-200 border-l-4 border-l-blue-600 rounded-xl shadow-sm mb-6 p-5 sm:p-7">
    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
            <!-- AVATAR / PHOTO -->
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-blue-700 to-blue-500 text-white flex items-center justify-center text-2xl sm:text-3xl font-black shadow-lg shadow-blue-500/30 border-2 border-white shrink-0">
                {{ substr($student->full_name, 0, 2) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-blue-50 border border-blue-100 text-blue-700 inline-flex items-center gap-1">
                        <span class="ms text-[14px]">account_balance</span> {{ $student->satdik->code }}
                    </span>
                    <span class="px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        NOSIK: {{ $student->nosik }}
                    </span>
                    @php 
                        $u = $student->unified_status; 
                        $bgClass = 'bg-slate-100 text-slate-700 border-slate-200';
                        if($student->status === 'Aktif') $bgClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        if($student->status === 'Sakit') $bgClass = 'bg-amber-50 text-amber-700 border-amber-200';
                        if(in_array($student->status, ['Lulus', 'Selesai'])) $bgClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                        if(str_contains($student->status, 'DO')) $bgClass = 'bg-rose-50 text-rose-700 border-rose-200';
                    @endphp
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold border {{ $bgClass }} inline-flex items-center gap-1">
                        <span class="ms text-[14px]">{{ $u['icon'] }}</span> {{ $u['label'] }}
                    </span>
                </div>
                <h1 class="m-0 text-xl sm:text-2xl font-bold text-slate-900 mb-1.5 tracking-tight">
                    {{ $student->full_name }}
                </h1>
                <div class="text-[11px] sm:text-xs text-slate-500 flex flex-wrap items-center gap-x-3 gap-y-1">
                    <span>Pangkat: <b class="text-slate-800">{{ $student->student_rank }}</b></span>
                    <span class="text-slate-300">•</span>
                    <span>Program: <b class="text-slate-800">{{ $student->educationProgram->name ?? '-' }}</b></span>
                    <span class="text-slate-300">•</span>
                    <span>Kelas: <b class="text-slate-800">{{ $student->classroom->name ?? 'Belum Ditentukan' }}</b></span>
                </div>
            </div>
        </div>

        <div class="lg:text-right shrink-0">
            @if($student->is_counted)
                <div class="mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-md text-xs font-bold">
                        <span class="ms text-[16px]">how_to_reg</span> Siswa Aktif Terhitung
                    </span>
                </div>
            @else
                <div class="mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 border border-slate-200 text-slate-600 rounded-md text-xs font-bold">
                        <span class="ms text-[16px]">archive</span> Status Arsip (Tidak Terhitung)
                    </span>
                </div>
            @endif
            <div class="mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-700 rounded-md text-xs font-bold shadow-sm">
                    <span class="ms text-[16px]">enhanced_encryption</span> Data Pribadi Terproteksi Sistem
                </span>
            </div>
            <div class="text-[11px] text-slate-400 mt-2 font-medium">
                Terdaftar sejak: {{ $student->created_at->format('d M Y, H:i') }} WIB
            </div>
        </div>
    </div>
</div>"""
content = content.replace(old_hero, new_hero)

# Grid layout
old_grid = """<div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">"""
new_grid = """<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">"""
content = content.replace(old_grid, new_grid)

# KARTU 1 (Military Data)
old_card1_start = """    <!-- KARTU 1: PROFIL KEMILITERAN & PENDIDIKAN (PUBLIC TO OPERATORS) -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <h3 class="card-title">
                <span class="ms" style="color:var(--o700);">military_tech</span> Data Kemiliteran & Satdik
            </h3>
            <span class="badge badge-satdik">{{ $student->satdik->code }}</span>
        </div>
        <div class="card-body">
            <table style="width:100%; border-collapse:collapse; font-size:13.5px;">"""
new_card1_start = """    <!-- KARTU 1: PROFIL KEMILITERAN & PENDIDIKAN (PUBLIC TO OPERATORS) -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 rounded-t-xl">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="ms text-blue-600 text-[20px]">military_tech</span> Data Kemiliteran & Satdik
            </h3>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">{{ $student->satdik->code }}</span>
        </div>
        <div class="p-5">
            <!-- Mobile View (Cards instead of Table) -->
            <div class="block sm:hidden space-y-4">"""
content = content.replace(old_card1_start, new_card1_start)

# We need to construct the mobile view cards for the first table, then the desktop table view.
# To make it easier, I will replace the entire KARTU 1 and KARTU 2 sections up to the Audit Trail.
import sys
sys.exit() # Wait, I shouldn't just replace blindly if I don't know the exact HTML for KARTU 1 body.

