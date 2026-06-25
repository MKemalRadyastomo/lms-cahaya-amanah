@extends('layouts.siswa')

@section('title', 'Ujian')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Ujian &amp; Kuis</h1>
        <p class="text-sm text-gray-500">Kerjakan ujian dan kuis sesuai jadwal yang ditentukan</p>
    </div>

    {{-- Filter --}}
    <div class="flex flex-wrap gap-2">
        @php
            $filters = [
                'semua' => 'Semua',
                'aktif' => 'Sedang Berlangsung',
                'selesai' => 'Sudah Dikerjakan',
            ];
        @endphp
        @foreach ($filters as $key => $label)
            <a href="{{ route('siswa.ujian.index', ['filter' => $key]) }}"
               class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ ($filter === $key || ($filter === 'semua' && ! request('filter'))) ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-emerald-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($ujians->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white py-16 text-center">
            <p class="text-sm text-gray-400">Tidak ada ujian yang ditemukan.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($ujians as $item)
                @php
                    $statusConfig = [
                        'tersedia' => ['Tersedia', 'bg-emerald-50 text-emerald-700'],
                        'berlangsung' => ['Sedang Dikerjakan', 'bg-blue-50 text-blue-700'],
                        'selesai' => ['Selesai', 'bg-gray-100 text-gray-600'],
                        'akan_datang' => ['Belum Dibuka', 'bg-gray-100 text-gray-500'],
                        'berakhir' => ['Waktu Habis', 'bg-red-50 text-red-700'],
                    ];
                    $sc = $statusConfig[$item->status_ujian] ?? $statusConfig['tersedia'];
                @endphp
                <a href="{{ route('siswa.ujian.show', $item) }}" class="block rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900">{{ $item->judul }}</h3>
                            <p class="mt-0.5 text-xs text-gray-500">{{ $item->mapel->nama }} &middot; {{ ucfirst($item->jenis) }} &middot; {{ $item->guru->name }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium {{ $sc[1] }}">{{ $sc[0] }}</span>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                        <span class="inline-flex items-center gap-1">
                            <svg class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08" /></svg>
                            {{ $item->jumlah_soal }} soal
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <svg class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ $item->durasi_menit }} menit
                        </span>
                        <span>{{ $item->waktu_mulai->isoFormat('D MMM Y, HH:mm') }} – {{ $item->waktu_selesai->isoFormat('HH:mm') }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div>{{ $ujians->links() }}</div>
    @endif
</div>
@endsection
