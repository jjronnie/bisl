<div class="w-80 lg:w-64 bg-primary text-white flex flex-col fixed top-0 left-0 h-screen z-40 lg:z-[10000] transform transition-transform duration-300 -translate-x-full lg:translate-x-0"
    id="sidebar">
    <!-- Sidebar Header -->

    <div class="sidebar-header p-4 ">


        <div class="flex items-center space-x-3">
            <div class="w-14 h-14  rounded-lg flex items-center justify-center text-white font-bold text-lg">
                <span>
                    <a href="{{ url('/') }}">
                        <x-logo />
                    </a>
                </span>
            </div>
            <div class="flex flex-col">
                <span class="text-center whitespace-nowrap text-white font-bold">Bondemala SG</span>

                <span class="text-xs text-center">"Gather to Grow"</span>
            </div>
        </div>
        <button class="lg:hidden p-1 rounded-md hover:bg-blue-900 transition-colors" id="closeSidebar">

            <i data-lucide="x" class="w-4 h-4  "></i>
        </button>
    </div>

    <!-- Scrollable Navigation Area -->



    <div class="flex-1 overflow-y-auto no-scrollbar">
        <nav class="p-4 space-y-1">
            {{-- Dashboard --}}

             <div class="flex items-center gap-3 my-4">
                        <span class="text-xs uppercase tracking-wider text-blue-300 font-semibold whitespace-nowrap">Sacco MGT</span>
                        <hr class="border-blue-800 flex-1">
                    </div>

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4 "></i>
                <span>Dashboard</span>
            </a>
            <div class="space-y-1">


                <a href="{{ route('admin.members.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.members.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="users" class="w-4 h-4 "></i>
                    <span>Members</span>
                </a>

                <a href="{{ route('admin.transactions.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.transactions.*') && !request()->routeIs('admin.transactions.reversal.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="arrow-left-right" class="w-4 h-4 "></i>
                    <span>Transactions</span>
                </a>

                <a href="{{ route('admin.transactions.reversal.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.transactions.reversal.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="rotate-ccw" class="w-4 h-4 "></i>
                    <span>Reversals</span>
                </a>


                <a href="{{ route('admin.loans.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.loans.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="hand-coins" class="w-4 h-4 "></i>
                    <span>Loans</span>
                </a>

                {{-- <a href="{{ route('admin.transfers.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.transfers.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="repeat-2" class="w-4 h-4 "></i>
                    <span>Transfers</span>
                </a>


                <a href="{{ route('admin.reports.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="wallet" class="w-4 h-4 "></i>
                    <span>Accounts</span>
                </a> --}}


                <a href="{{ route('admin.reports.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="bar-chart" class="w-4 h-4"></i>
                    <span>Reports</span>
                </a>

                <a href="{{ route('admin.interest.ledger') }}"
                    class="sidebar-link {{ request()->routeIs('admin.interest.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="percent" class="w-4 h-4"></i>
                    <span>Interest Ledger</span>
                </a>

                @role('superadmin')
                    <div class="flex items-center gap-3 my-4">
                        <span
                            class="text-xs uppercase tracking-wider text-blue-300 font-semibold whitespace-nowrap">Payroll</span>
                        <hr class="border-blue-800 flex-1">
                    </div>

                    <a href="{{ route('admin.payroll.dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('admin.payroll.dashboard') ? 'sidebar-link-active' : '' }}">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                        <span>Payroll Dashboard</span>
                    </a>
                    <a href="{{ route('admin.payroll.profiles.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.payroll.profiles.*') ? 'sidebar-link-active' : '' }}">
                        <i data-lucide="id-card" class="w-4 h-4"></i>
                        <span>Employee Profiles</span>
                    </a>
                    <a href="{{ route('admin.payroll.periods.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.payroll.periods.*') ? 'sidebar-link-active' : '' }}">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        <span>Salary Processing</span>
                    </a>
                    <a href="{{ route('admin.payroll.runs.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.payroll.runs.*') ? 'sidebar-link-active' : '' }}">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        <span>Salary History</span>
                    </a>

                    <div x-data="{ open: {{ request()->routeIs('admin.payroll.ledgers.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open"
                            class="sidebar-link w-full text-left flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <i data-lucide="book-open" class="w-4 h-4"></i>
                                <span>Ledgers</span>
                            </span>
                            <i data-lucide="chevron-down" class="w-3 h-3 transition-transform"
                                :class="open ? 'rotate-0' : '-rotate-90'"></i>
                        </button>
                        <div x-show="open" class="ml-4 space-y-1">
                            <a href="{{ route('admin.payroll.ledgers.tax') }}"
                                class="sidebar-link {{ request()->routeIs('admin.payroll.ledgers.tax*') ? 'sidebar-link-active' : '' }}">
                                <span>Tax Ledger</span>
                            </a>
                            <a href="{{ route('admin.payroll.ledgers.nssf') }}"
                                class="sidebar-link {{ request()->routeIs('admin.payroll.ledgers.nssf*') ? 'sidebar-link-active' : '' }}">
                                <span>NSSF Ledger</span>
                            </a>
                        </div>
                    </div>

                    <div x-data="{ open: {{ request()->routeIs('admin.payroll.settings.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open"
                            class="sidebar-link w-full text-left flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <i data-lucide="settings" class="w-4 h-4"></i>
                                <span>Settings</span>
                            </span>
                            <i data-lucide="chevron-down" class="w-3 h-3 transition-transform"
                                :class="open ? 'rotate-0' : '-rotate-90'"></i>
                        </button>
                        <div x-show="open" class="ml-4 space-y-1">
                            <a href="{{ route('admin.payroll.settings.index') }}"
                                class="sidebar-link {{ request()->routeIs('admin.payroll.settings.index') ? 'sidebar-link-active' : '' }}">
                                <span>General Settings</span>
                            </a>
                            <a href="{{ route('admin.payroll.grades.index') }}"
                                class="sidebar-link {{ request()->routeIs('admin.payroll.grades.*') ? 'sidebar-link-active' : '' }}">
                                <span>Salary Grades</span>
                            </a>
                            <a href="{{ route('admin.payroll.settings.tax-brackets') }}"
                                class="sidebar-link {{ request()->routeIs('admin.payroll.settings.tax-brackets*') ? 'sidebar-link-active' : '' }}">
                                <span>Tax Brackets</span>
                            </a>
                            <a href="{{ route('admin.payroll.settings.allowance-types') }}"
                                class="sidebar-link {{ request()->routeIs('admin.payroll.settings.allowance-types*') ? 'sidebar-link-active' : '' }}">
                                <span>Allowance Types</span>
                            </a>
                        </div>
                    </div>
                @endrole

                @role('superadmin')
                    <div class="flex items-center gap-3 my-4">
                        <span
                            class="text-xs uppercase tracking-wider text-blue-300 font-semibold whitespace-nowrap">SMS</span>
                        <hr class="border-blue-800 flex-1">
                    </div>

                    <a href="{{ route('admin.sms-settings.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.sms-settings.*') ? 'sidebar-link-active' : '' }}">

                        <i data-lucide="message-square" class="w-4 h-4 "></i>
                        <span>SMS Settings</span>
                    </a>

                    <a href="{{ route('admin.bulk-sms.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.bulk-sms.*') ? 'sidebar-link-active' : '' }}">

                        <i data-lucide="users" class="w-4 h-4 "></i>
                        <span>Bulk SMS</span>
                    </a>

                    <a href="{{ route('admin.sms-logs.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.sms-logs.*') ? 'sidebar-link-active' : '' }}">

                        <i data-lucide="history" class="w-4 h-4 "></i>
                        <span>SMS Logs</span>
                    </a>
                @endrole

                 <div class="flex items-center gap-3 my-4">
                        <span class="text-xs uppercase tracking-wider text-blue-300 font-semibold whitespace-nowrap">Admin</span>
                        <hr class="border-blue-800 flex-1">
                    </div>

                <a href="{{ route('admin.admins.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.admins.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="shield-user" class="w-4 h-4 "></i>
                    <span>Admins</span>
                </a>


                <div class="p-4 border-t border-blue-900">

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>

                    <a href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="flex items-center space-x-3 bg-blue-700 hover:bg-blue-800 text-white rounded-lg px-3 py-2 transition-colors no-underline">

                        <i data-lucide="log-out" class="w-4 h-4 text-white"></i>
                        <span class="text-sm  font-medium">Logout</span>
                    </a>
                </div>

            </div>
    </div>
    </nav>

</div>
