//##################################################
//      THESE COMPONENTS MUST BE INCLUDED:
//
//  > Messagebox (message-box.js)
//  > WaitingFor (lib/waitingfor/waiting-for.min.js)
//  > Utils (utils.js) [for sleep()]
//
//##################################################
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
            url: $('#frm-fetch-learner').find('#fetch-url').val(),
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
