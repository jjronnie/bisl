<x-app-layout>
    <x-page-title title="Add New Sacco Member" />

    <div class=" mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Personal Info --}}
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Personal Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name -->
                <div>
                    <x-input-label for="name" value="Full Name" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                        :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                        :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- <!-- Password -->
                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" value="Confirm Password" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                        class="mt-1 block w-full" required />
                </div> --}}

                <!-- Date of Birth -->
                <div>
                    <x-input-label for="date_of_birth" value="Date of Birth" />
                    <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full"
                        :value="old('date_of_birth')" />
                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                </div>

                <!-- Nationality -->
                <div>
                    <x-input-label for="nationality" value="Nationality" />
                    <x-text-input id="nationality" name="nationality" type="text" class="mt-1 block w-full"
                        :value="old('nationality')" />
                    <x-input-error :messages="$errors->get('nationality')" class="mt-2" />
                </div>

                <!-- Gender -->
                <div>
                    <x-input-label for="gender" value="Gender" />
                    <select name="gender" id="gender" class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="">Select</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                </div>

                <!-- Marital Status -->
                <div>
                    <x-input-label for="marital_status" value="Marital Status" />
                    <select name="marital_status" id="marital_status"
                        class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="">Select</option>
                        <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married</option>
                        <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                    </select>
                    <x-input-error :messages="$errors->get('marital_status')" class="mt-2" />
                </div>

                <!-- National ID -->
                <div>
                    <x-input-label for="national_id_number" value="National ID Number" />
                    <x-text-input id="national_id_number" name="national_id_number" type="text" class="mt-1 block w-full"
                        :value="old('national_id_number')" />
                    <x-input-error :messages="$errors->get('national_id_number')" class="mt-2" />
                </div>

                <!-- Passport Number -->
                <div>
                    <x-input-label for="passport_number" value="Passport Number" />
                    <x-text-input id="passport_number" name="passport_number" type="text" class="mt-1 block w-full"
                        :value="old('passport_number')" />
                    <x-input-error :messages="$errors->get('passport_number')" class="mt-2" />
                </div>

                <!-- Avatar -->
                <div class="md:col-span-2">
                    <x-input-label for="avatar" value="Profile Photo (optional)" />
                    <input id="avatar" name="avatar" type="file" accept="image/*"
                        class="mt-1 block w-full border-gray-300 rounded-md" />
                    <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                </div>
            </div>

            {{-- Contact Info --}}
            <h2 class="text-lg font-semibold text-gray-800 mt-6 mb-4 border-b pb-2">Contact Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Phone1 -->
                <div>
                    <x-input-label for="phone1" value="Primary Phone" />
                    <x-text-input id="phone1" name="phone1" type="text" class="mt-1 block w-full"
                        :value="old('phone1')" required />
                    <x-input-error :messages="$errors->get('phone1')" class="mt-2" />
                </div>

                <!-- Phone2 -->
                <div>
                    <x-input-label for="phone2" value="Secondary Phone (optional)" />
                    <x-text-input id="phone2" name="phone2" type="text" class="mt-1 block w-full"
                        :value="old('phone2')" />
                    <x-input-error :messages="$errors->get('phone2')" class="mt-2" />
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <x-input-label for="address" value="Address" />
                    <textarea id="address" name="address" rows="2" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
            </div>

            {{-- Financial / Declaration --}}
            <h2 class="text-lg font-semibold text-gray-800 mt-6 mb-4 border-b pb-2">Financial Declaration</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="has_existing_savings" value="1"
                            {{ old('has_existing_savings') ? 'checked' : '' }} />
                        <span>Has Existing Savings?</span>
                    </label>
                    <x-input-error :messages="$errors->get('has_existing_savings')" class="mt-2" />
                </div>

                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_currently_in_debt" value="1"
                            {{ old('is_currently_in_debt') ? 'checked' : '' }} />
                        <span>Currently in Debt?</span>
                    </label>
                    <x-input-error :messages="$errors->get('is_currently_in_debt')" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="existing_savings_details" value="Existing Savings Details" />
                    <textarea id="existing_savings_details" name="existing_savings_details" rows="2"
                        class="mt-1 block w-full border-gray-300 rounded-md">{{ old('existing_savings_details') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="debt_details" value="Debt Details" />
                    <textarea id="debt_details" name="debt_details" rows="2"
                        class="mt-1 block w-full border-gray-300 rounded-md">{{ old('debt_details') }}</textarea>
                </div>
            </div>

            {{-- Submit --}}
            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('members.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancel</a>

                <x-primary-button>
                    Create Member
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
