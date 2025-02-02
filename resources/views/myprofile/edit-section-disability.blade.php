<x-editable-form-section-header label="Ways You Communicate" caption="Please select your preferred way of communicating" /> {{-- :hidden="$hasErrors"/> --}}

@foreach ($disabilities as $k => $v)
    @php
        $id = 'disability-'.$k;
    @endphp
    <div class="form-check">
        <input class="form-check-input" type="radio" name="disability" id="{{ $id }}">
        <label class="form-check-label text-14 text-secondary" for="{{ $id }}">
            {{ $v }}
        </label>
    </div>
@endforeach

@push('styles')
    <style>
        .form-check-input:checked {
            background-color: #FE9424;
            border-color: #FE9424;
        }
        .form-check-input:focus {
            border-color: #fee886;
            outline: 0;
            box-shadow: 0 0 0 .25rem rgba(253, 169, 13, 0.25);
        }
    </style>
@endpush
