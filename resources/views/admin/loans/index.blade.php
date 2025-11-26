<x-app-layout>
    <x-page-title title="Loans" subtitle="Manage All loans" />

 <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- 2. Outstanding Loans (Count) --}}
<x-stat-card 
    title="Outstanding Loans " 
    value="{{ number_format($stats['total_outstanding_count']) }}" 
    icon="list-checks" 
/>

{{-- 1. Total Outstanding Principal (Amount) --}}
<x-stat-card 
    title="Outstanding Principal" 
    value="UGX {{ number_format($stats['total_outstanding_amount'], 0) }}" 
    icon="banknote" 
/>

{{-- 4. Pending Applications (Count) --}}
<x-stat-card 
    title="Pending  Applications" 
    value="{{ number_format($stats['total_pending_count']) }}" 
    icon="clipboard-list" 
/>





{{-- 3. Pending Applications Value (Amount) --}}
<x-stat-card 
    title="Pending Value" 
    value="UGX {{ number_format($stats['total_pending_amount'], 2) }}" 
    icon="clock" 
/>





{{-- 6. Completed Loans (Count) --}}
<x-stat-card 
    title="Completed Loans " 
    value="{{ number_format($stats['total_completed_count']) }}" 
    icon="archive" 
/>

{{-- 5. Completed Loans Value (Amount) --}}
<x-stat-card 
    title="Completed Loans" 
    value="UGX {{ number_format($stats['total_completed_amount'], 2) }}" 
    icon="check-circle" 
/>

{{-- 8. Rejected Applications (Count) --}}
<x-stat-card 
    title="Rejected Applications" 
    value="{{ number_format($stats['total_rejected_count']) }}" 
    icon="x-circle" 
/>


{{-- 7. Total Loans Managed (Overall Count) --}}
<x-stat-card 
    title="Total Loans" 
    value="{{ number_format($stats['total_loan_count']) }}" 
    icon="receipt" 
/>



</div>

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

                <a class="btn" href="{{ route('admin.loans.create') }}">Create New Loan</a>


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
            <x-table :headers="['Loan ID','Borrower', 'STATUS', 'Amount','RATE','DURATION' ,'DUE DATE']"
                showActions="false">
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
                            <x-status-badge :status="$loan->status" />

                        </x-table.cell>


                        {{-- Amount --}}
                        <x-table.cell>

                            UGX {{ number_format($loan->amount) }}

                        </x-table.cell>

                        <x-table.cell>

                            {{ number_format($loan->interest_rate) }}%

                        </x-table.cell>

                          <x-table.cell>
                {{ $loan->duration_months }} Months
            </x-table.cell>

                      

                        <x-table.cell>

                            @if ($loan->due_date)
                             {{ $loan->due_date->format('M d, Y')  }}
                          
                                 
                             @else
                                 
                            N/A
                                
                            @endif

                           


                        </x-table.cell>


                        <x-table.cell>

                            <a class="btn" href="{{ route('admin.loans.show', $loan) }}">
                                <i data-lucide="eye" class="w-4 h-4 "></i>
                            </a>


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