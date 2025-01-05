@php
$isReqFindLearners  = request()->routeIs('tutor.find-learners');
$isReqMyLearners    = request()->routeIs('mylearners');
$isRouteHireReq     = request()->routeIs('tutor.hire-requests');

$routeMyLearners    = route('mylearners');
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
        <a href="{{ $isRouteHireReq ? '#' : $routeHireReq }}" class="nav-link px-2 {{ $isRouteHireReq ? 'link-active' : '' }}">Hire Requests</a>
    </li>
</ul>
