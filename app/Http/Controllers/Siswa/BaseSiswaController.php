<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\KelasSiswa;
use Illuminate\Contracts\View\View;

abstract class BaseSiswaController extends Controller
{
    /**
     * Ambil pendaftaran kelas siswa pada tahun ajaran aktif.
     */
    protected function activeKelasSiswa(): ?KelasSiswa
    {
        return request()->user()->getActiveKelasSiswa();
    }

    /**
     * Ambil id kelas aktif siswa atau batalkan dengan pesan.
     */
    protected function activeKelasId(): ?int
    {
        return $this->activeKelasSiswa()?->kelas_id;
    }

    /**
     * Tampilkan halaman "belum terdaftar di kelas" untuk fitur tertentu.
     */
    protected function noKelasView(string $feature): View
    {
        return view('siswa.no-kelas', ['feature' => $feature]);
    }
}
