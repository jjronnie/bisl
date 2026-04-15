<x-app-layout>
    <x-page-title title="Transaction Reversals" />

    <div class="space-y-6">
       <div class="bg-white rounded-lg shadow p-4">
    <form method="GET"
        action="{{ route('admin.transactions.reversal.index') }}"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">

        <!-- Year -->
        <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
            <select name="year"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">All Years</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Month -->
        <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
            <select name="month"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">All Months</option>
                @foreach(range(1, 12) as $month)
                    <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Search -->
        <div class="lg:col-span-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                placeholder="Transaction ID or member name">
        </div>

        <!-- Buttons -->
        <div class="lg:col-span-3 flex flex-col sm:flex-row gap-2">
            <button type="submit" class="btn w-full sm:w-auto justify-center">
                Search
            </button>

            @if(request()->has('search') || request()->has('year') || request()->has('month'))
                <a href="{{ route('admin.transactions.reversal.index') }}"
                    class="btn-gray w-full sm:w-auto justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Clear
                </a>
            @endif
        </div>

    </form>
</div>

@role('superadmin')
<div class="flex justify-end mt-4">
    <a href="{{ route('admin.transactions.reversal.create') }}" class="btn w-full sm:w-auto justify-center">
        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
        New Reversal
    </a>
</div>
@endrole

        @if ($reversals->isEmpty())
            <x-empty-state icon="rotate-ccw" message="No reversals found." />
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reversed By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($reversals as $reversal)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $reversal->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $reversal->transaction->reference_number ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="font-medium text-gray-900">
                                            {{ $reversal->transaction->member->name ?? 'N/A' }}
                                        </span>
                                        <br>
                                        <span class="text-gray-500 text-xs">
                                            {{ $reversal->transaction->member->savingsAccount->account_number ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst($reversal->account ?? 'N/A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                        UGX {{ number_format($reversal->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $reversal->reversedBy->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $reversal->reason }}">
                                        {{ $reversal->reason }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($reversals->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-white rounded-lg shadow">
                    {{ $reversals->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
