<form action="{{ route('profile.update-password') }}"
      class="needs-validation allow-edit {{-- $formErrorClass --}}"
      id="form-section-bio"
      autocomplete="off"
      method="post"
      novalidate>

    @csrf
    <x-editable-form-section-header label="About Me" caption="Share what makes you unique and what you're most proud of"/>

    <div id="about-me-editor">
        <div id="about-me" class="mb-1 {{ $errors->has('about') ? 'is-invalid' : '' }}"></div>
        <div class="input-group has-validation mb-3" id="about-input-group">
            <textarea class="form-control d-none" id="about" name="about" rows="4" maxlength="180" required>{{ old('about') }}</textarea>
            <div class="invalid-feedback">
                Please provide a descriptive detail about yourself.
            </div>
            <div id="about-char-counter">0/0</div>
        </div>
    </div>

</form>
