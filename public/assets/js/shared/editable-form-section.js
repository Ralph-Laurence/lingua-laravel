$(() => allowEditOnFormSectionHeader());
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

    $('.btn-cancel-edit').on('click', function()
    {
        let targetForm = $(this).closest('form');

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
    });
}

function makeFieldsReadonly(targetForm, makeReadonly)
{
    // Find all input fields inside the form
    let fields = targetForm.find('input');

    // Unlock the readonly fields for edit
    $.each(fields, function()
    {
        $(this).attr('readonly', makeReadonly);
    });
}
