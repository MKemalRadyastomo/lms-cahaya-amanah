@extends('layouts.siswa')

@section('title', 'Hasil - ' . $ujian->judul)

@section('content')
<div class="space-y-5">
    <a href="{{ route('siswa.ujian.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-emerald-600">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Kembali ke daftar ujian
    </a>

    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    {{-- Ringkasan --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <span class="rounded-md bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">{{ $ujian->mapel->nama }} &middot; {{ ucfirst($ujian->jenis) }}</span>
                <h1 class="mt-3 text-2xl font-bold text-gray-900">{{ $ujian->judul }}</h1>
                <p class="mt-1 text-xs text-gray-500">Selesai pada {{ $hasil->waktu_selesai?->isoFormat('D MMM Y, HH:mm') }}</p>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 px-6 py-4 text-center text-white shadow-sm">
                @if ($tampilkan || $hasil->nilai !== null)
                    <p class="text-4xl font-bold tabular-nums">{{ $hasil->nilai ?? '-' }}</p>
                    <p class="text-xs text-emerald-50">Nilai</p>
                @else
                    <p class="text-sm font-semibold">Menunggu<br>Penilaian</p>
                @endif
            </div>
        </div>

        @if ($adaEssay)
            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                Ujian ini memuat soal essay yang sedang menunggu penilaian manual dari guru. Nilai saat ini belum final dan dapat berubah setelah essay dinilai.
            </div>
        @endif
    </div>

    {{-- Rincian jawaban --}}
    @if ($tampilkan && $rincian)
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="font-semibold text-gray-900">Rincian Jawaban</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($rincian as $i => $r)
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <span class="rounded-md bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">Soal {{ $i + 1 }}</span>
                            @if ($r->soal->tipe === 'pg')
                                @if ($r->benar)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Benar
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Salah
                                    </span>
                                @endif
                            @else
                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">Essay</span>
                            @endif
                        </div>

                        <div class="mt-3 prose prose-sm max-w-none text-gray-800">{!! nl2br(e($r->soal->pertanyaan)) !!}</div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-lg border border-gray-200 p-3">
                                <p class="text-xs font-medium text-gray-400">Jawaban Anda</p>
                                <p class="mt-1 text-sm text-gray-700">{{ $r->jawaban ?: '(tidak dijawab)' }}</p>
                            </div>
                            @if ($r->soal->tipe === 'pg')
                                <div class="rounded-lg border border-emerald-200 bg-emerald-50/40 p-3">
                                    <p class="text-xs font-medium text-emerald-600">Kunci Jawaban</p>
                                    <p class="mt-1 text-sm font-medium text-emerald-800">{{ $r->soal->jawaban_benar ?: '-' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="rounded-xl border border-gray-200 bg-white p-6 text-center shadow-sm">
            <p class="text-sm text-gray-500">Rincian jawaban tidak ditampilkan untuk ujian ini. Hubungi guru pengampu bila ingin mengetahui hasil detail.</p>
        </div>
    @endif
</div>
@endsection
