<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Confirmation</title>

    <!-- FRAMEWORKS, LIBRARIES -->
    <link rel="shortcut icon" href="{{ asset('asset/img/logo-s.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/lib/bootstrap5/bootstrap.min.css')}}">

    <!-- MAIN STYLES -->
    <link rel="stylesheet" href="{{ asset('assets/css/root.css') }}">
    <style>
        body {
            background-color: #FAFBFD;
        }
    </style>
</head>

<body>

    <div class="flex-center w-100 mt-5 pt-3 mb-4">
        <img src="{{ asset('assets/img/logo-brand-sm.png') }}" alt="logo" height="64">
    </div>
    <div class="flex-center w-100">
        <div class="card w-25 shadow-sm p-2">
            <div class="card-body text-center">
                <h6 class="text-14 poppins-semibold mb-3">Confirm your email</h6>
                <p class="text-muted text-12">For security reasons, we need to verify that it's really you trying to change the email and that the email belongs to you.</p>
                <p class="text-13">Please enter the code we've sent to your email address.</p>
                <form action="{{ route('myprofile.confirm-email') }}" method="post" class="needs-validation" novalidate>
                    @csrf
                    @php
                        $errMsg = 'Please enter the verification code sent to you';

                        if ($errors->has('code'))
                            $errMsg = $errors->first('code');
                    @endphp
                    <input type="hidden" name="profileId" value="{{ $profileId }}">
                    <x-editable-form-section-field
                        maxlength="6"
                        name="code"
                        class="input-code"
                        placeholder="Verification Code"
                        required="true"
                        invalidFeedback="{{ $errMsg }}"/>

                    <div class="text-12 w-100 text-start">New Email:</div>
                    <x-editable-form-section-field disabled value="{{ $newEmail }}" />
                    <div class="text-12 w-100 text-start">Old Email:</div>
                    <x-editable-form-section-field disabled value="{{ $oldEmail }}" />
                    <x-sl-button type="submit" style="primary" text="Continue" class="w-100" />
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/lib/jquery3.7.1/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap5-form-novalidate.js') }}"></script>
    <script>
    $(document).ready(function(){
        $('.input-code').on('input', function() {
            var inputVal = $(this).val();
            // Remove any non-numeric characters
            var numericVal = inputVal.replace(/[^0-9]/g, '');
            // Set the cleaned value back to the input
            $(this).val(numericVal);
        });
    });

    </script>
</body>

</html>
