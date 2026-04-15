<x-app-layout>
    <x-page-title title="New Reversal" subtitle="Reverse a deposit transaction" />

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.transactions.reversal.index') }}" class="btn-gray">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Reversals
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium mb-4">Find Transaction</h3>
            <form method="GET" action="{{ route('admin.transactions.reversal.find') }}" class="max-w-md">
                <div class="space-y-4">
                    <div>
                        <x-input-label for="transaction_id" value="Transaction ID" />
                        <div class="mt-1">
                            <input type="text" inputmode="numeric" pattern="[0-9]*" name="transaction_id" id="transaction_id"
                                value="{{ $searchQuery ?? '' }}"
                                class="block w-full border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter transaction ID number only (e.g., 00001)" />
                        </div>
                        <x-input-error :messages="$errors->get('transaction_id')" class="mt-2" />
                    </div>
                    <button type="submit" class="btn">
                        Find Transaction <i data-lucide="search" class="w-4 h-4 ml-2"></i>
                    </button>
                </div>
            </form>
        </div>

        @if(isset($transaction) && $transaction && !$errors->has('transaction_id'))
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium">Transaction Details</h3>
                    @if($eligibility['eligible'])
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                            Eligible for Reversal
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                            Not Eligible
                        </span>
                    @endif
                </div>

                <dl class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Transaction ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $transaction->reference_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Member</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $transaction->member->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Account No.</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $transaction->member->savingsAccount->account_number ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Transaction Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($transaction->transaction_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Account</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($transaction->account) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                        <dd class="mt-1 text-sm font-bold text-gray-900">UGX {{ number_format($transaction->amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Balance Before</dt>
                        <dd class="mt-1 text-sm text-gray-900">UGX {{ number_format($transaction->balance_before, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Balance After</dt>
                        <dd class="mt-1 text-sm text-gray-900">UGX {{ number_format($transaction->balance_after, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $transaction->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Age</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $transaction->created_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </div>

            @if(!$eligibility['eligible'])
                <div class="border-t p-6 bg-red-50">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i data-lucide="alert-triangle" class="h-5 w-5 text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Reversal Not Available</h3>
                            <p class="mt-1 text-sm text-red-700">{{ $eligibility['reason'] }}</p>
                        </div>
                    </div>
                </div>
            @elseif($transaction->reversals->isNotEmpty())
                <div class="border-t p-6 bg-red-50">
                    <h3 class="text-lg font-medium mb-4 text-red-800">Already Reversed</h3>
                    @foreach ($transaction->reversals as $reversal)
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Reversed By</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $reversal->reversedBy->name ?? 'Unknown' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Reversed At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $reversal->created_at->format('d M Y H:i') }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Reason</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $reversal->reason }}</dd>
                            </div>
                        </dl>
                    @endforeach
                </div>
            @else
                <div class="border-t p-6" x-data="{ showConfirm: false, reason: '', loading: false }">
                    <h3 class="text-lg font-medium mb-4">Reverse This Transaction</h3>
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="reason" value="Reason for Reversal" />
                            <textarea x-model="reason" id="reason" rows="3" required minlength="10"
                                class="mt-1 block w-full border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Provide a detailed reason for reversing this transaction..."></textarea>
                        </div>
                        <button type="button" @click="if (reason.length >= 10) showConfirm = true" :disabled="reason.length < 10"
                            class="btn bg-red-600 hover:bg-red-700 text-white disabled:opacity-50 disabled:cursor-not-allowed">
                            Reverse Transaction <i data-lucide="rotate-ccw" class="w-4 h-4 ml-2"></i>
                        </button>
                    </div>

                    <div x-show="showConfirm" x-cloak
                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">

                        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden" @click.away="showConfirm = false">
                            <div class="p-6">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                        <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Confirm Reversal</h3>
                                </div>

                                <p class="text-sm text-gray-600 mb-4">
                                    Are you sure you want to reverse this transaction? This action cannot be undone.
                                </p>

                                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                                    <p class="text-xs text-gray-500 mb-1">Transaction Details:</p>
                                    <p class="text-sm font-medium">Amount: <span class="text-red-600">UGX {{ number_format($transaction->amount) }}</span></p>
                                    <p class="text-sm text-gray-600 mt-1">Reason: <span class="italic">"<span x-text="reason"></span>"</span></p>
                                </div>

                                <form method="POST" action="{{ route('admin.transactions.reversal.process', $transaction->id) }}" @submit="loading = true">
                                    @csrf
                                    <input type="hidden" name="reason" :value="reason">
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" @click="showConfirm = false"
                                            class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300">
                                            Cancel
                                        </button>
                                        <button type="submit" :disabled="loading"
                                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50">
                                            <span x-show="!loading">Confirm Reversal</span>
                                            <span x-show="loading">Processing...</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @endif
    </div>
</x-app-layout>
