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
