<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationProgram extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'batch_number' => 'integer',
        ];
    }

    public function satdik(): BelongsTo
    {
        return $this->belongsTo(Satdik::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function getCountedStudentsCountAttribute(): int
    {
        return $this->students()->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count();
    }

    public function getActiveStudentsCountAttribute(): int
    {
        return $this->students()->where('status', 'Aktif')->count();
    }

    public function getSickStudentsCountAttribute(): int
    {
        return $this->students()->where('status', 'Sakit')->count();
    }

    public function getArchivedStudentsCountAttribute(): int
    {
        return $this->students()->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan'])->count();
    }

    public function getFinishedStudentsCountAttribute(): int
    {
        return $this->students()->whereIn('status', ['Selesai', 'Lulus'])->count();
    }
}
