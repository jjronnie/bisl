<x-app-layout>
    <x-page-title title="New Transaction" subtitle="Record a deposit or withdrawal" />

    <div class="max-w-4xl mx-auto" x-data="transactionForm()">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Step Indicator --}}
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4">
                <div class="flex items-center">
                    <div :class="step === 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'" class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</div>
                    <span :class="step === 1 ? 'text-indigo-600 font-semibold' : 'text-gray-500'" class="ml-2 text-sm">Member & Account</span>
                </div>
                <div class="w-12 h-px" :class="step === 2 ? 'bg-indigo-600' : 'bg-gray-300'"></div>
                <div class="flex items-center">
                    <div :class="step === 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'" class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                    <span :class="step === 2 ? 'text-indigo-600 font-semibold' : 'text-gray-500'" class="ml-2 text-sm">Amount & Details</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.transactions.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Step 1: Member & Account Selection --}}
            <div x-show="step === 1" class="bg-white rounded-lg shadow p-6 space-y-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2">Select Member</h2>

                <div>
                    <x-input-label for="member_id" value="Member" />
                    <select id="member_id" name="member_id" required x-model="selectedMemberId" @change="onMemberChange()"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="" disabled selected>Choose a member...</option>
                        @foreach ($members as $member)
                            <option value="{{ $member['id'] }}">
                                {{ $member['name'] }} ({{ $member['account_number'] ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <template x-if="selectedMember">
                    <div>
                        <h3 class="text-md font-semibold text-gray-700 mb-3">Select Account</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <template x-for="acct in selectedMember.accounts" :key="acct.key">
                                <div @click="selectAccount(acct)"
                                    :class="selectedAccount?.key === acct.key ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-50' : 'hover:border-gray-300'"
                                    class="border-2 rounded-xl p-4 cursor-pointer transition-all">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-gray-700" x-text="acct.label"></span>
                                        <template x-if="acct.key === 'savings'">
                                            <i data-lucide="coins" class="w-5 h-5 text-yellow-500"></i>
                                        </template>
                                        <template x-if="acct.key === 'loan_protection_fund'">
                                            <i data-lucide="shield" class="w-5 h-5 text-green-500"></i>
                                        </template>
                                        <template x-if="acct.key === 'salary'">
                                            <i data-lucide="wallet" class="w-5 h-5 text-blue-500"></i>
                                        </template>
                                    </div>
                                    <p class="text-lg font-bold text-gray-900 mb-1">UGX <span x-text="numberFormat(acct.balance)"></span></p>
                                    <p class="text-xs" x-text="acct.description"></p>
                                    <div class="mt-2 flex gap-1">
                                        <template x-for="type in acct.allowed_types" :key="type">
                                            <span class="inline-block px-2 py-0.5 text-xs rounded-full font-medium"
                                                :class="type === 'deposit' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'"
                                                x-text="type"></span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end pt-4 border-t">
                    <button type="button" @click="goToStep2()" :disabled="!canProceed"
                        class="btn"
                        :class="!canProceed ? 'opacity-50 cursor-not-allowed' : ''">
                        Next: Amount & Details
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-2 inline"></i>
                    </button>
                </div>
            </div>

            {{-- Step 2: Amount & Details --}}
            <div x-show="step === 2" class="bg-white rounded-lg shadow p-6 space-y-6">
                <div class="flex items-center justify-between border-b pb-2">
                    <h2 class="text-lg font-bold text-gray-800">Transaction Details</h2>
                    <button type="button" @click="step = 1" class="text-sm text-indigo-600 hover:text-indigo-800">Change Member/Account</button>
                </div>

                {{-- Selected Account Summary --}}
                <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <template x-if="selectedAccount?.key === 'savings'">
                            <i data-lucide="coins" class="w-6 h-6 text-yellow-500"></i>
                        </template>
                        <template x-if="selectedAccount?.key === 'loan_protection_fund'">
                            <i data-lucide="shield" class="w-6 h-6 text-green-500"></i>
                        </template>
                        <template x-if="selectedAccount?.key === 'salary'">
                            <i data-lucide="wallet" class="w-6 h-6 text-blue-500"></i>
                        </template>
                        <div>
                            <p class="font-semibold text-gray-900">
                                <span x-text="selectedMember?.name"></span> —
                                <span x-text="selectedAccount?.label"></span>
                            </p>
                            <p class="text-sm text-gray-500">Current balance: UGX <span x-text="numberFormat(selectedAccount?.balance || 0)"></span></p>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="member_id" x-model="selectedMemberId">
                <input type="hidden" name="account" x-model="selectedAccountKey">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="transaction_type" value="Transaction Type" />
                        <div class="mt-1 flex gap-3">
                            <template x-for="type in selectedAccount?.allowed_types || []" :key="type">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="transaction_type" :value="type" x-model="transactionType" class="sr-only peer">
                                    <div class="text-center px-4 py-3 border-2 rounded-lg peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-all"
                                        :class="transactionType === type ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                        <span class="font-medium text-gray-800" x-text="type === 'deposit' ? 'Deposit' : 'Withdrawal'"></span>
                                    </div>
                                </label>
                            </template>
                        </div>
                        @error('transaction_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="method" value="Payment Method" />
                        <select id="method" name="method" required
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="cash">Cash</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="bank">Bank</option>
                        </select>
                        @error('method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="amount" value="Amount (UGX)" />
                        <div class="mt-1">
                            <input type="text" id="amount" name="amount" required placeholder="0"
                                x-model="amountDisplay"
                                @input="onAmountInput($event)"
                                class="block w-full px-4 py-3 text-2xl font-bold text-right border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Balance Preview --}}
                        <div class="mt-3 bg-gray-50 rounded-lg p-3 space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Current Balance:</span>
                                <span class="font-medium">UGX <span x-text="numberFormat(selectedAccount?.balance || 0)"></span></span>
                            </div>
                            <div class="flex justify-between" x-show="parsedAmount > 0">
                                <span class="text-gray-600" x-text="transactionType === 'deposit' ? 'Amount to add:' : 'Amount to subtract:'"></span>
                                <span class="font-medium" :class="transactionType === 'deposit' ? 'text-green-600' : 'text-red-600'">- UGX <span x-text="numberFormat(parsedAmount)"></span></span>
                            </div>
                            <div class="flex justify-between border-t pt-1" x-show="parsedAmount > 0">
                                <span class="text-gray-800 font-semibold">Balance After:</span>
                                <span :class="balanceAfter >= 0 ? 'text-green-700 font-bold' : 'text-red-700 font-bold'">
                                    UGX <span x-text="numberFormat(balanceAfter)"></span>
                                </span>
                            </div>
                            <p x-show="insufficient" class="text-red-600 text-xs mt-1">Insufficient balance for this withdrawal.</p>
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <x-input-label for="remarks" value="Remarks (optional)" />
                    <textarea id="remarks" name="remarks" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Add any notes about this transaction...">{{ old('remarks') }}</textarea>
                </div>

                {{-- Document Upload --}}
                <div class="border-t pt-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <i data-lucide="paperclip" class="w-5 h-5"></i>
                        Attach Documents
                    </h3>

                    <div x-data="createDocsUpload()" class="space-y-4">
                        <template x-for="(doc, index) in documents" :key="index">
                            <div class="border rounded-lg p-4 space-y-3">
                                <div class="flex items-start justify-between">
                                    <span class="text-sm font-medium text-gray-700" x-text="'Document ' + (index + 1)"></span>
                                    <button type="button" @click="removeDocument(index)" class="text-red-600 hover:text-red-800 text-sm" x-show="documents.length > 1">Remove</button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label value="Document Name" />
                                        <input type="text" x-model="doc.name" :name="'documents[' + index + '][name]'"
                                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" placeholder="e.g. Payment Receipt">
                                    </div>
                                    <div>
                                        <x-input-label value="Notes (optional)" />
                                        <input type="text" x-model="doc.notes" :name="'documents[' + index + '][notes]'"
                                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" placeholder="Optional notes">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
                                    <label class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
                                        :class="doc.file ? 'border-green-400 bg-green-50' : ''">
                                        <template x-if="!doc.file">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <i data-lucide="upload" class="w-8 h-8 mb-1"></i>
                                                <span class="text-sm font-medium">Click to upload</span>
                                                <span class="text-xs text-gray-400 mt-1">PDF, Word, Excel, CSV, or Image (max 15MB)</span>
                                            </div>
                                        </template>
                                        <template x-if="doc.file">
                                            <div class="flex flex-col items-center justify-center text-green-600">
                                                <i data-lucide="check-circle" class="w-8 h-8 mb-1"></i>
                                                <span class="text-sm font-medium" x-text="doc.file.name"></span>
                                                <span class="text-xs text-gray-400 mt-1" x-text="(doc.file.size / 1024 / 1024).toFixed(2) + ' MB'"></span>
                                            </div>
                                        </template>
                                        <input type="file" :name="'documents[' + index + '][file]'" class="hidden"
                                            accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.jpg,.jpeg,.png,.gif,.webp,.bmp"
                                            @change="handleFileUpload($event, index)">
                                    </label>
                                    <template x-if="doc.error">
                                        <p class="text-red-500 text-sm mt-1" x-text="doc.error"></p>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div class="flex gap-2">
                            <button type="button" @click="addDocument()" class="btn">
                                <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                                Add Another Document
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <button type="button" @click="step = 1" class="btn-gray">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Back
                    </button>

                    <button type="submit" class="btn" :disabled="insufficient || parsedAmount <= 0"
                        :class="(insufficient || parsedAmount <= 0) ? 'opacity-50 cursor-not-allowed' : ''">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Save Transaction
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
function transactionForm() {
    return {
        step: 1,
        members: @json($members),
        selectedMemberId: '',
        selectedMember: null,
        selectedAccount: null,
        transactionType: '',
        amountDisplay: '',
        parsedAmount: 0,

        get canProceed() {
            return this.selectedMember && this.selectedAccount;
        },

        get selectedAccountKey() {
            return this.selectedAccount?.key || '';
        },

        get balanceAfter() {
            if (!this.selectedAccount || this.parsedAmount <= 0) return this.selectedAccount?.balance || 0;
            if (this.transactionType === 'deposit') return (this.selectedAccount.balance || 0) + this.parsedAmount;
            return (this.selectedAccount.balance || 0) - this.parsedAmount;
        },

        get insufficient() {
            if (this.transactionType !== 'withdrawal' || this.parsedAmount <= 0) return false;
            return this.parsedAmount > (this.selectedAccount?.balance || 0);
        },

        onMemberChange() {
            this.selectedAccount = null;
            this.transactionType = '';
            this.amountDisplay = '';
            this.parsedAmount = 0;
            this.selectedMember = this.members.find(m => m.id == this.selectedMemberId) || null;
        },

        selectAccount(acct) {
            this.selectedAccount = acct;
            this.transactionType = acct.allowed_types[0] || '';
            this.amountDisplay = '';
            this.parsedAmount = 0;
        },

        goToStep2() {
            if (!this.canProceed) return;
            this.step = 2;
            setTimeout(() => {
                const icons = document.querySelectorAll('[data-lucide]');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }, 50);
        },

        onAmountInput(event) {
            const raw = event.target.value.replace(/[^0-9]/g, '');
            this.parsedAmount = parseInt(raw) || 0;
            this.amountDisplay = this.parsedAmount ? this.parsedAmount.toLocaleString() : '';
        },

        numberFormat(num) {
            return Number(num || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }
    }
}

function createDocsUpload() {
    return {
        documents: [
            { name: '', notes: '', file: null, error: null }
        ],
        maxFileSize: 15 * 1024 * 1024,

        addDocument() {
            this.documents.push({ name: '', notes: '', file: null, error: null });
        },

        removeDocument(index) {
            this.documents.splice(index, 1);
        },

        handleFileUpload(event, index) {
            const file = event.target.files[0];
            if (!file) return;
            this.documents[index].error = null;
            if (file.size > this.maxFileSize) {
                this.documents[index].error = 'File size exceeds 15MB!';
                event.target.value = '';
                return;
            }
            this.documents[index].file = file;
        }
    }
}
</script>
