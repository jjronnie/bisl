<x-app-layout>
    <x-page-title title="Bulk SMS Broadcasts" subtitle="View and manage SMS Broadcasts" />

    <div class="space-y-6">
        <div class="flex justify-end">
            <a href="{{ route('admin.bulk-sms.create') }}" class="btn">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                New Broadcast
            </a>
        </div>

        @if($campaigns->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="text-gray-400 mb-4">
                    <i data-lucide="inbox" class="w-16 h-16 mx-auto"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No broadcasts yet</h3>
                <p class="text-gray-500 mb-6">Create your first bulk SMS broadcast to get started.</p>
                <a href="{{ route('admin.bulk-sms.create') }}" class="btn">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Create Broadcast
                </a>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recipients</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Failed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($campaigns as $campaign)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $campaign->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $campaign->total_recipients }}
                            </td>
                            <td class="px-6 py-4 text-sm text-green-600">
                                {{ $campaign->sent_count }}
                            </td>
                            <td class="px-6 py-4 text-sm {{ $campaign->failed_count > 0 ? 'text-red-600' : 'text-gray-500' }}">
                                {{ $campaign->failed_count }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                UGX {{ number_format($campaign->total_cost, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($campaign->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                @elseif($campaign->status === 'processing')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Processing
                                    </span>
                                @elseif($campaign->status === 'cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('admin.bulk-sms.show', $campaign->id) }}" class="text-blue-600 hover:text-blue-800">
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
