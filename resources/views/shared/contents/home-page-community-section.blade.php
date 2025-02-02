<section class="py-5  text-center" style="background: #F0E9FE;">
    <h2 class="fw-bold mb-4">Join the community</h2>
    <p>Creating an account is always FREE. Because we believe that education should be accessible to everyone.</p>
    </section>
    <section class="p-4 {{-- $showJoinCommunity ? '' : 'd-none' --}}" id="join-the-community">
        <div class="container">
        <div class="row py-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('assets/img/icn_home_become_tutor.png') }}" alt="icon" height="64">
                        <h4 class="fw-bold my-3">Become A tutor</h4>
                        <p class="msw-justify">
                            Share your expertise, inspire students, and make a difference in the sign language
                            community. Join us and transform the way others communicate.
                        </p>
                        <div class="flex-end w-100">

                            @auth
                                @if ($currentRole == 'learner')
                                    <a href="{{ route('become-tutor') }}" class="btn btn-dark">
                                        Get Started<i class="fas ms-2 fa-arrow-right"></i>
                                    </a>
                                @endif
                            @endauth

                            @guest
                                <a href="{{ route('tutor.register') }}" class="btn btn-dark">
                                    Get Started<i class="fas ms-2 fa-arrow-right"></i>
                                </a>
                            @endguest

                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                @if ($currentRole != 'learner')
                    <div class="card">
                        <div class="card-body">
                            <img src="{{ asset('assets/img/icn_home_join_student.png') }}" alt="icon"
                                height="64">
                            <h4 class="fw-bold my-3">Join As Learner</h4>
                            <p class="msw-justify">
                                Whether you're an absolute beginner or looking to level up your ASL communication
                                skills, join the community now to connect and learn from our dedicated tutors.
                            </p>
                            @if ($currentRole != 'pending')
                            <div class="flex-end w-100">
                                <a href="{{ route('learner.register') }}" class="btn btn-dark">
                                    Let's Go<i class="fas ms-2 fa-arrow-right"></i>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
        </div>
    </section>
