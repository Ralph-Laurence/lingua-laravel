{{-- FILE UPLOAD INPUT WITH "REVERT" BUTTON TO REMOVE IT FROM DOM. USEFUL WITH UPDATE MODE --}}
<div class="file-upload-input-update">
    <div class="form-label text-secondary text-13 mb-0">Documentary Proof</div>
    <label class="form-label text-secondary text-12">Upload Documentary Proof (PDF only):</label>
    <div class="input-group has-validation">
        <input type="file" name="file-upload" class="form-control text-13 rounded-end me-2"
            accept="application/pdf" required>
            {{-- @if (!empty($invalidFeedback))
                <div class="invalid-feedback">
                    {{ $invalidFeedback }}
                </div>
            @endif --}}
        <div class="invalid-feedback">
            Please provide a documentary proof you claim to hold. (PDF max 5MB)
        </div>
        <div class="input-group-addon">
            <x-sl-button type="button" class="btn-revert" style="danger" text="Revert" />
        </div>
    </div>
</div>
