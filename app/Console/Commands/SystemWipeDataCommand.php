<?php

namespace App\Console\Commands;

use App\Models\Satdik;
use App\Services\SystemCleanupService;
use Illuminate\Console\Command;

class SystemWipeDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:wipe-data 
                            {--mode=students_only : Mode pengosongan: students_only, students_and_programs, audit_logs_only, specific_satdik, full_reset}
                            {--satdik= : ID atau Kode Satdik jika mode specific_satdik}
                            {--wipe-programs : Hapus program diklat dan kompi jika mode specific_satdik}
                            {--force : Lewati konfirmasi manual}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pengosongan data operasional sistem secara aman oleh Super Admin';

    /**
     * Execute the console command.
     */
    public function handle(SystemCleanupService $cleanupService): int
    {
        $mode = $this->option('mode');
        $validModes = ['students_only', 'students_and_programs', 'audit_logs_only', 'specific_satdik', 'full_reset'];

        if (!in_array($mode, $validModes)) {
            $this->error("Mode tidak valid. Pilihan yang tersedia: " . implode(', ', $validModes));
            return Command::FAILURE;
        }

        $satdikId = null;
        if ($mode === 'specific_satdik') {
            $satdikInput = $this->option('satdik');
            if (empty($satdikInput)) {
                $this->error('Opsi --satdik wajib diisi untuk mode specific_satdik.');
                return Command::FAILURE;
            }

            $satdik = Satdik::where('id', $satdikInput)->orWhere('code', strtoupper($satdikInput))->first();
            if (!$satdik) {
                $this->error("Satdik '{$satdikInput}' tidak ditemukan.");
                return Command::FAILURE;
            }
            $satdikId = $satdik->id;
            $this->warn("Target Satdik: {$satdik->code} ({$satdik->name})");
        }

        $this->alert("PERINGATAN KEAMANAN SISTEM SIPANDU");
        $this->warn("Anda akan menjalankan pengosongan data dengan mode: [{$mode}].");
        $this->warn("Tindakan ini tidak dapat dibatalkan.");

        if (!$this->option('force')) {
            $confirmed = $this->confirm('Apakah Anda yakin ingin melanjutkan pengosongan data ini?', false);
            if (!$confirmed) {
                $this->info('Operasi dibatalkan oleh pengguna.');
                return Command::SUCCESS;
            }

            $phrase = $this->ask('Ketik "KOSONGKAN DATA" untuk konfirmasi final:');
            if (trim((string) $phrase) !== 'KOSONGKAN DATA') {
                $this->error('Frasa konfirmasi salah. Operasi dibatalkan.');
                return Command::FAILURE;
            }
        }

        $this->info('Menjalankan pengosongan data...');

        $summary = $cleanupService->wipeData(
            mode: $mode,
            satdikId: $satdikId,
            wipePrograms: (bool) $this->option('wipe-programs')
        );

        $this->table(
            ['Komponen Data', 'Jumlah Terhapus'],
            [
                ['Data Siswa (Students)', number_format($summary['students'], 0, ',', '.')],
                ['Rekam Medis (Health Records)', number_format($summary['health_records'], 0, ',', '.')],
                ['Profil Pribadi Sensitif (Profiles)', number_format($summary['personal_profiles'], 0, ',', '.')],
                ['Jejak Audit (Access Logs)', number_format($summary['audit_logs'], 0, ',', '.')],
                ['Kompi & Peleton (Classrooms)', number_format($summary['classrooms'], 0, ',', '.')],
                ['Program Pendidikan (Programs)', number_format($summary['education_programs'], 0, ',', '.')],
                ['Akun Pengguna Tambahan (Users)', number_format($summary['users'], 0, ',', '.')],
            ]
        );

        $this->info('Pengosongan data dan penyegaran cache sistem berhasil diselesaikan!');

        return Command::SUCCESS;
    }
}
