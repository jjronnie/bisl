<div class="w-80 lg:w-64 bg-primary text-white flex flex-col fixed top-0 left-0 h-screen z-40 lg:z-[10000] transform transition-transform duration-300 -translate-x-full lg:translate-x-0"
    id="sidebar">
    <!-- Sidebar Header -->

       <div class="sidebar-header p-4 border-b border-blue-900">

        
        <div class="flex items-center space-x-3">
            <div class="w-14 h-14  rounded-lg flex items-center justify-center text-white font-bold text-lg">
                <span>
                    <a href="{{ url('/') }}">
                        <x-logo  />
                    </a>
                </span>
            </div>
            <div class="flex flex-col">
                <span class="text-center whitespace-nowrap text-white font-bold">Bondemala SG</span>

                <span class="text-xs text-center">"Gather to Grow"</span>
            </div>
        </div>
        <button class="lg:hidden p-1 rounded-md hover:bg-blue-900 transition-colors" id="closeSidebar">

            <i data-lucide="x" class="w-4 h-4  text-white"></i>
        </button>
    </div>

    <!-- Scrollable Navigation Area -->



    <div class="flex-1 overflow-y-auto no-scrollbar">
        <nav class="p-4 space-y-1">
            {{-- Dashboard --}}

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4 text-white"></i>
                <span>Dashboard</span>
            </a>
            <div class="space-y-1">


                <a href="{{ route('admin.members.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.members.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="users" class="w-4 h-4 text-white"></i>
                    <span>Members</span>
                </a>

                   <a href="{{ route('admin.transactions.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.transactions.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="receipt" class="w-4 h-4 text-white"></i>
                    <span>Transactions</span>
                </a>


                 <a href="{{ route('admin.admins.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.admins.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="banknote" class="w-4 h-4 text-white"></i>
                    <span>Loans</span>
                </a>


               


                  <a href="{{ route('admin.admins.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.admins.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="bar-chart" class="w-4 h-4 text-white"></i>
                    <span>Reports</span>
                </a>

                  <a href="{{ route('admin.admins.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.admins.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="settings" class="w-4 h-4 text-white"></i>
                    <span>Settings</span>
                </a>



                 <a href="{{ route('admin.admins.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.admins.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="shield-user" class="w-4 h-4 text-white"></i>
                    <span>Admins</span>
                </a>

                  <a href="{{ route('admin.admins.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.admins.*') ? 'sidebar-link-active' : '' }}">

                    <i data-lucide="log-out" class="w-4 h-4 text-white"></i>
                    <span>Logout</span>
                </a>


            </div>
    </div>
    </nav>

</div>