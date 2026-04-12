<x-app-layout>
    <x-page-title title="SMS Notification Settings" />

    <div class="grid gap-6 max-w-2xl">
        <!-- App-Wide SMS Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Global SMS Configuration</h2>
            <p class="text-sm text-gray-600 mb-6">Configure which SMS notifications are sent to all members system-wide.
            </p>

            <form method="POST" action="{{ route('admin.sms-settings.update') }}">
                @csrf
                @method('PATCH')

                <div class="space-y-4">
                    <label
                        class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="payment_notifications_enabled" value="1"
                            {{ $smsSettings->payment_notifications_enabled ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" />
                        <div class="ml-3">
                            <span class="font-medium text-gray-900">Payment Notifications</span>
                            <p class="text-xs text-gray-500 mt-1">Send SMS when payment is received on a loan</p>
                        </div>
                    </label>

                    <label
                        class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="loan_status_notifications_enabled" value="1"
                            {{ $smsSettings->loan_status_notifications_enabled ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" />
                        <div class="ml-3">
                            <span class="font-medium text-gray-900">Loan Status Updates</span>
                            <p class="text-xs text-gray-500 mt-1">Send SMS when loan status changes (approved, rejected,
                                disbursed)</p>
                        </div>
                    </label>

                    <label
                        class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="transaction_alerts_enabled" value="1"
                            {{ $smsSettings->transaction_alerts_enabled ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" />
                        <div class="ml-3">
                            <span class="font-medium text-gray-900">Transaction Alerts</span>
                            <p class="text-xs text-gray-500 mt-1">Send SMS for all transaction activity (deposits,
                                withdrawals, etc.)</p>
                        </div>
                    </label>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Save Settings
                    </button>
                </div>
            </form>

            @if (session('success'))
                <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded text-green-800 text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
