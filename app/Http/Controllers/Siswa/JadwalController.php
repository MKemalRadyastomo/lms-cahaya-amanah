<?php

namespace App\Http\Controllers\Siswa;

use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalController extends BaseSiswaController
{
    public function index(Request $request): View
    {
        $kelasSiswa = $this->activeKelasSiswa();

        if (! $kelasSiswa) {
            return $this->noKelasView('Jadwal');
        }

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
        $hariAktif = $request->get('hari', now()->locale('id')->isoFormat('dddd'));

        if (! in_array($hariAktif, $hariList, true)) {
            $hariAktif = 'Senin';
        }

        $jadwal = $kelasSiswa->kelas->jadwals()
            ->where('hari', $hariAktif)
            ->with(['mapel', 'guru'])
            ->orderBy('jam_mulai')
            ->get();

        // Ringkasan per hari (jumlah jam pelajaran)
        $ringkasan = $kelasSiswa->kelas->jadwals()
            ->selectRaw('hari, COUNT(*) as jumlah')
            ->groupBy('hari')
            ->pluck('jumlah', 'hari');

        return view('siswa.jadwal.index', compact('jadwal', 'hariList', 'hariAktif', 'ringkasan', 'kelasSiswa'));
    }
}
