@extends('layouts.siswa')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Jadwal Pelajaran</h1>
        <p class="text-sm text-gray-500">Kelas {{ $kelasSiswa->kelas->nama }} &middot; {{ $kelasSiswa->tahunAjaran->tahun }} ({{ ucfirst($kelasSiswa->tahunAjaran->semester) }})</p>
    </div>

    {{-- Tab hari --}}
    <div class="flex flex-wrap gap-2">
        @foreach ($hariList as $h)
            <a href="{{ route('siswa.jadwal.index', ['hari' => $h]) }}"
               class="flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-medium transition {{ $hariAktif === $h ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-emerald-300' }}">
                {{ $h }}
                @if (($ringkasan[$h] ?? 0) > 0)
                    <span class="rounded-full px-1.5 text-[10px] {{ $hariAktif === $h ? 'bg-white/20' : 'bg-gray-100' }}">{{ $ringkasan[$h] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    @if ($jadwal->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white py-16 text-center">
            <p class="text-sm text-gray-400">Tidak ada jadwal pada hari {{ $hariAktif }}.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Waktu</th>
                        <th class="px-5 py-3 font-semibold">Mata Pelajaran</th>
                        <th class="hidden px-5 py-3 font-semibold sm:table-cell">Guru</th>
                        <th class="px-5 py-3 font-semibold">Ruang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($jadwal as $j)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-3.5">
                                <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H.i') }}</span>
                                <span class="text-gray-400"> - </span>
                                <span class="text-gray-600">{{ \Carbon\Carbon::parse($j->jam_selesai)->format('H.i') }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-medium text-gray-900">{{ $j->mapel->nama }}</span>
                                <span class="block text-xs text-gray-500 sm:hidden">{{ $j->guru->name }}</span>
                            </td>
                            <td class="hidden px-5 py-3.5 text-gray-600 sm:table-cell">{{ $j->guru->name }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $j->ruang ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
