    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="fixed top-4 left-4 z-50 lg:hidden p-2 rounded-lg bg-white shadow-lg">
        <i data-lucide="menu" class="w-6 h-6"></i>
    </button>
  
  <aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-40">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold" style="color: hsl(222, 47%, 11%);">Bondemala SG</h2>
            <p class="text-sm text-gray-600">Admin Portal</p>
        </div>
        
        <nav class="p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-50 text-blue-900 font-medium">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="members.html" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span>Members</span>
            </a>
            <a href="transactions.html" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                <i data-lucide="receipt" class="w-5 h-5"></i>
                <span>Transactions</span>
            </a>
            <a href="loans.html" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                <i data-lucide="banknote" class="w-5 h-5"></i>
                <span>Loans</span>
            </a>
            <a href="reports.html" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                <i data-lucide="bar-chart" class="w-5 h-5"></i>
                <span>Reports</span>
            </a>
            <a href="settings.html" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                <i data-lucide="settings" class="w-5 h-5"></i>
                <span>Settings</span>
            </a>
            <a href="login.html" onclick="localStorage.clear()" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors text-red-600">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>