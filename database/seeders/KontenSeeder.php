<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Materi;
use App\Models\PengumpulanTugas;
use App\Models\Soal;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KontenSeeder extends Seeder
{
    public function run(): void
    {
        $kelasA = Kelas::where('nama', 'X IPA 1')->first();
        $guruFauzi = User::where('email', 'fauzi@lms.test')->first();
        $mapelMtk = Mapel::where('kode', 'MTK')->first();
        $siswaA = $kelasA->siswa()->with('siswa')->get()->pluck('siswa');

        // --- Materi pembelajaran ---
        $materi = [
            ['Sistem Persamaan Linear', 'Pembahasan SPLDV dengan metode substitusi dan eliminasi.', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['Pengantar Logika Pemrograman', 'Konsep algoritma, flowchart, dan pseudocode dasar.', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['Fungsi Kuadrat', 'Grafik dan titik puncak fungsi kuadrat ax²+bx+c.', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ];
        foreach ($materi as [$judul, $konten, $video]) {
            Materi::create([
                'mapel_id' => $mapelMtk->id,
                'kelas_id' => $kelasA->id,
                'guru_id' => $guruFauzi->id,
                'judul' => $judul,
                'slug' => Str::slug($judul.'-'.Str::random(4)),
                'konten' => $konten,
                'video_url' => $video,
                'is_published' => true,
            ]);
        }

        // --- Tugas ---
        $tugas = Tugas::create([
            'mapel_id' => $mapelMtk->id,
            'kelas_id' => $kelasA->id,
            'guru_id' => $guruFauzi->id,
            'judul' => 'Latihan SPLDV',
            'deskripsi' => 'Kerjakan soal nomor 1-10 pada modul halaman 23. Kumpulkan jawaban dalam bentuk PDF.',
            'deadline' => now()->addDays(7),
            'poin_max' => 100,
            'is_published' => true,
        ]);

        // Beberapa siswa sudah mengumpulkan
        foreach ($siswaA->take(5) as $i => $siswa) {
            PengumpulanTugas::create([
                'tugas_id' => $tugas->id,
                'siswa_id' => $siswa->id,
                'file_path' => "tugas/{$tugas->id}/siswa-{$siswa->id}.pdf",
                'status' => $i < 2 ? 'dinilai' : 'terkirim',
                'nilai' => $i < 2 ? (string) (75 + $i * 10) : null,
                'submitted_at' => now()->subDays(1),
                'dinilai_at' => $i < 2 ? now() : null,
            ]);
        }

        // --- Ujian + Soal (pilihan ganda) ---
        $ujian = Ujian::create([
            'mapel_id' => $mapelMtk->id,
            'kelas_id' => $kelasA->id,
            'guru_id' => $guruFauzi->id,
            'judul' => 'Ulangan Harian 1 - Sistem Persamaan Linear',
            'deskripsi' => 'Kerjakan dengan jujur. Ujian otomatis tersimpan saat waktu habis.',
            'jenis' => 'ujian',
            'durasi_menit' => 45,
            'waktu_mulai' => now()->addDays(3),
            'waktu_selesai' => now()->addDays(3)->addHours(2),
            'acak_soal' => true,
            'acak_opsi' => true,
            'tampilkan_hasil' => false,
            'is_published' => false,
        ]);

        $soal = [
            ['Penyelesaian SPLDV x + y = 5 dan x - y = 1 adalah...', ['x=3, y=2', 'x=2, y=3', 'x=4, y=1', 'x=1, y=4'], 'x=3, y=2'],
            ['Gradien garis y = 2x + 3 adalah...', ['1', '2', '3', '-2'], '2'],
            ['Akar-akar persamaan x² - 5x + 6 = 0 adalah...', ['1 dan 6', '2 dan 3', '-2 dan -3', '1 dan 5'], '2 dan 3'],
            ['Hasil dari 3x + 2 = 17 adalah x = ...', ['3', '4', '5', '6'], '5'],
            ['Bentuk sederhana dari 2(x+3) adalah...', ['2x+3', '2x+6', 'x+6', '2x+5'], '2x+6'],
        ];

        foreach ($soal as [$pertanyaan, $opsi, $jawaban]) {
            Soal::create([
                'ujian_id' => $ujian->id,
                'tipe' => 'pg',
                'pertanyaan' => $pertanyaan,
                'opsi' => $opsi,
                'jawaban_benar' => $jawaban,
                'poin' => 20,
            ]);
        }
    }
}
