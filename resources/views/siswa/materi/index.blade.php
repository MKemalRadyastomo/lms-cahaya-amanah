@extends('layouts.siswa')

@section('title', 'Materi Pembelajaran')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Materi Pembelajaran</h1>
            <p class="text-sm text-gray-500">Pelajari materi yang telah dibagikan guru</p>
        </div>
    </div>

    {{-- Filter mapel --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('siswa.materi.index') }}"
           class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ ! $mapelId ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-emerald-300' }}">
            Semua
        </a>
        @foreach ($mapelList as $m)
            <a href="{{ route('siswa.materi.index', ['mapel' => $m->id]) }}"
               class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ $mapelId == $m->id ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-emerald-300' }}">
                {{ $m->nama }}
            </a>
        @endforeach
    </div>

    @if ($materi->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white py-16 text-center">
            <p class="text-sm text-gray-400">Belum ada materi yang dibagikan.</p>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($materi as $item)
                <a href="{{ route('siswa.materi.show', $item) }}" class="group flex flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">{{ $item->mapel->nama }}</span>
                        @if ($item->file_path)
                            <span class="text-gray-300 group-hover:text-emerald-500">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l11.314-11.314a3 3 0 014.243 4.243L6.202 20.396a1.5 1.5 0 01-2.121-2.122L15.254 7.1" /></svg>
                            </span>
                        @endif
                    </div>
                    <h3 class="mt-3 line-clamp-2 text-sm font-semibold text-gray-900 group-hover:text-emerald-700">{{ $item->judul }}</h3>
                    <p class="mt-1 text-xs text-gray-500">oleh {{ $item->guru->name }}</p>
                    <p class="mt-3 text-xs text-gray-400">{{ $item->created_at->isoFormat('D MMM Y') }}</p>
                </a>
            @endforeach
        </div>

        <div>{{ $materi->links() }}</div>
    @endif
</div>
@endsection
