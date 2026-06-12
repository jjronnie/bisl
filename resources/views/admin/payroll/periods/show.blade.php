<x-app-layout>
    <x-page-title
        title="Payroll Period: {{ date('F', mktime(0, 0, 0, $period->month, 1)) }} {{ $period->year }}"
        subtitle="Status: {{ ucfirst($period->status) }}"
    />

    <div class="grid grid-cols-1 gap-6 mt-6">

        {{-- Summary Cards --}}
        @if ($period->payrollRuns->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-stat-card title="Employees" value="{{ $period->payrollRuns->count() }}" icon="users" sub_title="Total staff in this period" />
                <x-stat-card title="Gross Salary" value="UGX {{ number_format($totalGross) }}" icon="dollar-sign" sub_title="Before deductions" />
                <x-stat-card title="Take Home" value="UGX {{ number_format($totalTakeHome) }}" icon="check-circle" sub_title="After all deductions" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 -mt-4">
                <x-stat-card title="PAYE Tax" value="UGX {{ number_format($totalPaye) }}" icon="landmark" sub_title="Total tax deducted" />
                <x-stat-card title="Savings" value="UGX {{ number_format($totalSavings) }}" icon="piggy-bank" sub_title="Total savings deducted" />
                <x-stat-card title="Deductions" value="UGX {{ number_format($totalDeductions) }}" icon="arrow-down" sub_title="PAYE + NSSF + LST" />
            </div>
        @endif

        {{-- Actions --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-800">Payroll Runs</h2>

                <div class="flex flex-wrap gap-2">
                    @if ($period->status === 'draft')
                        @if ($hasDraftRuns)
                            <x-confirm-modal
                                :action="route('admin.payroll.periods.regenerate', ['period' => $period->id])"
                                warning="Regenerate payroll to update calculations for all employees. This will overwrite existing draft runs with latest data (attendance, new employees, etc.)."
                                triggerText="Regenerate"
                                triggerIcon="refresh-cw"
                                triggerClass="btn btn-amber"
                                buttonText="Regenerate"
                                method="POST"
                            />
                            <x-confirm-modal
                                :action="route('admin.payroll.periods.dispatch', ['period' => $period->id])"
                                warning="This will dispatch salaries to all employees. Once queued, please refresh the page after a few moments to see results. This action cannot be undone."
                                triggerText="Dispatch Salaries"
                                triggerIcon="send"
                                triggerClass="btn btn-green"
                                buttonText="Queue Dispatch"
                                method="POST"
                            />
                        @else
                            <x-confirm-modal
                                :action="route('admin.payroll.periods.generate', ['period' => $period->id])"
                                warning="Generate payroll for all active employees? Attendance must be recorded for all employees before generating."
                                triggerText="Generate Payroll"
                                triggerIcon="zap"
                                triggerClass="btn btn-indigo"
                                buttonText="Generate"
                                method="POST"
                            />
                        @endif
                        <a href="{{ route('admin.payroll.attendance.index', ['payroll_period_id' => $period->id]) }}" class="btn-gray text-sm">
                            <i data-lucide="calendar" class="w-4 h-4 mr-1"></i>
                            Edit Attendance
                        </a>
                    @endif
                </div>
            </div>

            {{-- Runs Table --}}
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2 px-3 font-medium text-gray-600">Employee</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Days</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Gross</th>
                            <th class="py-2 px-3 font-medium text-gray-600">PAYE</th>
                            <th class="py-2 px-3 font-medium text-gray-600">NSSF</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Net</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Savings</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Advance</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Take Home</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Status</th>
                            <th class="py-2 px-3 font-medium text-gray-600"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($period->payrollRuns as $run)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-3">{{ $run->payrollProfile?->member?->name ?? 'N/A' }}</td>
                                <td class="py-2 px-3">{{ $run->days_worked }}</td>
                                <td class="py-2 px-3">UGX {{ number_format($run->gross_salary, 0) }}</td>
                                <td class="py-2 px-3">UGX {{ number_format($run->paye, 0) }}</td>
                                <td class="py-2 px-3">UGX {{ number_format($run->nssf_employee, 0) }}</td>
                                <td class="py-2 px-3">UGX {{ number_format($run->net_salary, 0) }}</td>
                                <td class="py-2 px-3">UGX {{ number_format($run->savings_contribution, 0) }}</td>
                                <td class="py-2 px-3">UGX {{ number_format($run->advance_amount, 0) }}</td>
                                <td class="py-2 px-3 font-semibold">UGX {{ number_format($run->final_take_home, 0) }}</td>
                                <td class="py-2 px-3">
                                    <x-status-badge :status="$run->status === 'completed' ? 'active' : 'pending'">
                                        {{ $run->status === 'completed' ? 'Dispatched' : 'Draft' }}
                                    </x-status-badge>
                                </td>
                                <td class="py-2 px-3">
                                    <x-slide-form buttonText="" buttonIcon="eye">
                                        <x-slot name="title">{{ $run->payrollProfile?->member?->name ?? 'Employee' }} — Salary Breakdown</x-slot>
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm">
                                                <thead>
                                                    <tr class="border-b bg-gray-50">
                                                        <th class="py-2 px-3 text-left font-medium text-gray-600">Item</th>
                                                        <th class="py-2 px-3 text-right font-medium text-gray-600">Amount (UGX)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="border-b bg-blue-50">
                                                        <td class="py-2 px-3 font-semibold text-blue-800" colspan="2">EARNINGS</td>
                                                    </tr>
                                                    <tr class="border-b">
                                                        <td class="py-2 px-3 text-gray-600">Basic Salary ({{ $run->days_worked }} days @ {{ number_format($run->daily_rate, 0) }}/day)</td>
                                                        <td class="py-2 px-3 text-right font-mono">{{ number_format($run->basic_salary_earned, 0) }}</td>
                                                    </tr>
                                                    @if ($run->qualification_allowance > 0)
                                                    <tr class="border-b">
                                                        <td class="py-2 px-3 text-gray-600">Qualification Allowance</td>
                                                        <td class="py-2 px-3 text-right font-mono">{{ number_format($run->qualification_allowance, 0) }}</td>
                                                    </tr>
                                                    @endif
                                                    @if ($run->recognition_allowance > 0)
                                                    <tr class="border-b">
                                                        <td class="py-2 px-3 text-gray-600">Recognition Allowance</td>
                                                        <td class="py-2 px-3 text-right font-mono">{{ number_format($run->recognition_allowance, 0) }}</td>
                                                    </tr>
                                                    @endif
                                                    @if ($run->meeting_allowance > 0)
                                                    <tr class="border-b">
                                                        <td class="py-2 px-3 text-gray-600">Meeting Allowance ({{ $run->meeting_count }} meetings)</td>
                                                        <td class="py-2 px-3 text-right font-mono">{{ number_format($run->meeting_allowance, 0) }}</td>
                                                    </tr>
                                                    @endif
                                                    @if ($run->other_allowances > 0)
                                                    <tr class="border-b">
                                                        <td class="py-2 px-3 text-gray-600">Other Allowances</td>
                                                        <td class="py-2 px-3 text-right font-mono">{{ number_format($run->other_allowances, 0) }}</td>
                                                    </tr>
                                                    @endif
                                                    <tr class="border-b font-semibold">
                                                        <td class="py-2 px-3">Gross Salary</td>
                                                        <td class="py-2 px-3 text-right font-mono">{{ number_format($run->gross_salary, 0) }}</td>
                                                    </tr>

                                                    <tr class="border-b bg-red-50">
                                                        <td class="py-2 px-3 font-semibold text-red-800" colspan="2">DEDUCTIONS</td>
                                                    </tr>
                                                    <tr class="border-b">
                                                        <td class="py-2 px-3 text-gray-600">PAYE (Income Tax)</td>
                                                        <td class="py-2 px-3 text-right font-mono text-red-600">({{ number_format($run->paye, 0) }})</td>
                                                    </tr>
                                                    <tr class="border-b">
                                                        <td class="py-2 px-3 text-gray-600">NSSF Employee Contribution (5%)</td>
                                                        <td class="py-2 px-3 text-right font-mono text-red-600">({{ number_format($run->nssf_employee, 0) }})</td>
                                                    </tr>
                                                    @if ($run->lst > 0)
                                                    <tr class="border-b">
                                                        <td class="py-2 px-3 text-gray-600">LST</td>
                                                        <td class="py-2 px-3 text-right font-mono text-red-600">({{ number_format($run->lst, 0) }})</td>
                                                    </tr>
                                                    @endif
                                                    @if ($run->advance_amount > 0)
                                                    <tr class="border-b">
                                                        <td class="py-2 px-3 text-gray-600">Advance Repayment</td>
                                                        <td class="py-2 px-3 text-right font-mono text-red-600">({{ number_format($run->advance_amount, 0) }})</td>
                                                    </tr>
                                                    @endif
                                                    <tr class="border-b font-semibold">
                                                        <td class="py-2 px-3">Total Deductions</td>
                                                        <td class="py-2 px-3 text-right font-mono text-red-600">({{ number_format($run->total_deductions, 0) }})</td>
                                                    </tr>

                                                    <tr class="border-b bg-green-50">
                                                        <td class="py-2 px-3 font-semibold text-green-800" colspan="2">SUMMARY</td>
                                                    </tr>
                                                    <tr class="border-b">
                                                        <td class="py-2 px-3 text-gray-600">Net Salary</td>
                                                        <td class="py-2 px-3 text-right font-mono">{{ number_format($run->net_salary, 0) }}</td>
                                                    </tr>
                                                    <tr class="border-b">
                                                        <td class="py-2 px-3 text-gray-600">Savings Contribution</td>
                                                        <td class="py-2 px-3 text-right font-mono text-amber-600">({{ number_format($run->savings_contribution, 0) }})</td>
                                                    </tr>
                                                    <tr class="font-bold text-base bg-gray-100">
                                                        <td class="py-3 px-3">Take Home</td>
                                                        <td class="py-3 px-3 text-right font-mono text-green-700">{{ number_format($run->final_take_home, 0) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            @if ($run->status === 'completed')
                                                <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-700 text-center">
                                                    <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                                                    Salary dispatched — deposited to salary account
                                                </div>
                                            @endif
                                        </div>
                                    </x-slide-form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-8 text-center text-gray-500">
                                    No payroll runs yet. Record attendance first, then generate.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Active Employees --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <h2 class="text-lg font-semibold text-gray-800">Active Employees ({{ $activeProfiles->count() }})</h2>
                @if ($period->status === 'completed')
                    <span class="text-sm text-gray-400"><i data-lucide="lock" class="w-3 h-3 inline"></i> Closed</span>
                @endif
            </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left">
                                <th class="py-2 px-3 font-medium text-gray-600">Employee</th>
                                <th class="py-2 px-3 font-medium text-gray-600">Grade</th>
                                <th class="py-2 px-3 font-medium text-gray-600">Daily Rate</th>
                                <th class="py-2 px-3 font-medium text-gray-600">Days Worked</th>
                                <th class="py-2 px-3 font-medium text-gray-600">Meetings</th>
                                <th class="py-2 px-3 font-medium text-gray-600">Advance (UGX)</th>
                                <th class="py-2 px-3 font-medium text-gray-600"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activeProfiles as $profile)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3">{{ $profile->member?->name ?? 'N/A' }}</td>
                                    <td class="py-2 px-3">{{ $profile->payrollGrade?->name }}</td>
                                    <td class="py-2 px-3">UGX {{ number_format($profile->payrollGrade?->dailyRate() ?? 0) }}</td>
                                    <td class="py-2 px-3">
                                        @if (in_array($profile->id, $attendanceExists))
                                            <span class="text-green-600 font-medium">
                                                {{ $profile->attendance->firstWhere('payroll_period_id', $period->id)?->days_worked ?? 'Recorded' }}
                                            </span>
                                        @else
                                            <span class="text-amber-600">Missing</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3">
                                        @if ($profile->meeting_allowance_eligible)
                                            @if (in_array($profile->id, $meetingAttendanceExists))
                                                <span class="text-green-600 font-medium">
                                                    {{ $profile->meetingAttendance->firstWhere('payroll_period_id', $period->id)?->meetings_attended ?? '0' }}
                                                </span>
                                            @else
                                                <span class="text-amber-600">Missing</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3">
                                        @if (in_array($profile->id, $attendanceExists))
                                            <span>UGX {{ number_format($profile->attendance->firstWhere('payroll_period_id', $period->id)?->advance_amount ?? 0, 0) }}</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <a href="{{ route('admin.payroll.profiles.show', ['profile' => $profile->id]) }}" class="btn btn-sm" title="View Payroll Profile">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</x-app-layout>
