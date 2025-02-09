@php
    use App\Models\FieldNames\ProfileFields;

    $workExp = $user['profile']->{ProfileFields::Experience};
@endphp
@push('dialogs')
    <x-doc-proof-upsert-modal
        as="workExpModal"
        createAction="{{ route('myprofile.add-work-exp') }}"
        fetchAction="{{ route('myprofile.fetch-workexp') }}"
        updateAction="{{ route('myprofile.update-work-exp') }}">

        <x-slot name="inputs">
            <div class="d-flex align-items-center justify-content-around gap-3 w-100 mb-3">

                <div class="date-picker-wrapper d-flex flex-column flex-fill">
                    <label class="text-12 text-secondary">From Year</label>
                    <x-year-combo-box id="work-year-from" class="year-from" name="work-year-from" {{-- data-value="{{ old('work-year-from', null) }}"--}}/>
                </div>

                <div class="date-picker-wrapper d-flex flex-column flex-fill">
                    <label class="text-12 text-secondary">To Year</label>
                    <x-year-combo-box id="work-year-to" class="year-to" name="work-year-to" {{-- data-value="{{ old('work-year-to', null) }}" --}}/>
                </div>
            </div>

            <x-editable-form-section-field
                type="text"
                name="company"
                maxlength="200"
                required="true"
                placeholder="Company or Organization"
                invalidFeedback="Please enter the name of the company you worked for."
                feedbackMode="manual" />

            <x-editable-form-section-field
                type="text"
                name="role"
                placeholder="Role or Position"
                required="true"
                maxlength="200"
                invalidFeedback="Please provide a valid job title."
                feedbackMode="manual" />
                {{-- value="{{ old('role', null) }}" /> --}}
        </x-slot>

        <x-slot name="hiddenBodyContent">
            @if (session()->has('validationErrors'))
                <textarea class="error-bag">{{ session('validationErrors') }}</textarea>
                <textarea class="old-inputs">{{ session('oldInputs') }}</textarea>
            @endif
        </x-slot>
    </x-doc-proof-upsert-modal>
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
                    <x-sl-button style="secondary" icon="fa-pen" text="Edit" class="btn-edit-workexp disabled"
                        data-doc-id="{{ $obj['docId'] }}" />
                    <x-sl-button style="danger" icon="fa-trash" text="Delete" class="btn-remove-workexp"
                        data-doc-id="{{ $obj['docId'] }}" />
                </div>
            </div>
        @empty
            <div class="text-14 text-muted mb-3">You haven't added your work experience details yet. Click 'Add' to include
                one.</div>
        @endforelse

        <x-sl-button type="primary" text="Add" icon="fa-plus" id="btn-add-workexp" />
    </div>

    <div class="d-none">
        <form action="{{ route('myprofile.remove-work-exp') }}" id="frm-remove-workexp" method="post">
            @csrf
            <input type="hidden" name="docId" id="docId">
        </form>
    </div>
</div>
