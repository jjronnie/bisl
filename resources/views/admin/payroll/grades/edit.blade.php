<x-app-layout>
    <x-page-title title="Edit Payroll Grade" subtitle="Update salary grade details" />

    <div class="mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        <form method="POST" action="{{ route('admin.payroll.grades.update', ['grade' => $grade->id]) }}">
            @csrf
            @method('PATCH')

            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Grade Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="name" value="Grade Name" />
                    <x-required-mark />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $grade->name)" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="monthly_basic_salary" value="Monthly Basic Salary (UGX)" />
                    <x-required-mark />
                    <x-text-input id="monthly_basic_salary" name="monthly_basic_salary" type="number" step="0.01" class="mt-1 block w-full" :value="old('monthly_basic_salary', $grade->monthly_basic_salary)" required />
                    <x-input-error :messages="$errors->get('monthly_basic_salary')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="working_days_divisor" value="Working Days Divisor" />
                    <x-required-mark />
                    <x-text-input id="working_days_divisor" name="working_days_divisor" type="number" class="mt-1 block w-full" :value="old('working_days_divisor', $grade->working_days_divisor)" required />
                    <x-input-error :messages="$errors->get('working_days_divisor')" class="mt-2" />
                </div>

                <div class="lg:col-span-3">
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('description', $grade->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center space-x-3 mt-4">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $grade->is_active) ? 'checked' : '' }} class="rounded border-gray-300">
                <label for="is_active" class="text-sm text-gray-700">Active</label>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6">
                <a href="{{ route('admin.payroll.grades.index') }}" class="btn-secondary">Cancel</a>
                <x-primary-button>Update Grade</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
