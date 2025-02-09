@php
    use App\Models\FieldNames\ProfileFields;

    $education = $user['profile']->{ProfileFields::Education}; //[]

    $errMsgInstitution = $errors->has('institution')
        ? $errors->first('institution')
        : 'Please enter a valid educational institution.';

    $errMsgDegree = $errors->has('degree') ? $errors->first('degree') : 'Please provide a valid degree title.';

    $errMsgEducationDoc = $errors->has('file-upload')
        ? $errors->first('file-upload')
        : 'Please provide a documentary proof you claim to hold. (PDF max 5MB)';

    $hasErrors = session()->has('education_action_error_type');
    $targetModal = '';
    $hasEditErrors = $hasErrors && session('education_action_error_type') == 'edit';

    if ($hasErrors && session('education_action_error_type') == 'add')
        $targetModal = 'modalAddEducation';

    elseif ($hasEditErrors)
        $targetModal = 'modalEditEducation';
@endphp
@push('dialogsx')
    <x-doc-proof-upsert-modal
        as="modalUpsertEducation"
        createAction="{{ route('myprofile.add-education') }}"
        updateAction="{{ route('myprofile.update-education') }}"
        fetchAction="{{ route('myprofile.fetch-education') }}">
        <x-slot name="inputs">

            <div class="d-flex align-items-center justify-content-around gap-3 w-100 mb-3">

                <div class="date-picker-wrapper d-flex flex-column flex-fill">
                    <label class="text-12 text-secondary">From Year</label>
                    <x-year-combo-box id="edit-year-from" class="year-from" name="year-from" data-value="{{-- $oldEditEducationInputs['year-from'] --}}"/>
                </div>

                <div class="date-picker-wrapper d-flex flex-column flex-fill">
                    <label class="text-12 text-secondary">To Year</label>
                    <x-year-combo-box id="edit-year-to" class="year-to" name="year-to" data-value="{{-- $oldEditEducationInputs['year-to'] --}}"/>
                </div>
            </div>

            <x-editable-form-section-field
                type="text"
                name="institution"
                maxlength="200"
                required="true"
                placeholder="Educational Institution"
                invalidFeedback="{{-- $errMsgInstitution --}}" value="{{-- $oldEditEducationInputs['institution'] --}}" />

            <x-editable-form-section-field
                type="text"
                name="degree"
                placeholder="Degree"
                required="true"
                maxlength="200"
                invalidFeedback="{{-- $errMsgDegree --}}" value="{{-- $oldEditEducationInputs['degree'] --}}" />
        </x-slot>
    </x-doc-proof-upsert-modal>
    {{-- @include('partials.modal-add-education')
    @include('partials.modal-edit-education') --}}
@endpush
<div class="card shadow-sm mb-5">
    <div class="card-body p-5">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="row">
            <div class="col-12 col-md-5">
                <x-editable-form-section-header label="Educational Attainment"
                    caption="Helps potential learners understand your overall qualifications." :hidden="true" />
            </div>
        </div>
        @forelse ($education as $k => $obj)
            <div class="row mx-auto mb-3 border-bottom education-entry">
                <div class="col-12 col-lg-9 col-md-8 ps-0">

                    <div class="d-flex gap-3 w-100 text-14">
                        <div class="year w-15" style="min-width: 85px;">{{ $obj['from'] . '-' . $obj['to'] }}</div>
                        <div class="label flex-fill text-truncate mb-2">
                            <h6 class="poppins-semibold text-14 mb-0 text-truncate institution">
                                {{ $obj['institution'] }}</h6>
                            <p class="text-12 text-muted text-truncate mb-1">{{ $obj['degree'] }}</p>
                            <button class="btn btn-link p-0 text-decoration-none text-12 btn-view-doc-proof"
                                type="button" data-url="{{ $obj['docUrl'] }}">
                                View Document
                            </button>
                        </div>
                    </div>

                </div>
                <div class="col-12 col-lg-3 col-md-4  ps-md-2 ps-0 text-end">
                    <x-sl-button style="secondary" icon="fa-pen" text="Edit" class="btn-edit-education"
                        data-doc-id="{{ $obj['docId'] }}" />
                    <x-sl-button style="danger" icon="fa-trash" text="Delete" class="btn-remove-education"
                        data-doc-id="{{ $obj['docId'] }}" />
                </div>
            </div>
        @empty
            <div class="text-14 text-muted mb-3">You haven't added your education details yet. Click 'Add' to include
                one.</div>
        @endforelse

        <x-sl-button type="primary" text="Add" icon="fa-plus" data-bs-toggle="modal"
            data-bs-target="#modalUpsertEducation"/>
        {{-- <x-sl-button type="primary" text="Add" icon="fa-plus" data-bs-toggle="modal"
            data-bs-target="#modalAddEducation" id="btn-add-education" /> --}}
    </div>

    <div class="d-none">
        <form action="{{ route('myprofile.remove-education') }}" id="frm-remove-education" method="post">
            @csrf
            <input type="hidden" name="docId" id="docId">
        </form>
    </div>
</div>

@push('scripts')
    @if($hasErrors)
    <script>
        document.addEventListener('DOMContentLoaded', function()
        {
            let targetModal = "{{ $targetModal }}";
            let modalEducation = new bootstrap.Modal(document.getElementById(targetModal));
            modalEducation.show();
        });
    </script>
    @endif

    @if($hasEditErrors)
        <script>
            $(document).ready(function()
            {
                if ($('#frm-update-education #oldInputs').length < 1)
                    return;

                const form = $('#frm-update-education');
                const old = JSON.parse( form.find('#oldInputs').val());
                console.log(old)
                for (const key in old)
                {
                    form.find(`#${key}`).val(old[key]);
                }

                form.find('#edit-year-from').selectmenu();
                form.find('#edit-year-to').selectmenu();
            });
        </script>
    @endif
@endpush
