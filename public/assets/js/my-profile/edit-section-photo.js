var croppieInstance;
var cropModal;

$(document).ready(function ()
{
    cropModal = new bootstrap.Modal(document.getElementById('cropModal'), {
        keyboard: false
    });

    $('#frm-delete-photo').on('submit', function(e)
    {
        e.preventDefault();
        let prompt = 'Your profile photo helps us and the community identify you. Are you sure you want to remove your profile photo?';

        ConfirmBox.show(prompt, 'Remove Photo',
        {
            onOK: () => {
                showWaiting();
                $('#frm-delete-photo')[0].submit();
            }
        });
    });

    $('#btn-update-photo').on('click', function()
    {
        $('#upload').click();
    });

    $('#upload').on('change', function ()
    {
        var reader = new FileReader();
        reader.onload = function(e) {
            handleImageUploaded(e);
        };
        reader.readAsDataURL(this.files[0]);
    });

    $('#crop-btn').on('click', function ()
    {
        croppieInstance.croppie('result', {
            type: 'base64', // Get the result as a base64 string
            size: 'viewport'
        })
        .then(function (response)
        {
            showWaiting();
            $('#cropped_photo').val(response);
            $('#photo-form').submit();
        });
    });

    /**
     * When binding a croppie element that isn't visible, i.e., in a modal - you'll need to
     * call bind again on your croppie element, to indicate to croppie that the position has
     * changed and it needs to recalculate its points.
     *
     * ~ https://foliotek.github.io/Croppie/
     */
    $('#cropModal').on('shown.bs.modal', function()
    {
        if (croppieInstance)
            croppieInstance.croppie('bind');
    })
    .on('hide.bs.modal', function()
    {
        // Clear the old input file value
        $('#upload').val('');

        // Destroy previous Croppie instance if any
        destoryLastInstance();
    });
});

function handleImageUploaded(e)
{
    var img = new Image();
    img.src = e.target.result;

    img.onload = function ()
    {
        var width = img.width;
        var height = img.height;

        // Check if the image meets the minimum dimensions
        if (width < 200 || height < 200)
        {
            MsgBox.showError('Image dimensions must be at least 200x200 pixels.');

            // Stop further processing
            return;
        }

        // Destroy previous Croppie instance if any
        destoryLastInstance();

        croppieInstance = $('#crop-image').croppie({
            url: e.target.result,
            viewport: {
                width: 200,
                height: 200,
                type: 'square'
            },
            boundary: {
                width: 300,
                height: 300
            },
            enforceBoundary: true,
            enableExif: true,
        });

        cropModal.show();
    };
}

function destoryLastInstance()
{
    $('#crop-image').croppie('destroy');
}
