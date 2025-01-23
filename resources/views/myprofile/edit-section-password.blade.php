@php
    $formErrorClass = '';
    $lockStateReadonly = true;
    $passwordErrors = 0;
    $errMsg = [
        'current_password'      => 'Please enter your current password.',
        'new_password'          => 'Please enter a new password.',
        'password_confirmation' => 'Please re-enter your new password.'
    ];
    $fields = array_keys($errMsg);

    foreach ($fields as $field)
    {
        if ($errors->has($field))
        {
            $errMsg[$field] = $errors->first($field);
            $passwordErrors++;
        }
    }

    if ($passwordErrors > 0)
    {
        $formErrorClass = 'was-validated';
        $lockStateReadonly = false;
    }
@endphp
<form action="{{ route('profile.update-password') }}"
      class="needs-validation allow-edit {{ $formErrorClass }}"
      id="form-section-password"
      autocomplete="off"
      method="post"
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
