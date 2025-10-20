@props([
    'title' => '',
    'sub_title' => '',
    'value' => '',
    'icon' => '',
    'color' => 'blue',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow p-6']) }}>
    <div class="flex items-center justify-between mb-4">
        {{-- Only display if title is provided --}}
        @if ($title)
            <p class="text-sm text-gray-600">{{ $title }}</p>
        @endif

        {{-- Only display icon if provided --}}
        @if ($icon)
            <i data-lucide="{{ $icon }}" class="w-5 h-5 text-{{ $color }}-600"></i>
        @endif
    </div>

    {{-- Only display value if provided --}}
    @if ($value)
        <p class="text-3xl font-bold" style="color: hsl(222, 47%, 11%);">{{ $value }}</p>
    @endif

    {{-- Only display subtitle if provided --}}
    @if ($sub_title)
        <p class="text-sm text-gray-600 mt-2">{{ $sub_title }}</p>
    @endif
</div>
