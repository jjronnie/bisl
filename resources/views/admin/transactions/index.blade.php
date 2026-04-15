<x-app-layout>
    <x-page-title title="Transactions" subtitle="Manage All Transactions" />

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-4">
    <form method="GET"
        action="{{ route('admin.transactions.index') }}"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">

        <!-- Type -->
        <div class="lg:col-span-3">
            <select name="type"
                class="block w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">All Types</option>
                <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Deposits</option>
                <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>Withdrawals</option>
                <option value="reversal" {{ request('type') === 'reversal' ? 'selected' : '' }}>Reversals</option>
            </select>
        </div>

        <!-- Search -->
        <div class="lg:col-span-6">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request()->get('search') }}"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="Search...">
            </div>
        </div>

        <!-- Buttons -->
        <div class="lg:col-span-3 flex flex-col sm:flex-row gap-2">
            <button type="submit" class="btn w-full sm:w-auto justify-center whitespace-nowrap">
                <i data-lucide="filter" class="w-4 h-4"></i>
                <span>Filter</span>
            </button>

            @if (request()->has('search') || request()->has('type'))
                <a href="{{ route('admin.transactions.index') }}"
                    class="btn-gray w-full sm:w-auto justify-center whitespace-nowrap">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    <span>Clear</span>
                </a>
            @endif
        </div>

    </form>
</div>

<div class="flex justify-end mt-4">
    <a href="{{ route('admin.transactions.create') }}" class="btn w-full sm:w-auto justify-center whitespace-nowrap">
        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
        New Transaction
    </a>
</div>

        @if ($transactions->isEmpty())
            <x-empty-state icon="receipt" message="No transactions found." />
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <x-table :headers="['Transaction ID', 'Date', 'Member', 'Type', 'Amount']" showActions="false">
                    @foreach ($transactions as $transaction)
                        @php $isReversed = $transaction->reversals()->exists(); @endphp
                        <x-table.row :class="$isReversed ? '!bg-red-100' : ''">

                            <x-table.cell>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $transaction->reference_number }}
                                </div>
                                @if ($isReversed)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-200 text-red-800 mt-1">
                                        <i data-lucide="rotate-ccw" class="w-3 h-3 mr-1"></i> Reversed
                                    </span>
                                @endif
                            </x-table.cell>


                            {{-- Date --}}
                            <x-table.cell>
                                <div class="text-sm text-gray-900">
                                    {{ $transaction->created_at->format('d M Y H:i') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    by: {{ $transaction->createdBy->name ?? 'Unknown' }}

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
                    @endforeach
                </x-table>

                {{-- Pagination --}}
                @if ($transactions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
