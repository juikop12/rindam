<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\EducationProgram;
use App\Models\Satdik;
use App\Models\Student;
use App\Models\StudentHealthRecord;
use App\Models\StudentPersonalDataAccessLog;
use App\Models\StudentPersonalProfile;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SystemCleanupService
{
    /**
     * Dapatkan statistik terkini jumlah data dalam sistem
     */
    public function getStatistics(): array
    {
        $satdiks = Satdik::orderBy('id')->get()->map(function ($satdik) {
            return [
                'id' => $satdik->id,
                'code' => $satdik->code,
                'name' => $satdik->name,
                'students_count' => Student::withTrashed()->where('satdik_id', $satdik->id)->count(),
                'programs_count' => EducationProgram::where('satdik_id', $satdik->id)->count(),
            ];
        });

        return [
            'total_students' => Student::withTrashed()->count(),
            'active_students' => Student::where('status', 'Aktif')->count(),
            'health_records' => StudentHealthRecord::count(),
            'personal_profiles' => StudentPersonalProfile::count(),
            'audit_logs' => StudentPersonalDataAccessLog::count(),
            'education_programs' => EducationProgram::count(),
            'classrooms' => Classroom::count(),
            'users' => User::count(),
            'satdiks' => $satdiks,
        ];
    }

    /**
     * Eksekusi pengosongan data berdasarkan opsi yang dipilih
     */
    public function wipeData(string $mode, ?int $satdikId = null, bool $wipePrograms = false): array
    {
        $driver = DB::getDriverName();
        $isMysql = in_array($driver, ['mysql', 'mariadb']);

        $summary = [
            'mode' => $mode,
            'students' => 0,
            'health_records' => 0,
            'personal_profiles' => 0,
            'audit_logs' => 0,
            'classrooms' => 0,
            'education_programs' => 0,
            'users' => 0,
        ];

        DB::transaction(function () use ($mode, $satdikId, $wipePrograms, $isMysql, &$summary) {
            Schema::disableForeignKeyConstraints();

            switch ($mode) {
                case 'students_only':
                    $summary['audit_logs'] = StudentPersonalDataAccessLog::count();
                    $summary['health_records'] = StudentHealthRecord::count();
                    $summary['personal_profiles'] = StudentPersonalProfile::count();
                    $summary['students'] = Student::withTrashed()->count();

                    DB::table('student_personal_data_access_logs')->delete();
                    DB::table('student_health_records')->delete();
                    DB::table('student_personal_profiles')->delete();
                    DB::table('students')->delete();

                    if ($isMysql) {
                        DB::statement('ALTER TABLE student_personal_data_access_logs AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE student_health_records AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE student_personal_profiles AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE students AUTO_INCREMENT = 1');
                    }
                    break;

                case 'students_and_programs':
                    $summary['audit_logs'] = StudentPersonalDataAccessLog::count();
                    $summary['health_records'] = StudentHealthRecord::count();
                    $summary['personal_profiles'] = StudentPersonalProfile::count();
                    $summary['students'] = Student::withTrashed()->count();
                    $summary['classrooms'] = Classroom::count();
                    $summary['education_programs'] = EducationProgram::count();

                    DB::table('student_personal_data_access_logs')->delete();
                    DB::table('student_health_records')->delete();
                    DB::table('student_personal_profiles')->delete();
                    DB::table('students')->delete();
                    DB::table('classrooms')->delete();
                    DB::table('education_programs')->delete();

                    if ($isMysql) {
                        DB::statement('ALTER TABLE student_personal_data_access_logs AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE student_health_records AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE student_personal_profiles AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE students AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE classrooms AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE education_programs AUTO_INCREMENT = 1');
                    }
                    break;

                case 'audit_logs_only':
                    $summary['audit_logs'] = StudentPersonalDataAccessLog::count();
                    DB::table('student_personal_data_access_logs')->delete();

                    if ($isMysql) {
                        DB::statement('ALTER TABLE student_personal_data_access_logs AUTO_INCREMENT = 1');
                    }
                    break;

                case 'specific_satdik':
                    if ($satdikId) {
                        $studentIds = Student::withTrashed()
                            ->where('satdik_id', $satdikId)
                            ->pluck('id')
                            ->toArray();

                        if (!empty($studentIds)) {
                            $summary['audit_logs'] = DB::table('student_personal_data_access_logs')->whereIn('student_id', $studentIds)->delete();
                            $summary['health_records'] = DB::table('student_health_records')->whereIn('student_id', $studentIds)->delete();
                            $summary['personal_profiles'] = DB::table('student_personal_profiles')->whereIn('student_id', $studentIds)->delete();
                            $summary['students'] = DB::table('students')->whereIn('id', $studentIds)->delete();
                        }

                        if ($wipePrograms) {
                            $programIds = EducationProgram::where('satdik_id', $satdikId)->pluck('id')->toArray();
                            if (!empty($programIds)) {
                                $summary['classrooms'] = DB::table('classrooms')->whereIn('education_program_id', $programIds)->delete();
                                $summary['education_programs'] = DB::table('education_programs')->whereIn('id', $programIds)->delete();
                            }
                        }
                    }
                    break;

                case 'full_reset':
                    $summary['audit_logs'] = StudentPersonalDataAccessLog::count();
                    $summary['health_records'] = StudentHealthRecord::count();
                    $summary['personal_profiles'] = StudentPersonalProfile::count();
                    $summary['students'] = Student::withTrashed()->count();
                    $summary['classrooms'] = Classroom::count();
                    $summary['education_programs'] = EducationProgram::count();

                    DB::table('student_personal_data_access_logs')->delete();
                    DB::table('student_health_records')->delete();
                    DB::table('student_personal_profiles')->delete();
                    DB::table('students')->delete();
                    DB::table('classrooms')->delete();
                    DB::table('education_programs')->delete();

                    // Hapus user non-standar (pertahankan akun sistem utama & user saat ini)
                    $standardEmails = [
                        'superadmin@rindam.mil.id',
                        'danrindam@rindam.mil.id',
                        'operator.danrindam@rindam.mil.id',
                        'operator.secaba@rindam.mil.id',
                        'operator.secata@rindam.mil.id',
                        'operator.dodikjur@rindam.mil.id',
                        'operator.dodiklatpur@rindam.mil.id',
                        'operator.belanegara@rindam.mil.id',
                        'tim.zi@rindam.mil.id',
                    ];
                    $currentUserId = auth()->id();
                    $deletedUsers = User::whereNotIn('email', $standardEmails)
                        ->when($currentUserId, fn($q) => $q->where('id', '!=', $currentUserId))
                        ->delete();
                    $summary['users'] = $deletedUsers;

                    if ($isMysql) {
                        DB::statement('ALTER TABLE student_personal_data_access_logs AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE student_health_records AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE student_personal_profiles AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE students AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE classrooms AUTO_INCREMENT = 1');
                        DB::statement('ALTER TABLE education_programs AUTO_INCREMENT = 1');
                    }
                    break;
            }

            Schema::enableForeignKeyConstraints();
        });

        // Bersihkan seluruh cache aplikasi agar metrik dashboard langsung kembali ke nol
        Cache::flush();

        // Catat jejak pengosongan ke log sistem untuk pertanggungjawaban keamanan militer
        $currentUser = auth()->user();
        Log::warning('PENGOSONGAN DATA SISTEM (SYSTEM DATA WIPE) BERHASIL DIJALANKAN', [
            'executed_by' => $currentUser ? "{$currentUser->name} ({$currentUser->email})" : 'CLI / System',
            'role' => $currentUser?->role_code,
            'ip' => request()->ip() ?? '127.0.0.1',
            'mode' => $mode,
            'satdik_id' => $satdikId,
            'summary' => $summary,
            'timestamp' => now()->toIso8601String(),
        ]);

        return $summary;
    }
}
