<?php

namespace App\Http\Controllers\Siswa;

use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NilaiController extends BaseSiswaController
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $kelasSiswa = $this->activeKelasSiswa();

        if (! $kelasSiswa) {
            return $this->noKelasView('Nilai');
        }

        $nilais = Nilai::with(['mapel', 'tahunAjaran'])
            ->where('siswa_id', $user->id)
            ->where('kelas_id', $kelasSiswa->kelas_id)
            ->orderBy('mapel_id')
            ->get();

        $rataTugas = $nilais->whereNotNull('nilai_tugas')->avg('nilai_tugas');
        $rataUjian = $nilais->whereNotNull('nilai_ujian')->avg('nilai_ujian');
        $rataAkhir = $nilais->whereNotNull('nilai_akhir')->avg('nilai_akhir');

        return view('siswa.nilai.index', [
            'nilais' => $nilais,
            'kelasSiswa' => $kelasSiswa,
            'rataTugas' => $rataTugas ? round($rataTugas, 2) : null,
            'rataUjian' => $rataUjian ? round($rataUjian, 2) : null,
            'rataAkhir' => $rataAkhir ? round($rataAkhir, 2) : null,
        ]);
    }
}
