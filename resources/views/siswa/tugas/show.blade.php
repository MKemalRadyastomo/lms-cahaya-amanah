@extends('layouts.siswa')

@section('title', $tugas->judul)

@section('content')
<div class="space-y-5">
    <a href="{{ route('siswa.tugas.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-emerald-600">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Kembali ke daftar tugas
    </a>

    {{-- Detail tugas --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <span class="rounded-md bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">{{ $tugas->mapel->nama }}</span>
                    <h1 class="mt-3 text-2xl font-bold text-gray-900">{{ $tugas->judul }}</h1>
                    <p class="mt-1 text-xs text-gray-500">oleh {{ $tugas->guru->name }}</p>
                </div>
                <div class="text-right">
                    @php
                        $statusConfig = [
                            'belum' => ['Belum Dikumpulkan', 'bg-gray-100 text-gray-600'],
                            'terkirim' => ['Terkirim', 'bg-blue-50 text-blue-700'],
                            'terlambat' => ['Terlambat', 'bg-amber-50 text-amber-700'],
                            'dinilai' => ['Sudah Dinilai', 'bg-emerald-50 text-emerald-700'],
                            'terlewat' => ['Melewati Tenggat', 'bg-red-50 text-red-700'],
                        ];
                        $sc = $statusConfig[$statusKirim] ?? $statusConfig['belum'];
                    @endphp
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $sc[1] }}">{{ $sc[0] }}</span>
                </div>
            </div>
        </div>

        <div class="space-y-4 p-6">
            @if ($tugas->deskripsi)
                <article class="prose prose-sm max-w-none text-gray-700">{!! $tugas->deskripsi !!}</article>
            @endif

            <div class="grid grid-cols-2 gap-3 text-sm sm:max-w-md">
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-400">Tenggat Waktu</p>
                    <p class="mt-0.5 font-semibold text-gray-800">{{ $tugas->deadline->isoFormat('D MMM Y, HH:mm') }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-400">Poin Maksimal</p>
                    <p class="mt-0.5 font-semibold text-gray-800">{{ $tugas->poin_max }}</p>
                </div>
            </div>

            @if ($tugas->file_path)
                <a href="{{ route('siswa.tugas.lampiran', $tugas) }}" class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Unduh Berkas Tugas
                </a>
            @endif
        </div>
    </div>

    {{-- Form pengumpulan --}}
    @if ($bisaKirim)
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Pengumpulan Tugas</h2>
            @if ($pengumpulan->exists)
                <p class="mt-1 text-xs text-gray-500">Anda sudah pernah mengumpulkan. Anda dapat memperbarui jawaban sebelum dinilai.</p>
            @endif

            <form action="{{ route('siswa.tugas.submit', $tugas) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Unggah Jawaban <span class="text-gray-400">(PDF, DOC, XLS, ZIP, gambar; maks 10 MB)</span></label>
                    <input type="file" name="file" id="file"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-emerald-700 hover:file:bg-emerald-100"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png,.txt">
                    @error('file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @if ($pengumpulan->file_path)
                        <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                            <svg class="size-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Berkas saat ini:
                            <a href="{{ route('siswa.tugas.jawaban', $tugas) }}" class="font-medium text-emerald-600 hover:underline">{{ basename($pengumpulan->file_path) }}</a>
                        </div>
                    @endif
                </div>

                <div>
                    <label for="catatan" class="mb-1.5 block text-sm font-medium text-gray-700">Catatan untuk Guru <span class="text-gray-400">(opsional)</span></label>
                    <textarea name="catatan" id="catatan" rows="4"
                              class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                              placeholder="Tulis catatan atau pesan untuk guru...">{{ old('catatan', $pengumpulan->catatan) }}</textarea>
                    @error('catatan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                    {{ $pengumpulan->exists ? 'Perbarui Pengumpulan' : 'Kumpulkan Tugas' }}
                </button>
            </form>
        </div>
    @else
        {{-- Sudah dinilai --}}
        @if ($pengumpulan->status === 'dinilai')
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-6">
                <h2 class="text-base font-semibold text-emerald-900">Penilaian</h2>
                <div class="mt-3 grid grid-cols-2 gap-3 sm:max-w-md">
                    <div class="rounded-lg bg-white p-4 text-center">
                        <p class="text-3xl font-bold text-emerald-600">{{ $pengumpulan->nilai }}</p>
                        <p class="text-xs text-gray-500">Nilai / {{ $tugas->poin_max }}</p>
                    </div>
                    <div class="rounded-lg bg-white p-4">
                        <p class="text-xs text-gray-400">Dinilai pada</p>
                        <p class="mt-0.5 text-sm font-medium text-gray-700">{{ $pengumpulan->dinilai_at?->isoFormat('D MMM Y, HH:mm') ?? '-' }}</p>
                        <p class="mt-1 text-xs text-gray-400">Status: {{ ucfirst($pengumpulan->status) }}</p>
                    </div>
                </div>
                @if ($pengumpulan->feedback)
                    <div class="mt-3 rounded-lg bg-white p-4">
                        <p class="text-xs font-medium text-gray-500">Umpan Balik Guru</p>
                        <p class="mt-1 text-sm text-gray-700">{!! nl2br(e($pengumpulan->feedback)) !!}</p>
                    </div>
                @endif
            </div>
        @endif

        @if ($pengumpulan->file_path)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">Jawaban Anda</h2>
                <div class="mt-3 flex items-center gap-2 text-sm">
                    <a href="{{ route('siswa.tugas.jawaban', $tugas) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 font-medium text-gray-700 transition hover:bg-gray-100">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        {{ basename($pengumpulan->file_path) }}
                    </a>
                </div>
                @if ($pengumpulan->catatan)
                    <p class="mt-3 rounded-lg bg-gray-50 p-3 text-sm text-gray-600">{{ $pengumpulan->catatan }}</p>
                @endif
            </div>
        @endif
    @endif
</div>
@endsection
