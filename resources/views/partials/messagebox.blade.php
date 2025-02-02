@php
    $preRenderTitle = '';
    $preRenderContent = '';
    $preRenderModal = '';

    if (isset($preRenderState))
    {
        $preRenderTitle = $preRenderState['title'];
        $preRenderContent = $preRenderState['content'];
        $preRenderModal = 'show';
    }
@endphp
@once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/message-box.css') }}">
    @endpush
@endonce
@once
    @push('scripts')
        <script src="{{ asset('assets/js/message-box.js') }}"></script>
        @if ($preRenderModal)
            <script>
                $(() => $('.message-box').modal('show'));
            </script>
        @endif
    @endpush
@endonce

<!-- Modal -->
<div class="modal fade message-box" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center justify-content-start">
                    <span class="message-box-icon me-2">
                        <i class="fas fa-circle-info"></i>
                    </span>
                    <h6 class="modal-title">{{ $preRenderTitle }}</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-14">
                {{ $preRenderContent }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-ok text-14 px-3" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

