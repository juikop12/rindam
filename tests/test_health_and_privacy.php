<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "======================================================================\n";
echo "  PENGUJIAN SISTEM: PEMISAHAN DATA SISWA & KESEHATAN (MENU TERPISAH)  \n";
echo "  PEMURNIAN DATA PRIBADI (TANPA PERBANKAN & TANPA BPJS)               \n";
echo "======================================================================\n\n";

$allPassed = true;

// 1. Pengujian Route GET
$getEndpoints = [
    'GET / (Dashboard Eksekutif Utama)' => '/',
    'GET /dashboard (Pusat Komando)' => '/dashboard',
    'GET /dashboard?satdik_id=1 (Filter Secaba)' => '/dashboard?satdik_id=1',
    'GET /students (Buku Induk Serdik)' => '/students',
    'GET /students/create (Pendaftaran)' => '/students/create',
    'GET /students/1 (Dossier Siswa)' => '/students/1',
    'GET /programs (Menu Program Satdik)' => '/programs',
    'GET /programs?satdik_id=1 (Program Secaba)' => '/programs?satdik_id=1',
    'GET /programs/1 (Detail Program DIKMABA)' => '/programs/1',
    'GET /health (Menu Terpisah Kesehatan)' => '/health',
    'GET /health?satdik_id=1 (Kesehatan Secaba)' => '/health?satdik_id=1',
    'GET /health/1 (Rekam Medis Serdik 1)' => '/health/1',
    'GET /health/1/edit (Form Update Kesehatan)' => '/health/1/edit',
    'GET /audit-logs (Pengawasan ZI Area 5)' => '/audit-logs',
    'GET /settings (Pengaturan Pejabat & Satuan)' => '/settings',
    'GET /students/import (Formulir Penginputan Excel)' => '/students/import',
    'GET /students/import/template (Unduh Template Excel)' => '/students/import/template?format=xlsx',
    'GET /students/import/template (Unduh Template CSV)' => '/students/import/template?format=csv',
];

foreach ($getEndpoints as $label => $uri) {
    $req = Illuminate\Http\Request::create($uri, 'GET');
    $res = $kernel->handle($req);
    $status = $res->getStatusCode();
    $ok = ($status === 200);
    if (!$ok) $allPassed = false;
    printf("%-45s -> Status: %d %s\n", $label, $status, $ok ? '[PASS]' : '[FAIL]');
}

// 2. Pengujian Reveal Data Pribadi (Memastikan TIDAK ADA perbankan, BPJS, atau Medis)
echo "\n--- PENGUJIAN 2: REVEAL DATA PRIBADI (VERIFIKASI PENGECUALIAN PERBANKAN & BPJS) ---\n";
$session = $app->make('session')->driver();
$session->start();

$user = App\Models\User::first();
Illuminate\Support\Facades\Auth::login($user);

$pdpReq = Illuminate\Http\Request::create('/students/1/reveal-sensitive', 'POST');
$pdpReq->setLaravelSession($session);
$pdpReq->headers->set('Accept', 'application/json');
$pdpReq->initialize([], [], [], [], [], [
    'CONTENT_TYPE' => 'application/json',
    'HTTP_ACCEPT' => 'application/json',
], json_encode(['access_reason' => 'Verifikasi keabsahan data domisili serdik']));

$pdpRes = $kernel->handle($pdpReq);
$pdpData = json_decode($pdpRes->getContent(), true);

$revealedFields = array_keys($pdpData['data'] ?? []);
echo "Field yang dikembalikan pada Data Pribadi:\n" . implode(', ', $revealedFields) . "\n";

$hasBanking = in_array('bank_name', $revealedFields) || in_array('bank_account_number', $revealedFields);
$hasBpjs = in_array('bpjs_number', $revealedFields);
$hasMedical = in_array('medical_history', $revealedFields);

echo "- Menampilkan Perbankan? " . ($hasBanking ? "YA [FAIL]" : "TIDAK [PASS]") . "\n";
echo "- Menampilkan BPJS?       " . ($hasBpjs ? "YA [FAIL]" : "TIDAK [PASS]") . "\n";
echo "- Menampilkan Medis?      " . ($hasMedical ? "YA [FAIL]" : "TIDAK [PASS]") . "\n";

if ($hasBanking || $hasBpjs || $hasMedical) {
    $allPassed = false;
}

