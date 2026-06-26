<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengumumanNotifikasiSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $kelasA = Kelas::where('nama', 'X IPA 1')->first();
        $kelasB = Kelas::where('nama', 'X IPS 1')->first();

        // --- Pengumuman ---
        $pengumuman = [
            ['Libur Hari Raya', 'Diberitahukan kepada seluruh siswa bahwa sekolah diliburkan dalam rangka Hari Raya. Mohon tetap mengerjakan tugas yang diberikan guru.', 'semua', null, true],
            ['Pengumpulan Tugas Matematika', 'Bagi siswa kelas X IPA 1, tugas SPLDV dikumpulkan paling lambat minggu ini.', 'kelas', $kelasA?->id, false],
            ['Rapat Guru', 'Seluruh guru diharapkan hadir pada rapat evaluasi akademik Jumat sore.', 'guru', null, false],
            ['Hasil Ulangan', 'Hasil ulangan harian sudah dipublikasikan. Silakan cek pada menu nilai.', 'semua', null, false],
            ['Jadwal Ujian Tengah Semester', 'Jadwal UTS akan diumumkan minggu depan. Persiapkan diri sebaik mungkin.', 'semua', null, true],
        ];

        foreach ($pengumuman as [$judul, $konten, $target, $targetId, $pinned]) {
            Pengumuman::create([
                'judul' => $judul,
                'konten' => $konten,
                'target_type' => $target,
                'target_id' => $targetId,
                'created_by' => $admin->id,
                'is_pinned' => $pinned,
            ]);
        }

        // --- Notifikasi per user ---
        $siswa = User::where('role', User::ROLE_SISWA)->get();
        foreach ($siswa as $i => $s) {
            Notifikasi::create([
                'user_id' => $s->id,
                'judul' => 'Tugas baru ditambahkan',
                'pesan' => 'Guru telah menambahkan tugas baru pada salah satu mata pelajaran Anda.',
                'tipe' => 'tugas',
                'url' => '/siswa/tugas',
                'dibaca' => $i % 3 === 0,
            ]);
            Notifikasi::create([
                'user_id' => $s->id,
                'judul' => 'Nilai telah diperbarui',
                'pesan' => 'Nilai salah satu tugas Anda sudah dinilai oleh guru.',
                'tipe' => 'nilai',
                'url' => '/siswa/nilai',
                'dibaca' => $i % 2 === 0,
            ]);
        }

        foreach (User::where('role', User::ROLE_GURU)->get() as $g) {
            Notifikasi::create([
                'user_id' => $g->id,
                'judul' => 'Pengumpulan tugas baru',
                'pesan' => 'Beberapa siswa telah mengumpulkan tugas yang Anda berikan.',
                'tipe' => 'tugas',
                'url' => '/admin',
                'dibaca' => false,
            ]);
        }

        Notifikasi::create([
            'user_id' => $admin->id,
            'judul' => 'Sistem diperbarui',
            'pesan' => 'Data dummy berhasil dimuat untuk keperluan pengujian.',
            'tipe' => 'pengumuman',
            'url' => '/admin',
            'dibaca' => true,
        ]);
    }
}
