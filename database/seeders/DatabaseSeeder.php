<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Guru (Pengelola Studio - Pak Fajar)
        User::updateOrCreate(
            ['email' => 'guru@studio.sch.id'],
            [
                'name' => 'Pak Fajar, S.Pd',
                'nis_nip' => '198506122010011005',
                'role' => 'guru',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Akun Siswa (Anggota Kelompok LKPD)
        $students = [
            [
                'name' => 'Alwi Husein',
                'email' => 'alwi@siswa.sch.id',
                'nis_nip' => '222310001',
                'phone' => '082111110001',
            ],
            [
                'name' => 'Anugrah',
                'email' => 'anugrah@siswa.sch.id',
                'nis_nip' => '222310002',
                'phone' => '082111110002',
            ],
            [
                'name' => 'Jahan',
                'email' => 'jahan@siswa.sch.id',
                'nis_nip' => '222310003',
                'phone' => '082111110003',
            ],
            [
                'name' => 'Belva',
                'email' => 'belva@siswa.sch.id',
                'nis_nip' => '222310004',
                'phone' => '082111110004',
            ],
            [
                'name' => 'Chiko',
                'email' => 'chiko@siswa.sch.id',
                'nis_nip' => '222310005',
                'phone' => '082111110005',
            ],
        ];

        foreach ($students as $stu) {
            User::updateOrCreate(
                ['email' => $stu['email']],
                [
                    'name' => $stu['name'],
                    'nis_nip' => $stu['nis_nip'],
                    'role' => 'siswa',
                    'phone' => $stu['phone'],
                    'password' => Hash::make('password'),
                ]
            );
        }

        $this->call(ItemSeeder::class);
    }
}
