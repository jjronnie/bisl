<x-app-layout>
    {{-- ASSUMPTION: The member record is passed to this view as $member --}}
    <x-page-title title="Member Details: {{ $member->name }}" />

    <div class="mx-auto bg-white p-8 rounded-xl shadow-lg mt-6">

        {{-- Header & Actions --}}
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h1 class="text-2xl font-bold text-indigo-700">{{ $member->name }}</h1>
            <a href="{{ route('admin.members.edit', $member) }}"
                class="btn">
                
                Edit Member
            </a>
        </div>

        {{-- Sacco & Profile Info (2 Columns) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            {{-- Member Info Column (Left/Center) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Sacco Details --}}
                <div class="border-b pb-4">
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Sacco Details</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        {{-- Replaced <x-detail-item> with standard HTML --}}
                        <div>
                            <dt class="font-medium text-gray-500">Account Number</dt>
                            <dd class="mt-1 text-gray-900">{{ $member->savingsAccount->account_number ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Join Date</dt>
                            <dd class="mt-1 text-gray-900">{{ $member->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-gray-900">Active</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">User Role</dt>
                            <dd class="mt-1 text-gray-900">{{ $member->user->getRoleNames()->first() ?? 'N/A' }}</dd>
                        </div>
                    </div>
                </div>

                {{-- Personal Information --}}
                <div class="border-b pb-4">
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Personal Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        {{-- Replaced <x-detail-item> with standard HTML --}}
                        <div>
                            <dt class="font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-gray-900">{{ $member->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Email Address</dt>
                            <dd class="mt-1 text-gray-900">{{ $member->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Date of Birth</dt>
                            <dd class="mt-1 text-gray-900">{{ $member->date_of_birth ? $member->date_of_birth->format('M d, Y') : 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Nationality</dt>
                            <dd class="mt-1 text-gray-900">{{ $member->nationality ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Gender</dt>
                            <dd class="mt-1 text-gray-900">{{ ucfirst($member->gender ?? 'N/A') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Marital Status</dt>
                            <dd class="mt-1 text-gray-900">{{ ucfirst($member->marital_status ?? 'N/A') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">National ID</dt>
                            <dd class="mt-1 text-gray-900">{{ $member->national_id_number ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Passport Number</dt>
                            <dd class="mt-1 text-gray-900">{{ $member->passport_number ?? 'N/A' }}</dd>
                        </div>
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="border-b pb-4">
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Contact Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        {{-- Replaced <x-detail-item> with standard HTML --}}
                        <div>
                            <dt class="font-medium text-gray-500">Primary Phone</dt>
                            <dd class="mt-1 text-gray-900">{{ $member->phone1 }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Secondary Phone</dt>
                            <dd class="mt-1 text-gray-900">{{ $member->phone2 ?? 'N/A' }}</dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="font-medium text-gray-500">Physical Address</dt>
                            <dd class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $member->address ?? 'N/A' }}</dd>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Avatar Column (Right) --}}
            <div class="lg:col-span-1 flex flex-col items-center pt-10 border-l lg:pl-6">
                <p class="text-lg font-medium text-gray-600 mb-3">Profile Photo</p>
                @if ($member->avatar)
                    <img src="{{ asset('storage/' . $member->avatar) }}" alt="Member Avatar"
                        class="w-48 h-48 object-cover rounded-full shadow-xl border-4 border-indigo-200">
                @else
                    <div class="w-48 h-48 flex items-center justify-center bg-gray-200 rounded-full text-gray-500 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">No photo available</p>
                @endif
            </div>
        </div>

  

        {{-- Back Button --}}
        <div class="mt-8 flex justify-start">

                <x-confirm-modal :action="route('admin.members.destroy', $member->id)"
                                warning="Are you sure you want to delete this Member? This action cannot be undone."
                                triggerIcon="trash" />
                                
            <a href="{{ route('admin.members.index') }}"
                class="btn-gray">
                 Back to Members List
            </a>
        </div>
    </div>
</x-app-layout>
