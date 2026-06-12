<x-app-layout>
    <x-page-title title="Payroll Settings" subtitle="Configure global payroll parameters" />

    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm p-6 mt-6">
        <form method="POST" action="{{ route('admin.payroll.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <x-input-label for="savings_deduction_percentage" value="Savings Deduction Percentage" />
                    <p class="text-sm text-gray-500 mb-1">Percentage of net salary deducted as mandatory savings.</p>
                    <div class="flex items-center gap-2">
                        <x-text-input
                            name="savings_deduction_percentage"
                            id="savings_deduction_percentage"
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                            class="w-32 text-center text-lg font-bold"
                            value="{{ old('savings_deduction_percentage', $settings->savings_deduction_percentage) }}"
                            required
                        />
                        <span class="text-lg font-semibold text-gray-600">%</span>
                    </div>
                    @error('savings_deduction_percentage')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end mt-6 pt-4 border-t">
                <x-primary-button>Save Settings</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
