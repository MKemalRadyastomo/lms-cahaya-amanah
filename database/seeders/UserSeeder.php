<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // --- Admin ---
        User::create([
            'name' => 'Administrator Sekolah',
            'email' => 'admin@lms.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'nip' => '198501012010011001',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567890',
            'status' => 'aktif',
            'email_verified_at' => now(),
        ]);

        // --- Guru ---
        $guru = [
            ['Ust. Ahmad Fauzi, S.Pd', 'fauzi@lms.test', 'L', 'Matematika & Informatika'],
            ['Usth. Siti Aminah, S.Pd', 'aminah@lms.test', 'P', 'Bahasa Arab'],
            ['Ust. Abdul Rahman, S.Ag', 'rahman@lms.test', 'L', 'Fiqih & SKI'],
            ['Usth. Fatimah Zahra, S.Pd', 'fatimah@lms.test', 'P', 'B. Inggris'],
            ['Ust. Yusuf Mansur, S.Pd.I', 'yusuf@lms.test', 'L', "Al-Qur'an Hadits"],
        ];

        foreach ($guru as $i => [$name, $email, $jk, $bidang]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => 'guru123',
                'role' => User::ROLE_GURU,
                'nip' => '198'.str_pad((string) (60 + $i), 2, '0', STR_PAD_LEFT).'01012015010'.($i + 1),
                'jenis_kelamin' => $jk,
                'no_hp' => '0812'.str_pad((string) (100000 + $i), 6, '0', STR_PAD_LEFT),
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]);
        }

        // --- Siswa (20 siswa demo) ---
        $namaDepan = ['Ahmad', 'Muhammad', 'Aisyah', 'Fatimah', 'Ali', 'Hasan', 'Husein', 'Khadijah', 'Umar', 'Zainab', 'Bilal', 'Ramlah', 'Hamzah', 'Maryam', 'Salman', 'Hafsah', 'Khalid', 'Sumayyah', 'Anas', 'Ruqayyah'];
        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'name' => $namaDepan[$i - 1].' '.Str::random(5),
                'email' => 'siswa'.$i.'@lms.test',
                'password' => 'student123',
                'role' => User::ROLE_SISWA,
                'nis' => '2024'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'nisn' => str_pad((string) (3000000000 + $i), 10, '0', STR_PAD_LEFT),
                'jenis_kelamin' => ($i % 2 === 0) ? 'P' : 'L',
                'tempat_lahir' => 'Bekasi',
                'tanggal_lahir' => now()->subYears(16)->subDays($i * 13),
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]);
        }
    }
}