// 3. Pengujian Modul Kesehatan: Update Status Kesehatan & Rekam Medis (Tanpa BPJS)
echo "\n--- PENGUJIAN 3: UPDATE STATUS KESEHATAN OLEH POLIKLINIK SATDIK (MURNI MEDIS) ---\n";
$updateHealthData = [
    '_token' => $session->token(),
    'daily_health_status' => 'Berobat Jalan',
    'stakes_grade' => 'Stakes II (Baik)',
    'height_cm' => 174,
    'weight_kg' => 70,
    'blood_pressure' => '115/75',
    'pulse_rate' => 70,
    'allergies' => 'Alergi Dingin & Debu',
    'medical_history' => 'Pemulihan cedera betis ringan pasca Hanmars',
    'psychological_record' => 'Kondisi psikologis stabil & siap latih terbatas',
    'referral_hospital' => 'Rumkit Tk. II Soedjono',
    'doctor_notes' => 'Dispensasi materi lari siang 2 hari. Terapi vitamin & analgesik.',
    'examined_by' => 'dr. Kapten Ckm Hendra Gunawan, Sp.KO',
];

$updateReq = Illuminate\Http\Request::create('/health/1', 'PUT', $updateHealthData);
$updateReq->setLaravelSession($session);
$updateRes = $kernel->handle($updateReq);
$updateStatus = $updateRes->getStatusCode();
printf("%-45s -> Status: %d %s\n", 'PUT /health/1 (Update Medis)', $updateStatus, in_array($updateStatus, [200, 302]) ? '[PASS]' : '[FAIL]');

// Verifikasi di basis data
$hr = App\Models\StudentHealthRecord::where('student_id', 1)->first();
$student = App\Models\Student::find(1);
echo "- Status Kesehatan Terkini: " . $hr->daily_health_status . "\n";
echo "- Stakes Militer: " . $hr->stakes_grade . "\n";
echo "- BMI Terhitung: " . $hr->bmi . "\n";
echo "- Rumkit Rujukan: " . ($hr->referral_hospital ?? '-') . "\n";

// 4. Pengujian Sinkronisasi Otomatis Status Serdik (Binsatdik <-> Poliklinik)
echo "\n--- PENGUJIAN 4: SINKRONISASI 2-ARAH STATUS SERDIK (BINSAT <-> KESEHATAN) ---\n";
echo "- Status Student (Binsat) setelah update kesehatan : " . $student->status . " (Unified: " . $student->unified_status['label'] . ")\n";
$syncPass = ($student->status === 'Sakit');
echo "- Status Tersinkron Otomatis? " . ($syncPass ? "YA [PASS]" : "TIDAK [FAIL]") . "\n";
if (!$syncPass) $allPassed = false;

// Uji pemulihan kembali ke Siap Latih
$hr->daily_health_status = 'Siap Latih';
$hr->save();
$student->refresh();
echo "- Status Student setelah kembali Siap Latih        : " . $student->status . " (Unified: " . $student->unified_status['label'] . ")\n";
$syncBackPass = ($student->status === 'Aktif');
echo "- Sinkronisasi Pemulihan Berhasil?                 " . ($syncBackPass ? "YA [PASS]" : "TIDAK [FAIL]") . "\n";
if (!$syncBackPass) $allPassed = false;

