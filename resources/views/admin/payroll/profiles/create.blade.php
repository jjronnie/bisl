<x-app-layout>
    <x-page-title title="Create Payroll Profile" subtitle="Assign a payroll profile to a member" />

    <div class="mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        <form method="POST" action="{{ route('admin.payroll.profiles.store') }}">
            @csrf

            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Member & Grade</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="member_id" value="Member" />
                    <x-required-mark />
                    <select name="member_id" id="member_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled selected>Select Member...</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                {{ $member->name }} ({{ $member->savingsAccount?->account_number ?? 'No Account' }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('member_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="payroll_grade_id" value="Salary Grade" />
                    <x-required-mark />
                    <select name="payroll_grade_id" id="payroll_grade_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled selected>Select Grade...</option>
                        @foreach ($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('payroll_grade_id') == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }} (UGX {{ number_format($grade->monthly_basic_salary, 0) }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('payroll_grade_id')" class="mt-2" />
                </div>

            </div>

            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 mt-6">Employment Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="employment_type" value="Employment Type" />
                    <x-required-mark />
                    <select name="employment_type" id="employment_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="permanent" {{ old('employment_type') === 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="part_time" {{ old('employment_type') === 'part_time' ? 'selected' : '' }}>Part Time</option>
                        <option value="casual" {{ old('employment_type') === 'casual' ? 'selected' : '' }}>Casual</option>
                    </select>
                    <x-input-error :messages="$errors->get('employment_type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="qualification_level" value="Qualification Level" />
                    <x-required-mark />
                    <select name="qualification_level" id="qualification_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="certificate" {{ old('qualification_level') === 'certificate' ? 'selected' : '' }}>Certificate</option>
                        <option value="diploma" {{ old('qualification_level') === 'diploma' ? 'selected' : '' }}>Diploma</option>
                        <option value="bachelors" {{ old('qualification_level') === 'bachelors' ? 'selected' : '' }}>Bachelor's</option>
                        <option value="masters" {{ old('qualification_level') === 'masters' ? 'selected' : '' }}>Master's</option>
                        <option value="phd" {{ old('qualification_level') === 'phd' ? 'selected' : '' }}>PhD</option>
                    </select>
                    <x-input-error :messages="$errors->get('qualification_level')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="recognition_level" value="Recognition Level" />
                    <x-required-mark />
                    <select name="recognition_level" id="recognition_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="none" {{ old('recognition_level') === 'none' ? 'selected' : '' }}>None</option>
                        <option value="appreciation" {{ old('recognition_level') === 'appreciation' ? 'selected' : '' }}>Appreciation</option>
                        <option value="golden_medal" {{ old('recognition_level') === 'golden_medal' ? 'selected' : '' }}>Golden Medal</option>
                    </select>
                    <x-input-error :messages="$errors->get('recognition_level')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="employment_start_date" value="Employment Start Date" />
                    <x-required-mark />
                    <x-text-input id="employment_start_date" name="employment_start_date" type="date" class="mt-1 block w-full" :value="old('employment_start_date')" required />
                    <x-input-error :messages="$errors->get('employment_start_date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="employment_end_date" value="Employment End Date" />
                    <x-text-input id="employment_end_date" name="employment_end_date" type="date" class="mt-1 block w-full" :value="old('employment_end_date')" />
                    <x-input-error :messages="$errors->get('employment_end_date')" class="mt-2" />
                </div>

                <div class="flex items-center space-x-3 mt-6">
                    <input type="checkbox" name="meeting_allowance_eligible" id="meeting_allowance_eligible" value="1" {{ old('meeting_allowance_eligible') ? 'checked' : '' }} class="rounded border-gray-300">
                    <label for="meeting_allowance_eligible" class="text-sm text-gray-700">Meeting Allowance Eligible</label>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6">
                <a href="{{ route('admin.payroll.profiles.index') }}" class="btn-secondary">Cancel</a>
                <x-primary-button>Create Profile</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
