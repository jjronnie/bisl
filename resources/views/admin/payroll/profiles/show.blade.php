<x-app-layout>
    <x-page-title title="Payroll Profile" subtitle="{{ $profile->member?->name ?? 'N/A' }}" />

    <div class="mt-6">
        <div class="flex justify-end gap-3 mb-6">
            <a href="{{ route('admin.payroll.profiles.edit', ['profile' => $profile->id]) }}" class="btn">Edit Profile</a>
            <a href="{{ route('admin.members.show', ['member' => $profile->member_id]) }}" class="btn">View Member</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex flex-col items-center mb-6">
                    <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center mb-4 overflow-hidden">
                        @if($profile->member?->avatar)
                            <img src="{{ asset('storage/' . $profile->member->avatar) }}" alt="{{ $profile->member->name }}" class="w-full h-full object-cover">
                        @else
                            <i data-lucide="user" class="w-12 h-12 text-gray-400"></i>
                        @endif
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $profile->member?->name ?? 'N/A' }}</h3>
                    <p class="text-sm text-gray-500">Employee #{{ $profile->employee_number }}</p>
                    <x-status-badge :status="$profile->is_active ? 'active' : 'inactive'" class="mt-2">{{ $profile->is_active ? 'Active' : 'Inactive' }}</x-status-badge>
                </div>
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-1">Grade</h4>
                        <p class="text-gray-800">{{ $profile->payrollGrade?->name }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-1">Basic Salary</h4>
                        <p class="text-gray-800">UGX {{ number_format($profile->payrollGrade?->monthly_basic_salary ?? 0, 0) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-1">Employment Type</h4>
                        <p class="text-gray-800">{{ ucfirst($profile->employment_type) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-1">Start Date</h4>
                        <p class="text-gray-800">{{ $profile->employment_start_date->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Personal Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 mb-1">Full Name</h3>
                            <p class="text-gray-800">{{ $profile->member?->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 mb-1">Email</h3>
                            <p class="text-gray-800">{{ $profile->member?->user?->email }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 mb-1">Phone</h3>
                            <p class="text-gray-800">{{ $profile->member?->phone1 }}</p>
                        </div>
                    
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Professional Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 mb-1">Qualification Level</h3>
                            <p class="text-gray-800">{{ ucfirst($profile->qualification_level) }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 mb-1">Recognition Level</h3>
                            <p class="text-gray-800">{{ ucfirst($profile->recognition_level) }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 mb-1">Meeting Allowance</h3>
                            <p class="text-gray-800">{{ $profile->meeting_allowance_eligible ? 'Eligible' : 'Not Eligible' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Payroll History</h2>
            @if ($profile->payrollRuns->isEmpty())
                <p class="text-gray-500">No payroll runs yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left">
                                <th class="py-2 px-3 font-medium text-gray-600">Period</th>
                                <th class="py-2 px-3 font-medium text-gray-600">Days</th>
                                <th class="py-2 px-3 font-medium text-gray-600">Gross</th>
                                <th class="py-2 px-3 font-medium text-gray-600">PAYE</th>
                                <th class="py-2 px-3 font-medium text-gray-600">NSSF</th>
                                <th class="py-2 px-3 font-medium text-gray-600">Net</th>
                                <th class="py-2 px-3 font-medium text-gray-600">Savings</th>
                                <th class="py-2 px-3 font-medium text-gray-600">Take Home</th>
                                <th class="py-2 px-3 font-medium text-gray-600">Status</th>
                                <th class="py-2 px-3 font-medium text-gray-600"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($profile->payrollRuns as $run)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3">{{ date('F', mktime(0, 0, 0, $run->payrollPeriod->month, 1)) }} {{ $run->payrollPeriod->year }}</td>
                                    <td class="py-2 px-3">{{ $run->days_worked }}</td>
                                    <td class="py-2 px-3">UGX {{ number_format($run->gross_salary, 0) }}</td>
                                    <td class="py-2 px-3">UGX {{ number_format($run->paye, 0) }}</td>
                                    <td class="py-2 px-3">UGX {{ number_format($run->nssf_employee, 0) }}</td>
                                    <td class="py-2 px-3">UGX {{ number_format($run->net_salary, 0) }}</td>
                                    <td class="py-2 px-3">UGX {{ number_format($run->savings_contribution, 0) }}</td>
                                    <td class="py-2 px-3 font-semibold">UGX {{ number_format($run->final_take_home, 0) }}</td>
                                    <td class="py-2 px-3">
                                        <x-status-badge :status="$run->status === 'completed' ? 'active' : 'pending'">
                                            {{ $run->status === 'completed' ? 'Dispatched' : 'Draft' }}
                                        </x-status-badge>
                                    </td>
                                    <td class="py-2 px-3">
                                        <x-slide-form buttonText="" buttonIcon="eye">
                                            <x-slot name="title">{{ $profile->member?->name ?? 'Employee' }} — Salary Breakdown</x-slot>
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
                                                            <td class="py-2 px-3 text-gray-600">Meeting Allowance</td>
                                                            <td class="py-2 px-3 text-right font-mono">{{ number_format($run->meeting_allowance, 0) }}</td>
                                                        </tr>
                                                        @endif
                                                        @if ($run->other_allowances > 0)
                                                        <tr class="border-b">
                                                            <td class="py-2 px-3 text-gray-600">Other Allowances</td>
                                                            <td class="py-2 px-3 text-right font-mono">{{ number_format($run->other_allowances, 0) }}</td>
                                                        </tr>
                                                        @endif
                                                        <tr class="border-b bg-red-50">
                                                            <td class="py-2 px-3 font-semibold text-red-800" colspan="2">DEDUCTIONS</td>
                                                        </tr>
                                                        <tr class="border-b">
                                                            <td class="py-2 px-3 text-gray-600">PAYE (Income Tax)</td>
                                                            <td class="py-2 px-3 text-right font-mono text-red-600">({{ number_format($run->paye, 0) }})</td>
                                                        </tr>
                                                        <tr class="border-b">
                                                            <td class="py-2 px-3 text-gray-600">NSSF Employee Contribution</td>
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
                                            </div>
                                        </x-slide-form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>