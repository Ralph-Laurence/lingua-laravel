<div class="colored-header p-2 text-center">
    Administrator Panel
</div>
<header class="p-3 border-bottomx">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                <img class="logo-brand" src="{{ asset('assets/img/logo-brand.png') }}" alt="logo-brand" height="40">
            </a>

            @php
                $dashboardIsActive = Request::is('/') || Request::is('admin/dashboard') ? 'link-active' : '';
            @endphp

            <ul class="nav col-12 col-lg-auto ms-lg-3 me-lg-auto mb-2 justify-content-center mb-md-0">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link px-2 {{ $dashboardIsActive }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{route('admin.tutors-index')}}" class="nav-link px-2 {{ (Request::is('admin/tutors') ? 'link-active' : '') }}">Tutors</a>
                </li>
                <li>
                    <a href="{{ route('admin.learners-index') }}" class="nav-link px-2 {{ (request()->routeIs('admin.learners-index')) ? 'link-active' : '' }}">Learners</a>
                </li>
            </ul>

            <div class="dropdown text-end d-flex align-items-center gap-2">
                <div class="badge role-badge px-3 py-2 text-center {{ $headerData['roleBadge'] }}">
                    {{ $headerData['roleStr'] }}
                </div>
                <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <small class="darker-text">{{ $headerData['fullname'] }}</small>
                    <img src="{{ $headerData['profilePic'] }}" alt="profile" width="32" height="32"
                        class="rounded-circle">
                </a>
                <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
                    <li>
                        <div class="text-12 text-muted py-2 px-3">
                            <img src="{{ asset('assets/img/icn_badge_pending.png') }}" width="20" height="20" alt="warning" class="d-inline">
                            Your profile is managed by your IT department.
                        </div>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="dropdown-item text-14 cursor-pointer"  onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fas fa-power-off me-2"></i>Sign Out
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</header>
