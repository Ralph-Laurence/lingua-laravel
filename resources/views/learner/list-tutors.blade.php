@extends('partials.base')
@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/tutors.css') }}">
@endpush()

{{-- @section('before-header')
@include('partials.ukraine')
@endsection --}}

<section class="py-5 text-center">
    <h3 class="mb-4">Find the Ideal Sign Language Tutor for You</h3>
    <p>Looking to learn sign language? Sign Lingua connects you with the best tutors for personalized, one-on-one lessons.</p>
</section>

<section class="section-choose-tutors">
    <div class="row">
        <div class="col">
            <h4 class="mb-3">Many ASL Tutors To Choose From</h4>
            <p class="msw-justify">
                You can choose from a diverse array of American Sign Language tutors to meet your learning needs.
                Whether you're a beginner or looking to advance your skills, our knowledgeable tutors are here to help
                you. Browse through our list of tutors and select the one that best fits your learning style and preferences.
                <br><br>Don't miss the opportunity to connect with passionate and knowledgeable tutors who are dedicated to your learning success.
                Book a lesson today and start your journey towards mastering American Sign Language.
            </p>

        </div>
        <div class="col text-center">
            <img src="{{ asset('assets/img/section-choose-tutors.png') }}" alt="tutor-session" height="320">
        </div>
    </div>

</section>

<hr class="mt-4 ">
<section id="browse-tutors" class="my-4 px-4 control-ribbon">
    <div class="row">
        <div class="col">
            <div class="d-flex h-100 align-items-center gap-2">
                <h5 class="m-0">Browse tutors</h5>
                <h6 class="m-0">({{ $totalTutors }} available)</h6>
            </div>
        </div>
        <div class="col d-flex flex-row align-items-center justify-content-end gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle sign-lingua-dropdown-button" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                    Tutor Fluency
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    <li><button class="dropdown-item" type="button">All</button></li>
                    {{-- < ?php foreach(FluencyLevels::Tutor as $key => $arr): ?>
                        < ?php
                            $obj = FluencyLevels::Tutor[$key];
                            $levelName = $obj['Level'];
                        ?>
                        <li><button class="dropdown-item" data-value="< ?= $key ?>" type="button">< ?= $levelName ?></button></li>
                    < ?php endforeach ?> --}}
                </ul>
            </div>
            <div class="find-tutor-wrapper d-flex gap-2 align-items-center">
                <div class="form-group">
                    <input type="text" class="form-control sign-lingua-input-field" id="input-find-tutor" placeholder="Tutor name" aria-describedby="find-tutor-help">
                </div>
                <button href="#" class="btn btn-secondary search-button">
                    <i class="fas fa-magnifying-glass text-white"></i>
                </button>
                <!-- <button class="btn btn-warning grad-btn-danger">Find Tutor</button> -->
            </div>
        </div>
    </div>
</section>
<hr>

<section class="tutors-list p-4">
    <div class="tutors-list-view">
        @foreach ($tutors as $key => $obj)
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row px-3">
                    <div class="col-4 gx-0">
                        <img src="{{ $obj['profilePic'] }}" class="profile-photo" alt="profile-photo">
                    </div>
                    <div class="col"></div>
                </div>
                <h6 class="card-title tutor-name mt-3 mb-1">
                    <i class="fas fa-circle-check {{ $obj['hiredIndicator'] }} accent-secondary me-2"></i>
                   {{ $obj['fullname'] }}
                </h6>
                <span class="badge {{ $obj['fluencyBadgeColor'] }} mb-3">
                    <i class="fas {{ $obj['fluencyBadgeIcon'] }} me-2"></i>
                    {{ $obj['fluencyLevelText'] }}
                </span>
                <p class="card-text tutor-bio">{{ $obj['bioNotes'] }}</p>
                <div class="flex-end">
                    <a role="button" href="{{ route('tutor.show', $obj['hashedId']) }}" data-tutor-id="{{ $obj['hashedId'] }} ?>" class="btn btn-sm btn-secondary btn-more-details">More Details</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</section>

<?php //require_once "../revamp/ui/progress-modal.php" ?>
<?php //require_once "../revamp/ui/message-box.php" ?>

@endsection
