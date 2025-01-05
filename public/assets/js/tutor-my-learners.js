$(document).ready(function ()
{
    $(document).on('click', '.row-button.btn-details-popover', function(event)
    {
        // Just for double safety...
        // When there is an open/active popover, we must
        // force the user to close it first before we
        // can open another popover
        if (activePopover)
            return;

        var learnerId = $(event.currentTarget).data('learner-id');
        fetchLearnerDetails(event.currentTarget, learnerId);
    });

    $(document).on('learnerDetailsFetched', function(event, res)
    {
        renderLearnerDetails(res.sender, res.data);
    });
});
