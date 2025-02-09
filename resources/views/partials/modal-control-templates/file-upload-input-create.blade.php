<div class="file-upload-input-create">
    <div class="form-label text-secondary text-13 mb-2">Documentary Proof</div>
    <div class="file-upload">
        <label class="form-label text-secondary text-13">Upload
            Documentary Proof (PDF only):</label>
        <div class="input-group has-validation">
            <input type="file" name="file-upload" class="form-control text-13" accept="application/pdf" required>
            <div class="invalid-feedback">
                Please provide a documentary proof you claim to hold. (PDF max 5MB)
            </div>
            {{-- @if (!empty($invalidFeedback))
                <div class="invalid-feedback">
                    {{ $invalidFeedback }}
                </div>
            @endif --}}
        </div>
    </div>
</div>
