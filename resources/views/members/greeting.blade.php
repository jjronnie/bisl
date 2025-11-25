@php
$hour = now()->hour;
// Base logic preserved
if ($hour < 12) { $greeting='Good morning' ; $greetIcon='sunrise' ; $bgImage=asset('assets/img/morning.webp'); } elseif
    ($hour < 17) { $greeting='Good afternoon' ; $greetIcon='sun' ; $bgImage=asset('assets/img/afternoon.webp'); } else {
    $greeting='Good evening' ; $greetIcon='moon' ; $bgImage=asset('assets/img/evening.webp'); } // Tier Color Logic
    $tier=strtolower($member->tier); // e.g., 'gold' or 'silver'

    if ($tier === 'gold') {
    // Yellow/Amber Glass styling
    $tierStyle = 'bg-yellow-500/20 border-yellow-400/30 text-yellow-300';
    } elseif ($tier === 'silver') {
    // Slate/White Glass styling (Metallic look)
    $tierStyle = 'bg-slate-400/20 border-slate-300/30 text-slate-100';
    } else {
    // Fallback (Bronze or Standard)
    $tierStyle = 'bg-blue-500/20 border-blue-400/30 text-blue-100';
    }
    @endphp

    <section class="mb-8 p-4">
        <div
            class="relative w-full rounded-[2rem] overflow-hidden shadow-2xl min-h-[220px] flex flex-col justify-center group">

            <div class="absolute inset-0 z-0">
                <img src="{{ $bgImage }}" alt="Background"
                    class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
            </div>

            <div class="absolute inset-0 z-0 bg-gradient-to-r from-blue-900/90 via-blue-900/70 to-transparent"></div>

            <div class="relative z-10 p-8 flex flex-col md:flex-row md:items-end justify-between gap-6">

                <div class="flex-1">
                    <div class="flex items-center space-x-2 text-blue-100 mb-1">
                        <i data-lucide="{{ $greetIcon }}" class="w-4 h-4"></i>
                        <span class="text-sm font-medium tracking-wide uppercase">{{ $greeting }}</span>
                    </div>

                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-6">
                        {{ auth()->user()->name }}
                    </h1>

                    <div class="flex items-center space-x-3 mb-2">
                        <p class="text-sm text-blue-200 font-medium">Available Balance</p>
                        <button id="toggleBalance"
                            class="flex items-center space-x-1 px-2 py-1 rounded-md bg-white/10 hover:bg-white/20 transition-colors text-xs text-white backdrop-blur-md border border-white/10">
                            <i data-lucide="eye-off" id="balanceIcon" class="w-3 h-3"></i>
                            <span id="balanceToggleText">Show</span>
                        </button>
                    </div>

                    <div class="flex items-baseline">
                        <p id="balanceAmount" class="text-2xl md:text-4xl font-bold text-white tracking-tight"
                            data-hidden="yes">
                            •••••••
                        </p>
                    </div>
                </div>

                <div class="flex flex-col items-start md:items-end justify-between h-full space-y-4">

                    <div
                        class="{{ $tierStyle }} px-4 py-1.5 rounded-full border text-xs font-bold uppercase tracking-wider backdrop-blur-md flex items-center gap-2 transition-colors duration-300">
                        <i data-lucide="crown" class="w-3 h-3"></i>
                        <span>{{ ucfirst($member->tier) }} Member</span>
                    </div>

                    <div class="text-left md:text-right">
                        <p class="text-xs text-blue-300 mb-1">Savings Account No.</p>
                        <p class="font-mono text-xl text-white/90 tracking-widest border-b border-white/20 pb-1">
                            {{ $member->savingsAccount->account_number }}
                        </p>
                    </div>
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
        // Re-init icons if you use the Lucide JS watcher
        if(typeof lucide !== 'undefined') { lucide.createIcons(); }
    });
    </script>