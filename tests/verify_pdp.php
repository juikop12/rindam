<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use Illuminate\Support\Facades\DB;

$student = Student::with(['satdik', 'personalProfile'])->first();
$raw = DB::table('student_personal_profiles')->where('student_id', $student->id)->first();

echo "\n=======================================================\n";
echo "  VERIFIKASI SISTEM PELINDUNGAN DATA PRIBADI (UU PDP) \n";
echo "=======================================================\n";
echo "Nama Serdik          : " . $student->full_name . "\n";
echo "Satdik               : " . $student->satdik->name . "\n";
echo "NOSIK                : " . $student->nosik . "\n";
echo "Status               : " . $student->status . "\n\n";

echo "[1] BENTUK DI DATABASE (AES-256 ENCRYPTED):\n";
echo "    Raw Ciphertext NIK : " . substr($raw->nik, 0, 50) . "...\n";
echo "    Raw Ibu Kandung    : " . substr($raw->mother_name, 0, 50) . "...\n";
echo "    Raw No BPJS        : " . substr($raw->bpjs_number, 0, 50) . "...\n\n";

echo "[2] TAMPILAN STANDAR (DATA MASKING DI UI):\n";
echo "    Masked NIK         : " . $student->personalProfile->masked_nik . "\n";
echo "    Masked Ibu Kandung : " . $student->personalProfile->masked_mother_name . "\n";
echo "    Masked No HP       : " . $student->personalProfile->masked_emergency_phone . "\n";
echo "    Masked BPJS        : " . $student->personalProfile->masked_bpjs . "\n";
echo "    Masked Rekening    : " . $student->personalProfile->masked_bank_account . "\n\n";

echo "[3] HASIL DEKRIPSI RESMI (HANYA DENGAN ALASAN & AUDIT LOG):\n";
echo "    Plaintext NIK      : " . $student->personalProfile->nik . "\n";
echo "    Plaintext Ibu      : " . $student->personalProfile->mother_name . "\n";
echo "    Plaintext Medis    : " . $student->personalProfile->medical_history . "\n";
echo "    Plaintext Keswa    : " . $student->personalProfile->psychological_record . "\n";
echo "=======================================================\n\n";
