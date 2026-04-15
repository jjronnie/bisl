<x-app-layout>
    <x-page-title title="Bulk SMS Campaign #{{ $campaign->id }}" subtitle="Campaign details and delivery status" />

    <div x-data="{
        status: '{{ $campaign->status }}',
        sentCount: {{ $campaign->sent_count }},
        failedCount: {{ $campaign->failed_count }},
        totalCost: {{ $campaign->total_cost }},
        pendingCount: {{ $campaign->total_recipients - $campaign->sent_count - $campaign->failed_count }},
        totalRecipients: {{ $campaign->total_recipients }},
        hideProgress: {{ $campaign->status === 'completed' || $campaign->status === 'cancelled' ? 'true' : 'false' }},
        showSuccess: {{ $campaign->status === 'completed' ? 'true' : 'false' }},
        pollStatus() {
            fetch('/admin/bulk-sms/{{ $campaign->id }}/status')
                .then(res => res.json())
                .then(data => {
                    this.status = data.status;
                    this.sentCount = data.sent_count;
                    this.failedCount = data.failed_count;
                    this.totalCost = data.total_cost;
                    this.pendingCount = data.pending_count;
                    this.totalRecipients = data.total_recipients;

                    if (data.status === 'completed') {
                        this.hideProgress = true;
                        this.showSuccess = true;
                    } else if (data.status === 'cancelled') {
                        this.hideProgress = true;
                    } else {
                        this.showSuccess = false;
                        setTimeout(() => this.pollStatus(), 2000);
                    }
                });
        }
    }" x-init="setTimeout(() => pollStatus(), 1000)" class="space-y-6">

        <div x-show="showSuccess" x-transition class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-800 font-medium">Campaign completed!</p>
                <p class="text-xs text-green-700 mt-1" x-text="sentCount + ' sent, ' + failedCount + ' failed'"></p>
            </div>
        </div>

        @if (session('info'))
            <div x-show="!hideProgress && !showSuccess" x-transition class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start">
                <div class="flex-shrink-0">
                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75 fill-current text-blue-600" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-800">{{ session('info') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium">Campaign Summary</h3>
                <span
                    :class="{
                        'bg-green-100 text-green-800': status === 'completed',
                        'bg-blue-100 text-blue-800': status === 'processing',
                        'bg-gray-100 text-gray-800': status === 'cancelled',
                        'bg-yellow-100 text-yellow-800': status === 'pending'
                    }"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                    <span x-show="status === 'processing' || status === 'pending'" class="flex items-center">
                        <svg class="animate-spin h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="status === 'pending' ? 'Queued' : 'Processing'"></span>
                    </span>
                    <span x-show="status === 'completed' || status === 'cancelled'"
                        x-text="status.charAt(0).toUpperCase() + status.slice(1)"></span>
                </span>
            </div>

            <dl class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500">Total</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900" x-text="totalRecipients">
                        {{ $campaign->total_recipients }}</dd>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <dt class="text-sm font-medium text-green-600">Sent</dt>
                    <dd class="mt-1 text-3xl font-semibold text-green-700" x-text="sentCount">
                        {{ $campaign->sent_count }}</dd>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <dt class="text-sm font-medium text-red-600">Failed</dt>
                    <dd class="mt-1 text-3xl font-semibold text-red-700" x-text="failedCount">
                        {{ $campaign->failed_count }}</dd>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <dt class="text-sm font-medium text-yellow-600">Pending</dt>
                    <dd class="mt-1 text-3xl font-semibold text-yellow-700" x-text="pendingCount">
                        {{ $campaign->total_recipients - $campaign->sent_count - $campaign->failed_count }}</dd>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <dt class="text-sm font-medium text-blue-600">Progress</dt>
                    <dd class="mt-1 text-3xl font-semibold text-blue-700"
                        x-text="sentCount + failedCount + '/' + totalRecipients">
                        {{ $campaign->sent_count + $campaign->failed_count }}/{{ $campaign->total_recipients }}
                    </dd>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <dt class="text-sm font-medium text-purple-600">Cost</dt>
                    <dd class="mt-1 text-3xl font-semibold text-purple-700">
                        UGX <span x-text="Number(totalCost).toFixed(2)">{{ number_format($campaign->total_cost, 2) }}</span>
                    </dd>
                </div>
            </dl>

            <div class="mb-4" x-show="!hideProgress">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                    <span>Progress</span>
                    <span x-text="sentCount + failedCount + ' of ' + totalRecipients + ' completed'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-300"
                        :style="'width: ' + Math.round(((sentCount + failedCount) / totalRecipients) * 100) + '%'"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Created:</span>
                    <span class="ml-2 text-gray-900">{{ $campaign->created_at->format('d M Y H:i') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">By:</span>
                    <span class="ml-2 text-gray-900">{{ $campaign->creator->name ?? 'Unknown' }}</span>
                </div>
                @if ($campaign->completed_at)
                    <div>
                        <span class="text-gray-500">Completed:</span>
                        <span class="ml-2 text-gray-900">{{ $campaign->completed_at->format('d M Y H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium mb-4">Message</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-800 whitespace-pre-wrap">{{ $campaign->message }}</p>
            </div>
            <p class="text-sm text-gray-500 mt-2">
                {{ strlen($campaign->message) }} characters |
                <span x-text="sentCount + failedCount"></span> SMS sent
            </p>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium">Recipients</h3>
                <div class="flex items-center gap-3">
                    <button x-show="status === 'completed'" @click="window.location.reload()" class="btn">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                        Refresh Table
                    </button>
                    @if ($campaign->canResend())
                        <form method="POST" action="{{ route('admin.bulk-sms.resend', $campaign->id) }}">
                            @csrf
                            <button type="submit" class="btn">
                                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                                Resend Failed
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">System</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent At</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($logs as $log)
                            @php
                                $providerSuccess = $log->isProviderSuccess();
                                $providerFailed = $log->isProviderFailed();
                            @endphp
                            <tr class="{{ $providerFailed ? 'bg-red-50' : ($providerSuccess ? 'bg-green-50' : '') }}">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @if ($log->recipient)
                                        {{ $log->recipient->name ?? 'N/A' }}
                                    @else
                                        <span class="text-gray-400">Unknown</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $log->phone_number }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($log->status === 'sent')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Sent
                                        </span>
                                    @elseif($log->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->provider_status_code)
                                        @if($providerSuccess)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" title="{{ $log->provider_status_code }}">
                                                {{ $log->getProviderStatusLabel() }}
                                            </span>
                                        @elseif($providerFailed)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" title="{{ $log->provider_status_code }}">
                                                {{ $log->getProviderStatusLabel() }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" title="{{ $log->provider_status_code }}">
                                                {{ $log->getProviderStatusLabel() }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $log->cost ? 'UGX ' . number_format($log->cost, 2) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $log->sent_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    @include('admin.bulk-sms.partials.log-detail')
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    No recipient logs found yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.bulk-sms.index') }}" class="btn-gray">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Campaigns
            </a>

            @if (auth()->user()->hasRole('superadmin'))
                <x-confirm-modal action="{{ route('admin.bulk-sms.destroy', $campaign->id) }}"
                    buttonText="Delete Campaign"
                    warning="Are you sure you want to delete this campaign? This action cannot be undone."
                    triggerText="Delete Campaign" trigger-icon="trash-2"
                    trigger-class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
                    method="DELETE" />
            @endif
        </div>
    </div>
</x-app-layout>
