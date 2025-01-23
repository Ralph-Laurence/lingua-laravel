@php
    $classList = $inputClasses;

    if ($errors->has($name))
        $classList = "$classList is-invalid";

@endphp
<div class="{{ $rootClasses }}">
    @if ($attributes->has('type') && $attributes->get('type') === 'tel')
        <span class="input-group-text text-12">
            <i class="fi fi-ph"></i> +63
        </span>
    @endif
    <input {{ $attributes->merge(['class' => $classList]) }}
        id="{{ $name }}" value="{{ $value }}" name="{{ $name }}"
        @if($placeholder !== 'false')
            placeholder="{{ $placeholder }}"
        @endif

        @if ($locked)
            {{ 'readonly' }}
        @endif

        @if ($originalValue !== 'false' && !empty($originalValue))
            data-original-value="{{ $originalValue }}"
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

