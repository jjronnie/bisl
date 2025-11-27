<x-slide-form button-text="Record New Transfer" title="Enter Transfer Details">

    <form method="POST" action="{{ route('admin.transfers.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

            {{-- From Account --}}
            <div>
                <x-input-label for="from_account" value="From Account" />
                <select name="from_account" id="from_account" required
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                    <option value="" disabled selected>Select account...</option>

                    @foreach ($accountFields as $key => $label)
                        <option value="{{ $key }}" {{ old('from_account') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <x-input-error :messages="$errors->get('from_account')" class="mt-2" />
            </div>

            {{-- To Account --}}
            <div>
                <x-input-label for="to_account" value="To Account" />
                <select name="to_account" id="to_account" required
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                    <option value="" disabled selected>Select account...</option>

                    @foreach ($accountFields as $key => $label)
                        <option value="{{ $key }}" {{ old('to_account') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <x-input-error :messages="$errors->get('to_account')" class="mt-2" />
            </div>

            {{-- Amount --}}
            <div class="md:col-span-2">
                <x-input-label for="amount" value="Amount" />
                <div class="mt-1 relative rounded-lg shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 text-sm">UGX</span>
                    </div>

                    <input type="number" name="amount" min="1" required placeholder="0.00" value="{{ old('amount') }}"
                        class="block w-full pl-12 pr-3 py-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                </div>
                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
            </div>

            {{-- Reason --}}
            <div class="md:col-span-2">
                <x-input-label for="reason" value="Reason" />
                <textarea name="reason" id="reason" rows="3" required
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('reason') }}</textarea>
                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
            </div>

        </div>

        {{-- Actions --}}
        <div class="flex justify-end space-x-3">
            <x-confirmation-checkbox />
            <button type="submit" class="btn">
                Save <i data-lucide="save" class="w-4 h-4 ml-2"></i>
            </button>
        </div>

    </form>

</x-slide-form>
