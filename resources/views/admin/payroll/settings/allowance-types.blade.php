<x-app-layout>
    <x-page-title title="Allowance Types" subtitle="Configure payroll allowance types" />

    <div x-data="{ loaded: true }" x-show="loaded" x-transition.duration.500ms class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        {{-- Add New --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Add Allowance Type</h2>
            <form method="POST" action="{{ route('admin.payroll.settings.allowance-types.store') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <x-input-label value="Name" />
                        <x-text-input name="name" type="text" class="w-full" placeholder="e.g. Housing Allowance" required />
                    </div>
                    <div>
                        <x-input-label value="Code" />
                        <x-text-input name="code" type="text" class="w-full" placeholder="e.g. housing" required />
                    </div>
                    <div>
                        <x-input-label value="Amount (UGX)" />
                        <x-text-input name="amount" type="number" step="0.01" min="0" class="w-full" required />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_taxable" id="is_taxable" value="1" class="rounded border-gray-300">
                        <x-input-label for="is_taxable" value="Taxable" />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_recurring" id="is_recurring" value="1" class="rounded border-gray-300" checked>
                        <x-input-label for="is_recurring" value="Recurring (included every month)" />
                    </div>
                    <x-primary-button class="w-full">Save Allowance</x-primary-button>
                </div>
            </form>
        </div>

        {{-- List --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Allowance Types</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2 px-3 font-medium text-gray-600">Name</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Code</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Amount</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Taxable</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Recurring</th>
                            <th class="py-2 px-3 font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($types as $type)
                            <tr class="border-b hover:bg-gray-50" x-data="{ editing: false, name: '{{ $type->name }}', code: '{{ $type->code }}', amount: '{{ $type->amount }}', is_taxable: {{ $type->is_taxable ? 'true' : 'false' }}, is_recurring: {{ $type->is_recurring ? 'true' : 'false' }} }">
                                {{-- View Mode --}}
                                <td class="py-2 px-3 font-medium" x-show="!editing">{{ $type->name }}</td>
                                <td class="py-2 px-3 text-gray-500" x-show="!editing">{{ $type->code }}</td>
                                <td class="py-2 px-3" x-show="!editing">UGX {{ number_format($type->amount) }}</td>
                                <td class="py-2 px-3" x-show="!editing">
                                    <x-status-badge :status="$type->is_taxable ? 'active' : 'inactive'">
                                        {{ $type->is_taxable ? 'Yes' : 'No' }}
                                    </x-status-badge>
                                </td>
                                <td class="py-2 px-3" x-show="!editing">
                                    <x-status-badge :status="$type->is_recurring ? 'active' : 'inactive'">
                                        {{ $type->is_recurring ? 'Yes' : 'No' }}
                                    </x-status-badge>
                                </td>
                                <td class="py-2 px-3" x-show="!editing">
                                    <button @click="editing = true" class="btn-sm">Edit</button>
                                </td>
                                {{-- Edit Mode --}}
                                <td class="py-2 px-3" x-show="editing">
                                    <input type="text" x-model="name" class="w-full border-gray-300 rounded text-sm">
                                </td>
                                <td class="py-2 px-3" x-show="editing">
                                    <input type="text" x-model="code" class="w-full border-gray-300 rounded text-sm">
                                </td>
                                <td class="py-2 px-3" x-show="editing">
                                    <input type="number" step="0.01" min="0" x-model="amount" class="w-24 border-gray-300 rounded text-sm">
                                </td>
                                <td class="py-2 px-3" x-show="editing">
                                    <input type="checkbox" x-model="is_taxable" class="rounded border-gray-300">
                                </td>
                                <td class="py-2 px-3" x-show="editing">
                                    <input type="checkbox" x-model="is_recurring" class="rounded border-gray-300">
                                </td>
                                <td class="py-2 px-3" x-show="editing">
                                    <form method="POST" :action="'{{ route('admin.payroll.settings.allowance-types.update', $type) }}'" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="name" :value="name">
                                        <input type="hidden" name="code" :value="code">
                                        <input type="hidden" name="amount" :value="amount">
                                        <input type="hidden" name="is_taxable" :value="is_taxable ? '1' : '0'">
                                        <input type="hidden" name="is_recurring" :value="is_recurring ? '1' : '0'">
                                        <button type="submit" class="btn-sm">Save</button>
                                    </form>
                                    <button @click="editing = false" class="btn-gray">Cancel</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">No allowance types configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $types->links() }}</div>
        </div>
    </div>
</x-app-layout>
