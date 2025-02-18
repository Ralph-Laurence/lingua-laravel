<!-- Modal -->
<div class="modal fade confirm-box" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center justify-content-start">
                    <span class="message-box-icon me-2">
                        <i class="fas fa-circle-question text-primary"></i>
                    </span>
                    <h6 class="modal-title"></h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-14">

            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary btn-cancel text-14 px-3" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary btn-ok text-14 px-3" data-bs-dismiss="modal">Yes</button> --}}
                <x-slbutton type="button" style="secondary" class="btn-cancel px-3" text="No" data-bs-dismiss="modal" />
                <x-slbutton type="button" style="primary" class="btn-ok px-3" text="Yes" data-bs-dismiss="modal" />
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/confirm-box.js') }}"></script>
@endpush
