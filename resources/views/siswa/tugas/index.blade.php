@extends('layouts.siswa')

@section('title', 'Tugas')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Tugas</h1>
        <p class="text-sm text-gray-500">Kerjakan dan kumpulkan tugas tepat waktu</p>
    </div>

    {{-- Filter --}}
    <div class="flex flex-wrap gap-2">
        @php
            $filters = [
                'semua' => 'Semua',
                'aktif' => 'Belum Berakhir',
                'selesai' => 'Sudah Dikumpulkan',
            ];
        @endphp
        @foreach ($filters as $key => $label)
            <a href="{{ route('siswa.tugas.index', ['filter' => $key]) }}"
               class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ ($filter === $key || ($filter === 'semua' && ! request('filter'))) ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-emerald-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($tugas->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white py-16 text-center">
            <p class="text-sm text-gray-400">Tidak ada tugas yang ditemukan.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($tugas as $item)
                @php
                    $statusConfig = [
                        'belum' => ['label' => 'Belum Dikumpulkan', 'class' => 'bg-gray-100 text-gray-600'],
                        'terkirim' => ['label' => 'Terkirim', 'class' => 'bg-blue-50 text-blue-700'],
                        'terlambat' => ['label' => 'Terlambat', 'class' => 'bg-amber-50 text-amber-700'],
                        'dinilai' => ['label' => 'Dinilai: ' . ($item->pengumpulan?->nilai ?? '-'), 'class' => 'bg-emerald-50 text-emerald-700'],
                        'terlewat' => ['label' => 'Melewati Tenggat', 'class' => 'bg-red-50 text-red-700'],
                    ];
                    $sc = $statusConfig[$item->status_kirim] ?? $statusConfig['belum'];
                @endphp
                <a href="{{ route('siswa.tugas.show', $item) }}" class="block rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900">{{ $item->judul }}</h3>
                            <p class="mt-0.5 text-xs text-gray-500">{{ $item->mapel->nama }} &middot; {{ $item->guru->name }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium {{ $sc['class'] }}">{{ $sc['label'] }}</span>
                    </div>
                    <div class="mt-3 flex items-center gap-2 text-xs">
                        @php $telat = now()->isAfter($item->deadline); @endphp
                        <svg class="size-4 {{ $telat ? 'text-red-400' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="{{ $telat ? 'font-medium text-red-600' : 'text-gray-500' }}">
                            Tenggat: {{ $item->deadline->isoFormat('D MMM Y, HH:mm') }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div>{{ $tugas->links() }}</div>
    @endif
</div>
@endsection
