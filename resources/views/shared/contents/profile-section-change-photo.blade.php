@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/lib/bootstrap5/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/lib/croppie/croppie.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/lib/jquery3.7.1/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/lib/bootstrap5/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/lib/croppie/croppie.min.js') }}"></script>
    <script>
$(document).ready(function() {
    var croppieInstance;

    $('#upload').on('change', function() {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#cropModal').modal('show');
            $('#crop-image').croppie('destroy'); // Destroy previous Croppie instance if any

            croppieInstance = $('#crop-image').croppie({
                url: e.target.result,
                viewport: { width: 200, height: 200, type: 'square' },
                boundary: { width: 300, height: 300 },
                enableExif: true
            });
        };
        reader.readAsDataURL(this.files[0]);
    });

    $('#crop-btn').on('click', function() {
        croppieInstance.croppie('result', {
            type: 'base64', // Get the result as a base64 string
            size: 'viewport'
        }).then(function(response) {
            $('#cropped_photo').val(response);
            $('#photo-form').submit();
        });
    });
});


    </script>
@endpush
<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Change Profile Photo') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropModalLabel">Crop Profile Photo</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="crop-image"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="crop-btn">Crop & Upload</button>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.update.photo') }}" enctype="multipart/form-data" id="photo-form">
        @csrf
        @method('PUT')
        <input type="file" name="photo" id="upload" accept="image/*">
        <input type="hidden" name="cropped_photo" id="cropped_photo">
        <button type="submit">Update Photo</button>
    </form>


    {{-- <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal> --}}
</section>
