@php
$isReqFindLearners  = request()->routeIs('tutor.find-learners');
$isReqMyLearners    = request()->routeIs('tutor.my-learners');
$isRouteHireReq     = request()->routeIs('tutor.hire-requests');

$routeMyLearners    = route('tutor.my-learners');
$routeHireReq       = route('tutor.hire-requests');
$routeFindLearners  = route('tutor.find-learners');
@endphp
<ul class="nav col-12 col-lg-auto ms-lg-3 me-lg-auto mb-2 justify-content-center mb-md-0">
    <li>
        <a href="{{ $isReqFindLearners ? '#' : $routeFindLearners }}" class="nav-link px-2 {{ $isReqFindLearners ? 'link-active' : '' }}">Find Learners</a>
    </li>
    <li>
        <a href="{{ $isReqMyLearners ? '#' : $routeMyLearners }}" class="nav-link px-2 {{ $isReqMyLearners ? 'link-active' : '' }}">My Learners</a>
    </li>

    <li>
        <a href="{{ $isRouteHireReq ? '#' : $routeHireReq }}" class="nav-link position-relative px-2 {{ $isRouteHireReq ? 'link-active' : '' }}">
            Hire Requests
        </a>
    </li>
</ul>

{{-- @if (isset($hireRequests) && $hireRequests->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                <span class="visually-hidden">New alerts</span>
            </span>
            @endif --}}


{{-- <button type="button" class="btn btn-primary position-relative">
    Profile
    <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
      <span class="visually-hidden">New alerts</span>
    </span>
  </button> --}}
