@php
    $from = date('Y');
    $classList = ['class' => 'form-select px-1 text-13 year-select mx-0'];
@endphp
<select {{ $attributes->merge($classList) }} name="{{ $as }}" id="{{ $as }}">
    @for ($i = $from; $i >= 1980; $i--)
        @php
            $isSelected = $value != null && $value == $i ? 'selected' : '';
        @endphp
        <option class="text-14" {{ $isSelected }} value="{{ $i }}">
            {{ $i }}</option>
    @endfor
</select>

@once
    @push('scripts')
        <script src="{{ asset('assets/js/components/year-combo-box.js') }}"></script>
    @endpush
@endonce
