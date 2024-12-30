@extends('shared.base-members')
@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/tutor-details.css') }}">
@endpush

@push('dialogs')
    @include('partials.confirmbox')
@endpush

<section class="px-5 pt-5 pb-3 mx-5">
    <div class="row">
        <div class="col-8 ps-5">
            <div class="profile-details d-flex">
                <div class="profile-photo-wrapper">
                    <img src="{{ $tutorDetails['photo'] }}" alt="profile-photo" height="160">
                </div>
                <div class="profile-captions ps-4">
                    <div class="tutor-name flex-start gap-2 mb-3">
                        <h2 class="mb-0 darker-text">{{ $tutorDetails['fullname'] }}</h2>
                        @if ($tutorDetails['isHired'])
                            <i class="fas fa-heart heart-color"></i>
                        @endif
                    </div>

                    <h6 class="tutor-bio darker-text text-14 mb-3">
                        @foreach(explode("\n", $tutorDetails['bio']) as $line)
                            {{ $line }}<br>
                        @endforeach
                    </h6>
                    <div class="tutor-address text-secondary mb-2">
                        <i class="fas fa-location-dot me-2"></i>
                        {{ $tutorDetails['address'] }}
                    </div>
                    <div class="tutor-email text-secondary mb-2">
                        <i class="fas fa-at me-2"></i>
                        {{ $tutorDetails['email'] }}
                    </div>
                    <div class="tutor-contact text-secondary mb-3">
                        <i class="fas fa-phone me-2"></i>
                        {{ $tutorDetails['contact'] }}
                    </div>
                    <div class="tutor-badges flex-start">
                        <span class="badge {{ $tutorDetails['fluencyBadgeColor'] }} mb-3">
                            <i class="fas {{ $tutorDetails['fluencyBadgeIcon'] }} me-2"></i>
                            {{ $tutorDetails['fluencyLevelText'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">

        @if ($tutorDetails['isHired'])

            <div class="card shadow">
                <div class="card-body">
                    <div class="ribbon-banner w-100 d-flex mb-2">
                        <div class="ribbon-banner-left"></div>
                        <div class="ribbon-banner-middle flex-center pt-1 flex-fill text-center">HIRED</div>
                        <div class="ribbon-banner-right"></div>
                    </div>
                    <h5 class="text-center title-session text-14 mb-1">
                        <span class="text-primary">{{ $tutorDetails['firstname'] }}</span>
                        <span class="text-secondary"> is currently your ASL tutor</span>
                    </h5>
                    <div class="row px-2 mt-4">
                        <div class="col">
                            <h5 class="mb-0 darker-text">
                                <i class="fas fa-graduation-cap text-16"></i>
                                <strong><?php // $studentCount ?></strong>
                                <small class="text-secondary text-14">Students</small>
                            </h5>
                        </div>
                        <div class="col">
                            @if (!empty($tutorDetails['verified']))
                                <i class="fas fa-circle-check"></i>
                                <small class="text-secondary">Verified</small>
                            @endif
                        </div>

                    </div>
                    <button class="btn btn-danger mt-4 mx-2 w-100 btn-end-tutor">
                        <i class="fa-solid fa-heart-crack me-2"></i>End Contract
                    </button>
                </div>
            </div>

        @else

            <div class="card shadow">
                <div class="card-body">
                    <div class="ribbon-banner w-100 d-flex mb-2">
                        <div class="ribbon-banner-left"></div>
                        <div class="ribbon-banner-middle flex-center pt-1 flex-fill text-center">HIRE TODAY!</div>
                        <div class="ribbon-banner-right"></div>
                    </div>
                    <h5 class="text-center title-session mb-1">ASL Tutorial Session</h5>
                    <h5 class="text-center title-with-tutor text-primary">With {{ $tutorDetails['firstname'] }}!</h5>
                    <div class="row px-2 mt-4">
                        <div class="col">
                            <h5 class="mb-0 darker-text">
                                <i class="fas fa-graduation-cap text-16"></i>
                                <strong><?php // $studentCount ?></strong>
                                <small class="text-secondary text-14">Students</small>
                            </h5>
                        </div>
                        <div class="col">
                            @if (!empty($tutorDetails['verified']))
                                <i class="fas fa-circle-check"></i>
                                <small class="text-secondary">Verified</small>
                            @endif
                        </div>

                    </div>
                    <button class="btn btn-primary mt-4 mx-2 w-100 btn-hire-tutor">
                        <i class="fa-solid fa-heart-circle-plus me-2"></i>Hire Tutor
                    </button>
                </div>
            </div>

        @endif
        </div>
    </div>
</section>

<section class="tutor-story px-5 mx-5 mb-4">
    <div class="row">
        <div class="col-8 ps-5">
            <h5 class="title-about-me darker-text">About Me</h5>
            <p class="msw-justify">
                @foreach(explode("\n", $tutorDetails['about']) as $line)
                    {{ $line }}<br>
                @endforeach
            </p>
        </div>
        <div class="col-4"></div>
    </div>
</section>

<section class="tutor-resume px-5 mx-5 mb-5">
    <div class="row">
        <div class="col-8 ps-5">
            <h5 class="title-about-me darker-text">Resume</h5>
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
        <div class="col-4"></div>
    </div>
</section>

<form class="d-none" id="frm-hire-tutor" action="{{ route('tutor.hire') }}" method="post">
    @csrf
    <input type="hidden" id="tutor_name" value="{{ $tutorDetails['firstname'] }}">
    <input type="hidden" name="tutor_id" value="{{ $tutorDetails['hashedId'] }}">
</form>
<form class="d-none" id="frm-end-contract" action="{{ route('tutor.end') }}" method="post">
    @csrf
    <input type="hidden" id="tutor_name" value="{{ $tutorDetails['firstname'] }}">
    <input type="hidden" name="tutor_id" value="{{ $tutorDetails['hashedId'] }}">
</form>
@endsection

@push('scripts')
    <script src="{{ asset('assets/lib/dompurify/purify.min.js') }}"></script>
    <script src="{{ asset('assets/js/tutor-details.js') }}"></script>
@endpush
