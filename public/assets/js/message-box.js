const MsgBox = function() {
    
    let show = function(message, title = 'Alert', type = 'info')
    {
        $('.message-box .modal-title').text(title);
        $('.message-box .modal-body').text(message);

        let icon = $('.message-box .message-box-icon');
        icon.removeClass().addClass(`message-box-icon me-2 ${type}`);

        $('.message-box').modal('show');
    };

    let showInfo = function(message, title = 'Information')
    {
        show(message, title);
    };

    let showWarn = function(message, title = 'Warning')
    {
        show(message, title, 'warn');
    };

    let showError = function(message, title = 'Failure')
    {
        show(message, title, 'error');
    };

    return {
        showInfo: showInfo,
        showWarn: showWarn,
        showError: showError
    };
}();