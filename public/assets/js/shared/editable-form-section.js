$(() => {
    allowEditOnFormSectionHeader();
    //handleOriginalDataReset();
});
//
// Disable readonly attribute on all forms with class "allow-edit"
// and show the cancel button
//
function allowEditOnFormSectionHeader()
{
    $('.btn-edit-form-section').on('click', function()
    {
        let targetForm = $(this).closest('form');

        if (targetForm == null)
        {
            MsgBox.showError("This action can't be completed because of a technical error. Please try again later.");
            return;
        }

        // Unlock the readonly fields for edit
        makeFieldsReadonly(targetForm, false);

        // Hide the Edit button
        $(this).hide();

        // Show the Cancel button
        targetForm.find('.btn-cancel-edit').removeClass('d-none');
        targetForm.find('.btn-save-edit').removeClass('disabled');
    });

    $('.btn-cancel-edit').on('click', function(e)
    {
        const targetForm = $(this).closest('form');

        if (targetForm == null)
        {
            MsgBox.showError("This action can't be completed because of a technical error. Please try again later.");
            return;
        }

        // Unlock the readonly fields for edit
        makeFieldsReadonly(targetForm, true);

        // Hide on click
        $(this).addClass('d-none');

        targetForm.find('.btn-edit-form-section').show();
        targetForm.find('.btn-save-edit').addClass('disabled');

        // Hide all invalid feedbacks
        targetForm.removeClass('was-validated');

        $.each(targetForm.find('.is-invalid'), function()
        {
            $(this).removeClass('is-invalid');
        });

        e.preventDefault();

        // Reset inputs to their original values
        targetForm.find('input, textarea').each(function()
        {
            // Check if input has data-original-value attribute
            if ($(this).attr('data-original-value'))
            {
                const original = $(this).data('original-value');
                $(this).val(original);
            }

            if ($(this).prop('tagName').toLowerCase() === 'textarea')
            {
                let id = $(this).attr('id');

                if (id.includes('-original'))
                    return;

                let original = targetForm.find(`#${id}-original`);
                if (original == undefined)
                    return;

                $(this).val(original.val());
            }
        });
    });
}

function makeFieldsReadonly(targetForm, makeReadonly)
{
    // Find all input fields inside the form
    let fields = targetForm.find('input, textarea');

    // Unlock the readonly fields for edit
    $.each(fields, function()
    {
        $(this).attr('readonly', makeReadonly);
    });

    $(document).trigger('')
}

function handleOriginalDataReset()
{
    $('form').on('reset', function(e)
    {
        e.preventDefault();
        const form = $(this);

        form.find('input, textarea').each(function() {
            const input = $(this);

            // Check if input has data-original-value attribute
            if (input.attr('data-original-value')) {
                input.val(input.data('original-value'));
            }
        });
    });
}

//
    // Set the input fields original value when its
    // parent form has been reset
    //
    let bindOriginalValuesOnReset = function()
    {
        let parentForm = $(this).closest('form');

        console.log('closest form is: ' + parentForm.attr('id'));
    };
