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
        Schema::create('student_health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            
            // Jaminan Kesehatan
            $table->text('bpjs_number')->nullable(); // Terenkripsi AES-256
            
            // Fisik & Tanda Vital
            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('bmi', 4, 1)->nullable();
            $table->string('blood_pressure', 20)->nullable(); // contoh: 120/80 mmHg
            $table->unsignedSmallInteger('pulse_rate')->nullable(); // bpm
            
            // Status Kesiapan Latih & Stakes Militer
            $table->enum('daily_health_status', [
                'Siap Latih',
                'Berobat Jalan',
                'Rawat Inap Poliklinik',
                'Rujuk Rumkit'
            ])->default('Siap Latih');

            $table->string('stakes_grade', 50)->default('Stakes I (Sangat Baik)'); // Stakes I, II, III, IV
            
            // Rekam Medis & Riwayat Khusus
            $table->text('allergies')->nullable(); // Riwayat alergi obat / makanan
            $table->text('medical_history')->nullable(); // Riwayat sakit/operasi terdahulu
            $table->text('psychological_record')->nullable(); // Stakes Keswa / Catatan Mental
            
            // Administrasi Poliklinik & Rujukan
            $table->date('polyclinic_admission_date')->nullable();
            $table->string('referral_hospital', 150)->nullable(); // e.g. Rumkit Tk. II dr. Soedjono Magelang
            $table->text('doctor_notes')->nullable(); // Catatan Dokter / Paramedis Satdik
            $table->string('examined_by', 100)->nullable(); // Nama Dokter/Bintara Kesehatan
            $table->timestamp('last_examined_at')->nullable();
            
            $table->timestamps();

            $table->index(['daily_health_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_health_records');
    }
};
