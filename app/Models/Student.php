<?php

namespace App\Models;

use App\Models\Scopes\SatdikScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([SatdikScope::class])]
class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Student $student) {
            // Sinkronisasi otomatis kompi dan peleton bila classroom_id disematkan
            if ($student->classroom_id) {
                $classroom = $student->classroom ?? Classroom::find($student->classroom_id);
                if ($classroom) {
                    if (empty($student->company) && !empty($classroom->company)) {
                        $student->company = $classroom->company;
                    }
                    if (empty($student->platoon) && !empty($classroom->platoon)) {
                        $student->platoon = $classroom->platoon;
                    }
                }
            }
        });

        static::saved(function (Student $student) {
            // Sinkronisasi status kesehatan ke StudentHealthRecord jika ada
            $healthRecord = $student->healthRecord()->first();
            if (!$healthRecord) return;

            if ($student->status === 'Aktif' && $healthRecord->daily_health_status !== 'Siap Latih') {
                $healthRecord->withoutEvents(function () use ($healthRecord) {
                    $healthRecord->update([
                        'daily_health_status' => 'Siap Latih',
                        'last_examined_at' => now(),
                    ]);
                });
            } elseif ($student->status === 'Sakit' && $healthRecord->daily_health_status === 'Siap Latih') {
                $healthRecord->withoutEvents(function () use ($healthRecord) {
                    $healthRecord->update([
                        'daily_health_status' => 'Berobat Jalan',
                        'stakes_grade' => 'Stakes II (Baik)',
                        'last_examined_at' => now(),
                    ]);
                });
            }
        });
    }

    public function getCompanyAttribute($value): ?string
    {
        if (!empty($value)) {
            return $value;
        }
        return $this->classroom?->company ?? null;
    }

    public function getPlatoonAttribute($value): ?string
    {
        if (!empty($value)) {
            return $value;
        }
        return $this->classroom?->platoon ?? null;
    }

    /**
     * Sinkronisasi Status Serdik antara Binsatdik & Poliklinik Kesehatan
     */
    public function getUnifiedStatusAttribute(): array
    {
        $daily = $this->healthRecord?->daily_health_status ?? ($this->status === 'Sakit' ? 'Berobat Jalan' : 'Siap Latih');

        if ($this->status === 'DO / Dikeluarkan') {
            return ['label' => 'DO / Dikeluarkan (Arsip)', 'badge' => 'badge-red', 'icon' => 'cancel', 'is_counted' => false];
        }
        if ($this->status === 'Lulus' || $this->status === 'Selesai') {
            return ['label' => 'Selesai Pendidikan (Arsip)', 'badge' => 'badge-blue', 'icon' => 'school', 'is_counted' => false];
        }
        if ($this->status === 'Dinas Luar') {
            return ['label' => 'Dinas Luar', 'badge' => 'badge-blue', 'icon' => 'flight_takeoff', 'is_counted' => true];
        }

        return match($daily) {
            'Siap Latih' => ['label' => 'Aktif (Siap Latih)', 'badge' => 'badge-green', 'icon' => 'check_circle', 'is_counted' => true],
            'Berobat Jalan' => ['label' => 'Sakit (Berobat Jalan)', 'badge' => 'badge-amber', 'icon' => 'healing', 'is_counted' => true],
            'Rawat Inap Poliklinik' => ['label' => 'Sakit (Rawat Poliklinik)', 'badge' => 'badge-red', 'icon' => 'local_hospital', 'is_counted' => true],
            'Rujuk Rumkit' => ['label' => 'Sakit (Rujuk Rumkit)', 'badge' => 'badge-blue', 'icon' => 'emergency', 'is_counted' => true],
            default => ['label' => $this->status, 'badge' => 'badge-satdik', 'icon' => 'help', 'is_counted' => !in_array($this->status, ['Selesai', 'Lulus', 'DO / Dikeluarkan'])],
        };
    }

    /**
     * Mengetahui apakah serdik terhitung dalam kuota siswa aktif berjalan
     */
    public function getIsCountedAttribute(): bool
    {
        return in_array($this->status, ['Aktif', 'Sakit', 'Dinas Luar']);
    }

    /**
     * Mengetahui apakah serdik berada di status arsip (selesai/lulus/keluar)
     */
    public function getIsArchivedAttribute(): bool
    {
        return in_array($this->status, ['Selesai', 'Lulus', 'DO / Dikeluarkan']);
    }

    public function satdik(): BelongsTo
    {
        return $this->belongsTo(Satdik::class);
    }

    public function educationProgram(): BelongsTo
    {
        return $this->belongsTo(EducationProgram::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Profil Pribadi Sensitif Terenkripsi (SIPANDU-WBK)
     */
    public function personalProfile(): HasOne
    {
        return $this->hasOne(StudentPersonalProfile::class);
    }

    /**
     * Rekam Medis & Kesehatan Serdik (Poliklinik Satdik)
     */
    public function healthRecord(): HasOne
    {
        return $this->hasOne(StudentHealthRecord::class);
    }

    /**
     * Jejak Audit Akses Data Pribadi Sensitif
     */
    public function accessLogs(): HasMany
    {
        return $this->hasMany(StudentPersonalDataAccessLog::class);
    }

    /**
     * Scope filter Satdik eksplisit
     */
    public function scopeBySatdik($query, $satdikId)
    {
        if (empty($satdikId)) return $query;
        return $query->where('satdik_id', $satdikId);
    }

    /**
     * Scope status aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Scope serdik terhitung (aktif & sakit dalam pendidikan)
     */
    public function scopeActiveCounted($query)
    {
        return $query->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar']);
    }

    /**
     * Scope serdik arsip (selesai, lulus, DO - tidak terhitung lagi)
     */
    public function scopeArchived($query)
    {
        return $query->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan']);
    }

    /**
     * Scope filter program pendidikan
     */
    public function scopeByProgram($query, $programId)
    {
        if (empty($programId)) return $query;
        return $query->where('education_program_id', $programId);
    }

    /**
     * Scope pencarian siswa (berdasar nosik, nama, atau asal kodim)
     */
    public function scopeSearch($query, ?string $keyword)
    {
        if (empty($keyword)) return $query;
        
        return $query->where(function ($q) use ($keyword) {
            $q->where('nosik', 'like', "%{$keyword}%")
              ->orWhere('full_name', 'like', "%{$keyword}%")
              ->orWhere('origin_military_unit', 'like', "%{$keyword}%");
        });
    }
}
