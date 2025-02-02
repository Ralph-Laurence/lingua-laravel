@php
    $profile = $user['profile'];

    $formErrorClass = '';
    $lockStateReadonly = true;
    $errCount = 0;
    $errMsg = 'Please write a short note about yourself.';

    if ($errors->has('bio'))
    {
        $errMsg = $errors->first('bio');
        $errCount++;
        $formErrorClass = 'was-validated';
        $lockStateReadonly = false;
    }

    $hasErrors = $errCount > 0;
@endphp

<form action="{{ route('profile.update-bio') }}"
      class="needs-validation allow-edit {{ $formErrorClass }}"
      id="form-section-bio"
      autocomplete="off"
      method="post"
      novalidate>

    @csrf
    <x-editable-form-section-header label="Bio" caption="A short note about yourself" :hidden="$hasErrors"/>

    <div class="input-group has-validation bio-input-group mb-3">
        <textarea id="bio-original" class="d-none">{{$profile['bio']}}</textarea>
        <textarea
            class="form-control p-3 text-13 no-resize mb-1 {{ ($hasErrors ? 'is-invalid' : '') }}"
            id="bio"
            name="bio"
            rows="6"
            placeholder="Write a short catchy note that serves as an opportunity for you to showcase your professional background, competencies, aspirations, and areas of expertise."
            maxlength="180"
            required
            {{ ($hasErrors ? '' : 'readonly') }}>{{ old('bio', $profile['bio']) }}</textarea>
        <div class="invalid-feedback">
            {{ $errMsg }}
        </div>
        <div id="bio-char-counter" class="text-muted text-12">0/0</div>
    </div>

    <x-editable-form-section-control-button
        saveButtonClassList="btn-save-edit" :unlock="$hasErrors"
        cancelButtonClassList="btn-cancel-edit" :unlock="$hasErrors"/>
</form>

@push('styles')
    <style>
    #bio.is-invalid ~ #bio-char-counter { bottom: 0; }
    #bio-char-counter
    {
        position: absolute;
        bottom: -20px;
        right: 0;
    }
    </style>
@endpush
@once
    @push('scripts')
        <script>
            $(() => {

                let bioInput = $('#bio');
                let bioInputMax = bioInput.attr('maxLength');

                updateCharLengthCounter(bioInput.val().length, bioInputMax, '#bio-char-counter');

                bioInput.on('input', function()
                {
                    updateCharLengthCounter(bioInput.val().length, bioInputMax, '#bio-char-counter');

                    if ($(this).val().trim() === '')
                        $(this).addClass('is-invalid');

                    else
                        $(this).removeClass('is-invalid');
                });
            });
            function updateCharLengthCounter(current, max, el)
            {
                $(el).text(`${current}/${max}`);
            }
        </script>
    @endpush
@endonce

