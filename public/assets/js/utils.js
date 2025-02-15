// Helper function to pause execution
function sleep(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

function initFluencyTooltips()
{
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('.disability-tooltip'));

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

function areObjectsEqual(object1, object2)
{
    // Get keys from both objects
    const keys1 = Object.keys(object1);
    const keys2 = Object.keys(object2);

    // If they have different number of properties, not equal
    if (keys1.length !== keys2.length) {
        return false;
    }

    // Check each property in object1 matches object2
    for (const key of keys1)
    {
        if (!object2.hasOwnProperty(key) || object1[key] !== object2[key])
        {
            return false;
        }
    }

    return true;
}

function decodeHtmlEntities(str)
{
    const parser = new DOMParser();
    const decodedString = parser.parseFromString(`<!doctype html><body>${str}`, 'text/html').body.textContent;
    return decodedString;
}
