<?php

namespace App\Http\Controllers;

use App\Models\EducationProgram;
use App\Models\Satdik;
use App\Models\Student;
use App\Models\StudentHealthRecord;
use App\Models\StudentPersonalDataAccessLog;
use App\Models\User;
use App\Services\StudentManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentHealthController extends Controller
{
    public function __construct(
        protected StudentManagementService $studentService
    ) {}

    /**
     * Halaman Utama Pengelolaan Kesehatan & Rekam Medis Serdik per Satdik
     */
    public function index(Request $request)
    {
        $satdiks = Satdik::active()->get();
        $selectedSatdikId = $request->query('satdik_id');
        $selectedSatdikCode = $request->query('satdik');

        if ($selectedSatdikCode && !$selectedSatdikId) {
            $matched = $satdiks->firstWhere('code', strtoupper($selectedSatdikCode));
            if ($matched) {
                $selectedSatdikId = $matched->id;
            }
        }

        // Jika user adalah operator terikat Satdik tertentu
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->isPimpinan() && $currentUser->satdik_id) {
            $selectedSatdikId = $currentUser->satdik_id;
        }

        $selectedProgramId = $request->query('program_id') ? (int)$request->query('program_id') : null;
        $healthStatus = $request->query('health_status');
        $keyword = $request->query('q');
        $tab = $request->query('tab', 'all'); // 'all', 'aktif', 'arsip'

        $query = Student::with(['satdik', 'educationProgram', 'classroom', 'healthRecord'])
            ->when($selectedSatdikId, function ($q, $satdikId) {
                $q->where('satdik_id', $satdikId);
            })
            ->when($selectedProgramId, function ($q, $progId) {
                $q->where('education_program_id', $progId);
            })
            ->when($healthStatus, function ($q, $status) {
                $q->whereHas('healthRecord', function ($hq) use ($status) {
                    $hq->where('daily_health_status', $status);
                });
            })
            ->when($keyword, function ($q, $kw) {
                $q->search($kw);
            });

        // Tab Filter:
        // 'aktif' -> Hanya siswa yang aktif terhitung dalam pendidikan (Aktif, Sakit, Dinas Luar)
        // 'arsip' -> Siswa yang selesai pendidikan / keluar (Selesai, Lulus, DO - Tidak Terhitung Lagi)
        if ($tab === 'aktif') {
            $query->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar']);
        } elseif ($tab === 'arsip') {
            $query->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan']);
        }

        $students = $query->latest()->paginate(12)->withQueryString();

        // Statistik Terpadu dari StudentManagementService (Siswa Aktif Terhitung vs Arsip Selesai & Program)
        $stats = $this->studentService->getSatdikSummaryStats(
            satdikId: $selectedSatdikId ? (int)$selectedSatdikId : null,
            programId: $selectedProgramId
        );

        // Tambahkan rincian statistik medis harian untuk pemantauan poliklinik
        $baseHealthQuery = StudentHealthRecord::whereHas('student', function ($sq) use ($selectedSatdikId, $selectedProgramId) {
            if ($selectedSatdikId) {
                $sq->where('satdik_id', $selectedSatdikId);
            }
            if ($selectedProgramId) {
                $sq->where('education_program_id', $selectedProgramId);
            }
        });

        // Breakdown khusus siswa aktif yang sedang menjalani pendidikan
        $activeHealthQuery = (clone $baseHealthQuery)->whereHas('student', function ($sq) {
            $sq->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar']);
        });

        $stats['siap_latih'] = (clone $activeHealthQuery)->where('daily_health_status', 'Siap Latih')->count();
        $stats['berobat_jalan'] = (clone $activeHealthQuery)->where('daily_health_status', 'Berobat Jalan')->count();
        $stats['rawat_inap'] = (clone $activeHealthQuery)->where('daily_health_status', 'Rawat Inap Poliklinik')->count();
        $stats['rujuk_rumkit'] = (clone $activeHealthQuery)->where('daily_health_status', 'Rujuk Rumkit')->count();
        $stats['perawatan_khusus'] = $stats['rawat_inap'] + $stats['rujuk_rumkit'];

        $selectedSatdik = $selectedSatdikId ? Satdik::find($selectedSatdikId) : null;

        // Daftar program untuk filter per satdik
        $programsQuery = EducationProgram::query();
        if ($selectedSatdikId) {
            $programsQuery->where('satdik_id', $selectedSatdikId);
        }
        $availablePrograms = $programsQuery->orderBy('name')->get();
        $selectedProgram = $selectedProgramId ? EducationProgram::find($selectedProgramId) : null;

        return view('health.index', compact(
            'satdiks',
            'students',
            'stats',
            'selectedSatdikId',
            'selectedSatdik',
            'availablePrograms',
            'selectedProgramId',
            'selectedProgram',
            'healthStatus',
            'keyword',
            'tab'
        ));
    }

    /**
     * Lembar Rekam Medis & Kesehatan Serdik
     */
    public function show(Student $student)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->isPimpinan() && $currentUser->satdik_id) {
            if ($student->satdik_id !== $currentUser->satdik_id) {
                abort(403, 'Akses Ditolak: Anda hanya berwenang mengakses rekam medis serdik di Satuan Pendidikan Anda (' . ($currentUser->satdik->name ?? 'Satdik Anda') . ').');
            }
        }

        $student->load(['satdik', 'educationProgram', 'classroom', 'healthRecord']);
        
        $healthRecord = $student->healthRecord ?? $student->healthRecord()->create([
            'daily_health_status' => 'Siap Latih',
            'stakes_grade' => 'Stakes I (Sangat Baik)',
            'last_examined_at' => now(),
        ]);

        return view('health.show', compact('student', 'healthRecord'));
    }

    /**
     * Form Update Rekam Medis & Status Kesehatan Serdik oleh Tim Kesehatan Satdik
     */
    public function edit(Student $student)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->isPimpinan() && $currentUser->satdik_id) {
            if ($student->satdik_id !== $currentUser->satdik_id) {
                abort(403, 'Akses Ditolak: Anda hanya berwenang mengedit rekam medis serdik di Satuan Pendidikan Anda (' . ($currentUser->satdik->name ?? 'Satdik Anda') . ').');
            }
        }

        $student->load(['satdik', 'educationProgram', 'classroom', 'healthRecord']);
        
        $healthRecord = $student->healthRecord ?? $student->healthRecord()->create([
            'daily_health_status' => 'Siap Latih',
            'stakes_grade' => 'Stakes I (Sangat Baik)',
            'last_examined_at' => now(),
        ]);

        return view('health.edit', compact('student', 'healthRecord'));
    }

    /**
     * Simpan Pembaruan Rekam Medis & Status Kesehatan
     */
    public function update(Request $request, Student $student)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->isPimpinan() && $currentUser->satdik_id) {
            if ($student->satdik_id !== $currentUser->satdik_id) {
                abort(403, 'Akses Ditolak: Anda hanya berwenang memperbarui rekam medis serdik di Satuan Pendidikan Anda (' . ($currentUser->satdik->name ?? 'Satdik Anda') . ').');
            }
        }

        $validated = $request->validate([
            'daily_health_status' => ['required', 'in:Siap Latih,Berobat Jalan,Rawat Inap Poliklinik,Rujuk Rumkit'],
            'stakes_grade' => ['nullable', 'string', 'max:50'],
            'height_cm' => ['nullable', 'integer', 'min:140', 'max:220'],
            'weight_kg' => ['nullable', 'numeric', 'min:40', 'max:150'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'pulse_rate' => ['nullable', 'integer', 'min:40', 'max:200'],
            'allergies' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
            'psychological_record' => ['nullable', 'string'],
            'referral_hospital' => ['nullable', 'string', 'max:150'],
            'doctor_notes' => ['nullable', 'string'],
            'examined_by' => ['nullable', 'string', 'max:100'],
        ]);

        $healthRecord = $student->healthRecord ?? new StudentHealthRecord(['student_id' => $student->id]);
        
        // Hitung BMI otomatis jika tinggi dan berat diisi
        $height = $validated['height_cm'] ?? $healthRecord->height_cm;
        $weight = $validated['weight_kg'] ?? $healthRecord->weight_kg;
        $bmi = null;
        if ($height > 0 && $weight > 0) {
            $hM = $height / 100;
            $bmi = round($weight / ($hM * $hM), 1);
        }

        $healthRecord->fill([
            'daily_health_status' => $validated['daily_health_status'],
            'stakes_grade' => $validated['stakes_grade'] ?? $healthRecord->stakes_grade ?? 'Stakes I (Sangat Baik)',
            'height_cm' => $height,
            'weight_kg' => $weight,
            'bmi' => $bmi,
            'blood_pressure' => $validated['blood_pressure'] ?? null,
            'pulse_rate' => $validated['pulse_rate'] ?? null,
            'allergies' => $validated['allergies'] ?? null,
            'medical_history' => $validated['medical_history'] ?? null,
            'psychological_record' => $validated['psychological_record'] ?? null,
            'referral_hospital' => $validated['referral_hospital'] ?? null,
            'doctor_notes' => $validated['doctor_notes'] ?? null,
            'examined_by' => $validated['examined_by'] ?? (Auth::user()?->name ?? 'Petugas Poliklinik Satdik'),
            'last_examined_at' => now(),
        ]);

        if ($validated['daily_health_status'] === 'Rawat Inap Poliklinik' && empty($healthRecord->polyclinic_admission_date)) {
            $healthRecord->polyclinic_admission_date = now()->toDateString();
        }

        $healthRecord->save();

        // Sinkronisasi status siswa kemiliteran jika sakit
        if (in_array($validated['daily_health_status'], ['Rawat Inap Poliklinik', 'Rujuk Rumkit', 'Berobat Jalan'])) {
            $student->update(['status' => 'Sakit']);
        } elseif ($validated['daily_health_status'] === 'Siap Latih' && $student->status === 'Sakit') {
            $student->update(['status' => 'Aktif']);
        }

        return redirect()->route('health.show', $student)
            ->with('success', "Rekam medis dan status kesehatan Serdik {$student->full_name} berhasil diperbarui.");
    }
}
