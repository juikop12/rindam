<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function satdik(): BelongsTo
    {
        return $this->belongsTo(Satdik::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Cek apakah user adalah Pimpinan Satuan / Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role_code === 'super_admin';
    }

    public function isPimpinan(): bool
    {
        return in_array($this->role_code, ['super_admin', 'pimpinan', 'kabag_diklat', 'kabag_dik']);
    }

    /**
     * Cek apakah user berhak mengakses Satdik tertentu
     */
    public function canAccessSatdik(?int $satdikId): bool
    {
        if ($this->isPimpinan()) {
            return true;
        }

        return $this->satdik_id === $satdikId;
    }
}
