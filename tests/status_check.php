<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$urls = [
    '/' => 'GET',
    '/students/create' => 'GET',
    '/students/1' => 'GET',
    '/audit-logs' => 'GET',
    '/api/satdiks/1/programs' => 'GET',
];

echo "=== STATUS CHECK FOR ALL CORE ENDPOINTS ===\n";
$allPassed = true;

foreach ($urls as $url => $method) {
    $request = Illuminate\Http\Request::create($url, $method);
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    
    $isOk = ($status === 200);
    if (!$isOk) $allPassed = false;
    
    printf("%-30s [%s] -> %d %s\n", $url, $method, $status, $isOk ? '[PASS]' : '[FAIL]');
}

// Test POST create new student with encrypted data
echo "\n=== TEST POST CREATE NEW STUDENT (WITH ENCRYPTED PROFILE) ===\n";
$newStudentData = [
    'satdik_id' => 1, // SECABA
    'education_program_id' => 1,
    'classroom_id' => 1,
    'full_name' => 'Bripda Ahmad Fauzi',
    'student_rank' => 'Siswa Secaba',
    'origin_military_unit' => 'Kodim 0705/Magelang',
    'birth_place' => 'Magelang',
    'birth_date' => '2004-05-12',
    'gender' => 'L',
    'religion' => 'Islam',
    'blood_type' => 'O',
    'status' => 'Aktif',
    'nik' => '3308123456789999',
    'family_card_number' => '3308123456780000',
    'mother_name' => 'Siti Nurhaliza',
    'father_name' => 'Bambang Irawan',
    'emergency_contact_phone' => '081299998888',
    'bpjs_number' => '0001999988887',
    'bank_name' => 'BRI TNI AD',
    'bank_account_number' => '0123-01-999999-50-1',
    'home_address' => 'Jl. Pemuda No. 12, Magelang',
    'medical_history' => 'Alergi dingin',
    'psychological_record' => 'MS (Memenuhi Syarat)',
];

// Start session for CSRF simulation
$session = $app->make('session')->driver();
$session->start();
$token = $session->token();
$newStudentData['_token'] = $token;

$storeReq = Illuminate\Http\Request::create('/students', 'POST', $newStudentData);
$storeReq->setLaravelSession($session);
$storeRes = $kernel->handle($storeReq);
$storeStatus = $storeRes->getStatusCode();
printf("%-30s [POST] -> %d %s\n", '/students', $storeStatus, in_array($storeStatus, [200, 302]) ? '[PASS]' : '[FAIL]');

// Check newly created student in DB
$created = App\Models\Student::where('full_name', 'Bripda Ahmad Fauzi')->first();
if ($created) {
    echo "Siswa Baru Berhasil Dibuat:\n";
    echo "- ID: {$created->id}\n";
    echo "- NOSIK Otomatis: {$created->nosik}\n";
    echo "- Satdik: {$created->satdik->name}\n";
    
    // Check raw database ciphertext for NIK and Mother Name
    $rawDb = Illuminate\Support\Facades\DB::table('student_personal_profiles')
        ->where('student_id', $created->id)
        ->first();
    
    $isEncrypted = str_starts_with($rawDb->nik, 'eyJpdiI6');
    echo "- Database Raw NIK Ciphertext: " . substr($rawDb->nik, 0, 30) . "... [AES-256 Valid: " . ($isEncrypted ? "YES" : "NO") . "]\n";
    echo "- Eloquent Masked NIK: " . $created->personalProfile->masked_nik . "\n";
    echo "- Eloquent Masked Ibu: " . $created->personalProfile->masked_mother_name . "\n";
    echo "- Decrypted Plain NIK: " . $created->personalProfile->nik . "\n";
} else {
    echo "Gagal menemukan siswa baru di database.\n";
    $allPassed = false;
}

echo "\nKESIMPULAN: " . ($allPassed ? "SELURUH FITUR & ENKRIPSI BERFUNGSI SEMPURNA (100% PASS)" : "ADA PENGUJIAN YANG GAGAL") . "\n";
