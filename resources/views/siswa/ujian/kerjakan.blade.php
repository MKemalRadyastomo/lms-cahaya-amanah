<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $ujian->judul }} - Ujian</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-800">
<div
    x-data="ujianApp({
        simpanUrl: '{{ route('siswa.ujian.simpan', $ujian) }}',
        submitUrl: '{{ route('siswa.ujian.submit', $ujian) }}',
        batasWaktu: '{{ $batasWaktu->toIso8601String() }}',
        totalSoal: {{ $soals->count() }},
        soalIds: {{ Illuminate\Support\Js::from($soals->pluck('id')->values()->all()) }},
        jawabanAwal: {{ Illuminate\Support\Js::from((object) ($jawabanTersimpan ?? [])) }},
        terjawabAwal: {{ $terjawab }}
    })"
    @beforeunload.window="simpan(true)"
>
    {{-- Topbar --}}
    <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6">
        <div class="min-w-0">
            <p class="truncate text-sm font-bold text-gray-900">{{ $ujian->judul }}</p>
            <p class="text-xs text-gray-500">{{ $ujian->mapel->nama }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden text-xs text-gray-400 sm:inline" x-text="statusSimpan"></span>
            <div
                class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm font-bold tabular-nums"
                :class="sisaDetik <= 60 ? 'bg-red-100 text-red-700 animate-pulse' : 'bg-emerald-50 text-emerald-700'">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span x-text="timerLabel">--:--:--</span>
            </div>
        </div>
    </header>

    <div class="mx-auto flex max-w-5xl gap-6 px-4 py-6 sm:px-6">
        {{-- Navigasi soal --}}
        <aside class="hidden w-48 shrink-0 lg:block">
            <div class="sticky top-24 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Navigasi Soal</p>
                <div class="grid grid-cols-5 gap-2">
                    <template x-for="n in totalSoal" :key="n">
                        <button
                            type="button"
                            @click="soalAktif = n - 1"
                            class="size-9 rounded-lg text-xs font-semibold transition"
                            :class="tombolKelas(n - 1)"
                            x-text="n"></button>
                    </template>
                </div>
                <div class="mt-4 space-y-1.5 text-xs text-gray-500">
                    <div class="flex items-center gap-2"><span class="size-3 rounded bg-emerald-500"></span> Terjawab</div>
                    <div class="flex items-center gap-2"><span class="size-3 rounded border border-gray-300"></span> Belum</div>
                    <div class="flex items-center gap-2"><span class="size-3 rounded bg-blue-500"></span> Aktif</div>
                </div>
                <div class="mt-4 border-t border-gray-100 pt-3 text-xs text-gray-500">
                    Terjawab: <span class="font-semibold text-gray-800" x-text="terjawab + '/' + totalSoal"></span>
                </div>
            </div>
        </aside>

        {{-- Soal --}}
        <main class="min-w-0 flex-1">
            <form id="formUjian" method="POST" :action="submitUrl" @submit.prevent="konfirmasiSubmit()">
                @csrf
                <input type="hidden" name="_method" value="POST">

                @foreach ($soals as $i => $soal)
                    <section
                        x-show="soalAktif === {{ $i }}"
                        x-transition.opacity
                        class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <span class="rounded-md bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">Soal {{ $i + 1 }} dari {{ $soals->count() }}</span>
                            @if ($soal->tipe === 'pg')
                                <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700">Pilihan Ganda (otomatis)</span>
                            @else
                                <span class="rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-medium text-amber-700">Essay (dinilai manual)</span>
                            @endif
                        </div>

                        <div class="prose prose-sm max-w-none text-gray-800">{!! nl2br(e($soal->pertanyaan)) !!}</div>
                        @if ((float) $soal->poin != 1)
                            <p class="mt-2 text-xs text-gray-400">Poin: {{ $soal->poin }}</p>
                        @endif

                        <input type="hidden" name="soal_{{ $soal->id }}" value="{{ $soal->id }}">

                        <div class="mt-5">
                            @if ($soal->tipe === 'pg')
                                <div class="space-y-2">
                                    @foreach ($soal->opsi_diurut as $opsi)
                                        @php $nilai = is_array($opsi) ? ($opsi['teks'] ?? json_encode($opsi)) : (string) $opsi; @endphp
                                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3 transition hover:border-emerald-300 hover:bg-emerald-50/30"
                                               :class="jawaban[{{ $soal->id }}] === @js($nilai) && 'border-emerald-500 bg-emerald-50'">
                                            <input type="radio"
                                                   name="jawaban[{{ $soal->id }}]"
                                                   value="{{ $nilai }}"
                                                   class="mt-0.5 text-emerald-600 focus:ring-emerald-500"
                                                   x-model.string="jawaban[{{ $soal->id }}]"
                                                   @change="simpan(false)">
                                            <span class="text-sm text-gray-700">{{ $nilai }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <textarea
                                    name="jawaban[{{ $soal->id }}]"
                                    rows="6"
                                    x-model="jawaban[{{ $soal->id }}]"
                                    @input.debounce.1500ms="simpan(false)"
                                    class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="Tulis jawaban Anda di sini..."></textarea>
                            @endif
                        </div>
                    </section>
                @endforeach

                {{-- Tombol navigasi bawah --}}
                <div class="mt-5 flex items-center justify-between">
                    <button type="button" @click="prev()" :disabled="soalAktif === 0"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                        Sebelumnya
                    </button>

                    <span class="text-xs text-gray-400 lg:hidden" x-text="'Soal ' + (soalAktif + 1) + ' / ' + totalSoal"></span>

                    <div class="flex items-center gap-2">
                        <template x-if="soalAktif < totalSoal - 1">
                            <button type="button" @click="next()"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                                Berikutnya
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                            </button>
                        </template>
                        <template x-if="soalAktif === totalSoal - 1">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                Kumpulkan Ujian
                            </button>
                        </template>
                    </div>
                </div>
            </form>
        </main>
    </div>

    {{-- Modal konfirmasi submit --}}
    <div x-show="tampilkanModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-xl" @click.outside="tampilkanModal = false">
            <h3 class="text-base font-bold text-gray-900">Kumpulkan Ujian?</h3>
            <p class="mt-2 text-sm text-gray-600">
                Anda telah menjawab <span class="font-semibold" x-text="terjawab"></span> dari <span class="font-semibold" x-text="totalSoal"></span> soal.
                <template x-if="terjawab < totalSoal">
                    <span class="text-amber-600">Masih ada soal yang belum terjawab.</span>
                </template>
            </p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="tampilkanModal = false" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">Batal</button>
                <button type="button" @click="finalSubmit()" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Ya, Kumpulkan</button>
            </div>
        </div>
    </div>
</div>

<script>
function ujianApp(config) {
    return {
        simpanUrl: config.simpanUrl,
        submitUrl: config.submitUrl,
        batasWaktu: new Date(config.batasWaktu).getTime(),
        totalSoal: config.totalSoal,
        soalIds: config.soalIds,
        jawaban: { ...(config.jawabanAwal || {}) },
        soalAktif: 0,
        sisaDetik: 0,
        timerLabel: '--:--:--',
        statusSimpan: '',
        terjawab: config.terjawabAwal || 0,
        tampilkanModal: false,
        _timer: null,
        _autosave: null,
        _submitting: false,

        init() {
            // jawaban sudah di-seed dari server (jawabanAwal); pastikan reaktivitas Alpine mengisi input.
            this.$nextTick(() => this.hitungTerjawab());

            this._timer = setInterval(() => this.updateTimer(), 1000);
            this.updateTimer();

            // Autosave berkala tiap 20 detik
            this._autosave = setInterval(() => this.simpan(false), 20000);
        },

        hitungTerjawab() {
            const n = Object.values(this.jawaban).filter(v => v !== null && v !== '' && v !== undefined).length;
            this.terjawab = n;
            return n;
        },

        updateTimer() {
            const now = Date.now();
            let sisa = Math.floor((this.batasWaktu - now) / 1000);
            if (sisa <= 0) {
                sisa = 0;
                clearInterval(this._timer);
                this.statusSimpan = '⏱ Waktu habis, mengumpulkan...';
                this.finalSubmit();
                return;
            }
            this.sisaDetik = sisa;
            const h = Math.floor(sisa / 3600);
            const m = Math.floor((sisa % 3600) / 60);
            const d = sisa % 60;
            this.timerLabel = [h, m, d].map(x => String(x).padStart(2, '0')).join(':');
        },

        tombolKelas(idx) {
            const id = String(this.soalIds[idx]);
            const terisi = this.jawaban[id] !== undefined && this.jawaban[id] !== null && this.jawaban[id] !== '';
            if (idx === this.soalAktif) return 'bg-blue-500 text-white';
            return terisi ? 'bg-emerald-500 text-white' : 'border border-gray-300 text-gray-500 hover:bg-gray-100';
        },

        prev() { if (this.soalAktif > 0) this.soalAktif--; },
        next() { if (this.soalAktif < this.totalSoal - 1) this.soalAktif++; },

        async simpan(sinkron) {
            if (this._submitting) return;
            const data = {};
            const fd = new FormData(document.getElementById('formUjian'));
            fd.forEach((val, key) => { if (key.startsWith('jawaban[')) data[key] = val; });

            this.hitungTerjawanFromData(data);

            this.statusSimpan = 'Menyimpan...';
            try {
                const res = await fetch(this.simpanUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ jawaban: this.ekstrakJawaban(data) }),
                });
                if (res.status === 410) {
                    this.finalSubmit();
                    return;
                }
                if (res.ok) {
                    const json = await res.json();
                    this.statusSimpan = '✓ Tersimpan ' + (json.tersimpan_pada || '');
                    this.terjawab = json.terjawab ?? this.terjawab;
                } else {
                    this.statusSimpan = 'Gagal menyimpan';
                }
            } catch (e) {
                this.statusSimpan = 'Gagal menyimpan';
            }
        },

        hitungTerjawanFromData(data) {
            this.jawaban = this.ekstrakJawaban(data);
            this.terjawab = Object.values(this.jawaban).filter(v => v !== null && v !== '' && v !== undefined).length;
        },

        ekstrakJawaban(data) {
            const out = {};
            Object.keys(data).forEach(k => {
                const m = k.match(/^jawaban\[(\d+)\]$/);
                if (m) out[m[1]] = data[k];
            });
            return out;
        },

        konfirmasiSubmit() {
            this.simpan(false);
            this.tampilkanModal = true;
        },

        finalSubmit() {
            if (this._submitting) return;
            this._submitting = true;
            clearInterval(this._timer);
            clearInterval(this._autosave);
            const form = document.getElementById('formUjian');
            form.action = this.submitUrl;
            form.submit();
        },
    };
}
</script>
</body>
</html>
