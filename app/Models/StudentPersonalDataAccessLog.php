<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPersonalDataAccessLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'accessed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function accessedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accessed_by_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accessed_by_user_id');
    }
}
