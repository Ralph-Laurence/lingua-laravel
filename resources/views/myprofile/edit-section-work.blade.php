@php
    use App\Models\FieldNames\ProfileFields;

    $workExp = $user['profile']->{ProfileFields::Experience}; //[]

    $errMsgCompany = $errors->has('company')
        ? $errors->first('company')
        : 'Please enter a company name.'; // Please enter the name of the company you worked for

    $errMsgRole = $errors->has('role') ? $errors->first('role') : 'Please provide a valid job title.';
    $errMsgWorkExpDoc = $errors->has('file-upload')
        ? $errors->first('file-upload')
        : 'Please provide a documentary proof you claim to hold. (PDF max 5MB)';

    $hasErrors = session()->has('workexp_action_error_type');
    $targetModal = '';
    $hasEditErrors = $hasErrors && session('workexp_action_error_type') == 'edit';
@endphp
@push('dialogs')
    {{-- @if ($hasErrors && session('education_action_error_type') == 'add')
        @include('partials.modal-add-education', ['formClass' => 'was-validated'])
        @php $targetModal = 'modalAddEducation'; @endphp
    @else
        @include('partials.modal-add-education')
    @endif

    @if ($hasEditErrors)
        @include('partials.modal-edit-education', ['formClass' => 'was-validated has-errors', 'oldInputs' => old()])
        @php $targetModal = 'modalEditEducation'; @endphp
    @else
        @include('partials.modal-edit-education')
    @endif --}}
    @include('partials.modal-add-workexp')
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
                <x-editable-form-section-header label="Work Experience"
                    caption="Highlight your career achievements, the roles you've held, and your experiences related to ASL." :hidden="true" />
            </div>
        </div>
        @forelse ($workExp as $k => $obj)
            <div class="row mx-auto mb-3 border-bottom workexp-entry">
                <div class="col-12 col-lg-9 col-md-8 ps-0">

                    <div class="d-flex gap-3 w-100 text-14">
                        <div class="year w-15" style="min-width: 85px;">{{ $obj['from'] . '-' . $obj['to'] }}</div>
                        <div class="label flex-fill text-truncate mb-2">
                            <h6 class="poppins-semibold text-14 mb-0 text-truncate company">
                                {{ $obj['company'] }}</h6>
                            <p class="text-12 text-muted text-truncate mb-1">{{ $obj['role'] }}</p>
                            <button class="btn btn-link p-0 text-decoration-none text-12 btn-view-doc-proof"
                                type="button" data-url="{{ $obj['docUrl'] }}">
                                View Document
                            </button>
                        </div>
                    </div>

                </div>
                <div class="col-12 col-lg-3 col-md-4  ps-md-2 ps-0 text-end">
                    <x-sl-button style="secondary" icon="fa-pen" text="Edit" class="btn-edit-workexp"
                        data-doc-id="{{ $obj['docId'] }}" />
                    <x-sl-button style="danger" icon="fa-trash" text="Delete" class="btn-remove-workexp"
                        data-doc-id="{{ $obj['docId'] }}" />
                </div>
            </div>
        @empty
            <div class="text-14 text-muted mb-3">You haven't added your work experience details yet. Click 'Add' to include
                one.</div>
        @endforelse

        <x-sl-button type="primary" text="Add" icon="fa-plus" data-bs-toggle="modal"
            data-bs-target="#modalAddWorkExp" id="btn-add-workexp" />
    </div>

    <div class="d-none">
        <form action="{{ route('myprofile.remove-work-exp') }}" id="frm-remove-workexp" method="post">
            @csrf
            <input type="hidden" name="docId" id="docId">
        </form>
    </div>
</div>

@push('scripts')
    @if($hasErrors)
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function()
        {
            let targetModal = "{{ $targetModal }}";
            let modalEducation = new bootstrap.Modal(document.getElementById(targetModal));
            modalEducation.show();
        });
    </script> --}}
    @endif

    @if($hasEditErrors)
        {{-- <script>
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
        </script> --}}
    @endif
@endpush
