<x-app-layout>
    <x-page-title title="Interest Ledgers" />

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-4">
            <form method="GET" action="{{ route('admin.interest.ledger') }}" class="flex flex-col lg:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                    <select name="member_id"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">All Members</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>
                                {{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full lg:w-32">
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

                <div class="w-full lg:w-32">
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

                <div class="flex gap-2">
                    <button type="submit" class="btn">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        Filter
                    </button>
                    @if(request()->has('member_id') || request()->has('year') || request()->has('month'))
                        <a href="{{ route('admin.interest.ledger') }}" class="btn-gray">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if($lastUpdated)
        <div class="text-sm text-gray-500">
            <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
            Last updated: {{ $lastUpdated->format('d M Y H:i') }}
        </div>
        @endif

        @if ($ledgers->isEmpty())
            <x-empty-state message="No interest ledger records found." />
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account No.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Interest Earned</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($ledgers as $ledger)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $ledger->member->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $ledger->member->savingsAccount->account_number ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-green-600 font-medium">
                                        UGX {{ number_format($ledger->interest_amount ?? 0) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ledger->tier === 'gold' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($ledger->tier ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $ledger->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($ledgers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-white rounded-lg shadow">
                    {{ $ledgers->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
