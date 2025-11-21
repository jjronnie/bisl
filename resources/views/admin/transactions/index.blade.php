<x-app-layout>
    <x-page-title title="Transactions" subtitle="Manage All Transactions" />

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
                        placeholder="Search by member name or transaction type...">
                </div>
            </div>

            <div class="flex gap-3">

                @include('admin.transactions.create')


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

        @if($transactions->isEmpty())
        <x-empty-state icon="receipt" message="No transactions found." />
        @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <x-table :headers="['Transaction ID','Date', 'Member', 'Type',  'Amount', ]" showActions="false">
                @foreach($transactions as $transaction)
                <template x-if="!search || 
                                '{{ $transaction->member->name ?? 'N/A' }}'.toLowerCase().includes(search.toLowerCase()) || 
                                '{{ $transaction->reference_number }}'.toLowerCase().includes(search.toLowerCase())
                             ">
                    <x-table.row>

                        <x-table.cell>
                            <div class="text-sm text-gray-900">
                                {{ $transaction->reference_number }}
                            </div>

                        </x-table.cell>


                        {{-- Date --}}
                        <x-table.cell>
                            <div class="text-sm text-gray-900">
                                {{ $transaction->created_at }}
                            </div>
                            <div class="text-xs text-gray-500">
                                by: {{ $transaction->creator->name ?? 'Unknown' }}

                            </div>

                        </x-table.cell>

                        {{-- Member --}}
                        <x-table.cell>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $transaction->member->name ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                A/C: {{ $transaction->member->savingsAccount->account_number }}


                            </div>

                        </x-table.cell>

                        {{-- Type --}}
                        <x-table.cell>


                            <x-status-badge :status="ucfirst($transaction->transaction_type)" />

                        </x-table.cell>



                        {{-- Amount --}}
                        <x-table.cell>


                            UGX {{ number_format($transaction->amount) }}

                        </x-table.cell>


                        <x-table.cell>



                            @include('admin.transactions.show')



                        </x-table.cell>






                    </x-table.row>
                </template>
                @endforeach
            </x-table>

            {{-- Pagination --}}
            @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>
        @endif
    </div>
</x-app-layout>