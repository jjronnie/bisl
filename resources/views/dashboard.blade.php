<x-app-layout>

<x-page-title title="Welcome Back {{ auth()->user()->name }}"/>
 
   <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
    <!-- card -->
    <x-stat-card title="Total Members" value="150" icon="users" sub_title="↑ 12% from last month"  />
    <x-stat-card title="Total Members" value="150" icon="users" sub_title="↑ 12% from last month"  />
    <x-stat-card title="Total Members" value="150" icon="users" sub_title="↑ 12% from last month"  />
    <x-stat-card title="Total Members" value="150" icon="users" sub_title="↑ 12% from last month"  />

</div>

       


        <!-- Charts -->
        {{-- <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Savings Trend</h3>
                <canvas id="savingsChart"></canvas>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Loans Overview</h3>
                <canvas id="loansChart"></canvas>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold">Recent Transactions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">John Doe</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Deposit</span></td>
                            <td class="px-6 py-4 font-semibold text-green-600">KES 5,000</td>
                            <td class="px-6 py-4 text-gray-600">2025-10-15</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">Jane Smith</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Loan</span></td>
                            <td class="px-6 py-4 font-semibold text-orange-600">KES 50,000</td>
                            <td class="px-6 py-4 text-gray-600">2025-10-14</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Approved</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> --}} 









</x-app-layout>