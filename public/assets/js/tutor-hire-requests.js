$(document).ready(function ()
{
    $(document)
        .on('click', '.hire-reqs-list-view .btn-details-popover', function(event)
        {
            // Just for double safety... When there is an open/active popover, we must
            // force the user to close it first before we can open another popover
            if (activePopover)
                return;

            var learnerId = $(event.currentTarget).data('learner-id');
            fetchLearnerDetails(event.currentTarget, learnerId);
        })
        .on('click', '.btn-accept-request', function(event)
        {
            acceptRequest(event);
        })
        .on('click', '.btn-decline-request', function(event)
        {
            declineRequest(event);
        });

    $(document).on('learnerDetailsFetched', function(event, res)
    {
        renderLearnerDetails(res.sender, res.data);
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
