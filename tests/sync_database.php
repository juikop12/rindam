<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== MENYINKRONKAN STATUS DATA SERDIK DENGAN KESEHATAN SERDIK ===\n";

$students = App\Models\Student::with('healthRecord')->get();

foreach ($students as $student) {
    $hr = $student->healthRecord;
    if (!$hr) {
        $student->healthRecord()->create([
            'daily_health_status' => ($student->status === 'Sakit' ? 'Berobat Jalan' : 'Siap Latih'),
            'stakes_grade' => ($student->status === 'Sakit' ? 'Stakes II (Baik)' : 'Stakes I (Sangat Baik)'),
            'last_examined_at' => now(),
        ]);
        continue;
    }

    if ($hr->daily_health_status === 'Siap Latih') {
        if ($student->status === 'Sakit') {
            $hr->update(['daily_health_status' => 'Berobat Jalan', 'stakes_grade' => 'Stakes II (Baik)']);
        } else {
            $student->update(['status' => 'Aktif']);
        }
    } else {
        // Berobat Jalan / Rawat Inap / Rujuk
        $student->update(['status' => 'Sakit']);
    }
}

echo "Sinkronisasi selesai!\n\n";

// Tampilkan hasil
$students = App\Models\Student::with(['healthRecord', 'satdik'])->get();
foreach ($students as $s) {
    printf(
        "ID: %-2d | %-25s | Satdik: %-10s | Status Siswa: %-7s | Status Kesehatan: %-22s | Unified: %s\n",
        $s->id,
        $s->full_name,
        $s->satdik->code ?? '-',
        $s->status,
        $s->healthRecord?->daily_health_status ?? '-',
        $s->unified_status['label']
    );
}
