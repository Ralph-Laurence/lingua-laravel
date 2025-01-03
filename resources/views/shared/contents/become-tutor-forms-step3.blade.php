<section id="section-banner" class="forms-section w-50 mx-auto">
    <div class="card border-0">
        <div class="card-body text-center">
            <h5 class="text-secondary">
                Step 3 <small class="text-14">of 3</small>
            </h5>
            <h2>Quick Test</h2>
            <div class="text-14">
                As a part of our commitment to building a knowledgeable and legitimate community, we ask our aspiring tutors
                to demonstrate their sign language proficiency. Please complete the verification below by identifying the
                sign language letters shown in the images.
            </div>
        </div>
    </div>
</section>

<section class="forms-section w-75 mx-auto mb-4">
    <div class="card shadow mx-auto p-4">
        <div class="card-body text-center">
            <h5 class="darker-text">Prove Your Fluency</h5>
            <div class="text-secondary flex-center w-100">
                <h6 class="text-14" style="width: 500px;">
                    You need to complete the test before you can submit your registration.
                    Identify and enter the correct letter for each sign shown in the images.
                </h6>
            </div>
            {{-- <div class="d-flex align-items-center gap-2 mb-1 py-2 flex-wrap" id="skill-entries"></div> --}}
            <div class="alert alert-danger text-14 text-center captcha-error d-none">
                Incorrect letters given. Please try again.
            </div>
            <div class="alert alert-success text-14 text-center captcha-passed d-none">
                <i class="fas fa-check me-2"></i>You passed the test! You may now submit your registration.
            </div>
            <div id="captcha-box" class="flex-center gap-3 w-100 mb-4">

            </div>
            <input type="hidden" id="captcha_csrf" value="{{ csrf_token() }}">
            <input type="hidden" id="captcha_text">
            @if (isset($showConvertAccWarning) && $showConvertAccWarning)
                @push('scripts')
                    <script>
                        $(() => {
                            $('#agreementCheck').on('change', function()
                            {
                                $('#btn-submit').prop('disabled', !this.checked);
                            });
                        })
                    </script>
                @endpush
                <div class="alert alert-warning submit-target mx-auto px-5" style="width: 500px;">
                    <p class="text-12">Your account will be permanently converted to a tutor account, resulting in the loss of all current tutor connections.</p>
                    <div class="agreement-box">
                        <div class="form-check d-inline-block mb-2">
                          <input class="form-check-input" type="checkbox" value="" id="agreementCheck">
                          <label class="form-check-label text-14" for="agreementCheck">
                            I understand and wish to proceed
                          </label>
                        </div>
                        <button id="btn-submit" class="btn btn-primary btn-sm w-100" type="submit" disabled>Submit Registration</button>
                    </div>
                </div>
            @else
                <button id="btn-submit" class="btn btn-primary btn-sm submit-target" type="submit" disabled>Submit Registration</button>
            @endif
        </div>
    </div>
</section>

@push('styles')
    <style>
        .alert.submit-target {
            display: none;
        }

        #captcha-box img {
            border-radius: .5rem;
            height: 100px;
            width: 100px;
        }
    </style>
@endpush
