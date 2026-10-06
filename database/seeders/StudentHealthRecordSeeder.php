<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentHealthRecord;
use Illuminate\Database\Seeder;

class StudentHealthRecordSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();

        foreach ($students as $idx => $student) {
            $existing = StudentHealthRecord::where('student_id', $student->id)->first();
            if ($existing) continue;

            $status = match($idx) {
                1 => 'Berobat Jalan',
                2 => 'Rujuk Rumkit',
                3 => 'Rawat Inap Poliklinik',
                default => 'Siap Latih',
            };

            $stakes = match($status) {
                'Berobat Jalan' => 'Stakes II (Baik)',
                'Rujuk Rumkit' => 'Stakes IV (TMS Sementara)',
                'Rawat Inap Poliklinik' => 'Stakes III (Kurang/Dispen)',
                default => 'Stakes I (Sangat Baik)',
            };

            $allergies = match($idx) {
                0 => 'Alergi Dingin & Debu',
                2 => 'Alergi Seafood / Udang',
                default => 'Tidak Ada Riwayat Alergi',
            };

            $history = match($idx) {
                1 => 'Cedera pergelangan kaki kanan ringan saat Hanmars, tahap pemulihan fisik.',
                2 => 'Gejala Febris Tifoid & dehidrasi sedang pasca latihan berganda.',
                3 => 'Gejala Febris (Demam 38.2 C) & Dehidrasi Ringan pasca latihan lapangan.',
                default => 'Tidak memiliki riwayat penyakit menahun atau kelainan fisik bawaan.',
            };

            $doctorNotes = match($status) {
                'Siap Latih' => 'Tanda vital dalam batas normal. Layak dan aman mengikuti latihan fisik lapangan penuh.',
                'Berobat Jalan' => 'Diberikan terapi analgesik & fisioterapi ringan. Dispensasi lari jauh 3 hari.',
                'Rujuk Rumkit' => 'Rujukan rawat inap Rumkit Tk. II dr. Soedjono Magelang: Observasi Febris Tifoid & terapi infus intensif.',
                'Rawat Inap Poliklinik' => 'Bed rest di Ruang Rawat Poliklinik Satdik, infus Ringer Laktat 20 tpm, pantau suhu tiap 4 jam.',
            };

            $height = rand(167, 178);
            $weight = rand(61, 74);
            $heightM = $height / 100;
            $bmi = round($weight / ($heightM * $heightM), 1);

            StudentHealthRecord::create([
                'student_id' => $student->id,
                'bpjs_number' => '000' . rand(1234567890, 9876543210),
                'height_cm' => $height,
                'weight_kg' => $weight,
                'bmi' => $bmi,
                'blood_pressure' => '120/80',
                'pulse_rate' => rand(68, 76),
                'daily_health_status' => $status,
                'stakes_grade' => $stakes,
                'allergies' => $allergies,
                'medical_history' => $history,
                'psychological_record' => 'Stakes Keswa Bintal Baik (Kategori B), Jiwa Korsa & Emosi Stabil',
                'polyclinic_admission_date' => in_array($status, ['Rawat Inap Poliklinik', 'Rujuk Rumkit']) ? now()->subDay()->toDateString() : null,
                'referral_hospital' => match($status) {
                    'Rujuk Rumkit' => 'Rumkit Tk. II dr. Soedjono Magelang',
                    'Rawat Inap Poliklinik' => 'Poliklinik Satdik Rindam',
                    default => null,
                },
                'doctor_notes' => $doctorNotes,
                'examined_by' => 'dr. Kapten Ckm Hendra Gunawan, Sp.KO',
                'last_examined_at' => now()->subHours(rand(2, 48)),
            ]);
        }
    }
}
