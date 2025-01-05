<section class="d-none popover-template position-fixed top-0 bg-white">
    <div id="popover-template" class="d-flex flex-column align-items-center p-1">
        <div class="rounded rounded-3 text-center w-100 mb-2 py-2 learner-details-title">
            Learner Details
        </div>
        <div class="learner-details-photo-container position-relative">
            <img class="learner-details-photo position-absolute top-0 left-0 centered-image shadow" src="{{ asset('assets/img/default_avatar.png') }}"/>
            <div class="learner-details-name position-absolute text-center p-2"></div>
        </div>
        <div class="learner-fluency-container text-center p-2 w-100">
            <span class="badge learner-details-proficiency"></span>
        </div>
        <div class="learner-information text-secondary d-flex flex-column align-items-center gap-1 text-13">
            <p class="mb-0 text-center">
                <i class="fas fa-phone me-2"></i>
                <span class="learner-details-contact"></span>
            </p>
            <p class="mb-0 text-center">
                <i class="fa-brands fa-google me-2"></i>
                <span class="learner-details-email"></span>
            </p>
            <p class="mb-3 text-center">
                <i class="fas fa-location-dot me-2"></i>
                <span class="learner-details-address"></span>
            </p>
        </div>
        <button type="button" class="btn btn-primary btn-sm w-100 btn-close-popover">OK, Close</button>
    </div>
</section>
<form class="d-none" id="frm-fetch-learner">
    <input type="hidden" id="fetch-url" value="{{ route('tutor.learners-show') }}">
</form>
@push('styles')
    <style>
        .popover {
            width: fit-content;
            min-width: 250px;
            max-width: 300px;
            font-family: 'Poppins';
        }
        .popover-body {
            overflow: hidden;
        }
        .learner-details-photo-container {
            width: 200px;
            height: 200px;
        }
        .learner-details-photo {
            width: 200px;
            height: 200px;
            border-radius: 8px;
            margin-left: 0;
            margin-right: 0;
        }
        .learner-details-name {
            bottom: 14px;
            left: 14px;
            right: 14px;
            border-radius: 2rem;
            background-color: rgba(0, 0, 0, 0.85);
            font-size: 13px;
            color: white;
            font-family: 'Poppins-Medium';
        }
        .popover-template {
            width: 250px;
            max-width: 400px;
            overflow: hidden;
        }
        .learner-details-title {
            background: #F6F6FA;
            color: #212529;
            font-family: 'Poppins-SemiBold';
            font-size: 13px;
            width: auto;
        }

    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/learner-popover.js') }}"></script>
@endpush
