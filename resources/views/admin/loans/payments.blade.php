<x-app-layout>
<x-page-title title="Log Payment for Loan #{{ $loan->loan_number }}"/>

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Payment Details</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-6">
        <p><strong>Member:</strong> {{ $loan->member->name }} </p>
        <p><strong>Loan Amount:</strong> UGX {{ number_format($loan->amount, 2) }}</p>
        <p><strong>Current Status:</strong> <span class="text-green-600 font-bold">{{ ucfirst($loan->status) }}</span></p>
        @if($installment)
            <p><strong>Next Installment Due:</strong> #{{ $installment->installment_number }}</p>
            <p><strong>Due Date:</strong> {{ $installment->due_date->format('Y-m-d') }}</p>
            <p class="text-lg font-bold">
                <strong>Amount Due :</strong> 
                <span class="text-indigo-700">UGX {{ number_format($amountDue, 2) }}</span>
            </p>
        @else
            <p class="col-span-full text-green-600 font-bold">No outstanding installments found. The loan is fully paid off.</p>
        @endif
    </div>

    @if($installment)
        <form method="POST" action="{{ route('admin.payments.store') }}" class="space-y-6">
            @csrf
            
            {{-- Hidden Loan ID --}}
            <input type="hidden" name="loan_id" value="{{ $loan->id }}">

            {{-- Payment Amount --}}
            <div>
                <x-input-label for="payment_amount" value="Installment Amount " />
                
                <div class="mt-1 relative rounded-lg shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 text-sm sm:text-base">UGX</span>
                    </div>
                    
                    <input type="number" name="payment_amount" id="payment_amount" min="1" step="0.01" required readonly
                        placeholder="{{ number_format($amountDue, 2, '.', '') }}"
                        value="{{ old('payment_amount', $amountDue) }}"
                        class="block w-full pl-12 pr-3 py-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-lg">
                </div>

                <p class="mt-2 text-sm text-gray-500">Should be the exact amount received from the member.</p>
                <x-input-error :messages="$errors->get('payment_amount')" class="mt-2" />
            </div>
            
            {{-- Additional Payment Fields (Optional but Recommended) --}}
            {{-- In a production app, you would log how the payment was made --}}
            <div>
                <x-input-label for="payment_method" value="Payment Method" />
                <select name="payment_method" id="payment_method" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="mobile_money">Mobile Money</option>
                </select>
            </div>

          


            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.loans.show', $loan->id) }}" class="btn-gray">
                    Cancel
                </a>
                <button type="submit" class="btn">
                    Confirm Payment
                </button>
            </div>

        </form>
    @endif
</div>


</x-app-layout>