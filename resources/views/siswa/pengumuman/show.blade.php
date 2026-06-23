@extends('layouts.siswa')

@section('title', $pengumuman->judul)

@section('content')
<div class="space-y-5">
    <a href="{{ route('siswa.pengumuman.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-emerald-600">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Kembali ke daftar pengumuman
    </a>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <h1 class="text-2xl font-bold text-gray-900">
                @if ($pengumuman->is_pinned)<span class="text-amber-500">📌</span> @endif{{ $pengumuman->judul }}
            </h1>
        </div>
        <div class="mt-2 flex items-center gap-2 text-xs text-gray-400">
            @if ($pengumuman->creator)
                <img src="https://ui-avatars.com/api/?name={{ urlencode($pengumuman->creator->name) }}&size=40" alt="" class="size-5 rounded-full">
                <span class="font-medium text-gray-600">{{ $pengumuman->creator->name }}</span>
                <span>&middot;</span>
            @endif
            <span>{{ $pengumuman->created_at->isoFormat('D MMMM Y, HH:mm') }}</span>
        </div>

        <article class="prose prose-sm mt-5 max-w-none text-gray-700">
            {!! $pengumuman->konten !!}
        </article>
    </div>
</div>
@endsection
