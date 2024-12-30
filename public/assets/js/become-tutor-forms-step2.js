let educationEntryCount     = 0;    // only the premade education entry should start with 0
let workEntryCount          = -1;   // dynamic entries must start at "-0"
let certificationEntryCount = -1;

$(document).ready(function()
{
    buildYearRangeSelect('#education-year-from-0', '#education-year-to-0');

    $('#add-education').click(function()
    {
        let educationEntry = new EntryItem({

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
        let certEntry = new EntryItem({

            fieldPrefix:    'certification',
            container:      '#cert-entries',
            field1:         'Title',
            field2:         'Description'
        });

        certEntry.add();
    });

    let myModalEl = document.getElementById('skillsPickerModal')
    let checkedValues = [];

    if ($('#skills-arr').val().trim() !== '')
    {
        checkedValues = JSON.parse($('#skills-arr').val());
        listDownSkills();
    }

    myModalEl.addEventListener('hidden.bs.modal', function (event)
    {
        $('#skill-entries').empty();
        checkedValues = [];

        listDownSkills();
    });

    myModalEl.addEventListener('show.bs.modal', function(event)
    {
        checkedValues.forEach(function(item) {
            $('#skill_' + item.value).prop('checked', true);
        });
    });

    $('#skill-entries').on('click', '.btn-remove-skill', function()
    {
        let targetItem = $(this).closest('.skill-item');
        let skillValue = targetItem.data('skill-value');

        checkedValues  = checkedValues.filter(item => item.value != skillValue);
        targetItem.remove();
    });

    function listDownSkills()
    {
        $('.skill-checkbox:checked').each(function ()
        {
            checkedValues.push({
                value: $(this).val(),
                label: $(this).next('label').text().trim()
            });
        });

        $('.skill-checkbox').prop('checked', false);


        let html = '';

        for (let i = 0; i < checkedValues.length; i++)
        {
            html += `<div data-skill-value="${checkedValues[i].value}" class="badge bg-secondary skill-item ps-3 pe-2 py-2 d-flex align-items-center justify-content-between">
                        <span class="me-2">${checkedValues[i].label}</span>
                        <button type="button" class="btn-remove-skill">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>`
        }

        $('#skill-entries').html(html);
        $('#skills-arr').val( JSON.stringify(checkedValues) );
    }
});

const generateYearOptions = function(startYear, endYear) {
    let options = '';
    for (let year = startYear; year >= endYear; year--) {
        options += `<option value="${year}">${year}</option>`;
    }
    return options;
};

const buildYearRangeSelect = function(elemFrom, elemTo, options = {})
{
    $(elemTo).selectmenu({width: 100});

    $(elemFrom).selectmenu({
        width: 100,
        change: function(event, ui)
        {
            let currentYear  = new Date().getFullYear();
            let selectedYear = parseInt(ui.item.value);
            let toYearSelect = $(elemTo);

            if (selectedYear == currentYear)
            {
                toYearSelect.html(generateYearOptions(currentYear, currentYear));
            }
            else
            {
                toYearSelect.html(generateYearOptions(currentYear, selectedYear));
            }

            toYearSelect.selectmenu('refresh');
        }
    });
}

const toSelectMenu = function(selectors)
{
    $(selectors).selectmenu({ width: 100 });
};

const EntryItem = function(options)
{
    let entryCount = 0;

    // Determine the entry count based on the category
    if (options.fieldPrefix === 'education') {
        entryCount = ++educationEntryCount;
    } else if (options.fieldPrefix === 'work') {
        entryCount = ++workEntryCount;
    } else if (options.fieldPrefix === 'certification') {
        entryCount = ++certificationEntryCount;
    }

    const addEntry = function()
    {
        //entryCount++;
        const field1Name     = `${options.fieldPrefix}-${options.field1.toLowerCase()}-${entryCount}`;
        const field2Name     = `${options.fieldPrefix}-${options.field2.toLowerCase()}-${entryCount}`;
        const fileUploadName = `${options.fieldPrefix}-file-upload-${entryCount}`;

        let currentYear = new Date().getFullYear();
        let pickerFromRangeTemplate = generateYearOptions(currentYear, 1980);

        let yearEntry = `
            <div class="col">
                <label for="${options.fieldPrefix}-year-from-${entryCount}" class="text-14 text-secondary">From Year</label>
                <select data-field-prefix="${options.fieldPrefix}-year-from-" id="${options.fieldPrefix}-year-from-${entryCount}" name="${options.fieldPrefix}-year-from-${entryCount}">
                    ${pickerFromRangeTemplate}
                </select>
            </div>
            <div class="col"></div>
        `;

        if (options.yearEntryMode === 'range')
        {
            yearEntry = `
                <div class="col">
                    <label for="${options.fieldPrefix}-year-from-${entryCount}" class="text-14 text-secondary">From Year</label>
                    <select data-field-prefix="${options.fieldPrefix}-year-from-" id="${options.fieldPrefix}-year-from-${entryCount}" name="${options.fieldPrefix}-year-from-${entryCount}">
                        ${pickerFromRangeTemplate}
                    </select>
                </div>
                <div class="col">
                    <label for="${options.fieldPrefix}-year-to-${entryCount}" class="text-14 text-secondary">To Year</label>
                    <select data-field-prefix="${options.fieldPrefix}-year-to-" id="${options.fieldPrefix}-year-to-${entryCount}" name="${options.fieldPrefix}-year-to-${entryCount}">
                        ${generateYearOptions(currentYear, currentYear)}
                    </select>
                </div>
            `;
        }

        let entrySuffix = options.container.replace('#', '');
        let entryId = `${entrySuffix}-${entryCount}`;

        const entryHtml = `
            <div id="${entryId}" data-entry-suffix="${entrySuffix}-" class="entry mt-3 p-3 border border-1 rounded rounded-3">
                <div class="row mb-2">
                    ${yearEntry}
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="input-group has-validation">
                            <input data-field-prefix="${options.fieldPrefix}-${options.field1.toLowerCase()}-" type="text" id="${field1Name}" name="${field1Name}" class="form-control" placeholder="${options.field1}" required>
                            <div class="invalid-feedback">
                                Please provide a valid ${options.field1.toLowerCase()}.
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group has-validation">
                            <input data-field-prefix="${options.fieldPrefix}-${options.field2.toLowerCase()}-" type="text" id="${field2Name}" name="${field2Name}" class="form-control" placeholder="${options.field2}" required>
                            <div class="invalid-feedback">
                                Please provide a valid ${options.field2.toLowerCase()}.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="file-upload mb-3">
                        <label for="${fileUploadName}" class="form-label text-secondary text-14">Upload Documentary Proof (PDF only):</label>
                        <div class="input-group has-validation">
                            <input type="file" id="${fileUploadName}" name="${fileUploadName}" class="form-control text-14" accept="application/pdf" required>
                            <div class="invalid-feedback">
                                Please provide a documentary proof you claim to hold. (PDF max 5MB)
                            </div>
                        </div>
                    </div>
                <button type="button" class="btn btn-sm btn-danger remove-btn entry-buttons btn-remove-entry">
                    <i class="fas fa-times me-1"></i>Remove
                </button>
            </div>
        `;

        $(options.container).append(entryHtml);

        let elFrom = `#${options.fieldPrefix}-year-from-${entryCount}`;
        let elTo   = `#${options.fieldPrefix}-year-to-${entryCount}`;

        buildYearRangeSelect(elFrom, elTo, options);

        if ('repopulateFromYear' in options)
        {
            $(elFrom).val(options['repopulateFromYear']).selectmenu('refresh');
        }

        if ('repopulateToYear' in options)
        {
            $(elTo).html(generateYearOptions(currentYear, options['repopulateToYear']))
                   .val(options['repopulateToYear'])
                   .selectmenu('refresh');
        }
    }

    $(() => {
        $(options.container).on('click', '.remove-btn', function()
        {
            $(this).closest('.entry').remove();
            //entryCount--;

            // Determine the entry count based on the category
            if (options.fieldPrefix === 'education' && educationEntryCount > 0)
            {
                entryCount = --educationEntryCount;
                reIndexEntries('#education-entries', true);
            }
            else if (options.fieldPrefix === 'work' && workEntryCount > -1)
            {
                entryCount = --workEntryCount;
                reIndexEntries('#work-entries');
            }
            else if (options.fieldPrefix === 'certification' && certificationEntryCount > -1)
            {
                entryCount = --certificationEntryCount;
                reIndexEntries('#cert-entries');
            }
        });
    });

    return {
        add: addEntry,
        toSelectMenu
    };
};

function reIndexEntries(container, options)
{
    let excludeFirst = options?.excludeFirst || false;

    $(`${container} .entry`).each(function(index, element)
    {
        if (excludeFirst && index == 0)
            return true; // continue;

        // The 'entry' element
        let entry = $(element);

        // Set the entry's id with incremental suffix
        let entrySuffix = entry.data('entry-suffix');

        entry.attr('id', `${entrySuffix}${index}`);

        // For every fields that belong to the entry, rename their ID
        entry.find('[data-field-prefix]').each(function(i, e)
        {
            let field = $(e);

            let fieldPrefix = field.data('field-prefix');
            let newNameId   = `${fieldPrefix}${index}`;

            field.attr('id',   newNameId);
            field.attr('name', newNameId);

            console.log(i + ' : ' + newNameId);
        });
    });
}
