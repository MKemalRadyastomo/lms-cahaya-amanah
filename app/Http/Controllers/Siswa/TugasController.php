<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Requests\Siswa\StorePengumpulanRequest;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TugasController extends BaseSiswaController
{
    public function index(Request $request): View
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId) {
            return $this->noKelasView('Tugas');
        }

        $user = $request->user();
        $filter = $request->get('filter', 'semua');

        $tugas = Tugas::with(['mapel', 'guru'])
            ->where('kelas_id', $kelasId)
            ->where('is_published', true)
            ->when($filter === 'aktif', fn ($q) => $q->where('deadline', '>=', now()))
            ->when($filter === 'selesai', fn ($q) => $q->whereHas('pengumpulanTugas', fn ($pq) => $pq->where('siswa_id', $user->id)))
            ->orderByRaw('CASE WHEN deadline >= NOW() THEN 0 ELSE 1 END')
            ->orderBy('deadline')
            ->paginate(10);

        $tugas->getCollection()->transform(function (Tugas $tugas) use ($user) {
            $tugas->pengumpulan = $tugas->pengumpulanTugas()
                ->where('siswa_id', $user->id)
                ->first();
            $tugas->status_kirim = $this->hitungStatus($tugas, $tugas->pengumpulan);

            return $tugas;
        });

        return view('siswa.tugas.index', compact('tugas', 'filter'));
    }

    public function show(Tugas $tugas): View
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId || $tugas->kelas_id !== $kelasId || ! $tugas->is_published) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $user = request()->user();
        $tugas->load(['mapel', 'guru']);

        $pengumpulan = PengumpulanTugas::firstOrNew(
            ['tugas_id' => $tugas->id, 'siswa_id' => $user->id]
        );

        $statusKirim = $this->hitungStatus($tugas, $pengumpulan);
        $bisaKirim = $this->bisaKirim($tugas, $pengumpulan);

        return view('siswa.tugas.show', compact('tugas', 'pengumpulan', 'statusKirim', 'bisaKirim'));
    }

    public function submit(StorePengumpulanRequest $request, Tugas $tugas): RedirectResponse
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId || $tugas->kelas_id !== $kelasId || ! $tugas->is_published) {
            abort(403);
        }

        $user = $request->user();

        if (! $this->bisaKirim($tugas, PengumpulanTugas::where('tugas_id', $tugas->id)->where('siswa_id', $user->id)->first())) {
            return back()->with('error', 'Tugas sudah tidak dapat dikumpulkan.');
        }

        $validated = $request->validated();

        if (! $request->hasFile('file') && empty($validated['catatan'])) {
            return back()->withErrors(['file' => 'Harap unggah berkas atau isi catatan sebelum mengumpulkan.'])->withInput();
        }

        $disk = Storage::disk(config('filesystems.default'));

        $existing = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('siswa_id', $user->id)
            ->first();

        // Hapus berkas lama jika ada (re-submit)
        if ($existing && $existing->file_path && $request->hasFile('file') && $disk->exists($existing->file_path)) {
            $disk->delete($existing->file_path);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store("pengumpulan/{$tugas->id}/{$user->id}", config('filesystems.default'));
        } elseif ($existing) {
            $filePath = $existing->file_path;
        }

        $terlambat = now()->isAfter($tugas->deadline);
        $status = $terlambat ? 'terlambat' : 'terkirim';

        PengumpulanTugas::updateOrCreate(
            ['tugas_id' => $tugas->id, 'siswa_id' => $user->id],
            [
                'file_path' => $filePath,
                'catatan' => $validated['catatan'] ?? null,
                'submitted_at' => now(),
                'status' => $status,
            ]
        );

        $pesan = $terlambat
            ? 'Tugas berhasil dikumpulkan, namun tercatat TERLAMBAT.'
            : 'Tugas berhasil dikumpulkan.';

        return redirect()->route('siswa.tugas.show', $tugas)->with('success', $pesan);
    }

    public function downloadLampiran(Tugas $tugas): StreamedResponse
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId || $tugas->kelas_id !== $kelasId || ! $tugas->is_published || ! $tugas->file_path) {
            abort(403);
        }

        return $this->download($tugas->file_path);
    }

    public function downloadJawaban(Tugas $tugas): StreamedResponse
    {
        $kelasId = $this->activeKelasId();
        $user = request()->user();

        if (! $kelasId || $tugas->kelas_id !== $kelasId) {
            abort(403);
        }

        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('siswa_id', $user->id)
            ->firstOrFail();

        if (! $pengumpulan->file_path) {
            abort(404, 'Tidak ada berkas yang diunggah.');
        }

        return $this->download($pengumpulan->file_path);
    }

    protected function download(string $path): StreamedResponse
    {
        $disk = Storage::disk(config('filesystems.default'));

        if (! $disk->exists($path)) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        return $disk->download($path);
    }

    protected function hitungStatus(Tugas $tugas, ?PengumpulanTugas $pengumpulan): string
    {
        if (! $pengumpulan || ! $pengumpulan->exists) {
            return now()->isAfter($tugas->deadline) ? 'terlewat' : 'belum';
        }

        return match ($pengumpulan->status) {
            'dinilai' => 'dinilai',
            'terlambat' => 'terlambat',
            'terkirim' => 'terkirim',
            default => now()->isAfter($tugas->deadline) ? 'terlewat' : 'belum',
        };
    }

    protected function bisaKirim(Tugas $tugas, ?PengumpulanTugas $pengumpulan): bool
    {
        // Sudah dinilai -> tidak bisa kirim ulang
        if ($pengumpulan && $pengumpulan->status === 'dinilai') {
            return false;
        }

        return true;
    }
}
