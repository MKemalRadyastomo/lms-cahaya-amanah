<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AkademikSeeder::class,
            JadwalSeeder::class,
            KontenSeeder::class,
            NilaiSeeder::class,
            PengumumanNotifikasiSeeder::class,
        ]);
    }
}
