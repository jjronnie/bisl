<x-app-layout>
    <x-page-title title="Payroll Grade: {{ $grade->name }}" subtitle="Grade details and configuration" />

    <div class="mx-auto mt-6 max-w-2xl">
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <dl class="space-y-4">
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-600 font-medium">Name</dt>
                    <dd class="font-semibold">{{ $grade->name }}</dd>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-600 font-medium">Monthly Basic Salary</dt>
                    <dd class="font-semibold">UGX {{ number_format($grade->monthly_basic_salary, 0) }}</dd>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-600 font-medium">Working Days Divisor</dt>
                    <dd class="font-semibold">{{ $grade->working_days_divisor }}</dd>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-600 font-medium">Daily Rate</dt>
                    <dd class="font-semibold">UGX {{ number_format($grade->dailyRate(), 0) }}</dd>
                </div>
                @if ($grade->description)
                    <div class="flex justify-between border-b pb-2">
                        <dt class="text-gray-600 font-medium">Description</dt>
                        <dd class="font-semibold">{{ $grade->description }}</dd>
                    </div>
                @endif
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-600 font-medium">Status</dt>
                    <dd>
                        <x-status-badge :status="$grade->is_active ? 'active' : 'inactive'">
                            {{ $grade->is_active ? 'Active' : 'Inactive' }}
                        </x-status-badge>
                    </dd>
                </div>
            </dl>

            <div class="mt-6 flex space-x-3">
                <a href="{{ route('admin.payroll.grades.edit', ['grade' => $grade->id]) }}" class="btn">Edit Grade</a>
                <a href="{{ route('admin.payroll.grades.index') }}" class="btn-secondary">Back to Grades</a>
            </div>
        </div>
    </div>
</x-app-layout>
