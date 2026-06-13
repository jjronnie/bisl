<x-app-layout>

    <x-page-title title="Penalties for {{ $member->name }}" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-red-200 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                Apply Penalty
            </h2>
            <div class="flex flex-col sm:flex-row gap-3">
                <x-penalty-modal
                    :action="route('admin.members.apply-penalty', $member)"
                    type="late_meeting"
                    title="Late Meeting Penalty (UGX 5,000)"
                    triggerText="Late Meeting Penalty (UGX 5,000)"
                    dateLabel="Meeting Date"
                    notesPlaceholder="e.g. Missed the monthly general meeting on this date..."
                    buttonText="Apply Penalty">
                    Apply penalty of UGX 5,000 to <strong>{{ $member->name }}</strong> for late meeting attendance?
                </x-penalty-modal>

                <x-penalty-modal
                    :action="route('admin.members.apply-penalty', $member)"
                    type="loss_identity_card"
                    title="Loss of BGG Identity Card Penalty (UGX 35,000)"
                    triggerText="Loss of BGG Identity Card Penalty (UGX 35,000)"
                    dateLabel="Date of Loss"
                    notesPlaceholder="e.g. Lost during travel on this date..."
                    buttonText="Apply Penalty">
                    Apply penalty of UGX 35,000 to <strong>{{ $member->name }}</strong> for loss of BGG identity card?
                </x-penalty-modal>
            </div>
        </div>

        @if($penalties->isEmpty())
            <x-empty-state icon="scroll-text" message="No penalties recorded." />
        @else
            <x-table :headers="['Date', 'Type', 'Amount', 'Balance Before', 'Balance After', 'Applied By']">
                @foreach($penalties as $penalty)
                    <x-table.row>
                        <x-table.cell>
                            {{ $penalty->created_at->format('d M Y H:i') }}
                        </x-table.cell>
                        <x-table.cell>
                            {{ $penalty->type === 'late_meeting' ? 'Late Meeting' : 'Loss of BGG ID Card' }}
                        </x-table.cell>
                        <x-table.cell>
                            <span class="text-red-600">UGX {{ number_format($penalty->amount) }}</span>
                        </x-table.cell>
                        <x-table.cell>
                            UGX {{ number_format($penalty->balance_before) }}
                        </x-table.cell>
                        <x-table.cell>
                            UGX {{ number_format($penalty->balance_after) }}
                        </x-table.cell>
                        <x-table.cell>
                            {{ $penalty->appliedBy?->name ?? 'System' }}
                        </x-table.cell>
                    </x-table.row>
                @endforeach
            </x-table>

            @if($penalties->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $penalties->links() }}
                </div>
            @endif
        @endif

        <div class="mt-6">
            <a href="{{ route('admin.members.show', $member) }}" class="btn-gray">
                ← Back to Member
            </a>
        </div>

    </div>

</x-app-layout>
