<?php

namespace App\Http\Controllers\Siswa;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengumumanController extends BaseSiswaController
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $kelasId = $this->activeKelasId();

        if (! $kelasId) {
            return $this->noKelasView('Pengumuman');
        }

        $pengumuman = Pengumuman::with('creator')
            ->visibleToSiswa($user, $kelasId)
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(10);

        return view('siswa.pengumuman.index', compact('pengumuman'));
    }

    public function show(Request $request, Pengumuman $pengumuman): View
    {
        $user = $request->user();
        $kelasId = $this->activeKelasId();

        if (! $kelasId) {
            abort(403);
        }

        // Validasi akses berdasarkan target
        $visible = Pengumuman::visibleToSiswa($user, $kelasId)->where('id', $pengumuman->id)->exists();
        if (! $visible) {
            abort(403, 'Pengumuman ini tidak ditujukan untuk Anda.');
        }

        $pengumuman->load('creator');

        return view('siswa.pengumuman.show', compact('pengumuman'));
    }
}
