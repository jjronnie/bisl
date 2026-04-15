@php
use Spatie\Permission\Models\Permission;
$permissions = Permission::all()->groupBy(function($item) {
    return explode('.', $item->name)[0];
});
@endphp

<x-slide-form buttonIcon="plus" title="Invite New System Admin">

    <form method="POST" action="{{ route('admin.admins.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label for="name" class="label">Name <span class="text-red-600">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="input @error('name') border-red-500 @enderror" />
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="label">Email Address <span class="text-red-600">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="input @error('email') border-red-500 @enderror" />
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="roles" class="label">Roles <span class="text-red-600">*</span></label>
                <div class="flex flex-wrap gap-3 mt-2">
                    @foreach($roles as $role)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                {{ old('roles') && in_array($role->name, old('roles')) ? 'checked' : '' }}>
                            <span class="ml-2">{{ ucfirst($role->name) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

        </div>

        <div class="flex justify-start space-x-3 mt-6">
            <button type="submit" class="btn">
                Send Invite <i data-lucide="save" class="w-4 h-4 ml-2"></i>
            </button>
        </div>
    </form>

</x-slide-form>