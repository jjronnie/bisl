<x-app-layout>
    <x-page-title title="Group Members" />

    <div x-data="{ search: '' }" class="space-y-8">

        <!-- Controls -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">

            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input x-model="search" type="text"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                        placeholder="Search by name or A/C No.">
                </div>
            </div>


            <div class="flex gap-3">

                <a class="btn" href="{{ route('admin.members.create') }}"> <i data-lucide="plus"
                        class="w-4 h-4 "></i></a>

                <!-- Export to PDF Button -->
                <button class="btn">
                    <i data-lucide="file-text" class="w-4 h-4 "></i>
                </button>


                <!-- Export to Excel Button -->
                <button class="btn">
                    <i data-lucide="sheet" class="w-4 h-4 "></i>
                </button>
            </div>

        </div>

        @if ($members->isEmpty())


        <x-empty-state icon="users" message="No members added yet. " />


        @else




        <x-table :headers="[ '#','Account Number', 'Member', 'Balance', 'Status', 'Tier', 'Actions']">
            @foreach ($members as $index =>$member)

            <template
                x-if="!search || '{{ $member->name }}'.toLowerCase().includes(search.toLowerCase()) || '{{ $member->savingsAccount->account_number  }}'.toLowerCase().includes(search.toLowerCase())">

                <x-table.row>
                    <x-table.cell>{{ $index + 1 }}</x-table.cell>
                    <x-table.cell>{{ $member->savingsAccount->account_number }}</x-table.cell>
                    <x-table.cell>
                        <div class="flex items-center">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ ucfirst($member->name) }}</div>
                                <div class="text-sm text-gray-500">{{ $member->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </x-table.cell>
                    <x-table.cell>UGX {{ number_format($member->savingsAccount->balance) }}</x-table.cell>
                    <x-table.cell>
                        <x-status-badge :status="$member->user->status" />
                    </x-table.cell>

                        <x-table.cell>
                        <x-status-badge :status="$member->tier" />
                    </x-table.cell>



                    <x-table.cell>

                        <div class="flex space-x-2">
                            <a class="btn" href="{{ route('admin.members.show', $member->id) }}"><i data-lucide="eye"
                                    class="w-4 h-4 "></i></a>
                            <a class="btn" href="{{ route('admin.members.edit', $member->id) }}"><i data-lucide="edit"
                                    class="w-4 h-4 "></i></a>

                        

                        </div>
                    </x-table.cell>

                </x-table.row>
            </template>
            @endforeach
        </x-table>

        @endif

    </div>
</x-app-layout>