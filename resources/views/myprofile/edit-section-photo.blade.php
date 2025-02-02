@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/lib/croppie/croppie.css') }}">
    <style>
        #profile-photo-wrapper {
            border: 2px solid #ccc;
            /* Optional: add a border to see the image boundaries */
        }

        #photo-preview {
            width: 128px;
            height: 128px;
            object-fit: cover;
            /* Scale the image to cover the container */
            object-position: center;
            /* Center the image horizontally and vertically */
            display: block;
            /* Make sure the image behaves like a block element */
            margin: 0 auto;
            /* Center the image horizontally if needed */
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('assets/js/my-profile/edit-section-photo.js') }}"></script>
@endpush
<h6 class="poppins-semibold">Profile Photo</h6>
<p class="text-muted text-12">Ensure your profile is always up-to-date with a recent photo.</p>
<div class="d-flex w-100 gap-4">
    <div class="rounded-half-rem overflow-hidden" id="profile-photo-wrapper">
        <img id="photo-preview" src="{{ $user['photo'] }}">
    </div>
    <div class="profile-buttons-wrapper flex-column h-100 d-flex gap-2">
        @php
            $photoExists = $user['photoExists'] !== false;
            $btnText = $photoExists ? 'Update Photo' : 'Set Photo';
        @endphp
        <x-sl-button type="button" style="primary" text="{{ $btnText }}" id="btn-update-photo"/>

        @if ($photoExists)
            <form action="{{ route('myprofile.remove-photo') }}" id="frm-delete-photo" method="POST">
                @csrf
                <x-sl-button type="submit" style="secondary" text="Remove Photo" class="btn-remove-photo" />
            </form>
        @endif
    </div>
</div>

<div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-14 poppins-semibold" id="cropModalLabel">Crop Profile Photo</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="crop-image"></div>
            </div>
            <div class="modal-footer">
                <x-sl-button type="button" style="secondary" text="Cancel" data-bs-dismiss="modal"/>
                <x-sl-button type="button" style="primary" text="Crop & Upload" id="crop-btn"/>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('myprofile.update-photo') }}" enctype="multipart/form-data" id="photo-form">
    @csrf
    @method('PUT')
    <input class="d-none" type="file" name="photo" id="upload" accept="image/*">
    <input type="hidden" name="cropped_photo" id="cropped_photo">
</form>
