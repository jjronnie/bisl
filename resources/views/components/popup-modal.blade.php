@props([
    'title',
    'buttonText' => null,
    'buttonIcon' => null,
    'openOnLoad' => false,
    'maxWidth' => 'max-w-lg',
])

<div x-cloak x-data="{ open: @js($openOnLoad) }" class="inline-block">

    @isset($trigger)
    <div @click="open = true">
        {{ $trigger }}
    </div>
    @else
    <button @click="open = true" class="btn whitespace-nowrap">
        <span>{{ $buttonText }}</span>

        @if($buttonIcon)
        <i data-lucide="{{ $buttonIcon }}" class="w-4 h-4 ml-1"></i>
        @endif
    </button>
    @endisset

    <div x-show="open" x-cloak @click="open = false" class="fixed inset-0 z-40 bg-black/45"
        x-transition.opacity></div>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click.stop x-transition:enter="transform transition-all duration-300 ease-out"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transform transition-all duration-300 ease-in"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="{{ $maxWidth }} w-full max-h-[calc(100vh-2rem)] flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl">

            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>

                <button @click="open = false" class="text-gray-400 hover:text-red-600" title="Close">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="max-h-[calc(100vh-8.5rem)] overflow-y-auto px-5 py-5">
                {{ $slot }}
            </div>

        </div>
    </div>
</div>
