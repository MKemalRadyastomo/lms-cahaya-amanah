@extends('layouts.siswa')

@section('title', $materi->judul)

@section('content')
<div class="space-y-5">
    <a href="{{ route('siswa.materi.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-emerald-600">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Kembali ke daftar materi
    </a>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 p-6">
            <span class="rounded-md bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">{{ $materi->mapel->nama }}</span>
            <h1 class="mt-3 text-2xl font-bold text-gray-900">{{ $materi->judul }}</h1>
            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                <span>oleh <span class="font-medium text-gray-700">{{ $materi->guru->name }}</span></span>
                <span>{{ $materi->created_at->isoFormat('D MMMM Y') }}</span>
            </div>
        </div>

        <div class="space-y-4 p-6">
            @if ($materi->video_url)
                <div class="overflow-hidden rounded-lg bg-black aspect-video">
                    <iframe class="size-full" src="{{ $materi->video_url }}" title="Video materi" allowfullscreen></iframe>
                </div>
            @endif

            @if ($materi->konten)
                <article class="prose prose-sm max-w-none text-gray-700">
                    {!! $materi->konten !!}
                </article>
            @endif

            @if ($materi->file_path)
                <a href="{{ route('siswa.materi.lampiran', $materi) }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Unduh Lampiran
                </a>
            @endif
        </div>
    </div>

    @if ($terkait->isNotEmpty())
        <div>
            <h2 class="mb-3 text-sm font-semibold text-gray-700">Materi Terkait</h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($terkait as $t)
                    <a href="{{ route('siswa.materi.show', $t) }}" class="rounded-lg border border-gray-200 bg-white p-4 transition hover:border-emerald-300">
                        <p class="line-clamp-2 text-sm font-medium text-gray-800">{{ $t->judul }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $t->created_at->isoFormat('D MMM Y') }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
