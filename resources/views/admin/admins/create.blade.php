<x-slide-form buttonIcon="plus" title="Invite New System Admin">


        <form method="POST" action="{{ route('admin.admins.store' ) }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 ">

            <div>
                <label for="name" class="label"> Name <span class="text-red-600"> *</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="input @error('name') border-red-500 @enderror" />
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

             <div>
                <label for="email" class="label">Email Address<span class="text-red-600"> *</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="input @error('email') border-red-500 @enderror" />
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

      
        </div>

        <div class="flex justify-start space-x-3 mt-6">
            <button type="submit" class="btn">
                Send Invite <i data-lucide="save" class="w-4 h-4 ml-2"></i>
            </button>
        </div>
    </form>



    </x-slide-form>