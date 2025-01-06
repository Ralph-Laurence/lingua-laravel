@php $includeFooter = false; @endphp
@extends('shared.base-members')

@push('dialogs')
    @include('partials.messagebox')
    @include('partials.learner-popover')
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
            <form action="{{ route('tutor.learners-filter') }}" method="post">
                @csrf
                <div class="mb-3">
                  <input type="text" class="form-control text-13" id="search-keyword" maxlength="64" name="search-keyword" placeholder="Search Learner" value="{{ ($learnerFilterInputs['search-keyword'] ?? '') }}">
                </div>
                <h6 class="text-13 text-secondary">What to include:</h6>
                <div class="row mb-3">
                    <div class="col col-4 text-13">
                        <div class="h-100 flex-start">Fluency</div>
                    </div>
                    <div class="col text-13">
                        <select class="form-select p-1 text-13" name="select-fluency" id="select-fluency">
                            @php
                                $fluencyFilter = ['-1' => 'All'] + $fluencyFilter;
                            @endphp
                            @foreach ($fluencyFilter as $k => $v)
                                @php
                                    $isSelected = ($learnerFilterInputs['select-fluency'] ?? -1) == $k  ? 'selected' : '';
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
                        <select class="form-select p-1 text-13" name="select-entries" id="select-entries">
                            <option class="text-14" {{ ($learnerFilterInputs['select-entries'] ?? null) == 10  ? 'selected' : '' }} value="10">10 Per Page</option>
                            <option class="text-14" {{ ($learnerFilterInputs['select-entries'] ?? null) == 25  ? 'selected' : '' }} value="25">25 Per Page</option>
                            <option class="text-14" {{ ($learnerFilterInputs['select-entries'] ?? null) == 50  ? 'selected' : '' }} value="50">50 Per Page</option>
                            <option class="text-14" {{ ($learnerFilterInputs['select-entries'] ?? null) == 100 ? 'selected' : '' }} value="100">100 Per Page</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-sm btn-primary w-100 action-button">Find Learners</button>
                @if (isset($hasFilter))
                    <a role="button" href="{{ route('tutor.learners-clear-filter') }}"
                      class="btn btn-sm btn-outline-secondary w-100 mt-2 btn-clear-results">Clear Filters</a>
                @endif
            </form>
        </div>
    </aside>
    <section class="workspace-workarea">
        @if (isset($hasFilter))
        <div id="breadcrumb">
            <a><i class="fas fa-filter me-1"></i>Filter</a>
            <a href="#">Fluency: {{ $fluencyFilter[$learnerFilterInputs['select-fluency']] }}</a>
            <a href="#">Entries: {{ $learnerFilterInputs['select-entries'] }} per page</a>
            <a href="#">Keyword: {{ $learnerFilterInputs['search-keyword'] ?? 'None' }}</a>
            {{-- Product --}}
        </div>
        @endif
        <div class="workarea-table-body mb-3 d-flex flex-wrap gap-3" style="padding-top: 0.4375rem;">
            @forelse ($learners as $key => $obj)
            <div class="card shadow-sm find-learner-item-card">
                <div class="card-body p-4 d-flex flex-column align-items-center">
                    <div class="photo-container mb-2">
                        <img class="learner-photo centered-image" src="{{ $obj['photo'] }}"/>
                    </div>
                    <div class="learner-name w-100 mb-1">
                        <div class="text-truncate text-center">{{ $obj['name']}}</div>
                    </div>
                    <button type="button" data-learner-id="{{ $obj['user_id'] }}" class="btn btn-sm btn-outline-secondary btn-details-popover w-100 text-12 mb-2">See Profile</button>
                    <button type="button" data-learner-id="{{ $obj['user_id'] }}" class="btn btn-sm btn-secondary w-100 text-12 btn-add-learner">
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
    <script src="{{ asset('assets/js/shared/fetch-learner-details.js') }}"></script>
    <script src="{{ asset('assets/js/tutor-my-learners.js') }}"></script>
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
