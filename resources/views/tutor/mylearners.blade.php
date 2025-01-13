@php $includeFooter = false; @endphp
@extends('shared.base-members')
{{-- @dd($learners) --}}
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
            <form action="{{ route('tutor.my-learners') }}" method="get">
                @csrf
                <div class="mb-3">
                    <input @if(session('search'))
                                value="{{ session('search') }}"
                           @endif
                           type="text" class="form-control text-13" maxlength="64"
                           name="search" placeholder="Search Learner">
                </div>
                <h6 class="text-13 text-secondary">What to include:</h6>
                <div class="row mb-3">
                    <div class="col col-4 text-13">
                        <div class="h-100 flex-start">Fluency</div>
                    </div>
                    <div class="col text-13">
                        <select class="form-select p-1 text-13" name="fluency">
                            @php
                                $fluencyFilter = ['-1' => 'All'] + $fluencyFilter;
                            @endphp
                            @foreach ($fluencyFilter as $k => $v)
                                @php
                                    $isSelected = (session('fluency') ?? -1) == $k  ? 'selected' : '';
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
                    <a role="button" href="{{ route('tutor.my-learners.clear') }}"
                      class="btn btn-sm btn-outline-secondary w-100 mt-2 btn-clear-results">Clear Filters</a>
                @endif
            </form>
        </div>
    </aside>
    <section class="workspace-workarea">
        @if ($filtersApplied)
        <div id="breadcrumb">
            <a><i class="fas fa-filter me-1"></i>Filter</a>
            <a href="#">Fluency: {{ $fluencyFilter[session('fluency')] }}</a>
            <a href="#">Entries: {{ session('minEntries') }} per page</a>
            <a href="#">Keyword: {{ session('search') ?? 'None' }}</a>
            {{-- Product --}}
        </div>
        @endif
        <div class="workarea-table-header mb-4">
            <div class="table-content-item row user-select-none">
                <div class="col-1">#</div>
                <div class="col-7">Learner</div>
                <div class="col-2 flex-center">Fluency</div>
                <div class="col-2 flex-center">Actions</div>
            </div>
            <div class="rect-mask"></div>
        </div>
        <div class="workarea-table-body mb-3">
            @forelse ($learners as $key => $obj)
            <div class="table-content-item row user-select-none mb-3">
                <div class="col-1 flex-start text-secondary">{{ ($learners->currentPage() - 1) * $learners->perPage() + $loop->index + 1 }}</div>
                <div class="col-7">
                    <div class="profile-info w-100 flex-start">
                        <img class="rounded profile-pic" src="{{ $obj['photo'] }}" alt="profile-pic">
                        <div class="ms-3 flex-fill">
                            <h6 class="profile-name text-truncate  mb-2 text-13">{{ $obj['name'] }}</h6>
                            @if ($obj['totalTutors'] > 0)
                                <p class="text-secondary m-0">{{ $obj['totalTutors'] }} Tutors</p>
                            @else
                                <p class="text-danger m-0">0 Tutors</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-2 flex-center">
                    <span title="{{ $obj['fluencyDesc'] }}" class="fluency-tooltip badge {{ $obj['fluencyBadge'] }}">{{ $obj['fluencyStr'] }}</span>
                </div>
                <div class="col-2 flex-center">
                    <button type="button" data-learner-id="{{ $obj['learnerId'] }}" class="btn btn-sm btn-secondary row-button btn-learner-details-popover">Details</button>
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
    <script>
        $(() => initFluencyTooltips());
    </script>
@endpush

