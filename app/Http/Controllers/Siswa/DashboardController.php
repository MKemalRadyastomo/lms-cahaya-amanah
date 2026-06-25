<?php

namespace App\Http\Controllers\Siswa;

use App\Models\Materi;
use App\Models\Pengumuman;
use App\Models\Tugas;
use App\Models\Ujian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends BaseSiswaController
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $kelasSiswa = $this->activeKelasSiswa();

        if (! $kelasSiswa) {
            return view('siswa.dashboard-no-kelas');
        }

        $kelasId = $kelasSiswa->kelas_id;
        $hariIni = Carbon::now(config('app.timezone'))->locale('id')->isoFormat('dddd');

        // Statistik
        $jumlahMateri = Materi::where('kelas_id', $kelasId)
            ->where('is_published', true)->count();
        $tugasAktif = Tugas::where('kelas_id', $kelasId)
            ->where('is_published', true)
            ->where('deadline', '>=', now())
            ->count();
        $tugasTerlewat = Tugas::where('kelas_id', $kelasId)
            ->where('is_published', true)
            ->where('deadline', '<', now())
            ->count();

        // Ujian aktif (terbuka & belum/tengah dikerjakan) untuk siswa ini
        $ujianAktif = Ujian::where('kelas_id', $kelasId)
            ->where('is_published', true)
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_selesai', '>=', now())
            ->whereDoesntHave('ujianHasils', fn ($q) => $q->where('siswa_id', $user->id)->where('status', 'selesai'))
            ->count();

        // Tugas dengan deadline terdekat (belum selesai)
        $tugasTerdekat = Tugas::with(['mapel', 'guru'])
            ->where('kelas_id', $kelasId)
            ->where('is_published', true)
            ->where('deadline', '>=', now())
            ->orderBy('deadline')
            ->limit(5)
            ->get()
            ->map(function (Tugas $tugas) use ($user) {
                $tugas->pengumpulan = $tugas->pengumpulanTugas()
                    ->where('siswa_id', $user->id)
                    ->first();

                return $tugas;
            });

        // Jadwal hari ini
        $jadwalHariIni = $kelasSiswa->kelas->jadwals()
            ->where('hari', $hariIni)
            ->with(['mapel', 'guru'])
            ->orderBy('jam_mulai')
            ->get();

        // Pengumuman terbaru yang relevan
        $pengumuman = Pengumuman::visibleToSiswa($user, $kelasId)
            ->latest()
            ->limit(5)
            ->get();

        return view('siswa.dashboard', [
            'user' => $user,
            'kelasSiswa' => $kelasSiswa,
            'jumlahMateri' => $jumlahMateri,
            'tugasAktif' => $tugasAktif,
            'tugasTerlewat' => $tugasTerlewat,
            'ujianAktif' => $ujianAktif,
            'tugasTerdekat' => $tugasTerdekat,
            'jadwalHariIni' => $jadwalHariIni,
            'pengumuman' => $pengumuman,
            'hariIni' => $hariIni,
        ]);
    }
}
