<x-slide-form buttonIcon="eye" title="Transaction ID: {{ $transaction->reference_number }}">

    @if($transaction->reversals()->exists())
        <div class="p-4 bg-red-50 border-b border-red-200">
            <div class="flex items-center">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-2"></i>
                <span class="text-sm font-medium text-red-800">This transaction has been reversed</span>
            </div>
        </div>
    @endif

    <div class="bg-white p-6 ">
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-6">

            <x-transaction-detail title="Transaction ID" value="{{ $transaction->reference_number }}" />

            <x-transaction-detail title="Member" value="{{ $transaction->member->name ?? 'N/A' }}" />
            <x-transaction-detail title="Account No."
                value=" {{ $transaction->member->savingsAccount->account_number }}" />


            <x-transaction-detail title="Transaction Type" value="{{ ucfirst($transaction->transaction_type) }}" />

            <x-transaction-detail title="Account" value="{{ ucfirst($transaction->account) }}" />


            <x-transaction-detail title="Amount" value="{{ number_format($transaction->amount, 2) }}" />

            <x-transaction-detail title="Balance Before" value="{{ number_format($transaction->balance_before, 2) }}" />

            <x-transaction-detail title="Balance After" value="{{ number_format($transaction->balance_after, 2) }}" />

            <x-transaction-detail title="Method" value="{{ $transaction->method ?? 'N/A' }}" />


            <x-transaction-detail title="Created At" value="{{ $transaction->created_at->format('d M Y H:i') }}" />

            <x-transaction-detail title="Transacted By" value="{{ $transaction->createdBy->name ?? 'Unknown' }}" />



        </dl>

        <dl class="grid grid-cols-1  gap-6 pt-6">
            <x-transaction-detail title="Remarks" value="{{ $transaction->remarks ?? 'None' }}" />

        </dl>
    </div>

    @if($transaction->reversals()->exists())
        <div class="border-t p-6 bg-red-50">
            <h3 class="text-lg font-medium mb-4 text-red-800">Reversal Details</h3>
            @foreach ($transaction->reversals as $reversal)
                <dl class="grid grid-cols-2 gap-4">
                    <x-transaction-detail title="Reversed By" value="{{ $reversal->reversedBy->name ?? 'Unknown' }}" />
                    <x-transaction-detail title="Reversed At"
                        value="{{ $reversal->created_at->format('d M Y H:i') }}" />
                    <x-transaction-detail title="Reason" value="{{ $reversal->reason }}" class="col-span-2" />
                </dl>
            @endforeach
        </div>
    @endif


</x-slide-form>
