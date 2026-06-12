<x-app-layout>
    <x-page-title title="Payroll Grades" subtitle="Manage salary grades" />

    <div class="mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        <div class="flex justify-between items-center mb-4">
            <p class="text-gray-600">Total: {{ $grades->total() }} grades</p>
            <a href="{{ route('admin.payroll.grades.create') }}" class="btn">Add New Grade</a>
        </div>

        <x-table :headers="['Name', 'Monthly Basic Salary', 'Divisor', 'Daily Rate', 'Status', 'Actions']" :showActions="true">
            @foreach ($grades as $grade)
                <x-table.row>
                    <x-table.cell>{{ $grade->name }}</x-table.cell>
                    <x-table.cell>UGX {{ number_format($grade->monthly_basic_salary, 0) }}</x-table.cell>
                    <x-table.cell>{{ $grade->working_days_divisor }}</x-table.cell>
                    <x-table.cell>UGX {{ number_format($grade->dailyRate(), 0) }}</x-table.cell>
                    <x-table.cell>
                        <x-status-badge :status="$grade->is_active ? 'active' : 'inactive'">
                            {{ $grade->is_active ? 'Active' : 'Inactive' }}
                        </x-status-badge>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex space-x-2">
                            <a class="btn" href="{{ route('admin.payroll.grades.edit', $grade->id) }}">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @endforeach
        </x-table>

        <div class="mt-4">
            {{ $grades->links() }}
        </div>
    </div>
</x-app-layout>
