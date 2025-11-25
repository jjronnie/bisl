@php
    $hour = now()->hour;
    if ($hour < 12) {
        $greeting = 'Good morning';
        $greetIcon = 'sunrise';
    } elseif ($hour < 17) {
        $greeting = 'Good afternoon';
        $greetIcon = 'sun';
    } else {
        $greeting = 'Good evening';
        $greetIcon = 'moon';
    }
@endphp

<section class="mb-8 p-4">
    <div class="bg-blue-700 rounded-2xl p-6 shadow-lg text-white w-full mx-auto">

        <!-- Greeting -->
        <div class="flex items-center space-x-3 mb-4">
            <div>
                <p class="text-sm opacity-80">{{ $greeting }}</p>
                <p class="text-lg font-semibold">{{ auth()->user()->name }}</p>
            </div>
            
        </div>

        <div class="border-t border-white/20 my-4"></div>

        <!-- Balance Header -->
        <div class="flex items-center justify-between">
            <span class="text-sm opacity-80">Savings Account Balance</span>

            <button id="toggleBalance" class="flex items-center text-xs font-medium bg-white/20 px-3 py-1 rounded-full">
                <i data-lucide="eye-off" id="balanceIcon" class="w-4 h-4 mr-1"></i>
                <span id="balanceToggleText">Show</span>
            </button>
        </div>

        <!-- Balance Amount (Hidden by default) -->
        <p id="balanceAmount" class="text-2xl font-bold mt-2 tracking-wide" data-hidden="yes">
            ••• ••• •
        </p>

        <!-- Account Number -->
        <div class="mt-6 flex items-center justify-between">
            <div>
                <p class="text-xs opacity-80">Account Number</p>
                <p class="mt-1 text-lg tracking-wider font-semibold">
                    {{ $member->savingsAccount->account_number }}
                </p>
            </div>

            <div class="bg-white text-blue-700 font-bold text-xs px-2 py-1 rounded">
              Tier:  {{ ucfirst($member->tier )}}
            </div>
        </div>
    </div>
</section>

<script>
    const balance = "UGX {{ number_format($balance) }}";
    const amount = document.getElementById('balanceAmount');
    const icon = document.getElementById('balanceIcon');
    const toggleText = document.getElementById('balanceToggleText');

    document.getElementById('toggleBalance').addEventListener('click', function () {
        if (amount.dataset.hidden === 'yes') {
            amount.textContent = balance;
            amount.dataset.hidden = 'no';
            icon.setAttribute('data-lucide', 'eye');
            toggleText.textContent = 'Hide';
        } else {
            amount.textContent = '•••••••';
            amount.dataset.hidden = 'yes';
            icon.setAttribute('data-lucide', 'eye-off');
            toggleText.textContent = 'Show';
        }
        lucide.createIcons();
    });
</script>
