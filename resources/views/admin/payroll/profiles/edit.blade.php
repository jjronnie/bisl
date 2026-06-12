<x-app-layout>
    <x-page-title title="Edit Payroll Profile" subtitle="Update employee payroll profile" />

    <div class="mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        <form method="POST" action="{{ route('admin.payroll.profiles.update', ['profile' => $profile->id]) }}">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="payroll_grade_id" value="Salary Grade" />
                    <x-required-mark />
                    <select name="payroll_grade_id" id="payroll_grade_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled>Select Grade...</option>
                        @foreach ($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('payroll_grade_id', $profile->payroll_grade_id) == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }} (UGX {{ number_format($grade->monthly_basic_salary, 0) }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('payroll_grade_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="employee_number" value="Employee Number" />
                    <x-text-input id="employee_number" name="employee_number" type="text" class="mt-1 block w-full bg-gray-50" :value="$profile->employee_number" disabled />
                </div>

                <div>
                    <x-input-label for="employment_type" value="Employment Type" />
                    <x-required-mark />
                    <select name="employment_type" id="employment_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="permanent" {{ old('employment_type', $profile->employment_type) === 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="part_time" {{ old('employment_type', $profile->employment_type) === 'part_time' ? 'selected' : '' }}>Part Time</option>
                        <option value="casual" {{ old('employment_type', $profile->employment_type) === 'casual' ? 'selected' : '' }}>Casual</option>
                    </select>
                    <x-input-error :messages="$errors->get('employment_type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="qualification_level" value="Qualification Level" />
                    <x-required-mark />
                    <select name="qualification_level" id="qualification_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="certificate" {{ old('qualification_level', $profile->qualification_level) === 'certificate' ? 'selected' : '' }}>Certificate</option>
                        <option value="diploma" {{ old('qualification_level', $profile->qualification_level) === 'diploma' ? 'selected' : '' }}>Diploma</option>
                        <option value="bachelors" {{ old('qualification_level', $profile->qualification_level) === 'bachelors' ? 'selected' : '' }}>Bachelor's</option>
                        <option value="masters" {{ old('qualification_level', $profile->qualification_level) === 'masters' ? 'selected' : '' }}>Master's</option>
                        <option value="phd" {{ old('qualification_level', $profile->qualification_level) === 'phd' ? 'selected' : '' }}>PhD</option>
                    </select>
                    <x-input-error :messages="$errors->get('qualification_level')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="recognition_level" value="Recognition Level" />
                    <x-required-mark />
                    <select name="recognition_level" id="recognition_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="none" {{ old('recognition_level', $profile->recognition_level) === 'none' ? 'selected' : '' }}>None</option>
                        <option value="appreciation" {{ old('recognition_level', $profile->recognition_level) === 'appreciation' ? 'selected' : '' }}>Appreciation</option>
                        <option value="golden_medal" {{ old('recognition_level', $profile->recognition_level) === 'golden_medal' ? 'selected' : '' }}>Golden Medal</option>
                    </select>
                    <x-input-error :messages="$errors->get('recognition_level')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="employment_start_date" value="Employment Start Date" />
                    <x-required-mark />
                    <x-text-input id="employment_start_date" name="employment_start_date" type="date" class="mt-1 block w-full" :value="old('employment_start_date', $profile->employment_start_date?->format('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('employment_start_date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="employment_end_date" value="Employment End Date" />
                    <x-text-input id="employment_end_date" name="employment_end_date" type="date" class="mt-1 block w-full" :value="old('employment_end_date', $profile->employment_end_date?->format('Y-m-d'))" />
                    <x-input-error :messages="$errors->get('employment_end_date')" class="mt-2" />
                </div>

                <div class="flex items-center space-x-3 mt-6">
                    <input type="checkbox" name="meeting_allowance_eligible" id="meeting_allowance_eligible" value="1" {{ old('meeting_allowance_eligible', $profile->meeting_allowance_eligible) ? 'checked' : '' }} class="rounded border-gray-300">
                    <label for="meeting_allowance_eligible" class="text-sm text-gray-700">Meeting Allowance Eligible</label>
                </div>

                <div class="flex items-center space-x-3 mt-6">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $profile->is_active) ? 'checked' : '' }} class="rounded border-gray-300">
                    <label for="is_active" class="text-sm text-gray-700">Active</label>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6">
                <a href="{{ route('admin.payroll.profiles.index') }}" class="btn-secondary">Cancel</a>
                <x-primary-button>Update Profile</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
