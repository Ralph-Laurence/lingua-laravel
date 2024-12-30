@php $includeFooter = false; @endphp
@extends('shared.base-members')
@section('content')
<section id="section-banner" class="forms-section w-50 mx-auto">
    <div class="card border-0">
        <div class="card-body text-center">
            <h5 class="text-secondary">
                Become a tutor
            </h5>
            <h2>Pending Registration</h2>
            <div class="text-14">
                Your registration request is being reviewed and processed by our administrators.<br>This may take <strong>2-3 working days</strong>. Thank you for your patience and understanding.
            </div>
        </div>
    </div>
</section>

<section id="section-banner" class="forms-section w-25 mx-auto">
    <div class="card">
        <div class="card-body ">
            <div class="alert alert-success text-12 text-center mb-3">
                We will notify you via email once your registration has been verified or reviewed.
                <br><br>
                <strong>{{ Auth::user()->email }}</strong>
            </div>
            <a role="button" href="/" class="btn btn-sm btn-primary w-100 shadow-sm">OK</a>
        </div>
    </div>
</section>
@endsection
