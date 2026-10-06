<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\EducationProgram;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "========================================================\n";
echo "VERIFIKASI SISTEMATIS: DANRINDAM TIDAK BISA CRUD\n";
echo "========================================================\n\n";

$danrindam = User::where('email', 'danrindam@rindam.mil.id')->first();
if (!$danrindam) {
    die("User danrindam@rindam.mil.id tidak ditemukan!\n");
}

echo "Memeriksa Danrindam User:\n";
echo "Name: {$danrindam->name}\n";
echo "Role Code: {$danrindam->role_code}\n";
echo "isDanrindam: " . ($danrindam->isDanrindam() ? 'TRUE' : 'FALSE') . "\n";
echo "canModifyData: " . ($danrindam->canModifyData() ? 'TRUE' : 'FALSE') . "\n";
echo "hasCrossSatdikAccess: " . ($danrindam->hasCrossSatdikAccess() ? 'TRUE' : 'FALSE') . "\n";
echo "isSuperAdmin: " . ($danrindam->isSuperAdmin() ? 'TRUE' : 'FALSE') . "\n\n";

if ($danrindam->canModifyData()) {
    die("FATAL ERROR: canModifyData() untuk Danrindam bernilai TRUE! Seharusnya FALSE!\n");
}

$sampleStudent = Student::first();
$sampleProgram = EducationProgram::first();
$sampleClassroom = Classroom::first();

function testRoute($user, $method, $uri, $data = []) {
    global $app;
    Auth::login($user);
    $session = $app->make('session')->driver();
    $session->start();
    $request = Request::create($uri, $method, array_merge(['_token' => $session->token()], $data));
    $request->setLaravelSession($session);
    $request->headers->set('X-CSRF-TOKEN', $session->token());
    $request->headers->set('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
    
    $response = $app->handle($request);
    $status = $response->getStatusCode();
    $content = $response->getContent();
    return ['status' => $status, 'content' => $content];
}

echo "--------------------------------------------------------\n";
echo "1. PENGUJIAN BACKEND MUTASI DATA (DANRINDAM MUTATION ATTEMPTS):\n";
echo "--------------------------------------------------------\n";

$mutationTests = [
    ['POST', '/students', ['full_name' => 'Test Siswa', 'satdik_id' => 1, 'education_program_id' => $sampleProgram->id, 'gender' => 'L', 'student_rank' => 'Prada', 'status' => 'Aktif', 'nik' => '1234567890123456']],
    ['PUT', '/students/' . $sampleStudent->id, ['full_name' => 'Edit Siswa', 'satdik_id' => 1, 'education_program_id' => $sampleProgram->id, 'gender' => 'L', 'student_rank' => 'Prada', 'status' => 'Aktif']],
    ['DELETE', '/students/' . $sampleStudent->id, []],
    ['PATCH', '/students/' . $sampleStudent->id . '/status', ['status' => 'Selesai']],
    ['GET', '/students/import', []],
    ['POST', '/students/import', []],
    ['POST', '/programs', ['name' => 'Program Baru', 'satdik_id' => 1, 'code' => 'NEW-PROG', 'academic_year' => '2026', 'status' => 'Berjalan']],
    ['PUT', '/programs/' . $sampleProgram->id, ['name' => 'Edit Program', 'satdik_id' => 1, 'code' => $sampleProgram->code, 'academic_year' => '2026', 'status' => 'Berjalan']],
    ['DELETE', '/programs/' . $sampleProgram->id, []],
    ['POST', '/programs/' . $sampleProgram->id . '/classrooms', ['company' => 'Kompi A', 'platoon' => 'Peleton 1']],
    ['PUT', '/classrooms/' . ($sampleClassroom ? $sampleClassroom->id : 1), ['name' => 'Edit Class']],
    ['DELETE', '/classrooms/' . ($sampleClassroom ? $sampleClassroom->id : 1), []],
    ['GET', '/health/' . $sampleStudent->id . '/edit', []],
    ['PUT', '/health/' . $sampleStudent->id, ['daily_health_status' => 'Siap Latih']],
    ['GET', '/settings', []],
    ['GET', '/users', []],
    ['GET', '/audit-logs', []],
];

$allBackendPassed = true;
foreach ($mutationTests as $t) {
    [$method, $uri, $params] = $t;
    $res = testRoute($danrindam, $method, $uri, $params);
    $passed = ($res['status'] === 403);
    echo sprintf("[%s] %-7s %-38s -> Status: %d %s\n", 
        $passed ? 'PASS' : 'FAIL', 
        $method, 
        $uri, 
        $res['status'], 
        $passed ? '(403 Forbidden Sesuai Aturan)' : '(TIDAK DIBLOKIR!)'
    );
    if (!$passed) $allBackendPassed = false;
}

echo "\n--------------------------------------------------------\n";
echo "2. PENGUJIAN KONTEN HTML (ZERO CRUD ELEMENTS UNTUK DANRINDAM):\n";
echo "--------------------------------------------------------\n";

$viewChecks = [
    [
        'uri' => '/students',
        'forbidden_strings' => [
            'id="createStudentModal"',
            'id="editStudentModal"',
            'id="deleteStudentModal"',
            'id="statusModal"',
            'Tambah Siswa Baru',
            'Tambah Serdik',
            'Impor Format Excel',
        ]
    ],
    [
        'uri' => '/programs',
        'forbidden_strings' => [
            'id="createProgramModal"',
            'id="editProgramModal"',
            'Tambah Program Pendidikan',
            'Tambah Program Baru',
        ]
    ],
    [
        'uri' => '/programs/' . $sampleProgram->id,
        'forbidden_strings' => [
            'id="createClassroomModal"',
            'id="editClassroomModal"',
            'Buat Kompi & Peleton',
            'Tambah Siswa ke Program',
        ]
    ],
    [
        'uri' => '/health',
        'forbidden_strings' => [
            'health/' . $sampleStudent->id . '/edit',
            'Perbarui Status Kesehatan',
        ]
    ],
    [
        'uri' => '/health/' . $sampleStudent->id,
        'forbidden_strings' => [
            'health/' . $sampleStudent->id . '/edit',
            'Perbarui Status Kesehatan',
            'Update Perawatan',
        ]
    ],
    [
        'uri' => '/students/' . $sampleStudent->id,
        'forbidden_strings' => [
            'students.update-status',
            'Simpan Status',
        ]
    ],
];

$allHtmlPassed = true;
foreach ($viewChecks as $vc) {
    $uri = $vc['uri'];
    $res = testRoute($danrindam, 'GET', $uri);
    echo "Halaman: {$uri} (Status {$res['status']})\n";
    foreach ($vc['forbidden_strings'] as $str) {
        $found = str_contains($res['content'], $str);
        $passed = !$found;
        echo sprintf("  [%s] Tidak ada teks/elemen '%s': %s\n",
            $passed ? 'PASS' : 'FAIL',
            $str,
            $passed ? 'BERSIH (Tidak Ada)' : 'DITEMUKAN DALAM HTML!'
        );
        if (!$passed) $allHtmlPassed = false;
    }
}

echo "\n========================================================\n";
if ($allBackendPassed && $allHtmlPassed) {
    echo "SEMUA PENGUJIAN BERHASIL (100% PASS)!\n";
    echo "Danrindam TERBUKTI SEPENUHNYA TIDAK BISA CRUD & BEBAS ELEMEN MUTASI!\n";
} else {
    echo "ADA PENGUJIAN YANG GAGAL!\n";
}
echo "========================================================\n";
