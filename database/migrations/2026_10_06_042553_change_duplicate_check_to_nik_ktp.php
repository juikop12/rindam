<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\StudentPersonalProfile;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Hapus unique constraint pada nosik di tabel students (NOSIK bukan lagi acuan duplikasi)
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique('students_nosik_unique');
            $table->index('nosik', 'students_nosik_index');
        });

        // 2. Tambahkan kolom blind index nik_hash pada student_personal_profiles untuk pencarian & deteksi duplikat NIK cepat
        Schema::table('student_personal_profiles', function (Blueprint $table) {
            $table->char('nik_hash', 64)->nullable()->after('nik')->index();
        });

        // 3. Populasi nik_hash untuk data serdik yang sudah ada
        try {
            $profiles = StudentPersonalProfile::all();
            foreach ($profiles as $profile) {
                if (!empty($profile->nik)) {
                    $nikValue = trim((string)$profile->nik);
                    DB::table('student_personal_profiles')
                        ->where('id', $profile->id)
                        ->update(['nik_hash' => hash('sha256', $nikValue)]);
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika tabel kosong
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_personal_profiles', function (Blueprint $table) {
            $table->dropColumn('nik_hash');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_nosik_index');
            $table->unique('nosik', 'students_nosik_unique');
        });
    }
};
