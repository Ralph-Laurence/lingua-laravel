<ul class="nav col-12 col-lg-auto ms-lg-3 me-lg-auto mb-2 justify-content-center mb-md-0">
    @php
        $joinAsLearnerHref = route('learner.register');
        $linkToSection = '#join-the-community';
        $becomeATutorHref = route('tutor.register');

        if (request()->is('/'))
        {
            $joinAsLearnerHref = $linkToSection;
            $becomeATutorHref = $linkToSection;
        }
    @endphp
    <li>
        <a href="{{ $joinAsLearnerHref }}" class="nav-link px-2">Join as Learner</a>
    </li>
    <li>
        @if (request()->routeIs('tutor.register'))
            <a class="nav-link link-active px-2">Become a Tutor</a>
        @else
            <a href="{{ $becomeATutorHref }}" class="nav-link px-2">Become a Tutor</a>
        @endif
    </li>
</ul>
