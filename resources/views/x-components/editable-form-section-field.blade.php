<div class="input-group has-validation mb-3">
    @if ($attributes->has('type') && $attributes->get('type') === 'tel')
        <span class="input-group-text text-12">
            <i class="fi fi-ph"></i> +63
        </span>
    @endif
    <input {{ $attributes->merge(['class' => $inputClasses]) }}
        id="{{ $name }}" value="{{ $value }}" name="{{ $name }}"
        @if($placeholder !== 'false')
            placeholder="{{ $placeholder }}"
        @endif

        @if ($locked)
            {{ 'readonly' }}
        @endif>

    @if (!empty($invalidFeedback))
    <div class="invalid-feedback">
        {{ $invalidFeedback }}
    </div>
    @endif

</div>

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/lib/flagicons7.2.3/css/flag-icons.min.css') }}">
    @endpush
    @push('scripts')
        <script src="{{ asset('assets/js/shared/editable-form-field.js') }}"></script>
    @endpush
@endonce

