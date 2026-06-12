<x-app-layout>
    <x-page-title title="Payroll Profiles" subtitle="Manage employee payroll profiles" />

    <div class="mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        <div class="flex justify-between items-center mb-4">
            <form method="GET" class="flex gap-2 items-center flex-wrap">
                <x-text-input name="search" type="text" placeholder="Search by name or employee number..." class="w-64" :value="request('search')" />
                <select name="employment_type" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">All Types</option>
                    <option value="permanent" {{ request('employment_type') === 'permanent' ? 'selected' : '' }}>Permanent</option>
                    <option value="part_time" {{ request('employment_type') === 'part_time' ? 'selected' : '' }}>Part Time</option>
                    <option value="casual" {{ request('employment_type') === 'casual' ? 'selected' : '' }}>Casual</option>
                </select>
                <select name="is_active" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                <x-primary-button>Filter</x-primary-button>
            </form>
            <a href="{{ route('admin.payroll.profiles.create') }}" class="btn">Add Profile</a>
        </div>

        <x-table :headers="['Employee #', 'Member Name', 'Grade', 'Type', 'Qualification', 'Status', ]" :showActions="true">
            @foreach ($profiles as $profile)
                <x-table.row>
                    <x-table.cell>{{ $profile->employee_number }}</x-table.cell>
                    <x-table.cell>{{ $profile->member?->name }}</x-table.cell>
                    <x-table.cell>{{ $profile->payrollGrade?->name }}</x-table.cell>
                    <x-table.cell>{{ ucfirst($profile->employment_type) }}</x-table.cell>
                    <x-table.cell>{{ ucfirst($profile->qualification_level) }}</x-table.cell>
                    <x-table.cell>
                        <x-status-badge :status="$profile->is_active ? 'active' : 'inactive'">
                            {{ $profile->is_active ? 'Active' : 'Inactive' }}
                        </x-status-badge>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex space-x-2">
                            <a class="btn" href="{{ route('admin.payroll.profiles.show', $profile->id) }}">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <a class="btn" href="{{ route('admin.payroll.profiles.edit', $profile->id) }}">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @endforeach
        </x-table>

        <div class="mt-4">
            {{ $profiles->links() }}
        </div>
    </div>
</x-app-layout>
