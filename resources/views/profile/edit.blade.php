<x-app-layout>
 

    <div class="py-4">
        <div class=" mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="">
           
                    @include('profile.partials.update-profile-information-form')
            </div>

            <div class="p-4 sm:p-8 bg-white shadow rounded-lg">
              
                    @include('profile.partials.update-password-form')
            </div>


        </div>
    </div>
</x-app-layout>
