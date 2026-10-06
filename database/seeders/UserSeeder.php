<?php

namespace Database\Seeders;

use App\Models\Satdik;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menggunakan updateOrCreate agar aman dijalankan berulang kali
     * tanpa error Duplicate Entry dan dapat menyinkronkan user di perangkat mana pun.
     */
    public function run(): void
    {
        $secaba = Satdik::where('code', 'SECABA')->first();
        $secata = Satdik::where('code', 'SECATA')->first();
        $dodikjur = Satdik::where('code', 'DODIKJUR')->first();
        $dodiklatpur = Satdik::where('code', 'DODIKLATPUR')->first();
        $belanegara = Satdik::where('code', 'BELANEGARA')->first();

        $users = [
            // 1. Super Administrator
            [
                'email' => 'superadmin@rindam.mil.id',
                'name' => 'Super Administrator (Admin Sistem)',
                'password' => Hash::make('password'),
                'role_code' => 'super_admin',
                'satdik_id' => null,
                'phone' => '081100000000',
            ],
            // 2. Danrindam (Komandan - Murni View-Only Lintas 5 Satdik Tanpa CRUD)
            [
                'email' => 'danrindam@rindam.mil.id',
                'name' => 'Kolonel Inf Danrindam III/Siliwangi (Komandan)',
                'password' => Hash::make('password'),
                'role_code' => 'pimpinan',
                'satdik_id' => null,
                'phone' => '081100000001',
            ],
            // 3. Operator Danrindam (Operator Pusat - Hak Akses CRUD Keseluruhan 5 Satdik)
            [
                'email' => 'operator.danrindam@rindam.mil.id',
                'name' => 'Mayor Inf Operator Danrindam (Operator Pusat)',
                'password' => Hash::make('password'),
                'role_code' => 'operator_danrindam',
                'satdik_id' => null,
                'phone' => '081100000002',
            ],
            // 4. Operator Secaba
            [
                'email' => 'operator.secaba@rindam.mil.id',
                'name' => 'Kapt Inf Sutrisno (Operator Secaba)',
                'password' => Hash::make('password'),
                'role_code' => 'operator_satdik',
                'satdik_id' => $secaba?->id,
                'phone' => '081200000002',
            ],
            // 5. Operator Secata
            [
                'email' => 'operator.secata@rindam.mil.id',
                'name' => 'Lettu Inf Rahman (Operator Secata)',
                'password' => Hash::make('password'),
                'role_code' => 'operator_satdik',
                'satdik_id' => $secata?->id,
                'phone' => '081300000003',
            ],
            // 6. Operator Dodikjur
            [
                'email' => 'operator.dodikjur@rindam.mil.id',
                'name' => 'Kapten Inf Darmawan (Operator Dodikjur)',
                'password' => Hash::make('password'),
                'role_code' => 'operator_satdik',
                'satdik_id' => $dodikjur?->id,
                'phone' => '081500000005',
            ],
            // 7. Operator Dodiklatpur
            [
                'email' => 'operator.dodiklatpur@rindam.mil.id',
                'name' => 'Lettu Inf Hendro (Operator Dodiklatpur)',
                'password' => Hash::make('password'),
                'role_code' => 'operator_satdik',
                'satdik_id' => $dodiklatpur?->id,
                'phone' => '081600000006',
            ],
            // 8. Operator Bela Negara
            [
                'email' => 'operator.belanegara@rindam.mil.id',
                'name' => 'Kapten Czi Anwar (Operator Belanegara)',
                'password' => Hash::make('password'),
                'role_code' => 'operator_satdik',
                'satdik_id' => $belanegara?->id,
                'phone' => '081700000007',
            ],
            // 9. Inspektorat / Tim ZI (View-Only)
            [
                'email' => 'tim.zi@rindam.mil.id',
                'name' => 'Mayor Inf Hadi (Inspektorat / Tim ZI)',
                'password' => Hash::make('password'),
                'role_code' => 'tim_zi',
                'satdik_id' => null,
                'phone' => '081400000004',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
