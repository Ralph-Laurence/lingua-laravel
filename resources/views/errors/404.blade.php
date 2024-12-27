@extends('errors.error')
@section('content')
<div class="flex-center mb-3">
    <img src="{{ asset('assets/img/icn_404.png') }}" alt="icon" height="64">
    <h1 class="fw-bold mb-0">OOPS!</h1>
</div>
<h6>Error 404: Page Not Found</h6>
<p class="mb-5">Sorry, we can't find the page you're looking for</p>
@endsection
