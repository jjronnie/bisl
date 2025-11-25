{{-- DESKTOP SIDEBAR --}}
<div class="hidden lg:flex w-80 lg:w-64 bg-primary text-white flex-col fixed top-0 left-0 h-screen z-40 lg:z-[10000] transform transition-transform duration-300 -translate-x-full lg:translate-x-0"
    id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header p-4 border-b border-blue-900">
        <div class="flex items-center space-x-3">
            <div class="w-14 h-14 rounded-lg flex items-center justify-center text-white font-bold text-lg">
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
            <i data-lucide="x" class="w-4 h-4 text-white"></i>
        </button>
    </div>

    <!-- Scrollable Navigation Area -->
    <div class="flex-1 overflow-y-auto no-scrollbar">
        <nav class="p-4 space-y-1">
            <a href="{{ route('member.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('member.dashboard') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4 "></i>
                <span>Dashboard</span>
            </a>

        

            <a href="{{ route('member.transactions') }}"
                class="sidebar-link {{ request()->routeIs('member.transactions') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="arrow-left-right" class="w-4 h-4 "></i>
                <span>Transactions</span>
            </a>

                <a href="{{ route('member.notifications') }}"
                class="sidebar-link {{ request()->routeIs('member.notifications') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="bell" class="w-4 h-4 "></i>
                <span>Notifications</span>
            </a>

            <a href="{{ route('profile.edit') }}"
                class="sidebar-link {{ request()->routeIs('profile.edit') ? 'sidebar-link-active' : '' }}">
                <i data-lucide="user" class="w-4 h-4 "></i>
                <span>Profile</span>
            </a>

        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-blue-900">
            <a href="/support.html"
                class="flex items-center space-x-3 bg-blue-700 hover:bg-blue-800 text-white rounded-lg px-3 py-2 transition-colors no-underline">
                <i data-lucide="ticket" class="w-4 h-4 text-white"></i>
                <span class="text-sm font-medium">Raise a Ticket</span>
            </a>
        </div>
    </div>
</div>

{{-- MOBILE SIDEBAR --}}
<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200  z-50">
    <div class="flex justify-around items-center h-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">



        <a href="{{ route('member.dashboard') }}" class="flex flex-col items-center justify-center w-full p-2 transition-colors duration-200
   {{ request()->routeIs('member.dashboard') ? 'text-blue-800' : 'text-gray-500 hover:text-blue-700' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span class="text-xs font-medium mt-1">Dashboard</span>
        </a>




        <a href="{{ route('member.transactions') }}" class="flex flex-col items-center justify-center w-full p-2 transition-colors duration-200
   {{ request()->routeIs('member.transactions') ? 'text-blue-800' : 'text-gray-500 hover:text-blue-700' }}">
            <i data-lucide="arrow-left-right" class="w-5 h-5"></i>
            <span class="text-xs font-medium mt-1">Transactions</span>
        </a>

        

        <a href="{{ route('member.notifications') }}" class="relative flex flex-col items-center justify-center w-full p-2 transition-colors duration-200
   {{ request()->routeIs('member.notifications') ? 'text-blue-800' : 'text-gray-500 hover:text-blue-700' }}">

            <i data-lucide="bell" class="w-5 h-5"></i>

            <!-- Badge -->
            <span
                class="absolute top-1 right-3 bg-red-600 text-white text-[10px] font-semibold px-1.5 py-[1px] rounded-full">
                0
            </span>

            <span class="text-xs font-medium mt-1">Notifications</span>
        </a>


        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center w-full p-2 transition-colors duration-200
   {{ request()->routeIs('profile.*') ? 'text-blue-800' : 'text-gray-500 hover:text-blue-700' }}">
            <i data-lucide="user-round" class="w-5 h-5"></i>
            <span class="text-xs font-medium mt-1">Profile</span>
        </a>




    </div>
</nav>