<?php

namespace App\Http\Controllers;

use App\Models\Satdik;
use App\Models\Student;
use App\Models\StudentHealthRecord;
use App\Models\StudentPersonalDataAccessLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilan Pusat Komando & Dashboard Eksekutif SIPANDU-WBK
     */
    public function index(Request $request)
    {
        $satdikId = $request->get('satdik_id');
        $satdiks = Satdik::where('is_active', true)->withCount('students')->get();
        $selectedSatdik = $satdikId ? Satdik::find($satdikId) : null;

        // Query dasar serdik
        $studentQuery = Student::query();
        if ($satdikId) {
            $studentQuery->where('satdik_id', $satdikId);
        }

        // Statistik Makro Siswa: Hanya Aktif yang terhitung dalam kekuatan pendidikan berjalan
        $totalStudents = (clone $studentQuery)->count();
        $countedActiveStudents = (clone $studentQuery)->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count();
        $activeStudents = (clone $studentQuery)->where('status', 'Aktif')->count();
        $sickStudents = (clone $studentQuery)->where('status', 'Sakit')->count();
        $archivedStudents = (clone $studentQuery)->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan'])->count();
        $graduatedStudents = (clone $studentQuery)->whereIn('status', ['Lulus', 'Selesai'])->count();
        $droppedStudents = (clone $studentQuery)->where('status', 'DO / Dikeluarkan')->count();

        // Query Rekam Medis Terkait
        $healthQuery = StudentHealthRecord::query();
        if ($satdikId) {
            $healthQuery->whereHas('student', function ($q) use ($satdikId) {
                $q->where('satdik_id', $satdikId);
            });
        }

        $totalHealth = (clone $healthQuery)->count();
        $siapLatih = (clone $healthQuery)->where('daily_health_status', 'Siap Latih')->count();
        $berobatJalan = (clone $healthQuery)->where('daily_health_status', 'Berobat Jalan')->count();
        $rawatPoliklinik = (clone $healthQuery)->where('daily_health_status', 'Rawat Inap Poliklinik')->count();
        $rujukRumkit = (clone $healthQuery)->where('daily_health_status', 'Rujuk Rumkit')->count();

        $siapLatihPercent = $totalHealth > 0 ? round(($siapLatih / $totalHealth) * 100, 1) : 100;
        $perawatanCount = $berobatJalan + $rawatPoliklinik + $rujukRumkit;

        // Distribusi Stakes Militer
        $stakesI = (clone $healthQuery)->where('stakes_grade', 'like', '%Stakes I (%')->count();
        $stakesII = (clone $healthQuery)->where('stakes_grade', 'like', '%Stakes II (%')->count();
        $stakesIII = (clone $healthQuery)->where('stakes_grade', 'like', '%Stakes III (%')->count();
        $stakesIV = (clone $healthQuery)->where('stakes_grade', 'like', '%Stakes IV (%')->count();

        // Distribusi Serdik per Satdik: Yang terhitung adalah siswa aktif berjalan
        $satdikBreakdown = $satdiks->map(function ($satdik) {
            $students = $satdik->students;
            $activeCount = $students->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count();
            $archivedCount = $students->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan'])->count();
            $readyCount = $students->where('status', 'Aktif')->count();
            $sickCount = $students->where('status', 'Sakit')->count();

            $readyPercent = $activeCount > 0 ? round(($readyCount / $activeCount) * 100) : 100;

            return [
                'satdik' => $satdik,
                'total' => $activeCount, // Yang terhitung aktif!
                'total_all' => $students->count(),
                'archived_count' => $archivedCount,
                'ready_count' => $readyCount,
                'sick_count' => $sickCount,
                'ready_percent' => $readyPercent,
            ];
        });

        // Rekapitulasi per Program Pendidikan (DIKMABA, DIKJURBA, DIKMATA, dll.)
        $programsBreakdown = \App\Models\EducationProgram::with('satdik')
            ->when($satdikId, fn($q) => $q->where('satdik_id', $satdikId))
            ->get()
            ->map(function($p) {
                return [
                    'program' => $p,
                    'counted' => $p->students()->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count(),
                    'archived' => $p->students()->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan'])->count(),
                    'total' => $p->students()->count(),
                ];
            });

        // Serdik Terkini (Feed Aktivitas)
        $recentStudents = (clone $studentQuery)
            ->with(['satdik', 'educationProgram', 'healthRecord'])
            ->latest()
            ->take(6)
            ->get();

        // Log Akses Audit Keamanan Terkini (Area 5 Zona Integritas)
        $recentAuditLogs = StudentPersonalDataAccessLog::with(['student', 'user'])
            ->latest('accessed_at')
            ->take(5)
            ->get();

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
