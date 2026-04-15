<x-app-layout>

    <x-page-title title=" System Administrators" />

    <!-- Controls -->
    <div class="flex justify-end mb-6">
        <div class="flex gap-3">
            @role('superadmin')
                @include('admin.admins.create')
            @endrole
        </div>
    </div>

    @if ($admins->isEmpty())
        <x-empty-state icon="receipt" message="No admins found." />
    @else
        <x-table :headers="['#', 'Admin', 'Role', 'Account Status', 'Created']" showActions="false">

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
                        {{ ucfirst($admin->getRoleNames()->implode(', ')) }}
                    </x-table.cell>


                    <x-table.cell>

                        <x-status-badge :status="$admin->status" />

                    </x-table.cell>
                    <x-table.cell>

                        <div class="text-sm font-medium text-gray-900">
                            {{ $admin->created_at->format('d M Y H:i') }}
                        </div>
                        <div class="text-xs text-gray-500">
                            by: {{ $admin->creator->name ?? 'System' }}
                        </div>

                    </x-table.cell>

                    <x-table.cell>
                        <div class="flex items-center space-x-2">
                            @include('admin.admins.show')

                            @if (auth()->user()->hasRole('superadmin'))
                                @if (!$admin->hasRole('superadmin'))
                                    @include('admin.admins.edit', ['user' => $admin])

                                    <x-confirm-modal :action="route('admin.admins.destroy', $admin->id)"
                                        warning="Are you sure you want to delete this admin? This action cannot be undone."
                                        method="DELETE" triggerIcon="trash-2" />
                                @endif
                            @endif

                        </div>

                    </x-table.cell>




                </x-table.row>
            @endforeach
        </x-table>


    @endif



</x-app-layout>
