<header class="p-3 border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                <img class="logo-brand" src="{{ asset('assets/img/logo-brand.png') }}" alt="logo-brand" height="40">
            </a>

            <ul class="nav col-12 col-lg-auto ms-lg-3 me-lg-auto mb-2 justify-content-center mb-md-0">
                <li>
                    <a href="{{ route('tutors.list') }}" class="nav-link px-2 {{ Request::is('learner/find-tutors') ? 'link-active' : '' }}">Find Tutors</a>
                </li>
                <li>
                    <a href="{{ route('mytutors') }}" class="nav-link px-2 {{ Request::is('learner/my-tutors') ? 'link-active' : '' }}">My Tutors</a>
                </li>
                @if ($headerData['showBecomeTutor'])
                <li>
                    <a href="{{ route('become-tutor') }}" class="nav-link px-2 {{ Route::is('become-tutor') || Route::is('become-tutor.forms') ? 'link-active' : '' }}">Become a Tutor</a>
                </li>
                @endif
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
                        <a class="dropdown-item text-14" href="#">
                            <i class="fas fa-user me-2"></i>My Profile
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="dropdown-item text-14"  onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fas fa-power-off me-2"></i>Sign Out
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</header>
