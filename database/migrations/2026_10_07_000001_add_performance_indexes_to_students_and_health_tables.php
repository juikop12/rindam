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
        Schema::table('students', function (Blueprint $table) {
            $table->index('full_name', 'students_full_name_index');
            $table->index('origin_military_unit', 'students_origin_unit_index');
        });

        Schema::table('student_health_records', function (Blueprint $table) {
            $table->index('stakes_grade', 'health_stakes_grade_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_health_records', function (Blueprint $table) {
            $table->dropIndex('health_stakes_grade_index');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_origin_unit_index');
            $table->dropIndex('students_full_name_index');
        });
    }
};
