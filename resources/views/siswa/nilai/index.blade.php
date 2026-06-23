@extends('layouts.siswa')

@section('title', 'Nilai / Rapor')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Nilai & Rapor</h1>
        <p class="text-sm text-gray-500">Kelas {{ $kelasSiswa->kelas->nama }} &middot; {{ $kelasSiswa->tahunAjaran->tahun }} ({{ ucfirst($kelasSiswa->tahunAjaran->semester) }})</p>
    </div>

    {{-- Ringkasan rata-rata --}}
    <div class="grid grid-cols-3 gap-4">
        @php
            $summary = [
                ['label' => 'Rata-rata Tugas', 'value' => $rataTugas, 'color' => 'text-blue-600'],
                ['label' => 'Rata-rata Ujian', 'value' => $rataUjian, 'color' => 'text-emerald-600'],
                ['label' => 'Rata-rata Akhir', 'value' => $rataAkhir, 'color' => 'text-gray-900'],
            ];
        @endphp
        @foreach ($summary as $s)
            <div class="rounded-xl border border-gray-200 bg-white p-4 text-center shadow-sm">
                <p class="text-2xl font-bold {{ $s['color'] }}">{{ $s['value'] ?? '-' }}</p>
                <p class="mt-0.5 text-xs font-medium text-gray-500">{{ $s['label'] }}</p>
            </div>
        @endforeach
    </div>

    @if ($nilais->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white py-16 text-center">
            <p class="text-sm text-gray-400">Belum ada nilai yang direkam.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Mata Pelajaran</th>
                        <th class="px-5 py-3 text-center font-semibold">Nilai Tugas</th>
                        <th class="px-5 py-3 text-center font-semibold">Nilai Ujian</th>
                        <th class="px-5 py-3 text-center font-semibold">Nilai Akhir</th>
                        <th class="px-5 py-3 text-center font-semibold">Predikat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($nilais as $n)
                        @php
                            $predikatConfig = [
                                'A' => 'bg-emerald-50 text-emerald-700',
                                'B' => 'bg-blue-50 text-blue-700',
                                'C' => 'bg-amber-50 text-amber-700',
                                'D' => 'bg-orange-50 text-orange-700',
                                'E' => 'bg-red-50 text-red-700',
                            ];
                            $pc = $predikatConfig[$n->predikat] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3.5">
                                <span class="font-medium text-gray-900">{{ $n->mapel->nama }}</span>
                                @if ($n->deskripsi)
                                    <span class="block text-xs text-gray-400">{{ $n->deskripsi }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center text-gray-700">{{ $n->nilai_tugas ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-center text-gray-700">{{ $n->nilai_ujian ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-center font-semibold text-gray-900">{{ $n->nilai_akhir ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                @if ($n->predikat)
                                    <span class="inline-flex size-7 items-center justify-center rounded-full text-xs font-bold {{ $pc }}">{{ $n->predikat }}</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
