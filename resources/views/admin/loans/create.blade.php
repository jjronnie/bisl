<x-app-layout>

    <x-page-title title="Enter Loan Details"/>

    {{-- Error/Success Messages --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.loans.store') }}" class="space-y-6 bg-white rounded-lg p-6 ">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

        {{-- 1. Member --}}
        <div>
            <x-input-label for="member_id" value="Select Member" />
            <select name="member_id" id="member_id" required
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                <option value="" disabled selected>Select member...</option>

                {{-- ASSUMPTION: $members is passed to the view and Member model has name, tier, and savingsAccount relationship --}}
                @foreach ($members as $member)
                <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                    {{ $member->savingsAccount->account_number ?? 'N/A' }} - {{ $member->name }} ({{ ucfirst($member->tier) }})
                </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('member_id')" class="mt-2" />
        </div>

        {{-- 2. Principal Amount (Field name changed to 'amount' for consistency with DB/Controller) --}}
        <div>
            <x-input-label for="amount" value="Principal Loan Amount (UGX)" />

            <div class="mt-1 relative rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 text-sm sm:text-base">UGX</span>
                </div>

                <input type="number" name="amount" id="amount" min="1" required placeholder="0.00"
                    value="{{ old('amount') }}"
                    class="block w-full pl-12 pr-3 py-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
            </div>

            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
        </div>

        {{-- 3. Interest Rate (Annual Rate) --}}
        <div>
            <x-input-label for="interest_rate" value="Annual Interest Rate (%)" />
            <input type="number" step="0.01" min="12" max="24" id="interest_rate" name="interest_rate" required
                value="{{ old('interest_rate') }}"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">

            <x-input-error :messages="$errors->get('interest_rate')" class="mt-2" />
        </div>

        {{-- 4. Loan Type (Used for Priority Loan/Tier Check) --}}
        <div>
            <x-input-label for="loan_type" value="Loan Type" />
            <select id="loan_type" name="loan_type" required
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                <option value="" disabled selected>Select type...</option>
                <option value="standard" {{ old('loan_type') == 'standard' ? 'selected' : '' }}>Standard Loan</option>
                <option value="priority" {{ old('loan_type') == 'priority' ? 'selected' : '' }}>Priority Loan (Gold Tier Only)</option>
                <option value="emergency" {{ old('loan_type') == 'emergency' ? 'selected' : '' }}>Emergency Loan</option>
            </select>

            <x-input-error :messages="$errors->get('loan_type')" class="mt-2" />
        </div>

        {{-- 5. Duration (Field name changed to 'period' for consistency with Controller/Service input) --}}
        <div>
            <x-input-label for="period" value="Duration (Months)" />
            <input type="number" min="1" id="period" name="period" required
                value="{{ old('period') }}"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">

            <x-input-error :messages="$errors->get('period')" class="mt-2" />
        </div>

        {{-- Application Date (Date field is kept as it's required for calculation start) --}}
        <div>
            <x-input-label for="application_date" value="Application Date" />
            <input type="date" name="application_date" id="application_date" required
                value="{{ old('application_date', now()->toDateString()) }}"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">

            <x-input-error :messages="$errors->get('application_date')" class="mt-2" />
        </div>
        
        {{-- Removed Interest Type, Penalty Rate, Approval Date, Disbursement Date, Due Date --}}
        <div class="md:col-span-2">
            <p class="text-sm text-gray-500 italic">
                Note: **Interest Type** is fixed at **Reducing Balance** by the system logic. **Approval** and **Disbursement Dates** will be set when the loan status is updated.
            </p>
        </div>


        {{-- Purpose --}}
        <div class="md:col-span-2">
            <x-input-label for="purpose" value="Loan Purpose" />
            <input type="text" name="purpose" id="purpose" required
                value="{{ old('purpose') }}"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">

            <x-input-error :messages="$errors->get('purpose')" class="mt-2" />
        </div>

        {{-- Notes --}}
        <div class="md:col-span-2">
            <x-input-label for="notes" value="Notes (optional)" />
            <textarea name="notes" id="notes" rows="3"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">{{ old('notes') }}</textarea>

            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
        </div>

    </div>

    <div class="flex justify-end space-x-3">
        <button type="submit" class="btn bg-indigo-600 text-white hover:bg-indigo-700 p-3 rounded-lg font-semibold">
            Create Loan & Calculate Amortization
        </button>
    </div>

</form>

</x-app-layout>