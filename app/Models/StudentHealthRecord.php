<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentHealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'height_cm',
        'weight_kg',
        'bmi',
        'blood_pressure',
        'pulse_rate',
        'daily_health_status',
        'stakes_grade',
        'allergies',
        'medical_history',
        'psychological_record',
        'polyclinic_admission_date',
        'referral_hospital',
        'doctor_notes',
        'examined_by',
        'last_examined_at',
    ];

    /**
     * Enkripsi data sensitif rekam medis menggunakan AES-256
     */
    protected $casts = [
        'allergies' => 'encrypted',
        'medical_history' => 'encrypted',
        'psychological_record' => 'encrypted',
        'doctor_notes' => 'encrypted',
        'last_examined_at' => 'datetime',
        'polyclinic_admission_date' => 'date',
        'weight_kg' => 'decimal:2',
        'bmi' => 'decimal:1',
    ];

    protected static function booted(): void
    {
        static::saved(function (StudentHealthRecord $healthRecord) {
            $student = $healthRecord->student;
            if (!$student) return;

            // Jangan timpa jika siswa telah DO, Selesai, Lulus, atau Dinas Luar
            if (in_array($student->status, ['DO / Dikeluarkan', 'Lulus', 'Selesai', 'Dinas Luar'])) {
                return;
            }

            if ($healthRecord->daily_health_status === 'Siap Latih') {
                if ($student->status !== 'Aktif') {
                    $student->withoutEvents(function () use ($student) {
                        $student->update(['status' => 'Aktif']);
                    });
                }
            } else {
                // Berobat Jalan, Rawat Inap Poliklinik, Rujuk Rumkit
                if ($student->status !== 'Sakit') {
                    $student->withoutEvents(function () use ($student) {
                        $student->update(['status' => 'Sakit']);
                    });
                }
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Hitung Nilai BMI Otomatis jika Tinggi & Berat Terisi
     */
    public function calculateBmi(): ?float
    {
        if ($this->height_cm > 0 && $this->weight_kg > 0) {
            $heightM = $this->height_cm / 100;
            return round($this->weight_kg / ($heightM * $heightM), 1);
        }
        return null;
    }

    /**
     * Badge status warna untuk UI
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->daily_health_status) {
            'Siap Latih' => ['class' => 'badge-green', 'icon' => 'check_circle', 'label' => 'Sehat'],
            'Berobat Jalan' => ['class' => 'badge-amber', 'icon' => 'healing', 'label' => 'Berobat Jalan / Dispen'],
            'Rawat Inap Poliklinik' => ['class' => 'badge-red', 'icon' => 'local_hospital', 'label' => 'Rawat Inap Poliklinik'],
            'Rujuk Rumkit' => ['class' => 'badge-blue', 'icon' => 'emergency', 'label' => 'Rujuk Rumkit'],
            default => ['class' => 'badge-satdik', 'icon' => 'help', 'label' => $this->daily_health_status],
        };
    }
}
