<?php

namespace App\Http\Controllers;

use App\Models\Satdik;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Tampilkan Halaman Pengaturan Sistem & Pejabat Pimpinan
     */
    public function index()
    {
        $settings = SystemSetting::getAllKeyValues();
        $satdiks = Satdik::orderBy('id')->get();

        return view('settings.index', compact('settings', 'satdiks'));
    }

    /**
     * Simpan Perubahan Pengaturan Pejabat & Satuan
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Identitas Satuan Pusat
            'institution_name' => ['required', 'string', 'max:150'],
            'institution_sub' => ['nullable', 'string', 'max:255'],
            'institution_slogan' => ['nullable', 'string', 'max:100'],
            'mako_location' => ['nullable', 'string', 'max:255'],

            // Pejabat Utama Rindam (Danrindam)
            'danrindam_name' => ['required', 'string', 'max:150'],
            'danrindam_rank' => ['nullable', 'string', 'max:50'],
            'danrindam_nrp' => ['nullable', 'string', 'max:30'],
            'danrindam_title' => ['required', 'string', 'max:150'],

            // Wadanrindam
            'wadanrindam_name' => ['nullable', 'string', 'max:150'],
            'wadanrindam_rank' => ['nullable', 'string', 'max:50'],
            'wadanrindam_nrp' => ['nullable', 'string', 'max:30'],
            'wadanrindam_title' => ['nullable', 'string', 'max:150'],

            // Kepala Tim Kesehatan / Poliklinik
            'kakes_name' => ['nullable', 'string', 'max:150'],
            'kakes_rank' => ['nullable', 'string', 'max:50'],
            'kakes_nrp' => ['nullable', 'string', 'max:30'],
            'kakes_title' => ['nullable', 'string', 'max:150'],

            // Komandan Satuan Pendidikan (5 Satdik)
            'satdiks' => ['nullable', 'array'],
            'satdiks.*.id' => ['required', 'integer', 'exists:satdiks,id'],
            'satdiks.*.commander_name' => ['required', 'string', 'max:150'],
            'satdiks.*.commander_title' => ['required', 'string', 'max:150'],
            'satdiks.*.location' => ['nullable', 'string', 'max:255'],
        ]);

        // 1. Simpan Pengaturan Satuan & Pejabat Pusat
        $settingsToSave = [
            'institution_name' => 'institution',
            'institution_sub' => 'institution',
            'institution_slogan' => 'institution',
            'mako_location' => 'institution',

            'danrindam_name' => 'officials',
            'danrindam_rank' => 'officials',
            'danrindam_nrp' => 'officials',
            'danrindam_title' => 'officials',

            'wadanrindam_name' => 'officials',
            'wadanrindam_rank' => 'officials',
            'wadanrindam_nrp' => 'officials',
            'wadanrindam_title' => 'officials',

            'kakes_name' => 'officials',
            'kakes_rank' => 'officials',
            'kakes_nrp' => 'officials',
            'kakes_title' => 'officials',
        ];

        foreach ($settingsToSave as $key => $group) {
            SystemSetting::set($key, $validated[$key] ?? null, $group);
        }

        // 2. Sinkronkan Nama Danrindam ke Akun User Pimpinan
        if (!empty($validated['danrindam_name'])) {
            User::where('role_code', 'pimpinan')->update([
                'name' => $validated['danrindam_name'],
            ]);
        }

        // 3. Perbarui Pejabat Komandan tiap Satdik
        if (!empty($validated['satdiks'])) {
            foreach ($validated['satdiks'] as $satdikData) {
                Satdik::where('id', $satdikData['id'])->update([
                    'commander_name' => $satdikData['commander_name'],
                    'commander_title' => $satdikData['commander_title'],
                    'location' => $satdikData['location'] ?? null,
                ]);
            }
        }

        Cache::forget('app_system_settings_all');

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan sistem dan data pejabat pimpinan berhasil disimpan dan diperbarui.');
    }
}
