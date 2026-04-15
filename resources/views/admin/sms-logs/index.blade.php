<x-app-layout>
    <x-page-title title="SMS Logs" />

    <div class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <x-stat-card title="Total SMS" value="{{ $stats['total'] }}" icon="message-square" sub_title="All time" />

            <x-stat-card title="Sent" value="{{ $stats['sent'] }}" icon="check-circle" sub_title="Successfully sent" />

            <x-stat-card title="Pending" value="{{ $stats['pending'] }}" icon="clock" sub_title="Waiting to send" />

            <x-stat-card title="Failed" value="{{ $stats['failed'] }}" icon="alert-circle"
                sub_title="Delivery failed" />

            <x-stat-card title="Delivery Rate" value="{{ $stats['delivery_rate'] }}%" icon="trending-up"
                sub_title="Successfully delivered" />

            <x-stat-card title="Total Cost" value="UGX {{ number_format($stats['total_cost'], 2) }}" icon="wallet"
                sub_title="All time" />


        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" x-data class="flex gap-4 flex-wrap items-end">
                <div class="flex-1 min-w-xs">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" placeholder="Phone number or message..."
                        value="{{ request('search') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" @change="$el.form.submit()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="notification_type" @change="$el.form.submit()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="PaymentReceived"
                            {{ request('notification_type') === 'PaymentReceived' ? 'selected' : '' }}>Payment</option>
                        <option value="LoanStatusUpdate"
                            {{ request('notification_type') === 'LoanStatusUpdate' ? 'selected' : '' }}>Loan Status
                        </option>
                        <option value="TransactionAlert"
                            {{ request('notification_type') === 'TransactionAlert' ? 'selected' : '' }}>Transaction
                        </option>
                    </select>
                </div>
            </form>
        </div>

        <!-- SMS Logs Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">System</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent At</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($logs as $log)
                            @php
                                $providerSuccess = $log->isProviderSuccess();
                                $providerFailed = $log->isProviderFailed();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $log->phone_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-medium">
                                        {{ str_replace(['PaymentReceived', 'LoanStatusUpdate', 'TransactionAlert', 'bulk_sms'], ['Payment', 'Loan Status', 'Transaction', 'Bulk SMS'], $log->notification_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if ($log->status === 'sent')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i data-lucide="check" class="w-3 h-3 mr-1"></i> Sent
                                        </span>
                                    @elseif($log->status === 'pending')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i data-lucide="clock" class="w-3 h-3 mr-1"></i> Pending
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i data-lucide="x" class="w-3 h-3 mr-1"></i> Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($log->provider_status_code)
                                        @if($providerSuccess)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" title="Code: {{ $log->provider_status_code }}">
                                                {{ $log->getProviderStatusLabel() }}
                                            </span>
                                        @elseif($providerFailed)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" title="Code: {{ $log->provider_status_code }}">
                                                {{ $log->getProviderStatusLabel() }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" title="Code: {{ $log->provider_status_code }}">
                                                {{ $log->getProviderStatusLabel() }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if ($log->cost)
                                        UGX {{ number_format($log->cost, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $log->sent_at ? $log->sent_at->format('M d, Y H:i') : '—' }}
                                </td>
                                <td class="">

                                    @include('admin.sms-logs.show')

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-400"></i>
                                    <p>No SMS logs found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($logs->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
