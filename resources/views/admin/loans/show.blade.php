<x-app-layout>
      <x-page-title title="Loan #{{ $loan->loan_number }} Details" />

      {{-- Success/Error Message Display --}}
      @if(session('success'))
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
      </div>

      @endif
      @if(session('error'))
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
      </div>
      @endif

      {{-- Loan Status and Management Actions --}}
      <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <div class="flex flex-wrap justify-between items-center mb-4 border-b pb-4">
                  <h2 class="text-xl font-semibold">
                        Current Status:
                        <span class="
                @if($loan->status === 'pending') text-yellow-600
                @elseif($loan->status === 'approved') text-indigo-600
                @elseif($loan->status === 'disbursed' || $loan->status === 'active') text-green-600
                @elseif($loan->status === 'defaulted' || $loan->status === 'default_pending') text-red-600
                @else text-gray-500
                @endif
            ">
                              {{ ucfirst($loan->status) }}
                        </span>
                  </h2>

                  <div class="flex space-x-3">
                        {{-- PENDING Actions: Approve / Reject --}}
                        @if($loan->status === 'pending')

                        <x-confirm-modal :action="route('admin.loans.approve', $loan)"
                              warning="Are you sure you want to Approve This Loan? This action cannot be undone."
                              method="POST" triggerText="Approve Loan" triggerClass="btn-success" />

                        <x-confirm-modal :action="route('admin.loans.reject', $loan)"
                              warning="Are you sure you want to Reject This Loan? This action cannot be undone."
                              method="POST" triggerText="Reject  Loan" triggerClass="btn-danger" />








                        @endif

                        {{-- APPROVED Actions: Disburse --}}
                        @if($loan->status === 'approved')
                     

                        <x-confirm-modal :action="route('admin.loans.disburse', $loan)"
                              warning="Are you sure you want to Disburse Funds? This action cannot be undone."
                              method="POST" triggerText=" Disburse Funds" triggerClass="btn" />


                        @endif

                        {{-- ACTIVE / DEFAULTED Actions: Log Payment / Default Status --}}
                        @if($loan->status === 'active' || $loan->status === 'defaulted' || $loan->status ===
                        'default_pending')
                        <a href="{{ route('admin.payments.create', ['loan_id' => $loan->id]) }}"
                              class="btn">
                              Log Payment
                        </a>
                        @endif
                  </div>
            </div>

            {{-- Basic Loan Information Table --}}
            <h3 class="text-lg font-semibold mb-3">Summary</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                  <p><strong>Member:</strong> {{ $loan->member->name }} </p>
                  <p><strong>Tier:</strong> ({{ ucfirst($loan->member->tier) }})</p>
                  <p><strong>Amount:</strong> UGX {{ number_format($loan->amount, 0) }}</p>
                  <p><strong>Rate (Annual):</strong> {{ $loan->interest_rate }}%</p>
                  <p><strong>Duration:</strong> {{ $loan->duration_months }} Months</p>
                  <p><strong>Type:</strong> {{ ucfirst($loan->loan_type) }}</p>
                  <p><strong>Application Date:</strong> {{ $loan->application_date->format('Y-m-d') }}</p>
                  <p><strong>Approval Date:</strong> {{ $loan->approval_date?->format('Y-m-d') ?? 'N/A' }}</p>
                  <p><strong>Disbursement Date:</strong> {{ $loan->disbursement_date?->format('Y-m-d') ?? 'N/A' }}</p>
                  <p><strong>Maturity Date:</strong> {{ $loan->due_date?->format('Y-m-d') ?? 'Calculated on
                        Disbursement' }}</p>
                  <p class="col-span-full"><strong>Purpose:</strong> {{ $loan->purpose }}</p>
                  <p class="col-span-full"><strong>Notes:</strong> {{ $loan->notes ?? 'None' }}</p>
            </div>
      </div>

      {{-- Amortization Schedule --}}
      <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4">Amortization Schedule (Reducing Balance)</h3>

            <x-table :headers="[
        '#', 
        'Due Date', 
        'Starting Balance', 
        'EMI (Total)', 
        'Principal', 
        'Interest', 
        'Penalty', 
        'Ending Balance', 
        'Payment Status'
    ]" showActions="false">

                  @forelse ($loan->installments as $installment)
                  <x-table.row
                        class="@if($installment->status === 'paid') bg-green-50 @elseif($installment->status === 'defaulted') bg-red-50 @endif">
                        <x-table.cell>{{ $installment->installment_number }}</x-table.cell>
                        <x-table.cell>{{ $installment->due_date->format('Y-m-d') }}</x-table.cell>
                        <x-table.cell>UGX {{ number_format($installment->starting_balance, 2) }}</x-table.cell>
                        <x-table.cell>UGX {{ number_format($installment->total_amount, 2) }}</x-table.cell>
                        <x-table.cell>UGX {{ number_format($installment->principal_amount, 2) }}</x-table.cell>
                        <x-table.cell>UGX {{ number_format($installment->interest_amount, 2) }}</x-table.cell>
                        <x-table.cell>UGX {{ number_format($installment->penalty_amount, 2) }}</x-table.cell>
                        <x-table.cell>UGX {{ number_format($installment->ending_balance, 2) }}</x-table.cell>
                        <x-table.cell>
                              <span class="p-1 rounded text-xs font-semibold 
                        @if($installment->status === 'paid') bg-green-200 text-green-800
                        @elseif($installment->status === 'defaulted') bg-red-200 text-red-800
                        @else bg-yellow-200 text-yellow-800
                        @endif
                    ">
                                    {{ ucfirst($installment->status) }}
                              </span>
                        </x-table.cell>
                  </x-table.row>
                  @empty
                  <x-table.row>
                        <x-table.cell colspan="9" class="text-center">No installment schedule found.</x-table.cell>
                  </x-table.row>
                  @endforelse
            </x-table>
      </div>


</x-app-layout>