<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_personal_profiles', function (Blueprint $table) {
            // Data Psikologi & IQ
            $table->unsignedSmallInteger('iq_score')->nullable()->after('home_address');
            $table->decimal('psychology_score', 5, 2)->nullable()->after('iq_score');
            $table->string('psychology_grade', 50)->nullable()->after('psychology_score'); // PSI 1, PSI 2, etc.
            $table->string('branch_recommendations', 150)->nullable()->after('psychology_grade'); // ARH, ARM, INF, etc.

            // Data Kesamaptaan Jasmani (Garjas)
            $table->decimal('physical_fitness_score', 5, 2)->nullable()->after('branch_recommendations'); // Nilai Akhir Jas
            $table->string('physical_fitness_grade', 30)->nullable()->after('physical_fitness_score'); // MS / TMS
            $table->unsignedInteger('run_12m_distance')->nullable()->after('physical_fitness_grade'); // meter
            $table->decimal('run_12m_score', 5, 2)->nullable()->after('run_12m_distance');
            $table->unsignedSmallInteger('pull_ups_count')->nullable()->after('run_12m_score');
            $table->decimal('pull_ups_score', 5, 2)->nullable()->after('pull_ups_count');
            $table->unsignedSmallInteger('sit_ups_count')->nullable()->after('pull_ups_score');
            $table->decimal('sit_ups_score', 5, 2)->nullable()->after('sit_ups_count');
            $table->unsignedSmallInteger('push_ups_count')->nullable()->after('sit_ups_score');
            $table->decimal('push_ups_score', 5, 2)->nullable()->after('push_ups_count');
            $table->decimal('shuttle_run_seconds', 4, 1)->nullable()->after('push_ups_score');
            $table->decimal('shuttle_run_score', 5, 2)->nullable()->after('shuttle_run_seconds');
            $table->decimal('swimming_score', 5, 2)->nullable()->after('shuttle_run_score');
            $table->string('swimming_style', 30)->nullable()->after('swimming_score');

            // Data Litpers / Mental Ideologi & Akademik
            $table->string('litpers_grade', 30)->nullable()->after('swimming_style'); // MS / TMS
            $table->decimal('litpers_written_score', 5, 2)->nullable()->after('litpers_grade');
            $table->decimal('litpers_interview_score', 5, 2)->nullable()->after('litpers_written_score');
            $table->decimal('final_selection_score', 5, 2)->nullable()->after('litpers_interview_score');
            $table->string('selection_test_number', 60)->nullable()->after('final_selection_score');

            // Kontak & Informasi Tambahan Calon / Ortu
            $table->string('candidate_phone', 40)->nullable()->after('selection_test_number');
            $table->string('candidate_email', 100)->nullable()->after('candidate_phone');
            $table->string('parent_occupation', 100)->nullable()->after('candidate_email');
            $table->string('origin_school', 150)->nullable()->after('parent_occupation');
            $table->string('academic_major', 60)->nullable()->after('origin_school');
            $table->decimal('academic_score', 5, 2)->nullable()->after('academic_major');
            $table->unsignedSmallInteger('graduation_year')->nullable()->after('academic_score');
            $table->string('ethnicity', 50)->nullable()->after('graduation_year');
            $table->string('keswa_grade', 30)->nullable()->after('ethnicity');

            // Payload lengkap 123 atribut Dapokdikma (JSON terenkripsi/terstruktur)
            $table->longText('dapokdikma_raw_json')->nullable()->after('keswa_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_personal_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'iq_score', 'psychology_score', 'psychology_grade', 'branch_recommendations',
                'physical_fitness_score', 'physical_fitness_grade',
                'run_12m_distance', 'run_12m_score',
                'pull_ups_count', 'pull_ups_score',
                'sit_ups_count', 'sit_ups_score',
                'push_ups_count', 'push_ups_score',
                'shuttle_run_seconds', 'shuttle_run_score',
                'swimming_score', 'swimming_style',
                'litpers_grade', 'litpers_written_score', 'litpers_interview_score',
                'final_selection_score', 'selection_test_number',
                'candidate_phone', 'candidate_email', 'parent_occupation',
                'origin_school', 'academic_major', 'academic_score', 'graduation_year',
                'ethnicity', 'keswa_grade', 'dapokdikma_raw_json'
            ]);
        });
    }
};
