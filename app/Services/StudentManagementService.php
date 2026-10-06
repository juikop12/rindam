<?php

namespace App\Services;

use App\Models\Satdik;
use App\Models\EducationProgram;
use App\Models\Student;
use App\Models\StudentPersonalProfile;
use App\Models\StudentPersonalDataAccessLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;

class StudentManagementService
{
    /**
     * Dapatkan kueri siswa yang disaring berdasar Satdik, Program, status, tab, dan kata kunci
     */
    public function getStudentsQuery(
        ?int $satdikId = null,
        ?int $programId = null,
        ?string $keyword = null,
        ?string $status = null,
        ?string $tab = null
    ): Builder {
        $query = Student::with(['satdik', 'educationProgram', 'classroom', 'personalProfile', 'healthRecord']);

        if (!empty($satdikId)) {
            $query->where('satdik_id', $satdikId);
        }

        if (!empty($programId)) {
            $query->where('education_program_id', $programId);
        }

        // Tab Filter:
        // 'aktif' -> Hanya siswa yang aktif terhitung dalam pendidikan (Aktif, Sakit, Dinas Luar)
        // 'arsip' -> Siswa yang selesai pendidikan / keluar (Selesai, Lulus, DO - Tidak Terhitung Lagi)
        if ($tab === 'aktif') {
            $query->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar']);
        } elseif ($tab === 'arsip') {
            $query->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan']);
        } elseif ($tab === 'lulus') {
            $query->whereIn('status', ['Selesai', 'Lulus']);
        } elseif ($tab === 'do') {
            $query->where('status', 'DO / Dikeluarkan');
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($keyword)) {
            $query->search($keyword);
        }

        return $query->latest();
    }

    /**
     * Dapatkan statistik ringkas serdik per Satdik dan per Program Pendidikan
     */
    public function getSatdikSummaryStats(?int $satdikId = null, ?int $programId = null): array
    {
        $satdiks = Satdik::active()->withCount([
            'students as total_students',
            'students as active_students' => function ($q) {
                $q->where('status', 'Aktif');
            },
            'students as counted_students' => function ($q) {
                $q->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar']);
            },
            'students as sick_students' => function ($q) {
                $q->where('status', 'Sakit');
            },
            'students as archived_students' => function ($q) {
                $q->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan']);
            },
            'students as finished_students' => function ($q) {
                $q->whereIn('status', ['Selesai', 'Lulus']);
            },
        ])->get();

        $baseQuery = Student::query();
        if (!empty($satdikId)) {
            $baseQuery->where('satdik_id', $satdikId);
        }
        if (!empty($programId)) {
            $baseQuery->where('education_program_id', $programId);
        }

        $overallTotal = (clone $baseQuery)->count();
        $overallActive = (clone $baseQuery)->where('status', 'Aktif')->count();
        $overallCounted = (clone $baseQuery)->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count();
        $overallSick = (clone $baseQuery)->where('status', 'Sakit')->count();
        $overallArchived = (clone $baseQuery)->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan'])->count();
        $overallFinished = (clone $baseQuery)->whereIn('status', ['Selesai', 'Lulus'])->count();
        $overallDo = (clone $baseQuery)->where('status', 'DO / Dikeluarkan')->count();
        $overallProtectedProfiles = StudentPersonalProfile::count();

        // Rekapitulasi per Program Pendidikan (Aktif Terhitung vs Selesai Arsip)
        $progQuery = EducationProgram::with('satdik');
        if (!empty($satdikId)) {
            $progQuery->where('satdik_id', $satdikId);
        }
        $programStats = $progQuery->orderBy('name')->get()->map(function ($p) {
            $counted = Student::where('education_program_id', $p->id)
                ->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count();
            $activeOnly = Student::where('education_program_id', $p->id)
                ->where('status', 'Aktif')->count();
            $sickOnly = Student::where('education_program_id', $p->id)
                ->where('status', 'Sakit')->count();
            $archived = Student::where('education_program_id', $p->id)
                ->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan'])->count();
            $finished = Student::where('education_program_id', $p->id)
                ->whereIn('status', ['Selesai', 'Lulus'])->count();
            $total = Student::where('education_program_id', $p->id)->count();

            return [
                'id' => $p->id,
                'satdik_id' => $p->satdik_id,
                'satdik_code' => $p->satdik?->code ?? '-',
                'satdik_name' => $p->satdik?->name ?? '-',
                'code' => $p->code,
                'name' => $p->name,
                'academic_year' => $p->academic_year,
                'batch_number' => $p->batch_number,
                'status' => $p->status,
                'counted_students' => $counted,     // TERHITUNG (Aktif & Sakit)
                'active_students' => $activeOnly,   // Siap Latih
                'sick_students' => $sickOnly,       // Dispen Medis
                'archived_students' => $archived,   // ARSIP (Tidak terhitung)
                'finished_students' => $finished,   // Selesai/Lulus
                'total_students' => $total,
            ];
        });

        return [
            'overall_total' => $overallTotal,
            'overall_active' => $overallActive,
            'overall_counted' => $overallCounted,
            'overall_sick' => $overallSick,
            'overall_archived' => $overallArchived,
            'overall_finished' => $overallFinished,
            'overall_do' => $overallDo,
            'overall_protected_profiles' => $overallProtectedProfiles,
            'satdik_list' => $satdiks,
            'program_list' => $programStats,
        ];
    }

