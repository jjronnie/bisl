<x-app-layout>
    <x-page-title title="Salary History" subtitle="View all payroll salary disbursements" />

    <div class="mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        <form method="GET" class="mb-4 flex flex-wrap gap-2 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Period</label>
                <select name="payroll_period_id" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">All Periods</option>
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" {{ request('payroll_period_id') == $period->id ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $period->month, 1)) }} {{ $period->year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Employee</label>
                <select name="profile_id" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">All Employees</option>
                    @foreach ($profiles as $profile)
                        <option value="{{ $profile->id }}" {{ request('profile_id') == $profile->id ? 'selected' : '' }}>
                            {{ $profile->member?->name ?? "Profile #{$profile->id}" }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Dispatched</option>
                </select>
            </div>
            <x-primary-button>Filter</x-primary-button>
            @if (request()->anyFilled(['payroll_period_id', 'profile_id', 'status']))
                <a href="{{ route('admin.payroll.runs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Clear</a>
            @endif
        </form>

        <x-table :headers="['Employee', 'Period', 'Days', 'Gross', 'Net', 'Take Home', 'Status', ]" :showActions="true">
            @foreach ($runs as $run)
                <x-table.row>
                    <x-table.cell>{{ $run->payrollProfile?->member?->name ?? 'N/A' }}</x-table.cell>
                    <x-table.cell>{{ $run->payrollPeriod?->month }}/{{ $run->payrollPeriod?->year }}</x-table.cell>
                    <x-table.cell>{{ $run->days_worked }}</x-table.cell>
                    <x-table.cell>UGX {{ number_format($run->gross_salary, 0) }}</x-table.cell>
                    <x-table.cell>UGX {{ number_format($run->net_salary, 0) }}</x-table.cell>
                    <x-table.cell class="font-semibold">UGX {{ number_format($run->final_take_home, 0) }}</x-table.cell>
                    <x-table.cell>
                        <x-status-badge :status="$run->status === 'completed' ? 'active' : 'pending'">
                            {{ $run->status === 'completed' ? 'Dispatched' : 'Draft' }}
                        </x-status-badge>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex space-x-2">
                            <a class="btn" href="{{ route('admin.payroll.runs.show', $run->id) }}">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @endforeach
        </x-table>

        <div class="mt-4">
            {{ $runs->links() }}
        </div>
    </div>
</x-app-layout>
