@php
    $colors = [
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'blue' => 'bg-blue-50 text-blue-600',
        'red' => 'bg-red-50 text-red-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'purple' => 'bg-purple-50 text-purple-600',
        'gray' => 'bg-gray-100 text-gray-600',
    ];
    $colorClass = $colors[$color] ?? $colors['gray'];
@endphp
<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
    <div class="flex items-center gap-3">
        <span class="flex size-10 items-center justify-center rounded-lg {{ $colorClass }}">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
            </svg>
        </span>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
            <p class="text-xs font-medium text-gray-500">{{ $label }}</p>
        </div>
    </div>
</div>
