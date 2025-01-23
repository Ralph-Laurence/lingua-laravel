@php
    $formErrorClass = '';
    $userNameLockStateReadonly = true;
    $emailLockStateReadonly = true;

    $hasErrors = false;
    $errMsg = [
        'username'  => 'Please add a username.',
        'email'     => 'Please add a valid email.'
    ];
    $fields = array_keys($errMsg);

    foreach ($fields as $field)
    {
        if ($errors->has($field))
        {
            $errMsg[$field] = $errors->first($field);
            $hasErrors = true;
        }
    }

    if ($hasErrors)
    {
        $formErrorClass = 'was-validated';
        $userNameLockStateReadonly = false;

        if ($hasPendingEmailUpdate)
            $emailLockStateReadonly = true;
        else
            $emailLockStateReadonly = false;
    }
@endphp
<form action="{{ route('profile.update-account') }}"
      class="needs-validation allow-edit {{ $formErrorClass }}"
      id="form-section-account"
      autocomplete="off"
      method="post"
      novalidate>

    @csrf
    <x-editable-form-section-header
        label="Account Details"
        caption="You can use either your username or email to log in."
        :hidden="$hasErrors"/>

    <x-editable-form-section-field
        type="text"
        name="username"
        allowSpaces="false"
        required="true"
        maxlength="32"
        :locked="$userNameLockStateReadonly"
        placeholder="Username"
        value="{{ old('username', $user['username']) }}"
        originalValue="{{ $user['username'] }}"
        invalidFeedback="{{ $errMsg['username'] }}"/>

    @if ($hasPendingEmailUpdate)

        <x-editable-form-section-control-button
            saveButtonClassList="btn-save-edit" :unlock="$hasErrors"
            cancelButtonClassList="btn-cancel-edit" :unlock="$hasErrors"/>

        </form>

        <div class="alert border text-12 mt-3 mb-2">
            <ul class="mb-0">
                <li class="mb-1">You have a pending email change. We've sent you an email at <span class="text-primary">{{ $pendingEmail }}</span>.</li>
                <li class="mb-1">You can still log in using your old email until you confirm the new one.</li>
                <li>You can also revert this change until you confirm the new email.</li>
            </ul>
        </div>
        <form action="{{ route('myprofile.revert-email') }}" method="post" id="frm-revert-email">
            @csrf
            <div class="row">
                <div class="col">
                    <div class="text-13 mb-2">
                        <label class="text-muted">Old Email</label>
                        <input type="text" class="form-control text-13" disabled value="{{ $user['email'] }}">
                    </div>
                </div>
                <div class="col">
                    <div class="text-13 mb-2">
                        <label class="text-muted">New Email (Pending)</label>
                        <input type="text" class="form-control text-13" disabled value="{{ $pendingEmail }}">
                    </div>
                </div>
            </div>
            <x-sl-button type="submit" style="primary" text="Revert Email" class="btn-revert-email mt-2" icon="fa-undo"/>
        </form>
    @else
        <x-editable-form-section-field
            type="email"
            name="email"
            with-tooltip="Use a valid email so that we can reach you."
            allowSpaces="false"
            required="true"
            maxlength="64"
            :locked="$emailLockStateReadonly"
            placeholder="Email"
            value="{{ old('email', $user['email']) }}"
            originalValue="{{ $user['email'] }}"
            invalidFeedback="{{ $errMsg['email'] }}"
            rootClassList="mb-2"/>

        <x-editable-form-section-control-button
            saveButtonClassList="btn-save-edit" :unlock="$hasErrors"
            cancelButtonClassList="btn-cancel-edit" :unlock="$hasErrors"/>
        </form>
    @endif

@push('dialogs')
    @include('partials.confirmbox')
@endpush
@push('scripts')
    <script src="{{ asset('assets/lib/waitingfor/bootstrap-waitingfor.min.js') }}"></script>
    <script>
        $(document).ready(function()
        {
            function showWaiting()
            {
                waitingDialog.show("Processing...", {
                    headerSize: 6,
                    headerText: "Hold on, this shouldn't take long...",
                    dialogSize: 'sm',
                    contentClass: 'text-13'
                });
            }

            $('#form-section-account').on('submit', function(e) {
                showWaiting();
            });

            $('#frm-revert-email').on('submit', function(e)
            {
                e.preventDefault();

                let prompt = `Do you want to cancel your email change? If you do, your new email will not be saved, and you'll continue using your old email for login.<br><br>Click 'Yes' to proceed.`;
                let form = $(this);

                ConfirmBox.show(prompt, 'Revert Email',
                {
                    onOK: () => {
                        showWaiting();
                        form[0].submit();
                    }
                })
            });
        })
    </script>
@endpush
