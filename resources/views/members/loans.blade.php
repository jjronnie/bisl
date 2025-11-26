<x-app-layout>
    <x-page-title title="Loans" />

  

    @if($loans->isEmpty())
    <x-empty-state icon="receipt" message="No loans found." />
    @else

      <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        <!-- card -->
        <x-stat-card title="Total Loans" value="{{ $loanCount ?? '-' }}" icon="receipt" />
        <x-stat-card title="Pending" value="{{ $pendingCount ?? '-' }}" icon="clock" />
        <x-stat-card title="Completed" value="{{ $completedCount ?? '-' }}" icon="check-circle" />
        <x-stat-card title="Rejected" value="{{ $rejectedCount ?? '-' }}" icon="x-circle" />

    </div>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <x-table :headers="['#','Loan ID', 'STATUS', 'Amount','RATE','DURATION' ,'DUE DATE']" showActions="false">
            @foreach($loans as $index => $loan)

            <x-table.row>

                <x-table.cell>
                    {{ $index +1 }}
                </x-table.cell>

                <x-table.cell>
                    {{ $loan->loan_number }}
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
                    {{ $loan->due_date->format('M d, Y') }}


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


</x-app-layout>