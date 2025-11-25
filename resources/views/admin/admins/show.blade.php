<x-slide-form buttonIcon="eye" title=" {{ $admin->name }}">

      




      <div class="bg-white p-6 ">
            <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <x-transaction-detail title="Name" value="{{ $admin->name }}" />
                  <x-transaction-detail title="Email" value="{{ $admin->email }}" />
                  <x-transaction-detail title="Role" value="{{ ucfirst($admin->getRoleNames()->implode(', ') )}}" />
                  <x-transaction-detail title="Invite Date" value="   {{ $admin->created_at->format('d M Y H:i') }}" />
                  <x-transaction-detail title="Invited by" value=" {{ $admin->creator->name ?? 'System' }}" />

                  @php
                  $verified = $admin
                  @endphp

                  <x-transaction-detail title="Verified"
                        value="{{ $verified && $verified->email_verified_at && !$verified->must_change_password ? 'Yes' : 'No' }}" />
                      

                   


            </dl>
      </div>


      @if($admin->status === 'active')
      <x-confirm-modal :action="route('admin.suspend', $admin)"
            warning="Are you sure you want to suspend this user? They will lose access till you unsuspend them."
            method="PATCH" triggerText="Suspend" />
      @else
      <x-confirm-modal :action="route('admin.unsuspend', $admin)"
            warning="Are you sure you want to unsuspend this user? They will regain access to the system."
            method="PATCH" triggerText="UnSuspend" triggerClass="btn-gray" />
      @endif

</x-slide-form>