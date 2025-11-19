<x-slide-form buttonIcon="eye" title=" {{ $admin->name }}">

      @if($admin->status === 'active')
      <x-confirm-modal :action="route('admin.suspend', $admin)"
            warning="Are you sure you want to suspend this user? They will lose access till you unsuspend them."
            method="PATCH" triggerText="Suspend" />
      @else
      <x-confirm-modal :action="route('admin.unsuspend', $admin)"
            warning="Are you sure you want to unsuspend this user? They will regain access to the system."
            method="PATCH" triggerText="UnSuspend" triggerClass="btn-gray"/>
      @endif

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 ">



      </div>

</x-slide-form>