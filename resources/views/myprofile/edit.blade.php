{{-- @if ($errors->any())
    @dd($errors)
@endif --}}

@extends('shared.base-members')

@push('dialogs')
    @include('partials.messagebox')
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/lib/croppie/croppie.css') }}">
    <style>
        #profile-photo-wrapper {
            border: 2px solid #ccc;
            /* Optional: add a border to see the image boundaries */
        }

        #photo-preview {
            width: 128px;
            height: 128px;
            object-fit: cover;
            /* Scale the image to cover the container */
            object-position: center;
            /* Center the image horizontally and vertically */
            display: block;
            /* Make sure the image behaves like a block element */
            margin: 0 auto;
            /* Center the image horizontally if needed */
        }
    </style>
@endpush
@section('content')
    <div class="container">
        <h6 class="py-3 poppins-semibold">My Profile</h6>

        <div class="card shadow-sm mb-4">
            <div class="card-body p-5">
                <div class="row mx-auto">
                    <div class="col-12 col-md-5">
                        <h6 class="poppins-semibold">Profile Photo</h6>
                        <p class="text-muted text-12">Ensure your profile is always up-to-date with a recent photo.</p>
                        <div class="d-flex w-100 gap-4">
                            <div class="rounded-half-rem overflow-hidden" id="profile-photo-wrapper">
                                <img id="photo-preview" src="{{ $user['photo'] }}">
                            </div>
                            <div class="profile-buttons-wrapper flex-column h-100 d-flex gap-2">
                                <x-sl-button type="button" style="primary" text="Update Photo" class="btn-update-photo" onclick="$('#upload').click()" />
                                <x-sl-button type="button" style="secondary" text="Remove Photo" class="btn-remove-photo"/>
                            </div>
                        </div>
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

        <div class="card shadow-sm mb-4">
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
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/utils.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap5-form-novalidate.js') }}"></script>
    <script src="{{ asset("assets/js/shared/editable-form-section.js") }}"></script>
@endpush

@push('dialogs')
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
