@php
    $formClassAddEducation = '';

    $oldAddEducationInputs = [
            'year-from'     => '',
            'year-to'       => '',
            'institution'   => '',
            'degree'        => ''
        ];

    if (session()->has('education_action_error_type') && session('education_action_error_type') == 'add')
    {
        $formClassAddEducation = 'was-validated';
        $oldAddEducationInputs = [
            'year-from'             => old('year-from', null),
            'year-to'               => old('year-to', null),
            'institution'           => old('institution', null),
            'degree'                => old('degree', null),
        ];
    }
@endphp
<div class="modal" tabindex="-1" id="modalAddEducation" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title poppins-semibold text-14">Add Education</h6>
            </div>
            <div class="modal-body">
                <form action="{{ route('myprofile.add-education') }}"
                      method="post"
                      class="needs-validation {{ $formClassAddEducation }}" novalidate
                      id="frm-add-education"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="alert alert-secondary text-12 text-center mb-3">
                        <i class="fas fa-circle-info"></i>
                        Advertise <strong>honestly</strong>. Every claim you make must be factually correct, and you should
                        be able to provide documentary proof of educational background you claim to hold.
                    </div>
                    <div class="d-flex align-items-center justify-content-around gap-3 w-100 mb-3">

                        <div class="date-picker-wrapper d-flex flex-column flex-fill">
                            <label class="text-12 text-secondary">From Year</label>
                            <x-year-combo-box as="year-from" class="year-from" data-value="{{ $oldAddEducationInputs['year-from'] }}"/>
                        </div>

                        <div class="date-picker-wrapper d-flex flex-column flex-fill">
                            <label class="text-12 text-secondary">To Year</label>
                            <x-year-combo-box as="year-to" class="year-to" data-value="{{ $oldAddEducationInputs['year-to'] }}"/>
                        </div>

                    </div>

                    <x-editable-form-section-field
                        type="text"
                        name="institution"
                        maxlength="200"
                        required="true"
                        placeholder="Educational Institution"
                        invalidFeedback="{{ $errMsgInstitution }}" value="{{ $oldAddEducationInputs['institution'] }}" />

                    <x-editable-form-section-field
                        type="text"
                        name="degree"
                        placeholder="Degree"
                        required="true"
                        maxlength="200"
                        invalidFeedback="{{ $errMsgDegree }}" value="{{ $oldAddEducationInputs['degree'] }}" />

                    <div class="file-upload">
                        <label class="form-label text-secondary text-13">Upload
                            Documentary Proof (PDF only):</label>
                        <div class="input-group has-validation">
                            <input type="file" name="file-upload" class="form-control text-13" accept="application/pdf" required>
                            <div class="invalid-feedback">
                                {{ $errMsgEducationDoc }}
                            </div>
                        </div>
                    </div>
                    <div class="d-none">
                        <input type="submit" class="hdn-submit"/>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <x-sl-button style="secondary" text="Cancel" data-bs-dismiss="modal" />
                <x-sl-button style="primary" id="btn-save" text="Save" />
            </div>
        </div>
    </div>
</div>
