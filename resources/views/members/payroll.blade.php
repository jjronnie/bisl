<x-app-layout>
    <x-page-title title="Payroll History" subtitle="Your salary disbursement records" />

    @if($runs->isEmpty())
        <x-empty-state icon="clock" message="No payroll records found." />
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="py-3 px-4 text-left font-medium text-gray-600">Period</th>
                            <th class="py-3 px-4 text-left font-medium text-gray-600">Days</th>
                            <th class="py-3 px-4 text-right font-medium text-gray-600">Savings</th>
                            <th class="py-3 px-4 text-right font-medium text-gray-600">Take Home</th>
                            <th class="py-3 px-4 text-center font-medium text-gray-600"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($runs as $run)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium">
                                    {{ date('F', mktime(0, 0, 0, $run->payrollPeriod->month, 1)) }} {{ $run->payrollPeriod->year }}
                                </td>
                                <td class="py-3 px-4">{{ $run->days_worked }}</td>
                                <td class="py-3 px-4 text-right font-mono text-amber-600">{{ number_format($run->savings_contribution, 0) }}</td>
                                <td class="py-3 px-4 text-right font-mono font-semibold text-green-700">{{ number_format($run->final_take_home, 0) }}</td>
                                <td class="py-3 px-4 text-center">
                                    <x-slide-form buttonText="" buttonIcon="eye">
                                        <x-slot name="title">Salary Breakdown — {{ date('F', mktime(0, 0, 0, $run->payrollPeriod->month, 1)) }} {{ $run->payrollPeriod->year }}</x-slot>
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
                                                        <td class="py-2 px-3 text-gray-600">Gross Salary</td>
                                                        <td class="py-2 px-3 text-right font-mono">{{ number_format($run->gross_salary, 0) }}</td>
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
        </div>

        <div class="mt-4">
            {{ $runs->links() }}
        </div>
    @endif
</x-app-layout>