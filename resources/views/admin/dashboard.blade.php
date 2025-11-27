<x-app-layout>

    @php
    $hour = now()->hour;
    if ($hour < 12) {
        $greeting = 'Good morning';
    } elseif ($hour < 17) {
        $greeting = 'Good afternoon';
    } else {
        $greeting = 'Good evening';
    }
@endphp

<x-page-title title="{{ $greeting }}, {{ auth()->user()->name }}" />


    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        <!-- card -->
        <x-stat-card title="Total Members" value="{{ $totalMembers }}" icon="users" />
        <x-stat-card title="Operational Account" value="UGX {{ number_format($saccoAccount ->operational) }}" icon="coins" />
        <x-stat-card title="Savings Account" value="UGX {{ number_format($saccoAccount ->member_savings) }}" icon="coins" />
        <x-stat-card title="Accumulated Interest" value="UGX {{ number_format($saccoAccount ->member_interest) }}" icon="coins" />
        <x-stat-card title="Interest on Loans" value="UGX {{ number_format($saccoAccount ->loan_interest) }}" icon="coins" />
        <x-stat-card title="Loan Protection Fund" value="UGX {{ number_format($saccoAccount ->loan_protection_fund) }}" icon="shield" />
        {{-- <x-stat-card title="Outstanding Loans" value="{{ $totalOutstandingLoans ?? '-' }}" icon="clipboard-clock" />       
        <x-stat-card title="Loan Portfolio" value="UGX {{ number_format($totalOutstandingAmount) }}" icon="wallet" /> --}}

    </div>

    <x-page-title title="Recent Transactions" />

    @if($transactions->isEmpty())
    <x-empty-state icon="receipt" message="No transactions found." />
    @else

    <x-table :headers="['Transaction ID','Date', 'Member', 'Type',  'Amount','Action' ]" >
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
                    by {{ optional($transaction->createdBy)->name ?? 'N/A' }}

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

   

    @endif





    

  

         








</x-app-layout>