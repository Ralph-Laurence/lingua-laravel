@if ($elementTag == 'a')
    <a href="{{ $action }}"
@else
    <button
@endif

    type="{{ $type }}" id="{{ $id }}" {{ $attributes->merge(['class' => $classList]) }}>
    @if (!empty($icon))
        <i class="fas {{$icon}}"></i>
    @endif
    {{ $text }}
@if ($elementTag == 'a')
    </a>
@else
    </button>
@endif
