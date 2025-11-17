<x-app-layout>

    <x-slot name="header">
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
    </x-slot>
    
    <x-page-title title="Add New Transaction" />

    <div x-data="{
        selectedMemberId: '{{ old('member_id') }}' || null,
        selectedMemberName: '{{ old('member_id') ? addslashes(\App\Models\Member::find(old('member_id'))?->name ?? '...') : '...' }}',
        selectedMemberAccount: '{{ old('member_id') ? \App\Models\Member::find(old('member_id'))?->savingsAccount?->account_number ?? '...' : '...' }}',
        transactionType: '{{ old('transaction_type', '') }}',

        selectMember(event) {
            if (!event.target.value) {
                this.selectedMemberId = null;
                this.selectedMemberName = '...';
                this.selectedMemberAccount = '...';
                return;
            }
            let selectedOption = event.target.options[event.target.selectedIndex];
            this.selectedMemberId = event.target.value;
            this.selectedMemberName = selectedOption.getAttribute('data-member-name');
            this.selectedMemberAccount = selectedOption.getAttribute('data-member-account');
        },
        
        initTomSelect() {
            let tomselect = new TomSelect(this.$refs.memberSelect, {
                create: false,
                sortField: {
                    field: 'text',
                    direction: 'asc'
                },
                placeholder: 'Search by name, ID, or account...'
            });
            
            // If validation failed, ensure the form is visible on page load
            if (this.selectedMemberId) {
                @if(old('member_id'))
                    @php
                        $oldMember = $members->firstWhere('id', old('member_id'));
                    @endphp
                    @if($oldMember)
                        this.selectedMemberName = '{{ addslashes($oldMember->name) }}';
                        this.selectedMemberAccount = '{{ $oldMember->savingsAccount?->account_number ?? 'N/A' }}';
                    @endif
                @endif
            }
        }
    }" x-init="initTomSelect()">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    <strong class="font-bold">Whoops! Something went wrong.</strong>
                    <ul class="mt-3 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Step 1: Select Member
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Search for a member to post a transaction.
                        </p>
                    </header>

                    <div class="mt-6">
                        <x-input-label for="member_select" value="Search Member" />
                        <select id="member_select" 
                                x-ref="memberSelect" 
                                @change="selectMember($event)"
                                class="mt-1 block w-full"
                        >
                            <option value="">Select a member...</option>
                            @foreach($members as $member)
                                <option 
                                    value="{{ $member->id }}"
                                    data-member-name="{{ addslashes($member->name) }}"
                                    data-member-account="{{ $member->savingsAccount?->account_number ?? 'N/A' }}"
                                    data-search-string="{{ $member->name }} {{ $member->sacco_member_id }} {{ $member->savingsAccount?->account_number }}"
                                    {{ old('member_id') == $member->id ? 'selected' : '' }}
                                >
                                    {{ $member->name }} A/C {{ $member->savingsAccount?->account_number }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('member_id')" class="mt-2" />
                    </div>
                </section>
            </div>
            <div x-show="selectedMemberId" 
                 style="display: none;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Step 2: Post Transaction for <span x-text="selectedMemberName" class="text-indigo-600"></span>
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Account Number: <span x-text="selectedMemberAccount" class="font-mono font-semibold"></span>
                        </p>
                    </header>

                    <form method="post" action="{{ route('admin.transactions.store') }}" class="mt-6 space-y-6">
                        @csrf
                        
                        <input type="hidden" name="member_id" :value="selectedMemberId">

                        <div>
                            <x-input-label for="transaction_type" value="Transaction Type" />
                            <select id="transaction_type" name="transaction_type" x-model="transactionType"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select a type...</option>
                                <option value="deposit" @if(old('transaction_type') == 'deposit') selected @endif>Deposit</option>
                                <option value="withdrawal" @if(old('transaction_type') == 'withdrawal') selected @endif>Withdrawal</option>
                                <option value="loan_disbursement" @if(old('transaction_type') == 'loan_disbursement') selected @endif>Loan Disbursement</option>
                                <option value="loan_repayment" @if(old('transaction_type') == 'loan_repayment') selected @endif>Loan Repayment</option>
                                <option value="fee" @if(old('transaction_type') == 'fee') selected @endif>Fee</option>
                                <option value="other" @if(old('transaction_type') == 'other') selected @endif>Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('transaction_type')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="amount" value="Amount (UGX)" />
                            <x-text-input id="amount" name="amount" type="number" 
                                          class="mt-1 block w-full" 
                                          :value="old('amount')" 
                                          placeholder="e.g., 50000" step="0.01" />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="transaction_date" value="Transaction Date & Time" />
                                <x-text-input id="transaction_date" name="transaction_date" type="datetime-local" 
                                              class="mt-1 block w-full" 
                                              :value="old('transaction_date', now()->format('Y-m-d\TH:i'))" />
                                <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="method" value="Method (e.g., Cash, Bank, Mobile Money)" />
                                <x-text-input id="method" name="method" type="text" 
                                              class="mt-1 block w-full" 
                                              :value="old('method')" 
                                              placeholder="Cash" />
                                <x-input-error :messages="$errors->get('method')" class="mt-2" />
                            </div>
                        </div>

                        <div x-show="transactionType === 'loan_disbursement' || transactionType === 'loan_repayment'" 
                             x-transition
                             class="space-y-1">
                            <x-input-label for="loan_id" value="Associated Loan ID" />
                            <x-text-input id="loan_id" name="loan_id" type="number" 
                                          class="mt-1 block w-full" 
                                          :value="old('loan_id')" 
                                          placeholder="Enter the Loan ID" />
                            <x-input-error :messages="$errors->get('loan_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" value="Description" />
                            <x-text-input id="description" name="description" type="text" 
                                          class="mt-1 block w-full" 
                                          :value="old('description')" 
                                          placeholder="e.g., Monthly Savings Deposit, Loan Repayment for Jan" />
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="remarks" value="Remarks (Internal Note - Optional)" />
                            <textarea id="remarks" name="remarks" rows="3"
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                      placeholder="e.g., Cleared by manager, partial payment...">{{ old('remarks') }}</textarea>
                            <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>
                                Post Transaction
                            </x-primary-button>
                        </div>
                    </form>
                </section>
            </div>
            </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    
</x-app-layout>