<x-app-layout>


<x-page-title title="Transaction History"/>


    @if($transactions->isEmpty())
    <x-empty-state icon="receipt" message="No transactions found." />
    @else

    <x-table :headers="['Transaction ID','Date','Type', 'Method',  'Amount', 'Balance ','Action' ]">
        @foreach($transactions as $transaction)
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
                    by {{ optional($transaction->creator)->name ?? 'N/A' }}

                </div>

            </x-table.cell>



            {{-- Type --}}
            <x-table.cell>
                <x-status-badge :status="ucfirst($transaction->transaction_type)" />
            </x-table.cell>

            <x-table.cell>
               {{ ucfirst($transaction->method) }}
            </x-table.cell>
            {{-- Amount --}}
            <x-table.cell>
                UGX {{ number_format($transaction->amount) }}
            </x-table.cell>

            <x-table.cell>
                <div class="text-sm text-gray-900">
                  Before:  UGX{{ $transaction->balance_before }}
                </div>

                     <div class="text-sm text-gray-900">
                  After: UGX{{ $transaction->balance_after }}
                </div>
              

            </x-table.cell>


            <x-table.cell>



                @include('admin.transactions.show')



            </x-table.cell>
        </x-table.row>
        @endforeach
    </x-table>

    @endif





</x-app-layout>