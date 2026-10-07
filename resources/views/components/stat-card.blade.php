@props([
    'title',
    'value',
    'icon',
    'color' => 'indigo',
    'hint' => null,
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'div';

    $colors = [
        'indigo' => 'bg-indigo-100 text-indigo-600',
        'blue'   => 'bg-blue-100 text-blue-600',
        'green'  => 'bg-emerald-100 text-emerald-600',
        'amber'  => 'bg-amber-100 text-amber-600',
        'red'    => 'bg-red-100 text-red-600',
        'violet' => 'bg-violet-100 text-violet-600',
    ];
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'block rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition' . ($href ? ' hover:shadow-md hover:ring-indigo-300' : '')]) }}>
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $title }}</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $value }}</p>
            @if ($hint)
                <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
            @endif
        </div>
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full {{ $colors[$color] ?? $colors['indigo'] }}">
            <x-icon :name="$icon" class="h-6 w-6" />
        </span>
    </div>
</{{ $tag }}>