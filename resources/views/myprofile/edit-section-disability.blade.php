@php
    $profile = $user['profile'];
    $disability = $profile['disability'];

    $formErrorClass = '';
    $lockState = 'disabled';
    $errCount = 0;
    $errMsg = 'Please select your preferred way of communicating';

    if ($errors->has('disability')) {
        $errMsg = $errors->first('disability');
        $errCount++;
        $formErrorClass = 'was-validated';
        $lockState = '';
    }

    $hasErrors = $errCount > 0;
@endphp
<form action="{{ route('myprofile.update-disability') }}" class="needs-validation allow-edit {{ $formErrorClass }}"
    id="form-section-disability" autocomplete="off" method="post" novalidate>

    @csrf
    <x-editable-form-section-header caption="Please select your preferred way of communicating"
        label="Ways You Communicate" />

    <div class="mb-3">
        @foreach ($disabilities as $k => $v)
            @php
                $id = "disability-$k";
                $isSelected = $k == $disability ? 'checked' : '';
            @endphp
            <div class="form-check">
                <input class="form-check-input" value="{{ $k }}" type="radio" name="disability"
                    id="{{ $id }}" {{ "$isSelected $lockState" }}>
                <label class="form-check-label text-14 text-secondary" for="{{ $id }}">
                    {{ $v }}
                </label>
            </div>
        @endforeach
    </div>
    @if ($errors->has('disability'))
        <div class="text-danger text-12">
            {{ $errMsg }}
        </div>
    @endif
    <x-editable-form-section-control-button saveButtonClassList="btn-save-edit" :unlock="$hasErrors"
        cancelButtonClassList="btn-cancel-edit" :unlock="$hasErrors" />

</form>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function()
        {
            $(document).on('formSectionUnlocked', function(event, data)
            {
                if (data && 'scope' in data && data.scope)
                {
                    let targetForm = data.scope;

                    if (data.madeReadOnly) {
                        targetForm.trigger('reset');
                        targetForm.find('input[type="radio"]').prop('disabled', true);
                        return;
                    }

                    targetForm.find('input[type="radio"]').prop('disabled', false);
                }
            });
        });
    </script>
@endpush
