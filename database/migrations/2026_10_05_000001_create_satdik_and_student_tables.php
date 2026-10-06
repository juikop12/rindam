<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Satdik (Satuan Pendidikan)
        Schema::create('satdiks', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // SECABA, SECATA, DODIKJUR, DODIKLATPUR, BELANEGARA
            $table->string('name', 150); // Sekolah Calon Bintara, dll.
            $table->string('commander_title', 100)->default('Komandan Satuan Pendidikan');
            $table->string('commander_name', 150)->nullable();
            $table->string('location', 200)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Program Pendidikan per Satdik
        Schema::create('education_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satdik_id')->constrained('satdiks')->cascadeOnDelete();
            $table->string('code', 50)->unique(); // DIKTUKBA-2026-I
            $table->string('name', 150); // Diktukba TNI AD Gel II TA 2026
            $table->string('academic_year', 10)->default('2026');
            $table->unsignedInteger('batch_number')->default(1);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['Perencanaan', 'Berjalan', 'Selesai', 'Ditutup'])->default('Berjalan');
            $table->timestamps();
        });

        // 3. Rombongan Belajar / Kompi / Peleton
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('education_program_id')->constrained('education_programs')->cascadeOnDelete();
            $table->string('code', 50); // KMP-A-PLT-1
            $table->string('name', 100); // Kompi A Peleton 1
            $table->string('platoon_leader_name', 150)->nullable(); // Komandan Peleton / Danton
            $table->unsignedInteger('capacity')->default(40);
            $table->timestamps();
            
            $table->unique(['education_program_id', 'code']);
        });

        // 4. Update tabel users untuk mendukung relasi Satdik dan peran
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('satdik_id')->nullable()->after('email')->constrained('satdiks')->nullOnDelete();
            $table->string('role_code', 50)->default('operator_satdik')->after('satdik_id'); // super_admin, pimpinan, operator_satdik, tim_zi, gadik, siswa
            $table->string('phone', 30)->nullable()->after('role_code');
        });

        // 5. Data Pokok Siswa (Identitas Publik Militer)
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satdik_id')->constrained('satdiks')->cascadeOnDelete();
            $table->foreignId('education_program_id')->constrained('education_programs')->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('nosik', 40)->unique(); // Nomor Siswa Resmi (mis. 2026.01.001)
            $table->string('full_name', 150);
            $table->string('student_rank', 50)->default('Siswa Prajurit');
            $table->string('origin_military_unit', 100)->nullable(); // Satuan Pengirim: Kodim 0501 / Korem 052
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 10)->default('L');
            $table->string('religion', 30)->nullable();
            $table->string('blood_type', 10)->nullable();
            $table->string('education_level', 50)->nullable(); // SMA / SMK / S1
            $table->string('photo_path')->nullable();
            $table->enum('status', ['Aktif', 'Sakit', 'Dinas Luar', 'DO / Dikeluarkan', 'Lulus'])->default('Aktif');
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['satdik_id', 'status']);
            $table->index(['education_program_id', 'classroom_id']);
        });

        // 6. Data Pribadi Sensitif Serdik (Terenkripsi AES-256 sesuai UU PDP No. 27/2022)
        Schema::create('student_personal_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            
            // Kolom disimpan dalam format ciphertext terenkripsi Laravel Eloquent
            $table->text('nik'); // Nomor Induk Kependudukan (16 digit)
            $table->text('family_card_number')->nullable(); // No Kartu Keluarga
            $table->text('mother_name')->nullable(); // Nama Ibu Kandung
            $table->text('father_name')->nullable(); // Nama Ayah
            $table->text('emergency_contact_name')->nullable();
            $table->text('emergency_contact_phone')->nullable();
            $table->text('bpjs_number')->nullable();
            $table->text('bank_name')->nullable(); // Bank Penyalur ULP / Uang Saku
            $table->text('bank_account_number')->nullable();
            $table->text('home_address')->nullable(); // Alamat Asal
            
            // Data Rekam Medis & Psikologi
            $table->text('medical_history')->nullable(); // Riwayat Penyakit & Alergi Obat
            $table->text('psychological_record')->nullable(); // Stakes Keswa & Catatan Bintal
            $table->text('initial_physical_record')->nullable(); // Garjas Awal Masuk Diklat
            
            $table->timestamps();
        });

        // 7. Audit Log Akses Data Pribadi Sensitif (Pasal 16 & 28 UU PDP)
        Schema::create('student_personal_data_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('accessed_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('access_reason', 255); // Alasan pembukaan data (mis. Verifikasi Medis)
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamp('accessed_at');

            $table->index(['student_id', 'accessed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_personal_data_access_logs');
        Schema::dropIfExists('student_personal_profiles');
        Schema::dropIfExists('students');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['satdik_id']);
            $table->dropColumn(['satdik_id', 'role_code', 'phone']);
        });
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('education_programs');
        Schema::dropIfExists('satdiks');
    }
};
