@extends('errors.error')
@section('content')
<div class="flex-center mb-3">
    <img src="{{ asset('assets/img/icn_500.png') }}" alt="icon" height="64">
    <h1 class="fw-bold mb-0">OOPS!</h1>
</div>
<h6>Error 500: Internal Server Error</h6>
<p class="mb-5">Something went wrong on our end.</p>
@endsection
