@extends('layouts.siswa')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 p-6 text-white shadow-sm">
        <h1 class="text-2xl font-bold">Assalamu'alaikum, {{ $user->name }} 👋</h1>
        <p class="mt-1 text-sm text-emerald-50">
            Kelas {{ $kelasSiswa->kelas->nama }} &middot; {{ $kelasSiswa->tahunAjaran->tahun }}
            ({{ ucfirst($kelasSiswa->tahunAjaran->semester) }})
        </p>
        <p class="mt-3 text-xs text-emerald-100">Hari ini {{ $hariIni }}, {{ now()->isoFormat('D MMMM Y') }}</p>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @include('siswa.partials.stat-card', [
            'label' => 'Materi',
            'value' => $jumlahMateri,
            'color' => 'emerald',
            'icon' => 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25',
        ])
        @include('siswa.partials.stat-card', [
            'label' => 'Tugas Aktif',
            'value' => $tugasAktif,
            'color' => 'blue',
            'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08',
        ])
        @include('siswa.partials.stat-card', [
            'label' => 'Tugas Terlewat',
            'value' => $tugasTerlewat,
            'color' => 'red',
            'icon' => 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z',
        ])
        @include('siswa.partials.stat-card', [
            'label' => 'Jadwal Hari Ini',
            'value' => $jadwalHariIni->count(),
            'color' => 'amber',
            'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25',
        ])
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Tugas deadline terdekat --}}
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h2 class="font-semibold text-gray-900">Tugas Mendatang</h2>
                    <a href="{{ route('siswa.tugas.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Lihat semua</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($tugasTerdekat as $tugas)
                        @php
                            $telat = $tugas->pengumpulan && in_array($tugas->pengumpulan->status, ['dinilai']);
                            $sudah = $tugas->pengumpulan && in_array($tugas->pengumpulan->status, ['terkirim', 'terlambat']);
                        @endphp
                        <a href="{{ route('siswa.tugas.show', $tugas) }}" class="flex items-center justify-between gap-4 px-5 py-3.5 transition hover:bg-gray-50">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $tugas->judul }}</p>
                                <p class="text-xs text-gray-500">{{ $tugas->mapel->nama }} &middot; {{ $tugas->guru->name }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                @if ($telat)
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Sudah Dinilai</span>
                                @elseif ($sudah)
                                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">Terkirim</span>
                                @else
                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">{{ $tugas->deadline->diffForHumans() }}</span>
                                @endif
                                <p class="mt-1 text-xs text-gray-400">{{ $tugas->deadline->isoFormat('D MMM, HH:mm') }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-gray-400">Tidak ada tugas mendatang 🎉</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar: Jadwal + Pengumuman --}}
        <div class="space-y-6">
            {{-- Jadwal hari ini --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h2 class="font-semibold text-gray-900">Jadwal {{ $hariIni }}</h2>
                    <a href="{{ route('siswa.jadwal.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Detail</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($jadwalHariIni as $j)
                        <div class="flex items-center gap-3 px-5 py-3">
                            <div class="w-16 shrink-0 text-right">
                                <p class="text-xs font-semibold text-gray-900">{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H.i') }}</p>
                                <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($j->jam_selesai)->format('H.i') }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-800">{{ $j->mapel->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $j->guru->name }} &middot; {{ $j->ruang ?: '-' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-gray-400">Libur, tidak ada jadwal</div>
                    @endforelse
                </div>
            </div>

            {{-- Pengumuman --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h2 class="font-semibold text-gray-900">Pengumuman</h2>
                    <a href="{{ route('siswa.pengumuman.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Semua</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($pengumuman as $p)
                        <a href="{{ route('siswa.pengumuman.show', $p) }}" class="block px-5 py-3 transition hover:bg-gray-50">
                            <p class="truncate text-sm font-medium text-gray-800">
                                @if ($p->is_pinned)<span class="text-amber-500">📌</span> @endif{{ $p->judul }}
                            </p>
                            <p class="mt-0.5 text-xs text-gray-400">{{ $p->created_at->isoFormat('D MMM Y') }}</p>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada pengumuman</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
