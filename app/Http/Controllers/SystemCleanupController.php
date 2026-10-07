<?php

namespace App\Http\Controllers;

use App\Models\Satdik;
use App\Services\SystemCleanupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SystemCleanupController extends Controller
{
    protected function authorizeSuperAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Fitur Pengosongan Data Sistem ini hanya berhak diakses dan dijalankan oleh Super Administrator.');
        }
    }

    /**
     * Tampilkan Halaman Pengosongan & Reset Data Sistem
     */
    public function index(SystemCleanupService $cleanupService)
    {
        $this->authorizeSuperAdmin();

        $stats = $cleanupService->getStatistics();
        $satdiks = Satdik::orderBy('id')->get();

        return view('settings.cleanup', compact('stats', 'satdiks'));
    }

    /**
     * Eksekusi Pengosongan Data Sistem Secara Aman & Terproteksi
     */
    public function destroy(Request $request, SystemCleanupService $cleanupService)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'password' => ['required', 'string'],
            'confirmation' => ['required', 'string'],
            'wipe_target' => ['required', 'string', 'in:students_only,students_and_programs,audit_logs_only,specific_satdik,full_reset'],
            'satdik_id' => ['nullable', 'required_if:wipe_target,specific_satdik', 'exists:satdiks,id'],
            'wipe_satdik_programs' => ['nullable', 'boolean'],
        ], [
            'password.required' => 'Password otentikasi Super Administrator wajib dimasukkan.',
            'confirmation.required' => 'Ketik frasa konfirmasi "KOSONGKAN DATA" untuk melanjutkan.',
            'wipe_target.required' => 'Pilih jenis data yang akan dikosongkan.',
            'satdik_id.required_if' => 'Pilih Satuan Pendidikan (Satdik) yang datanya akan dikosongkan.',
        ]);

        // 1. Verifikasi Password Super Administrator
        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors([
                'password' => 'Password otentikasi Super Administrator tidak sesuai! Operasi pengosongan data dibatalkan demi keamanan.',
            ])->withInput();
        }

        // 2. Verifikasi Frasa Konfirmasi Keamanan
        if (trim($request->confirmation) !== 'KOSONGKAN DATA') {
            return back()->withErrors([
                'confirmation' => 'Frasa konfirmasi tidak valid. Harap ketik "KOSONGKAN DATA" persis dengan huruf kapital.',
            ])->withInput();
        }

        // 3. Eksekusi Pengosongan Data melalui Service
        $summary = $cleanupService->wipeData(
            mode: $request->wipe_target,
            satdikId: $request->satdik_id ? (int) $request->satdik_id : null,
            wipePrograms: (bool) $request->boolean('wipe_satdik_programs')
        );

        // 4. Susun Rincian Pesan Berhasil
        $details = [];
        if ($summary['students'] > 0) $details[] = number_format($summary['students'], 0, ',', '.') . ' data siswa';
        if ($summary['health_records'] > 0) $details[] = number_format($summary['health_records'], 0, ',', '.') . ' rekam medis';
        if ($summary['personal_profiles'] > 0) $details[] = number_format($summary['personal_profiles'], 0, ',', '.') . ' profil pribadi';
        if ($summary['audit_logs'] > 0) $details[] = number_format($summary['audit_logs'], 0, ',', '.') . ' log audit';
        if ($summary['classrooms'] > 0) $details[] = number_format($summary['classrooms'], 0, ',', '.') . ' kompi/peleton';
        if ($summary['education_programs'] > 0) $details[] = number_format($summary['education_programs'], 0, ',', '.') . ' program diklat';
        if ($summary['users'] > 0) $details[] = number_format($summary['users'], 0, ',', '.') . ' akun pengguna tambahan';

        $detailStr = empty($details) ? 'Tidak ada data tersisa untuk dihapus.' : implode(', ', $details);
        $message = "Operasi pengosongan data berhasil dilaksanakan. Data terhapus: {$detailStr}. Cache sistem telah disegarkan kembali.";

        return redirect()->route('settings.cleanup')
            ->with('success', $message)
            ->with('summary', $summary);
    }
}