// 5. Pengujian Pengaturan Pejabat Pimpinan (Settings Module)
echo "\n--- PENGUJIAN 5: PENGATURAN PEJABAT PIMPINAN & IDENTITAS SATUAN ---\n";
$danrindamBaru = 'Kolonel Inf Triyono Suryo, S.I.P., M.Han.';
$setReq = Illuminate\Http\Request::create('/settings', 'POST', [
    '_token' => $session->token(),
    '_method' => 'PUT',
    'institution_name' => 'RINDAM III / SILIWANGI',
    'institution_sub' => 'Komando Pembinaan Pendidikan Militer Kodam III/Siliwangi',
    'institution_slogan' => 'Esa Hilang Dua Terbilang',
    'mako_location' => 'Ksatrian Mako Rindam III/Siliwangi, Jl. Menado No. 1 / Bihbul Bandung',
    'danrindam_name' => $danrindamBaru,
    'danrindam_rank' => 'Kolonel Inf',
    'danrindam_nrp' => '11980099887766',
    'danrindam_title' => 'Komandan Resimen Induk Kodam III/Siliwangi',
    'wadanrindam_name' => 'Kolonel Inf Ahmad Fauzi, S.E.',
    'wadanrindam_rank' => 'Kolonel Inf',
    'wadanrindam_nrp' => '11990088776655',
    'wadanrindam_title' => 'Wakil Komandan Rindam III/Siliwangi',
    'kakes_name' => 'Mayor Ckm dr. Hendra Irawan, Sp.KO',
    'kakes_rank' => 'Mayor Ckm',
    'kakes_nrp' => '11020034560789',
    'kakes_title' => 'Kepala Tim Kesehatan / Dokter Poliklinik Rindam',
    'satdiks' => [
        [
            'id' => 1,
            'commander_name' => 'Letkol Inf Hendra Prasetyo, S.I.P.',
            'commander_title' => 'Komandan Secaba Rindam III/Siliwangi',
            'location' => 'Ksatrian Secaba Rindam III/Siliwangi (Bihbul, Bandung)',
        ]
    ]
]);
$setReq->setLaravelSession($session);
$setRes = $kernel->handle($setReq);
$setStatus = $setRes->getStatusCode();
$setPass = ($setStatus === 302);
printf("%-45s -> Status: %d %s\n", "PUT /settings (Simpan Pejabat Pimpinan)", $setStatus, $setPass ? '[PASS]' : '[FAIL]');
if (!$setPass) $allPassed = false;

$savedDanrindam = App\Models\SystemSetting::get('danrindam_name');
echo "- Danrindam Tersimpan di Settings : " . $savedDanrindam . "\n";
$danrindamPass = ($savedDanrindam === $danrindamBaru);
echo "- Nilai Settings Berhasil Update?  " . ($danrindamPass ? "YA [PASS]" : "TIDAK [FAIL]") . "\n";
if (!$danrindamPass) $allPassed = false;

$pimpinanUser = App\Models\User::where('role_code', 'pimpinan')->first();
echo "- Nama User Pimpinan Tersinkron   : " . $pimpinanUser->name . "\n";
$userSyncPass = ($pimpinanUser->name === $danrindamBaru);
echo "- Sinkronisasi Akun Pimpinan OK?   " . ($userSyncPass ? "YA [PASS]" : "TIDAK [FAIL]") . "\n";
if (!$userSyncPass) $allPassed = false;

// 6. Pengujian Penginputan Data Siswa Format Excel (Batch Upload)
echo "\n--- PENGUJIAN 6: PENGINPUTAN DATA SERDIK FORMAT EXCEL (BATCH IMPORT) ---\n";
$excelService = $app->make(App\Services\StudentExcelService::class);
$templatePath = $excelService->exportTemplateExcel();
echo "- File Template Excel berhasil digenerate di: " . basename($templatePath) . "\n";

$uploadedFile = new Illuminate\Http\UploadedFile(
    $templatePath,
    'Format_Input_Serdik_Rindam.xlsx',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    null,
    true
);

$importResult = $excelService->importFile($uploadedFile, null, true);
echo "- Status Hasil Impor Excel        : " . ($importResult['success'] ? "SUKSES [PASS]" : "GAGAL [FAIL]") . "\n";
echo "- Jumlah Serdik Baru Ditambahkan  : " . $importResult['imported_count'] . "\n";
echo "- Jumlah Serdik Diperbarui        : " . $importResult['updated_count'] . "\n";
if (!$importResult['success']) $allPassed = false;

// Verifikasi serdik baru di database
$importedSample = App\Models\Student::where('nosik', '2026-SECABA-011')->first();
$sampleFound = ($importedSample && $importedSample->full_name === 'Prada Ahmad Kurnia');
echo "- Serdik Baru (Ahmad Kurnia) Ada? : " . ($sampleFound ? "YA [PASS]" : "TIDAK [FAIL]") . "\n";
if (!$sampleFound) $allPassed = false;

