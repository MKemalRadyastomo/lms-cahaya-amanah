<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Mapel;
use App\Models\Pengampu;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;

class AkademikSeeder extends Seeder
{
    public function run(): void
    {
        // --- Tahun Ajaran aktif ---
        $ta = TahunAjaran::create([
            'tahun' => '2024/2025',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        // --- Mata Pelajaran ---
        $mapelData = [
            ['MTK', 'Matematika', 'X', 70],
            ['ARB', 'Bahasa Arab', 'X', 70],
            ['FQH', 'Fiqih', 'X', 75],
            ['QHD', "Al-Qur'an Hadits", 'X', 75],
            ['ING', 'Bahasa Inggris', 'X', 70],
            ['INF', 'Informatika', 'X', 70],
        ];
        $mapels = [];
        foreach ($mapelData as [$kode, $nama, $jenjang, $kkm]) {
            $mapels[$kode] = Mapel::create(compact('kode', 'nama', 'jenjang', 'kkm'));
        }

        // --- Kelas ---
        $guru = User::where('role', User::ROLE_GURU)->get();
        $siswa = User::where('role', User::ROLE_SISWA)->get();

        $kelasA = Kelas::create([
            'nama' => 'X IPA 1',
            'tingkat' => 'X',
            'jurusan' => 'IPA',
            'tahun_ajaran_id' => $ta->id,
            'walikelas_id' => $guru->firstWhere('email', 'fauzi@lms.test')->id,
        ]);
        $kelasB = Kelas::create([
            'nama' => 'X IPS 1',
            'tingkat' => 'X',
            'jurusan' => 'IPS',
            'tahun_ajaran_id' => $ta->id,
            'walikelas_id' => $guru->firstWhere('email', 'aminah@lms.test')->id,
        ]);

        // --- Distribusi siswa ke 2 kelas ---
        foreach ($siswa as $i => $s) {
            KelasSiswa::create([
                'kelas_id' => ($i % 2 === 0) ? $kelasA->id : $kelasB->id,
                'siswa_id' => $s->id,
                'tahun_ajaran_id' => $ta->id,
            ]);
        }

        // --- Pengampu (guru mengajar mapel di kelas) ---
        $penugasan = [
            'fauzi@lms.test' => ['MTK', 'INF'],
            'aminah@lms.test' => ['ARB'],
            'rahman@lms.test' => ['FQH'],
            'yusuf@lms.test' => ['QHD'],
            'fatimah@lms.test' => ['ING'],
        ];

        foreach ([$kelasA, $kelasB] as $kelas) {
            foreach ($penugasan as $email => $kodeMapel) {
                $g = $guru->firstWhere('email', $email);
                foreach ((array) $kodeMapel as $kode) {
                    Pengampu::create([
                        'guru_id' => $g->id,
                        'mapel_id' => $mapels[$kode]->id,
                        'kelas_id' => $kelas->id,
                        'tahun_ajaran_id' => $ta->id,
                    ]);
                }
            }
        }
    }
}
