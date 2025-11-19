<x-app-layout>

    <x-page-title title=" System Administrators" />

    <!-- Controls -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                </div>
                <input x-model="search" type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="Search by member name or transaction type...">
            </div>
        </div>

        <div class="flex gap-3">




            <!-- Export to PDF Button -->
            <button class="btn">
                <i data-lucide="file-text" class="w-4 h-4"></i>
            </button>
            <!-- Export to Excel Button -->
            <button class="btn">
                <i data-lucide="sheet" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    @if($admins->isEmpty())
    <x-empty-state icon="receipt" message="No admins found." />
    @else


    <x-table :headers="['#', 'Admin' ,'Role','Status', 'Created' ]" showActions="false">

        @foreach ($admins as $index => $admin)

        <x-table.row>
            <x-table.cell>{{ $index + 1 }}</x-table.cell>
            <x-table.cell>
                <div class="text-sm font-medium text-gray-900">
                    {{ $admin->name ?? 'N/A' }}
                </div>
                <div class="text-xs text-gray-500">
                    {{ $admin->email ?? '' }}
                </div>
            </x-table.cell>

             <x-table.cell>
    {{ ucfirst($admin->getRoleNames()->implode(', ') )}}
</x-table.cell>


            <x-table.cell>

                <x-status-badge :status="$admin->status" />

            </x-table.cell>
            <x-table.cell>

                <div class="text-sm font-medium text-gray-900">
                    {{ $admin->created_at ?? 'N/A' }}
                </div>
                <div class="text-xs text-gray-500">
                    by: {{ $admin->creator->name ?? 'System' }}
                </div>

            </x-table.cell>

            <x-table.cell>
                <div class="flex items-center space-x-2">
                    @include('admin.admins.show')
                    
                </div>

            </x-table.cell>




        </x-table.row>
        @endforeach
    </x-table>


    @endif



</x-app-layout>