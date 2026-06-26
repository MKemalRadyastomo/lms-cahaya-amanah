<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class NilaiSeeder extends Seeder
{
    public function run(): void
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $semester = $ta->semester;

        foreach (Kelas::all() as $kelas) {
            $siswaList = $kelas->siswa()->with('siswa')->get()->pluck('siswa');
            $mapels = $kelas->pengampus()->with('mapel')->get()->pluck('mapel')->unique('id');

            foreach ($siswaList as $i => $siswa) {
                foreach ($mapels as $mapel) {
                    $nilaiTugas = 70 + (($i + $mapel->id) % 4) * 7;     // 70-91
                    $nilaiUjian = 65 + (($i + $mapel->id * 2) % 5) * 7; // 65-93
                    $nilaiAkhir = round(($nilaiTugas * 0.4) + ($nilaiUjian * 0.6), 2);
                    $predikat = $this->predikat($nilaiAkhir);

                    Nilai::create([
                        'siswa_id' => $siswa->id,
                        'mapel_id' => $mapel->id,
                        'kelas_id' => $kelas->id,
                        'tahun_ajaran_id' => $ta->id,
                        'semester' => $semester,
                        'nilai_tugas' => $nilaiTugas,
                        'nilai_ujian' => $nilaiUjian,
                        'nilai_akhir' => $nilaiAkhir,
                        'predikat' => $predikat,
                        'deskripsi' => $this->deskripsi($predikat),
                    ]);
                }
            }
        }
    }

    private function predikat(float $nilai): string
    {
        return match (true) {
            $nilai >= 90 => 'A',
            $nilai >= 80 => 'B',
            $nilai >= 70 => 'C',
            $nilai >= 60 => 'D',
            default => 'E',
        };
    }

    private function deskripsi(string $predikat): string
    {
        return [
            'A' => 'Sangat baik, penguasaan materi sangat memuaskan.',
            'B' => 'Baik, terus tingkatkan konsistensi belajar.',
            'C' => 'Cukup, perlu lebih rajin berlatih.',
            'D' => 'Kurang, segera perbaiki dengan bimbingan.',
            'E' => 'Sangat kurang, wajib mengikuti remedial.',
        ][$predikat];
    }
}
