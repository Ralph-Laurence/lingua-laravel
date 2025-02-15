@php $includeFooter = false; @endphp
@extends('shared.base-members')
{{-- @dd($tutors) --}}
@section('content')
<main class="workspace-wrapper" style="height: calc(100vh - 73px)">
    <aside class="workspace-sidepane">
        <div class="action-pane">
            <h6 class="action-pane-title border p-2 rounded text-center">
                Actions
            </h6>
            <hr class="border border-gray-800">
            <h6 class="text-13 fw-bold">
                <i class="fas fa-filter me-2"></i>Filter Tutors
            </h6>
            <form action="{{-- route('tutor.find-learners') --}}" method="get">
                <div class="mb-3">
                <input @if(session('search'))
                          value="{{ session('search') }}"
                       @endif
                       type="text" class="form-control text-13" maxlength="20"
                       name="search" placeholder="Search Tutor">
                </div>
                <h6 class="text-13 text-secondary">What to include:</h6>
                <div class="row mb-3">
                    <div class="col col-4 text-13">
                        <div class="h-100 flex-start">Impairment</div>
                    </div>
                    <div class="col text-13">
                        <select class="form-select p-1 text-13" name="disability">
                            @php
                                $disabilityFilter = ['-1' => 'All'] + $disabilityFilter;
                            @endphp
                            @foreach ($disabilityFilter as $k => $v)
                                @php
                                    $isSelected = (session('disability') ?? -1) == $k  ? 'selected' : '';
                                @endphp
                                <option class="text-14" {{ $isSelected }} value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col col-4 text-13">
                        <div class="h-100 flex-start">Entries</div>
                    </div>
                    <div class="col text-13">
                        <select class="form-select p-1 text-13" name="min-entries">
                            @foreach ($entriesOptions as $opt)
                                @php
                                    $isSelected = (session('minEntries') ?? -1) == $opt  ? 'selected' : '';
                                @endphp
                                <option class="text-14" {{ $isSelected }} value="{{ $opt }}">{{ $opt }} Per Page</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button class="btn btn-sm btn-primary action-button w-100">Find Tutors</button>
                @if ($filtersApplied)
                    <a role="button" href="{{route('learner.find-tutors-clear') }}"
                      class="btn btn-sm btn-outline-secondary w-100 mt-2 btn-clear-results">Clear Filters</a>
                @endif
            </form>
        </div>
    </aside>
    <section class="workspace-workarea">

        <div class="alert alert-secondary py-2 px-3 text-12 mb-3" style="margin-top: 0.4375rem;">
            <i class="fas fa-info-circle me-1"></i>
            Tutors who are already connected to you will not appear in this list.
        </div>

        @if ($filtersApplied)
        <div id="breadcrumb">
            <a><i class="fas fa-filter me-1"></i>Filter</a>
            <a href="#">Disability: {{ $disabilityFilter[session('disability')] }}</a>
            <a href="#">Entries: {{ session('minEntries') }} per page</a>
            <a href="#">Keyword: {{ session('search') ?? 'None' }}</a>
            {{-- Product --}}
        </div>
        @endif

        <div class="workarea-table-body mb-3 d-flex flex-wrap gap-3">

            @php
                use \Carbon\Carbon;
                $thisWeek = Carbon::now()->subWeek();
            @endphp

            @forelse ($tutors as $key => $obj)
            <div class="card shadow-sm find-tutor-item-card">
                <div class="card-body position-relative">
                    <div class="row mx-auto">
                        <div class="col-4 ps-0">
                            <img class="tutor-photo centered-image mb-2" src="{{ $obj['photo'] }}"/>

                            <div class="star-ratings">
                                @if(Carbon::parse($obj['dateJoined'])->greaterThanOrEqualTo($thisWeek))
                                    <div class="flex-center">
                                        <img src="{{ asset('assets/img/icn_hand_waving.png') }}" alt="waving" height="28">
                                        <p class="ms-1 text-14 mb-0">New</p>
                                    </div>
                                @endif

                                @if(empty($obj['ratings'])) {{-- == 0) --}}
                                    <div class="flex-center unrated">
                                        <img src="{{ asset('assets/img/icn_unrated.png') }}" alt="waving" height="20">
                                        <p class="ms-1 text-12 mb-0">Unrated</p>
                                    </div>
                                @else

                                    <div class="text-center">
                                        <div class="ms-1 mb-0 poppins-semibold mb-1">
                                            <i class="fas fa-star me-1 text-16"></i>
                                            <span class="text-13">{{ $obj['ratings'] }}</span>
                                        </div>
                                        <div class="darker-text text-13">{{ $obj['reviews'] }} {{ $obj['reviews'] == 1 ? 'Review' : 'Reviews' }}</div>
                                    </div>

                                @endif

                            </div>

                            @push('styles')
                                <style>
                                    .star-ratings {
                                        color: #1D1134;
                                        font-family: 'Poppins-Medium';
                                    }
                                    .star-ratings .unrated {
                                        color: #5C636A;
                                    }
                                    .star-ratings .unrated p {
                                        padding-top: 3px;
                                    }
                                </style>
                            @endpush
                        </div>
                        <div class="col-8 gx-0">
                            <div class="tutor-name w-100 text-start">
                                {{ $obj['name']}}
                            </div>
                            <div class="flex-start gap-2 mt-3 mb-2">
                                <div class="tutor-disability text-14 flex-center">
                                    {{-- <span title="{{ $obj['fluencyDesc'] }}" class="disability-tooltip badge {{ $obj['fluencyBadge'] }} ">{{ $obj['fluencyStr'] }}</span> --}}
                                </div>
                                <div class="total-students px-3">
                                    @if ($obj['totalLearners'] == 1)
                                        {{ __("1 Learner") }}
                                    @else
                                        {{ $obj['totalLearners'] }} Learners
                                    @endif
                                </div>
                            </div>
                            <div class="bio tutor-bio">
                                {{ $obj['bioNotes'] }}
                            </div>
                        </div>
                    </div>
                    <div class="position-absolute bottom-0 start-0 end-0 w-100 pb-3">
                        <div class="row mx-3">
                            <div class="col-4"></div>
                            <div class="col-8 d-flex justify-content-between align-items-center gap-2 gx-0">
                                <a type="button" href="{{ route('user', $obj['chatUserId']) }}" class="btn btn-sm btn-outline-secondary flex-fill text-12">
                                    <i class="fas fa-message me-1"></i>
                                    Message
                                </a>
                                <a type="button" href="{{ route('tutor.show', $obj['tutorId']) }}" class="btn btn-sm btn-secondary flex-fill text-12 btn-get-started">
                                    Get Started
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="photo-container mb-2">
                        <img class="tutor-photo centered-image" src="{{ $obj['photo'] }}"/>
                    </div>
                    <div class="tutor-name w-100 mb-1">
                        <div class="text-truncate text-center">{{ $obj['name']}}</div>
                    </div>
                    <button type="button" data-tutor-id="{{ $obj['tutorId'] }}" class="btn btn-sm btn-outline-secondary btn-tutor-details-popover w-100 text-12 mb-2">See Profile</button>
                    <button type="button" data-tutor-id="{{ $obj['tutorId'] }}" class="btn btn-sm btn-secondary w-100 text-12 btn-add-tutor">
                        <i class="fas fa-plus me-1"></i>
                        Add Tutor
                    </button> --}}
                </div>
            </div>
            @empty
                @if (isset($hasFilter))
                    <div class="text-center my-5 py-5">
                        <h5>No Results Found</h5>
                    </div>
                @else
                    <div class="text-center my-5 py-5">
                        <h5>No Records Yet</h5>
                    </div>
                @endif
            @endforelse
        </div>
        <div class="mt-5 pagination-wrapper">
            {{ $tutors->links() }}
        </div>
    </section>
