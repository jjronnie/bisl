<x-app-layout>
    <x-page-title title="New Transaction" subtitle="Record a deposit or withdrawal" />

    <div class="max-w-7xl mx-auto">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
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

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.transactions.store') }}"
                  enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="member_id" value="Select Member" />
                        <select id="member_id" name="member_id" required
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="" disabled selected>Select member...</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}"
                                    data-savings="{{ $member->savingsAccount?->balance ?? 0 }}"
                                    data-lpf="{{ $member->savingsAccount?->loan_protection_fund ?? 0 }}">
                                    {{ $member->name }} ({{ $member->savingsAccount?->account_number ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('member_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="transaction_type" value="Transaction Type" />
                        <select id="transaction_type" name="transaction_type" required
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="deposit" selected>Deposit</option>
                            <option value="withdrawal">Withdrawal</option>
                        </select>
                        @error('transaction_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="account" value="Account" />
                        <select id="account" name="account" required
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="savings" selected>Savings</option>
                            <option value="loan_protection_fund">Loan Protection Fund</option>
                        </select>
                        @error('account')
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

                    <div>
                        <x-input-label for="amount" value="Amount" />
                        <div class="mt-1 relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">UGX</span>
                            </div>
                            <input type="text" id="amount" name="amount" required placeholder="0"
                                class="block w-full pl-12 pr-3 py-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',')">
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
                                    <button type="button" @click="removeDocument(index)"
                                            class="text-red-600 hover:text-red-800 text-sm" x-show="documents.length > 1">
                                        Remove
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label value="Document Name" />
                                        <input type="text" x-model="doc.name"
                                               :name="'documents[' + index + '][name]'"
                                               class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                               placeholder="e.g. Payment Receipt">
                                    </div>
                                    <div>
                                        <x-input-label value="Notes (optional)" />
                                        <input type="text" x-model="doc.notes"
                                               :name="'documents[' + index + '][notes]'"
                                               class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                               placeholder="Optional notes">
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
                                        <input type="file"
                                               :name="'documents[' + index + '][file]'"
                                               class="hidden"
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
                            <button type="button" @click="addDocument()"
                                    class="btn">
                                <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                                Add Another Document
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.transactions.index') }}" class="btn-gray">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Cancel
                    </a>

                    <button type="submit" class="btn">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Save Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
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
