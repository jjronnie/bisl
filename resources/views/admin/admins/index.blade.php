<x-app-layout>

    <x-page-title title=" System Administrators" />

    <!-- Controls -->
    <div class="flex justify-end mb-6">
        <div class="flex gap-3">
            @role('superadmin')
                <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Invite Admin
                </a>
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
                        <div class="flex items-center gap-3">
                            @if ($admin->profile_photo)
                                <img src="{{ asset('storage/' . $admin->profile_photo) }}" alt="{{ $admin->name }}"
                                    class="w-10 h-10 rounded-full object-cover">
                            @else
                                <img src="{{ asset('default-avatar.png') }}" alt="{{ $admin->name }}"
                                    class="w-10 h-10 rounded-full">
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $admin->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $admin->email ?? '' }}
                                </div>
                            </div>
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
                                    <a href="{{ route('admin.admins.edit', $admin->id) }}"
                                        class="btn">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>

                                    <x-confirm-modal :action="route('admin.admins.destroy', $admin->id)"
                                        warning="Are you sure you want to delete this admin? This action cannot be undone."
                                        method="DELETE" triggerIcon="trash-2" />
                            @endif

                        </div>

                    </x-table.cell>




                </x-table.row>
            @endforeach
        </x-table>


    @endif



</x-app-layout>
