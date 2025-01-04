//if (confirm("Do you want to hire this ASL tutor? You can end the contract anytime.

$(document).ready(function()
{
    $('.btn-hire-tutor').on('click', function()
    {
        let form      = $('#frm-hire-tutor');
        let firstname = DOMPurify.sanitize($('#tutor_name').val());
        let prompt    = `Would you like to hire <strong>${firstname}</strong> as your ASL tutor?<br><br>You can end the contract anytime.`;

        ConfirmBox.show(prompt, 'Hire Tutor',
        {
            onOK: () => form.submit()
        })
    });

    $('.btn-end-tutor').on('click', function()
    {
        let form      = $('#frm-end-contract');
        let firstname = DOMPurify.sanitize($('#tutor_name').val());
        let prompt    = `Would you like to end the tutorial contract with <strong>${firstname}</strong>?<br><br>You can hire ${firstname} again anytime.`;

        ConfirmBox.show(prompt, 'Hire Tutor',
        {
            onOK: () => form.submit()
        })
    });

    $('.btn-cancel-hire-req').on('click', function()
    {
        let form      = $('#frm-cancel-hire');
        let firstname = DOMPurify.sanitize(form.find('#tutor_name').val());
        let prompt    = `Would you like to cancel your hire request with <strong>${firstname}</strong>?`;

        ConfirmBox.show(prompt, 'Cancel Hire',
        {
            onOK: () => form.submit()
        })
    });
});
