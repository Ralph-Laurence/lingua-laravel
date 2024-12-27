@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/lib/jquery-ui-1.14.1/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/become-tutor-forms2.css') }}">
@endpush

<section id="section-banner" class="forms-section w-50 mx-auto">
    <div class="card border-0">
        <div class="card-body text-center">
            <h5 class="text-secondary">
                Step 2 <small class="text-14">of 3</small>
            </h5>
            <h2>Documentary Proof</h2>
            <div class="text-14">
                Providing these documents establishes your credibility as a qualified tutor and helps build trust with
                potential students and their parents. They need to be confident that you have the necessary knowledge
                and skills to teach effectively.
            </div>
        </div>
    </div>
</section>

<section id="section-banner" class="forms-section w-50 mx-auto">
    <div class="card border-0">
        <div class="card-body px-0">
            <div class="alert alert-secondary text-12 text-center mb-0">
                <i class="fas fa-circle-info"></i>
                Advertise <strong>honestly</strong>. Every claim you make must be factually correct, and you should
                be able to provide documentary proof of any qualification you claim to hold.
            </div>
        </div>
    </div>
</section>

<section class="forms-section w-50 mx-auto mb-4">
    <div class="card shadow mx-auto p-4">
        <div class="card-body">
            <h5 class="darker-text">Education</h5>
            <small class="text-secondary mb-3">Your educational background is <span style="color: #FE233A;">required.</span> You may add more as necessary.</small>
            <div class="mb-4" id="education-entries">
                {{-- BEGIN: USE THIS AS ENTRY TEMPLATE --}}
                <div id="education-entries-1" class="entry mt-3 p-3 border border-1 rounded rounded-3">
                    <div class="row mb-2">
                        <div class="col">
                            <label for="education-year-from" class="text-14 text-secondary">From Year</label>
                            <select id="education-year-from" name="education-year-from[0]">
                                @for ($year = $currentYear; $year >= 1980; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col">
                            <label for="education-year-to" class="text-14 text-secondary">To Year</label>
                            <select id="education-year-to" name="education-year-to[0]">
                                @for ($year = $currentYear; $year >= 1980; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="input-group has-validation">
                                <input type="text" id="education-institution" name="education-institution[0]" class="form-control" placeholder="Institution" value="{{ old('education-institution.0') }}" required />
                                <div class="invalid-feedback">
                                    Please provide your educational institution.
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group has-validation">
                                <input type="text" id="education-degree" name="education-degree[0]" class="form-control" placeholder="Degree" value="{{ old('education-degree.0') }}" required />
                                <div class="invalid-feedback">
                                    Please enter a valid degree.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="file-upload">
                        <label for="education-file-upload-0" class="form-label text-secondary text-14">Upload Documentary Proof (PDF only):</label>
                        <div class="input-group has-validation">
                            <input type="file" id="education-file-upload" name="education-file-upload[0]" class="form-control text-14" accept="application/pdf" required />
                            <div class="invalid-feedback">
                                Please provide a documentary proof of education you claim to hold.
                            </div>
                        </div>
                    </div>
                </div>
                {{-- END --}}
            </div>
            <button type="button" class="btn btn-primary entry-buttons btn-add-entry btn-sm" id="add-education">
                <i class="fas fa-plus me-1"></i>Add Education
            </button>
        </div>
    </div>
</section>

<section class="forms-section w-50 mx-auto mb-4">
    <div class="card shadow mx-auto p-4">
        <div class="card-body">
            <h5 class="darker-text">Work Experience</h5>
            <small class="text-secondary mb-3 msw-justify">If you don't have prior tutoring experience, you may skip this part. However, if you have any relevant work experience, particularly related to ASL (American Sign Language), we encourage you to share it. This information helps us better understand your background and potential as a tutor.</small>
            <div class="mb-4" id="work-entries"></div>
            <button type="button" class="btn btn-primary entry-buttons btn-add-entry btn-sm" id="add-work">
                <i class="fas fa-plus me-1"></i>Add Work
            </button>
        </div>
    </div>
</section>

<section class="forms-section w-50 mx-auto mb-4">
    <div class="card shadow mx-auto p-4">
        <div class="card-body">
            <h5 class="darker-text">Certifications</h5>
            <small class="text-secondary mb-3">If you have any relevant certifications, especially in ASL (American Sign Language) or related fields, please share them with us. Otherwise, you may skip this part.</small>
            <div class="mb-4" id="cert-entries"></div>
            <button type="button" class="btn btn-primary entry-buttons btn-add-entry btn-sm" id="add-cert">
                <i class="fas fa-plus me-1"></i>Add Certification
            </button>
        </div>
    </div>
</section>

<section class="forms-section w-50 mx-auto mb-4">
    <div class="card shadow mx-auto p-4">
        <div class="card-body">
            <h5 class="darker-text">Skills & Abilities</h5>
            <small class="text-secondary mb-3">This is entirely optional. You add many skills as necessary. No documents required.</small>
            <div class="mb-4" id="education-entries"></div>
            <button type="button" class="btn btn-sm btn-primary" id="add-skill">Add Skill</button>
        </div>
    </div>
</section>

<section class="forms-section w-50 mx-auto mb-4">
    <div class="card border-0 mx-auto p-4">
        <div class="card-body">
            <button class="btn btn-secondary btn-prev-step" data-prev-frame="0" type="button">Back</button>
            {{-- <button class="btn btn-primary btn-next-step" data-prev-frame="0" data-next-frame="1" type="button">Next</button> --}}
            <button class="btn btn-primary btn-next-step" id="step2-submit-button" type="submit">Submit</button>
        </div>
    </div>
</section>

@push('scripts')
    <script src="{{ asset('assets/lib/jquery-ui-1.14.1/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/become-tutor-forms-step2.js') }}"></script>
@endpush
