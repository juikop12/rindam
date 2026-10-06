<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom company dan platoon pada tabel classrooms
        Schema::table('classrooms', function (Blueprint $table) {
            $table->string('company', 100)->nullable()->after('name');
            $table->string('platoon', 100)->nullable()->after('company');
        });

        // 2. Tambah kolom company dan platoon pada tabel students
        Schema::table('students', function (Blueprint $table) {
            $table->string('company', 100)->nullable()->after('student_rank');
            $table->string('platoon', 100)->nullable()->after('company');
        });

        // 3. Migrasi data awal: ekstrak kompi dan peleton dari classrooms.name yang ada
        $classrooms = DB::table('classrooms')->get();
        foreach ($classrooms as $cls) {
            $company = null;
            $platoon = null;

            // Ekstrak Kompi (mis. "Kompi A", "Kompi Senapan B", "Kompi Bantuan")
            if (preg_match('/(Kompi\s+[A-Za-z0-9\s]+?)(?=\s+(?:Peleton|Ton)|$)/i', $cls->name, $mKompi)) {
                $company = trim($mKompi[1]);
            } elseif (preg_match('/(Kompi\s+[A-Za-z0-9]+)/i', $cls->name, $mKompi)) {
                $company = trim($mKompi[1]);
            }

            // Ekstrak Peleton (mis. "Peleton 1", "Peleton Taktik Senban", "Peleton Runduk")
            if (preg_match('/((?:Peleton|Ton)\s+[^,\n]+)/i', $cls->name, $mPlt)) {
                $platoon = trim($mPlt[1]);
            }

            if (!$company && !$platoon) {
                $company = $cls->name;
            }

            DB::table('classrooms')->where('id', $cls->id)->update([
                'company' => $company,
                'platoon' => $platoon,
            ]);
        }

        // 4. Sinkronisasikan kolom company dan platoon pada seluruh data students
        $students = DB::table('students')->whereNotNull('classroom_id')->get();
        foreach ($students as $stu) {
            $cls = DB::table('classrooms')->where('id', $stu->classroom_id)->first();
            if ($cls) {
                DB::table('students')->where('id', $stu->id)->update([
                    'company' => $cls->company,
                    'platoon' => $cls->platoon,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['company', 'platoon']);
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn(['company', 'platoon']);
        });
    }
};
