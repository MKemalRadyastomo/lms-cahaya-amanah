@php
    $record = $getRecord();
    $jawaban = $record?->jawaban ?? [];
    $soals = $record?->ujian?->soals()->orderBy('id')->get() ?? collect();
@endphp

@if ($soals->isEmpty())
    <p class="text-sm text-gray-500">Ujian ini tidak memiliki soal.</p>
@else
    <div class="space-y-3">
        @foreach ($soals as $i => $soal)
            @php
                $jawab = $jawaban[$soal->id] ?? null;
                $benar = $soal->tipe === 'pg' && $jawab !== null && $jawab !== ''
                    && strcasecmp(trim($jawab), trim((string) $soal->jawaban_benar)) === 0;
            @endphp
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="flex items-start justify-between gap-3">
                    <span class="rounded bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">Soal {{ $i + 1 }}</span>
                    @if ($soal->tipe === 'pg')
                        @if ($benar)
                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Benar ({{ $soal->poin }})</span>
                        @else
                            <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">Salah</span>
                        @endif
                    @else
                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">Essay - nilai manual</span>
                    @endif
                </div>

                <div class="mt-2 text-sm text-gray-800">{!! nl2br(e($soal->pertanyaan)) !!}</div>

                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    <div class="rounded border border-gray-200 bg-gray-50 p-2">
                        <p class="text-[10px] font-semibold uppercase text-gray-400">Jawaban Siswa</p>
                        <p class="mt-0.5 text-sm text-gray-700">{{ $jawab ?: '(tidak dijawab)' }}</p>
                    </div>
                    @if ($soal->tipe === 'pg')
                        <div class="rounded border border-emerald-200 bg-emerald-50 p-2">
                            <p class="text-[10px] font-semibold uppercase text-emerald-500">Kunci</p>
                            <p class="mt-0.5 text-sm font-medium text-emerald-800">{{ $soal->jawaban_benar ?: '-' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