if ($importedSample) {
    echo "- Kodam / Kodim Asal Terimpor     : " . ($importedSample->origin_military_unit ?? '-') . "\n";
    $kodamOk = str_contains($importedSample->origin_military_unit ?? '', 'Kodam');
    echo "- Kodam / Kodim Teridentifikasi?  : " . ($kodamOk ? "YA [PASS]" : "TIDAK [FAIL]") . "\n";
    if (!$kodamOk) $allPassed = false;

    $sampleHealth = $importedSample->healthRecord;
    echo "- Status Kesehatan Terimpor       : " . ($sampleHealth?->daily_health_status ?? '-') . " (Stakes: " . ($sampleHealth?->stakes_grade ?? '-') . ")\n";
    $healthOk = ($sampleHealth && $sampleHealth->daily_health_status === 'Siap Latih');
    if (!$healthOk) $allPassed = false;
}

// 7. Pengujian Program Pendidikan & Status Aktif (Terhitung) vs Selesai (Arsip)
echo "\n--- PENGUJIAN 7: DATA SISWA PER PROGRAM & ARSIP SISWA SELESAI ---\n";
$reqProg = Illuminate\Http\Request::create('/students?program_id=1', 'GET');
$resProg = $kernel->handle($reqProg);
$progStatusPass = ($resProg->getStatusCode() === 200);
echo "- GET /students?program_id=1 (Filter Program DIKMABA)  : Status " . $resProg->getStatusCode() . " " . ($progStatusPass ? "[PASS]" : "[FAIL]") . "\n";
if (!$progStatusPass) $allPassed = false;

$reqAktif = Illuminate\Http\Request::create('/students?tab=aktif', 'GET');
$resAktif = $kernel->handle($reqAktif);
$aktifStatusPass = ($resAktif->getStatusCode() === 200);
echo "- GET /students?tab=aktif (Tab Siswa Aktif Terhitung)   : Status " . $resAktif->getStatusCode() . " " . ($aktifStatusPass ? "[PASS]" : "[FAIL]") . "\n";
if (!$aktifStatusPass) $allPassed = false;

$reqArsip = Illuminate\Http\Request::create('/students?tab=arsip', 'GET');
$resArsip = $kernel->handle($reqArsip);
$arsipStatusPass = ($resArsip->getStatusCode() === 200);
echo "- GET /students?tab=arsip (Tab Arsip Siswa Selesai)     : Status " . $resArsip->getStatusCode() . " " . ($arsipStatusPass ? "[PASS]" : "[FAIL]") . "\n";
if (!$arsipStatusPass) $allPassed = false;

// Verifikasi aturan: Status Aktif terhitung, Status Selesai tidak terhitung (masuk arsip)
$testStudent = App\Models\Student::find(2);
$initialCounted = $testStudent->is_counted;
echo "- Serdik #2 Awal Status                             : " . $testStudent->status . " (Terhitung? " . ($initialCounted ? 'YA' : 'TIDAK') . ")\n";

// Ubah ke status Selesai lewat controller/endpoint
$statusReq = Illuminate\Http\Request::create('/students/' . $testStudent->id . '/status', 'POST', [
    '_token' => $session->token(),
    '_method' => 'PATCH',
    'status' => 'Selesai',
]);
$statusReq->setLaravelSession($session);
$statusRes = $kernel->handle($statusReq);
$testStudent->refresh();

$isUncountedNow = (!$testStudent->is_counted && $testStudent->is_archived);
echo "- Status setelah Tamat/Selesai Diklat               : " . $testStudent->status . " (Unified: " . $testStudent->unified_status['label'] . ")\n";
echo "- Apakah Siswa Selesai TIDAK TERHITUNG & Jadi Arsip?: " . ($isUncountedNow ? "YA [PASS]" : "TIDAK [FAIL]") . "\n";
if (!$isUncountedNow) $allPassed = false;

// Pulihkan kembali ke Aktif
$testStudent->update(['status' => 'Aktif']);
$testStudent->refresh();
$isCountedAgain = $testStudent->is_counted;
echo "- Status setelah diaktifkan kembali                 : " . $testStudent->status . " (Terhitung kembali? " . ($isCountedAgain ? "YA [PASS]" : "TIDAK [FAIL]") . ")\n";
if (!$isCountedAgain) $allPassed = false;

echo "\n======================================================================\n";
echo "KESIMPULAN: " . ($allPassed ? "100% PENGUJIAN BERHASIL & SESUAI PERMINTAAN USER" : "ADA PENGUJIAN YANG GAGAL") . "\n";
echo "======================================================================\n";

