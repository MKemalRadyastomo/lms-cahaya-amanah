<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Requests\Siswa\MulaiUjianRequest;
use App\Http\Requests\Siswa\SubmitUjianRequest;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\UjianHasil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UjianController extends BaseSiswaController
{
    /**
     * Daftar ujian yang tersedia untuk kelas aktif siswa.
     */
    public function index(Request $request): View
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId) {
            return $this->noKelasView('Ujian');
        }

        $user = $request->user();
        $filter = $request->get('filter', 'semua');

        $ujians = Ujian::with(['mapel', 'guru', 'soals'])
            ->where('kelas_id', $kelasId)
            ->where('is_published', true)
            ->when($filter === 'aktif', fn ($q) => $q->where('waktu_mulai', '<=', now())->where('waktu_selesai', '>=', now()))
            ->when($filter === 'selesai', fn ($q) => $q->whereHas('ujianHasils', fn ($hq) => $hq->where('siswa_id', $user->id)->where('status', 'selesai')))
            ->orderByRaw('CASE WHEN waktu_selesai >= ? THEN 0 ELSE 1 END', [now()])
            ->orderBy('waktu_selesai')
            ->paginate(10);

        $ujians->getCollection()->transform(function (Ujian $ujian) use ($user) {
            $ujian->hasil = $ujian->ujianHasils()->where('siswa_id', $user->id)->first();
            $ujian->jumlah_soal = $ujian->soals->count();
            $ujian->status_ujian = $this->statusUjian($ujian, $ujian->hasil);

            return $ujian;
        });

        return view('siswa.ujian.index', compact('ujians', 'filter'));
    }

    /**
     * Halaman detail / instruksi sebelum mengerjakan.
     */
    public function show(Ujian $ujian): View
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId || $ujian->kelas_id !== $kelasId || ! $ujian->is_published) {
            abort(403, 'Anda tidak memiliki akses ke ujian ini.');
        }

        $user = request()->user();
        $ujian->load(['mapel', 'guru']);
        $jumlahSoal = $ujian->soals()->count();
        $jumlahPg = $ujian->soals()->where('tipe', 'pg')->count();
        $jumlahEssay = $ujian->soals()->where('tipe', 'essay')->count();

        $hasil = UjianHasil::firstOrNew(
            ['ujian_id' => $ujian->id, 'siswa_id' => $user->id]
        );

        $status = $this->statusUjian($ujian, $hasil);

        return view('siswa.ujian.show', compact('ujian', 'hasil', 'status', 'jumlahSoal', 'jumlahPg', 'jumlahEssay'));
    }

    /**
     * Mulai ujian: validasi jendela waktu & passcode, lalu buat/buka UjianHasil.
     */
    public function mulai(MulaiUjianRequest $request, Ujian $ujian): RedirectResponse
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId || $ujian->kelas_id !== $kelasId || ! $ujian->is_published) {
            abort(403);
        }

        $user = $request->user();

        // Cek apakah sudah selesai -> tidak bisa mulai lagi
        $hasil = UjianHasil::where('ujian_id', $ujian->id)->where('siswa_id', $user->id)->first();
        if ($hasil && $hasil->status === 'selesai') {
            return redirect()->route('siswa.ujian.hasil', $ujian);
        }

        // Validasi jendela waktu
        if (now()->lt($ujian->waktu_mulai)) {
            return back()->with('error', 'Ujian belum dibuka. Dibuka pada '.$ujian->waktu_mulai->isoFormat('D MMM Y, HH:mm'));
        }
        if (now()->gt($ujian->waktu_selesai)) {
            return back()->with('error', 'Waktu ujian telah berakhir.');
        }

        // Validasi passcode bila ada
        if ($ujian->passcode) {
            $input = trim((string) $request->input('passcode'));
            if (! hash_equals((string) $ujian->passcode, $input)) {
                return back()->withErrors(['passcode' => 'Kode akses ujian salah.'])->withInput();
            }
        }

        // Pastikan ada soal
        if ($ujian->soals()->doesntExist()) {
            return back()->with('error', 'Ujian belum memiliki soal. Hubungi guru pengampu.');
        }

        $hasil = UjianHasil::firstOrCreate(
            ['ujian_id' => $ujian->id, 'siswa_id' => $user->id],
            [
                'status' => 'sedang',
                'waktu_mulai' => now(),
            ]
        );

        // Bila catatan sudah ada tapi belum ada waktu mulai, isi sekarang.
        if (! $hasil->waktu_mulai) {
            $hasil->waktu_mulai = now();
            $hasil->status = 'sedang';
            $hasil->save();
        }

        return redirect()->route('siswa.ujian.kerjakan', $ujian);
    }

    /**
     * Halaman pengerjaan ujian (soal + timer).
     */
    public function kerjakan(Ujian $ujian): View|RedirectResponse
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId || $ujian->kelas_id !== $kelasId || ! $ujian->is_published) {
            abort(403);
        }

        $user = request()->user();
        $hasil = UjianHasil::where('ujian_id', $ujian->id)->where('siswa_id', $user->id)->first();

        if (! $hasil || $hasil->status !== 'sedang') {
            abort(403, 'Anda belum memulai ujian ini.');
        }

        // Auto-submit jika waktu habis
        if ($this->batasWaktu($ujian, $hasil)->isPast()) {
            $this->finalisasi($ujian, $hasil, $hasil->jawaban ?? []);

            return redirect()->route('siswa.ujian.hasil', $ujian);
        }

        $soals = $ujian->soals()->orderBy('id')->get();

        if ($ujian->acak_soal) {
            $soals = $this->urutAcak($soals, $user->id, fn (Soal $s) => $s->id);
        }

        // Acak opsi bila diaktifkan (urutan stabil per siswa)
        $jawaban = $hasil->jawaban ?? [];
        $soals->transform(function (Soal $soal) use ($user, $jawaban, $ujian) {
            if ($soal->tipe === 'pg' && $ujian->acak_opsi) {
                $soal->opsi_diurut = $this->urutAcak(collect($soal->opsi ?? []), $user->id.'_o', fn ($op, $i) => $op.'-'.$i)->values()->all();
            } else {
                $soal->opsi_diurut = $soal->opsi ?? [];
            }
            $soal->jawaban_siswa = $jawaban[$soal->id] ?? null;

            return $soal;
        });

        $batasWaktu = $this->batasWaktu($ujian, $hasil);

        return view('siswa.ujian.kerjakan', [
            'ujian' => $ujian,
            'hasil' => $hasil,
            'soals' => $soals,
            'batasWaktu' => $batasWaktu,
            'jawabanTersimpan' => $jawaban,
            'terjawab' => collect($jawaban)->filter(fn ($v) => ! is_null($v) && $v !== '')->count(),
        ]);
    }

    /**
     * Simpan jawaban otomatis (autosave via AJAX).
     */
    public function simpan(Request $request, Ujian $ujian): JsonResponse
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId || $ujian->kelas_id !== $kelasId || ! $ujian->is_published) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $user = $request->user();
        $hasil = UjianHasil::where('ujian_id', $ujian->id)->where('siswa_id', $user->id)->first();

        if (! $hasil || $hasil->status !== 'sedang') {
            return response()->json(['message' => 'Ujian tidak aktif.'], 403);
        }

        if ($this->batasWaktu($ujian, $hasil)->isPast()) {
            $this->finalisasi($ujian, $hasil, $hasil->jawaban ?? []);

            return response()->json(['message' => 'Waktu habis.', 'selesai' => true], 410);
        }

        $validSoalIds = $ujian->soals()->pluck('id')->all();
        $jawaban = array_filter(
            (array) $request->input('jawaban', []),
            fn ($id) => in_array((int) $id, $validSoalIds, true),
            ARRAY_FILTER_USE_KEY
        );

        $hasil->jawaban = $jawaban;
        $hasil->save();

        $terjawab = collect($jawaban)->filter(fn ($v) => ! is_null($v) && $v !== '')->count();

        return response()->json([
            'ok' => true,
            'terjawab' => $terjawab,
            'total' => count($validSoalIds),
            'tersimpan_pada' => now()->isoFormat('HH:mm:ss'),
        ]);
    }

    /**
     * Submit / kumpulkan ujian + auto-grading soal PG.
     */
    public function submit(SubmitUjianRequest $request, Ujian $ujian): RedirectResponse
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId || $ujian->kelas_id !== $kelasId || ! $ujian->is_published) {
            abort(403);
        }

        $user = $request->user();
        $hasil = UjianHasil::where('ujian_id', $ujian->id)->where('siswa_id', $user->id)->first();

        if (! $hasil || $hasil->status === 'selesai') {
            return redirect()->route('siswa.ujian.hasil', $ujian);
        }

        $validSoalIds = $ujian->soals()->pluck('id')->all();
        $jawaban = array_filter(
            (array) $request->input('jawaban', []),
            fn ($id) => in_array((int) $id, $validSoalIds, true),
            ARRAY_FILTER_USE_KEY
        );

        $this->finalisasi($ujian, $hasil, $jawaban);

        return redirect()->route('siswa.ujian.hasil', $ujian)->with('success', 'Ujian berhasil dikumpulkan.');
    }

    /**
     * Halaman hasil ujian.
     */
    public function hasil(Ujian $ujian): View
    {
        $kelasId = $this->activeKelasId();

        if (! $kelasId || $ujian->kelas_id !== $kelasId || ! $ujian->is_published) {
            abort(403);
        }

        $user = request()->user();
        $ujian->load(['mapel', 'guru']);
        $hasil = UjianHasil::where('ujian_id', $ujian->id)->where('siswa_id', $user->id)->first();

        if (! $hasil || $hasil->status !== 'selesai') {
            abort(403, 'Anda belum menyelesaikan ujian ini.');
        }

        $adaEssay = $ujian->soals()->where('tipe', 'essay')->exists();
        $tampilkan = (bool) $ujian->tampilkan_hasil;

        // Rincian jawaban hanya jika guru mengizinkan tampilan hasil
        $rincian = null;
        if ($tampilkan) {
            $rincian = $this->bangunRincian($ujian, $hasil);
        }

        return view('siswa.ujian.hasil', [
            'ujian' => $ujian,
            'hasil' => $hasil,
            'adaEssay' => $adaEssay,
            'tampilkan' => $tampilkan,
            'rincian' => $rincian,
        ]);
    }

    // =====================================================================
    //  Helper
    // =====================================================================

    /**
     * Hitung batas waktu pengerjaan: min(waktu_mulai + durasi, waktu_selesai ujian).
     */
    protected function batasWaktu(Ujian $ujian, UjianHasil $hasil)
    {
        $mulai = $hasil->waktu_mulai ?? now();

        return $mulai->copy()->addMinutes($ujian->durasi_menit)->min($ujian->waktu_selesai);
    }

    /**
     * Finalisasi: simpan jawaban, set status selesai, lakukan auto-grading PG.
     */
    protected function finalisasi(Ujian $ujian, UjianHasil $hasil, array $jawaban): void
    {
        $soals = $ujian->soals()->get();
        $totalPoin = 0;
        $perolehan = 0;

        foreach ($soals as $soal) {
            $totalPoin += (float) $soal->poin;

            if ($soal->tipe === 'pg') {
                $jawab = trim((string) ($jawaban[$soal->id] ?? ''));
                $benar = trim((string) $soal->jawaban_benar);

                if ($jawab !== '' && $benar !== '' && strcasecmp($jawab, $benar) === 0) {
                    $perolehan += (float) $soal->poin;
                }
            }
        }

        // Skala 0-100 berdasar total poin seluruh soal (essay bernilai 0 sampai dinilai manual)
        $nilai = $totalPoin > 0 ? round(($perolehan / $totalPoin) * 100, 2) : 0;

        $hasil->jawaban = $jawaban;
        $hasil->nilai = $nilai;
        $hasil->status = 'selesai';
        $hasil->waktu_selesai = now();
        $hasil->save();
    }

    /**
     * Bangun rincian jawaban per soal untuk halaman hasil.
     */
    protected function bangunRincian(Ujian $ujian, UjianHasil $hasil)
    {
        $jawaban = $hasil->jawaban ?? [];

        return $ujian->soals()->orderBy('id')->get()->map(function (Soal $soal) use ($jawaban) {
            $jawab = $jawaban[$soal->id] ?? null;
            $benar = $soal->tipe === 'pg' && $jawab !== null && $jawab !== ''
                && strcasecmp(trim($jawab), trim((string) $soal->jawaban_benar)) === 0;

            return (object) [
                'soal' => $soal,
                'jawaban' => $jawab,
                'benar' => $benar,
            ];
        });
    }

    /**
     * Status ujian dari sudut pandang siswa.
     */
    protected function statusUjian(Ujian $ujian, ?UjianHasil $hasil): string
    {
        if ($hasil && $hasil->status === 'selesai') {
            return 'selesai';
        }
        if (now()->lt($ujian->waktu_mulai)) {
            return 'akan_datang';
        }
        if (now()->gt($ujian->waktu_selesai)) {
            return 'berakhir';
        }
        if ($hasil && $hasil->status === 'sedang') {
            return 'berlangsung';
        }

        return 'tersedia';
    }

    /**
     * Urutkan koleksi secara deterministik berdasarkan hash (stabil per siswa).
     *
     * @param  iterable  $items
     * @param  int|string  $seed
     * @param  callable(mixed, int): string  $keyResolver
     */
    protected function urutAcak($items, $seed, $keyResolver)
    {
        $collection = collect($items)->values();

        return $collection->map(function ($item, $i) use ($seed, $keyResolver) {
            $key = $keyResolver($item, $i);

            return [
                'item' => $item,
                'weight' => crc32(md5($seed.'-'.$key)),
            ];
        })->sortBy('weight')->pluck('item');
    }
}
