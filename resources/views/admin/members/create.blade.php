<x-app-layout>
    <x-page-title title="Add New Sacco Member" />

    <div class=" mx-auto bg-white p-6 rounded-xl shadow-sm mt-6">
        <form method="POST" action="{{ route('admin.members.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Personal Info --}}
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Personal Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Name -->
                <div>
                    <x-input-label for="name" value="Full Name" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')"
                        required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')"
                        required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- opening_balance --}}
                <div>
                    <x-input-label for="opening_balance" value="Opening Balance" />
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm sm:text-base">UGX</span>
                        </div>

                        <input type="number" name="opening_balance" min="1" placeholder="0.00"
                            value="{{ old('opening_balance') }}"
                            class="block w-full pl-12 pr-3 py-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base" />
                    </div>
                    <x-input-error :messages="$errors->get('opening_balance')" class="mt-2" />
                </div>


                <div>
                    <x-input-label for="loan_protection_fund" value="Loan Protection Fund" />
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm sm:text-base">UGX</span>
                        </div>

                        <input type="number" name="loan_protection_fund" min="1" placeholder="0.00"
                            value="{{ old('loan_protection_fund') }}"
                            class="block w-full pl-12 pr-3 py-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base" />
                    </div>
                    <x-input-error :messages="$errors->get('loan_protection_fund')" class="mt-2" />
                </div>


            
                <div>
                    <x-input-label for="membership_fee" value="Membership Fee" />
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm sm:text-base">UGX</span>
                        </div>

                        <input type="number" name="membership_fee" min="1" placeholder="0.00"
                            value="{{ old('membership_fee') }}"
                            class="block w-full pl-12 pr-3 py-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base" />
                    </div>
                    <x-input-error :messages="$errors->get('membership_fee')" class="mt-2" />
                </div>



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
                        <option value="" selected disabled>Select</option>
                        <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Female</option>
                    </select>
                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                </div>

                <!-- Marital Status -->
                <div>
                    <x-input-label for="marital_status" value="Marital Status" />
                    <select name="marital_status" id="marital_status"
                        class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="" selected disabled>Select</option>
                        <option value="single" {{ old('marital_status')=='single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('marital_status')=='married' ? 'selected' : '' }}>Married
                        </option>
                        <option value="divorced" {{ old('marital_status')=='divorced' ? 'selected' : '' }}>Divorced
                        </option>
                        <option value="widowed" {{ old('marital_status')=='widowed' ? 'selected' : '' }}>Widowed
                        </option>
                    </select>
                    <x-input-error :messages="$errors->get('marital_status')" class="mt-2" />
                </div>

                <!-- National ID -->
                <div>
                    <x-input-label for="national_id_number" value="National ID Number" />
                    <x-text-input id="national_id_number" name="national_id_number" type="text"
                        class="mt-1 block w-full" :value="old('national_id_number')" />
                    <x-input-error :messages="$errors->get('national_id_number')" class="mt-2" />
                </div>

                <!-- Passport Number -->
                <div>
                    <x-input-label for="passport_number" value="Passport Number" />
                    <x-text-input id="passport_number" name="passport_number" type="text" class="mt-1 block w-full"
                        :value="old('passport_number')" />
                    <x-input-error :messages="$errors->get('passport_number')" class="mt-2" />
                </div>


            </div>

            {{-- Contact Info --}}
            <h2 class="text-lg font-semibold text-gray-800 mt-6 mb-4 border-b pb-2">Contact Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Phone1 -->
                <div>
                    <x-input-label for="phone1" value="Primary Phone" />
                    <x-text-input id="phone1" name="phone1" type="text" class="mt-1 block w-full" :value="old('phone1')"
                        required />
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
                <div>
                    <x-input-label for="address" value="Address" />
                    <textarea id="address" name="address" rows="2"
                        class="mt-1 block w-full border-gray-300 rounded-md">{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

             <!-- Avatar -->
<div>
    <x-input-label for="avatar" value="Profile Photo (optional)" />
    <input id="avatar" name="avatar" type="file" accept="image/*"
           class="mt-1 block w-full border-gray-300 rounded-md" 
           onchange="previewAvatar(event)" />

    <!-- Preview -->
    <div class="mt-2">
        <img id="avatarPreview" src="#" alt="Avatar Preview" class="hidden w-24 h-24 object-cover rounded-full border" />
    </div>

    <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
</div>

<script>
function previewAvatar(event) {
    const input = event.target;
    const preview = document.getElementById('avatarPreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '#';
        preview.classList.add('hidden');
    }
}
</script>


            </div>


            {{-- Submit --}}
            <div class="mt-6 flex items-center justify-end space-x-3">

                <x-confirmation-checkbox />
                <button class="btn" type="submit">Create Member</button>
            </div>
        </form>
    </div>
</x-app-layout>