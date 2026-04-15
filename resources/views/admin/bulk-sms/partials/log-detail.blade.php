<x-slide-form buttonIcon="eye" title="SMS Log: {{ $log->phone_number }}">

    <div class="bg-white p-6">
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <dt class="text-gray-500 text-sm">Phone Number:</dt>
                <dd class="text-sm font-medium text-gray-900 mt-1">{{ $log->phone_number }}</dd>
            </div>

            <div>
                <dt class="text-gray-500 text-sm">System Status:</dt>
                <dd class="mt-1">
                    @if ($log->status === 'sent')
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Sent
                        </span>
                    @elseif($log->status === 'pending')
                        <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i> Pending
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i> Failed
                        </span>
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-gray-500 text-sm">Provider Status:</dt>
                <dd class="mt-1">
                    @php
                        $providerCode = $log->provider_status_code;
                        $providerLabel = $log->getProviderStatusLabel();
                        $isSuccess = $log->isProviderSuccess();
                        $isFailed = $log->isProviderFailed();
                    @endphp
                    @if($providerCode)
                        @if($isSuccess)
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> {{ $providerCode }} - {{ $providerLabel }}
                            </span>
                        @elseif($isFailed)
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">
                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> {{ $providerCode }} - {{ $providerLabel }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-800">
                                {{ $providerCode }} - {{ $providerLabel }}
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-500">
                            Pending
                        </span>
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-gray-500 text-sm">Cost:</dt>
                <dd class="text-sm font-medium text-gray-900 mt-1">{{ $log->cost ? 'UGX ' . number_format($log->cost, 4) : 'N/A' }}</dd>
            </div>

            <div>
                <dt class="text-gray-500 text-sm">Retry Count:</dt>
                <dd class="text-sm font-medium text-gray-900 mt-1">{{ $log->retry_count ?? 0 }}</dd>
            </div>

            <div>
                <dt class="text-gray-500 text-sm">Sent At:</dt>
                <dd class="text-sm font-medium text-gray-900 mt-1">{{ $log->sent_at ? $log->sent_at->format('d M Y H:i') : 'Pending' }}</dd>
            </div>
        </dl>

        <dl class="grid grid-cols-1 gap-6 pt-6">
            <div>
                <dt class="text-gray-500 text-sm">Message:</dt>
                <dd class="text-sm text-gray-900 mt-1 break-words whitespace-pre-wrap">{{ $log->message }}</dd>
            </div>
        </dl>

        @if ($log->error_message)
            <dl class="grid grid-cols-1 gap-6 pt-6">
                <div>
                    <dt class="text-gray-500 text-sm">Error Details:</dt>
                    <dd class="text-sm text-red-900 mt-1 break-words bg-red-50 p-3 rounded border border-red-200">
                        {{ $log->error_message }}
                    </dd>
                </div>
            </dl>
        @endif

        @if ($log->provider_response)
            @php
                $response = $log->provider_response;
                $smsData = $response['data']['SMSMessageData'] ?? [];
                $recipients = $smsData['Recipients'] ?? [];
            @endphp

            <div class="pt-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Raw Provider Response</h3>
                
                @if (!empty($recipients))
                    @foreach ($recipients as $recipient)
                        <div class="p-4 bg-gray-50 rounded-lg mb-4">
                            <dl class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div>
                                    <dt class="text-gray-500 text-xs">Phone</dt>
                                    <dd class="text-sm font-medium text-gray-900 mt-1">{{ $recipient['number'] ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 text-xs">Message ID</dt>
                                    <dd class="text-sm font-medium text-gray-900 mt-1">{{ $recipient['messageId'] ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 text-xs">Cost</dt>
                                    <dd class="text-sm font-medium text-gray-900 mt-1">{{ $recipient['cost'] ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
    </div>

</x-slide-form>
