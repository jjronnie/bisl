<x-app-layout>
    <x-page-title title="New Bulk SMS Broadcast" subtitle="Send SMS to multiple members" />

    @php
        $placeholders = [
            ['key' => '{{'.'first_name}}', 'label' => 'First Name'],
            ['key' => '{{'.'last_name}}', 'label' => 'Last Name'],
            ['key' => '{{'.'name}}', 'label' => 'Full Name'],

        ];
    @endphp

    <script>
        window.smsPlaceholders = @json($placeholders);
        window.membersData = @json($members->map(fn($m) => ['id' => $m->id, 'name' => $m->name, 'phone1' => $m->phone1]));
    </script>

    <div x-data="{
        selectedMembers: [],
        message: '',
        recipientsCount: 0,
        searchQuery: '',
        placeholders: window.smsPlaceholders || [],
        members: window.membersData || [],
        get filteredMembers() {
            if (!this.searchQuery) return this.members;
            const q = this.searchQuery.toLowerCase();
            return this.members.filter(m =>
                m.name.toLowerCase().includes(q) ||
                (m.phone1 && m.phone1.includes(q))
            );
        },
        checkAllFiltered() {
            this.filteredMembers.filter(m => m.phone1).forEach(m => {
                if (!this.selectedMembers.includes(m.id)) {
                    this.selectedMembers.push(m.id);
                }
            });
            this.updateSelected();
        },
        uncheckAll() {
            this.selectedMembers = [];
            this.updateSelected();
        },
        updateSelected() {
            this.recipientsCount = this.selectedMembers.length;
            this.$refs.memberIdsContainer.innerHTML = '';
            this.selectedMembers.forEach(id => {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'member_ids[]';
                input.value = id;
                this.$refs.memberIdsContainer.appendChild(input);
            });
        },
        getSmsParts() {
            return Math.ceil(this.message.length / 160) || 0;
        },
        insertPlaceholder(placeholder) {
            const textarea = document.getElementById('message');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const before = this.message.substring(0, start);
            const after = this.message.substring(end);
            this.message = before + placeholder + after;
            this.$nextTick(() => {
                textarea.focus();
                textarea.setSelectionRange(start + placeholder.length, start + placeholder.length);
            });
        },
        isSelected(id) {
            return this.selectedMembers.includes(id);
        },
        toggleMember(id) {
            if (!this.members.find(m => m.id == id)?.phone1) return;
            const idx = this.selectedMembers.indexOf(id);
            if (idx > -1) {
                this.selectedMembers.splice(idx, 1);
            } else {
                this.selectedMembers.push(id);
            }
            this.updateSelected();
        },
    }" class="space-y-6" x-init="updateSelected()">

        @csrf

        @if($activeCampaign)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800">Campaign in Progress</h3>
                    <p class="text-sm text-blue-600 mt-1">
                        {{ $activeCampaign->total_recipients }} recipients -
                        <a href="{{ route('admin.bulk-sms.show', $activeCampaign->id) }}" class="underline">View progress</a>
                    </p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium">Select Recipients</h3>
                    <div class="flex gap-2">
                        <button type="button" @click="checkAll()" class="text-sm text-blue-600 hover:text-blue-800">Select All</button>
                        <span class="text-gray-300">|</span>
                        <button type="button" @click="uncheckAll()" class="text-sm text-gray-600 hover:text-gray-800">Clear</button>
                    </div>
                </div>

                <div class="mb-4 flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                    <span class="text-sm text-gray-600">Selected:</span>
                    <span class="font-semibold text-gray-900" x-text="recipientsCount + ' members'"></span>
                </div>

                <div class="mb-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <input type="text" x-model="searchQuery"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="Search by name or phone...">
                    </div>
                </div>

                <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" @change="checkAllFiltered()" class="rounded border-gray-300">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="member in filteredMembers" :key="member.id">
                                <tr :class="!member.phone1 ? 'bg-gray-100 opacity-60' : ''">
                                    <td class="px-4 py-3">
                                        <input type="checkbox"
                                            :value="member.id"
                                            :checked="isSelected(member.id)"
                                            @change="toggleMember(member.id)"
                                            class="rounded border-gray-300"
                                            :disabled="!member.phone1">
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <span x-text="member.name"></span>
                                        <template x-if="!member.phone1">
                                            <span class="text-xs text-red-500 block">No phone number</span>
                                        </template>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500" x-text="member.phone1 || 'N/A'"></td>
                                </tr>
                            </template>
                            <template x-if="filteredMembers.length === 0">
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">No members found</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.bulk-sms.send') }}" class="bg-white rounded-lg shadow p-6">
                @csrf
                <div x-ref="memberIdsContainer"></div>

                <h3 class="text-lg font-medium mb-4">Compose Message</h3>

                <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex items-start">
                        <i data-lucide="info" class="w-5 h-5 text-yellow-600 mt-0.5 mr-2"></i>
                        <div>
                            <p class="text-sm text-yellow-800 font-medium">160 Characters = 1 SMS</p>
                            <p class="text-xs text-yellow-700 mt-1">
                                Keep messages short and clear to minimize costs.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-start">
                        <div>
                            <p class="text-sm text-blue-800 font-medium">Click to Insert Variable</p>

                            <div class="flex flex-wrap gap-2">
                                <template x-for="placeholder in placeholders" :key="placeholder.key">
                                    <button type="button" @click="insertPlaceholder(placeholder.key)"
                                        class="px-3 py-1.5 bg-white border border-blue-300 rounded-full text-sm text-blue-700 hover:bg-blue-100 transition">
                                        <span x-text="placeholder.label"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea x-model="message" name="message" id="message" rows="6" maxlength="612"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Type your message here... Click a field above to insert it."></textarea>
                </div>

                <div class="text-sm mb-6 space-y-1">
                    <div class="text-gray-500">
                        <span x-text="message.length"></span> / 612 characters
                    </div>
                    <div class="text-gray-500">
                        <span x-text="getSmsParts()"></span> SMS
                        <span x-show="getSmsParts() > 1" class="text-orange-600">(long message)</span>
                    </div>
                </div>

                <x-confirmation-checkbox/>

                <div class="flex justify-start mt-2">
                    <button type="submit"
                        :disabled="selectedMembers.length === 0 || !message.trim()"
                        class="btn disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                        Send Bulk SMS (<span x-text="recipientsCount"></span>)
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
