<?php

namespace App\Http\Controllers;

use App\Models\Satdik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Pastikan hanya Pimpinan / Super Admin yang berhak mengelola akun pengguna
     */
    protected function authorizeManager()
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isPimpinan()) {
            abort(403, 'Akses Ditolak: Modul Manajemen Akun Pengguna hanya dapat diakses oleh Komandan / Pimpinan Satuan.');
        }
    }

    /**
     * Halaman Utama Manajemen Akun Pengguna SIPANDU-WBK
     */
    public function index(Request $request)
    {
        $this->authorizeManager();

        $satdiks = Satdik::active()->get();
        $selectedSatdikId = $request->query('satdik_id');
        $selectedRole = $request->query('role');
        $keyword = $request->query('q');

        $query = User::with('satdik')
            ->when($selectedSatdikId, function ($q, $satdikId) {
                $q->where('satdik_id', $satdikId);
            })
            ->when($selectedRole, function ($q, $role) {
                $q->where('role_code', $role);
            })
            ->when($keyword, function ($q, $kw) {
                $q->where(function ($sub) use ($kw) {
                    $sub->where('name', 'like', "%{$kw}%")
                        ->orWhere('email', 'like', "%{$kw}%")
                        ->orWhere('phone', 'like', "%{$kw}%");
                });
            })
            ->orderByRaw("CASE WHEN role_code = 'pimpinan' THEN 1 WHEN role_code = 'operator_satdik' THEN 2 ELSE 3 END")
            ->orderBy('satdik_id')
            ->orderBy('id');

        $users = $query->paginate(15)->withQueryString();

        // Statistik Pengguna
        $allUsers = User::all();
        $stats = [
            'total' => $allUsers->count(),
            'pimpinan' => $allUsers->whereIn('role_code', ['pimpinan', 'super_admin'])->count(),
            'operator' => $allUsers->where('role_code', 'operator_satdik')->count(),
            'tim_zi' => $allUsers->where('role_code', 'tim_zi')->count(),
        ];

        return view('users.index', compact('users', 'satdiks', 'stats', 'selectedSatdikId', 'selectedRole', 'keyword'));
    }

    /**
     * Tambah Akun Pengguna Baru
     */
    public function store(Request $request)
    {
        $this->authorizeManager();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role_code' => ['required', 'string', 'in:pimpinan,operator_satdik,tim_zi,poliklinik,super_admin'],
            'satdik_id' => [
                'nullable',
                'exists:satdiks,id',
                Rule::requiredIf(fn() => $request->input('role_code') === 'operator_satdik'),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'email.unique' => 'Alamat email ini sudah terdaftar pada pengguna lain.',
            'satdik_id.required' => 'Satuan Pendidikan wajib dipilih untuk peran Operator Satdik.',
            'password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
        ]);

        // Jika bukan operator satdik, satdik_id diset null (akses menyeluruh)
        $satdikId = ($validated['role_code'] === 'operator_satdik') ? $validated['satdik_id'] : null;

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role_code' => $validated['role_code'],
            'satdik_id' => $satdikId,
            'phone' => $validated['phone'] ?? null,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Akun pengguna [{$user->name}] berhasil dibuat dengan peran {$user->role_label}.");
    }

    /**
     * Perbarui Akun Pengguna
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeManager();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role_code' => ['required', 'string', 'in:pimpinan,operator_satdik,tim_zi,poliklinik,super_admin'],
            'satdik_id' => [
                'nullable',
                'exists:satdiks,id',
                Rule::requiredIf(fn() => $request->input('role_code') === 'operator_satdik'),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'email.unique' => 'Alamat email ini sudah terdaftar pada pengguna lain.',
            'satdik_id.required' => 'Satuan Pendidikan wajib dipilih untuk peran Operator Satdik.',
        ]);

        // Cegah pengguna mencopot perannya sendiri jika dia satu-satunya pimpinan
        if ($user->id === Auth::id() && $validated['role_code'] !== 'pimpinan' && $validated['role_code'] !== 'super_admin') {
            return back()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri dari Pimpinan.');
        }

        $satdikId = ($validated['role_code'] === 'operator_satdik') ? $validated['satdik_id'] : null;

        $updateData = [
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'role_code' => $validated['role_code'],
            'satdik_id' => $satdikId,
            'phone' => $validated['phone'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', "Data akun pengguna [{$user->name}] berhasil diperbarui.");
    }

    /**
     * Hapus Akun Pengguna
     */
    public function destroy(User $user)
    {
        $this->authorizeManager();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Akun pengguna [{$userName}] berhasil dihapus dari sistem.");
    }
}
