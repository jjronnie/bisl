<x-app-layout>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Header Card --}}
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl shadow-xl p-6 mb-6">
            <div class="flex flex-col sm:flex-row items-center gap-6">
                {{-- Avatar --}}
                <div class="flex-shrink-0">
                    @if ($member->avatar)
                        <img src="{{ asset('storage/' . $member->avatar) }}" alt="{{ $member->name }}"
                            class="w-24 h-24 sm:w-28 sm:h-28 object-cover rounded-full ring-4 ring-white shadow-lg">
                    @else
                        <img src="{{ asset('default-avatar.png') }}" alt="{{ $member->name }}"
                            class="w-24 h-24 sm:w-28 sm:h-28 object-cover rounded-full ring-4 ring-white shadow-lg">
                    @endif
                </div>

                {{-- Member Info --}}
                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">{{ $member->name }}</h1>
                    <p class="text-indigo-100 text-sm sm:text-base">Account: {{ $member->savingsAccount->account_number
                        ?? 'N/A' }}</p>
                    <div class="flex flex-wrap gap-2 justify-center sm:justify-start mt-3">
                        <span class="">
                            <x-status-badge :status="$member->user->status" />

                        </span>
                        <span class="">
                            <x-status-badge :status="$member->tier" />

                        </span>
                    </div>
                </div>


            </div>
        </div>


        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
            <!-- card -->
            <x-stat-card title="Savings Account" value="UGX {{ number_format($balance) }}" icon="coins" />
            <x-stat-card title="Loan Protection Fund" value="UGX {{ number_format($loanProtection) }}" icon="shield" />
            <x-stat-card title="Accessible Balance" value="UGX {{ number_format($accessible) }}" icon="dollar-sign" />
            <x-stat-card title="Accumulated Interest "
                value="{{ number_format( $member->savingsAccount->interest_earned) }}" icon="percent" />


        </div>







        {{-- Info Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            {{-- Personal Information Card --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-200">Personal Information
                </h2>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Full Name</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $member->name }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Email</dt>
                        <dd class="text-sm text-gray-900">{{ $member->user->email }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Date of Birth</dt>
                        <dd class="text-sm text-gray-900">{{ $member->date_of_birth ? $member->date_of_birth->format('M
                            d, Y') : 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Gender</dt>
                        <dd class="text-sm text-gray-900">{{ ucfirst($member->gender ?? 'N/A') }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Marital Status</dt>
                        <dd class="text-sm text-gray-900">{{ ucfirst($member->marital_status ?? 'N/A') }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Nationality</dt>
                        <dd class="text-sm text-gray-900">{{ $member->nationality ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>




            {{-- Membership Details Card --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-200">Membership Details
                </h2>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Join Date</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $member->created_at->format('M d, Y') }}</dd>
                    </div>


                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Account Number</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ $member->savingsAccount->account_number ?? 'N/A'
                            }}</dd>
                    </div>

                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Account Status</dt>
                        <dd class="text-sm text-gray-900 font-mono">
                            <x-status-badge :status="$member->savingsAccount->status" />

                        </dd>
                    </div>

                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Savings Balance</dt>
                        <dd class="text-sm text-gray-900 font-mono">UGX {{
                            number_format($balance ?? 'N/A') }}</dd>
                    </div>

                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Loan Protection Fund</dt>
                        <dd class="text-sm text-gray-900 font-mono">UGX {{
                            number_format($loanProtection ?? 'N/A') }}</dd>
                    </div>

                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Total Accessible Balance</dt>
                        <dd class="text-sm text-gray-900 font-mono">UGX {{
                            number_format($accessible ?? 'N/A') }}</dd>
                    </div>







                </dl>
            </div>


            {{-- Contact Information Card --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-200">Contact Information
                </h2>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Primary Phone</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $member->phone1 }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Secondary Phone</dt>
                        <dd class="text-sm text-gray-900">{{ $member->phone2 ?? 'N/A' }}</dd>
                    </div>
                    <div class="py-2">
                        <dt class="text-sm font-medium text-gray-600 mb-1">Physical Address</dt>
                        <dd class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $member->address ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Identification Card --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-200">Identification</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">National ID</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ $member->national_id_number ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-600">Passport Number</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ $member->passport_number ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-between bg-white rounded-xl shadow-md mb-6 p-6">
            <a href="{{ route('admin.members.index') }}" class="btn-gray">
                ← Back to Members
            </a>

            <a href="{{  route('admin.members.edit', $member) }}" class="btn">
                Edit Member
            </a>

            <a href="{{  route('admin.members.transactions.index', $member) }}" class="btn">
                View Transactions
            </a>

@role('superadmin')
            @if($member->user->status === 'suspended')
                <x-confirm-modal :action="route('admin.members.unsuspend', $member)"
                    warning="Are you sure you want to reactivate this member's account?"
                    triggerText="Reactivate Member" buttonClass="bg-green-600 hover:bg-green-700" />
            @else
                <x-confirm-modal :action="route('admin.members.suspend', $member)"
                    warning="Are you sure you want to suspend this member's account?"
                    triggerText="Suspend Member" buttonClass="bg-orange-500 hover:bg-orange-600" />
            @endif

            <x-confirm-modal :action="route('admin.members.destroy', $member->id)"
                warning="Are you sure you want to delete this Member? This action cannot be undone."
                triggerText="Delete Member" />
@endrole

        </div>



    </div>
</x-app-layout>