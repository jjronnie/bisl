<x-app-layout>
    <x-page-title title="Reports"
        subtitle="Here you can view Summarized reports of how the Institution is performing" />

    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        <!-- card -->
        <x-stat-card title="Total Members" value="{{ $totalMembers }}" icon="users" />
        <x-stat-card title="Total Savings" value="UGX {{ number_format($totalBalance) }}" icon="coins" />
        <x-stat-card title="Loan Protection Fund" value="" icon="shield" />
        <x-stat-card title="Total Accessible Balance" value="" icon="dollar-sign" />

        <x-stat-card title="Outstanding Loans" value="{{ $totalOutstandingLoans ?? '-' }}" icon="clipboard-clock" />
        <x-stat-card title="Loan Portfolio" value="UGX {{ number_format($totalOutstandingAmount) }}" icon="wallet" />
        <x-stat-card title="Average Savings" value="UGX {{ $averageBalanceFormatted }}" icon="slash" />

    </div>



    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-5 mb-8 text-center">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Members by Tier</h3>
            <div class="h-64">


                {!! $membersTierChart->container() !!}
            </div>
        </div>

         <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Members by Gender</h3>
            <div class="h-64">
                {!! $genderChart->container() !!}
            </div>
        </div>



        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Transactions by Method</h3>
            <div class="h-64">
                {!! $transactionsMethodChart->container() !!}
            </div>
        </div>

        



<div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Transactions This Year (Monthly)</h3>
            <div class="h-64">
              {!! $transactionsChart->container() !!}
            </div>
        </div>
     </div>



    <!-- Chart.js script -->
    {!! $membersTierChart->script() !!}
    {!! $transactionsMethodChart->script() !!}
    {!! $genderChart->script() !!}
    {!! $transactionsChart->script() !!}

</x-app-layout>