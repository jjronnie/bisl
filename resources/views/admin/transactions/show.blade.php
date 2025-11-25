<x-slide-form buttonIcon="eye" title="Transaction ID: {{ $transaction->reference_number }}">

     <div class="bg-white p-6 ">
    <dl class="grid grid-cols-2 md:grid-cols-4 gap-6">

        <x-transaction-detail title="Transaction ID" value="{{ $transaction->reference_number }}" />

        <x-transaction-detail title="Member" value="{{ $transaction->member->name ?? 'N/A' }}" />
        <x-transaction-detail title="Account No." value=" {{ $transaction->member->savingsAccount->account_number }}" />


        <x-transaction-detail title="Transaction Type" value="{{ ucfirst($transaction->transaction_type) }}" />


        <x-transaction-detail title="Amount" value="{{ number_format($transaction->amount, 2) }}" />

        <x-transaction-detail title="Balance Before" value="{{ number_format($transaction->balance_before, 2) }}" />

        <x-transaction-detail title="Balance After" value="{{ number_format($transaction->balance_after, 2) }}" />

        <x-transaction-detail title="Method" value="{{ $transaction->method ?? 'N/A' }}" />

        <x-transaction-detail title="Remarks" value="{{ $transaction->remarks ?? 'None' }}" />

        <x-transaction-detail title="Created At" value="{{ $transaction->created_at->format('d M Y H:i') }}" />

        <x-transaction-detail title="Created By" value="{{ $transaction->creator->name ?? 'Unknown' }}" />

    </dl>
</div>



</x-slide-form>