</main>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/lib/fontawesome6.7.2/css/brands.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/tutor-workspace.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/breadcrumb.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/js/utils.js') }}"></script>
    <script src="{{ asset('assets/lib/waitingfor/bootstrap-waitingfor.min.js') }}"></script>
    <script>
        // $(() => initFluencyTooltips());
    </script>
@endpush
@push('styles')
    <style>
        /* .find-tutor-item-card
        {
            width: fit-content;
            min-width: 180px;
            max-width: 180px;
            font-family: 'Poppins';
        } */
         .btn-get-started {
            border: 1px solid #1D1134;
            background-color: #2C1A4E;
         }
         .btn-get-started:hover {
            border: 1px solid #2C1A4E;
            background-color: #623a91;
         }
        .find-tutor-item-card .card-body .tutor-bio {
            height: 72px;
            font-size: 14px;
            line-height: 24px; /* Adjust line height to ensure proper spacing (16px font size * 1.5) */
            overflow: hidden;
            display: -webkit-box; /* Required for -webkit-line-clamp */
            -webkit-box-orient: vertical; /* Required for -webkit-line-clamp */
            -webkit-line-clamp: 3; /* Limit to 3 lines */
            white-space: normal; /* Ensure normal white-space behavior */
            text-overflow: ellipsis;
            color: #707070;
        }
        .find-tutor-item-card
        {
            width: 400px;
            min-width: 300px;
            max-width: 400px;
            height: 250px;
            max-height: 250px;
            font-family: 'Poppins';
        }
        .find-tutor-item-card .card-body {
            overflow: hidden;
        }
        .find-tutor-item-card .photo-container {
            width: 98px;
            height: 98px;
        }
        .find-tutor-item-card .tutor-photo {
            width: 98px;
            height: 98px;
            border-radius: 3px;
            margin-left: 0;
            margin-right: 0;
        }
        .find-tutor-item-card .tutor-name {
            font-size: 16px;
            font-family: 'Poppins-SemiBold';
            color: #121117;
        }
        .find-tutor-item-card .total-students {
            background: #EEF2F9;
            padding-top: 2px;
            padding-bottom: 2px;
            border-radius: 4rem;
            font-family: 'Poppins-SemiBold';
            font-size: 12px;
            color: #34313b;
        }
    </style>
@endpush
