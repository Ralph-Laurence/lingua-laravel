{{-- BEST USED ON SECTIONS WITH SEPARATE FORM --}}

<div class="w-100 mb-3">
    <div class="d-flex align-items-center mb-2 h-20px">
        <h6 class="poppins-semibold flex-fill mb-0">{{ $label }}</h6>
        <button type="button"
            class="btn btn-link btn-sm text-decoration-none text-secondary text-12 btn-edit-form-section"
            @if ($hidden)
                style="display: none;"
            @endif>
            <i class="fas fa-pen"></i>
            <span>Edit</span>
        </button>
    </div>
    @if (!empty($caption))
        <p class="text-muted text-12 mb-0">{{ $caption }}</p>
    @endif
</div>
