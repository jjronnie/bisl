<x-app-layout>
    <x-page-title title="Payroll Periods" subtitle="Manage payroll processing periods" />

    <div class="mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        <div class="flex justify-between items-center mb-4">
            <p class="text-gray-600">Total: {{ $periods->total() }} periods</p>
            <a href="{{ route('admin.payroll.periods.create') }}" class="btn">New Period</a>
        </div>

        <x-table :headers="['Period', 'Month', 'Year', 'Status', 'Processed At', ]" :showActions="true">
            @foreach ($periods as $period)
                <x-table.row>
                    <x-table.cell>{{ $period->month }}/{{ $period->year }}</x-table.cell>
                    <x-table.cell>{{ date('F', mktime(0, 0, 0, $period->month, 1)) }}</x-table.cell>
                    <x-table.cell>{{ $period->year }}</x-table.cell>
                    <x-table.cell>
                        <x-status-badge :status="$period->status === 'completed' ? 'active' : ($period->status === 'processing' ? 'pending' : ($period->status === 'draft' ? 'info' : 'inactive'))">
                            {{ $period->status === 'completed' ? 'Closed' : ucfirst($period->status) }}
                        </x-status-badge>
                    </x-table.cell>
                    <x-table.cell>{{ $period->processed_at?->format('d M Y H:i') ?? '-' }}</x-table.cell>
                    <x-table.cell>
                        <div class="flex space-x-2">
                            <a class="btn" href="{{ route('admin.payroll.periods.show', $period->id) }}">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @endforeach
        </x-table>

        <div class="mt-4">
            {{ $periods->links() }}
        </div>
    </div>
</x-app-layout>
