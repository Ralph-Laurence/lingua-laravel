@extends('shared.layouts.master')

@section('before-header')
    @include('partials.ukraine')
@endsection

@section('header_old')
    <header class="p-3 home-header">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                    <img class="logo-brand" src="{{ asset('assets/img/logo-brand-dark.png') }}" alt="logo-brand" height="40">
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
                        <a role="button" href="#join-the-community"
                            class="btn btn-sm btn-outline-dark border-dark border-2 rounded-3">Register</a>
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
                                <a class="dropdown-item text-14" href="/profile">
                                    <i class="fas fa-user me-2"></i>My Profile
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item text-14"
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
@endsection

@section('header')
    @include('shared.common-header')
@endsection

@section('content')
    <section class="banner p-4">
        <div class="container">
            <div class="row py-4">
                @if ($currentRole == 'tutor')
                    <div class="col">
                        <h1 class="fw-bold text-64 mb-4">Teach anytime, anywhere. Be your own boss!</h1>
                        <p class="msw-justify">Decide when and how many hours you want to teach. No minimum time commitment or fixed schedule.</p>
                    </div>
                    <div class="col">
                        <img src="{{ asset('assets/img/section-anytime-anywhere.jpg') }}" class="rounded rounded-3" height="320">
                    </div>
                @else
                    <div class="col">
                        <h1 class="fw-bold text-64 mb-4">Discover lessons you'll love. Guaranteed.</h1>
                        <p class="msw-justify">With a vast network of experienced tutors and a thriving community of dedicated
                            learners, we truly understand the art of language learning.</p>
                    </div>
                    <div class="col">
                        <img src="{{ asset('assets/img/section-guaranteed.png') }}" class="rounded-half-rem shadow" height="320">
                    </div>
                @endif
            </div>
        </div>
    </section>
    <section class="p-4">
        <div class="container">
            <div class="row py-4">
                <div class="col">
                    <img src="{{ asset('assets/img/section_what_is_asl.jpg') }}" class="rounded rounded-3" height="320">
                </div>
                <div class="col">
                    <h2 class="fw-bold mb-5">What is American Sign Language?</h2>
                    <p class="msw-justify">American Sign Language (ASL) is a complete, natural language that has the same
                        linguistic properties as spoken languages, with grammar that differs from English. ASL is expressed
                        by movements of the hands and face. It is the primary language of many North Americans who are deaf
                        and hard of hearing and is used by some hearing people as well.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="p-4">
        <div class="container">
            <div class="row py-4">
                <div class="col">
                    <h2 class="fw-bold mb-5">ASL Alphabet Chart</h2>
                    <p class="msw-justify">
                        Why are we offering a free ASL alphabet chart? Because deaf and hard of hearing children who grow up
                        in a language-rich environment have better outcomes and brighter futures. Whether your child is
                        using American Sign Language or English (or both!) as a primary language, knowing the ASL alphabet
                        is beneficial. Research shows that there are advantages to fingerspelling both with infants and
                        toddlers and with older children.<br><br>
                        Part of our mission here at SignLingua ASL Community is to provide resources to parents and
                        professionals. Feel free to download this printable chart for personal use. You can also share it
                        with your friends, family, and teachers.
                    </p>
                    <a class="text-decoration-none" href="{{ asset('assets/img/asl_alphabet.jpg') }}" download>
                        <i class="fas fa-download"></i>
                        Download ASL Chart
                    </a>
                </div>
                <div class="col text-center">
                    <img src="{{ asset('assets/img/asl_alphabet.jpg') }}" class="rounded rounded-3" height="400">
                </div>
            </div>
        </div>
    </section>
    @if ($currentRole == 'tutor')
        @include('shared.contents.home-page-community-section-tutor')
    @else
        @include('shared.contents.home-page-community-section')
    @endif

@endsection

@push('dialogs')
    <x-toast-container>
        @if (session('registration_message'))
            @include('partials.toast', [
                'toastMessage' => session('registration_message'),
                'toastTitle' => 'Registration Successful'
            ])
        @endif

        @if (session('msgIsPending'))
            @include('partials.toast', [
                'toastMessage' => session('msgIsPending'),
                'toastTitle' => 'Pending Registration',
                'useOKButton' => 'true'
            ])
        @endif
    </x-toast-container>
@endpush
