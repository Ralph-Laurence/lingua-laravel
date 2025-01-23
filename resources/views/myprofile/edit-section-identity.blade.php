@php
    $formErrorClass = '';
    $lockStateReadonly = true;
    $hasErrors = 0;
    $errMsg = [
        'firstname' => 'Please enter your firstname.',
        'lastname'  => 'Please enter your lastname.',
        'contact'   => 'Please add a valid contact number.',
        'address'   => 'Please add your address.'
    ];
    $fields = array_keys($errMsg);

    foreach ($fields as $field)
    {
        if ($errors->has($field))
        {
            $errMsg[$field] = $errors->first($field);
            $hasErrors ++;
            error_log("Has Error on -> $field");
        }
    }

    if ($hasErrors > 0)
    {
        error_log("Yes has Error");
        error_log($errMsg['contact']);
        $formErrorClass = 'was-validated';
        $lockStateReadonly = false;
    }
@endphp
<form action="{{ route('profile.update-identity') }}"
      class="needs-validation allow-edit {{ $formErrorClass }}"
      id="form-section-identity"
      autocomplete="off"
      method="post"
      novalidate>

    @csrf
    <x-editable-form-section-header
        label="Identity & Contact"
        caption="This can help us and others to uniquely identify and reach you."
        :hidden="$hasErrors > 0"/>

    <div class="d-flex align-items-center gap-3">

        <x-editable-form-section-field
            type="text"
            name="firstname"
            required="true"
            maxlength="32"
            placeholder="Firstname"
            :locked="$lockStateReadonly"
            invalidFeedback="{{ $errMsg['firstname'] }}"
            value="{{ old('firstname', $user['firstname']) }}"
            originalValue="{{ $user['firstname'] }}"/>

        <x-editable-form-section-field
            type="text"
            name="lastname"
            required="true"
            maxlength="32"
            placeholder="Lastname"
            :locked="$lockStateReadonly"
            invalidFeedback="{{ $errMsg['lastname'] }}"
            originalValue="{{ $user['lastname'] }}"
            value="{{ old('lastname', $user['lastname']) }}"/>
    </div>

    <x-editable-form-section-field
            type="tel"
            name="contact"
            required="true"
            maxlength="10"
            placeholder="9xxxxxxxxx"
            allowSpaces="false"
            with-tooltip="Phone numbers in the Philippines should start with a '9' after +63."
            invalidFeedback="{{ $errMsg['contact'] }}"
            originalValue="{{ $user['contact'] }}"
            :locked="$lockStateReadonly"
            value="{{ old('contact', $user['contact']) }}"/>

    <x-editable-form-section-field
            type="text"
            name="address"
            required="true"
            maxlength="150"
            placeholder="Address"
            :locked="$lockStateReadonly"
            invalidFeedback="{{ $errMsg['address'] }}"
            originalValue="{{ $user['address'] }}"
            value="{{ old('address', $user['address']) }}"/>

    <x-editable-form-section-control-button
            saveButtonClassList="btn-save-edit" :unlock="$hasErrors"
            cancelButtonClassList="btn-cancel-edit" :unlock="$hasErrors"/>
</form>
