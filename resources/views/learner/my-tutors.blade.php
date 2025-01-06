@extends('shared.base-members')
@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/tutors.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/my-tutors.css') }}">
@endpush()

<section class="section-learn-faster">
    <div class="row">
        <div class="col">
            <h4 class="mb-4 big-title darker-text">Learn faster with your best American Sign Language tutor</h4>
            <p class="msw-justify pt-3 mb-0 darker-text caption-try-another">
                You can always try another tutor if you are not satisfied
            </p>
        </div>
        <div class="col text-center">
            <img src="{{ asset('assets/img/section-choose-tutors-2.png') }}" alt="tutor-session" height="320">
        </div>
    </div>

</section>

@if (empty($myTutors))
    <section class="mt-5 mb-3 px-4 text-center">
        <h3 class="m-0 darker-text fw-bold">No tutors yet</h3>
        <h6 class="text-14 mt-2 mb-3">You aren't connected to any tutors.</h6>
        <a role="button" href="{{ route('learner.find-tutors') }}#browse-tutors" class="btn btn-dark">
            Browse Tutors<i class="fas fa-arrow-right ms-2"></i>
        </a>
    </section>
@else
    <section class="mt-5 mb-3 px-4 control-ribbon">
        <div class="row">
            <div class="col">
                <h3 class="m-0 darker-text fw-bold">Pickup where you left off</h3>
                <h6 class="text-14 mt-2">Start learning with these tutors you've hired</h6>
            </div>
            <div class="col d-flex flex-row align-items-center justify-content-end gap-2">
                <a role="button" href="{{ route('learner.find-tutors') }}#browse-tutors" class="btn btn-outline-secondary">
                    <i class="fas fa-plus me-2"></i>Add More
                </a>
            </div>
        </div>
    </section>
@endif
<section class="tutors-list p-4 mb-5">

    <div class="tutors-list-view d-flex flex-wrap gap-4">
        @foreach ($myTutors as $key => $obj)
            <a href="{{ route('tutor.show', $obj['tutorId']) }}" class="text-decoration-none tutors-list-item">
                <div class="card p-3 text-center" style="width: 11rem;">
                    <div class="card-body p-1">
                        <img style="border-radius: 4px;" src="{{ $obj['photo'] }}" alt="profile" height="128" width="128">
                        <h6 class="text-14 fw-bold p-2 d-inline-block text-truncate mt-2 pb-0 w-100 mb-0">{{ $obj['shortName'] }}</h6>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

</section>
@endsection
