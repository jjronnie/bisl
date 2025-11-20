<x-app-layout>
    <x-page-title title="loans" subtitle="Manage All loans" />

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

                @include('admin.loans.create')


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

        @if($loans->isEmpty())
        <x-empty-state icon="receipt" message="No loans found." />
        @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <x-table :headers="['Loan ID','Date', 'Member',  'Amount', ]" showActions="false">
                @foreach($loans as $loan)
                <template x-if="!search || 
                                '{{ $loan->member->name ?? 'N/A' }}'.toLowerCase().includes(search.toLowerCase()) || 
                                '{{ $loan->loan_number }}'.toLowerCase().includes(search.toLowerCase())
                             ">
                    <x-table.row>

                        <x-table.cell>
                            <div class="text-sm text-gray-900">
                                {{ $loan->loan_number }}
                            </div>

                        </x-table.cell>


                        {{-- Date --}}
                        <x-table.cell>
                            <div class="text-sm text-gray-900">
                                {{ $loan->created_at }}
                            </div>
                              <div class="text-xs text-gray-500">
                    by {{ optional($loan->creator)->name ?? 'N/A' }}

                </div>

                        </x-table.cell>

                        {{-- Member --}}
                        <x-table.cell>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $loan->member->name ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                A/C: {{ $loan->member->savingsAccount->account_number }}


                            </div>

                        </x-table.cell>

                        {{-- Type --}}
                        <x-table.cell>


                         -

                        </x-table.cell>



                        {{-- Amount --}}
                        <x-table.cell>


                            UGX {{ number_format($loan->amount) }}

                        </x-table.cell>


                        <x-table.cell>



                         


                        </x-table.cell>

                       




                    </x-table.row>
                </template>
                @endforeach
            </x-table>

            {{-- Pagination --}}
            @if($loans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $loans->links() }}
            </div>
            @endif
        </div>
        @endif
    </div>
</x-app-layout>