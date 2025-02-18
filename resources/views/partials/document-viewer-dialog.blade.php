@once
    @push('scripts')
        <script src="{{ asset('assets/js/components/document-viewer-dialog.js') }}"></script>
    @endpush
@endonce

<div id="pdf-viewer" class="modal fade" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title poppins-semibold text-14">Documentary Proof</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdf-iframe" src="" style="width: 100%; min-height: 400px;" frameborder="0"></iframe>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-sm btn-primary px-3" data-bs-dismiss="modal">OK, Close</button> --}}
                <x-slbutton type="button" style="primary" data-bs-dismiss="modal" text="OK, Close"/>
            </div>
        </div>
    </div>
</div>
