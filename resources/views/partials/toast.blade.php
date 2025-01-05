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
@endphp

<div id="{{ $toastUuid }}" data-bs-autohide="{{ $autoHide }}" class="toast primary-toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
        <img src="{{ asset('assets/img/logo-s.png') }}" class="rounded me-2" width="18" height="18">
        <strong class="me-auto">
            @if (isset( $toastTitle ))
                {{ $toastTitle }}
            @endif
        </strong>
        <small class="toast-time">1 min ago</small>
        <button type="button" class="btn btn-sm toast-btn-close" data-bs-dismiss="toast" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="toast-body">
        @if (isset( $toastMessage ))
            {{ $toastMessage }}
        @endif
        @if ($hasOKButton)
            <div class="flex-end pt-3">
                <button class="btn btn-sm btn-light" data-bs-dismiss="toast">OK</button>
            </div>
        @endif
    </div>
</div>

@once
    @push('scripts')
        <script>
            $(() => {
                let toastId = "#{{ $toastUuid }}";
                new bootstrap.Toast($(toastId).get(0)).show();
            });
        </script>
    @endpush

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/toast.css') }}">
    @endpush
@endonce
