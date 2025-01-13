// Helper function to pause execution
function sleep(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

function initFieldNoSpaces(fieldSelector, tooltip)
{
    fieldSelector = fieldSelector || '.input-no-spaces';

    if (tooltip)
    {
        $(fieldSelector).attr('data-bs-toggle', 'tooltip').tooltip({
            trigger: 'focus',
            title: tooltip,
            placement: 'auto'
        });
    }

    $(fieldSelector).on('input', function()
    {
        this.value = this.value.replace(/\s+/g, '');
    });
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
