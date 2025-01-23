const editableFormFields = (function()
{
    const tooltipAttr = 'with-tooltip';

    let addTooltip = function(targetElement, tooltipMsg)
    {
        // Add a tooltip to the field
        targetElement.attr('data-bs-toggle', 'tooltip').tooltip({
            trigger: 'focus',
            title: tooltipMsg,
            placement: 'auto'
        });
    };
    //
    // Check if a target element has a "with-tooltip" attribute
    //
    let withTooltip = function(targetElement)
    {
        if (targetElement.attr(tooltipAttr))
            return true;

        return false;
    }
    //
    // Get the tooltip message on the target element
    //
    let getTooltip = function(targetElement)
    {
        return targetElement.attr(tooltipAttr);
    };
    //
    // Find all fields with the attribute "with-tooltip", and add a tooltip
    //
    let bindTooltips = function()
    {
        $(`[${tooltipAttr}]`).each(function()
        {
            let targetElement = $(this);
            let tooltip = getTooltip(targetElement);

            // Skip elements with "false" tooltips
            if (withTooltip(targetElement) && tooltip == 'false')
                return true;

            addTooltip(targetElement, tooltip);
        });
    };
    //
    // Make all input fields not accept spaces
    //
    let initFieldNoSpaces = function()
    {
        let fieldSelector = '.no-spaces';

        $(fieldSelector).on('input', function()
        {
            let fixed = $(this).val().replace(/\s+/g, '');
            $(this).val(fixed);
        })
        .each(function()
        {
            // Default tooltip message
            let tooltipMsg = "White-spaces aren't allowed";
            let targetElement = $(this);

            // Check the element if it overrides a Tooltip
            if (withTooltip(targetElement))
            {
                // Allow a field that doesnt accept spaces to not show tooltips.
                // If set to false, we dont add it.
                if (getTooltip(targetElement) == 'false')
                    return true;

                // Set the the tooltip message
                tooltipMsg = targetElement.attr(tooltipAttr);
            }

            // Add the tooltip
            addTooltip(targetElement, tooltipMsg);
        });
    };
    //
    // Prevent the user from entering "0" trailing phone number
    //
    let initFieldsLeftTrimContactNumber = function()
    {
        const phoneNumberInput = $('input[type="tel"]');

        phoneNumberInput.on('input', function ()
        {
            let val = $(this).val();

            if (/^0/.test(val))
            {
                // Remove any leading zeroes when typing
                $(this).val(val.replace(/^0+/, ''));
            }
        });
    };
    //
    //
    //
    let init = function()
    {
        initFieldNoSpaces();
        initFieldsLeftTrimContactNumber();
        bindTooltips();
    };

    return {
        'init' : init,
    }

})();

$(document).ready(function()
{
    editableFormFields.init();
});
