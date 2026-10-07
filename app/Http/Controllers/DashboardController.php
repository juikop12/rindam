<?php

namespace App\Http\Controllers;

use App\Models\Satdik;
use App\Models\Student;
use App\Models\StudentHealthRecord;
use App\Models\StudentPersonalDataAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Tampilan Pusat Komando & Dashboard Eksekutif SIPANDU-WBK
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        if ($currentUser && !$currentUser->isPimpinan() && $currentUser->satdik_id) {
            $satdikId = $currentUser->satdik_id;
            $satdiks = Satdik::where('id', $satdikId)->withCount('students')->get();
            $selectedSatdik = Satdik::find($satdikId);
        } else {
            $satdikId = $request->get('satdik_id');
            $satdiks = Satdik::where('is_active', true)->withCount('students')->get();
            $selectedSatdik = $satdikId ? Satdik::find($satdikId) : null;
        }

        // Query agregasi serdik tunggal
        $studentQuery = Student::query();
        if ($satdikId) {
            $studentQuery->where('satdik_id', $satdikId);
        }

        $sStats = (clone $studentQuery)->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Aktif' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as sick,
            SUM(CASE WHEN status IN ('Aktif', 'Sakit', 'Dinas Luar') THEN 1 ELSE 0 END) as counted,
            SUM(CASE WHEN status IN ('Selesai', 'Lulus', 'DO / Dikeluarkan') THEN 1 ELSE 0 END) as archived,
            SUM(CASE WHEN status IN ('Lulus', 'Selesai') THEN 1 ELSE 0 END) as graduated,
            SUM(CASE WHEN status = 'DO / Dikeluarkan' THEN 1 ELSE 0 END) as dropped
        ")->first();

        // Query agregasi rekam medis tunggal
        $healthQuery = StudentHealthRecord::query();
        if ($satdikId) {
            $healthQuery->whereHas('student', function ($q) use ($satdikId) {
                $q->where('satdik_id', $satdikId);
            });
        }

        $hStats = (clone $healthQuery)->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN daily_health_status = 'Siap Latih' THEN 1 ELSE 0 END) as siap_latih,
            SUM(CASE WHEN daily_health_status = 'Berobat Jalan' THEN 1 ELSE 0 END) as berobat_jalan,
            SUM(CASE WHEN daily_health_status = 'Rawat Inap Poliklinik' THEN 1 ELSE 0 END) as rawat_poliklinik,
            SUM(CASE WHEN daily_health_status = 'Rujuk Rumkit' THEN 1 ELSE 0 END) as rujuk_rumkit,
            SUM(CASE WHEN stakes_grade LIKE '%Stakes I (%' THEN 1 ELSE 0 END) as stakes_i,
            SUM(CASE WHEN stakes_grade LIKE '%Stakes II (%' THEN 1 ELSE 0 END) as stakes_ii,
            SUM(CASE WHEN stakes_grade LIKE '%Stakes III (%' THEN 1 ELSE 0 END) as stakes_iii,
            SUM(CASE WHEN stakes_grade LIKE '%Stakes IV (%' THEN 1 ELSE 0 END) as stakes_iv
        ")->first();

        // Distribusi Serdik per Satdik via agregasi GROUP BY
        $satdikStudentAgg = Student::selectRaw("
            satdik_id,
            COUNT(*) as total_all,
            SUM(CASE WHEN status IN ('Aktif', 'Sakit', 'Dinas Luar') THEN 1 ELSE 0 END) as active_count,
            SUM(CASE WHEN status IN ('Selesai', 'Lulus', 'DO / Dikeluarkan') THEN 1 ELSE 0 END) as archived_count,
            SUM(CASE WHEN status = 'Aktif' THEN 1 ELSE 0 END) as ready_count,
            SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as sick_count
        ")->groupBy('satdik_id')->get()->keyBy('satdik_id');

        $satdikBreakdown = $satdiks->map(function ($satdik) use ($satdikStudentAgg) {
            $agg = $satdikStudentAgg->get($satdik->id);
            $activeCount = (int)($agg->active_count ?? 0);
            $archivedCount = (int)($agg->archived_count ?? 0);
            $readyCount = (int)($agg->ready_count ?? 0);
            $sickCount = (int)($agg->sick_count ?? 0);
            $totalAll = (int)($agg->total_all ?? 0);
            $readyPercent = $activeCount > 0 ? round(($readyCount / $activeCount) * 100) : 100;

            return [
                'satdik' => $satdik,
                'total' => $activeCount,
                'total_all' => $totalAll,
                'archived_count' => $archivedCount,
                'ready_count' => $readyCount,
                'sick_count' => $sickCount,
                'ready_percent' => $readyPercent,
            ];
        });

        // Rekapitulasi per Program Pendidikan via agregasi GROUP BY (Bebas N+1)
        $progStudentAgg = Student::selectRaw("
            education_program_id,
            COUNT(*) as total,
            SUM(CASE WHEN status IN ('Aktif', 'Sakit', 'Dinas Luar') THEN 1 ELSE 0 END) as counted,
            SUM(CASE WHEN status IN ('Selesai', 'Lulus', 'DO / Dikeluarkan') THEN 1 ELSE 0 END) as archived
        ")->groupBy('education_program_id')->get()->keyBy('education_program_id');

        $programsBreakdown = \App\Models\EducationProgram::with('satdik')
            ->when($satdikId, fn($q) => $q->where('satdik_id', $satdikId))
            ->get()
            ->map(function($p) use ($progStudentAgg) {
                $agg = $progStudentAgg->get($p->id);
                return [
                    'program' => $p,
                    'counted' => (int)($agg->counted ?? 0),
                    'archived' => (int)($agg->archived ?? 0),
                    'total' => (int)($agg->total ?? 0),
                ];
            });

        $totalStudents = (int)($sStats->total ?? 0);
        $activeStudents = (int)($sStats->active ?? 0);
        $sickStudents = (int)($sStats->sick ?? 0);
        $countedActiveStudents = (int)($sStats->counted ?? 0);
        $archivedStudents = (int)($sStats->archived ?? 0);
        $graduatedStudents = (int)($sStats->graduated ?? 0);
        $droppedStudents = (int)($sStats->dropped ?? 0);

        $totalHealth = (int)($hStats->total ?? 0);
        $siapLatih = (int)($hStats->siap_latih ?? 0);
        $berobatJalan = (int)($hStats->berobat_jalan ?? 0);
        $rawatPoliklinik = (int)($hStats->rawat_poliklinik ?? 0);
        $rujukRumkit = (int)($hStats->rujuk_rumkit ?? 0);
        $siapLatihPercent = $totalHealth > 0 ? round(($siapLatih / $totalHealth) * 100, 1) : 100;
        $perawatanCount = $berobatJalan + $rawatPoliklinik + $rujukRumkit;

        $stakesI = (int)($hStats->stakes_i ?? 0);
        $stakesII = (int)($hStats->stakes_ii ?? 0);
        $stakesIII = (int)($hStats->stakes_iii ?? 0);
        $stakesIV = (int)($hStats->stakes_iv ?? 0);

        $studentQuery = Student::query();
        if ($satdikId) {
            $studentQuery->where('satdik_id', $satdikId);
        }

        // Serdik Terkini (Feed Aktivitas)
        $recentStudents = (clone $studentQuery)
            ->with(['satdik', 'educationProgram', 'healthRecord'])
            ->latest()
            ->take(6)
            ->get();

        // Log Akses Audit Keamanan Terkini (Area 5 Zona Integritas)
        $auditQuery = StudentPersonalDataAccessLog::with(['student', 'user']);
        if ($satdikId) {
            $auditQuery->whereHas('student', function ($q) use ($satdikId) {
                $q->where('satdik_id', $satdikId);
            });
        }
        $recentAuditLogs = $auditQuery->latest('accessed_at')->take(5)->get();

        $bloodTypes = (clone $studentQuery)
            ->selectRaw('blood_type, count(*) as total')
            ->whereNotNull('blood_type')
            ->groupBy('blood_type')
            ->pluck('total', 'blood_type')->toArray();

        $religions = (clone $studentQuery)
            ->selectRaw('religion, count(*) as total')
            ->whereNotNull('religion')
            ->groupBy('religion')
            ->pluck('total', 'religion')->toArray();

        // Data Grafik Analitik & Diagram Interaktif (Murni Data & Kesehatan Siswa)
        $chartData = [
            'health_status' => [
                'labels' => ['Siap Latih (Lapangan)', 'Berobat Jalan', 'Rawat Inap Poliklinik', 'Rujuk Rumkit Dinas'],
                'counts' => [$siapLatih, $berobatJalan, $rawatPoliklinik, $rujukRumkit],
                'percentages' => [
                    $totalHealth > 0 ? round(($siapLatih / $totalHealth) * 100, 1) : 0,
                    $totalHealth > 0 ? round(($berobatJalan / $totalHealth) * 100, 1) : 0,
                    $totalHealth > 0 ? round(($rawatPoliklinik / $totalHealth) * 100, 1) : 0,
                    $totalHealth > 0 ? round(($rujukRumkit / $totalHealth) * 100, 1) : 0,
                ],
            ],
            'satdik_comparison' => [
                'labels' => $satdikBreakdown->pluck('satdik.code')->values()->all(),
                'names' => $satdikBreakdown->pluck('satdik.name')->values()->all(),
                'total' => $satdikBreakdown->pluck('total')->values()->all(),
                'ready' => $satdikBreakdown->pluck('ready_count')->values()->all(),
                'sick' => $satdikBreakdown->pluck('sick_count')->values()->all(),
                'ready_percent' => $satdikBreakdown->pluck('ready_percent')->values()->all(),
            ],
            'student_status' => [
                'labels' => ['Siswa Aktif', 'Siswa Sakit', 'Siswa Lulus', 'DO / Dikeluarkan'],
                'counts' => [$activeStudents, $sickStudents, $graduatedStudents, $droppedStudents],
                'percentages' => [
                    $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100, 1) : 0,
                    $totalStudents > 0 ? round(($sickStudents / $totalStudents) * 100, 1) : 0,
                    $totalStudents > 0 ? round(($graduatedStudents / $totalStudents) * 100, 1) : 0,
                    $totalStudents > 0 ? round(($droppedStudents / $totalStudents) * 100, 1) : 0,
                ],
            ],
            'stakes_distribution' => [
                'labels' => ['Stakes I (Sangat Baik)', 'Stakes II (Baik)', 'Stakes III (Cukup)', 'Stakes IV (TMS Sementara)'],
                'counts' => [$stakesI, $stakesII, $stakesIII, $stakesIV],
                'percentages' => [
                    $totalHealth > 0 ? round(($stakesI / $totalHealth) * 100, 1) : 0,
                    $totalHealth > 0 ? round(($stakesII / $totalHealth) * 100, 1) : 0,
                    $totalHealth > 0 ? round(($stakesIII / $totalHealth) * 100, 1) : 0,
                    $totalHealth > 0 ? round(($stakesIV / $totalHealth) * 100, 1) : 0,
                ],
            ],
            'blood_type' => [
                'labels' => array_keys($bloodTypes),
                'counts' => array_values($bloodTypes),
            ],
            'religion' => [
                'labels' => array_keys($religions),
                'counts' => array_values($religions),
            ],
        ];

        return view('dashboard.index', compact(
            'satdiks',
            'selectedSatdik',
            'totalStudents',
            'activeStudents',
            'sickStudents',
            'graduatedStudents',
            'droppedStudents',
            'totalHealth',
            'siapLatih',
            'berobatJalan',
            'rawatPoliklinik',
            'rujukRumkit',
            'siapLatihPercent',
            'perawatanCount',
            'stakesI',
            'stakesII',
            'stakesIII',
            'stakesIV',
            'satdikBreakdown',
            'recentStudents',
            'recentAuditLogs',
            'chartData',
            'countedActiveStudents',
            'archivedStudents',
            'programsBreakdown'
        ));
    }
}
