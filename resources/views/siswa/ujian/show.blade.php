@extends('layouts.siswa')

@section('title', $ujian->judul)

@section('content')
<div class="space-y-5">
    <a href="{{ route('siswa.ujian.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-emerald-600">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Kembali ke daftar ujian
    </a>

    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <span class="rounded-md bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">{{ $ujian->mapel->nama }} &middot; {{ ucfirst($ujian->jenis) }}</span>
                    <h1 class="mt-3 text-2xl font-bold text-gray-900">{{ $ujian->judul }}</h1>
                    <p class="mt-1 text-xs text-gray-500">oleh {{ $ujian->guru->name }}</p>
                </div>
                @php
                    $statusConfig = [
                        'tersedia' => ['Tersedia', 'bg-emerald-50 text-emerald-700'],
                        'berlangsung' => ['Sedang Dikerjakan', 'bg-blue-50 text-blue-700'],
                        'selesai' => ['Selesai', 'bg-gray-100 text-gray-600'],
                        'akan_datang' => ['Belum Dibuka', 'bg-gray-100 text-gray-500'],
                        'berakhir' => ['Waktu Habis', 'bg-red-50 text-red-700'],
                    ];
                    $sc = $statusConfig[$status] ?? $statusConfig['tersedia'];
                @endphp
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $sc[1] }}">{{ $sc[0] }}</span>
            </div>
        </div>

        <div class="space-y-4 p-6">
            @if ($ujian->deskripsi)
                <article class="prose prose-sm max-w-none text-gray-700">{!! $ujian->deskripsi !!}</article>
            @endif

            <div class="grid grid-cols-2 gap-3 text-sm sm:max-w-2xl">
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-400">Waktu Mulai</p>
                    <p class="mt-0.5 font-semibold text-gray-800">{{ $ujian->waktu_mulai->isoFormat('D MMM Y, HH:mm') }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-400">Waktu Selesai</p>
                    <p class="mt-0.5 font-semibold text-gray-800">{{ $ujian->waktu_selesai->isoFormat('D MMM Y, HH:mm') }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-400">Durasi</p>
                    <p class="mt-0.5 font-semibold text-gray-800">{{ $ujian->durasi_menit }} menit</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-400">Jumlah Soal</p>
                    <p class="mt-0.5 font-semibold text-gray-800">{{ $jumlahSoal }} ({{ $jumlahPg }} PG, {{ $jumlahEssay }} Essay)</p>
                </div>
            </div>

            @if ($status === 'selesai')
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    Anda telah menyelesaikan ujian ini.
                    <a href="{{ route('siswa.ujian.hasil', $ujian) }}" class="font-semibold underline">Lihat hasil</a>
                </div>
            @elseif ($status === 'akan_datang')
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    Ujian belum dibuka. Silakan kembali pada {{ $ujian->waktu_mulai->isoFormat('D MMM Y, HH:mm') }}.
                </div>
            @elseif ($status === 'berakhir')
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    Masa ujian telah berakhir dan tidak dapat dikerjakan lagi.
                </div>
            @else
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-medium text-amber-900">⚠️ Perhatian sebelum memulai:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1 text-xs text-amber-800">
                        <li>Pastikan koneksi internet stabil.</li>
                        <li>Jawaban tersimpan otomatis secara berkala, tetapi disarankan menekan tombol simpan.</li>
                        <li>Ujian akan terkirim otomatis saat waktu habis.</li>
                        @if ($status === 'berlangsung')
                            <li>Anda <strong>sudah memulai</strong> ujian ini. Lanjutkan untuk menyelesaikannya.</li>
                        @endif
                    </ul>
                </div>

                <form action="{{ route('siswa.ujian.mulai', $ujian) }}" method="POST" class="space-y-4">
                    @csrf
                    @if ($ujian->passcode)
                        <div>
                            <label for="passcode" class="mb-1.5 block text-sm font-medium text-gray-700">Kode Akses Ujian</label>
                            <input type="text" name="passcode" id="passcode"
                                   class="block w-full max-w-xs rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                   placeholder="Masukkan kode akses" value="{{ old('passcode') }}">
                            @error('passcode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endif
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" /></svg>
                        {{ $status === 'berlangsung' ? 'Lanjutkan Ujian' : 'Mulai Ujian' }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
