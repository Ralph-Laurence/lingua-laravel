<ul class="nav col-12 col-lg-auto ms-lg-3 me-lg-auto mb-2 justify-content-center mb-md-0">
    <li>
        <a href="" class="nav-link px-2">Find Learners</a>
    </li>
    <li>
        @php
            $isReqMyLearners = request()->routeIs('mylearners');
            $routeMyLearners = route('mylearners');
        @endphp
        <a href="{{ $isReqMyLearners ? '#' : $routeMyLearners }}" class="nav-link px-2 {{ $isReqMyLearners ? 'link-active' : '' }}">My Learners</a>
    </li>
</ul>
