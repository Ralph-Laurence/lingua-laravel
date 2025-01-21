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
                                <x-sl-button type="button" style="secondary" text="Update Photo" class="btn-update-photo" onclick="$('#upload').click()" />
                                <x-sl-button type="button" style="danger" text="Remove Photo" class="btn-remove-photo"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-2 gx-0 flex-center">
                        <div class="border-end h-100 w-0"></div>
                    </div>
                    <div class="col-12 col-md-5">
                        <form action="" id="form-section-account" method="post" class="needs-validation allow-edit" novalidate>

                            <x-editable-form-section-header label="Account Details" caption="You can use either your username or email to log in."/>

                            <x-editable-form-section-field
                                type="text" name="username"
                                allowSpaces="false" required="true" readonly="true" maxlength="32"
                                invalidFeedback="Please add a unique username."
                                value="{{ old('username', $user['username']) }}"/>

                            <x-editable-form-section-field
                                type="email" name="email" with-tooltip="Use a valid email so that we can reach you."
                                allowSpaces="false" required="true" readonly="true" maxlength="64"
                                invalidFeedback="Please add a valid email."
                                value="{{ old('email', $user['email']) }}"/>

                            <x-editable-form-section-control-button />
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body p-5">
                <div class="row mx-auto">
                    <div class="col-12 col-md-5">
                        <form action="" id="form-section-identity" method="post" class="needs-validation allow-edit" novalidate>

                            <x-editable-form-section-header label="Identity & Contact" caption="This can help us and others to uniquely identify and reach you."/>

                            <div class="d-flex align-items-center gap-3">
                                <x-editable-form-section-field
                                    type="text" name="firstname"
                                    required="true" readonly="true" maxlength="32"
                                    invalidFeedback="Please enter your firstname."
                                    value="{{ old('firstname', $user['firstname']) }}"/>

                                <x-editable-form-section-field
                                    type="text" name="lastname"
                                    required="true" readonly="true" maxlength="32"
                                    invalidFeedback="Please enter your lastname."
                                    value="{{ old('lastname', $user['lastname']) }}"/>
                            </div>

                            <x-editable-form-section-field
                                    type="tel" name="contact"
                                    required="true" readonly="true" maxlength="10"
                                    placeholder="9123456789" with-tooltip="Phone numbers in the Philippines should start with a '9' after +63."
                                    invalidFeedback="Please add a valid contact number."
                                    value="{{ old('contact', $user['contact']) }}"/>

                            <x-editable-form-section-field
                                    type="text" name="address"
                                    required="true" readonly="true" maxlength="150"
                                    placeholder="Address"
                                    invalidFeedback="Please add your address."
                                    value="{{ old('address', $user['address']) }}"/>

                            <x-editable-form-section-control-button />
                        </form>
                    </div>
                    <div class="col-12 col-md-2 gx-0 flex-center">
                        <div class="border-end h-100 w-0"></div>
                    </div>
                    <div class="col-12 col-md-5">
                        {{-- @if ($errors->any())
                            @dd($errors)
                        @endif --}}
                        @php
                            $passwordErrors     = 0;
                            $formErrorClass     = '';
                            $lockStateReadonly  = true;

                            $errMsg = [
                                'current_password'        => 'Please enter your current password.',
                                'new_password'            => 'Please enter a new password.',
                                'password_confirmation'   => 'Please re-enter your new password'
                            ];

                            if ($errors->has('current_password'))
                            {
                                $errMsg['current_password'] = $errors->first('current_password');
                                $passwordErrors++;
                            }

                            if ($errors->has('new_password'))
                            {
                                $errMsg['new_password'] = $errors->first('new_password');
                                $passwordErrors++;
                            }

                            if ($errors->has('password_confirmation'))
                            {
                                $errMsg['password_confirmation'] = $errors->first('password_confirmation');
                                $passwordErrors++;
                            }

                            if ($passwordErrors > 0)
                            {
                                $formErrorClass     = 'was-validated';
                                $lockStateReadonly  = false;
                            }

                        @endphp
                        <form action="{{ route('profile.update-password') }}"
                              autocomplete="off" id="form-section-password"
                              method="post"
                              class="needs-validation allow-edit {{ $formErrorClass }}"
                              novalidate>

                            @csrf
                            <x-editable-form-section-header
                                label="Update Password"
                                caption="Regularly update your password to stay secure."
                                :hidden="$passwordErrors > 0"/>

                            <x-editable-form-section-field
                                type="password" name="current_password" placeholder="Current Password"
                                allowSpaces="false" with-tooltip="false" required="true" :locked="$lockStateReadonly" maxlength="64"
                                invalidFeedback="{{ $errMsg['current_password'] }}" autocomplete="new-password"/>

                            <x-editable-form-section-field
                                type="password" name="new_password" placeholder="New Password"
                                allowSpaces="false" with-tooltip="false" required="true" :locked="$lockStateReadonly" maxlength="64"
                                invalidFeedback="{{ $errMsg['new_password'] }}" autocomplete="new-password"/>

                            <x-editable-form-section-field
                                type="password" name="password_confirmation" placeholder="Confirm Password"
                                allowSpaces="false" with-tooltip="false" required="true" :locked="$lockStateReadonly" maxlength="64"
                                invalidFeedback="{{ $errMsg['password_confirmation'] }}" autocomplete="new-password"/>

                            <x-editable-form-section-control-button
                                saveButtonClassList="btn-save-edit" :unlock="$passwordErrors > 0"
                                cancelButtonClassList="btn-cancel-edit" :unlock="$passwordErrors > 0"/>
                        </form>
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
