@extends('layouts.siswa')

@section('title', 'Pengumuman')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Pengumuman</h1>
        <p class="text-sm text-gray-500">Informasi terbaru dari sekolah</p>
    </div>

    @if ($pengumuman->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white py-16 text-center">
            <p class="text-sm text-gray-400">Belum ada pengumuman.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($pengumuman as $p)
                <a href="{{ route('siswa.pengumuman.show', $p) }}" class="block rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900">
                                @if ($p->is_pinned)<span class="text-amber-500">📌</span> @endif{{ $p->judul }}
                            </h3>
                            <p class="mt-1 line-clamp-2 text-sm text-gray-500">{!! strip_tags($p->konten) !!}</p>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center gap-2 text-xs text-gray-400">
                        @if ($p->creator)
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($p->creator->name) }}&size=40" alt="" class="size-5 rounded-full">
                            <span>{{ $p->creator->name }}</span>
                            <span>&middot;</span>
                        @endif
                        <span>{{ $p->created_at->isoFormat('D MMMM Y, HH:mm') }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div>{{ $pengumuman->links() }}</div>
    @endif
</div>
@endsection
