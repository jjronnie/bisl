<x-slide-form button-text="Record New Transaction" title="Enter Transaction Details">

    <form method="POST" action="{{ route('admin.transactions.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

            {{-- Member --}}
            <div>
                <x-input-label for="member_id" value="Select Member" />

                <select name="member_id" id="member_id" required
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                    <option value="" disabled selected>Select member...</option>

                    @foreach ($members as $member)
                    <option value="{{ $member->id }}" {{ old('member_id')==$member->id ? 'selected' : '' }}>
                        {{ $member->savingsAccount->account_number }} - {{ $member->name }}
                    </option>
                    @endforeach
                </select>

                <x-input-error :messages="$errors->get('member_id')" class="mt-2" />
            </div>


            {{-- Transaction Type --}}
            <div>
                <x-input-label for="transaction_type" value="Transaction Type" />
                <select id="transaction_type" name="transaction_type" required
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                    <option value="" disabled selected>Select type...</option>
                    <option value="deposit" {{ old('transaction_type')=='deposit' ? 'selected' : '' }}>Deposit</option>
                    <option value="withdrawal" {{ old('transaction_type')=='withdrawal' ? 'selected' : '' }}>Withdrawal
                    </option>

                </select>
                <x-input-error :messages="$errors->get('transaction_type')" class="mt-2" />
            </div>


            {{-- Method --}}
            <div>
                <x-input-label for="method" value="Payment Method" />

                <select name="method" id="method" required
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                    <option value="" disabled selected>Select method...</option>
                    <option value="mobile_money" {{ old('method')=='mobile_money' ? 'selected' : '' }}>Mobile Money
                    </option>
                    <option value="cash" {{ old('method')=='cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank" {{ old('method')=='bank' ? 'selected' : '' }}>Bank</option>
                </select>

                <x-input-error :messages="$errors->get('method')" class="mt-2" />
            </div>


         


            {{-- Amount --}}
            <div>
                <x-input-label for="amount" value="Amount" />
                <div class="mt-1 relative rounded-lg shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 text-sm sm:text-base">UGX</span>
                    </div>

                    <input type="number" name="amount" min="1" required placeholder="0.00" value="{{ old('amount') }}"
                        class="block w-full pl-12 pr-3 py-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base" />
                </div>
                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
            </div>


            {{-- Remarks --}}
            <div class="md:col-span-2">
                <x-input-label for="remarks" value="Remarks (optional)" />
                <textarea name="remarks" id="remarks" rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">{{ old('remarks') }}</textarea>
                <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
            </div>

        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            <x-confirmation-checkbox />
            <button type="submit" class="btn">
                Save <i data-lucide="save" class="w-4 h-4 ml-2"></i>
            </button>
        </div>

    </form>






</x-slide-form>