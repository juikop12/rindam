<?php

namespace App\Console\Commands;

use Database\Seeders\UserSeeder;
use Illuminate\Console\Command;

class SyncUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronkan semua akun pengguna standar (Super Admin, Danrindam, Operator Danrindam, Operator 5 Satdik, Tim ZI)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai sinkronisasi akun pengguna SIPANDU...');

        $seeder = new UserSeeder();
        $seeder->run();

        $this->info('Berhasil! Seluruh akun pengguna telah disinkronkan:');
        $this->table(
            ['Email', 'Peran (Role)', 'Password'],
            [
                ['superadmin@rindam.mil.id', 'Super Admin', 'password'],
                ['danrindam@rindam.mil.id', 'Danrindam (View-Only)', 'password'],
                ['operator.danrindam@rindam.mil.id', 'Operator Danrindam (CRUD 5 Satdik)', 'password'],
                ['operator.secaba@rindam.mil.id', 'Operator Secaba', 'password'],
                ['operator.secata@rindam.mil.id', 'Operator Secata', 'password'],
                ['operator.dodikjur@rindam.mil.id', 'Operator Dodikjur', 'password'],
                ['operator.dodiklatpur@rindam.mil.id', 'Operator Dodiklatpur', 'password'],
                ['operator.belanegara@rindam.mil.id', 'Operator Bela Negara', 'password'],
                ['tim.zi@rindam.mil.id', 'Tim ZI / Wasrik', 'password'],
            ]
        );

        return Command::SUCCESS;
    }
}
