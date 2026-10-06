<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Satdik;
use App\Models\EducationProgram;
use App\Models\Classroom;
use App\Models\StudentPersonalProfile;
use App\Models\StudentHealthRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(array_merge(
            array_fill(0, 85, 'Aktif'), // 85% Aktif
            array_fill(0, 5, 'Sakit'), // 5% Sakit
            array_fill(0, 5, 'Dinas Luar'), // 5% Dinas Luar
            array_fill(0, 3, 'DO / Dikeluarkan'), // 3% DO
            array_fill(0, 2, 'Lulus') // 2% Lulus
        ));

        // Generate Nosis/Nosik yang unik
        $nosik = '2026.' . fake()->unique()->numerify('##.###');

        return [
            'nosik' => $nosik,
            'full_name' => fake()->name('male'),
            'student_rank' => fake()->randomElement(['Siswa Prajurit', 'Siswa Bintara', 'Siswa Tamtama']),
            'origin_military_unit' => 'Kodim 0' . fake()->numberBetween(601, 623) . ' / ' . fake()->city(),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->dateTimeBetween('-24 years', '-18 years')->format('Y-m-d'),
            'gender' => 'L',
            'religion' => fake()->randomElement(['Islam', 'Islam', 'Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'blood_type' => fake()->randomElement(['A', 'B', 'AB', 'O']),
            'education_level' => fake()->randomElement(['SMA', 'SMK', 'MA', 'D3', 'S1']),
            'status' => $status,
            'company' => 'Kompi ' . fake()->randomElement(['A', 'B', 'C']),
            'platoon' => 'Peleton ' . fake()->numberBetween(1, 3),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Student $student) {
            // 1. Buat Data Pribadi Sensitif (Otomatis terenkripsi di model jika diset)
            StudentPersonalProfile::create([
                'student_id' => $student->id,
                'nik' => fake()->unique()->numerify('327#############'),
                'family_card_number' => fake()->numerify('327#############'),
                'mother_name' => fake()->name('female'),
                'father_name' => fake()->name('male'),
                'emergency_contact_name' => fake()->name(),
                'emergency_contact_phone' => fake()->phoneNumber(),
                'bpjs_number' => fake()->numerify('000########'),
                'bank_name' => fake()->randomElement(['BRI', 'BNI', 'Mandiri', 'BJB']),
                'bank_account_number' => fake()->numerify('0###-01-######-##-#'),
                'home_address' => fake()->address(),
                'medical_history' => fake()->randomElement(['Tidak ada', 'Tidak ada', 'Alergi dingin', 'Asma ringan (terkontrol)']),
                'psychological_record' => 'Stakes II (Baik)',
                'initial_physical_record' => 'Lulus Garjas A & B',
            ]);

            // 2. Buat Data Rekam Kesehatan (Default)
            $dailyHealth = 'Siap Latih';
            $stakes = 'Stakes I (Sangat Baik)';
            
            if ($student->status === 'Sakit') {
                $dailyHealth = fake()->randomElement(['Berobat Jalan', 'Rawat Inap Poliklinik', 'Rujuk Rumkit']);
                $stakes = 'Stakes II (Baik)';
            }

            StudentHealthRecord::create([
                'student_id' => $student->id,
                'blood_pressure' => fake()->numberBetween(110, 130) . '/' . fake()->numberBetween(70, 90),
                'pulse_rate' => fake()->numberBetween(60, 90),
                'weight_kg' => fake()->numberBetween(60, 85),
                'height_cm' => fake()->numberBetween(165, 185),
                'bmi' => 22.5,
                'daily_health_status' => $dailyHealth,
                'stakes_grade' => $stakes,
                'doctor_notes' => $student->status === 'Sakit' ? 'Membutuhkan observasi medis.' : null,
                'last_examined_at' => now(),
            ]);
        });
    }
}
