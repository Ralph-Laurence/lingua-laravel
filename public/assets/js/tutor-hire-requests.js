$(document).ready(function ()
{
    $(document)
        .on('click', '.btn-accept-request', function(event)
        {
            acceptRequest(event);
        })
        .on('click', '.btn-decline-request', function(event)
        {
            declineRequest(event);
        });
});

function acceptRequest(event)
{
    var learnerId = $(event.currentTarget).data('learner-id');
    var form = $('#frm-accept-request');

    form.find('#learner-id').val(learnerId);
    form.submit();
}

function declineRequest(event)
{
    var learnerId = $(event.currentTarget).data('learner-id');
    var form = $('#frm-decline-request');

    form.find('#learner-id').val(learnerId);
    form.submit();
}
