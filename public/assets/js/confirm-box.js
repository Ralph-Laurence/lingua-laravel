const ConfirmBox = function()
{
    let show = function(message, title = 'Confirm', dialogResult = {})
    {
        $('.confirm-box .modal-title').text(title);
        $('.confirm-box .modal-body').html(message);

        // Bind the OK button click event
        $('.confirm-box .btn-ok').off('click').on('click', function()
        {
            if (typeof dialogResult.onOK === 'function')
                dialogResult.onOK();
        });

        // Bind the Cancel button click event
        $('.confirm-box .btn-cancel').off('click').on('click', function()
        {
            if (typeof dialogResult.onCancel === 'function')
                dialogResult.onCancel();
        });

        $('.confirm-box').modal('show');
    };

    return {
        show: show
    };
}();
