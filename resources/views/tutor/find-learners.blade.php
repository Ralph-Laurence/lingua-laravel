@php $includeFooter = false; @endphp
@extends('shared.base-members')

@push('dialogs')
    @include('partials.messagebox')
    @include('partials.learner-details-popover')
@endpush

@section('content')
<main class="workspace-wrapper">
    <aside class="workspace-sidepane">
        <div class="action-pane">
            <h6 class="action-pane-title border p-2 rounded text-center">
                Actions
            </h6>
            <hr class="border border-gray-800">
            <h6 class="text-13 fw-bold">
                <i class="fas fa-filter me-2"></i>Filter Learners
            </h6>
            <form action="{{ route('tutor.find-learners') }}" method="get">
                <div class="mb-3">
                <input @if(session('search'))
                          value="{{ session('search') }}"
                       @endif
                       type="text" class="form-control text-13" maxlength="20"
                       name="search" placeholder="Search Learner">
                </div>
                <h6 class="text-13 text-secondary">What to include:</h6>
                <div class="row mb-3">
                    <div class="col col-4 text-13">
                        <div class="h-100 flex-start">Accessibility</div>
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
                <button class="btn btn-sm btn-primary w-100 action-button">Find Learners</button>
                @if ($filtersApplied)
                    <a role="button" href="{{ route('tutor.find-learners.clear') }}"
                      class="btn btn-sm btn-outline-secondary w-100 mt-2 btn-clear-results">Clear Filters</a>
                @endif
            </form>
        </div>
    </aside>
    <section class="workspace-workarea">

        <div class="alert alert-secondary py-2 px-3 text-12 mb-3" style="margin-top: 0.4375rem;">
            <i class="fas fa-info-circle me-1"></i>
            Learners who are already connected to you will not appear in this list.
        </div>

        @if ($filtersApplied)
        <div id="breadcrumb">
            <a><i class="fas fa-filter me-1"></i>Filter</a>
            <a href="#">Accessibility: {{ $disabilityFilter[session('disability')] }}</a>
            <a href="#">Entries: {{ session('minEntries') }} per page</a>
            <a href="#">Keyword: {{ session('search') ?? 'None' }}</a>
            {{-- Product --}}
        </div>
        @endif

        <div class="workarea-table-body mb-3 d-flex flex-wrap gap-3">
            @forelse ($learners as $key => $obj)
            <div class="card shadow-sm find-learner-item-card">
                <div class="card-body p-4 d-flex flex-column align-items-center">
                    <div class="photo-container mb-2">
                        <img class="learner-photo centered-image" src="{{ $obj['photo'] }}"/>
                    </div>
                    <div class="learner-name w-100 mb-1">
                        <div class="text-truncate text-center">{{ $obj['name']}}</div>
                    </div>
                    <button type="button" data-learner-id="{{ $obj['learnerId'] }}" class="btn btn-sm btn-outline-secondary btn-learner-details-popover w-100 text-12 mb-2">See Profile</button>
                    <button type="button" data-learner-id="{{ $obj['learnerId'] }}" class="btn btn-sm btn-secondary w-100 text-12 btn-add-learner">
                        <i class="fas fa-plus me-1"></i>
                        Add Learner
                    </button>
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
            {{ $learners->links() }}
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
@endpush
@push('styles')
    <style>
        .find-learner-item-card
        {
            width: fit-content;
            min-width: 180px;
            max-width: 180px;
            font-family: 'Poppins';
        }
        .find-learner-item-card .card-body {
            overflow: hidden;
        }
        .find-learner-item-card .photo-container {
            width: 98px;
            height: 98px;
        }
        .find-learner-item-card .learner-photo {
            width: 98px;
            height: 98px;
            border-radius: 3px;
            margin-left: 0;
            margin-right: 0;
        }
        .find-learner-item-card .learner-name {
            font-size: 13px;
            font-family: 'Poppins-Medium';
        }
    </style>
@endpush
