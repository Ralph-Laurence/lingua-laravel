@php
    $profile = $user['profile'];
    $modalShowOnLoad = '';
    $modalShowDialog = '';

    if ($errors->any() && $errors->has('about'))
    {
        $modalShowOnLoad = 'show';
        $modalShowDialog = 'style="display: block;"';
    }
@endphp
@push('dialogs')
    <div class="modal fade {{ $modalShowOnLoad }}" tabindex="-1" id="aboutmeModal" data-bs-backdrop="static" data-bs-keyboard="false" aria-modal="true"
        role="dialog" {{ $modalShowDialog }}>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title poppins-semibold text-14">
                        <i class="fas fa-pen me-1"></i> About Me
                    </h6>
                </div>
                <div class="modal-body">
                    <form id="frm-about" method="post" action="{{ route('profile.update-about') }}"
                        novalidate class="main-form needs-validation">
                        @csrf

                        <p class="darker-text text-13">Share what makes you unique and what you're most proud of.</p>
                        <div class="alert alert-danger text-12 p-2 text-center d-none" id="alert-error">
                            Please provide a descriptive detail about yourself.
                        </div>
                        <div id="about-me" class="mb-1"></div>
                        <div class="input-group has-validation mb-3" id="about-input-group">
                            <textarea class="d-none" id="about-me-original">{{ $profile['about'] }}</textarea>
                            <textarea class="form-control d-none" id="input-about-me" name="input-about-me" rows="4" maxlength="2000" required>{{ old('about') }}</textarea>
                            <div id="about-char-counter" class="w-100 text-12 text-end text-muted">0/0</div>
                        </div>
                        <div class="d-none">
                            <input type="submit" id="aboutme-hidden-submit"/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <x-slbutton type="button" style="secondary" data-bs-dismiss="modal" text="Cancel" />
                    <x-slbutton type="button" style="primary" text="Save" id="btn-save-edit-aboutme" />
                </div>
            </div>
        </div>

    </div>
@endpush
<div class="w-100 mb-3">
    <div class="d-flex align-items-center mb-2 h-20px">
        <h6 class="poppins-semibold flex-fill mb-0">About Me</h6>
        <button type="button" id="btn-edit-about-me"
            class="btn btn-link btn-sm text-decoration-none text-secondary text-12">
            <i class="fas fa-pen"></i>
            <span>Edit</span>
        </button>
    </div>
    <p class="text-muted text-12 mb-0">Share what makes you unique and what you're most proud of</p>
</div>

<div class="mb-3">
    <textarea class="form-control p-3 text-13 no-resize mb-1" id="about-me-preview" rows="6" readonly></textarea>
</div>
@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/lib/quilljs2.0.3/css/quill.snow.css') }}"/>
        <style>
            #aboutmeModal .ql-editor {
                height: 320px;
            }
        </style>
    @endpush
    @push('scripts')
        <script src="{{ asset('assets/lib/quilljs2.0.3/js/quill.min.js') }}"></script>
    @endpush
@endonce
