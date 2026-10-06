<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\EducationProgram;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    /**
     * Tambah Kompi / Peleton Baru secara Manual ke Program Pendidikan
     */
    public function store(Request $request, ?EducationProgram $program = null)
    {
        $currentUser = auth()->user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat memodifikasi peleton/kelas.');
        }

        $programId = $program ? $program->id : $request->input('education_program_id');

        $validated = $request->validate([
            'education_program_id' => ['required_without:program', 'nullable', 'exists:education_programs,id'],
            'company' => ['nullable', 'string', 'max:100'],
            'platoon' => ['nullable', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50'],
            'platoon_leader_name' => ['nullable', 'string', 'max:150'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $finalProgramId = $programId ?? $validated['education_program_id'];
        $prog = EducationProgram::findOrFail($finalProgramId);

        if ($currentUser && !$currentUser->canManageSatdik($prog->satdik_id)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengubah peleton pada Satuan Pendidikan ini.');
        }

        $company = !empty($validated['company']) ? trim($validated['company']) : null;
        $platoon = !empty($validated['platoon']) ? trim($validated['platoon']) : null;

        // Tentukan nama kelas
        $name = !empty($validated['name']) ? trim($validated['name']) : null;
        if (empty($name)) {
            $parts = array_filter([$company, $platoon]);
            $name = !empty($parts) ? implode(' ', $parts) : 'Peleton Baru';
        }

        // Tentukan kode kelas
        $code = !empty($validated['code']) ? strtoupper(trim($validated['code'])) : null;
        if (empty($code)) {
            $code = strtoupper(Str::slug($prog->code . '-' . $name, '-'));
            // Pastikan unik per program
            $baseCode = $code;
            $counter = 1;
            while (Classroom::where('education_program_id', $prog->id)->where('code', $code)->exists()) {
                $code = $baseCode . '-' . $counter;
                $counter++;
            }
        }

        $classroom = Classroom::create([
            'education_program_id' => $prog->id,
            'code' => $code,
            'name' => $name,
            'company' => $company,
            'platoon' => $platoon,
            'platoon_leader_name' => !empty($validated['platoon_leader_name']) ? trim($validated['platoon_leader_name']) : null,
            'capacity' => $validated['capacity'] ?? 35,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => "Kompi/Peleton [{$classroom->name}] berhasil dibuat secara manual.",
                'classroom' => [
                    'id' => $classroom->id,
                    'name' => $classroom->name,
                    'company' => $classroom->company,
                    'platoon' => $classroom->platoon,
                    'code' => $classroom->code,
                    'platoon_leader_name' => $classroom->platoon_leader_name,
                    'capacity' => $classroom->capacity,
                ],
            ]);
        }

        return back()->with('success', "Kompi/Peleton [{$classroom->name}] (Kode: {$classroom->code}) berhasil ditambahkan ke program {$prog->name}.");
    }

    /**
     * Perbarui Data Kompi / Peleton
     */
    public function update(Request $request, Classroom $classroom)
    {
        $currentUser = auth()->user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat memodifikasi peleton/kelas.');
        }
        if ($currentUser && !$currentUser->canManageSatdik($classroom->educationProgram?->satdik_id)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengubah peleton pada Satuan Pendidikan ini.');
        }

        $validated = $request->validate([
            'company' => ['nullable', 'string', 'max:100'],
            'platoon' => ['nullable', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50'],
            'platoon_leader_name' => ['nullable', 'string', 'max:150'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $company = !empty($validated['company']) ? trim($validated['company']) : null;
        $platoon = !empty($validated['platoon']) ? trim($validated['platoon']) : null;

        $name = !empty($validated['name']) ? trim($validated['name']) : null;
        if (empty($name)) {
            $parts = array_filter([$company, $platoon]);
            $name = !empty($parts) ? implode(' ', $parts) : $classroom->name;
        }

        $classroom->update([
            'code' => strtoupper(trim($validated['code'])),
            'name' => $name,
            'company' => $company,
            'platoon' => $platoon,
            'platoon_leader_name' => !empty($validated['platoon_leader_name']) ? trim($validated['platoon_leader_name']) : null,
            'capacity' => $validated['capacity'],
        ]);

        // Sinkronisasi data serdik yang ada di peleton ini
        if ($company || $platoon) {
            $classroom->students()->update([
                'company' => $company,
                'platoon' => $platoon,
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => "Data Kompi/Peleton [{$classroom->name}] berhasil diperbarui.",
                'classroom' => $classroom,
            ]);
        }

        return back()->with('success', "Data Kompi/Peleton [{$classroom->name}] berhasil diperbarui.");
    }

    /**
     * Hapus Kompi / Peleton (Hanya jika belum memiliki serdik)
     */
    public function destroy(Classroom $classroom)
    {
        $currentUser = auth()->user();
        if ($currentUser && !$currentUser->canModifyData()) {
            abort(403, 'Akses Ditolak: Akun Anda berada dalam mode peninjauan (Hanya Baca / View-Only) dan tidak dapat menghapus peleton/kelas.');
        }
        if ($currentUser && !$currentUser->canManageSatdik($classroom->educationProgram?->satdik_id)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk menghapus peleton pada Satuan Pendidikan ini.');
        }
        $studentCount = $classroom->students()->count();
        if ($studentCount > 0) {
            return back()->withErrors("Kompi/Peleton [{$classroom->name}] tidak dapat dihapus karena menaungi {$studentCount} prajurit siswa. Silakan alihkan atau hapus data serdik terlebih dahulu.");
        }

        $name = $classroom->name;
        $classroom->delete();

        return back()->with('success', "Kompi/Peleton [{$name}] berhasil dihapus dari sistem.");
    }

    /**
     * API Ambil Daftar Kompi & Peleton per Program Pendidikan
     */
    public function apiList(EducationProgram $program)
    {
        $classrooms = $program->classrooms()->withCount('students')->get();

        return response()->json([
            'status' => 'success',
            'program' => [
                'id' => $program->id,
                'name' => $program->name,
                'code' => $program->code,
            ],
            'classrooms' => $classrooms,
        ]);
    }
}
