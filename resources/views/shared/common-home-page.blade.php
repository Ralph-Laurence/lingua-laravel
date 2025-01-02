@php
$includeHeader = false;
@endphp
@extends('shared.base-members')

@section('before-header')
    @include('partials.ukraine')
@endsection
@section('content')
<header class="p-3 home-header">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                <img class="logo-brand" src="{{ asset('assets/img/logo-brand-dark.png') }}" alt="logo-brand" height="40">
            </a>

            @guest
                @include('shared.home-page-links-guest')
            @endguest

            @auth
                @if($currentRole == 'tutor')
                    @include('shared.home-page-links-tutor')

                @elseif ($currentRole == 'learner')
                    @include('shared.home-page-links-learner')

                @endif
            @endauth


            <div class="dropdown text-end d-flex align-items-center gap-2">
                @auth
                    <div class="badge role-badge bg-dark px-3 py-2 text-center text-white">
                        {{ $headerData['roleStr'] }}
                    </div>
                    <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <small class="darker-text">{{ $headerData['username'] }}</small>
                        <img src="{{ $headerData['profilePhoto'] }}" alt="profile" width="32" height="32"
                            class="rounded-circle">
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
                                <a class="dropdown-item text-14"  onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="fas fa-power-off me-2"></i>Sign Out
                                </a>
                            </form>
                        </li>
                    </ul>
                @endauth

                @guest
                <a role="button" href="#join-the-community" class="btn btn-sm btn-outline-dark border-dark border-2 rounded-3">Register</a>
                <a role="button" href="{{ route('login') }}" class="btn btn-sm btn-dark border-2 rounded-3">Login</a>
                @endguest
            </div>
        </div>
    </div>

</header>
<section class="banner p-4">
    <div class="container">
        <div class="row py-4">
            <div class="col">
                <h1 class="fw-bold big-text mb-4">Discover lessons you'll love. Guaranteed.</h1>
                <p class="msw-justify">With a vast network of experienced tutors and a thriving community of dedicated learners, we truly understand the art of language learning.</p>
            </div>
            <div class="col">
                <img src="{{ asset('assets/img/section-guaranteed.png') }}" class="rounded rounded-3" height="320">
            </div>
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
                <p class="msw-justify">American Sign Language (ASL) is a complete, natural language that has the same linguistic properties as spoken languages, with grammar that differs from English. ASL is expressed by movements of the hands and face. It is the primary language of many North Americans who are deaf and hard of hearing and is used by some hearing people as well.</p>
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
                    Why are we offering a free ASL alphabet chart? Because deaf and hard of hearing children who grow up in a language-rich environment have better outcomes and brighter futures. Whether your child is using American Sign Language or English (or both!) as a primary language, knowing the ASL alphabet is beneficial. Research shows that there are advantages to fingerspelling both with infants and toddlers and with older children.<br><br>
                    Part of our mission here at SignLingua ASL Community is to provide resources to parents and professionals. Feel free to download this printable chart for personal use. You can also share it with your friends, family, and teachers.
                </p>
                <a href="{{ asset('assets/img/asl_alphabet.jpg') }}" download>
                    Download ASL Chart
                </a>
            </div>
            <div class="col text-center">
                <img src="{{ asset('assets/img/asl_alphabet.jpg') }}" class="rounded rounded-3" height="400">
            </div>
        </div>
    </div>
</section>
<section class="py-5  text-center" style="background: #F0E9FE;">
    <h2 class="fw-bold mb-4">Join the community</h2>
    <p>Creating an account is always FREE. Because we believe that education should be accessible to everyone.</p>
</section>
<section class="p-4" id="join-the-community">
    <div class="container">
        <div class="row py-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('assets/img/icn_home_become_tutor.png') }}" alt="icon" height="64">
                        <h4 class="fw-bold my-3">Become A tutor</h4>
                        <p class="msw-justify">
                            Share your expertise, inspire students, and make a difference in the sign language community. Join us and transform the way others communicate.
                        </p>
                        <div class="flex-end w-100">
                            <a href="{{ route('become-tutor') }}" class="btn btn-dark">
                                Get Started<i class="fas ms-2 fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                @guest
                    <div class="card">
                        <div class="card-body">
                            <img src="{{ asset('assets/img/icn_home_join_student.png') }}" alt="icon" height="64">
                            <h4 class="fw-bold my-3">Join As Learner</h4>
                            <p class="msw-justify">
                                Whether you're an absolute beginner or looking to level up your ASL communication skills, join the community now to connect and learn from our dedicated tutors.
                            </p>
                            <div class="flex-end w-100">
                                <a href="{{ route('registration.learner') }}" class="btn btn-dark">
                                    Let's Go<i class="fas ms-2 fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
    <style>
        .home-header, .banner {
            background: #FDC057;
        }
        .home-header .nav-link {
            /* color: #4F1E05 !important; */
            color: #141C3A !important;
            font-family: 'Poppins-Medium';
            padding-bottom: 0 !important;
        }
        .big-text {
            font-size: 64px;
        }
    </style>
@endpush