<?php

namespace App\Http\Controllers;

use App\Models\Satdik;
use App\Models\EducationProgram;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use App\Models\StudentPersonalDataAccessLog;
use App\Models\StudentPersonalProfile;
use App\Services\StudentExcelService;
use App\Services\StudentManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function __construct(
        protected StudentManagementService $studentService,
        protected StudentExcelService $excelService
    ) {}

    /**
     * Halaman Utama Pengolahan Data Siswa per Satdik
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->isPimpinan() && $currentUser->satdik_id) {
            $selectedSatdikId = $currentUser->satdik_id;
            $satdiks = Satdik::where('id', $selectedSatdikId)->get();
        } else {
            $satdiks = Satdik::active()->get();
            $selectedSatdikId = $request->query('satdik_id');
            $selectedSatdikCode = $request->query('satdik');

            if ($selectedSatdikCode && !$selectedSatdikId) {
                $matched = $satdiks->firstWhere('code', strtoupper($selectedSatdikCode));
                if ($matched) {
                    $selectedSatdikId = $matched->id;
                }
            }
        }

        $selectedProgramId = $request->query('program_id') ? (int)$request->query('program_id') : null;
        $keyword = $request->query('q');
        $status = $request->query('status');
        $tab = $request->query('tab', 'all'); // 'all', 'aktif', 'arsip'

        $query = $this->studentService->getStudentsQuery(
            satdikId: $selectedSatdikId ? (int)$selectedSatdikId : null,
            programId: $selectedProgramId,
            keyword: $keyword,
            status: $status,
            tab: $tab
        );

        $students = $query->paginate(12)->withQueryString();
        $stats = $this->studentService->getSatdikSummaryStats(
            satdikId: $selectedSatdikId ? (int)$selectedSatdikId : null,
            programId: $selectedProgramId
        );
        $selectedSatdik = $selectedSatdikId ? Satdik::find($selectedSatdikId) : null;

        // Daftar program untuk filter per satdik
        $programsQuery = EducationProgram::query();
        if ($selectedSatdikId) {
            $programsQuery->where('satdik_id', $selectedSatdikId);
        }
        $availablePrograms = $programsQuery->orderBy('name')->get();
        $selectedProgram = $selectedProgramId ? EducationProgram::find($selectedProgramId) : null;

        $allProgramsQuery = EducationProgram::with('classrooms')->orderBy('name');
        if ($currentUser && !$currentUser->isPimpinan() && $currentUser->satdik_id) {
            $allProgramsQuery->where('satdik_id', $currentUser->satdik_id);
        }
        $allPrograms = $allProgramsQuery->get();
        $allClassrooms = Classroom::orderBy('name')->get();

        return view('students.index', compact(
            'satdiks',
            'students',
            'stats',
            'selectedSatdikId',
            'selectedSatdik',
            'availablePrograms',
            'selectedProgramId',
            'selectedProgram',
            'allPrograms',
            'allClassrooms',
            'keyword',
            'status',
            'tab'
        ));
    }

    /**
     * Halaman Detail Profil Siswa & Data Pribadi Terproteksi
     */
    public function show(Student $student)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->isPimpinan() && $currentUser->satdik_id) {
            if ($student->satdik_id !== $currentUser->satdik_id) {
                abort(403, 'Akses Ditolak: Anda hanya berwenang mengakses berkas prajurit siswa di ' . ($currentUser->satdik->name ?? 'Satuan Pendidikan Anda') . '.');
            }
        }

        $student->load(['satdik', 'educationProgram', 'classroom', 'personalProfile', 'accessLogs.accessedByUser']);
        
        return view('students.show', compact('student'));
    }

    /**
     * Pengalihan: Sistem pendaftaran baru digantikan dengan Modal CRUD di Buku Induk
     */
    public function create(Request $request)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat menambah data serdik.');
        }

        return redirect()->route('students.index', [
            'action' => 'create',
            'satdik_id' => $request->query('satdik_id'),
            'program_id' => $request->query('program_id'),
        ]);
    }

    /**
     * Simpan Siswa Baru Beserta Data Pribadi Terenkripsi
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat menambah data serdik.');
        }

        if ($currentUser && !$currentUser->hasCrossSatdikAccess() && $currentUser->satdik_id) {
            $request->merge(['satdik_id' => $currentUser->satdik_id]);
        }

        $validated = $request->validate([
            'satdik_id' => ['required', 'exists:satdiks,id'],
            'education_program_id' => ['required', 'exists:education_programs,id'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'company' => ['nullable', 'string', 'max:100'],
            'platoon' => ['nullable', 'string', 'max:100'],
            'full_name' => ['required', 'string', 'max:150'],
            'student_rank' => ['required', 'string', 'max:50'],
            'origin_military_unit' => ['nullable', 'string', 'max:100'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['required', 'in:L,P'],
            'religion' => ['nullable', 'string', 'max:30'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'status' => ['required', 'in:Aktif,Sakit,Dinas Luar,DO / Dikeluarkan,Lulus,Selesai'],
            
            // Validasi Data Pribadi Murni (SIPANDU-WBK) - Tanpa Perbankan & BPJS
            'nik' => ['required', 'digits:16'],
            'family_card_number' => ['nullable', 'digits:16'],
            'mother_name' => ['required', 'string', 'max:100'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['required', 'string', 'max:25'],
            'home_address' => ['nullable', 'string', 'max:500'],

            // Validasi Data Fisik & Medis Awal (Selaras Format Excel)
            'height_cm' => ['nullable', 'integer', 'min:100', 'max:250'],
            'weight_kg' => ['nullable', 'numeric', 'min:30', 'max:200'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'daily_health_status' => ['nullable', 'in:Siap Latih,Berobat Jalan,Rawat Inap Poliklinik,Rujuk Rumkit'],
            'stakes_grade' => ['nullable', 'string', 'max:50'],
            'doctor_notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($currentUser && !$currentUser->hasCrossSatdikAccess() && $currentUser->satdik_id) {
            $validated['satdik_id'] = $currentUser->satdik_id;
            $selectedProg = EducationProgram::findOrFail($validated['education_program_id']);
            if ($selectedProg->satdik_id !== $currentUser->satdik_id) {
                $err = 'Program pendidikan yang dipilih tidak berada di bawah wewenang Satdik Anda (' . ($currentUser->satdik->name ?? 'Satdik Anda') . ').';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['message' => $err, 'errors' => ['education_program_id' => [$err]]], 422);
                }
                return back()->withInput()->withErrors(['education_program_id' => $err]);
            }
        }

        // Pengecekan Data Duplikat Berdasarkan NIK KTP (16 Digit)
        if (StudentPersonalProfile::isNikRegistered($validated['nik'])) {
            $existingProfile = StudentPersonalProfile::findByNik($validated['nik']);
            $existingStudent = $existingProfile?->student;
            $info = $existingStudent 
                ? " atas nama {$existingStudent->full_name} di {$existingStudent->satdik?->name} ({$existingStudent->educationProgram?->name})" 
                : "";
            $errMsg = "NIK KTP {$validated['nik']} sudah terdaftar{$info}. Data serdik duplikat ditolak.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $errMsg,
                    'errors' => ['nik' => [$errMsg]]
                ], 422);
            }
            return back()->withInput()->withErrors([
                'nik' => $errMsg,
            ]);
        }

        $classroomId = $validated['classroom_id'] ?? null;
        $company = !empty($validated['company']) ? trim($validated['company']) : null;
        $platoon = !empty($validated['platoon']) ? trim($validated['platoon']) : null;

        // Jika classroom_id dipilih, sinkronkan nilai kompi & peleton dari rombel tersebut
        if ($classroomId) {
            $cls = Classroom::find($classroomId);
            if ($cls) {
                $company = $company ?: $cls->company;
                $platoon = $platoon ?: $cls->platoon;
            }
        } elseif (!empty($company) || !empty($platoon)) {
            // Jika memilih/mengetik manual Kompi & Peleton tanpa memilih dropdown yang sudah ada,
            // buat otomatis Rombongan Belajar (Classroom) baru di Program Pendidikan ini
            $prog = EducationProgram::findOrFail($validated['education_program_id']);
            $cName = trim("{$company} {$platoon}");
            $cCode = strtoupper(\Illuminate\Support\Str::slug($prog->code . '-' . ($cName ?: 'CLS'), '-'));
            $baseCode = $cCode;
            $counter = 1;
            while (Classroom::where('education_program_id', $prog->id)->where('code', $cCode)->exists()) {
                $cCode = $baseCode . '-' . $counter;
                $counter++;
            }
            $cls = Classroom::firstOrCreate([
                'education_program_id' => $prog->id,
                'name' => $cName,
            ], [
                'code' => $cCode,
                'company' => $company,
                'platoon' => $platoon,
                'capacity' => 35,
            ]);
            $classroomId = $cls->id;
        }

        $studentData = [
            'satdik_id' => $validated['satdik_id'],
            'education_program_id' => $validated['education_program_id'],
            'classroom_id' => $classroomId,
            'company' => $company,
            'platoon' => $platoon,
            'full_name' => $validated['full_name'],
            'student_rank' => $validated['student_rank'],
            'origin_military_unit' => $validated['origin_military_unit'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'],
            'religion' => $validated['religion'] ?? null,
            'blood_type' => $validated['blood_type'] ?? null,
            'status' => $validated['status'],
        ];

        $personalData = [
            'nik' => $validated['nik'],
            'family_card_number' => $validated['family_card_number'] ?? null,
            'mother_name' => $validated['mother_name'],
            'father_name' => $validated['father_name'] ?? null,
            'emergency_contact_name' => $validated['mother_name'],
            'emergency_contact_phone' => $validated['emergency_contact_phone'],
            'home_address' => $validated['home_address'] ?? null,
        ];

        $student = $this->studentService->createStudent($studentData, $personalData, Auth::user() ?? User::first());

        // Hitung BMI jika TB dan BB diisi
        $height = $validated['height_cm'] ?? null;
        $weight = $validated['weight_kg'] ?? null;
        $bmi = null;
        if ($height > 0 && $weight > 0) {
            $hM = $height / 100;
            $bmi = round($weight / ($hM * $hM), 1);
        }

        $dailyHealth = $validated['daily_health_status'] ?? ($validated['status'] === 'Sakit' ? 'Berobat Jalan' : 'Siap Latih');
        $stakes = $validated['stakes_grade'] ?? 'Stakes I (Sangat Baik)';

        // Inisialisasi rekam kesehatan serdik awal
        $student->healthRecord()->create([
            'daily_health_status' => $dailyHealth,
            'stakes_grade' => $stakes,
            'height_cm' => $height,
            'weight_kg' => $weight,
            'bmi' => $bmi,
            'blood_pressure' => $validated['blood_pressure'] ?? '120/80',
            'doctor_notes' => $validated['doctor_notes'] ?? 'Pemeriksaan fisik awal pendaftaran',
            'examined_by' => Auth::user()?->name ?? 'Tim Medis Satdik',
            'last_examined_at' => now(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => "Data Serdik {$student->full_name} berhasil ditambahkan.",
                'student' => $student,
            ]);
        }

        return redirect()->route('students.index')
            ->with('success', "Data Serdik {$student->full_name} berhasil didaftarkan di {$student->satdik->name}. Data pribadi kependudukan terenkripsi AES-256.");
    }

    /**
     * Perbarui Data Pokok Serdik Secara Modal
     */
    public function update(Request $request, Student $student)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat mengubah data serdik.');
        }
        if ($currentUser && !$currentUser->canManageSatdik($student->satdik_id)) {
            abort(403, 'Akses Ditolak: Anda hanya berwenang memperbarui data prajurit siswa di ' . ($currentUser->satdik->name ?? 'Satuan Pendidikan Anda') . '.');
        }
        if ($currentUser && !$currentUser->hasCrossSatdikAccess() && $currentUser->satdik_id) {
            $request->merge(['satdik_id' => $currentUser->satdik_id]);
        }

        $validated = $request->validate([
            'satdik_id' => ['required', 'exists:satdiks,id'],
            'education_program_id' => ['required', 'exists:education_programs,id'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'company' => ['nullable', 'string', 'max:100'],
            'platoon' => ['nullable', 'string', 'max:100'],
            'full_name' => ['required', 'string', 'max:150'],
            'student_rank' => ['required', 'string', 'max:50'],
            'origin_military_unit' => ['nullable', 'string', 'max:100'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['required', 'in:L,P'],
            'religion' => ['nullable', 'string', 'max:30'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'status' => ['required', 'in:Aktif,Sakit,Dinas Luar,DO / Dikeluarkan,Lulus,Selesai'],
            
            // Kolom Data Pribadi & Kontak (Opsional pada update)
            'nik' => ['nullable', 'digits:16'],
            'family_card_number' => ['nullable', 'digits:16'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:25'],
            'home_address' => ['nullable', 'string', 'max:500'],

            // Kolom Fisik & Medis (Opsional)
            'height_cm' => ['nullable', 'integer', 'min:100', 'max:250'],
            'weight_kg' => ['nullable', 'numeric', 'min:30', 'max:200'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'daily_health_status' => ['nullable', 'string'],
            'stakes_grade' => ['nullable', 'string'],
            'doctor_notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Cek NIK duplikat jika NIK diisi dan berubah
        if (!empty($validated['nik'])) {
            $existingProfile = StudentPersonalProfile::findByNik($validated['nik']);
            if ($existingProfile && $existingProfile->student_id !== $student->id) {
                $otherStudent = $existingProfile->student;
                $info = $otherStudent ? " atas nama {$otherStudent->full_name}" : "";
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => "NIK KTP {$validated['nik']} sudah terdaftar{$info} pada serdik lain.",
                        'errors' => ['nik' => ["NIK KTP {$validated['nik']} sudah terdaftar{$info} pada serdik lain."]]
                    ], 422);
                }
                return back()->withInput()->withErrors([
                    'nik' => "NIK KTP {$validated['nik']} sudah terdaftar{$info} pada serdik lain.",
                ]);
            }
        }

        $classroomId = $validated['classroom_id'] ?? null;
        $company = !empty($validated['company']) ? trim($validated['company']) : null;
        $platoon = !empty($validated['platoon']) ? trim($validated['platoon']) : null;

        if ($classroomId) {
            $cls = Classroom::find($classroomId);
            if ($cls) {
                $company = $company ?: $cls->company;
                $platoon = $platoon ?: $cls->platoon;
            }
        } elseif (!empty($company) || !empty($platoon)) {
            $prog = EducationProgram::findOrFail($validated['education_program_id']);
            $cName = trim("{$company} {$platoon}");
            $cCode = strtoupper(\Illuminate\Support\Str::slug($prog->code . '-' . ($cName ?: 'CLS'), '-'));
            $baseCode = $cCode;
            $counter = 1;
            while (Classroom::where('education_program_id', $prog->id)->where('code', $cCode)->exists()) {
                $cCode = $baseCode . '-' . $counter;
                $counter++;
            }
            $cls = Classroom::firstOrCreate([
                'education_program_id' => $prog->id,
                'name' => $cName,
            ], [
                'code' => $cCode,
                'company' => $company,
                'platoon' => $platoon,
                'capacity' => 35,
            ]);
            $classroomId = $cls->id;
        }

        $student->update([
            'satdik_id' => $validated['satdik_id'],
            'education_program_id' => $validated['education_program_id'],
            'classroom_id' => $classroomId,
            'company' => $company,
            'platoon' => $platoon,
            'full_name' => $validated['full_name'],
            'student_rank' => $validated['student_rank'],
            'origin_military_unit' => $validated['origin_military_unit'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'],
            'religion' => $validated['religion'] ?? null,
            'blood_type' => $validated['blood_type'] ?? null,
            'status' => $validated['status'],
        ]);

        // Perbarui data pribadi
        $profile = $student->personalProfile;
        if ($profile) {
            $pData = [];
            if (!empty($validated['nik'])) $pData['nik'] = $validated['nik'];
            if (isset($validated['family_card_number'])) $pData['family_card_number'] = $validated['family_card_number'];
            if (!empty($validated['mother_name'])) $pData['mother_name'] = $validated['mother_name'];
            if (isset($validated['father_name'])) $pData['father_name'] = $validated['father_name'];
            if (!empty($validated['emergency_contact_phone'])) $pData['emergency_contact_phone'] = $validated['emergency_contact_phone'];
            if (isset($validated['home_address'])) $pData['home_address'] = $validated['home_address'];
            
            if (!empty($pData)) {
                $profile->update($pData);
            }
        }

        // Perbarui data status kesehatan dan fisik jika diubah
        $health = $student->healthRecord;
        if ($health) {
            $hData = ['last_examined_at' => now()];
            if (!empty($validated['daily_health_status'])) $hData['daily_health_status'] = $validated['daily_health_status'];
            if (!empty($validated['stakes_grade'])) $hData['stakes_grade'] = $validated['stakes_grade'];
            if (!empty($validated['height_cm'])) $hData['height_cm'] = (int)$validated['height_cm'];
            if (!empty($validated['weight_kg'])) $hData['weight_kg'] = (float)$validated['weight_kg'];
            if (!empty($validated['blood_pressure'])) $hData['blood_pressure'] = $validated['blood_pressure'];
            if (isset($validated['doctor_notes'])) $hData['doctor_notes'] = $validated['doctor_notes'];
            
            $curHeight = $hData['height_cm'] ?? $health->height_cm;
            $curWeight = $hData['weight_kg'] ?? $health->weight_kg;
            if ($curHeight && $curWeight) {
                $heightM = $curHeight / 100;
                $hData['bmi'] = round($curWeight / ($heightM * $heightM), 1);
            }
            $health->update($hData);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => "Data serdik {$student->full_name} berhasil diperbarui.",
                'student' => $student,
            ]);
        }

        return back()->with('success', "Data serdik {$student->full_name} berhasil diperbarui.");
    }

    /**
     * Hapus Data Serdik dari Sistem
     */
    public function destroy(Request $request, Student $student)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat menghapus data serdik.');
        }
        if ($currentUser && !$currentUser->canManageSatdik($student->satdik_id)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk menghapus serdik dari Satdik ini.');
        }

        $name = $student->full_name;
        $student->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => "Data serdik [{$name}] berhasil dihapus.",
            ]);
        }

        return redirect()->route('students.index')
            ->with('success', "Data serdik [{$name}] berhasil dihapus dari sistem.");
    }

    /**
     * API Cascading Dropdown: Program Pendidikan & Kelas per Satdik
     */
    public function getSatdikPrograms(Satdik $satdik)
    {
        $programs = EducationProgram::where('satdik_id', $satdik->id)
            ->with(['classrooms' => function ($q) {
                $q->orderBy('name');
            }])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'satdik' => $satdik,
            'programs' => $programs,
        ]);
    }

    /**
     * Buka & Dekripsi Data Pribadi Sensitif Serdik (Memicu Pencatatan Audit Trail Keamanan Sistem)
     */
    public function revealSensitive(Request $request, Student $student)
    {
        $request->validate([
            'access_reason' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        $currentUser = Auth::user() ?? User::first(); // Fallback untuk dev jika belum login session
        if ($currentUser && !$currentUser->isPimpinan() && $currentUser->satdik_id) {
            if ($student->satdik_id !== $currentUser->satdik_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akses Ditolak: Anda tidak diizinkan membuka data pribadi serdik dari Satdik lain.',
                ], 403);
            }
        }
        
        $decryptedData = $this->studentService->revealSensitiveData(
            student: $student,
            user: $currentUser,
            reason: $request->input('access_reason')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data sensitif berhasil dibuka dan didekripsi. Akses Anda telah dicatat dalam Audit Trail Keamanan Sistem.',
            'data' => $decryptedData,
        ]);
    }

    /**
     * Halaman Pemantauan Audit Trail Akses Data Pribadi (Khusus Super Administrator)
     */
    public function auditLogs(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Modul Audit Log Keamanan Sistem hanya dapat diakses oleh Super Administrator.');
        }

        $logs = StudentPersonalDataAccessLog::with(['student.satdik', 'accessedByUser'])
            ->latest('accessed_at')
            ->paginate(20);

        return view('students.audit_logs', compact('logs'));
    }

    /**
     * Halaman Formulir Penginputan Data Siswa Format Excel
     */
    public function importForm(Request $request)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat mengimpor data serdik.');
        }

        if ($currentUser && !$currentUser->hasCrossSatdikAccess() && $currentUser->satdik_id) {
            $selectedSatdikId = $currentUser->satdik_id;
            $satdiks = Satdik::where('id', $selectedSatdikId)->get();
        } else {
            $satdiks = Satdik::active()->get();
            $selectedSatdikId = $request->query('satdik_id');
        }

        $selectedSatdik = $selectedSatdikId ? Satdik::find($selectedSatdikId) : null;

        return view('students.import', compact('satdiks', 'selectedSatdikId', 'selectedSatdik'));
    }

    /**
     * Unduh Template Format Excel (.xlsx) atau CSV (.csv)
     */
    public function downloadTemplate(Request $request)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->hasCrossSatdikAccess() && $currentUser->satdik_id) {
            $satdikId = $currentUser->satdik_id;
        } else {
            $satdikId = $request->query('satdik_id');
        }

        $format = strtolower($request->query('format', 'xlsx'));

        if ($format === 'csv') {
            $filePath = $this->excelService->exportTemplateCsv($satdikId ? (int)$satdikId : null);
            $filename = 'Format_Input_Serdik_Rindam_' . date('Ymd_His') . '.csv';
            return response()->download($filePath, $filename, [
                'Content-Type' => 'text/csv',
            ])->deleteFileAfterSend(true);
        }

        $filePath = $this->excelService->exportTemplateExcel($satdikId ? (int)$satdikId : null);
        $filename = 'Format_Input_Serdik_Rindam_' . date('Ymd_His') . '.xlsx';
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Proses Pengunggahan & Penginputan Data Siswa dari Berkas Excel
     */
    public function processImport(Request $request)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat mengimpor data serdik.');
        }

        if ($currentUser && !$currentUser->hasCrossSatdikAccess() && $currentUser->satdik_id) {
            $request->merge(['satdik_id' => $currentUser->satdik_id]);
        }

        $request->validate([
            'excel_file' => ['required', 'file', 'max:10240', 'mimes:xlsx,xls,csv,txt'],
            'satdik_id' => ['nullable', 'exists:satdiks,id'],
            'update_existing' => ['nullable', 'boolean'],
        ], [
            'excel_file.required' => 'Pilih berkas Excel (.xlsx / .csv) yang akan diimpor.',
            'excel_file.mimes' => 'Format berkas harus berupa Excel (.xlsx, .xls) atau CSV (.csv).',
            'excel_file.max' => 'Ukuran berkas maksimal 10 MB.',
        ]);

        if ($currentUser && !$currentUser->hasCrossSatdikAccess() && $currentUser->satdik_id) {
            $defaultSatdikId = $currentUser->satdik_id;
        } else {
            $defaultSatdikId = $request->input('satdik_id') ? (int)$request->input('satdik_id') : null;
        }

        $updateExisting = $request->boolean('update_existing', true);

        $result = $this->excelService->importFile(
            file: $request->file('excel_file'),
            defaultSatdikId: $defaultSatdikId,
            updateExisting: $updateExisting
        );

        if (!$result['success']) {
            return redirect()->back()
                ->withErrors($result['errors'] ?? ['Terjadi kesalahan saat memproses berkas Excel.'])
                ->withInput();
        }

        $msg = "Penginputan data dari Excel berhasil! {$result['imported_count']} serdik baru ditambahkan";
        if ($result['updated_count'] > 0) {
            $msg .= ", {$result['updated_count']} serdik diperbarui.";
        } else {
            $msg .= ".";
        }

        if (!empty($result['errors'])) {
            return redirect()->route('students.index')
                ->with('success', $msg)
                ->with('import_warnings', $result['errors']);
        }

        return redirect()->route('students.index')->with('success', $msg);
    }

    /**
     * Perbarui Status Serdik (Aktif, Sakit, Selesai, Lulus, DO)
     * Status Selesai / Lulus otomatis memindahkan siswa ke Arsip (tidak terhitung lagi dalam kekuatan aktif).
     */
    public function updateStatus(Request $request, Student $student)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat mengubah status prajurit siswa.');
        }
        if ($currentUser && !$currentUser->canManageSatdik($student->satdik_id)) {
            abort(403, 'Akses Ditolak: Anda hanya berwenang mengubah status prajurit siswa di Satuan Pendidikan Anda.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Aktif,Sakit,Selesai,Lulus,DO / Dikeluarkan,Dinas Luar'],
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $student->status;

        $student->update(['status' => $newStatus]);

        if (in_array($newStatus, ['Selesai', 'Lulus'])) {
            $student->healthRecord()->update([
                'daily_health_status' => 'Siap Latih',
            ]);
        }

        $isArchived = in_array($newStatus, ['Selesai', 'Lulus', 'DO / Dikeluarkan']);
        $message = $isArchived
            ? "Status Serdik {$student->full_name} berhasil diubah ke [{$newStatus}]. Data serdik telah dialihkan ke ARSIP (tidak terhitung lagi dalam kekuatan aktif)."
            : "Status Serdik {$student->full_name} berhasil diubah ke [{$newStatus}]. Serdik kini kembali TERHITUNG dalam kekuatan pendidikan aktif.";

        return back()->with('success', $message);
    }

    /**
     * Ekspor Format Cetak / PDF Lembar Dossier Pribadi & Buku Induk Prajurit Siswa
     */
    public function exportStudentPdf(Student $student)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->isPimpinan() && $currentUser->satdik_id) {
            if ($student->satdik_id !== $currentUser->satdik_id) {
                abort(403, 'Akses Ditolak: Anda hanya berwenang mencetak dossier prajurit siswa di ' . ($currentUser->satdik->name ?? 'Satuan Pendidikan Anda') . '.');
            }
        }

        $student->load(['satdik', 'educationProgram', 'classroom', 'personalProfile', 'healthRecord']);
        
        return view('students.pdf_student', compact('student'));
    }

    /**
     * Ekspor Format Cetak / PDF Buku Induk & Daftar Nominatif Prajurit Siswa
     */
    public function exportPdf(Request $request)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->isPimpinan() && $currentUser->satdik_id) {
            $selectedSatdikId = $currentUser->satdik_id;
            $satdiks = Satdik::where('id', $selectedSatdikId)->get();
        } else {
            $satdiks = Satdik::active()->get();
            $selectedSatdikId = $request->query('satdik_id');
            $selectedSatdikCode = $request->query('satdik');

            if ($selectedSatdikCode && !$selectedSatdikId) {
                $matched = $satdiks->firstWhere('code', strtoupper($selectedSatdikCode));
                if ($matched) {
                    $selectedSatdikId = $matched->id;
                }
            }
        }

        $selectedProgramId = $request->query('program_id') ? (int)$request->query('program_id') : null;
        $keyword = $request->query('q');
        $status = $request->query('status');
        $tab = $request->query('tab', 'all');

        $query = $this->studentService->getStudentsQuery(
            satdikId: $selectedSatdikId ? (int)$selectedSatdikId : null,
            programId: $selectedProgramId,
            keyword: $keyword,
            status: $status,
            tab: $tab
        );

        $students = $query->get();
        $stats = $this->studentService->getSatdikSummaryStats(
            satdikId: $selectedSatdikId ? (int)$selectedSatdikId : null,
            programId: $selectedProgramId
        );
        $selectedSatdik = $selectedSatdikId ? Satdik::find($selectedSatdikId) : null;
        $selectedProgram = $selectedProgramId ? EducationProgram::find($selectedProgramId) : null;

        return view('students.pdf_nominatif', compact(
            'students',
            'stats',
            'selectedSatdik',
            'selectedProgram',
            'tab'
        ));
    }

    /**
     * Ekspor Format Cetak / PDF Rekaman Forensik Digital Audit Trail Data Sensitif (Super Admin)
     */
    public function exportAuditLogsPdf(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Laporan Audit Trail Forensik hanya dapat dicetak oleh Super Administrator.');
        }

        $logs = StudentPersonalDataAccessLog::with(['student.satdik', 'accessedByUser'])
            ->latest('accessed_at')
            ->limit(100)
            ->get();

        return view('students.pdf_audit_logs', compact('logs'));
    }
}
