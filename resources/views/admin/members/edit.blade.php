<x-app-layout>
    {{-- ASSUMPTION: The member record is passed to this view as $member --}}
    <x-page-title title="Edit Sacco Member: {{ $member->name }}" />

    <div class="mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        <form method="POST" action="{{ route('admin.members.update', $member) }}" enctype="multipart/form-data">
            @csrf
            {{-- Required to spoof the PUT/PATCH method in Laravel --}}
            @method('PUT')

            {{-- Personal Info --}}
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Personal Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name -->
                <div>
                    <x-input-label for="name" value="Full Name" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                        :value="old('name', $member->name)" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                        :value="old('email', $member->user->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>


                <!-- Date of Birth -->
                <div>
                    <x-input-label for="date_of_birth" value="Date of Birth" />
                    <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full"
                        {{-- FIX: Explicitly format the date to YYYY-MM-DD for HTML date input --}}
                        :value="old('date_of_birth', $member->date_of_birth?->format('Y-m-d'))" />
                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                </div>

                <!-- Nationality -->
                <div>
                    <x-input-label for="nationality" value="Nationality" />
                    <x-text-input id="nationality" name="nationality" type="text" class="mt-1 block w-full"
                        :value="old('nationality', $member->nationality)" />
                    <x-input-error :messages="$errors->get('nationality')" class="mt-2" />
                </div>

                <!-- Gender -->
                <div>
                    <x-input-label for="gender" value="Gender" />
                    <select name="gender" id="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select</option>
                        <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                </div>

                <!-- Marital Status -->
                <div>
                    <x-input-label for="marital_status" value="Marital Status" />
                    <select name="marital_status" id="marital_status"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select</option>
                        <option value="single" {{ old('marital_status', $member->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('marital_status', $member->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                        <option value="divorced" {{ old('marital_status', $member->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="widowed" {{ old('marital_status', $member->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                    </select>
                    <x-input-error :messages="$errors->get('marital_status')" class="mt-2" />
                </div>

                <!-- National ID -->
                <div>
                    <x-input-label for="national_id_number" value="National ID Number" />
                    <x-text-input id="national_id_number" name="national_id_number" type="text" class="mt-1 block w-full"
                        :value="old('national_id_number', $member->national_id_number)" />
                    <x-input-error :messages="$errors->get('national_id_number')" class="mt-2" />
                </div>

                <!-- Passport Number -->
                <div>
                    <x-input-label for="passport_number" value="Passport Number" />
                    <x-text-input id="passport_number" name="passport_number" type="text" class="mt-1 block w-full"
                        :value="old('passport_number', $member->passport_number)" />
                    <x-input-error :messages="$errors->get('passport_number')" class="mt-2" />
                </div>

                <!-- Avatar -->
                <div class="md:col-span-2">
                    <x-input-label for="avatar" value="Profile Photo (optional) - Leave blank to keep current" />
                    <input id="avatar" name="avatar" type="file" accept="image/*"
                        class="mt-1 block w-full border-gray-300 rounded-md" />
                    <x-input-error :messages="$errors->get('avatar')" class="mt-2" />

                    @if ($member->avatar)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Current Photo:</p>
                            {{-- Assuming the $member->avatar holds the path/URL to the image --}}
                            <img src="{{ asset('storage/' . $member->avatar) }}" alt="Current Avatar"
                                class="w-16 h-16 object-cover rounded-full mt-1 border border-gray-200">
                        </div>
                    @endif
                </div>
            </div>

            {{-- Contact Info --}}
            <h2 class="text-lg font-semibold text-gray-800 mt-6 mb-4 border-b pb-2">Contact Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Phone1 -->
                <div>
                    <x-input-label for="phone1" value="Primary Phone" />
                    <x-text-input id="phone1" name="phone1" type="text" class="mt-1 block w-full"
                        :value="old('phone1', $member->phone1)" required />
                    <x-input-error :messages="$errors->get('phone1')" class="mt-2" />
                </div>

                <!-- Phone2 -->
                <div>
                    <x-input-label for="phone2" value="Secondary Phone (optional)" />
                    <x-text-input id="phone2" name="phone2" type="text" class="mt-1 block w-full"
                        :value="old('phone2', $member->phone2)" />
                    <x-input-error :messages="$errors->get('phone2')" class="mt-2" />
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <x-input-label for="address" value="Address" />
                    <textarea id="address" name="address" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('address', $member->address) }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
            </div>

         

            {{-- Submit --}}
            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.members.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>

                <x-primary-button>
                    Update Member Details
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
