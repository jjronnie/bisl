<x-app-layout>

    <x-page-title title="Enter Loan Details" />


    <form method="POST" action="{{ route('admin.loans.store') }}" class="space-y-6 bg-white rounded-lg p-6 ">
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



            {{-- Principal Amount --}}
            <div>
                <x-input-label for="principal_amount" value="Principal Amount" />

                <div class="mt-1 relative rounded-lg shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 text-sm sm:text-base">UGX</span>
                    </div>

                    <input type="number" name="principal_amount" min="1" required placeholder="0.00"
                        value="{{ old('principal_amount') }}"
                        class="block w-full pl-12 pr-3 py-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                </div>

                <x-input-error :messages="$errors->get('principal_amount')" class="mt-2" />
            </div>

            {{-- Interest Rate --}}
            <div>
                <x-input-label for="interest_rate" value="Interest Rate (%)" />
                <input type="number" step="0.01" min="0" id="interest_rate" name="interest_rate" required
                    value="{{ old('interest_rate') }}"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">

                <x-input-error :messages="$errors->get('interest_rate')" class="mt-2" />
            </div>

            {{-- Interest Type --}}
            <div>
                <x-input-label for="interest_type" value="Interest Type" />

                <select id="interest_type" name="interest_type" required
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                    <option value="" disabled>Select type...</option>
                    <option value="reducing_balance" selected {{ old('interest_type')=='reducing_balance' ? 'selected'
                        : '' }}>Reducing Balance</option>
                    <option value="flat" {{ old('interest_type')=='flat' ? 'selected' : '' }}>Flat</option>

                </select>

                <x-input-error :messages="$errors->get('interest_type')" class="mt-2" />
            </div>

            {{-- Duration Months --}}
            <div>
                <x-input-label for="duration_months" value="Duration (Months)" />
                <input type="number" min="1" id="duration_months" name="duration_months" required
                    value="{{ old('duration_months') }}"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">

                <x-input-error :messages="$errors->get('duration_months')" class="mt-2" />
            </div>

            {{-- Penalty Rate --}}
            <div>
                <x-input-label for="penalty_rate" value="Penalty Rate (%) (optional)" />
                <input type="number" min="0" step="0.01" id="penalty_rate" name="penalty_rate"
                    value="{{ old('penalty_rate') }}"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">

                <x-input-error :messages="$errors->get('penalty_rate')" class="mt-2" />
            </div>

            {{-- Purpose --}}
            <div class="md:col-span-2">
                <x-input-label for="purpose" value="Loan Purpose (optional)" />
                <input type="text" name="purpose" id="purpose" value="{{ old('purpose') }}"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">

                <x-input-error :messages="$errors->get('purpose')" class="mt-2" />
            </div>

            {{-- Application Date --}}
            <div>
                <x-input-label for="application_date" value="Application Date" />
                <input type="date" name="application_date" id="application_date" required
                    value="{{ old('application_date', now()->toDateString()) }}"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">

                <x-input-error :messages="$errors->get('application_date')" class="mt-2" />
            </div>

            {{-- Approval Date --}}
            <div>
                <x-input-label for="approval_date" value="Approval Date (optional)" />
                <input type="date" name="approval_date" id="approval_date" value="{{ old('approval_date') }}"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">

                <x-input-error :messages="$errors->get('approval_date')" class="mt-2" />
            </div>

            {{-- Disbursement Date --}}
            <div>
                <x-input-label for="disbursement_date" value="Disbursement Date (optional)" />
                <input type="date" name="disbursement_date" id="disbursement_date"
                    value="{{ old('disbursement_date') }}"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">

                <x-input-error :messages="$errors->get('disbursement_date')" class="mt-2" />
            </div>

            {{-- Due Date --}}
            <div>
                <x-input-label for="due_date" value="Due Date (optional)" />
                <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">

                <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
            </div>

            {{-- Notes --}}
            <div class="md:col-span-2">
                <x-input-label for="notes" value="Notes (optional)" />
                <textarea name="notes" id="notes" rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">{{ old('notes') }}</textarea>

                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
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



</x-app-layout>