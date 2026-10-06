<?php

namespace Database\Seeders;

use App\Models\Satdik;
use App\Models\EducationProgram;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentPersonalProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat 5 Satuan Pendidikan (Satdik) Jajaran Rindam III/Siliwangi
        $secaba = Satdik::create([
            'code' => 'SECABA',
            'name' => 'Sekolah Calon Bintara (Secaba) Rindam III/Siliwangi',
            'commander_title' => 'Komandan Secaba Rindam III/Siliwangi',
            'commander_name' => 'Letkol Inf Hendra Prasetyo, S.I.P.',
            'location' => 'Ksatrian Secaba Rindam III/Siliwangi (Bihbul, Bandung)',
            'description' => 'Menyelenggarakan Pendidikan Pertama Bintara (Semaba) dan Pembentukan Bintara (Diktukba) TNI AD.',
            'is_active' => true,
        ]);

        $secata = Satdik::create([
            'code' => 'SECATA',
            'name' => 'Sekolah Calon Tamtama (Secata) Rindam III/Siliwangi',
            'commander_title' => 'Komandan Secata Rindam III/Siliwangi',
            'commander_name' => 'Letkol Inf Bambang Yudho, M.Tr.(Han)',
            'location' => 'Ksatrian Secata Rindam III/Siliwangi (Pangalengan, Bandung)',
            'description' => 'Menyelenggarakan Pendidikan Pertama Tamtama (Semata) PK TNI AD.',
            'is_active' => true,
        ]);

        $dodikjur = Satdik::create([
            'code' => 'DODIKJUR',
            'name' => 'Depo Pendidikan Kejuruan (Dodikjur) Rindam III/Siliwangi',
            'commander_title' => 'Komandan Dodikjur Rindam III/Siliwangi',
            'commander_name' => 'Letkol Inf Agus Supriyanto',
            'location' => 'Ksatrian Dodikjur Rindam III/Siliwangi (Lembang, Bandung Barat)',
            'description' => 'Menyelenggarakan Pendidikan Kejuruan Bintara dan Tamtama lintas kecabangan.',
            'is_active' => true,
        ]);

        $dodiklatpur = Satdik::create([
            'code' => 'DODIKLATPUR',
            'name' => 'Depo Pendidikan Latihan Tempur (Dodiklatpur) Rindam III/Siliwangi',
            'commander_title' => 'Komandan Dodiklatpur Rindam III/Siliwangi',
            'commander_name' => 'Letkol Inf Dwi Wicaksono',
            'location' => 'Ksatrian Dodiklatpur Rindam III/Siliwangi (Ciuyah, Lebak)',
            'description' => 'Menyelenggarakan pendidikan kemahiran taktik regu, pertempuran hutan, dan menembak mahir.',
            'is_active' => true,
        ]);

        $belanegara = Satdik::create([
            'code' => 'BELANEGARA',
            'name' => 'Depo Pendidikan Bela Negara Rindam III/Siliwangi',
            'commander_title' => 'Komandan Dodik Bela Negara Rindam III/Siliwangi',
            'commander_name' => 'Letkol Czi Arif Budiman',
            'location' => 'Ksatrian Bela Negara Rindam III/Siliwangi (Lembang, Bandung Barat)',
            'description' => 'Menyelenggarakan Pelatihan Dasar Kemiliteran Komponen Cadangan (Komcad) dan Pembinaan Karakter Bangsa.',
            'is_active' => true,
        ]);

        // 2. Buat Pengguna Percontohan Berdasarkan Peran
        $superAdmin = User::create([
            'name' => 'Super Administrator (Admin Sistem)',
            'email' => 'superadmin@rindam.mil.id',
            'password' => Hash::make('password'),
            'role_code' => 'super_admin',
            'phone' => '081100000000',
        ]);

        $adminPimpinan = User::create([
            'name' => 'Kolonel Inf Danrindam III/Siliwangi (Komandan)',
            'email' => 'danrindam@rindam.mil.id',
            'password' => Hash::make('password'),
            'role_code' => 'pimpinan',
            'phone' => '081100000001',
        ]);

        $opDanrindam = User::create([
            'name' => 'Mayor Inf Operator Danrindam (Operator Pusat)',
            'email' => 'operator.danrindam@rindam.mil.id',
            'password' => Hash::make('password'),
            'role_code' => 'operator_danrindam',
            'phone' => '081100000002',
        ]);

        $opSecaba = User::create([
            'name' => 'Kapt Inf Sutrisno (Operator Secaba)',
            'email' => 'operator.secaba@rindam.mil.id',
            'password' => Hash::make('password'),
            'satdik_id' => $secaba->id,
            'role_code' => 'operator_satdik',
            'phone' => '081200000002',
        ]);

        $opSecata = User::create([
            'name' => 'Lettu Inf Rahman (Operator Secata)',
            'email' => 'operator.secata@rindam.mil.id',
            'password' => Hash::make('password'),
            'satdik_id' => $secata->id,
            'role_code' => 'operator_satdik',
            'phone' => '081300000003',
        ]);

        $timZi = User::create([
            'name' => 'Mayor Inf Hadi (Inspektorat / Tim ZI)',
            'email' => 'tim.zi@rindam.mil.id',
            'password' => Hash::make('password'),
            'role_code' => 'tim_zi',
            'phone' => '081400000004',
        ]);

        $opDodikjur = User::create([
            'name' => 'Kapten Inf Darmawan (Operator Dodikjur)',
            'email' => 'operator.dodikjur@rindam.mil.id',
            'password' => Hash::make('password'),
            'satdik_id' => $dodikjur->id,
            'role_code' => 'operator_satdik',
            'phone' => '081500000005',
        ]);

        $opDodiklatpur = User::create([
            'name' => 'Lettu Inf Hendro (Operator Dodiklatpur)',
            'email' => 'operator.dodiklatpur@rindam.mil.id',
            'password' => Hash::make('password'),
            'satdik_id' => $dodiklatpur->id,
            'role_code' => 'operator_satdik',
            'phone' => '081600000006',
        ]);

        $opBelanegara = User::create([
            'name' => 'Kapten Czi Anwar (Operator Belanegara)',
            'email' => 'operator.belanegara@rindam.mil.id',
            'password' => Hash::make('password'),
            'satdik_id' => $belanegara->id,
            'role_code' => 'operator_satdik',
            'phone' => '081700000007',
        ]);

        // 3. Program Pendidikan & Kelas
        // Secaba
        $progSecaba = EducationProgram::create([
            'satdik_id' => $secaba->id,
            'code' => 'DIKMABA-2026',
            'name' => 'DIKMABA TA 2026',
            'academic_year' => '2026',
            'batch_number' => 1,
            'start_date' => '2026-03-01',
            'end_date' => '2026-07-30',
            'status' => 'Berjalan',
        ]);

        $classSecaba1 = Classroom::create([
            'education_program_id' => $progSecaba->id,
            'code' => 'SCB-A1',
            'name' => 'Kompi A Peleton 1',
            'platoon_leader_name' => 'Lettu Inf Suryadi',
            'capacity' => 35,
        ]);
        $classSecaba2 = Classroom::create([
            'education_program_id' => $progSecaba->id,
            'code' => 'SCB-A2',
            'name' => 'Kompi A Peleton 2',
            'platoon_leader_name' => 'Letda Inf Bagas Pratama',
            'capacity' => 35,
        ]);

        // Secata
        $progSecata = EducationProgram::create([
            'satdik_id' => $secata->id,
            'code' => 'DIKMATA-2026',
            'name' => 'DIKMATA TA 2026',
            'academic_year' => '2026',
            'batch_number' => 1,
            'start_date' => '2026-04-15',
            'end_date' => '2026-09-15',
            'status' => 'Berjalan',
        ]);

        $classSecata1 = Classroom::create([
            'education_program_id' => $progSecata->id,
            'code' => 'SCT-B1',
            'name' => 'Kompi B Peleton 1',
            'platoon_leader_name' => 'Lettu Inf Fauzan',
            'capacity' => 40,
        ]);

        // Dodikjur
        $progDodikjur = EducationProgram::create([
            'satdik_id' => $dodikjur->id,
            'code' => 'DIKJURBA-2026',
            'name' => 'DIKJURBA TA 2026',
            'academic_year' => '2026',
            'batch_number' => 1,
            'start_date' => '2026-08-01',
            'end_date' => '2026-11-30',
            'status' => 'Berjalan',
        ]);
        $classDodikjur1 = Classroom::create([
            'education_program_id' => $progDodikjur->id,
            'code' => 'DJK-INF-1',
            'name' => 'Peleton Taktik Senban',
            'platoon_leader_name' => 'Kapten Inf Wahyudi',
            'capacity' => 30,
        ]);

        // Dodiklatpur
        $progLatpur = EducationProgram::create([
            'satdik_id' => $dodiklatpur->id,
            'code' => 'DIKLATPUR-2026',
            'name' => 'DIKLATPUR TA 2026',
            'academic_year' => '2026',
            'batch_number' => 1,
            'start_date' => '2026-09-01',
            'end_date' => '2026-10-30',
            'status' => 'Berjalan',
        ]);
        $classLatpur1 = Classroom::create([
            'education_program_id' => $progLatpur->id,
            'code' => 'LTP-SNIP-1',
            'name' => 'Peleton Runduk / Tembak Jitu',
            'platoon_leader_name' => 'Mayor Inf Rahmat Hidayat',
            'capacity' => 25,
        ]);

        // Bela Negara
        $progBelaNegara = EducationProgram::create([
            'satdik_id' => $belanegara->id,
            'code' => 'BELANEGARA-2026',
            'name' => 'BELA NEGARA TA 2026',
            'academic_year' => '2026',
            'batch_number' => 1,
            'start_date' => '2026-05-01',
            'end_date' => '2026-08-01',
            'status' => 'Berjalan',
        ]);
        $classBelaNegara1 = Classroom::create([
            'education_program_id' => $progBelaNegara->id,
            'code' => 'KMC-YON-1',
            'name' => 'Kompi Senapan Komcad',
            'platoon_leader_name' => 'Kapten Inf Danang',
            'capacity' => 50,
        ]);

        // 4. Data Siswa Realistis dengan Profil Pribadi Terenkripsi (UU PDP No. 27/2022)
        $sampleStudents = [
            // Secaba
            [
                'satdik_id' => $secaba->id,
                'education_program_id' => $progSecaba->id,
                'classroom_id' => $classSecaba1->id,
                'nosik' => 'SECABA.2026.1.001',
                'full_name' => 'Dimas Arya Pratama',
                'student_rank' => 'Siswa Calon Bintara',
                'origin_military_unit' => 'Kodim 0501/BS Jakarta Pusat',
                'birth_place' => 'Jakarta',
                'birth_date' => '2004-05-14',
                'gender' => 'L',
                'religion' => 'Islam',
                'blood_type' => 'O',
                'education_level' => 'SMA IPA',
                'status' => 'Aktif',
                // Data Sensitif (Otomatis Terenkripsi AES-256)
                'nik' => '3171011405040001',
                'family_card_number' => '3171011002980005',
                'mother_name' => 'Siti Nurjanah',
                'father_name' => 'Bambang Sudibyo',
                'emergency_contact_phone' => '081298765432',
                'bpjs_number' => '0001234567890',
                'bank_name' => 'BRI Cabang Kodam Jaya',
                'bank_account_number' => '020601004567501',
                'home_address' => 'Jl. Salemba Tengah No. 42, RT 04/RW 03, Jakarta Pusat',
                'medical_history' => 'Bebas riwayat alergi obat, riwayat patah tulang lengan kiri tahun 2019 (sembuh total)',
                'psychological_record' => 'Stakes Keswa: I (Memenuhi Syarat Bintara Tempur), Disiplin Baik',
            ],
            [
                'satdik_id' => $secaba->id,
                'education_program_id' => $progSecaba->id,
                'classroom_id' => $classSecaba1->id,
                'nosik' => 'SECABA.2026.1.002',
                'full_name' => 'Rizky Firmansyah',
                'student_rank' => 'Siswa Calon Bintara',
                'origin_military_unit' => 'Kodim 0504/Jakarta Selatan',
                'birth_place' => 'Bogor',
                'birth_date' => '2003-11-20',
                'gender' => 'L',
                'religion' => 'Islam',
                'blood_type' => 'A',
                'education_level' => 'SMK Teknik Mesin',
                'status' => 'Aktif',
                'nik' => '3201022011030008',
                'family_card_number' => '3201021406010003',
                'mother_name' => 'Ratna Sari Dewi',
                'father_name' => 'Ahmad Suhendar',
                'emergency_contact_phone' => '085712345678',
                'bpjs_number' => '0001987654321',
                'bank_name' => 'BRI Cabang Khusus TNI',
                'bank_account_number' => '020601009876503',
                'home_address' => 'Kp. Cilebut Barat No. 18, RT 02/RW 05, Sukaraja, Kab. Bogor',
                'medical_history' => 'Alergi dingin ringan, riwayat asma anak (tidak aktif)',
                'psychological_record' => 'Stakes Keswa: II, Kemampuan Logika & Kepemimpinan Tinggi',
            ],
            [
                'satdik_id' => $secaba->id,
                'education_program_id' => $progSecaba->id,
                'classroom_id' => $classSecaba2->id,
                'nosik' => 'SECABA.2026.1.003',
                'full_name' => 'Yohanes Kristian',
                'student_rank' => 'Siswa Calon Bintara',
                'origin_military_unit' => 'Kodim 0508/Depok',
                'birth_place' => 'Kupang',
                'birth_date' => '2004-01-10',
                'gender' => 'L',
                'religion' => 'Kristen Protestan',
                'blood_type' => 'B',
                'education_level' => 'SMA IPS',
                'status' => 'Sakit',
                'nik' => '5371011001040004',
                'family_card_number' => '5371012508990001',
                'mother_name' => 'Maria Magdalena',
                'father_name' => 'Paulus Dima',
                'emergency_contact_phone' => '082144556677',
                'bpjs_number' => '0002123487650',
                'bank_name' => 'BRI Unit Cimanggis',
                'bank_account_number' => '034101001234509',
                'home_address' => 'Jl. Radar Auri No. 5, Cimanggis, Kota Depok',
                'medical_history' => 'Sedang dirawat di Poliklinik Satdik karena demam tifoid, alergi amoksisilin',
                'psychological_record' => 'Stakes Keswa: I, Moril Stabil dan Pantang Menyerah',
            ],

            // Secata
            [
                'satdik_id' => $secata->id,
                'education_program_id' => $progSecata->id,
                'classroom_id' => $classSecata1->id,
                'nosik' => 'SECATA.2026.1.001',
                'full_name' => 'Budi Santoso',
                'student_rank' => 'Siswa Prajurit Dua',
                'origin_military_unit' => 'Kodim 0507/Bekasi',
                'birth_place' => 'Bekasi',
                'birth_date' => '2005-08-17',
                'gender' => 'L',
                'religion' => 'Islam',
                'blood_type' => 'O',
                'education_level' => 'SMA Negeri 1',
                'status' => 'Aktif',
                'nik' => '3275011708050002',
                'family_card_number' => '3275010903000007',
                'mother_name' => 'Aminah Kusuma',
                'father_name' => 'Sugiyono',
                'emergency_contact_phone' => '081399887766',
                'bpjs_number' => '0003456712340',
                'bank_name' => 'BRI Cabang Bekasi',
                'bank_account_number' => '015601007890508',
                'home_address' => 'Desa Tambun Selatan No. 12, RT 01/RW 09, Bekasi',
                'medical_history' => 'Sehat prima, Garjas A Lari 12 menit: 3.150 meter',
                'psychological_record' => 'Stakes Keswa: I, Ketahanan Mental Sangat Tangguh',
            ],
            [
                'satdik_id' => $secata->id,
                'education_program_id' => $progSecata->id,
                'classroom_id' => $classSecata1->id,
                'nosik' => 'SECATA.2026.1.002',
                'full_name' => 'I Made Wardana',
                'student_rank' => 'Siswa Prajurit Dua',
                'origin_military_unit' => 'Kodim 0503/Jakarta Barat',
                'birth_place' => 'Denpasar',
                'birth_date' => '2004-12-05',
                'gender' => 'L',
                'religion' => 'Hindu',
                'blood_type' => 'AB',
                'education_level' => 'SMK Otomotif',
                'status' => 'Aktif',
                'nik' => '5171020512040003',
                'family_card_number' => '5171021111970002',
                'mother_name' => 'Ni Luh Putu Rai',
                'father_name' => 'I Ketut Sudirga',
                'emergency_contact_phone' => '081988776655',
                'bpjs_number' => '0003987654329',
                'bank_name' => 'BRI TNI AD Jakbar',
                'bank_account_number' => '015601004321502',
                'home_address' => 'Jl. Kebon Jeruk Raya No. 88, Kebon Jeruk, Jakarta Barat',
                'medical_history' => 'Tidak ada kelainan fisik, penglihatan 6/6 tanpa kacamata',
                'psychological_record' => 'Stakes Keswa: I, Minat Bakat Senjata Mesin',
            ],

            // Dodikjur
            [
                'satdik_id' => $dodikjur->id,
                'education_program_id' => $progDodikjur->id,
                'classroom_id' => $classDodikjur1->id,
                'nosik' => 'DODIKJUR.2026.1.001',
                'full_name' => 'Serda M. Fadhil Ramadhan',
                'student_rank' => 'Sersan Dua Siswa',
                'origin_military_unit' => 'Yonif 201/Jaya Yudha',
                'birth_place' => 'Bandung',
                'birth_date' => '2002-10-09',
                'gender' => 'L',
                'religion' => 'Islam',
                'blood_type' => 'A',
                'education_level' => 'Diktukba TNI AD 2024',
                'status' => 'Aktif',
                'nik' => '3273010910020006',
                'family_card_number' => '3273011504950009',
                'mother_name' => 'Eni Rohaeni',
                'father_name' => 'Deden Kurnia',
                'emergency_contact_phone' => '087811223344',
                'bpjs_number' => '0004567890123',
                'bank_name' => 'BRI Yonif 201',
                'bank_account_number' => '029801005544501',
                'home_address' => 'Asrama Yonif 201/JY, Gandaria, Pasar Rebo, Jakarta Timur',
                'medical_history' => 'Pernah dislokasi bahu kanan (2023), telah pulih dengan terapi fisioterapi',
                'psychological_record' => 'Stakes Keswa: I, Bakat Senjata Bantuan Mortir',
            ],

            // Dodiklatpur
            [
                'satdik_id' => $dodiklatpur->id,
                'education_program_id' => $progLatpur->id,
                'classroom_id' => $classLatpur1->id,
                'nosik' => 'DODIKLATPUR.2026.1.001',
                'full_name' => 'Prada Danu Tirta',
                'student_rank' => 'Prajurit Dua Siswa',
                'origin_military_unit' => 'Yonif 202/Tajimalela',
                'birth_place' => 'Semarang',
                'birth_date' => '2003-03-25',
                'gender' => 'L',
                'religion' => 'Islam',
                'blood_type' => 'O',
                'education_level' => 'Dikmata TNI AD 2024',
                'status' => 'Aktif',
                'nik' => '3374012503030005',
                'family_card_number' => '3374010808980004',
                'mother_name' => 'Sri Wahyuni',
                'father_name' => 'Joko Susilo',
                'emergency_contact_phone' => '081233445566',
                'bpjs_number' => '0005123456789',
                'bank_name' => 'BRI Yonif 202',
                'bank_account_number' => '041201009988506',
                'home_address' => 'Asrama Yonif 202/TM, Rawalumbu, Kota Bekasi',
                'medical_history' => 'Bebas alergi, ketahanan paru dan daya tahan kardiovaskular tinggi',
                'psychological_record' => 'Stakes Keswa: I, Sangat Tenang di Bawah Tekanan (Calon Penembak Runduk)',
            ],

            // Bela Negara (Komcad)
            [
                'satdik_id' => $belanegara->id,
                'education_program_id' => $progBelaNegara->id,
                'classroom_id' => $classBelaNegara1->id,
                'nosik' => 'BELANEGARA.2026.1.001',
                'full_name' => 'Fajar Nugraha, S.T.',
                'student_rank' => 'Serdik Komcad',
                'origin_military_unit' => 'Korem 051/Wijayakarta (Pekerja BUMN)',
                'birth_place' => 'Surabaya',
                'birth_date' => '1999-07-12',
                'gender' => 'L',
                'religion' => 'Islam',
                'blood_type' => 'B',
                'education_level' => 'S1 Teknik Elektro ITS',
                'status' => 'Aktif',
                'nik' => '3578011207990001',
                'family_card_number' => '3578012001920005',
                'mother_name' => 'Dewi Lestari',
                'father_name' => 'Gunawan Wibisono',
                'emergency_contact_phone' => '081122334455',
                'bpjs_number' => '0006789012345',
                'bank_name' => 'Mandiri BUMN / Payroll',
                'bank_account_number' => '1420019887766',
                'home_address' => 'Apartemen Signature Park Grande, Cawang, Jakarta Timur',
                'medical_history' => 'Riwayat kacamata silinder 0.5D, fisik sehat dan fit',
                'psychological_record' => 'Stakes Keswa: I, Bakat Komunikasi & Perang Siber Komcad',
            ],
        ];

        foreach ($sampleStudents as $data) {
            $student = Student::create([
                'satdik_id' => $data['satdik_id'],
                'education_program_id' => $data['education_program_id'],
                'classroom_id' => $data['classroom_id'],
                'nosik' => $data['nosik'],
                'full_name' => $data['full_name'],
                'student_rank' => $data['student_rank'],
                'origin_military_unit' => $data['origin_military_unit'],
                'birth_place' => $data['birth_place'],
                'birth_date' => $data['birth_date'],
                'gender' => $data['gender'],
                'religion' => $data['religion'],
                'blood_type' => $data['blood_type'],
                'education_level' => $data['education_level'],
                'status' => $data['status'],
            ]);

            // Data Pribadi Sensitif disimpan terenkripsi
            StudentPersonalProfile::create([
                'student_id' => $student->id,
                'nik' => $data['nik'],
                'family_card_number' => $data['family_card_number'],
                'mother_name' => $data['mother_name'],
                'father_name' => $data['father_name'],
                'emergency_contact_name' => $data['mother_name'],
                'emergency_contact_phone' => $data['emergency_contact_phone'],
                'bpjs_number' => $data['bpjs_number'],
                'bank_name' => $data['bank_name'],
                'bank_account_number' => $data['bank_account_number'],
                'home_address' => $data['home_address'],
                'medical_history' => $data['medical_history'],
                'psychological_record' => $data['psychological_record'],
            ]);
        }

        // 5. Buat Catatan Kesehatan Awal Siswa (Termasuk Rawat Inap & Rujuk Rumkit)
        $this->call(StudentHealthRecordSeeder::class);

        // 6. Buat 1000 Siswa Dummy
        $this->call(StudentSeeder::class);
    }
}
