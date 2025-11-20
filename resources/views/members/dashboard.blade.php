<x-app-layout>

  @php
    $hour = now()->hour;
    if ($hour < 12) {
        $greeting = 'Good morning';
    } elseif ($hour < 17) {
        $greeting = 'Good afternoon';
    } else {
        $greeting = 'Good evening';
    }
@endphp

<x-page-title title="{{ $greeting }}, {{ auth()->user()->name }}" />

</x-app-layout>