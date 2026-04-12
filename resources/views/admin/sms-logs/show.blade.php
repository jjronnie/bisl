<x-slide-form buttonIcon="eye" title="SMS Log: {{ $log->phone_number }}">

    <div class="bg-white p-6">
        <!-- Header & Details -->
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <x-transaction-detail title="Phone Number" value="{{ $log->phone_number }}" />

            <div>
                <dt class="text-gray-500 text-sm">Status:</dt>
                <dd class="text-lg font-semibold mt-1">
                    @if ($log->status === 'sent')
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Sent
                        </span>
                    @elseif($log->status === 'pending')
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i> Pending
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i> Failed
                        </span>
                    @endif
                </dd>
            </div>

            <x-transaction-detail title="Notification Type"
                value="{{ str_replace(['PaymentReceived', 'LoanStatusUpdate', 'TransactionAlert'], ['Payment', 'Loan Status', 'Transaction'], $log->notification_type) }}" />

            <x-transaction-detail title="Cost"
                value="{{ $log->cost ? 'UGX ' . number_format($log->cost, 4) : 'N/A' }}" />

            <x-transaction-detail title="Retry Count" value="{{ $log->retry_count }}" />

            <x-transaction-detail title="Log ID" value="{{ $log->id }}" />

            <x-transaction-detail title="Created At" value="{{ $log->created_at->format('d M Y H:i') }}" />

            <x-transaction-detail title="Sent At"
                value="{{ $log->sent_at ? $log->sent_at->format('d M Y H:i') : 'Pending' }}" />

            <x-transaction-detail title="Updated At" value="{{ $log->updated_at->format('d M Y H:i') }}" />
        </dl>

        <!-- Message -->
        <dl class="grid grid-cols-1 gap-6 pt-6">
            <div>
                <dt class="text-gray-500 text-sm">Message:</dt>
                <dd class="text-sm text-gray-900 mt-1 break-words whitespace-pre-wrap">{{ $log->message }}</dd>
            </div>
        </dl>

        <!-- Provider Response -->
        @if ($log->provider_response)
            @php
                $response = $log->provider_response;
                $status = $response['status'] ?? 'Unknown';
                $smsData = $response['data']['SMSMessageData'] ?? [];
                $message = $smsData['Message'] ?? 'No message';
                $recipients = $smsData['Recipients'] ?? [];
            @endphp

            <dl class="grid grid-cols-1 gap-6 pt-6">
                <div>
                    <dt class="text-gray-500 text-sm">Response Status:</dt>
                    <dd class="text-lg font-semibold mt-1">
                        <span
                            class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">
                            {{ ucfirst($status) }}
                        </span>
                    </dd>
                </div>

                <div>
                    <dt class="text-gray-500 text-sm">Response Summary:</dt>
                    <dd class="text-sm text-gray-900 mt-1 break-words">{{ $message }}</dd>
                </div>
            </dl>

            @if (!empty($recipients))
                <div class="pt-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Recipients</h3>
                    @foreach ($recipients as $recipient)
                        <dl class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-6 p-4 bg-gray-50 rounded">
                            <x-transaction-detail title="Phone" value="{{ $recipient['number'] ?? 'N/A' }}" />

                            <x-transaction-detail title="Delivery Status"
                                value="{{ $recipient['status'] ?? 'N/A' }}" />

                            <x-transaction-detail title="Status Code"
                                value="{{ $recipient['statusCode'] ?? 'N/A' }}" />

                            <x-transaction-detail title="Message ID" value="{{ $recipient['messageId'] ?? 'N/A' }}" />

                            <x-transaction-detail title="Cost" value="{{ $recipient['cost'] ?? 'N/A' }}" />
                        </dl>
                    @endforeach
                </div>
            @endif
        @endif

        <!-- Error Details -->
        @if ($log->error_message)
            <dl class="grid grid-cols-1 gap-6 pt-6">
                <div>
                    <dt class="text-gray-500 text-sm">Error Message:</dt>
                    <dd class="text-sm text-red-900 mt-1 break-words bg-red-50 p-3 rounded border border-red-200">
                        {{ $log->error_message }}
                    </dd>
                </div>
            </dl>
        @endif
    </div>

</x-slide-form>
