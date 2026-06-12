<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Payroll Dashboard
            </h2>
        </div>
    </x-slot>

    {{-- Period Selector --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <form method="GET" class="flex items-center gap-3">
            <label class="text-sm font-medium text-gray-700">Select Period:</label>

            <select name="period_id" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm text-sm">
                @foreach ($periods as $p)
                    <option value="{{ $p->id }}" {{ $selectedPeriod?->id === $p->id ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $p->month, 1)) }} {{ $p->year }}
                        ({{ ucfirst($p->status) }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-6 gap-4">
        <x-stat-card title="Active Employees" value="{{ $periodStats['totalActiveEmployees'] }}" icon="users"
            sub_title="Employees in selected period" />

        <x-stat-card title="Total Gross" value="UGX {{ number_format($periodStats['totalGross'], 0) }}" icon="wallet"
            sub_title="Gross salary in period" />

        <x-stat-card title="Total Savings" value="UGX {{ number_format($periodStats['totalSavings'], 0) }}"
            icon="piggy-bank" sub_title="Deductions via payroll" />

        <x-stat-card title="Take Home" value="UGX {{ number_format($periodStats['totalNet'], 0) }}" icon="check-circle"
            sub_title="Net salary paid" />
    </div>

    {{-- Main Content --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        @if ($selectedPeriod)

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="py-3 px-4 text-left font-medium text-gray-600">Employee</th>
                            <th class="py-3 px-4 text-left font-medium text-gray-600">Days Worked</th>
                            <th class="py-3 px-4 text-right font-medium text-gray-600">Gross</th>
                            <th class="py-3 px-4 text-right font-medium text-gray-600">Savings</th>
                            <th class="py-3 px-4 text-right font-medium text-gray-600">NSSF</th>
                            <th class="py-3 px-4 text-right font-medium text-gray-600">Tax</th>
                            <th class="py-3 px-4 text-right font-medium text-gray-600">Net</th>
                            <th class="py-3 px-4 text-center font-medium text-gray-600"></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($employees as $emp)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium">
                                    {{ $emp['name'] }}
                                    <div class="text-xs text-gray-500">
                                        Emp #{{ $emp['employee_number'] }}
                                    </div>
                                </td>

                                <td class="py-3 px-4">
                                    {{ $emp['days_worked'] }}
                                </td>

                                <td class="py-3 px-4 text-right font-mono">
                                    {{ number_format($emp['gross_salary'], 0) }}
                                </td>

                                <td class="py-3 px-4 text-right font-mono text-amber-600">
                                    {{ number_format($emp['savings_contribution'], 0) }}
                                </td>

                                <td class="py-3 px-4 text-right font-mono">
                                    {{ number_format($emp['nssf_employee'], 0) }}
                                </td>

                                <td class="py-3 px-4 text-right font-mono">
                                    {{ number_format($emp['paye'], 0) }}
                                </td>

                                <td class="py-3 px-4 text-right font-mono font-semibold">
                                    {{ number_format($emp['net_salary'], 0) }}
                                </td>

                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('admin.payroll.profiles.show', ['profile' => $emp['id']]) }}"
                                        class="btn" title="View Profile">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-500">
                                    No employees in this period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <i data-lucide="user-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                <p class="text-lg font-medium">Select a period</p>
                <p class="text-sm">
                    Choose a payroll period from the dropdown to view employee data.
                </p>
            </div>

        @endif

    </div>
</x-app-layout>
