$(document).ready(function()
{
    // Initialize the premade entries at education category
    toSelectMenu(`#education-year-from, #education-year-to`);

    $('#add-education').click(function()
    {
        let educationEntry = new EntryItem({

            //entryOffset: 1, // begin at 1 because of the premade entry

            fieldPrefix:    'education',
            container:      '#education-entries',
            yearEntryMode:  'range',
            field1:         'Institution',
            field2:         'Degree'
        });

        educationEntry.add();
    });

    $('#add-work').click(function()
    {
        let workEntry = new EntryItem({

            fieldPrefix:    'work',
            container:      '#work-entries',
            yearEntryMode:  'range',
            field1:         'Company',
            field2:         'Role'
        });

        workEntry.add();
    });

    $('#add-cert').click(function()
    {
        let workEntry = new EntryItem({

            fieldPrefix:    'certification',
            container:      '#cert-entries',
            field1:         'Title',
            field2:         'Description'
        });

        workEntry.add();
    });
});

const toSelectMenu = function(selectors)
{
    $(selectors).selectmenu({ width: 100 });
};

const EntryItem = function(options)
{
    let entryCount = 0; // options.entryOffset ||

    function generateYearOptions()
    {
        const currentYear = new Date().getFullYear();
        let options = '';

        for (let year = currentYear; year >= 1980; year--)
        {
            options += `<option value="${year}">${year}</option>`;
        }
        return options;
    }

    const addEntry = function()
    {
        entryCount++;

        const field1Name = `${options.fieldPrefix}-${options.field1.toLowerCase()}-${entryCount}`;
        const field2Name = `${options.fieldPrefix}-${options.field2.toLowerCase()}-${entryCount}`;
        const fileUploadName = `${options.fieldPrefix}-file-upload-${entryCount}`;

        let yearEntry = `
            <div class="col">
                <label for="${options.fieldPrefix}-year-from-${entryCount}" class="text-14 text-secondary">From Year</label>
                <select id="${options.fieldPrefix}-year-from-${entryCount}" name="${options.fieldPrefix}-year-from-${entryCount}">
                    ${generateYearOptions()}
                </select>
            </div>
            <div class="col"></div>
        `;

        if (options.yearEntryMode === 'range')
        {
            yearEntry = `
                <div class="col">
                    <label for="${options.fieldPrefix}-year-from-${entryCount}" class="text-14 text-secondary">From Year</label>
                    <select id="${options.fieldPrefix}-year-from-${entryCount}" name="${options.fieldPrefix}-year-from-${entryCount}">
                        ${generateYearOptions()}
                    </select>
                </div>
                <div class="col">
                    <label for="${options.fieldPrefix}-year-to-${entryCount}" class="text-14 text-secondary">To Year</label>
                    <select id="${options.fieldPrefix}-year-to-${entryCount}" name="${options.fieldPrefix}-year-to-${entryCount}">
                        ${generateYearOptions()}
                    </select>
                </div>
            `;
        }

        let entryId = `${options.container.replace('#', '')}-${entryCount}`;

        const entryHtml = `
            <div id="${entryId}" class="entry mt-3 p-3 border border-1 rounded rounded-3">
                <div class="row mb-2">
                    ${yearEntry}
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" id="${field1Name}" name="${field1Name}" class="form-control" placeholder="${options.field1}" required>
                    </div>
                    <div class="col">
                        <input type="text" id="${field2Name}" name="${field2Name}" class="form-control" placeholder="${options.field2}" required>
                    </div>
                </div>
                <div class="file-upload mb-3">
                        <label for="${fileUploadName}" class="form-label text-secondary text-14">Upload Documentary Proof (PDF only):</label>
                        <input type="file" id="${fileUploadName}" name="${fileUploadName}" class="form-control text-14" accept="application/pdf" required>
                    </div>
                <button type="button" class="btn btn-sm btn-danger remove-btn entry-buttons btn-remove-entry">
                    <i class="fas fa-times me-1"></i>Remove
                </button>
            </div>
        `;

        $(options.container).append(entryHtml);

        toSelectMenu(`#${options.fieldPrefix}-year-from-${entryCount}, #${options.fieldPrefix}-year-to-${entryCount}`);
    }

    $(document).ready(function()
    {
        $(options.container).on('click', '.remove-btn', function()
        {
            $(this).closest('.entry').remove();
            entryCount--;
        });
    });

    return {
        add: addEntry,
        toSelectMenu
    };
};
