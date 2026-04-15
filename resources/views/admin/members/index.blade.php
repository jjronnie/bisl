<x-app-layout>
    <x-page-title title="Group Members" />

    <div class="space-y-8">

        <!-- Controls -->
        <div class="bg-white rounded-lg shadow p-4">
            <form method="GET" action="{{ route('admin.members.index') }}" class="flex flex-col lg:flex-row lg:items-center gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="Search by name, phone, or account number">
                    </div>
                </div>

                <div class="w-full lg:w-48">
                    <select name="tier" onchange="this.form.submit()"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">All Tiers</option>
                        <option value="silver" {{ request('tier') === 'silver' ? 'selected' : '' }}>Silver</option>
                        <option value="gold" {{ request('tier') === 'gold' ? 'selected' : '' }}>Gold</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn">
                        <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                        Search
                    </button>
                    @if(request()->has('search') || request()->has('tier'))
                        <a href="{{ route('admin.members.index') }}" class="btn-gray">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="flex gap-3">
            <a class="btn" href="{{ route('admin.members.create') }}">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add Member
            </a>


        </div>

        @if ($members->isEmpty())
            <x-empty-state icon="users" message="No members found." />
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($members as $index => $member)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $members->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $member->savingsAccount->account_number ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($member->avatar)
                                                <img src="{{ asset('storage/' . $member->avatar) }}" alt="{{ $member->name }}"
                                                    class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <img src="{{ asset('default-avatar.png') }}" alt="{{ $member->name }}"
                                                    class="w-10 h-10 rounded-full object-cover">
                                            @endif

                                            <div class="leading-tight">
                                                <span class="font-medium text-gray-900">
                                                    {{ ucfirst($member->name) }}
                                                </span>
                                                <span class="block text-sm text-gray-500">
                                                    {{ $member->user->email }}<br>
                                                    {{ $member->phone1 ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        UGX {{ number_format($member->savingsAccount->balance ?? 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$member->user->status" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$member->tier" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <a class="btn" href="{{ route('admin.members.show', $member->id) }}">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <a class="btn" href="{{ route('admin.members.edit', $member->id) }}">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                            <a class="btn" href="{{ route('admin.members.transactions.index', $member->id) }}">
                                                <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($members->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-white rounded-lg shadow">
                    {{ $members->withQueryString()->links() }}
                </div>
            @endif
        @endif

    </div>
</x-app-layout>
