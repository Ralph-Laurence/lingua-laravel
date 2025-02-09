<div class="modal fade" tabindex="-1" id="{{ $as }}" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title poppins-semibold text-14">{{ $title }}</h6>
            </div>
            <div class="modal-body">
                <form id="{{ $formId }}"
                    method="post"
                    data-action-create="{{ $createAction }}"
                    data-action-update="{{ $updateAction }}"
                    data-action-fetch="{{ $fetchAction }}"
                    enctype="multipart/form-data"
                    {{ $attributes->merge(['class' => 'main-form needs-validation']) }} novalidate>

                    @csrf
                    <div class="alert alert-secondary text-12 text-center mb-3">
                        <i class="fas fa-circle-info"></i>
                        Please Advertise <strong>honestly</strong>. Every claim you make must be factually correct, and you
                        should
                        be able to provide the documentary proof you claim to hold.
                    </div>

                    {{ $inputs ?? '' }}

                    <div class="documentary-proof-previewer">
                    </div>
                    <div class="file-upload-input-container">
                        {{-- @error('file-upload')
                        @enderror --}}
                    </div>
                    <div class="d-none">
                        {{-- HIDDEN FIELDS ARE INCLUDED IN THE POST REQUEST --}}
                        {{ $hiddenFields ?? '' }}
                        <input type="submit" class="hdn-submit" />
                    </div>
                </form>
                {{-- HIDDEN BODY CONTENT IS A HIDDEN SLOT INSIDE THE MODAL's BODY --}}
                <div class="d-none">
                    {{ $hiddenBodyContent ?? '' }}
                </div>
            </div>
            <div class="modal-footer">
                <x-sl-button style="secondary" text="Cancel" data-bs-dismiss="modal" />
                <x-sl-button style="primary" class="btn-save" text="Save" />
            </div>
        </div>
    </div>
    {{-- TEMPLATES FOR USE BY FRONTEND (eg Js/Jquery) --}}
    <div class="control-templates d-none">

        {{-- FILE UPLOAD INPUT USEFUL DURING CREATE MODE --}}
        @include('partials.modal-control-templates.file-upload-input-create')

        {{-- FILE UPLOAD INPUT WITH "REVERT" BUTTON TO REMOVE IT FROM DOM. USEFUL WITH UPDATE MODE --}}
        @include('partials.modal-control-templates.file-upload-input-update')

        {{-- UPLOADED DOCUMENTARY PROOF PREVIEWER THUMBNAIL, USEFUL DURING UPDATE MODE --}}
        @include('partials.modal-control-templates.docproof-preview-thumbnail')
    </div>
</div>
@once
    @push('scripts')
        <script src="{{ asset('assets/js/components/doc-proof-upsert-modal.js') }}"></script>
    @endpush
@endonce
