<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$students = App\Models\Student::with(['healthRecord', 'satdik'])->get();

echo "=== DAFTAR STATUS SISWA vs KESEHATAN ===\n";
foreach ($students as $s) {
    printf(
        "ID: %-2d | %-25s | Satdik: %-15s | Status Siswa: %-10s | Status Kesehatan: %s\n",
        $s->id,
        $s->full_name,
        $s->satdik->code ?? '-',
        $s->status,
        $s->healthRecord?->daily_health_status ?? 'BELUM ADA'
    );
}

// Cek jumlah serdik per satdik di Student vs di Health
echo "\n=== JUMLAH SISWA PER SATDIK ===\n";
$satdiks = App\Models\Satdik::all();
foreach ($satdiks as $st) {
    $countStudent = App\Models\Student::where('satdik_id', $st->id)->count();
    $countHealth = App\Models\StudentHealthRecord::whereHas('student', function($q) use ($st) {
        $q->where('satdik_id', $st->id);
    })->count();
    printf("Satdik %-18s: Siswa = %d | Health = %d\n", $st->name, $countStudent, $countHealth);
}
