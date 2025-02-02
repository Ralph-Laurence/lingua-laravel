@php
    $formClass ??= '';
@endphp
    {{-- @php
        $oldInputs = old();
        dd($oldInputs);
    @endphp --}}

<div class="modal educationModal" tabindex="-1" id="modalEditEducation" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title poppins-semibold text-14">Edit Education</h6>
            </div>
            <div class="modal-body">
                <form action="{{ route('myprofile.update-education') }}"
                      data-action-fetch="{{ route('myprofile.fetch-education') }}"
                      method="post"
                      class="needs-validation {{ $formClass }}" novalidate
                      id="frm-update-education"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="alert alert-secondary text-12 text-center mb-3">
                        <i class="fas fa-circle-info"></i>
                        Advertise <strong>honestly</strong>. Every claim you make must be factually correct, and you should
                        be able to provide documentary proof of educational background you claim to hold.
                    </div>
                    @if ($errors->has('doc_id'))
                    <div class="alert alert-danger text-12 text-center mb-3">
                        @error('doc_id')
                            {{ $message }}
                        @enderror
                    </div>
                    @endif
                    <div class="d-flex align-items-center justify-content-around gap-3 w-100 mb-3">

                        <div class="date-picker-wrapper d-flex flex-column flex-fill">
                            <label class="text-12 text-secondary">From Year</label>
                            <x-year-combo-box id="edit-year-from" class="year-from" name="year-from" data-value="{{ old('year-from', null) }}"/>
                        </div>

                        <div class="date-picker-wrapper d-flex flex-column flex-fill">
                            <label class="text-12 text-secondary">To Year</label>
                            <x-year-combo-box id="edit-year-to" class="year-to" name="year-to" data-value="{{ old('year-to', null) }}"/>
                        </div>

                    </div>

                    <x-editable-form-section-field
                        type="text"
                        name="institution"
                        maxlength="200"
                        required="true"
                        placeholder="Educational Institution"
                        invalidFeedback="{{ $errMsgInstitution }}" value="{{ old('institution', null) }}" />

                    <x-editable-form-section-field
                        type="text"
                        name="degree"
                        placeholder="Degree"
                        required="true"
                        maxlength="200"
                        invalidFeedback="{{ $errMsgDegree }}" value="{{ old('degree', null) }}" />

                    <div class="documentary-proof-previewer">
                        <div class="form-label text-secondary text-13 mb-0">Documentary Proof</div>
                        <div class="file-upload-preview-container row">
                            <div class="col-5 pe-0">
                                <p class="text-12 text-dark poppins-semibold my-1">Preview:</p>
                                <canvas id="pdf-thumbnail" width="128" height="96" class="border rounded-2"></canvas>
                            </div>
                            <div class="col-7">
                                <p class="text-12 mb-1">
                                    <span class="text-dark poppins-semibold">Filename:</span>
                                </p>
                                <p class="text-primary text-12 my-1" id="document-filename"></p>
                                <x-sl-button type="button" style="primary" id="btn-upload-new-education" icon="fa-arrow-up-from-bracket me-1" text="Upload New" />
                            </div>
                        </div>
                    </div>
                    <div class="file-upload-input-container">
                        @error('file-upload')
                        <div class="has-file-errors">
                            <label class="form-label text-secondary text-12">Upload Documentary Proof (PDF only):</label>
                            <div class="input-group has-validation">
                                <input type="file" name="file-upload" class="form-control text-13 rounded-end me-2" accept="application/pdf" required>
                                <div class="invalid-feedback">
                                    {{ $errMsgEducationDoc }}
                                </div>
                                <div class="input-group-addon">
                                    <x-sl-button type="button" class="btn-revert" style="danger" text="Revert" />
                                </div>
                            </div>
                        </div>
                        @enderror
                    </div>
                    <div class="d-none">
                        <input type="hidden" name="doc_id" id="doc_id" value="{{ old('doc_id', null) }}"/>
                        <input type="submit" class="hdn-submit"/>
                        <input type="hidden" id="old-docProofUrl" name="old-docProofUrl" value="{{ old('old-docProofUrl', null) }}">
                        <input type="hidden" id="old-docProofFilename" name="old-docProofFilename" value="{{ old('old-docProofFilename', null) }}">
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
<div class="d-none">
    <div id="education-file-upload-input-template">
        <label class="form-label text-secondary text-12">Upload Documentary Proof (PDF only):</label>
        <div class="input-group has-validation">
            <input type="file" name="file-upload" class="form-control text-13 rounded-end me-2" accept="application/pdf" required>
            <div class="invalid-feedback">
                {{ $errMsgEducationDoc }}
            </div>
            <div class="input-group-addon">
                <x-sl-button type="button" class="btn-revert" style="danger" text="Revert" />
            </div>
        </div>
    </div>
</div>
