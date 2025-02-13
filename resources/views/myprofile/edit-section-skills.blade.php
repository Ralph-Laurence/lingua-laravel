@php
    use App\Models\FieldNames\ProfileFields;

    $skillsList = $user['profile']->{ProfileFields::Skills} ?? [];
@endphp
@push('dialogs')
    <x-skills-picker-dialog />
    <x-toast-container>
        @include('partials.toast', [
            // This toast is frontend driven, not triggered by backend
            'as' => 'frontendToast',
            'autoClose' => 'true'
        ])
    </x-toast-container>
@endpush
<div class="card shadow-sm mb-5">
    <div class="card-body p-5">
        <div class="row">
            <div class="col-12 col-md-5">
                <x-editable-form-section-header label="Skills"
                    caption="Showcase your capabilities and strengths. You may add more skills as necessary." :hidden="true" />
            </div>
        </div>
        @if (!empty($skillsList))
            <div class="d-flex gap-2 skill-entry">
                @foreach ($skillsList as $k => $v)
                    <div data-skill-value="{{ $k }}" class="badge bg-secondary skill-item ps-3 pe-2 py-2 d-flex align-items-center justify-content-between">
                        <span class="me-2">{{ $v }}</span>
                    </div>
                @endforeach
            </div>
            <div class="d-flex gap-2 align-items-center">
                <form action="{{ route('myprofile.remove-skills') }}" id="frm-remove-skills" method="post">
                    @csrf
                    <x-sl-button style="danger" type="submit" text="Remove All" icon="fa-trash" id="btn-clear-skill" class="mt-3"/>
                </form>
                <x-sl-button type="button" text="Edit" icon="fa-plus" id="btn-edit-skill" class="mt-3"/>
            </div>
        @else
            <div class="text-14 text-muted mb-3">You haven't added your skills yet. Click 'Add' to include one.</div>
            <form action="{{ route('myprofile.add-skills') }}" id="frm-add-skills" method="post">
                @csrf
                <x-sl-button type="button" text="Add" icon="fa-plus" id="btn-add-skill" class="mt-3"/>
                <input type="hidden" name="skillsKeys" id="input-add-skills">
            </form>
        @endif
    </div>

    <div class="d-none">
        <textarea class="d-none"
                  id="skills-datasource"
                  data-update-action="{{ route('myprofile.update-skills') }}">{{ json_encode($skillsList) }}</textarea>
    </div>
</div>
