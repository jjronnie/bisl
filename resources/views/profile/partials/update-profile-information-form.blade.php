@php
    $user = auth()->user();
    $member = $user?->member;

      $bannerImage=asset('assets/img/evening.webp');

      
    // Determine Avatar URL
    $avatarUrl = ($member && $member->avatar) 
        ? asset('storage/' . $member->avatar) 
        : asset('default-avatar.png');
@endphp

<section class="mx-auto">
    <!-- Profile Card -->
    <div class="bg-white rounded-lg overflow-hidden border border-gray-100">
        
      <img src="{{ $bannerImage }}" 
             alt="Profile Banner Background" 
             class="h-48 w-full object-cover brightness-75"> <div class="px-6 pb-8 relative text-center">

        <!-- Avatar & Content Wrapper -->
        <div class="px-6 pb-8 relative text-center">
            
            <!-- Centered Large Avatar -->
            <div class="-mt-16 mb-6 inline-block relative">
                <img src="{{ $avatarUrl }}" 
                     alt="{{ $user->name }}" 
                     class="w-32 h-32 object-cover rounded-full border-4 border-white shadow-lg mx-auto bg-gray-100">
                
                <!-- Active Status Dot (Optional decoration) -->
                <div class="absolute bottom-2 right-2 w-5 h-5 bg-green-500 border-4 border-white rounded-full" title="Active"></div>
            </div>

            <!-- User Details -->
            <div class="space-y-2">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                    {{ $user->name }}
                </h2>
                
                <div class="flex items-center justify-center space-x-2 text-gray-500 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ $user->email }}</span>
                </div>

                <!-- Verification Badge -->
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        Email Unverified
                    </div>
                @else
                    <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        Verified Account
                    </div>
                @endif
            </div>

            <!-- Optional: Member Info Grid -->
            @if($member)
                <div class="mt-8 grid grid-cols-2 gap-4 border-t border-gray-100 pt-6">
                    <div class="text-center">
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Tier</p>
                        <p class="font-bold text-gray-800 text-lg">{{ ucfirst($member->tier ?? 'Standard') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Joined</p>
                        <p class="font-bold text-gray-800 text-lg">{{ $user->created_at->format('M Y') }}</p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</section>