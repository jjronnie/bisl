<x-slide-form buttonIcon="eye" title="    {{ $loan->loan_number }}">

    <div class="bg-white p-6 ">
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-6">

            <x-transaction-detail title="Loan ID" value="{{ $loan->loan_number }}" />
            <x-transaction-detail title="Loan ID" value="{{ucfirst( $loan->status) }}" />



            <x-transaction-detail title="Amount" value="UGX {{ number_format($loan->amount, 0) }}" />

            <x-transaction-detail title="Rate (Annual)" value="{{ $loan->interest_rate }} %" />

            <x-transaction-detail title="Duration" value="{{ $loan->duration_months }} Months" />

            <x-transaction-detail title="Type" value="{{ ucfirst($loan->loan_type) }}" />

            <x-transaction-detail title="Application Date" value="{{ $loan->application_date->format('Y-m-d') }}" />

            <x-transaction-detail title="Approval Date" value="{{ $loan->approval_date?->format('Y-m-d') ?? 'N/A' }}" />

            <x-transaction-detail title="Disbursement Date"
                value="{{ $loan->disbursement_date?->format('Y-m-d') ?? 'N/A' }}" />

            <x-transaction-detail title="Maturity Date"
                value="{{ $loan->due_date?->format('Y-m-d') ?? 'Calculated on Disbursement' }}" />

            <x-transaction-detail class="col-span-full" title="Purpose" value="{{ $loan->purpose }}" />

            <x-transaction-detail class="col-span-full" title="Notes" value="{{ $loan->notes ?? 'None' }}" />
        </dl>
    </div>






    {{-- Amortization Schedule --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold mb-4">Amortization Schedule (Reducing Balance)</h3>

        <x-table :headers="[
        '#', 
        'Due Date', 
        'Starting Balance', 
        'Monthly Installment', 
        'Principal', 
        'Interest', 
        'Penalty', 
        'Ending Balance', 
        'Payment Status'
    ]">

            @forelse ($loan->installments as $installment)
            <x-table.row
                class="@if($installment->status === 'paid') bg-green-50 @elseif($installment->status === 'defaulted') bg-red-50 @endif">
                <x-table.cell>{{ $installment->installment_number }}</x-table.cell>
                <x-table.cell>{{ $installment->due_date->format('Y-m-d') }}</x-table.cell>
                <x-table.cell>UGX {{ number_format($installment->starting_balance, 2) }}</x-table.cell>
                <x-table.cell>UGX {{ number_format($installment->total_amount, 2) }}</x-table.cell>
                <x-table.cell>UGX {{ number_format($installment->principal_amount, 2) }}</x-table.cell>
                <x-table.cell>UGX {{ number_format($installment->interest_amount, 2) }}</x-table.cell>
                <x-table.cell>UGX {{ number_format($installment->penalty_amount, 2) }}</x-table.cell>
                <x-table.cell>UGX {{ number_format($installment->ending_balance, 2) }}</x-table.cell>
                <x-table.cell>
                    <span class="p-1 rounded text-xs font-semibold 
                        @if($installment->status === 'paid') bg-green-200 text-green-800
                        @elseif($installment->status === 'defaulted') bg-red-200 text-red-800
                        @else bg-yellow-200 text-yellow-800
                        @endif
                    ">
                        {{ ucfirst($installment->status) }}
                    </span>
                </x-table.cell>
            </x-table.row>
            @empty
            <x-table.row>
                <x-table.cell colspan="9" class="text-center">No installment schedule found.</x-table.cell>
            </x-table.row>
            @endforelse
        </x-table>
    </div>


</x-slide-form>