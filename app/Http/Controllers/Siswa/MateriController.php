<?php

namespace App\Http\Controllers\Siswa;

use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MateriController extends BaseSiswaController
{
    public function index(Request $request)
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId) {
            return $this->noKelasView('Materi');
        }

        $mapelId = $request->get('mapel');

        $materi = Materi::with(['mapel', 'guru'])
            ->where('kelas_id', $kelasId)
            ->where('is_published', true)
            ->when($mapelId, fn ($q) => $q->where('mapel_id', $mapelId))
            ->latest()
            ->paginate(12);

        $mapelList = Materi::where('kelas_id', $kelasId)
            ->where('is_published', true)
            ->with('mapel')
            ->get()
            ->pluck('mapel')
            ->unique('id')
            ->values();

        return view('siswa.materi.index', compact('materi', 'mapelList', 'mapelId'));
    }

    public function show(Materi $materi)
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId || $materi->kelas_id !== $kelasId || ! $materi->is_published) {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }

        $materi->load(['mapel', 'guru']);

        // Materi terkait (mapel yang sama)
        $terkait = Materi::where('mapel_id', $materi->mapel_id)
            ->where('kelas_id', $kelasId)
            ->where('is_published', true)
            ->where('id', '!=', $materi->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('siswa.materi.show', compact('materi', 'terkait'));
    }

    public function downloadLampiran(Materi $materi)
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId || $materi->kelas_id !== $kelasId || ! $materi->is_published || ! $materi->file_path) {
            abort(403);
        }

        return $this->downloadFile($materi->file_path);
    }

    protected function downloadFile(string $path): StreamedResponse
    {
        $disk = Storage::disk(config('filesystems.default'));

        if (! $disk->exists($path)) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        return $disk->download($path);
    }
}
