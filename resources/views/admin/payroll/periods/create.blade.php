<x-app-layout>
    <x-page-title title="Create Payroll Period" subtitle="Start a new payroll processing period" />

    <div class="mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        <form method="POST" action="{{ route('admin.payroll.periods.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="month" value="Month" />
                    <x-required-mark />
                    <select name="month" id="month" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled selected>Select Month...</option>
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('month')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="year" value="Year" />
                    <x-required-mark />
                    <select name="year" id="year" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled selected>Select Year...</option>
                        @foreach (range(now()->year - 1, now()->year + 2) as $y)
                            <option value="{{ $y }}" {{ old('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('year')" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6">
                <a href="{{ route('admin.payroll.periods.index') }}" class="btn-secondary">Cancel</a>
                <x-primary-button>Create Period</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
