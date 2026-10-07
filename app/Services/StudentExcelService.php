<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\EducationProgram;
use App\Models\Satdik;
use App\Models\Student;
use App\Models\StudentHealthRecord;
use App\Models\StudentPersonalProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentExcelService
{
    /**
     * Header standar format Excel penginputan data Serdik
     */
    public const COLUMNS = [
        'A' => ['key' => 'no', 'label' => 'NO', 'width' => 6],
        'B' => ['key' => 'nik', 'label' => 'NIK_KTP', 'width' => 22],
        'C' => ['key' => 'nosik', 'label' => 'NOSIS', 'width' => 20],
        'D' => ['key' => 'full_name', 'label' => 'NAMA_LENGKAP', 'width' => 28],
        'E' => ['key' => 'satdik_code', 'label' => 'KODE_SATDIK', 'width' => 16],
        'F' => ['key' => 'program_name', 'label' => 'PROGRAM_PENDIDIKAN', 'width' => 26],
        'G' => ['key' => 'student_rank', 'label' => 'PANGKAT_SISWA', 'width' => 16],
        'H' => ['key' => 'company', 'label' => 'KOMPI', 'width' => 18],
        'I' => ['key' => 'platoon', 'label' => 'PELETON', 'width' => 14],
        'J' => ['key' => 'gender', 'label' => 'GENDER_L_P', 'width' => 12],
        'K' => ['key' => 'origin_military_unit', 'label' => 'KODAM/KODIM_ASAL', 'width' => 28],
        'L' => ['key' => 'birth_place', 'label' => 'TEMPAT_LAHIR', 'width' => 18],
        'M' => ['key' => 'birth_date', 'label' => 'TANGGAL_LAHIR', 'width' => 16],
        'N' => ['key' => 'blood_type', 'label' => 'GOL_DARAH', 'width' => 12],
        'O' => ['key' => 'height_cm', 'label' => 'TB_CM', 'width' => 10],
        'P' => ['key' => 'weight_kg', 'label' => 'BB_KG', 'width' => 10],
        'Q' => ['key' => 'blood_pressure', 'label' => 'TENSI_MMHG', 'width' => 14],
        'R' => ['key' => 'daily_health_status', 'label' => 'STATUS_KESEHATAN', 'width' => 22],
        'S' => ['key' => 'stakes_grade', 'label' => 'STAKES_MILITER', 'width' => 20],
        'T' => ['key' => 'doctor_notes', 'label' => 'CATATAN_MEDIS', 'width' => 30],
        'U' => ['key' => 'status', 'label' => 'STATUS_SISWA', 'width' => 20],
        'V' => ['key' => 'iq_score', 'label' => 'IQ', 'width' => 10],
        'W' => ['key' => 'psychology_score', 'label' => 'NILAI_PSI', 'width' => 12],
        'X' => ['key' => 'psychology_grade', 'label' => 'KLASIFIKASI_PSI', 'width' => 16],
        'Y' => ['key' => 'branch_recommendations', 'label' => 'SARAN_KECABANGAN', 'width' => 20],
        'Z' => ['key' => 'physical_fitness_score', 'label' => 'NILAI_JASMANI', 'width' => 14],
        'AA' => ['key' => 'physical_fitness_grade', 'label' => 'KLASIFIKASI_JAS', 'width' => 16],
        'AB' => ['key' => 'run_12m_distance', 'label' => 'LARI_JARAK_M', 'width' => 14],
        'AC' => ['key' => 'run_12m_score', 'label' => 'LARI_NILAI', 'width' => 12],
        'AD' => ['key' => 'pull_ups_count', 'label' => 'PULL_UP_JML', 'width' => 12],
        'AE' => ['key' => 'pull_ups_score', 'label' => 'PULL_UP_NILAI', 'width' => 14],
        'AF' => ['key' => 'sit_ups_count', 'label' => 'SIT_UP_JML', 'width' => 12],
        'AG' => ['key' => 'sit_ups_score', 'label' => 'SIT_UP_NILAI', 'width' => 12],
        'AH' => ['key' => 'push_ups_count', 'label' => 'PUSH_UP_JML', 'width' => 12],
        'AI' => ['key' => 'push_ups_score', 'label' => 'PUSH_UP_NILAI', 'width' => 14],
        'AJ' => ['key' => 'shuttle_run_seconds', 'label' => 'SHUTTLE_DETIK', 'width' => 14],
        'AK' => ['key' => 'shuttle_run_score', 'label' => 'SHUTTLE_NILAI', 'width' => 14],
        'AL' => ['key' => 'swimming_score', 'label' => 'RENANG_NILAI', 'width' => 14],
        'AM' => ['key' => 'swimming_style', 'label' => 'RENANG_GAYA', 'width' => 14],
        'AN' => ['key' => 'litpers_grade', 'label' => 'KLASIFIKASI_LITPERS', 'width' => 18],
        'AO' => ['key' => 'litpers_written_score', 'label' => 'LITPERS_TERTULIS', 'width' => 16],
        'AP' => ['key' => 'litpers_interview_score', 'label' => 'LITPERS_WAWANCARA', 'width' => 18],
        'AQ' => ['key' => 'final_selection_score', 'label' => 'NILAI_AKHIR_SELEKSI', 'width' => 18],
        'AR' => ['key' => 'selection_test_number', 'label' => 'NOMOR_TES_PANPUS', 'width' => 24],
        'AS' => ['key' => 'candidate_phone', 'label' => 'NO_HP_SISWA', 'width' => 16],
        'AT' => ['key' => 'candidate_email', 'label' => 'EMAIL_SISWA', 'width' => 24],
        'AU' => ['key' => 'origin_school', 'label' => 'ASAL_SEKOLAH', 'width' => 24],
        'AV' => ['key' => 'father_name', 'label' => 'NAMA_ORTU', 'width' => 20],
        'AW' => ['key' => 'parent_occupation', 'label' => 'PEKERJAAN_ORTU', 'width' => 20],
        'AX' => ['key' => 'emergency_contact_phone', 'label' => 'NO_HP_ORTU', 'width' => 16],
        'AY' => ['key' => 'home_address', 'label' => 'ALAMAT_DOMISILI', 'width' => 30],
    ];

    /**
     * Hasilkan berkas Spreadsheet template Excel berformat resmi Rindam III/Siliwangi
     */
    public function generateTemplateSpreadsheet(?int $selectedSatdikId = null): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Serdik Rindam');

        // 1. Tulis Header Judul Satuan
        $satdik = $selectedSatdikId ? Satdik::find($selectedSatdikId) : null;
        $title = $satdik 
            ? "FORMAT INPUT DATA SERDIK — " . strtoupper($satdik->name)
            : "FORMAT PENGINPUTAN DATA SERDIK PER SATDIK — RINDAM III / SILIWANGI";

        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:AY1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13)->getColor()->setRGB('10170C');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // 2. Baris Petunjuk
        $sheet->setCellValue('A2', 'PETUNJUK: Format komprehensif memuat data serdik, IQ, Nilai Psikologi, Kesamaptaan Jasmani (Garjas A & B), Litpers, dan Kontak Keluarga. NIK KTP (16 digit) adalah ID unik utama pencegah duplikasi. Kolom wajib: NAMA_LENGKAP, NIK_KTP, & KODE_SATDIK.');
        $sheet->mergeCells('A2:AY2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('5B6A52');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // Format kolom B (NIK) sebagai Teks murni agar tidak terpotong eksponensial (E+)
        $sheet->getStyle('B')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // 3. Header Kolom di Baris 4
        $headerRow = 4;
        $sheet->getRowDimension($headerRow)->setRowHeight(26);

        foreach (self::COLUMNS as $col => $info) {
            $cell = $col . $headerRow;
            $sheet->setCellValue($cell, $info['label']);
            $sheet->getColumnDimension($col)->setWidth($info['width']);

            // Styling Header Militer Dark Gold
            $sheet->getStyle($cell)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 10,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1D2A16'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'C9A227'],
                    ],
                ],
            ]);
        }

        // 4. Contoh Baris Data (Row 5 s.d. 9) dengan NIK KTP 16 Digit
        $sampleRows = [
            [
                1, '3204011503040001', '2026-SECABA-011', 'Prada Ahmad Kurnia', 'SECABA',
                'DIKMABA TA 2026', 'Siswa Bintara', 'Kompi A', 'Peleton 1',
                'L', 'Kodam III/Slw - Kodim 0618/Kota Bandung', 'Bandung', '2004-03-15', 'O',
                173, 68, '120/80', 'Siap Latih', 'Stakes I (Sangat Baik)', 'Sehat prima siap latihan lapangan',
                'Aktif'
            ],
            [
                2, '3209022011030002', '2026-SECABA-012', 'Prada Budi Santoso', 'SECABA',
                'DIKMABA TA 2026', 'Siswa Bintara', 'Kompi A', 'Peleton 2',
                'L', 'Kodam III/Slw - Kodim 0609/Cimahi', 'Cimahi', '2003-11-20', 'A',
                170, 65, '118/78', 'Berobat Jalan', 'Stakes II (Baik)', 'Pemulihan cedera betis ringan',
                'Aktif'
            ],
            [
                3, '3204241001050003', '2026-SECATA-011', 'Prada Danu Wijaya', 'SECATA',
                'DIKMATA TA 2026', 'Siswa Tamtama', 'Kompi Senapan B', 'Peleton 1',
                'L', 'Kodam III/Slw - Kodim 0624/Kab. Bandung', 'Pangalengan', '2005-01-10', 'B',
                168, 62, '122/80', 'Siap Latih', 'Stakes I (Sangat Baik)', 'Nihil keluhan',
                'Aktif'
            ],
            [
                4, '3272012208020004', '2026-DODIKJUR-011', 'Serda Fajar Hidayat', 'DODIKJUR',
                'DIKJURBA TA 2026', 'Siswa Bintara', 'Kompi Bantuan', 'Peleton 1',
                'L', 'Kodam III/Slw - Kodim 0607/Kota Sukabumi', 'Sukabumi', '2002-08-22', 'AB',
                175, 71, '125/82', 'Rawat Inap Poliklinik', 'Stakes III (Kurang/Dispen)', 'Observasi demam di Poliklinik Satdik',
                'Sakit'
            ],
            [
                5, '3205030507040005', '2026-BELANEGARA-011', 'Guruh Pratama', 'BELANEGARA',
                'BELA NEGARA TA 2026', 'Siswa Komcad', 'Kompi Bela Negara', 'Peleton 3',
                'L', 'Kodam III/Slw - Kodim 0611/Garut', 'Garut', '2004-07-05', 'O',
                171, 66, '115/75', 'Siap Latih', 'Stakes I (Sangat Baik)', 'Fisik memenuhi syarat',
                'Selesai'
            ],
        ];

        // Jika Satdik tertentu dipilih, filter contoh baris untuk satdik tersebut (index 4 = KODE_SATDIK)
        if ($satdik) {
            $filtered = array_filter($sampleRows, fn($r) => $r[4] === $satdik->code);
            if (!empty($filtered)) {
                $sampleRows = array_values($filtered);
            }
        }

        $currentRow = 5;
        foreach ($sampleRows as $data) {
            $colLetter = 'A';
            foreach ($data as $val) {
                if ($colLetter === 'B' || $colLetter === 'C') {
                    $sheet->getCell($colLetter . $currentRow)->setValueExplicit((string)$val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($colLetter . $currentRow, $val);
                }
                $sheet->getStyle($colLetter . $currentRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_HAIR,
                            'color' => ['rgb' => 'DCE4D6'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'horizontal' => in_array($colLetter, ['A', 'B', 'C', 'E', 'I', 'J', 'N', 'O', 'P', 'Q', 'S'])
                            ? Alignment::HORIZONTAL_CENTER
                            : Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
                $colLetter++;
            }
            $sheet->getRowDimension($currentRow)->setRowHeight(20);
            $currentRow++;
        }

        return $spreadsheet;
    }

    /**
     * Ekspor template ke format Excel (.xlsx)
     */
    public function exportTemplateExcel(?int $selectedSatdikId = null): string
    {
        $spreadsheet = $this->generateTemplateSpreadsheet($selectedSatdikId);
        $writer = new Xlsx($spreadsheet);
        
        $tempPath = tempnam(sys_get_temp_dir(), 'serdik_tpl_') . '.xlsx';
        $writer->save($tempPath);

        return $tempPath;
    }

    /**
     * Ekspor template ke format CSV (.csv)
     */
    public function exportTemplateCsv(?int $selectedSatdikId = null): string
    {
        $spreadsheet = $this->generateTemplateSpreadsheet($selectedSatdikId);
        $writer = new Csv($spreadsheet);
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        $writer->setSheetIndex(0);

        $tempPath = tempnam(sys_get_temp_dir(), 'serdik_tpl_') . '.csv';
        $writer->save($tempPath);

        return $tempPath;
    }

    /**
     * Proses Impor Berkas Excel / CSV Data Serdik per Satdik
     */
    public function importFile(UploadedFile $file, ?int $defaultSatdikId = null, bool $updateExisting = true): array
    {
        $realPath = $file->getRealPath();
        $spreadsheet = IOFactory::load($realPath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) < 2) {
            return [
                'success' => false,
                'imported_count' => 0,
                'updated_count' => 0,
                'errors' => ['Berkas Excel kosong atau tidak memiliki baris data.'],
            ];
        }

        // Cari baris header (biasanya baris 4 atau baris 1 yang mengandung 'NAMA' atau 'NOSIK')
        $headerIndex = 0;
        $columnMap = [];

        foreach ($rows as $idx => $row) {
            $normalizedRow = array_map(fn($v) => strtoupper(trim((string)$v)), $row);
            if (in_array('NAMA_LENGKAP', $normalizedRow) || in_array('NAMA LENGKAP', $normalizedRow) || in_array('NOSIS', $normalizedRow) || in_array('NOSIK', $normalizedRow) || in_array('NIK_KTP', $normalizedRow) || in_array('NIK', $normalizedRow)) {
                $headerIndex = $idx;
                foreach ($normalizedRow as $colIdx => $colName) {
                    $cleanName = strtolower(str_replace([' ', '-', '/'], '_', $colName));
                    $columnMap[$cleanName] = $colIdx;
                }
                break;
            }
        }

        // Jika tidak ditemukan header bernama persis, gunakan indeks default kolom
        if (empty($columnMap)) {
            $headerIndex = 0;
            $columnMap = [
                'no' => 0,
                'nik_ktp' => 1,
                'nik' => 1,
                'nosik' => 2,
                'nosis' => 2,
                'nama_lengkap' => 3,
                'kode_satdik' => 4,
                'program_pendidikan' => 5,
                'program_diklat' => 5,
                'pangkat_siswa' => 6,
                'kompi' => 7,
                'peleton' => 8,
                'gender_l_p' => 9,
                'kodam_kodim_asal' => 10,
                'kodim_asal' => 10,
                'tempat_lahir' => 11,
                'tanggal_lahir' => 12,
                'gol_darah' => 13,
                'tb_cm' => 14,
                'bb_kg' => 15,
                'tensi_mmhg' => 16,
                'status_kesehatan' => 17,
                'stakes_militer' => 18,
                'catatan_medis' => 19,
                'status_siswa' => 20,
                'status' => 20,
            ];
        }

        $allSatdiks = Satdik::all()->keyBy(fn($s) => strtoupper($s->code));
        $defaultSatdik = $defaultSatdikId ? Satdik::find($defaultSatdikId) : null;

        $importedCount = 0;
        $updatedCount = 0;
        $errors = [];

        // In-memory cache untuk mencegah query repetitif di dalam loop
        $programsCache = [];
        $classroomsCache = [];
        $satdikStudentCounts = [];

        DB::beginTransaction();
        try {
            for ($i = $headerIndex + 1; $i < count($rows); $i++) {
                $row = $rows[$i];

                // Cek apakah baris kosong
                $allEmpty = true;
                foreach ($row as $cell) {
                    if (!empty(trim((string)$cell))) {
                        $allEmpty = false;
                        break;
                    }
                }
                if ($allEmpty) continue;

                $rowNum = $i + 1;

                // Ambil data dari kolom
                $fullName = trim((string)($row[$this->getColIdx($columnMap, ['nama_lengkap', 'nama', 'full_name'])] ?? ''));
                if (empty($fullName)) {
                    // Baris tanpa nama diabaikan / dicatat jika bukan baris kosong
                    continue;
                }

                $nosik = trim((string)($row[$this->getColIdx($columnMap, ['nosis', 'nosik', 'nomor_siswa', 'no_serdik'])] ?? ''));
                $satdikCode = strtoupper(trim((string)($row[$this->getColIdx($columnMap, ['kode_satdik', 'satdik', 'satuan'])] ?? '')));
                $programName = trim((string)($row[$this->getColIdx($columnMap, ['program_pendidikan', 'program_diklat', 'prodi', 'program', 'pendidikan'])] ?? ''));
                $studentRank = trim((string)($row[$this->getColIdx($columnMap, ['pangkat_siswa', 'pangkat'])] ?? 'Siswa'));
                $company = trim((string)($row[$this->getColIdx($columnMap, ['kompi'])] ?? ''));
                $platoon = trim((string)($row[$this->getColIdx($columnMap, ['peleton', 'ton'])] ?? ''));
                $gender = strtoupper(trim((string)($row[$this->getColIdx($columnMap, ['gender_l_p', 'gender', 'jk'])] ?? 'L')));
                $gender = in_array($gender, ['P', 'PEREMPUAN']) ? 'P' : 'L';
                $kodim = trim((string)($row[$this->getColIdx($columnMap, ['kodam_kodim_asal', 'kodim_asal', 'kodam_asal', 'asal_satuan', 'satuan_asal', 'kodam', 'kodim'])] ?? ''));
                $birthPlace = trim((string)($row[$this->getColIdx($columnMap, ['tempat_lahir'])] ?? ''));
                $birthDateRaw = trim((string)($row[$this->getColIdx($columnMap, ['tanggal_lahir', 'tgl_lahir'])] ?? ''));
                $bloodType = strtoupper(trim((string)($row[$this->getColIdx($columnMap, ['gol_darah', 'golongan_darah'])] ?? 'O')));
                $height = (int)($row[$this->getColIdx($columnMap, ['tb_cm', 'tinggi_badan', 'tinggi'])] ?? 0);
                $weight = (float)($row[$this->getColIdx($columnMap, ['bb_kg', 'berat_badan', 'berat'])] ?? 0);
                $bloodPressure = trim((string)($row[$this->getColIdx($columnMap, ['tensi_mmhg', 'tensi', 'tekanan_darah'])] ?? '120/80'));
                $dailyHealth = trim((string)($row[$this->getColIdx($columnMap, ['status_kesehatan', 'kesehatan'])] ?? 'Siap Latih'));
                $stakes = trim((string)($row[$this->getColIdx($columnMap, ['stakes_militer', 'stakes'])] ?? 'Stakes I (Sangat Baik)'));
                $doctorNotes = trim((string)($row[$this->getColIdx($columnMap, ['catatan_medis', 'catatan_dokter', 'catatan'])] ?? ''));
                $statusRaw = trim((string)($row[$this->getColIdx($columnMap, ['status_siswa', 'status', 'kategori_status', 'keaktifan'])] ?? ''));

                // Normalisasi Satdik
                $satdik = null;
                if (!empty($satdikCode) && isset($allSatdiks[$satdikCode])) {
                    $satdik = $allSatdiks[$satdikCode];
                } elseif ($defaultSatdik) {
                    $satdik = $defaultSatdik;
                } else {
                    $satdik = $allSatdiks->first();
                }

                if (!$satdik) {
                    $errors[] = "Baris {$rowNum}: Satdik tidak ditemukan untuk siswa {$fullName}.";
                    continue;
                }

                // Cari atau buat Program Pendidikan (dengan memory cache)
                $progKey = "{$satdik->id}_" . Str::slug($programName);
                if (!isset($programsCache[$progKey])) {
                    $educationProgram = null;
                    if (!empty($programName)) {
                        $educationProgram = EducationProgram::where('satdik_id', $satdik->id)
                            ->where(function($q) use ($programName) {
                                $q->where('name', 'like', "%{$programName}%")
                                  ->orWhere('code', 'like', "%{$programName}%");
                            })
                            ->first();
                    }
                    if (!$educationProgram) {
                        $educationProgram = EducationProgram::where('satdik_id', $satdik->id)->first();
                    }
                    if (!$educationProgram) {
                        $educationProgram = EducationProgram::create([
                            'satdik_id' => $satdik->id,
                            'name' => !empty($programName) ? $programName : "Pendidikan {$satdik->code}",
                            'code' => strtoupper(substr($satdik->code, 0, 4)) . '-' . date('Y'),
                            'academic_year' => date('Y'),
                            'batch_number' => 1,
                            'status' => 'Berjalan',
                        ]);
                    }
                    $programsCache[$progKey] = $educationProgram;
                }
                $educationProgram = $programsCache[$progKey];

                // Cari atau buat Ruang Kelas / Peleton (dengan memory cache)
                $classroomId = null;
                if (!empty($company) || !empty($platoon)) {
                    $className = trim("{$company} {$platoon}");
                    $classCode = strtoupper(Str::slug($className, '-'));
                    if (empty($classCode)) {
                        $classCode = 'CLS-' . mt_rand(1000, 9999);
                    }
                    $classKey = "{$educationProgram->id}_{$classCode}";
                    if (!isset($classroomsCache[$classKey])) {
                        $classroom = Classroom::firstOrCreate([
                            'education_program_id' => $educationProgram->id,
                            'code' => $classCode,
                        ], [
                            'name' => $className,
                            'company' => !empty($company) ? $company : null,
                            'platoon' => !empty($platoon) ? $platoon : null,
                            'capacity' => 40,
                        ]);
                        $classroomsCache[$classKey] = $classroom;
                    }
                    $classroomId = $classroomsCache[$classKey]->id;
                }

                // Normalisasi Tanggal Lahir
                $birthDate = null;
                if (!empty($birthDateRaw)) {
                    $timestamp = strtotime($birthDateRaw);
                    if ($timestamp) {
                        $birthDate = date('Y-m-d', $timestamp);
                    }
                }

                // Normalisasi Status Siswa (Mendukung Siswa Aktif Terhitung vs Arsip Selesai Diklat)
                $studentStatus = 'Aktif';
                if (!empty($statusRaw)) {
                    $normStatus = strtoupper($statusRaw);
                    if (str_contains($normStatus, 'SELESAI') || str_contains($normStatus, 'TAMAT')) {
                        $studentStatus = 'Selesai';
                    } elseif (str_contains($normStatus, 'LULUS')) {
                        $studentStatus = 'Lulus';
                    } elseif (str_contains($normStatus, 'DO') || str_contains($normStatus, 'KELUAR')) {
                        $studentStatus = 'DO / Dikeluarkan';
                    } elseif (str_contains($normStatus, 'DINAS')) {
                        $studentStatus = 'Dinas Luar';
                    } elseif (str_contains($normStatus, 'SAKIT')) {
                        $studentStatus = 'Sakit';
                    } else {
                        $studentStatus = 'Aktif';
                    }
                } else {
                    if (in_array($dailyHealth, ['Berobat Jalan', 'Rawat Inap Poliklinik', 'Rujuk Rumkit'])) {
                        $studentStatus = 'Sakit';
                    }
                }

                // Baca & bersihkan NIK KTP (16 Digit)
                $nikRaw = trim((string)($row[$this->getColIdx($columnMap, ['nik_ktp', 'nik', 'no_ktp', 'nomor_induk_kependudukan', 'ktp'])] ?? ''));
                if (preg_match('/^[0-9]+(\.[0-9]+)?E\+[0-9]+$/i', $nikRaw)) {
                    $nikRaw = number_format((float)$nikRaw, 0, '', '');
                }
                $nik = preg_replace('/[^0-9]/', '', $nikRaw);

                // Cek apakah data duplikat berdasarkan NIK KTP (O(1) Indexed Blind Index)
                $existingProfile = !empty($nik) ? StudentPersonalProfile::findByNik($nik) : null;
                $existingStudent = $existingProfile?->student;

                if ($existingStudent) {
                    if (!$updateExisting) {
                        $errors[] = "Baris {$rowNum}: NIK KTP {$nik} (Serdik: {$fullName}) sudah terdaftar di {$existingStudent->satdik?->name} (dilewati demi mencegah duplikasi).";
                        continue;
                    }

                    // Perbarui Data Siswa yang sudah ada berdasar NIK KTP
                    $updateData = [
                        'satdik_id' => $satdik->id,
                        'education_program_id' => $educationProgram->id,
                        'classroom_id' => $classroomId ?? $existingStudent->classroom_id,
                        'full_name' => $fullName,
                        'student_rank' => !empty($studentRank) ? $studentRank : $existingStudent->student_rank,
                        'company' => !empty($company) ? $company : $existingStudent->company,
                        'platoon' => !empty($platoon) ? $platoon : $existingStudent->platoon,
                        'gender' => $gender,
                        'origin_military_unit' => !empty($kodim) ? $kodim : $existingStudent->origin_military_unit,
                        'birth_place' => !empty($birthPlace) ? $birthPlace : $existingStudent->birth_place,
                        'birth_date' => $birthDate ?? $existingStudent->birth_date,
                        'blood_type' => !empty($bloodType) ? $bloodType : $existingStudent->blood_type,
                        'status' => $studentStatus,
                    ];
                    if (!empty($nosik)) {
                        $updateData['nosik'] = $nosik;
                    }
                    $existingStudent->update($updateData);

                    $student = $existingStudent;
                    $updatedCount++;
                } else {
                    // Serdik Baru (NIK belum pernah terdaftar)
                    if (empty($nosik)) {
                        if (!isset($satdikStudentCounts[$satdik->id])) {
                            $satdikStudentCounts[$satdik->id] = Student::where('satdik_id', $satdik->id)->count();
                        }
                        $satdikStudentCounts[$satdik->id]++;
                        $nosik = sprintf("%s-%s-%03d", date('Y'), $satdik->code, $satdikStudentCounts[$satdik->id]);
                    }

                    $student = Student::create([
                        'nosik' => $nosik,
                        'satdik_id' => $satdik->id,
                        'education_program_id' => $educationProgram->id,
                        'classroom_id' => $classroomId,
                        'full_name' => $fullName,
                        'student_rank' => !empty($studentRank) ? $studentRank : 'Siswa',
                        'company' => $company,
                        'platoon' => $platoon,
                        'gender' => $gender,
                        'origin_military_unit' => $kodim,
                        'birth_place' => $birthPlace,
                        'birth_date' => $birthDate,
                        'blood_type' => $bloodType,
                        'status' => $studentStatus,
                        'batch_year' => (int)date('Y'),
                        'admission_year' => (int)date('Y'),
                    ]);

                    // Jika NIK belum ada pada baris berkas, generate 16 digit NIK unik
                    if (empty($nik)) {
                        do {
                            $nik = '3204' . str_pad((string)mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
                        } while (StudentPersonalProfile::isNikRegistered($nik));
                    }

                    $importedCount++;
                }

                // Data Tambahan Komprehensif (IQ, Jasmani, Psikologi, Litpers, Kontak)
                $iqVal = (int)preg_replace('/[^0-9]/', '', (string)($row[$this->getColIdx($columnMap, ['iq_score', 'iq', 'nilai_iq'])] ?? '')) ?: null;
                $psiScoreVal = floatval(str_replace(',', '.', (string)($row[$this->getColIdx($columnMap, ['psychology_score', 'nilai_psi', 'nilai_psikologi'])] ?? ''))) ?: null;
                $psiGradeVal = trim((string)($row[$this->getColIdx($columnMap, ['psychology_grade', 'klasifikasi_psi', 'klasifikasi_psikologi'])] ?? '')) ?: null;
                $branchRecsVal = trim((string)($row[$this->getColIdx($columnMap, ['branch_recommendations', 'saran_kecabangan', 'kecabangan'])] ?? '')) ?: null;

                $jasScoreVal = floatval(str_replace(',', '.', (string)($row[$this->getColIdx($columnMap, ['physical_fitness_score', 'nilai_jasmani', 'nilai_jas_akhir'])] ?? ''))) ?: null;
                $jasGradeVal = trim((string)($row[$this->getColIdx($columnMap, ['physical_fitness_grade', 'klasifikasi_jas', 'klasifikasi_jasmani'])] ?? '')) ?: null;
                $runDistVal = (int)preg_replace('/[^0-9]/', '', (string)($row[$this->getColIdx($columnMap, ['run_12m_distance', 'lari_jarak_m', 'jarak_lari'])] ?? '')) ?: null;
                $runScoreVal = floatval(str_replace(',', '.', (string)($row[$this->getColIdx($columnMap, ['run_12m_score', 'lari_nilai', 'nilai_lari'])] ?? ''))) ?: null;
                $pullCountVal = (int)preg_replace('/[^0-9]/', '', (string)($row[$this->getColIdx($columnMap, ['pull_ups_count', 'pull_up_jml', 'pullup_jml'])] ?? '')) ?: null;
                $pullScoreVal = floatval(str_replace(',', '.', (string)($row[$this->getColIdx($columnMap, ['pull_ups_score', 'pull_up_nilai', 'pullup_nilai'])] ?? ''))) ?: null;
                $sitCountVal = (int)preg_replace('/[^0-9]/', '', (string)($row[$this->getColIdx($columnMap, ['sit_ups_count', 'sit_up_jml', 'situp_jml'])] ?? '')) ?: null;
                $sitScoreVal = floatval(str_replace(',', '.', (string)($row[$this->getColIdx($columnMap, ['sit_ups_score', 'sit_up_nilai', 'situp_nilai'])] ?? ''))) ?: null;
                $pushCountVal = (int)preg_replace('/[^0-9]/', '', (string)($row[$this->getColIdx($columnMap, ['push_ups_count', 'push_up_jml', 'pushup_jml'])] ?? '')) ?: null;
                $pushScoreVal = floatval(str_replace(',', '.', (string)($row[$this->getColIdx($columnMap, ['push_ups_score', 'push_up_nilai', 'pushup_nilai'])] ?? ''))) ?: null;
                $shuttleSecVal = floatval(str_replace(',', '.', (string)($row[$this->getColIdx($columnMap, ['shuttle_run_seconds', 'shuttle_detik'])] ?? ''))) ?: null;
                $shuttleScoreVal = floatval(str_replace(',', '.', (string)($row[$this->getColIdx($columnMap, ['shuttle_run_score', 'shuttle_nilai'])] ?? ''))) ?: null;
                $swimScoreVal = floatval(str_replace(',', '.', (string)($row[$this->getColIdx($columnMap, ['swimming_score', 'renang_nilai'])] ?? ''))) ?: null;
                $swimStyleVal = trim((string)($row[$this->getColIdx($columnMap, ['swimming_style', 'renang_gaya'])] ?? '')) ?: null;

                $litGradeVal = trim((string)($row[$this->getColIdx($columnMap, ['litpers_grade', 'klasifikasi_litpers'])] ?? '')) ?: null;
                $litWrittenVal = floatval(str_replace(',', '.', (string)($row[$this->getColIdx($columnMap, ['litpers_written_score', 'litpers_tertulis'])] ?? ''))) ?: null;
                $litInterviewVal = floatval(str_replace(',', '.', (string)($row[$this->getColIdx($columnMap, ['litpers_interview_score', 'litpers_wawancara'])] ?? ''))) ?: null;
                $finalSelScoreVal = floatval(str_replace(',', '.', (string)($row[$this->getColIdx($columnMap, ['final_selection_score', 'nilai_akhir_seleksi', 'nilai_akhir'])] ?? ''))) ?: null;
                $selTestNoVal = trim((string)($row[$this->getColIdx($columnMap, ['selection_test_number', 'nomor_tes_panpus', 'nomor_tes'])] ?? '')) ?: null;

                $candPhoneVal = trim((string)($row[$this->getColIdx($columnMap, ['candidate_phone', 'no_hp_siswa', 'nomor_hp_calon'])] ?? '')) ?: null;
                $candEmailVal = trim((string)($row[$this->getColIdx($columnMap, ['candidate_email', 'email_siswa', 'email'])] ?? '')) ?: null;
                $origSchoolVal = trim((string)($row[$this->getColIdx($columnMap, ['origin_school', 'asal_sekolah'])] ?? '')) ?: null;
                $fatherNameVal = trim((string)($row[$this->getColIdx($columnMap, ['father_name', 'nama_ortu', 'nama_ortu_wali'])] ?? '')) ?: null;
                $parentOccVal = trim((string)($row[$this->getColIdx($columnMap, ['parent_occupation', 'pekerjaan_ortu', 'pekerjaan_ortu_wali'])] ?? '')) ?: null;
                $emgPhoneVal = trim((string)($row[$this->getColIdx($columnMap, ['emergency_contact_phone', 'no_hp_ortu', 'nomor_hp_ortu_wali'])] ?? '')) ?: null;
                $homeAddrVal = trim((string)($row[$this->getColIdx($columnMap, ['home_address', 'alamat_domisili', 'alamat_calon'])] ?? '')) ?: null;

                $profilePayload = array_filter([
                    'nik' => $nik,
                    'iq_score' => $iqVal,
                    'psychology_score' => $psiScoreVal,
                    'psychology_grade' => $psiGradeVal,
                    'branch_recommendations' => $branchRecsVal,
                    'physical_fitness_score' => $jasScoreVal,
                    'physical_fitness_grade' => $jasGradeVal,
                    'run_12m_distance' => $runDistVal,
                    'run_12m_score' => $runScoreVal,
                    'pull_ups_count' => $pullCountVal,
                    'pull_ups_score' => $pullScoreVal,
                    'sit_ups_count' => $sitCountVal,
                    'sit_ups_score' => $sitScoreVal,
                    'push_ups_count' => $pushCountVal,
                    'push_ups_score' => $pushScoreVal,
                    'shuttle_run_seconds' => $shuttleSecVal,
                    'shuttle_run_score' => $shuttleScoreVal,
                    'swimming_score' => $swimScoreVal,
                    'swimming_style' => $swimStyleVal,
                    'litpers_grade' => $litGradeVal,
                    'litpers_written_score' => $litWrittenVal,
                    'litpers_interview_score' => $litInterviewVal,
                    'final_selection_score' => $finalSelScoreVal,
                    'selection_test_number' => $selTestNoVal,
                    'candidate_phone' => $candPhoneVal,
                    'candidate_email' => $candEmailVal,
                    'origin_school' => $origSchoolVal,
                    'father_name' => $fatherNameVal,
                    'parent_occupation' => $parentOccVal,
                    'emergency_contact_phone' => $emgPhoneVal ?: ('0812' . mt_rand(10000000, 99999999)),
                    'home_address' => $homeAddrVal,
                ], fn($v) => !is_null($v));

                if (!empty($profilePayload)) {
                    StudentPersonalProfile::updateOrCreate(
                        ['student_id' => $student->id],
                        $profilePayload
                    );
                }

                // Hitung BMI jika TB dan BB tersedia
                $bmi = null;
                if ($height > 0 && $weight > 0) {
                    $hM = $height / 100;
                    $bmi = round($weight / ($hM * $hM), 1);
                }

                // Normalisasi nilai status kesehatan
                if (!in_array($dailyHealth, ['Siap Latih', 'Berobat Jalan', 'Rawat Inap Poliklinik', 'Rujuk Rumkit'])) {
                    $dailyHealth = 'Siap Latih';
                }

                // Buat / Perbarui Rekam Medis
                StudentHealthRecord::updateOrCreate(
                    ['student_id' => $student->id],
                    [
                        'height_cm' => $height > 0 ? $height : null,
                        'weight_kg' => $weight > 0 ? $weight : null,
                        'bmi' => $bmi,
                        'blood_pressure' => !empty($bloodPressure) ? $bloodPressure : '120/80',
                        'daily_health_status' => $dailyHealth,
                        'stakes_grade' => !empty($stakes) ? $stakes : 'Stakes I (Sangat Baik)',
                        'doctor_notes' => !empty($doctorNotes) ? $doctorNotes : 'Pemeriksaan berkala import Excel',
                        'last_examined_at' => now(),
                    ]
                );
            }

            DB::commit();

            return [
                'success' => true,
                'imported_count' => $importedCount,
                'updated_count' => $updatedCount,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'imported_count' => 0,
                'updated_count' => 0,
                'errors' => ['Gagal memproses berkas: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Helper cari index kolom berdasar alias kata kunci
     */
    private function getColIdx(array $columnMap, array $aliases): ?int
    {
        foreach ($aliases as $alias) {
            if (isset($columnMap[$alias])) {
                return $columnMap[$alias];
            }
        }
        return null;
    }
}
