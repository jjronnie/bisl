@extends('layouts.main')
@section('content')
<!-- Sidebar -->
@include('layouts.sidebar')

<!-- Page Content -->
<main class="lg:ml-64 p-4 lg:p-8">
    {{ $slot }}
</main>
@endsection