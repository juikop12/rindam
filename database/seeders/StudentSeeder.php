<?php

namespace Database\Seeders;

use App\Models\Satdik;
use App\Models\EducationProgram;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding data siswa...');

        $satdiks = Satdik::all();

        if ($satdiks->isEmpty()) {
            $this->command->error('Tidak ada data Satdik! Pastikan Anda sudah menjalankan DatabaseSeeder utama terlebih dahulu.');
            return;
        }

        foreach ($satdiks as $satdik) {
            $this->command->info("Memproses Satdik: {$satdik->code}");

            // Ambil program pendidikan pertama dari satdik ini
            $program = EducationProgram::where('satdik_id', $satdik->id)->first();

            if (!$program) {
                $this->command->warn("  -> Melewati {$satdik->code} karena tidak memiliki Program Pendidikan.");
                continue;
            }

            // Ambil daftar kelas dari program tersebut
            $classrooms = Classroom::where('education_program_id', $program->id)->get();
            $classroomIds = $classrooms->pluck('id')->toArray();

            // Jika tidak ada kelas, kita set null
            if (empty($classroomIds)) {
                $classroomIds = [null];
            }

            $this->command->info("  -> Membuat 200 Siswa untuk {$program->code}...");

            // Karena kita akan membuat relasi (Personal Profile & Health Record) 
            // di dalam event afterCreating factory, maka proses insert ini 
            // dilakukan via loop atau chunked factory creation agar event Model berjalan.

            $bar = $this->command->getOutput()->createProgressBar(200);
            
            for ($i = 0; $i < 200; $i++) {
                // Pilih kelas secara acak jika ada
                $classroomId = $classroomIds[array_rand($classroomIds)];

                Student::factory()->create([
                    'satdik_id' => $satdik->id,
                    'education_program_id' => $program->id,
                    'classroom_id' => $classroomId,
                    // Company & Platoon otomatis akan menyesuaikan dari classroom_id 
                    // karena ada static::saving di App\Models\Student
                ]);
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->command->info(""); // New line after progress bar
            $this->command->info("  -> Selesai membuat 200 siswa untuk {$satdik->code}.");
        }

        $this->command->info('Seeding data siswa selesai!');
    }
}
