@php
$adminRoles = $admin->roles->pluck('name')->toArray();
@endphp

<x-app-layout>
    <x-page-title title="Edit Admin: {{ $admin->name }}" />

    <div class="mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        <form method="POST" action="{{ route('admin.admins.update', $admin->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="name" value="Name" />
                    <x-required-mark />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                        :value="old('name', $admin->name)" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="Email Address" />
                    <x-required-mark />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                        :value="old('email', $admin->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" value="Status" />
                    <x-required-mark />
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="active" {{ old('status', $admin->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ old('status', $admin->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="roles" value="Roles" />
                    <x-required-mark />
                    <div class="flex flex-wrap gap-3 mt-2">
                        @foreach($roles as $role)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    {{ in_array($role->name, $adminRoles) ? 'checked' : '' }}>
                                <span class="ml-2">{{ ucfirst($role->name) }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                </div>

                <!-- profile_photo - Last -->
                <div class="lg:col-span-3">
                    <x-image-upload name="profile_photo" label="Profile Photo (optional)" :preview="$admin->profile_photo ? 'storage/' . $admin->profile_photo : null" />
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.admins.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>
                <x-primary-button>
                    Update Admin <i data-lucide="save" class="w-4 h-4 ml-2"></i>
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
