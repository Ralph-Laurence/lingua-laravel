@php
    $isCurrentlyHired = $tutorDetails['hireStatus'] == 1;
    $isHireRequested = $tutorDetails['hireStatus'] == 2;
@endphp
{{-- @dd($tutorDetails) --}}
@extends('shared.base-members')
@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/tutor-details.css') }}">
    @endpush

    @push('dialogs')
        @include('partials.confirmbox')
    @endpush

    <div class="container">
        <div class="row">
            <div class="col-8 p-4">
                <div class="profile-details d-flex mb-4">
                    <div class="profile-photo-wrapper">
                        <img src="{{ $tutorDetails['photo'] }}" alt="profile-photo" height="160">
                    </div>
                    <div class="profile-captions ps-4">

                        <h2 class=" mb-3 darker-text tutor-name ">{{ $tutorDetails['fullname'] }}</h2>

                        <div class="tutor-badges flex-start mb-3 gap-2">
                            <span class="badge {{ $tutorDetails['fluencyBadgeColor'] }}">
                                <i class="fas {{ $tutorDetails['fluencyBadgeIcon'] }} me-2"></i>
                                {{ $tutorDetails['fluencyLevelText'] }}
                            </span>
                            @if ($tutorDetails['hireStatus'] == 1)
                                <span class="d-inline-block sign-lingua-red-text cursor-pointer">
                                    <i class="fas fa-link"></i>
                                    <span class="text-14">Hired</span>
                                </span>
                            @endif
                        </div>

                        <h6 class="tutor-bio darker-text text-14 mb-3">
                            @foreach (explode("\n", $tutorDetails['bio']) as $line)
                                {{ $line }}<br>
                            @endforeach
                        </h6>
                        <div class="tutor-address text-secondary mb-2 text-14">
                            <i class="fas fa-location-dot w-20px"></i>
                            {{ $tutorDetails['address'] }}
                        </div>
                        <div class="tutor-email text-secondary mb-2 text-14">
                            <i class="fas fa-at w-20px"></i>
                            {{ $tutorDetails['email'] }}
                        </div>
                        <div class="tutor-contact text-secondary mb-3 text-14">
                            <i class="fas fa-phone w-20px"></i>
                            {{ $tutorDetails['contact'] }}
                        </div>
                    </div>
                </div>

                <div class="about-me-wrapper mb-4">
                    <h5 class="title-about-me darker-text mb-3">About Me</h5>
                    <p class="msw-justify">
                        @foreach (explode("\n", $tutorDetails['about']) as $line)
                            {{ $line }}<br>
                        @endforeach
                    </p>
                </div>

                <div class="resume-wrapper">
                    <h5 class="title-about-me darker-text mb-3">Resume</h5>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#home">Education</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#menu1">Work Experience</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#menu2">Certifications</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#menu3">My Skills</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div id="home" class="container tab-pane active"><br>

                            @if (!empty($tutorDetails['education']))
                                @foreach ($tutorDetails['education'] as $obj)
                                    <div class="row mb-3">
                                        <div class="col-2 text-secondary">{{ $obj['from'] }} - {{ $obj['to'] }}</div>
                                        <div class="col">
                                            <p class="mb-1">{{ $obj['institution'] }}</p>
                                            <small class="text-secondary">{{ $obj['degree'] }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-secondary">Nothing to show</span>
                            @endif

                        </div>
                        <div id="menu1" class="container tab-pane fade"><br>
                            @if (!empty($tutorDetails['work']))
                                @foreach ($tutorDetails['work'] as $obj)
                                    <div class="row mb-3">
                                        <div class="col-2 text-secondary">{{ $obj['from'] }} - {{ $obj['to'] }}</div>
                                        <div class="col">
                                            <p class="mb-1">{{ $obj['company'] }}</p>
                                            <small class="text-secondary">{{ $obj['role'] }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-secondary">Nothing to show</span>
                            @endif
                        </div>
                        <div id="menu2" class="container tab-pane fade"><br>
                            @if (!empty($tutorDetails['certs']))
                                @foreach ($tutorDetails['certs'] as $obj)
                                    <div class="row mb-3">
                                        <div class="col-2 text-secondary">{{ $obj['year'] }}</div>
                                        <div class="col">
                                            <p class="mb-1">{{ $obj['certification'] }}</p>
                                            <small class="text-secondary">{{ $obj['description'] }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-secondary">Nothing to show</span>
                            @endif
                        </div>
                        <div id="menu3" class="container tab-pane fade"><br>
                            @if (!empty($tutorDetails['skills']))
                                <div class="w-100-h-100 flex-start gap-2 skills-list">
                                    @foreach ($tutorDetails['skills'] as $skill)
                                        <span class="badge bg-secondary skill-badge">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-secondary">Nothing to show</span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-4 p-4">

                <div class="card shadow-sm mb-3">
                    <div class="card-body">

                        @php
                            $cardTitle = $tutorDetails['hireStatus'] == 1 ? 'HIRED' : 'HIRE TODAY!';
                        @endphp

                        <div class="ribbon-banner w-100 d-flex mb-2">
                            <div class="ribbon-banner-left"></div>
                            <div class="ribbon-banner-middle flex-center pt-1 flex-fill text-center">{{ $cardTitle }}
                            </div>
                            <div class="ribbon-banner-right"></div>
                        </div>

                        @if ($tutorDetails['hireStatus'] == 1)
                            <h5 class="text-center text-14 my-3">
                                <i class="fas fa-link sign-lingua-red-text"></i>
                                <span class="text-secondary">{{ $tutorDetails['firstname'] }} is currently your ASL
                                    tutor</span>
                            </h5>
                        @else
                            <h5 class="text-center title-session mb-1">ASL Tutorial Session</h5>
                            <h5 class="text-center title-with-tutor text-primary">With {{ $tutorDetails['firstname'] }}!
                            </h5>
                        @endif

                        <div class="row px-2 mt-4">
                            <div class="col darker-text">
                                <h6 class="mb-0">
                                    <i class="fas fa-graduation-cap text-16"></i>
                                    {{ $totalLearners }} Learners
                                </h6>
                            </div>
                            <div class="col darker-text">
                                @if ($tutorDetails['verified'])
                                    <h6 class="mb-0">
                                        <i class="fas fa-circle-check"></i>
                                        <small>Verified</small>
                                    </h6>
                                @endif
                            </div>

                        </div>

                        @if ($isCurrentlyHired)
                            <div class="w-100 flex-center button-wrapper">
                                <button class="btn btn-danger sign-lingua-red-button mt-4 mx-2 w-100 btn-end-tutor">
                                    <i class="fa-solid fa-link-slash me-2"></i>Leave Tutor
                                </button>
                            </div>
                        @else
                            <div class="w-100 flex-center button-wrapper">
                                @if ($isHireRequested)
                                    <button class="btn btn-secondary mt-4 mx-2 flex-fill btn-cancel-hire-req">
                                        <i class="fa-solid fa-times me-2"></i>Cancel Request
                                    </button>
                                @else
                                    <button class="btn btn-primary mt-4 mx-2 flex-fill btn-hire-tutor">
                                        <i class="fa-solid fa-user-plus me-2"></i>Hire Tutor
                                    </button>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>

                @if ($isCurrentlyHired)

                    @php
                        $learnerRating = $learnerReview['rating'] ?? 0;
                        $learnerReview = $learnerReview['review'] ?? '';
                        $hasRateReview = !empty($learnerRating);
                        $textAreaAttrs = 'class="form-control no-resize text-14" id="input-review-comment" rows="5"
                                        placeholder="Write a review" maxlength="250" name="review"';
                    @endphp
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <div class="d-flex align-items-center justify-content-around" style="height: 30px;">
                                <h6 class="darker-text font-semi-bold flex-fill mb-0">Rate Your Tutor</h6>
                                @if ($hasRateReview)
                                <button class="btn btn-sm btn-link text-decoration-none text-12 btn-edit-review">
                                    <i class="fas fa-pen"></i>
                                    Edit Review
                                </button>
                                @endif
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger text-13 my-2" role="alert">
                                    <ul class="list-unstyled mb-0">
                                        @error('rating')
                                            <li>{{ $message }}</li>
                                        @enderror
                                        @error('review')
                                            <li>{{ $message }}</li>
                                        @enderror
                                    </ul>
                                </div>
                            @endif

                            <small class="text-muted text-13 msw">On a scale of 1 to 5 stars, how satisfied are you with
                                {{ $tutorDetails['possessiveName'] }} teaching?</small>
                            <div class="star-rating-wrapper flex-center py-2 w-100 position-relative">

                                @if ($hasRateReview)
                                    <div class="star-controls-blocker position-absolute top-0 left-0 w-100 h-100 z-idx-1 pointer-events-none"></div>
                                @endif

                                <ul class="list-group list-group-horizontal">

                                    @for ($i = 1; $i <= 5; $i++)
                                        @php
                                            $isStarSelected = $learnerRating >= $i ? 'filled' : '';
                                        @endphp
                                    <li class="list-group-item border-0 py-0 flex-center">
                                        <div alt="star" height="30" class="star-rating-control {{ $isStarSelected }}" data-rating="{{ $i }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $starRatings[$i] }}"></div>
                                    </li>
                                    @endfor

                                </ul>
                            </div>
                            <form action="{{ route('tutor.store-review') }}" id="frm-rating" method="post">
                                @csrf
                                <input type="hidden" name="tutorId" value="{{ $tutorDetails['hashedId'] }}">
                                <div class="my-2">
                                    <label for="input-review-comment" class="form-label text-13 text-muted">
                                        Please tell us about your experience with this tutor and any suggestions for
                                        improvement.
                                    </label>
                                    <input type="hidden" id="rating" name="rating" data-original="{{ $learnerRating }}" value="{{ $learnerRating }}">

                                    {{-- The interactable review textarea box --}}
                                    @if (!empty($learnerReview))
                                        <textarea {!! $textAreaAttrs !!} readonly>{{ $learnerReview }}</textarea>
                                    @else
                                        <textarea {!! $textAreaAttrs !!}></textarea>
                                    @endif

                                    {{-- Store the original review here... --}}
                                    <textarea class="d-none" id="original-review" readonly>{{ $learnerReview }}</textarea>

                                    <div id="review-char-counter" class="text-muted text-12 py-1 flex-end">0/0</div>
                                </div>
                                <div class="flex-end gap-2">
                                    @if ($hasRateReview)
                                    <button type="button" class="btn btn-outline-danger text-13 btn-sm d-none" id="btn-delete-review">
                                        Delete
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary text-13 btn-sm d-none" id="btn-cancel-update-review">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary text-13 btn-sm sign-lingua-purple-button" id="btn-submit-update-review" disabled>
                                        Update Review
                                    </button>
                                    @else
                                    <button type="submit" class="btn btn-primary text-13 btn-sm sign-lingua-purple-button">
                                        Submit Review
                                    </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <form class="d-none" id="tutor-hiring-action-form" method="post"
          data-action-hire-tutor="{{ route('learner.hire-tutor') }}"
          data-action-cancel-hire="{{ route('learner.cancel-hire-tutor') }}"
          data-action-leave-tutor="{{ route('tutor.end') }}"
          data-action-delete-review="{{ route('tutor.delete-review') }}">
        @csrf
        <input type="hidden" id="tutor_name" value="{{ $tutorDetails['firstname'] }}">
        <input type="hidden" name="tutor_id" value="{{ $tutorDetails['hashedId'] }}">
    </form>


    {{-- <form class="d-none" id="frm-hire-tutor" action="{{ route('learner.hire-tutor') }}" method="post">
        @csrf
        <input type="hidden" id="tutor_name" value="{{ $tutorDetails['firstname'] }}">
        <input type="hidden" name="tutor_id" value="{{ $tutorDetails['hashedId'] }}">
    </form>
    <form class="d-none" id="frm-delete-tutor" action="{{ route('tutor.delete-review') }}" method="post">
        @csrf
        <input type="hidden" id="tutor_name" value="{{ $tutorDetails['firstname'] }}">
        <input type="hidden" name="tutor_id" value="{{ $tutorDetails['hashedId'] }}">
    </form>
    <form class="d-none" id="frm-cancel-hire" action="{{ route('learner.cancel-hire-tutor') }}" method="post">
        @csrf
        <input type="hidden" id="tutor_name" value="{{ $tutorDetails['firstname'] }}">
        <input type="hidden" name="tutor_id" value="{{ $tutorDetails['hashedId'] }}">
    </form>
    <form class="d-none" id="frm-end-contract" action="{{ route('tutor.end') }}" method="post">
        @csrf
        <input type="hidden" id="tutor_name" value="{{ $tutorDetails['firstname'] }}">
        <input type="hidden" name="tutor_id" value="{{ $tutorDetails['hashedId'] }}">
    </form> --}}
@endsection

@push('styles')
    <style>
        .grid-column-right {
            width: 380px;
        }

        .star-rating-control {
            width: 30px;
            height: 30px;
            background-size: cover;
            background-repeat: no-repeat;
            display: inline-block;
            background-image: url({{ asset('assets/img/rating_star_unfilled.png') }});
        }

        .star-rating-control:hover,
        .star-rating-control.hover {
            background-image: url({{ asset('assets/img/rating_star_filled.png') }});
        }

        .star-rating-control.filled {
            background-image: url({{ asset('assets/img/rating_star_filled.png') }});
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/tooltips.js') }}"></script>
    <script src="{{ asset('assets/lib/dompurify/purify.min.js') }}"></script>
    <script src="{{ asset('assets/js/tutor/show.js') }}"></script>
@endpush

@push('dialogs')
    <x-toast-container>
        @if (session('booking_request_success'))
            @php
                $to = $tutorDetails['firstname'];
                $requestMsg = "Your hire request has been sent to $to! We'll notify you once it has been accepted. In the meantime, feel free to explore and connect with other tutors.";
            @endphp
            @include('partials.toast', [
                'toastMessage' => $requestMsg,
                'toastTitle' => 'Request Sent!',
                'useOKButton' => 'true',
            ])
        @endif

        @if (session('booking_request_canceled'))
            @php
                $to = $tutorDetails['firstname'];
                $cancelMsg = "Your hire request to $to has been canceled.";
            @endphp
            @include('partials.toast', [
                'toastMessage' => $cancelMsg,
                'toastTitle' => 'Request Canceled',
                'useOKButton' => 'true',
            ])
        @endif

        @if (session('review_msg'))
            @include('partials.toast', [
                'toastMessage'  => session('review_msg'),
                'toastTitle'    => 'Rate and Review',
                'useOKButton'   => 'true',
            ])
        @endif

    </x-toast-container>
@endpush
