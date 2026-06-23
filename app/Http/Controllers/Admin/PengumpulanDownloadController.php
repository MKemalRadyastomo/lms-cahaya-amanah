<?php

namespace App\Http\Controllers\Admin;

use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PengumpulanDownloadController
{
    public function __invoke(Request $request, PengumpulanTugas $pengumpulan): StreamedResponse
    {
        $user = $request->user();

        // Guru hanya boleh mengunduh pengumpulan dari tugas miliknya
        if ($user->isGuru() && $pengumpulan->tugas->guru_id !== $user->id) {
            abort(403);
        }

        if (! $pengumpulan->file_path) {
            abort(404, 'Tidak ada berkas.');
        }

        $disk = Storage::disk(config('filesystems.default'));

        if (! $disk->exists($pengumpulan->file_path)) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        return $disk->download($pengumpulan->file_path);
    }
}
