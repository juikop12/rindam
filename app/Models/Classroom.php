<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Classroom extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::saving(function (Classroom $classroom) {
            // Jika name kosong, rangkai otomatis dari company dan platoon
            if (empty($classroom->name)) {
                $parts = array_filter([$classroom->company, $classroom->platoon]);
                $classroom->name = !empty($parts) ? implode(' ', $parts) : 'Peleton Tanpa Nama';
            }

            // Jika code kosong, generate otomatis
            if (empty($classroom->code)) {
                $base = !empty($classroom->name) ? $classroom->name : 'CLS-' . mt_rand(100, 999);
                $prog = $classroom->educationProgram;
                $prefix = $prog ? $prog->code : 'RND';
                $classroom->code = strtoupper(Str::slug($prefix . '-' . $base, '-'));
            }
        });
    }

    public function educationProgram(): BelongsTo
    {
        return $this->belongsTo(EducationProgram::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Hitung sisa kuota kapasitas serdik pada peleton ini
     */
    public function getRemainingCapacityAttribute(): int
    {
        $current = $this->students()->count();
        return max(0, (int)$this->capacity - $current);
    }
}
