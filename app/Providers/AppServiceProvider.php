<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom');

        // Pastikan akses dari perangkat mobile / LAN (IP 192.168.x.x) selalu menggunakan
        // build aset terkompilasi agar tidak gagal memuat CSS dari localhost/::1 Vite dev server
        if (request() && file_exists(public_path('build/manifest.json'))) {
            $host = request()->getHost();
            $isLocalHost = in_array($host, ['localhost', '127.0.0.1', '::1', 'rindam.test']);
            if (!$isLocalHost) {
                \Illuminate\Support\Facades\Vite::useHotFile(storage_path('framework/non_existent_hot'));
            }
        }
    }
}
