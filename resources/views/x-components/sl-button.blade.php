<button type="{{ $type }}" id="{{ $id }}" {{ $attributes->merge(['class' => $classList]) }}>
    @if (!empty($icon))
        <i class="fas {{$icon}}"></i>
    @endif
    {{ $text }}
</button>
