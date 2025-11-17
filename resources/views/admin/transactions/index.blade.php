<x-app-layout>

    <x-page-title title="Transactions" subtitle="Manage All Transactions" />

    <!-- Controls -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">

        <div class="flex flex-col sm:flex-row gap-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                </div>
                <input x-model="search" type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="Search by name or A/C No.">
            </div>
        </div>


        <div class="flex gap-3">

            <a class="btn" href="{{ route('admin.transactions.create') }}"> <i data-lucide="plus" class="w-4 h-4 "></i></a>

            <!-- Export to PDF Button -->
            <button class="btn">
                <i data-lucide="file-text" class="w-4 h-4 "></i>
            </button>


            <!-- Export to Excel Button -->
            <button class="btn">
                <i data-lucide="sheet" class="w-4 h-4 "></i>
            </button>
        </div>

    </div>

    <div class="bg-white rounded-lg shadow">

        <x-table :headers="['Date', 'Member', 'Type', 'Reason', 'Amount' ]" showActions="false">
            <div class="p-6 border-b">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1">
                        <i data-lucide="search"
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="text" placeholder="Search transactions..."
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2">
                    </div>
                    <select class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2">
                        <option value="all">All Types</option>
                        <option value="deposit">Deposits</option>
                        <option value="withdrawal">Withdrawals</option>
                        <option value="loan">Loans</option>
                    </select>
                </div>
            </div>
            
            <x-table.row>
                <x-table.cell></x-table.cell>
            </x-table.row>
        </x-table>




</x-app-layout>