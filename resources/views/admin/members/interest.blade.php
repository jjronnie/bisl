<x-app-layout>
    <x-page-title title="Interest Ledgers" />

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

            
                        
                <form action="{{ route('admin.interest.update') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn">
                        Update Monthly Interest
                    </button>
                </form>



                
            </div>

        </div>

    @if($ledgers->isEmpty())
        <x-empty-state message="No interest ledger records found." />
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <x-table :headers="['Member', 'Balance Before', 'Interest Earned', 'Balance After', 'Tier', 'Date']" >
                @foreach($ledgers as $ledger)
                    <x-table.row>
                        <x-table.cell>{{ $ledger->member->name ?? 'N/A' }}</x-table.cell>
                        <x-table.cell>UGX {{ number_format($ledger->balance_before ?? 0) }}</x-table.cell>
                        <x-table.cell>UGX {{ number_format($ledger->interest_amount ?? 0) }}</x-table.cell>
                        <x-table.cell>UGX {{ number_format($ledger->balance_after ?? 0) }}</x-table.cell>
                        <x-table.cell>{{ ucfirst($ledger->tier ?? 'N/A') }}</x-table.cell>
                        <x-table.cell>

                            @if ($ledger->created_at)

                                {{ $ledger->created_at->format('d M Y H:i') }}
                                
                            @endif
                            
                        </x-table.cell>
                    </x-table.row>
                @endforeach
            </x-table>

            {{-- Pagination --}}
            @if($ledgers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $ledgers->links() }}
                </div>
            @endif
        </div>
    @endif
</x-app-layout>
