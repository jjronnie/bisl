<x-app-layout>
    <x-page-title title="Reports"
        subtitle="Here you can view Summarized reports of how the Institution is performing" />





    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-5 mb-8 text-center">

           <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Loans by Status</h3>
            <div class="h-64">
                {!! $loanStatusChart->container() !!}
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Loans by Tier</h3>
            <div class="h-64">
                {!! $loanTierChart->container() !!}
            </div>
        </div>


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
    {!! $loanStatusChart->script() !!}
    {!! $loanTierChart->script() !!}

</x-app-layout>