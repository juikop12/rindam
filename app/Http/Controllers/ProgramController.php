<?php

namespace App\Http\Controllers;

use App\Models\Satdik;
use App\Models\EducationProgram;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    /**
     * Halaman Utama Menu Program Pendidikan per Satdik
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
        }

        $keyword = $request->query('q');
        $statusFilter = $request->query('status'); // 'Berjalan', 'Perencanaan', 'Selesai', 'Ditutup'

        $query = EducationProgram::with(['satdik', 'classrooms'])
            ->withCount('students')
            ->when($selectedSatdikId, function ($q, $satdikId) {
                $q->where('satdik_id', $satdikId);
            })
            ->when($statusFilter, function ($q, $st) {
                $q->where('status', $st);
            })
            ->when($keyword, function ($q, $kw) {
                $q->where(function ($sub) use ($kw) {
                    $sub->where('name', 'like', "%{$kw}%")
                        ->orWhere('code', 'like', "%{$kw}%")
                        ->orWhere('academic_year', 'like', "%{$kw}%");
                });
            })
            ->orderBy('satdik_id')
            ->orderBy('id');

        $programs = $query->get()->map(function ($program) {
            $students = $program->students;
            $counted = $students->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count();
            $ready = $students->where('status', 'Aktif')->count();
            $sick = $students->where('status', 'Sakit')->count();
            $archived = $students->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan'])->count();
            $finished = $students->whereIn('status', ['Selesai', 'Lulus'])->count();

            return [
                'model' => $program,
                'id' => $program->id,
                'satdik_id' => $program->satdik_id,
                'satdik_code' => $program->satdik?->code ?? '-',
                'satdik_name' => $program->satdik?->name ?? '-',
                'location' => $program->satdik?->location ?? 'Ksatrian Rindam',
                'name' => $program->name,
                'code' => $program->code,
                'academic_year' => $program->academic_year,
                'batch_number' => $program->batch_number,
                'start_date' => $program->start_date,
                'end_date' => $program->end_date,
                'status' => $program->status,
                'classrooms' => $program->classrooms,
                'total_students' => $students->count(),
                'counted_students' => $counted,   // TERHITUNG AKTIF
                'ready_students' => $ready,
                'sick_students' => $sick,
                'archived_students' => $archived, // ARSIP (TIDAK TERHITUNG)
                'finished_students' => $finished,
            ];
        });

        // Statistik Makro Program & Serdik
        $allPrograms = EducationProgram::all();
        $totalPrograms = $allPrograms->count();
        $activePrograms = $allPrograms->where('status', 'Berjalan')->count();
        
        $totalCountedStudents = Student::whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count();
        $totalArchivedStudents = Student::whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan'])->count();
        $totalStudents = Student::count();

        $selectedSatdik = $selectedSatdikId ? Satdik::find($selectedSatdikId) : null;

        return view('programs.index', compact(
            'satdiks',
            'programs',
            'selectedSatdikId',
            'selectedSatdik',
            'keyword',
            'statusFilter',
            'totalPrograms',
            'activePrograms',
            'totalCountedStudents',
            'totalArchivedStudents',
            'totalStudents'
        ));
    }

    /**
     * Halaman Detail Program Pendidikan & Daftar Serdik di Dalamnya
     */
    public function show(EducationProgram $program, Request $request)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->isPimpinan() && $currentUser->satdik_id) {
            if ($program->satdik_id !== $currentUser->satdik_id) {
                abort(403, 'Akses Ditolak: Anda hanya berwenang mengakses program pendidikan pada Satuan Pendidikan Anda (' . ($currentUser->satdik->name ?? 'Satdik Anda') . ').');
            }
        }

        $program->load(['satdik', 'classrooms']);
        
        $tab = $request->query('tab', 'all'); // 'all', 'aktif', 'arsip'
        $classroomId = $request->query('classroom_id');
        $keyword = $request->query('q');

        $studentQuery = Student::with(['classroom', 'healthRecord', 'personalProfile'])
            ->where('education_program_id', $program->id);

        if ($tab === 'aktif') {
            $studentQuery->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar']);
        } elseif ($tab === 'arsip') {
            $studentQuery->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan']);
        }

        if (!empty($classroomId)) {
            $studentQuery->where('classroom_id', $classroomId);
        }

        if (!empty($keyword)) {
            $studentQuery->search($keyword);
        }

        $students = $studentQuery->paginate(15)->withQueryString();

        // Hitung statistik untuk program ini
        $allProgStudents = $program->students;
        $countedCount = $allProgStudents->whereIn('status', ['Aktif', 'Sakit', 'Dinas Luar'])->count();
        $readyCount = $allProgStudents->where('status', 'Aktif')->count();
        $sickCount = $allProgStudents->where('status', 'Sakit')->count();
        $archivedCount = $allProgStudents->whereIn('status', ['Selesai', 'Lulus', 'DO / Dikeluarkan'])->count();
        $finishedCount = $allProgStudents->whereIn('status', ['Selesai', 'Lulus'])->count();

        return view('programs.show', compact(
            'program',
            'students',
            'tab',
            'classroomId',
            'keyword',
            'countedCount',
            'readyCount',
            'sickCount',
            'archivedCount',
            'finishedCount'
        ));
    }

    /**
     * Tambah Program Pendidikan Baru untuk Satdik
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat menambah program pendidikan.');
        }

        if ($currentUser && !$currentUser->hasCrossSatdikAccess() && $currentUser->satdik_id) {
            $request->merge(['satdik_id' => $currentUser->satdik_id]);
        }

        $validated = $request->validate([
            'satdik_id' => ['required', 'exists:satdiks,id'],
            'code' => ['required', 'string', 'max:50', 'unique:education_programs,code'],
            'name' => ['required', 'string', 'max:150'],
            'academic_year' => ['required', 'string', 'max:10'],
            'batch_number' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:Perencanaan,Berjalan,Selesai,Ditutup'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'initial_classrooms' => ['nullable', 'string', 'max:255'],
        ]);

        if ($currentUser && !$currentUser->hasCrossSatdikAccess() && $currentUser->satdik_id) {
            $validated['satdik_id'] = $currentUser->satdik_id;
        }

        $program = EducationProgram::create([
            'satdik_id' => $validated['satdik_id'],
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'academic_year' => $validated['academic_year'],
            'batch_number' => $validated['batch_number'] ?? 1,
            'status' => $validated['status'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ]);

        // Buat kelas/peleton awal jika diinputkan
        if (!empty($validated['initial_classrooms'])) {
            $classNames = array_map('trim', explode(',', $validated['initial_classrooms']));
            foreach ($classNames as $idx => $cName) {
                if (!empty($cName)) {
                    $company = null;
                    $platoon = null;
                    if (preg_match('/(Kompi\s+[A-Za-z0-9\s]+?)(?=\s+(?:Peleton|Ton)|$)/i', $cName, $mKompi)) {
                        $company = trim($mKompi[1]);
                    } elseif (preg_match('/(Kompi\s+[A-Za-z0-9]+)/i', $cName, $mKompi)) {
                        $company = trim($mKompi[1]);
                    }
                    if (preg_match('/((?:Peleton|Ton)\s+[^,\n]+)/i', $cName, $mPlt)) {
                        $platoon = trim($mPlt[1]);
                    }
                    if (!$company && !$platoon) {
                        $company = $cName;
                    }

                    Classroom::create([
                        'education_program_id' => $program->id,
                        'code' => strtoupper(Str::slug($program->code . '-CLS-' . ($idx + 1))),
                        'name' => $cName,
                        'company' => $company,
                        'platoon' => $platoon,
                        'capacity' => 40,
                    ]);
                }
            }
        }

        return redirect()->route('programs.index', ['satdik_id' => $program->satdik_id])
            ->with('success', "Program Pendidikan [{$program->name}] berhasil dibuat untuk {$program->satdik->code}.");
    }

    /**
     * Perbarui Program Pendidikan
     */
    public function update(Request $request, EducationProgram $program)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat mengubah program pendidikan.');
        }
        if ($currentUser && !$currentUser->canManageSatdik($program->satdik_id)) {
            abort(403, 'Akses Ditolak: Anda hanya berwenang memperbarui program pendidikan pada Satuan Pendidikan Anda.');
        }
        if ($currentUser && !$currentUser->hasCrossSatdikAccess() && $currentUser->satdik_id) {
            $request->merge(['satdik_id' => $currentUser->satdik_id]);
        }

        $validated = $request->validate([
            'satdik_id' => ['required', 'exists:satdiks,id'],
            'code' => ['required', 'string', 'max:50', 'unique:education_programs,code,' . $program->id],
            'name' => ['required', 'string', 'max:150'],
            'academic_year' => ['required', 'string', 'max:10'],
            'batch_number' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:Perencanaan,Berjalan,Selesai,Ditutup'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        if ($currentUser && !$currentUser->hasCrossSatdikAccess() && $currentUser->satdik_id) {
            $validated['satdik_id'] = $currentUser->satdik_id;
        }

        $program->update([
            'satdik_id' => $validated['satdik_id'],
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'academic_year' => $validated['academic_year'],
            'batch_number' => $validated['batch_number'] ?? 1,
            'status' => $validated['status'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ]);

        return back()->with('success', "Program Pendidikan [{$program->name}] berhasil diperbarui.");
    }

    /**
     * Hapus Program Pendidikan (hanya jika belum memiliki siswa)
     */
    public function destroy(EducationProgram $program)
    {
        $currentUser = Auth::user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat menghapus program pendidikan.');
        }
        if ($currentUser && !$currentUser->canManageSatdik($program->satdik_id)) {
            abort(403, 'Akses Ditolak: Anda hanya berwenang menghapus program pendidikan pada Satuan Pendidikan Anda.');
        }

        $studentCount = $program->students()->count();
        if ($studentCount > 0) {
            return back()->withErrors("Program [{$program->name}] tidak dapat dihapus karena memiliki {$studentCount} peserta didik. Ubah status program menjadi 'Ditutup' atau 'Selesai' sebagai gantinya.");
        }

        $name = $program->name;
        $program->classrooms()->delete();
        $program->delete();

        return redirect()->route('programs.index')
            ->with('success', "Program Pendidikan [{$name}] berhasil dihapus.");
    }
}
