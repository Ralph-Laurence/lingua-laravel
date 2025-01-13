{{-- <header class="p-3 border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                <img class="logo-brand" src="{{ asset('assets/img/logo-brand-dark.png') }}" alt="logo-brand" height="40">
            </a>

            <ul class="nav col-12 col-lg-auto ms-lg-3 me-lg-auto mb-2 justify-content-center mb-md-0">
                <li>
                    <a href="{{ route('learner.register') }}" class="nav-link px-2">Join as Learner</a>
                </li>
                @if (Route::is('tutor.register'))
                    <li>
                        <a role="button" class="nav-link px-2">Become a Tutor</a>
                    </li>
                @else
                    <li>
                        <a href="#join-the-community" class="nav-link px-2">Become a Tutor</a>
                    </li>
                @endif
            </ul>

            <div class="dropdown text-end d-flex align-items-center gap-2">

                <a role="button" href="#join-the-community"
                    class="btn btn-sm btn-outline-dark border-dark border-2 rounded-3">Register</a>
                <a role="button" href="{{ route('login') }}" class="btn btn-sm btn-dark border-2 rounded-3">Login</a>

            </div>
        </div>
    </div>

</header> --}}


<header class="p-3 {{ request()->is('/') ? 'home-header' : '' }}">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                @php
                    $logoBrand = asset('assets/img/logo-brand.png');

                    if (request()->is('/'))
                        $logoBrand = asset('assets/img/logo-brand-dark.png');

                @endphp
                <img class="logo-brand" src="{{ $logoBrand }}" alt="logo-brand" height="40">
            </a>

            @guest
                {{-- Common header for guest users --}}
                @include('shared.contents.home-page-navlinks-guest')
            @endguest

            @auth
                {{-- User is authenticated but account is pending... --}}
                @if ($isPendingAccount)
                    @include('shared.contents.home-page-navlinks-guest')

                {{-- User is fully authenticated... Use relevant navlinks --}}
                @else
                    @if ($currentRole == 'tutor')
                        @include('shared.contents.home-page-navlinks-tutor')
                    @elseif ($currentRole == 'learner')
                        @include('shared.contents.home-page-navlinks-learner')
                    @endif
                @endif

            @endauth


            <div class="dropdown text-end d-flex align-items-center gap-2">

                @guest
                    @if (request()->routeIs('tutor.register'))
                        <span class="text-14 me-2">Already a member?</span>
                    @else
                        <a role="button" href="#join-the-community"
                            class="btn btn-sm btn-outline-dark border-dark border-2 rounded-3">Register</a>
                    @endif
                    <a role="button" href="{{ route('login') }}" class="btn btn-sm btn-dark border-2 rounded-3">Login</a>
                @endguest

                @auth
                    <div class="badge role-badge bg-dark px-3 py-2 text-center text-white">
                        @if ($isPendingAccount)
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/img/icn_badge_pending.png') }}" alt="icon" height="16">
                                <span class="ms-2">Pending Account</span>
                            </div>
                        @else
                            {{ $headerData['roleStr'] }}
                        @endif
                    </div>
                    <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <small class="darker-text">{{ $headerData['username'] }}</small>
                        <img src="{{ $headerData['profilePhoto'] }}" alt="profile" width="36" height="36"
                            class="border border-2 bg-white border-dark rounded-circle">
                    </a>
                    <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
                        <li>
                            <a class="dropdown-item text-14 text-secondary" href="/profile">
                                <i class="fas fa-user me-2"></i>My Profile
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item text-14 text-secondary"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="fas fa-power-off me-2"></i>Sign Out
                                </a>
                            </form>
                        </li>
                    </ul>
                @endauth

            </div>
        </div>
    </div>

</header>