    /**
     * Buat siswa baru sekaligus enkripsi data pribadi sensitif
     */
    public function createStudent(array $studentData, array $personalData, ?User $actor = null): Student
    {
        return DB::transaction(function () use ($studentData, $personalData, $actor) {
            $satdik = Satdik::findOrFail($studentData['satdik_id']);
            $program = EducationProgram::findOrFail($studentData['education_program_id']);

            // Cegah duplikasi data serdik berdasarkan NIK KTP (16 Digit)
            if (!empty($personalData['nik']) && StudentPersonalProfile::isNikRegistered($personalData['nik'])) {
                $existingProfile = StudentPersonalProfile::findByNik($personalData['nik']);
                $existingStudent = $existingProfile?->student;
                $detail = $existingStudent ? " (atas nama {$existingStudent->full_name} di {$existingStudent->satdik?->name})" : "";
                throw ValidationException::withMessages([
                    'nik' => "NIK KTP {$personalData['nik']} sudah terdaftar di sistem{$detail}. Data duplikat ditolak.",
                ]);
            }

            // Generate otomatis NOSIK jika belum diisi
            if (empty($studentData['nosik'])) {
                $studentData['nosik'] = $this->generateNosik($satdik, $program);
            }

            // Simpan data pokok siswa
            $student = Student::create($studentData);

            // Simpan data pribadi sensitif (Eloquent cast otomatis mengenkripsi dengan AES-256)
            $personalData['student_id'] = $student->id;
            StudentPersonalProfile::create($personalData);

            return $student->load(['satdik', 'educationProgram', 'classroom', 'personalProfile']);
        });
    }

    /**
     * Buka dan dekripsi data sensitif serdik dengan mencatat audit trail keamanan data
     */
    public function revealSensitiveData(Student $student, User $user, string $reason): array
    {
        if (empty(trim($reason))) {
            throw ValidationException::withMessages([
                'access_reason' => 'Alasan akses data pribadi sensitif wajib diisi untuk keamanan data sistem.',
            ]);
        }

        // Catat Audit Trail secara permanen
        StudentPersonalDataAccessLog::create([
            'student_id' => $student->id,
            'accessed_by_user_id' => $user->id,
            'access_reason' => $reason,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::userAgent() ?? 'System',
            'accessed_at' => now(),
        ]);

        $profile = $student->personalProfile;

        return [
            'student_id' => $student->id,
            'nosik' => $student->nosik,
            'full_name' => $student->full_name,
            'nik' => $profile?->nik ?? '-',
            'family_card_number' => $profile?->family_card_number ?? '-',
            'mother_name' => $profile?->mother_name ?? '-',
            'father_name' => $profile?->father_name ?? '-',
            'emergency_contact_name' => $profile?->emergency_contact_name ?? '-',
            'emergency_contact_phone' => $profile?->emergency_contact_phone ?? '-',
            'home_address' => $profile?->home_address ?? '-',
            'unmasked_at' => now()->translatedFormat('d F Y H:i:s') . ' WIB',
            'accessed_by' => $user->name,
        ];
    }

    /**
     * Format Nomor Siswa (NOSIK): KODE_SATDIK.TAHUN.BATCH.NOMOR_URUT
     * Contoh: SECABA.2026.1.001
     */
    public function generateNosik(Satdik $satdik, EducationProgram $program): string
    {
        $year = $program->academic_year ?? date('Y');
        $batch = $program->batch_number ?? 1;

        $count = Student::withoutGlobalScopes()
            ->where('education_program_id', $program->id)
            ->count() + 1;

        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);

        return "{$satdik->code}.{$year}.{$batch}.{$sequence}";
    }
}
