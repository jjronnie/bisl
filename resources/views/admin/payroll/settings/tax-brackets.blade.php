<x-app-layout>
    <x-page-title title="Tax Brackets" subtitle="Configure PAYE tax brackets for payroll" />

    <div x-data="{ loaded: true }" x-show="loaded" x-transition.duration.500ms class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        {{-- Add New Bracket --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Add Tax Bracket</h2>
            <form method="POST" action="{{ route('admin.payroll.settings.tax-brackets.store') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <x-input-label value="From Amount (UGX)" />
                        <x-text-input name="from_amount" type="number" step="0.01" min="0" class="w-full" required />
                    </div>
                    <div>
                        <x-input-label value="To Amount (UGX) — leave empty for unlimited" />
                        <x-text-input name="to_amount" type="number" step="0.01" min="0" class="w-full" />
                    </div>
                    <div>
                        <x-input-label value="Rate (%)" />
                        <x-text-input name="rate" type="number" step="0.01" min="0" max="100" class="w-full" required />
                    </div>
                    <div>
                        <x-input-label value="Effective From" />
                        <x-text-input name="effective_from" type="date" class="w-full" required />
                    </div>
                    <div>
                        <x-input-label value="Effective To (optional)" />
                        <x-text-input name="effective_to" type="date" class="w-full" />
                    </div>
                    <x-primary-button class="w-full">Save Bracket</x-primary-button>
                </div>
            </form>
        </div>

        {{-- Brackets List --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Brackets</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2 px-3 font-medium text-gray-600">From</th>
                            <th class="py-2 px-3 font-medium text-gray-600">To</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Rate</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Effective</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($brackets as $bracket)
                            <tr class="border-b hover:bg-gray-50" x-data="{ editing: false, from_amount: '{{ $bracket->from_amount }}', to_amount: '{{ $bracket->to_amount ?? '' }}', rate: '{{ $bracket->rate }}', effective_from: '{{ $bracket->effective_from->format('Y-m-d') }}', effective_to: '{{ $bracket->effective_to?->format('Y-m-d') ?? '' }}' }">
                                {{-- View Mode --}}
                                <td class="py-2 px-3" x-show="!editing">UGX {{ number_format($bracket->from_amount) }}</td>
                                <td class="py-2 px-3" x-show="!editing">{{ $bracket->to_amount ? 'UGX '.number_format($bracket->to_amount) : '∞' }}</td>
                                <td class="py-2 px-3 font-medium" x-show="!editing">{{ $bracket->rate }}%</td>
                                <td class="py-2 px-3 text-xs" x-show="!editing">
                                    {{ $bracket->effective_from->format('d M Y') }}
                                    @if ($bracket->effective_to)
                                        → {{ $bracket->effective_to->format('d M Y') }}
                                    @endif
                                </td>
                                <td class="py-2 px-3" x-show="!editing">
                                    <button @click="editing = true" class="btn-sm">Edit</button>
                                </td>
                                {{-- Edit Mode --}}
                                <td class="py-2 px-3" x-show="editing">
                                    <input type="number" step="0.01" x-model="from_amount" class="w-24 border-gray-300 rounded text-sm">
                                </td>
                                <td class="py-2 px-3" x-show="editing">
                                    <input type="number" step="0.01" x-model="to_amount" class="w-24 border-gray-300 rounded text-sm" placeholder="∞">
                                </td>
                                <td class="py-2 px-3" x-show="editing">
                                    <input type="number" step="0.01" min="0" max="100" x-model="rate" class="w-20 border-gray-300 rounded text-sm">
                                </td>
                                <td class="py-2 px-3" x-show="editing">
                                    <input type="date" x-model="effective_from" class="w-full border-gray-300 rounded text-sm mb-1">
                                    <input type="date" x-model="effective_to" class="w-full border-gray-300 rounded text-sm" placeholder="∞">
                                </td>
                                <td class="py-2 px-3" x-show="editing">
                                    <form method="POST" :action="'{{ route('admin.payroll.settings.tax-brackets.update', $bracket) }}'" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="from_amount" :value="from_amount">
                                        <input type="hidden" name="to_amount" :value="to_amount || ''">
                                        <input type="hidden" name="rate" :value="rate">
                                        <input type="hidden" name="effective_from" :value="effective_from">
                                        <input type="hidden" name="effective_to" :value="effective_to || ''">
                                        <button type="submit" class="btn-sm">Save</button>
                                    </form>
                                    <button @click="editing = false" class="btn-gray">Cancel</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">No tax brackets configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $brackets->links() }}</div>
        </div>
    </div>
</x-app-layout>
