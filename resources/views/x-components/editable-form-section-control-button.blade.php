@php
    $defaultClasses = [
        'saveButton'    => 'disabled',
        'cancelButton'  => 'd-none'
    ];

    if ($unlock)
    {
        $defaultClasses['saveButton']   = '';
        $defaultClasses['cancelButton'] = '';
    }
@endphp
<div class="flex-start gap-2">
    <x-sl-button type="reset" class="btn-cancel-edit {{ $defaultClasses['cancelButton'] }}" text="Cancel" style="secondary" />
    <x-sl-button type="submit" class="btn-save-edit {{ $defaultClasses['saveButton'] }}" text="Save" />
</div>
