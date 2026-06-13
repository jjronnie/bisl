@props([
    'action',
    'type',
    'title' => 'Apply Penalty',
    'triggerText' => '',
    'triggerClass' => ' btn-sm ',
    'dateLabel' => 'Date',
    'datePlaceholder' => '',
    'notesPlaceholder' => 'Additional details...',
    'buttonText' => 'Apply Penalty',
    'method' => 'POST',
])

<div x-data="{ showModal: false }" class="inline-block">
    <button @click="showModal = true" type="button" class="{{ $triggerClass }}">
        {{ $triggerText }}
    </button>

    <div x-show="showModal" x-transition
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" style="display: none;"
        @keydown.escape.window="showModal = false">

        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6" @click.away="showModal = false">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-red-200">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
            </div>

            <p class="text-sm text-red-600 mb-4">{{ $slot }}</p>

            <form method="POST" action="{{ $action }}">
                @csrf
                @method($method)

                <input type="hidden" name="type" value="{{ $type }}">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $dateLabel }}</label>
                        <input type="date" name="meeting_date" value="{{ old('meeting_date') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                        <textarea name="notes" rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 text-sm"
                            placeholder="{{ $notesPlaceholder }}">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button @click="showModal = false" type="button" class="btn-gray">
                        Cancel
                    </button>
                    <button type="submit" class=" btn-sm ">
                        {{ $buttonText }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
