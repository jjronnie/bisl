@if(auth()->check() && auth()->user()->hasRole('admin'))
    @include('layouts.partials.sidebar.admin')

@elseif(auth()->check() && auth()->user()->hasRole('user'))
    @include('layouts.partials.sidebar.users')
@endif




