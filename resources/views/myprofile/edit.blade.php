@extends('shared.base-members')

@push('scripts')
<script src="{{ asset('assets/js/bootstrap5-form-novalidate.js') }}"></script>
<script src="{{ asset('assets/lib/waitingfor/bootstrap-waitingfor.min.js') }}"></script>
<script src="{{ asset('assets/lib/jquery-ui-1.14.1/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/lib/pdfjs-3.11.174/pdf.js') }}"></script>
<script src="{{ asset('assets/js/utils.js') }}"></script>
<script src="{{ asset('assets/js/components/pdf-thumbnail.js') }}"></script>
<script src="{{ asset('assets/lib/croppie/croppie.min.js') }}"></script>
@endpush

@push('dialogs')
    @include('partials.messagebox')
    @include('partials.confirmbox')
    <x-document-viewer-dialog />
    <x-toast-container>
        @if (session('profile_update_message'))
            @include('partials.toast', [
                'toastMessage'  => session('profile_update_message'),
                'toastTitle'    => 'Update Profile',
                'autoClose'     => 'true'
            ])
        @endif
    </x-toast-container>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/lib/croppie/croppie.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/lib/jquery-ui-1.14.1/jquery-ui.min.css') }}">
    <style>
        .ui-selectmenu-menu .ui-menu {
            max-height: 185px;
        }

        .ui-selectmenu-button {
            flex: 1 1 auto !important;
            width: auto !important;
        }

        .ui-selectmenu-open {
            position: absolute;
            z-index: 65535;
        }

        .ui-state-active,
        .ui-widget-content .ui-state-active,
        .ui-widget-header .ui-state-active,
        a.ui-button:active,
        .ui-button:active,
        .ui-button.ui-state-active:hover {
            border: 1px solid #F88D0C;
            background: #FFA30E;
            font-weight: normal;
            color: #fff;
        }
    </style>
@endpush
@section('content')
    <div class="container">
        <h6 class="py-3 poppins-semibold">My Profile</h6>

        <div class="card shadow-sm mb-5">
            <div class="card-body p-5">
                <div class="row mx-auto">
                    <div class="col-12 col-md-5">
                        @include('myprofile.edit-section-photo')
                    </div>
                    <div class="col-12 col-md-2 gx-0 flex-center">
                        <div class="border-end h-100 w-0"></div>
                    </div>
                    <div class="col-12 col-md-5">
                        @include('myprofile.edit-section-account')
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body p-5">
                <div class="row mx-auto">
                    <div class="col-12 col-md-5">
                        @include('myprofile.edit-section-identity')
                    </div>
                    <div class="col-12 col-md-2 gx-0 flex-center">
                        <div class="border-end h-100 w-0"></div>
                    </div>
                    <div class="col-12 col-md-5">
                        @include('myprofile.edit-section-password')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!empty($user['profile']))
        <div class="container">
            <h6 class="py-3 poppins-semibold">My Resume</h6>

            <div class="card shadow-sm mb-5">
                <div class="card-body p-5">
                    <div class="row mx-auto">
                        <div class="col">
                            @include('myprofile.edit-section-bio')
                        </div>
                        <div class="col-12 col-md-2 gx-0 flex-center">
                            <div class="border-end h-100 w-0"></div>
                        </div>
                        <div class="col">
                            @include('myprofile.edit-section-disability')
                        </div>
                        {{-- <div class="col">
                            @include('myprofile.edit-section-about-me')
                        </div> --}}
                    </div>
                </div>
            </div>
            @include('myprofile.edit-section-education')
            @include('myprofile.edit-section-work')
        </div>

    @endif
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/my-profile/my-profile.js') }}"></script>
    <script src="{{ asset("assets/js/shared/editable-form-section.js") }}"></script>
    <script src="{{ asset('assets/js/my-profile/edit-section-education.js') }}"></script>
    <script src="{{ asset('assets/js/my-profile/edit-section-workexp.js') }}"></script>
@endpush
