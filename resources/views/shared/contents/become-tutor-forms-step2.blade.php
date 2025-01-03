@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/lib/jquery-ui-1.14.1/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/become-tutor-forms2.css') }}">
@endpush

@push('dialogs')
    <!-- Modal -->
    <div class="modal fade" id="skillsPickerModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="skillsPickerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="skillsPickerModalLabel darker-text">Pick your skills</h6>
                    <button type="button btn-sm" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tbody>
                            @php
                                $keys = array_keys($softSkills);
                                $values = array_values($softSkills);
                            @endphp
                            @foreach(array_chunk($keys, 3) as $chunk)
                                <tr>
                                    @foreach($chunk as $index)
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input skill-checkbox" type="checkbox" name="skillset[]" value="{{ $index }}" id="skill_{{ $index }}">
                                                <label class="form-check-label" for="skill_{{ $index }}">
                                                    {{ $softSkills[$index] }}
                                                </label>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm px-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm px-4" data-bs-dismiss="modal"
                        id="skillsPickerOKbtn">OK</button>
                </div>
            </div>
        </div>
    </div>
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
            <small class="text-secondary mb-3">Your educational background is <span
                    style="color: #FE233A;">required.</span> You may add more as necessary.</small>
            <div class="mb-4" id="education-entries">
                {{-- BEGIN: USE THIS AS ENTRY TEMPLATE --}}
                <div id="education-entries-0" class="entry mt-3 p-3 border border-1 rounded rounded-3">
                    <div class="row mb-2">
                        <div class="col">
                            <label for="education-year-from-0" class="text-14 text-secondary">From Year</label>
                            <select id="education-year-from-0" name="education-year-from-0">
                                @for ($year = $currentYear; $year >= 1980; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col">
                            <label for="education-year-to-0" class="text-14 text-secondary">To Year</label>
                            <select id="education-year-to-0" name="education-year-to-0">
                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="input-group has-validation">
                                <input type="text" id="education-institution-0" name="education-institution-0"
                                    class="form-control" placeholder="Institution" required />
                                <div class="invalid-feedback">
                                    Please provide a valid institution.
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group has-validation">
                                <input type="text" id="education-degree-0" name="education-degree-0"
                                    class="form-control" placeholder="Degree" required />
                                <div class="invalid-feedback">
                                    Please provide a valid degree.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="file-upload">
                        <label for="education-file-upload-0" class="form-label text-secondary text-14">Upload
                            Documentary Proof (PDF only):</label>
                        <div class="input-group has-validation">
                            <input type="file" id="education-file-upload" name="education-file-upload-0"
                                class="form-control text-14" accept="application/pdf" required />
                            <div class="invalid-feedback">
                                Please provide a documentary proof you claim to hold. (PDF max 5MB)
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
            <small class="text-secondary mb-3 msw-justify">If you don't have prior tutoring experience, you may skip
                this part. However, if you have any relevant work experience, particularly related to ASL (American Sign
                Language), we encourage you to share it. This information helps us better understand your background and
                potential as a tutor.</small>
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
            <small class="text-secondary mb-3">If you have any relevant certifications, especially in ASL (American
                Sign Language) or related fields, please share them with us. Otherwise, you may skip this part.</small>
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
            <small class="text-secondary mb-3">This is entirely optional. You add many skills as necessary. No
                documents required.</small>
            <div class="d-flex align-items-center gap-2 mb-1 py-2 flex-wrap" id="skill-entries"></div>
            <textarea name="skills-arr" id="skills-arr" class="d-none">
                {{ old('skills-arr') }}
            </textarea>
            <button type="button" class="btn btn-primary btn-add-entry entry-buttons btn-sm" id="add-skill"
                data-bs-toggle="modal" data-bs-target="#skillsPickerModal">
                <i class="fas fa-plus me-1"></i>Add Skill
            </button>
        </div>
    </div>
</section>

<section id="section-banner" class="forms-section w-50 mx-auto">
    <div class="card border-0">
        <div class="card-body px-0">
            <div class="alert alert-secondary text-12 text-center mb-0">
                <i class="fas fa-circle-info"></i>
                Please double-check your entries before submitting. You may go back and correct them.
                Feel free to submit the form once you have reviewed and corrected your entries.
            </div>
        </div>
    </div>
</section>

<section class="forms-section w-50 mx-auto mb-5">
    <div class="card border-0 mx-auto">
        <div class="card-body px-0 d-flex justify-content-between">
            <button class="btn btn-secondary btn-sm btn-prev-slide" type="button">
                <i class="fas fa-arrow-left"></i>
                <span class="ms-2">Back</span>
            </button>
            {{-- <button class="btn btn-primary btn-sm" id="step2-submit-button"
                type="submit">Submit</button> --}}
            <button class="btn btn-primary btn-sm btn-next-slide" type="button">
                <span class="me-2">Next</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
</section>
