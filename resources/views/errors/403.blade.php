@extends('errors.error')
@section('content')
<div class="flex-center mb-3">
    <img src="{{ asset('assets/img/icn_403.png') }}" alt="icon" height="64">
    <h1 class="fw-bold mb-0">OOPS!</h1>
</div>
<h6>Error 403: Forbidden</h6>
<p class="mb-5">
    @if (!empty($message))
        {{ $message }}
    @else
        {{ 'Sorry, this is a restricted area' }}
    @endif
</p>
@endsection
