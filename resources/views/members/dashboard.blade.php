<x-app-layout>


@include('members.greeting')



    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-4 mb-8">
        <!-- card -->
        <x-stat-card title="Loan Protection Fund" value="UGX {{ number_format($loanProtection) }}" icon="shield" />
        <x-stat-card title=" Accessible Balance" value="UGX {{ number_format($accessible) }}" icon="dollar-sign" />
        <x-stat-card title="Interest Earned" value="{{ $member->savingsAccount->interest_earned }}" icon="percent" />
        {{-- <x-stat-card title="Outstanding Loan" value="" icon="clipboard-clock" /> --}}

    </div>


    <x-page-title title="Recent Transactions" />

    @if($transactions->isEmpty())
    <x-empty-state icon="receipt" message="No transactions found." />
    @else

    <x-table :headers="['Date','Type',   'Amount','Action' ]">
        @foreach($transactions as $transaction)
        <x-table.row>
           

            {{-- Date --}}
            <x-table.cell>
                    {{ $transaction->created_at->format('d M Y H:i') }}
             
               
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

    @endif



</x-app-layout>