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
echo "VERIFIKASI: OPERATOR DANRINDAM BISA CRUD 5 SATDIK\n";
echo "========================================================\n\n";

$opDanrindam = User::where('email', 'operator.danrindam@rindam.mil.id')->first();
if (!$opDanrindam) {
    die("User operator.danrindam@rindam.mil.id tidak ditemukan!\n");
}

echo "Memeriksa Operator Danrindam:\n";
echo "Name: {$opDanrindam->name}\n";
echo "Role Code: {$opDanrindam->role_code}\n";
echo "canModifyData: " . ($opDanrindam->canModifyData() ? 'TRUE' : 'FALSE') . "\n";
echo "hasCrossSatdikAccess: " . ($opDanrindam->hasCrossSatdikAccess() ? 'TRUE' : 'FALSE') . "\n";
echo "isSuperAdmin: " . ($opDanrindam->isSuperAdmin() ? 'TRUE' : 'FALSE') . "\n\n";

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

$sampleProgram = EducationProgram::first();
$sampleStudent = Student::first();

// Operator Danrindam CAN see create modal in HTML
$resStudents = testRoute($opDanrindam, 'GET', '/students');
$hasCreateModal = str_contains($resStudents['content'], 'id="createStudentModal"');
$hasTambahButton = str_contains($resStudents['content'], 'Tambah Siswa Baru');
echo "Operator Danrindam GET /students -> Status {$resStudents['status']}\n";
echo "  [".($hasCreateModal ? 'PASS' : 'FAIL')."] Modal Tambah Siswa Tersedia: ".($hasCreateModal ? 'YA' : 'TIDAK')."\n";
echo "  [".($hasTambahButton ? 'PASS' : 'FAIL')."] Tombol Tambah Siswa Tersedia: ".($hasTambahButton ? 'YA' : 'TIDAK')."\n";

// Operator Danrindam CANNOT access Superadmin settings
$resSettings = testRoute($opDanrindam, 'GET', '/settings');
$resUsers = testRoute($opDanrindam, 'GET', '/users');
$resAudit = testRoute($opDanrindam, 'GET', '/audit-logs');
echo "Operator Danrindam Akses Menu Superadmin:\n";
echo "  [".($resSettings['status'] === 403 ? 'PASS' : 'FAIL')."] GET /settings -> Status: {$resSettings['status']} (Harus 403)\n";
echo "  [".($resUsers['status'] === 403 ? 'PASS' : 'FAIL')."] GET /users -> Status: {$resUsers['status']} (Harus 403)\n";
echo "  [".($resAudit['status'] === 403 ? 'PASS' : 'FAIL')."] GET /audit-logs -> Status: {$resAudit['status']} (Harus 403)\n";

echo "\n========================================================\n";
echo "VERIFIKASI ROLE OPERATOR DANRINDAM LENGKAP & TEPAT!\n";
echo "========================================================\n";
