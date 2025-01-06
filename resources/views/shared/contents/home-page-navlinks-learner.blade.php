<ul class="nav col-12 col-lg-auto ms-lg-3 me-lg-auto mb-2 justify-content-center mb-md-0">
    <li>
        <a href="{{ request()->routeIs('learner.find-tutors') ? '#' : route('learner.find-tutors') }}" class="nav-link px-2 {{ request()->routeIs('learner.find-tutors') ? 'link-active' : '' }}">Find Tutors</a>
    </li>
    <li>
        <a href="{{ request()->routeIs('mytutors') ? '#' : route('mytutors') }}" class="nav-link px-2 {{ request()->routeIs('mytutors') ? 'link-active' : '' }}">My Tutors</a>
    </li>
    <li>
        <a href="{{ (request()->routeIs('become-tutor') || request()->routeIs('become-tutor.forms')) ? '#' : route('become-tutor') }}" class="nav-link px-2 {{ (request()->routeIs('become-tutor') || request()->routeIs('become-tutor.forms')) ? 'link-active' : '' }}">Become a Tutor</a>
    </li>
</ul>
