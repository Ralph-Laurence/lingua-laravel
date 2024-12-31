@php $includeFooter = false; @endphp
@extends('shared.base-admin')

@section('content')
<main class="workspace-wrapper">
    <aside class="workspace-sidepane">
        <div class="action-pane">
            <h6 class="action-pane-title border p-2 rounded text-center">
                Dataset Actions
            </h6>
            <hr class="border border-gray-800">
            <h6 class="text-13 fw-bold">
                <i class="fas fa-filter me-2"></i>Filter Results
            </h6>
            <form action="{{ route('admin.tutors-filter') }}" method="post">
                @csrf
                <div class="mb-3">
                  <input type="text" class="form-control text-13" id="search-keyword" maxlength="64" name="search-keyword" placeholder="Search Tutor" value="{{ ($inputs['search-keyword'] ?? '') }}">
                </div>
                <h6 class="text-13 text-secondary">What to include:</h6>
                <div class="row mb-2">
                    <div class="col col-4 text-13">
                        <div class="h-100 flex-start">Status</div>
                    </div>
                    <div class="col text-13">
                        <select class="form-select p-1 text-13" name="select-status" id="select-status">
                            <option class="text-14" {{ ($inputs['select-status'] ?? null) == 0  ? 'selected' : '' }} value="0">All</option>
                            <option class="text-14" {{ ($inputs['select-status'] ?? null) == 1  ? 'selected' : '' }} value="1">Pending</option>
                            <option class="text-14" {{ ($inputs['select-status'] ?? null) == 2  ? 'selected' : '' }} value="2">Verified</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col col-4 text-13">
                        <div class="h-100 flex-start">Fluency</div>
                    </div>
                    <div class="col text-13">
                        <select class="form-select p-1 text-13" name="select-fluency" id="select-fluency">
                            <option class="text-14" value="-1">All</option>
                            @foreach ($fluencyFilter as $k => $v)
                                @php
                                    $isSelected = ($inputs['select-fluency'] ?? -1) == $k  ? 'selected' : '';
                                @endphp
                                <option class="text-14" {{ $isSelected }} value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr class="border border-gray-800 mb-3">
                <div class="row mb-3">
                    <div class="col col-4 text-13">
                        <div class="h-100 flex-start">Entries</div>
                    </div>
                    <div class="col text-13">
                        <select class="form-select p-1 text-13" name="select-entries" id="select-entries">
                            <option class="text-14" {{ ($inputs['select-entries'] ?? null) == 10  ? 'selected' : '' }} value="10">10 Per Page</option>
                            <option class="text-14" {{ ($inputs['select-entries'] ?? null) == 25  ? 'selected' : '' }} value="25">25 Per Page</option>
                            <option class="text-14" {{ ($inputs['select-entries'] ?? null) == 50  ? 'selected' : '' }} value="50">50 Per Page</option>
                            <option class="text-14" {{ ($inputs['select-entries'] ?? null) == 100 ? 'selected' : '' }} value="100">100 Per Page</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-sm btn-danger w-100 btn-find-results">Find Results</button>
                @if (isset($hasFilter))
                    <a role="button" href="{{ route('admin.tutors-clear-filter') }}"
                      class="btn btn-sm btn-outline-secondary w-100 mt-2 btn-clear-results">Clear Filters</a>
                @endif
            </form>
        </div>
    </aside>
    <section class="workspace-workarea">
        <div class="workarea-table-header mb-4">
            <div class="table-content-item row user-select-none">
                <div class="col-1">#</div>
                <div class="col-5">Tutor</div>
                <div class="col-2 flex-center">Fluency</div>
                <div class="col-2 flex-center">Status</div>
                <div class="col-2 flex-center">Actions</div>
            </div>
            <div class="rect-mask"></div>
        </div>
        <div class="workarea-table-body mb-3">
            @forelse ($tutors as $key => $obj)
            <div class="table-content-item row user-select-none mb-3">
                <div class="col-1 flex-start text-secondary">{{ ($tutors->currentPage() - 1) * $tutors->perPage() + $loop->index + 1 }}</div>
                <div class="col-5">
                    <div class="profile-info w-100 flex-start">
                        <img class="rounded profile-pic" src="{{ $obj['photo'] }}" alt="profile-pic">
                        <div class="ms-3 flex-fill">
                            <h6 class="profile-name text-truncate  mb-2 text-13">{{ $obj->name }}</h6>
                            @if ($obj['totalStudents'] > 0)
                                <p class="text-secondary m-0">{{ $obj->totalStudents }} Students</p>
                            @else
                                <p class="text-danger m-0">0 Students</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-2 flex-center">
                    <span class="badge {{ $obj['fluencyBadge'] }}">{{ $obj['fluencyStr'] }}</span>
                </div>
                <div class="col-2 flex-center">
                    <span class="badge {{ $obj['statusBadge'] }}">{{ $obj['statusStr'] }}</span>
                </div>
                <div class="col-2 flex-center">Actions</div>
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

            {{ $tutors->links() }}
        </div>
        
    </section>
</main>
@endsection