<x-app-layout>
    <x-page-title title="Transfers" subtitle="Manage All Transfers" />

    <div x-data="{ search: '' }" class="space-y-6">
        <!-- Controls -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input x-model="search" type="text"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                        placeholder="Search by member name or transfer type...">
                </div>
            </div>

            <div class="flex gap-3">

                @include('admin.transfers.create')


                <!-- Export to PDF Button -->
                <button class="btn">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                </button>
                <!-- Export to Excel Button -->
                <button class="btn">
                    <i data-lucide="sheet" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

    @if($transfers->isEmpty())
    <x-empty-state icon="receipt" message="No transfers found." />
@else
<div class="bg-white rounded-lg shadow overflow-hidden">
    <x-table :headers="['Transfer ID', 'Date', 'From', 'To', 'Amount', 'By', 'Reason']" showActions="false">

        @foreach($transfers as $transfer)
        <template x-if="!search || 
                        '{{ $transfer->transfer_id }}'.toLowerCase().includes(search.toLowerCase()) || 
                        '{{ $transfer->from_account }}'.toLowerCase().includes(search.toLowerCase()) || 
                        '{{ $transfer->to_account }}'.toLowerCase().includes(search.toLowerCase())">
            <x-table.row>

                {{-- Transfer ID --}}
                <x-table.cell>
                    <div class="text-sm text-gray-900">
                        {{ $transfer->transfer_id }}
                    </div>
                </x-table.cell>

                {{-- Date --}}
                <x-table.cell>
                    <div class="text-sm text-gray-900">
                        {{ $transfer->created_at->format('d M Y H:i') }}
                    </div>
                </x-table.cell>

                {{-- From Account --}}
                <x-table.cell>
                    <div class="text-sm text-gray-900">
                        {{ ucfirst(str_replace('_', ' ', $transfer->from_account)) }}
                    </div>
                    <div class="text-xs text-gray-500">
                        Before: UGX {{ number_format($transfer->from_balance_before) }}
                    </div>
                    <div class="text-xs text-gray-500">
                        After: UGX {{ number_format($transfer->from_balance_after) }}
                    </div>
                </x-table.cell>

                {{-- To Account --}}
                <x-table.cell>
                    <div class="text-sm text-gray-900">
                        {{ ucfirst(str_replace('_', ' ', $transfer->to_account)) }}
                    </div>
                    <div class="text-xs text-gray-500">
                        Before: UGX {{ number_format($transfer->to_balance_before) }}
                    </div>
                    <div class="text-xs text-gray-500">
                        After: UGX {{ number_format($transfer->to_balance_after) }}
                    </div>
                </x-table.cell>

                {{-- Amount --}}
                <x-table.cell>
                    <span class="font-semibold text-gray-900">
                        UGX {{ number_format($transfer->amount) }}
                    </span>
                </x-table.cell>

                {{-- Transferred By --}}
                <x-table.cell>
                    <div class="text-sm text-gray-900">
                        {{ $transfer->user->name ?? 'Unknown' }}
                    </div>
                </x-table.cell>

                {{-- Reason --}}
                <x-table.cell>
                    <div class="text-sm text-gray-700">
                        {{ $transfer->reason ?? 'N/A' }}
                    </div>
                </x-table.cell>

            </x-table.row>
        </template>
        @endforeach

    </x-table>

    @if($transfers->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $transfers->links() }}
    </div>
    @endif
</div>
@endif

    </div>
</x-app-layout>