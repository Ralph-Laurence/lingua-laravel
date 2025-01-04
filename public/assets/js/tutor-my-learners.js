let activePopover = null;

$(document).ready(function ()
{
    $(document).on('click', '.row-button.btn-details', function(event)
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

    // Close popover when clicking outside
    $(document).on('click', function (e)
    {
        if (activePopover &&
            !$(e.target).closest('.popover').length &&
            !$(e.target).closest('.row-button.btn-details').length) {
            activePopover.hide();
        }
    });

    $(document).on('learnerDetailsFetched', function(event, res)
    {
        renderLearnerDetails(res.sender, res.data);
    });
});

// Helper function to pause execution
function sleep(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}
//
// this function processes the data retrieved from the server into human-readable form.
// We clone the original template then modify it.
// 
function renderLearnerDetails(sender, data)
{
    var template = $('#popover-template').clone()[0];

    $(template).find('.learner-details-photo').attr('src', data.photo);
    $(template).find('.learner-details-name').text(data.fullname);
    $(template).find('.learner-details-email').text(data.email);
    $(template).find('.learner-details-contact').text(data.contact);
    $(template).find('.learner-details-address').text(data.address);
    $(template).find('.learner-details-proficiency')
               .text(data.fluencyLevelText)
               .addClass(data.fluencyBadgeColor);
             
    handleShowPopOver(sender, template);
}
//
// this function handles how the popover should behave
//
async function handleShowPopOver(sender, content)
{
    var popoverElement = sender;

    // If there's an active popover and we're clicking a different button
    if (activePopover && activePopover._element !== popoverElement)
    {
        activePopover.hide(); // This will trigger the hidden event handler
        activePopover = null;
        await sleep(250);
    }

    // If this button already has an active popover, just return
    if (activePopover && activePopover._element === popoverElement) {
        return;
    }

    // Create new popover
    var popover = new bootstrap.Popover(popoverElement, {
        // content: function () {
        //     return $('#popover-template').clone()[0];
        // },
        content: content,
        html: true,
        trigger: 'manual', // Changed to manual to have better control
        placement: 'auto'
    });

    activePopover = popover;
    popover.show();

    function shownEventHandler()
    {
        $(document).on('click.closePopover', '.btn-close-popover', function (e)
        {
            if (activePopover) {
                activePopover.hide();
            }
        });
        popoverElement.removeEventListener('shown.bs.popover', shownEventHandler);
    }

    function hiddenEventHandler()
    {
        $(document).off('click.closePopover', '.btn-close-popover');

        if (activePopover)
        {
            activePopover.dispose();
            activePopover = null;
        }

        popoverElement.removeEventListener('hidden.bs.popover', hiddenEventHandler);
    }

    popoverElement.addEventListener('shown.bs.popover', shownEventHandler);
    popoverElement.addEventListener('hidden.bs.popover', hiddenEventHandler);
}
//
// this function retrieves the data from server via asynchronous GET request
//
async function fetchLearnerDetails(sender, learnerId)
{
    waitingDialog.show("Loading learner details...", {
        headerSize: 6,
        headerText: "Hold on, this shouldn't take long...",
        dialogSize: 'sm',
        contentClass: 'text-13'
    });

    try
    {
        const res = await $.ajax({
            url: $('#learners-list-view').data('action-learner-details'),
            method: 'get',
            data: {
                "learner_id": learnerId
            }
        });

        await sleep(1000);
        waitingDialog.hide();
        await sleep(300);

        if (res)
        {
            if (res.status == 200)
            {
                let output = {
                    'sender': sender,
                    'data': res.data
                };

                $(document).trigger('learnerDetailsFetched', output);
            }

            else
                MsgBox.showError("Aww, this shouldn't happen. Please try again.", 'Failure');
        }
    }
    catch (jqXHR)
    {
        waitingDialog.hide();
        await sleep(1000);

        // Check if responseJSON exists to get the message
        let message = 'Unknown error occurred';

        if (jqXHR.responseJSON && jqXHR.responseJSON.message)
            message = jqXHR.responseJSON.message;

        MsgBox.showError(message, 'Fatal Error');
    }
}
