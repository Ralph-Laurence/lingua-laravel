<p class="form-label fw-bold">Your Identity *</p>

<input type="hidden" name="is_guest" value="1">

<div class="border p-2 mb-3">
    <h6 class="text-14 text-secondary mb-2">Basic Information</h6>
    <div class="row mb-2">
    <div class="col">
        <label class="text-14" for="firstname">Firstname *</label>
        <input type="text" class="form-control text-13 {{ $errors->any() && $errors->has('firstname') ? 'is-invalid' : '' }}" id="firstname" name="firstname" maxlength="32"
            value="{{ old('firstname') }}" required>
        <div class="invalid-feedback text-12">
            @if ($errors->has('firstname'))
                @error('firstname')
                    {{ $message }}
                @enderror
            @else
                Please enter your firstname
            @endif
        </div>
    </div>
    <div class="col">
        <label class="text-14" for="lastname">Lastname *</label>
        <input type="text" class="form-control text-13 {{ $errors->any() && $errors->has('lastname') ? 'is-invalid' : '' }}" id="lastname" name="lastname" maxlength="32"
            value="{{ old('lastname') }}" required>
        <div class="invalid-feedback text-12">
            @if ($errors->has('lastname'))
                @error('lastname')
                    {{ $message }}
                @enderror
            @else
                Please enter your lastname
            @endif
        </div>
    </div>
    </div>
    <div class="row mb-3">
    <div class="col">
        <label class="text-14" for="contact">Contact No. *</label>
        <input type="tel" class="form-control text-13 {{ $errors->any() && $errors->has('contact') ? 'is-invalid' : '' }}" id="contact" name="contact" maxlength="32"
            value="{{ old('contact') }}" required>
        <div class="invalid-feedback text-12">
            @if ($errors->has('contact'))
                @error('contact')
                    {{ $message }}
                @enderror
            @else
                Please add a contact number
            @endif
        </div>
    </div>
    <div class="col">
        <label class="text-14" for="address">Address *</label>
        <input type="text" class="form-control text-13 {{ $errors->any() && $errors->has('address') ? 'is-invalid' : '' }}" id="address" name="address" maxlength="150"
            value="{{ old('address') }}" required>
        <div class="invalid-feedback text-12">
            @if ($errors->has('address'))
                @error('address')
                    {{ $message }}
                @enderror
            @else
                Please enter your address
            @endif
        </div>
    </div>
    </div>
</div>

<div class="border p-2 mb-3">
    <h6 class="text-13 text-secondary mb-2">Your Account</h6>
    <div class="row mb-2">
        <div class="col">
            <label class="text-14" for="email">Email *</label>
            <input type="email" class="form-control text-13 {{ $errors->any() && $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email" maxlength="32"
                value="{{ old('email') }}" required>
            <div class="invalid-feedback text-12">
                @if ($errors->has('email'))
                    @error('email')
                        {{ $message }}
                    @enderror
                @else
                    Please enter your email
                @endif
            </div>
        </div>
        <div class="col">
            <label class="text-14" for="username">Username *</label>
            <input type="text" class="form-control text-13 {{ $errors->any() && $errors->has('username') ? 'is-invalid' : '' }}" id="username" name="username" maxlength="32"
                value="{{ old('username') }}" required>
            <div class="invalid-feedback text-12">
                @if ($errors->has('username'))
                    @error('username')
                        {{ $message }}
                    @enderror
                @else
                    Please choose enter username
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <label class="text-14" for="password">Password *</label>
            <input type="password" class="form-control text-13 {{ $errors->any() && $errors->has('password') ? 'is-invalid' : '' }}" id="password" name="password" maxlength="32" required>
            <div class="invalid-feedback text-12">
                @if ($errors->has('password'))
                    @error('password')
                        {{ $message }}
                    @enderror
                @else
                    Please enter your password
                @endif
            </div>
        </div>
        <div class="col">
            <label class="text-14" for="password_confirmation">Confirm Password *</label>
            <input type="password" class="form-control text-13" id="password_confirmation" name="password_confirmation" maxlength="64" required>
            <div class="invalid-feedback text-12">
                Please confirm your password
            </div>
        </div>
    </div>
</div>
