<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPersonalProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Pelindungan Data Pribadi via Native Laravel Eloquent Encrypted Casts (AES-256-CBC).
     * Seluruh nilai disimpan dalam bentuk ciphertext di database.
     */
    protected function casts(): array
    {
        return [
            'nik' => 'encrypted',
            'family_card_number' => 'encrypted',
            'mother_name' => 'encrypted',
            'father_name' => 'encrypted',
            'emergency_contact_name' => 'encrypted',
            'emergency_contact_phone' => 'encrypted',
            'home_address' => 'encrypted',
            'candidate_phone' => 'encrypted',
            'dapokdikma_raw_json' => 'array',
        ];
    }

    /**
     * Otomatiskan komputasi nik_hash saat menyimpan NIK
     */
    protected static function booted(): void
    {
        static::saving(function (StudentPersonalProfile $profile) {
            if (!empty($profile->nik)) {
                $profile->nik_hash = hash('sha256', trim((string)$profile->nik));
            }
        });
    }

    /**
     * Cari profil siswa berdasarkan NIK KTP (menggunakan blind index SHA-256)
     */
    public static function findByNik(?string $nik): ?self
    {
        if (empty($nik)) {
            return null;
        }
        $clean = trim((string)$nik);
        $hash = hash('sha256', $clean);
        return self::where('nik_hash', $hash)->first();
    }

    /**
     * Cek apakah NIK KTP sudah terdaftar di basis data
     */
    public static function isNikRegistered(string $nik, ?int $ignoreStudentId = null): bool
    {
        if (empty($nik)) {
            return false;
        }
        $clean = trim((string)$nik);
        $hash = hash('sha256', $clean);
        $query = self::where('nik_hash', $hash);
        if ($ignoreStudentId) {
            $query->where('student_id', '!=', $ignoreStudentId);
        }
        return $query->exists();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Accessor Penyamaran NIK (Contoh: 3201********5678)
     */
    public function getMaskedNikAttribute(): string
    {
        $val = $this->nik;
        if (empty($val)) return '-';
        if (strlen($val) < 8) return '****************';
        return substr($val, 0, 4) . '********' . substr($val, -4);
    }

    /**
     * Accessor Penyamaran No KK
     */
    public function getMaskedFamilyCardAttribute(): string
    {
        $val = $this->family_card_number;
        if (empty($val)) return '-';
        if (strlen($val) < 8) return '****************';
        return substr($val, 0, 4) . '********' . substr($val, -4);
    }

    /**
     * Accessor Penyamaran Nama Ibu Kandung (Contoh: S*** W***)
     */
    public function getMaskedMotherNameAttribute(): string
    {
        $val = $this->mother_name;
        if (empty($val)) return '-';
        $parts = explode(' ', trim($val));
        $maskedParts = array_map(function ($part) {
            if (strlen($part) <= 1) return $part;
            return substr($part, 0, 1) . str_repeat('*', max(strlen($part) - 1, 2));
        }, $parts);
        return implode(' ', $maskedParts);
    }

    /**
     * Accessor Penyamaran No HP Darurat (Contoh: 0812********89)
     */
    public function getMaskedEmergencyPhoneAttribute(): string
    {
        $val = $this->emergency_contact_phone;
        if (empty($val)) return '-';
        if (strlen($val) < 6) return '**********';
        return substr($val, 0, 4) . '********' . substr($val, -2);
    }
}
