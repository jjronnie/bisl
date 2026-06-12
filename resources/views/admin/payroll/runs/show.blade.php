<x-app-layout>
    <x-page-title title="Payslip" subtitle="{{ $payslipData['employee_name'] }} - {{ $payslipData['period'] }}" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Earnings</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between py-1">
                        <dt class="text-gray-600">Monthly Basic Salary</dt>
                        <dd class="font-medium">UGX {{ number_format($payslipData['monthly_basic_salary'], 0) }}</dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-gray-600">Daily Rate</dt>
                        <dd class="font-medium">UGX {{ number_format($payslipData['daily_rate'], 0) }}</dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-gray-600">Days Worked</dt>
                        <dd class="font-medium">{{ $payslipData['days_worked'] }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-t pt-2">
                        <dt class="text-gray-600 font-medium">Basic Salary Earned</dt>
                        <dd class="font-semibold">UGX {{ number_format($payslipData['basic_salary_earned'], 0) }}</dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-gray-600">Qualification Allowance</dt>
                        <dd class="font-medium">UGX {{ number_format($payslipData['qualification_allowance'], 0) }}</dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-gray-600">Recognition Allowance</dt>
                        <dd class="font-medium">UGX {{ number_format($payslipData['recognition_allowance'], 0) }}</dd>
                    </div>
                    @if ($payslipData['meeting_allowance'] > 0)
                        <div class="flex justify-between py-1">
                            <dt class="text-gray-600">Meeting Allowance ({{ $payslipData['meeting_count'] }} meetings)</dt>
                            <dd class="font-medium">UGX {{ number_format($payslipData['meeting_allowance'], 0) }}</dd>
                        </div>
                    @endif
                    @if ($payslipData['other_allowances'] > 0)
                        <div class="flex justify-between py-1">
                            <dt class="text-gray-600">Other Allowances</dt>
                            <dd class="font-medium">UGX {{ number_format($payslipData['other_allowances'], 0) }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between py-1 border-t border-b pt-2 pb-2">
                        <dt class="text-gray-800 font-semibold">Gross Salary</dt>
                        <dd class="font-bold text-lg">UGX {{ number_format($payslipData['gross_salary'], 0) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Transactions</h2>
                <x-table :headers="['Type', 'Amount', 'Reference', 'Description']">
                    @foreach ($payslipData['transactions'] as $txn)
                        <x-table.row>
                            <x-table.cell><x-status-badge :status="$txn->type === 'salary' || $txn->type === 'allowance' ? 'active' : 'inactive'">{{ ucfirst($txn->type) }}</x-status-badge></x-table.cell>
                            <x-table.cell>UGX {{ number_format($txn->amount, 0) }}</x-table.cell>
                            <x-table.cell>{{ $txn->reference }}</x-table.cell>
                            <x-table.cell>{{ $txn->description }}</x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Deductions</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between py-1">
                        <dt class="text-gray-600">PAYE Tax</dt>
                        <dd class="font-medium text-red-600">- UGX {{ number_format($payslipData['paye'], 0) }}</dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-gray-600">NSSF (Employee)</dt>
                        <dd class="font-medium text-red-600">- UGX {{ number_format($payslipData['nssf_employee'], 0) }}</dd>
                    </div>
                    @if ($payslipData['lst'] > 0)
                        <div class="flex justify-between py-1">
                            <dt class="text-gray-600">LST</dt>
                            <dd class="font-medium text-red-600">- UGX {{ number_format($payslipData['lst'], 0) }}</dd>
                        </div>
                    @endif
                    @if ($payslipData['advance_amount'] > 0)
                        <div class="flex justify-between py-1">
                            <dt class="text-gray-600">Advance Repayment</dt>
                            <dd class="font-medium text-red-600">- UGX {{ number_format($payslipData['advance_amount'], 0) }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between py-1 border-t pt-2">
                        <dt class="text-gray-800 font-semibold">Total Deductions</dt>
                        <dd class="font-bold text-red-600">- UGX {{ number_format($payslipData['total_deductions'], 0) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Summary</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between py-1">
                        <dt class="text-gray-600">Net Salary</dt>
                        <dd class="font-semibold">UGX {{ number_format($payslipData['net_salary'], 0) }}</dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-gray-600">Savings Contribution (5%)</dt>
                        <dd class="font-medium text-red-600">- UGX {{ number_format($payslipData['savings_contribution'], 0) }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-t pt-2">
                        <dt class="text-gray-800 font-bold">Final Take Home</dt>
                        <dd class="font-bold text-lg text-green-600">UGX {{ number_format($payslipData['final_take_home'], 0) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Details</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Employee #</dt>
                        <dd>{{ $payslipData['employee_number'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Grade</dt>
                        <dd>{{ $payslipData['grade'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Type</dt>
                        <dd>{{ ucfirst($payslipData['employment_type']) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Generated</dt>
                        <dd>{{ $payslipData['generated_at']?->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
