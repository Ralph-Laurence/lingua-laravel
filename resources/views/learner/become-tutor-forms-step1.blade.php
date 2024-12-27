<section id="section-banner" class="forms-section w-50 mx-auto">
    <div class="card border-0">
        <div class="card-body text-center">
            <h5 class="text-secondary">
                Step 1 <small class="text-14">of 3</small>
            </h5>
            <h2>Your Resume</h2>
            <div class="text-14">
                At SignLingua, creating an account is always <strong>FREE</strong>! By creating a tutor account,
                you are joining a huge community of learners and tutors around the world.
            </div>
        </div>
    </div>
</section>

<section id="section-banner" class="forms-section w-50 mx-auto">
    <div class="card border-0">
        <div class="card-body px-0">
            <div class="alert alert-secondary text-12 text-center mb-0">
                <i class="fas fa-circle-info"></i>
                Please ensure you provide accurate and valid information. This can provide reassurance,
                especially to the parents of students.
            </div>
        </div>
    </div>
</section>

<section id="section-basic-details" class="forms-section w-50 mx-auto mb-4">
    <div class="card shadow mx-auto p-4">
        <div class="card-body">
            <h5 class="fw-bold darker-text">Introduce Yourself</h5>
            <small class="text-secondary mb-3 d-block">All fields with <strong>*</strong> are required.</small>
            <label for="bio" class="form-label d-block">My Bio *</label>
            <div class="input-group has-validation bio-input-group mb-3">
                <textarea class="form-control p-3 text-14 no-resize mb-1 {{ $errors->has('bio') ? 'is-invalid' : '' }}" id="bio" name="bio" rows="4"
                    placeholder="Write a short catchy note that serves as an opportunity for you to showcase your professional background, competencies, aspirations, and areas of expertise."
                    maxlength="180" required>{{ old('bio') }}</textarea>
                <div class="invalid-feedback">
                    Please write a short note about yourself.
                </div>
                <div id="bio-char-counter">0/0</div>
            </div>

            <div>
                <label for="about-me" class="form-label">About Me *</label>
                <div id="about-me" class="mb-1 {{ $errors->has('about') ? 'is-invalid' : '' }}"></div>
                <div class="input-group has-validation mb-3" id="about-input-group">
                    <textarea class="form-control d-none" id="about" name="about" rows="4"
                        maxlength="180" required>{{ old('about') }}</textarea>
                    <div class="invalid-feedback">
                        Please provide a descriptive detail about yourself.
                    </div>
                    <div id="about-char-counter">0/0</div>
                </div>
            </div>
        </div>

        <div class="p-2 text-end">
            <button class="btn btn-primary btn-next-step" id="step1-next-button" data-next-frame="1" type="button">Next</button>
        </div>

    </div>
</section>
@push('scripts')
    <script src="{{ asset('assets/lib/katex0.16.9/js/katex.min.js') }}"></script>
    <script src="{{ asset('assets/lib/quilljs2.0.3/js/quill.min.js') }}"></script>
    <script src="{{ asset('assets/lib/maxlength/maxlength.js') }}"></script>
    <script src="{{ asset('assets/js/become-tutor-forms-step1.js') }}"></script>
@endpush
