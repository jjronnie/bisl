<x-app-layout>


    <x-page-title title="Transaction History" />


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