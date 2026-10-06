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
     * Cek apakah user adalah Super Administrator
     */
    public function isSuperAdmin(): bool
    {
        return $this->role_code === 'super_admin';
    }

    /**
     * Cek apakah user adalah Danrindam (Komandan Satuan - View Only)
     */
    public function isDanrindam(): bool
    {
        return in_array($this->role_code, ['pimpinan', 'danrindam']);
    }

    /**
     * Cek apakah user adalah Operator Danrindam (Hak Akses Penuh ke 5 Satdik)
     */
    public function isOperatorDanrindam(): bool
    {
        return in_array($this->role_code, ['operator_danrindam', 'operator_pusat']);
    }

    /**
     * Cek apakah user adalah Operator Satdik Lokal (Terkunci 1 Satdik)
     */
    public function isOperatorSatdik(): bool
    {
        return $this->role_code === 'operator_satdik';
    }

    /**
     * Cek apakah user memiliki hak melihat data lintas seluruh 5 Satdik
     */
    public function hasCrossSatdikAccess(): bool
    {
        return in_array($this->role_code, ['super_admin', 'pimpinan', 'danrindam', 'operator_danrindam', 'kabag_diklat', 'kabag_dik', 'tim_zi']);
    }

    public function isPimpinan(): bool
    {
        return $this->hasCrossSatdikAccess();
    }

    /**
     * Apakah user diperbolehkan merubah/menginput/menghapus data?
     * Danrindam & Tim ZI hanya bisa VIEW (read-only) tanpa bisa merubah data!
     */
    public function canModifyData(): bool
    {
        if ($this->isDanrindam() || $this->role_code === 'tim_zi') {
            return false;
        }

        return true;
    }

    /**
     * Cek apakah user berhak mengelola data serdik di Satdik tertentu
     */
    public function canManageSatdik(?int $satdikId): bool
    {
        if (!$this->canModifyData()) {
            return false;
        }

        if ($this->isSuperAdmin() || $this->isOperatorDanrindam()) {
            return true;
        }

        return $this->satdik_id === $satdikId;
    }

    /**
     * Cek apakah user berhak melihat data di Satdik tertentu
     */
    public function canAccessSatdik(?int $satdikId): bool
    {
        if ($this->hasCrossSatdikAccess()) {
            return true;
        }

        return $this->satdik_id === $satdikId;
    }

    public function isOperator(): bool
    {
        return in_array($this->role_code, ['operator_satdik', 'operator_danrindam']);
    }

    public function isTimZi(): bool
    {
        return $this->role_code === 'tim_zi';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role_code) {
            'super_admin' => 'Super Administrator',
            'pimpinan', 'danrindam' => 'Danrindam (Pimpinan - View Only)',
            'operator_danrindam' => 'Operator Danrindam (Akses Penuh)',
            'operator_satdik' => 'Operator Satdik',
            'tim_zi' => 'Tim Pengawasan ZI Area 5',
            'poliklinik' => 'Petugas Medis / Poliklinik',
            default => strtoupper($this->role_code ?? 'PENGGUNA'),
        };
    }

    public function getScopeLabelAttribute(): string
    {
        if ($this->hasCrossSatdikAccess() || empty($this->satdik_id)) {
            return 'Seluruh Satdik (Pusat)';
        }

        return $this->satdik?->code ?? 'Satdik Terbatas';
    }
}
