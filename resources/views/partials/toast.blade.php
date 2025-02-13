@php
    $uuid = uniqid();
    $toastUuid = 'toast-'. $uuid;
    $hasOKButton = false;
    $autoHide = 'false';

    if (isset($useOKButton) && $useOKButton == 'true')
    {
        $hasOKButton = true;
    }

    if (isset($autoClose) && $autoClose == 'true')
    {
        $autoHide = 'true';
    }

    if (isset($as))
    {
        $toastUuid = $as;
    }
@endphp

<div id="{{ $toastUuid }}" data-bs-autohide="{{ $autoHide }}" class="toast primary-toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
        <img src="{{ asset('assets/img/logo-s.png') }}" class="rounded me-2" width="18" height="18">
        <strong class="me-auto">
            <div class="toast-title w-100">
                @if (isset( $toastTitle ))
                    {{ $toastTitle }}
                @endif
            </div>
        </strong>
        <small class="toast-time">1 min ago</small>
        <button type="button" class="btn btn-sm toast-btn-close" data-bs-dismiss="toast" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="toast-body">
        <div class="toast-message w-100">
            @if (isset( $toastMessage ))
                {{ $toastMessage }}
            @endif
        </div>
        @if ($hasOKButton)
            <div class="flex-end pt-3">
                <button class="btn btn-sm btn-light" data-bs-dismiss="toast">OK</button>
            </div>
        @endif
    </div>
</div>

@once
    @push('scripts')
        @if (!isset($as))
            {{--
                When there is no passed "as", we let the backend generate a script that triggers the toast.
                Passing the "as" explicitly allows us to give a different id, however this results to manual
                triggering and instantiation of the toast.
            --}}
            <script>
                $(() => {
                    let toastId = "#{{ $toastUuid }}";
                    new bootstrap.Toast($(toastId).get(0)).show();
                });
            </script>
        @endif

        {{-- We'll always include the default toast driver script --}}
        <script src="{{ asset('assets/js/components/toast-frontend.js') }}"></script>
    @endpush

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/toast.css') }}">
    @endpush
@endonce
