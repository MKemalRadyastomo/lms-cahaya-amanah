<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Pengampu;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $ta = TahunAjaran::where('is_active', true)->first();

        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
        $slot = [
            ['07:00:00', '07:45:00'],
            ['07:45:00', '08:30:00'],
            ['08:30:00', '09:15:00'],
            ['09:30:00', '10:15:00'],
            ['10:15:00', '11:00:00'],
            ['11:00:00', '11:45:00'],
        ];

        $pengampus = Pengampu::with(['guru', 'mapel', 'kelas'])
            ->where('tahun_ajaran_id', $ta->id)
            ->get()
            ->groupBy('kelas_id');

        foreach ($pengampus as $kelasId => $items) {
            $i = 0;
            foreach ($items as $pengampu) {
                // tiap mapel 2x per minggu
                for ($x = 0; $x < 2; $x++) {
                    $h = $hari[(int) floor($i / 2) % count($hari)];
                    [$mulai, $selesai] = $slot[$i % count($slot)];

                    Jadwal::create([
                        'kelas_id' => $kelasId,
                        'mapel_id' => $pengampu->mapel_id,
                        'guru_id' => $pengampu->guru_id,
                        'hari' => $h,
                        'jam_mulai' => $mulai,
                        'jam_selesai' => $selesai,
                        'ruang' => 'Ruang '.($kelasId === 1 ? 'A-101' : 'B-201'),
                        'tahun_ajaran_id' => $ta->id,
                    ]);
                    $i++;
                }
            }
        }
    }
}
