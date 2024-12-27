@extends('partials.base')
@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/tutors.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/become-tutor.css') }}">
    @endpush()

    <section class="section-become-tutor section-content">
        <div class="row">
            <div class="col text-center">
                <img src="{{ asset('assets/img/become-a-tutor.jpg') }}" alt="tutor-session" height="400">
            </div>
            <div class="col">
                <h4 class="mb-3 big-title darker-text">Become an ASL tutor</h4>
                <p class="pt-3 mb-4 msw-justify text-14 darker-text">Share your knowledge in American Sign Language with
                    others. Register now to help others learn, grow, and communicate effectively within our supportive and
                    inclusive community.</p>
                <ul class="fw-bold mb-4">
                    <li>Connect with new learners</li>
                    <li>Share your expertise</li>
                    <li>Support and learn from each other</li>
                </ul>
                <a role="button" href="#pre-flight" class="btn btn-dark">Become a tutor <i
                        class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <section class="section-experience-skills section-content">
        <div class="row">
            <div class="col">
                <h4 class="mb-3 big-title darker-text">Experience and skills</h4>
                <p class="pt-3 msw-justify darker-text">
                    To become an effective ASL tutor, it's essential to have a strong understanding of American Sign
                    Language
                    as well as some teaching experience and skills. You can gain this experience and enhance your ASL skills
                    by actively participating in the SignLingua community. Our platform provides opportunities for you to
                    develop and refine your ASL teaching abilities. At SignLingua, learners benefit from the guidance of
                    experienced tutors, and anyone who becomes highly skilled in ASL can become a tutor, sharing their
                    knowledge and contributing to the community.
                </p>
            </div>
            <div class="col text-center">
                <img src="{{ asset('assets/img/become-a-tutor-2.jpg') }}" alt="tutor-session" height="400">
            </div>
        </div>
    </section>

    <section class="section-tutor-responsibilities section-content">
        <div class="row">
            <div class="col text-center">
                <img src="{{ asset('assets/img/become-a-tutor-3.jpg') }}" alt="tutor-session" height="400">
            </div>
            <div class="col">
                <h4 class="mb-3 big-title darker-text">Tutor's Responsibilities</h4>
                <p class="pt-3 msw-justify darker-text">
                    As an ASL tutor, you'll be responsible for planning and delivering engaging and interactive
                    lessons, adapting to diverse learning styles and needs, providing constructive feedback, and
                    fostering a positive and respectful learning environment. Strong communication, interpersonal,
                    and organizational skills are crucial, along with a genuine passion for mentoring and inspiring
                    others within our community.
                </p>
            </div>
        </div>
    </section>

    <section class="section-consider section-content mb-5" id="pre-flight">
        <div class="row">
            <div class="col">
                <h4 class="mb-3 big-title darker-text">Here's what else to consider</h4>
                <p class="pt-3 msw-justify darker-text">
                    When applying to become an ASL tutor on SignLingua, please ensure you provide valid information.
                    You will need to fill out an on-site resume that includes details about your education,
                    certifications, work experience, and relevant skills. This helps us maintain a high standard of
                    tutoring and ensures that learners receive the best possible guidance.
                    <br><br>
                    If you are initially a learner and decide to become a tutor, your account will be converted to a
                    tutor account. Please note that this change will result in the loss of all connections to tutors
                    you are currently enrolled with or have booked sessions with. This ensures that there is no
                    conflict of interest and maintains the integrity of our platform.
                </p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="chk-grant-consent">
                    <label class="form-check-label text-14" for="chk-grant-consent">
                        I understand and wish to proceed
                    </label>
                </div>
                <a href="{{ route('become-tutor.forms') }}" id="btn-register-now" class="btn btn-sm btn-dark ms-4 disabled" style="width: 236px;">
                    Register Now <i class="fas fa-arrow-right"></i>
                </a>
                @push('scripts')
                    <script>
                        $(() => {

                            $('#chk-grant-consent').on('change', function() {
                                if ($(this).is(':checked')) {
                                    $('#btn-register-now').removeClass('disabled');
                                } else {
                                    $('#btn-register-now').addClass('disabled');
                                }
                            });

                        });
                    </script>
                @endpush
            </div>
            <div class="col text-center pt-5">
                <img src="{{ asset('assets/img/become-a-tutor-4.jpg') }}" alt="tutor-session" height="500">
            </div>
        </div>
    </section>

    <section class="p-4 mb-5">
        <div class="text-center w-100 mb-5">
            <h3 class="darker-text fw-bold">It's easy as 1-2-3!</h3>
            <p>Becoming a tutor on SignLingua is as simple as following three steps.</p>
        </div>
        <div class="easy-steps">

            <div class="card shadow" style="width: 19rem;">
                <div class="card-body text-secondary">
                    <img src="{{ asset('assets/img/card-icn-resume.png') }}" alt="resume icon" width="80">
                    <div>
                        <h6 class="fw-bold pt-2">1. Fill out your resume</h6>
                        <p class="text-14">Provide your details and qualifications to get started.</p>
                    </div>
                </div>
            </div>

            <div class="card shadow" style="width: 19rem;">
                <div class="card-body text-secondary">
                    <img src="{{ asset('assets/img/card-icn-connect.png') }}" alt="resume icon" width="80">
                    <div>
                        <h6 class="fw-bold pt-2">2. Find a learner</h6>
                        <p class="text-14">Connect with learners eager to improve their ASL skills.</p>
                    </div>
                </div>
            </div>

            <div class="card shadow" style="width: 20rem;">
                <div class="card-body text-secondary">
                    <img src="{{ asset('assets/img/card-icn-teach.png') }}" alt="resume icon" width="80">
                    <div>
                        <h6 class="fw-bold pt-2">3. Start teaching</h6>
                        <p class="text-14">Begin your ASL lesson and share your expertise.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
