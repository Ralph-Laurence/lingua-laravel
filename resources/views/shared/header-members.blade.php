<header class="p-3 border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                <img class="logo-brand" src="{{ asset('assets/img/logo-brand.png') }}" alt="logo-brand" height="40">
            </a>

            @auth
                @if($headerData['roleStr'] == 'Tutor')
                    @include('shared.contents.home-page-navlinks-tutor')

                @elseif($headerData['roleStr'] == 'Learner')
                    @include('shared.contents.home-page-navlinks-learner')
                @endif

                <div class="dropdown text-end d-flex align-items-center gap-2">
                    <div class="badge role-badge px-3 py-2 text-center {{ $headerData['roleBadge'] }}">
                        {{ $headerData['roleStr'] }}
                    </div>
                    <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <small class="darker-text">{{ $headerData['username'] }}</small>
                        <img src="{{ $headerData['profilePic'] }}" alt="profile" width="36" height="36"
                            class="border border-2 bg-white border-secondary rounded-circle">
                    </a>
                    <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
                        @if (!request()->routeIs('myprofile.edit'))
                            <li>
                                <a class="dropdown-item text-14" href="{{ route('myprofile.edit') }}">
                                    <i class="fas fa-user me-2"></i>My Profile
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        @endif
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
            @endauth
        </div>
    </div>

</header>
