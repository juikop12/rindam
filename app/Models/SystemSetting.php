<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Dapatkan nilai pengaturan berdasarkan kunci dengan cache
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $settings = static::getAllKeyValues();
        return $settings[$key] ?? $default;
    }

    /**
     * Simpan / perbarui nilai pengaturan
     */
    public static function set(string $key, ?string $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget('app_system_settings_all');
    }

    /**
     * Ambil seluruh pengaturan dalam bentuk associative array [key => value]
     */
    public static function getAllKeyValues(): array
    {
        return Cache::remember('app_system_settings_all', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Hapus cache saat model berubah
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('app_system_settings_all');
        });

        static::deleted(function () {
            Cache::forget('app_system_settings_all');
        });
    }
}
