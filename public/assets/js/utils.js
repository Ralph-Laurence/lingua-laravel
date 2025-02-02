// Helper function to pause execution
function sleep(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

function initFluencyTooltips()
{
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('.fluency-tooltip'));

    tooltipTriggerList.map(function (tooltipTriggerEl)
    {
        $(tooltipTriggerEl).css('cursor', 'pointer').addClass('user-select-none');

        return new bootstrap.Tooltip(tooltipTriggerEl, {
            'placement': 'auto'
        });
    })
}

function showWaiting()
{
    waitingDialog.show("Processing...", {
        headerSize: 6,
        headerText: "Hold on, this shouldn't take long...",
        dialogSize: 'sm',
        contentClass: 'text-13'
    });
}
