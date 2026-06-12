<x-app-layout>
    <x-page-title title="NSSF Ledger" subtitle="Aggregated NSSF payable account" />

    <div x-data="{ loaded: true, showWithdraw: false }" x-show="loaded" x-transition.duration.500ms class="space-y-6">

        {{-- Account Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-stat-card title="Current Balance" value="UGX {{ number_format($account->balance, 0) }}" icon="clock" sub_title="Total NSSF collected" />
            <x-stat-card title="Total Credited" value="UGX {{ number_format($account->total_credited, 0) }}" icon="arrow-up" sub_title="All payroll deductions" />
            <x-stat-card title="Total Withdrawn" value="UGX {{ number_format($account->total_withdrawn, 0) }}" icon="arrow-down" sub_title="Paid to NSSF" />
        </div>

        {{-- Withdraw Form --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Withdraw from NSSF Account</h2>
                <button @click="showWithdraw = !showWithdraw"
                    class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
                    x-text="showWithdraw ? 'Cancel' : 'New Withdrawal'">
                </button>
            </div>
            <div x-show="showWithdraw" x-collapse.duration.300ms>
                <form method="POST" action="{{ route('admin.payroll.ledgers.withdraw', $account) }}" class="max-w-lg space-y-4">
                    @csrf
                    <div>
                        <x-input-label value="Amount (UGX)" />
                        <x-text-input name="amount" type="number" step="0.01" min="0.01" :max="$account->balance" class="w-full" required />
                    </div>
                    <div>
                        <x-input-label value="Reason / Reference" />
                        <textarea name="reason" rows="2" class="border-gray-300 rounded-md shadow-sm w-full text-sm" placeholder="e.g. Monthly NSSF remittance" required></textarea>
                    </div>
                    <x-primary-button>Record Withdrawal</x-primary-button>
                </form>
            </div>
        </div>

        {{-- Withdrawal History --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Withdrawal History</h2>
            <x-table :headers="['Date', 'Amount', 'Reason', 'Withdrawn By']">
                @forelse ($withdrawals as $w)
                    <x-table.row>
                        <x-table.cell>{{ $w->withdrawn_at->format('d M Y H:i') }}</x-table.cell>
                        <x-table.cell class="font-mono">UGX {{ number_format($w->amount, 0) }}</x-table.cell>
                        <x-table.cell>{{ $w->reason }}</x-table.cell>
                        <x-table.cell>{{ $w->withdrawnBy?->name ?? 'N/A' }}</x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="4" class="text-center py-8 text-gray-500">No withdrawals yet.</x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table>
            <div class="mt-4">{{ $withdrawals->links() }}</div>
        </div>
    </div>
</x-app-layout>
