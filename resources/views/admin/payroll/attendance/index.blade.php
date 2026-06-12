<x-app-layout>
    <x-page-title title="Employee Attendance" subtitle="Record days worked and meetings for payroll" />

    <div class="mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        @if (!$selectedPeriod)
            <form method="GET" class="mb-6">
                <div class="flex gap-4 items-end">
                    <div>
                        <x-input-label for="payroll_period_id" value="Payroll Period" />
                        <x-required-mark />
                        <select name="payroll_period_id" id="payroll_period_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="" disabled selected>Select Period...</option>
                            @foreach ($periods as $period)
                                <option value="{{ $period->id }}" {{ ($selectedPeriod?->id ?? request('payroll_period_id')) == $period->id ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $period->month, 1)) }} {{ $period->year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button>Load Employees</x-primary-button>
                </div>
            </form>
        @endif

        @if ($selectedPeriod)
            <div class="flex items-center justify-between mb-4 border-b pb-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        {{ date('F', mktime(0, 0, 0, $selectedPeriod->month, 1)) }} {{ $selectedPeriod->year }}
                    </h2>
                    <p class="text-sm text-gray-500">Record days worked and meetings attended for each employee</p>
                </div>
                <a href="{{ route('admin.payroll.periods.show', ['period' => $selectedPeriod->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    ← Back to Period
                </a>
            </div>

            @if ($selectedPeriod->status === 'completed')
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-amber-700 text-sm">
                    <i data-lucide="lock" class="w-4 h-4 inline mr-1"></i>
                    This period is closed. Attendance cannot be edited.
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('admin.payroll.periods.show', ['period' => $selectedPeriod->id]) }}" class="btn-gray text-sm">
                        Back to Period
                    </a>
                </div>
            @elseif ($profiles->isNotEmpty())
                <form method="POST" action="{{ route('admin.payroll.attendance.store') }}">
                    @csrf
                    <input type="hidden" name="payroll_period_id" value="{{ $selectedPeriod->id }}">

                    <x-table :headers="['Employee', 'Grade', 'Days Worked', 'Advance (UGX)', 'Meetings Attended']">
                        @foreach ($profiles as $profile)
                            @php
                                $existingAttendance = $profile->attendance->firstWhere('payroll_period_id', $selectedPeriod->id);
                                $existingMeeting = $profile->meetingAttendance->firstWhere('payroll_period_id', $selectedPeriod->id);
                            @endphp
                            <x-table.row>
                                <x-table.cell>{{ $profile->member->name }}</x-table.cell>
                                <x-table.cell>{{ $profile->payrollGrade?->name }}</x-table.cell>
                                <x-table.cell>
                                    <input type="hidden" name="attendance[{{ $loop->index }}][payroll_profile_id]" value="{{ $profile->id }}">
                                    <x-text-input name="attendance[{{ $loop->index }}][days_worked]" type="number" min="0" max="31" class="w-20" :value="old('attendance.' . $loop->index . '.days_worked', $existingAttendance?->days_worked ?? 0)" required />
                                </x-table.cell>
                                <x-table.cell>
                                    <x-text-input name="attendance[{{ $loop->index }}][advance_amount]" type="number" min="0" step="0.01" class="w-24" :value="old('attendance.' . $loop->index . '.advance_amount', $existingAttendance?->advance_amount ?? 0)" />
                                </x-table.cell>
                                <x-table.cell>
                                    @if ($profile->meeting_allowance_eligible)
                                        <input type="hidden" name="meeting_attendance[{{ $loop->index }}][payroll_profile_id]" value="{{ $profile->id }}">
                                        <x-text-input name="meeting_attendance[{{ $loop->index }}][meetings_attended]" type="number" min="0" class="w-20" :value="old('meeting_attendance.' . $loop->index . '.meetings_attended', $existingMeeting?->meetings_attended ?? 0)" />
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </x-table.cell>
                            </x-table.row>
                        @endforeach
                    </x-table>

                    <div class="flex items-center justify-between mt-6 pt-4 border-t">
                        <a href="{{ route('admin.payroll.periods.show', ['period' => $selectedPeriod->id]) }}" class="btn-gray text-sm">
                            Cancel
                        </a>
                        <x-primary-button>Save & Continue</x-primary-button>
                    </div>
                </form>
            @else
                <p class="text-gray-500 text-center py-8">No active payroll profiles found for this period.</p>
            @endif
        @endif
    </div>
</x-app-layout>
