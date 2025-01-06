@extends('shared.base-members')
@section('content')

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
                @if ($inputs['withFilter'] ?? false)
                    <h5 class="m-0">Filter tutors</h5>
                @else
                    <h5 class="m-0">Browse tutors</h5>
                    <h6 class="m-0">({{ $totalTutors }} available)</h6>
                @endif
            </div>
        </div>
        <div class="col">

            <form action="{{ route('learner.find-filtered-tutors') }}" method="get">
                <div class="d-flex flex-row align-items-center justify-content-end gap-2">

                    <select style="height: 38px;" class="form-select p-2 w-25 text-secondary" name="select-fluency" id="select-fluency">
                        @php
                            // Add option "All"
                            $fluencyFilters = ['-1' => 'All'] + $fluencyFilters;
                            // Set default value to -1 if not set
                            $selectedFluency = $inputs['select-fluency'] ?? -1;
                        @endphp
                        @foreach ($fluencyFilters as $k => $v)
                            @php
                                $isSelected = ($selectedFluency == $k) ? 'selected' : '';
                            @endphp
                            <option class="text-14" {{ $isSelected }} value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>

                    <div class="find-tutor-wrapper d-flex gap-2 align-items-center">
                        <div class="form-group">
                            <input type="text" class="form-control sign-lingua-input-field" id="input-find-tutor" name="search-term"
                                   placeholder="Tutor name" aria-describedby="find-tutor-help"
                                   value="{{ $inputs['search-term'] ?? '' }}">
                        </div>
                        <button type="submit" style="height: 38px;" href="#" class="btn btn-secondary search-button">
                            <i class="fas fa-magnifying-glass text-white"></i>
                        </button>
                        @if ($inputs['withFilter'] ?? false)
                        <a role="button" style="height: 38px;" href="{{ route('learner.find-tutors') }}" class="btn btn-secondary">
                            <i class="fas fa-close text-white"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>

        </div>
    </div>
</section>
<hr>

<section class="tutors-list p-4" id="tutors-list">
    @if($tutors->count() < 1)
        <div class="flex-center w-100 text-secondary py-5">
            Nothing to show...
        </div>
    @else
    <div class="tutors-list-view">
        @foreach ($tutors as $key => $obj)
        <div class="card shadow-sm">
            <div class="card-body">
            <div class="row px-3">
                <div class="col-4 gx-0">
                    <img src="{{ $obj['profilePic'] }}" class="profile-photo" alt="profile-photo">
                </div>
                <div class="col pe-0">
                    <div class="flex-end">
                        <a role="button" href="{{ route('tutor.show', $obj['hashedId']) }}" data-tutor-id="{{ $obj['hashedId'] }} ?>" class="btn btn-sm btn-secondary btn-more-details">More Details</a>
                    </div>
                </div>
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
            {{-- <div class="flex-end">
                <a role="button" href="{{ route('tutor.show', $obj['hashedId']) }}" data-tutor-id="{{ $obj['hashedId'] }} ?>" class="btn btn-sm btn-secondary btn-more-details">More Details</a>
            </div> --}}
            </div>
        </div>
        @endforeach
    </div>
    @endif
    {{ $tutors->fragment('tutors-list')->links() }}
</section>
@endsection

@push('dialogs')
    <div class="fadeout-overlay"></div>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/tutors.css') }}">
<style>
    .fadeout-overlay {
        width: 100%;
        height: 100%;
        background-color: white;
        opacity: 1;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 100;
        transition: opacity 1s ease-out; /* Adjust transition duration as needed */
    }

    .fadeout-fx {
        opacity: 0;
    }
</style>
@endpush()

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', (event) => {

    const fadeOverlay = document.querySelector('.fadeout-overlay');

    if (fadeOverlay) {
        // Check if the URL contains a hash
        if (window.location.hash)
        {
            // Wait for the scrolling to the anchor to complete
            setTimeout(() => {
                fadeOverlay.classList.add('fadeout-fx');

                // Optionally remove the element from the DOM after the transition
                fadeOverlay.addEventListener('transitionend', () => {
                    fadeOverlay.remove();
                });
            }, 1000); // Adjust the delay as needed (1000ms = 1 second)
        }
        else
        {
            // No hash, fade out immediately
            $(fadeOverlay).hide(); //classList.add('fadeout-fx');

            // Optionally remove the element from the DOM after the transition
            fadeOverlay.addEventListener('transitionend', () => {
                fadeOverlay.remove();
            });
        }
    }
});
</script>
@endpush


