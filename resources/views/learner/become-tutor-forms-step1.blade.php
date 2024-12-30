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

<section id="section-basic-details" class="forms-section w-50 mx-auto mb-5">
    <div class="card shadow mx-auto p-4">
        <div class="card-body">
            <h5 class="fw-bold darker-text">Introduce Yourself</h5>
            <small class="text-secondary mb-3 d-block">All fields with <strong>*</strong> are required.</small>
            <p for="bio" class="form-label fw-bold">My Bio *</p>
            <p for="bio" class="darker-text text-14">Write a short note about yourself.</p>
            <div class="input-group has-validation bio-input-group mb-3">
                <textarea class="form-control p-3 text-14 no-resize mb-1 {{ $errors->has('bio') ? 'is-invalid' : '' }}" id="bio" name="bio" rows="4"
                    placeholder="Write a short catchy note that serves as an opportunity for you to showcase your professional background, competencies, aspirations, and areas of expertise."
                    maxlength="180" required>{{ old('bio') }}</textarea>
                <div class="invalid-feedback">
                    Please write a short note about yourself.
                </div>
                <div id="bio-char-counter">0/0</div>
            </div>

            <div id="about-me-editor">
                <p for="bio" class="form-label fw-bold">About Me *</p>
                <p class="darker-text text-14">Share what makes you unique and what you're most proud of.</p>
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

            <div class="fluency-options mb-3">
                <p class="form-label fw-bold">ASL Fluency *</p>
                <p for="fluency-level" class="text-14">How fluent are you with American Sign Language?</p>
                <div class="alert alert-secondary text-12 px-1">
                    <ul class="mb-0">
                        @foreach ($fluencyOptions as $key => $obj)
                            <li>
                                <span class="fw-bold" style="width: 90px; display: inline-block;">{{ $obj['Level'] }}</span> - {{ $obj['Description'] }}
                            </li>
                        @endforeach

                    </ul>
                </div>
                <select id="fluency-level" name="fluency-level">
                    @foreach ($fluencyOptions as $key => $obj)
                        <option data-description="{{ $obj['Description'] }}" value="{{ $key }}">{{ $obj['Level'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="p-2 text-end">
            <button class="btn btn-primary btn-sm btn-next-slide" type="button">
                <span class="me-2">Next</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>

    </div>
</section>
