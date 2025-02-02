const EditSectionEducation = (function ()
{
    const currentYear = new Date().getFullYear();
    let yearFromSelector = '.year-from';
    let yearToSelect = '.year-to';

    const showWaitingDialog = function()
    {
        // Send a trigger event to open the waiting dialog
        $(document).trigger('showWaitingDialog');
    };

    const hideWaitingDialog = function()
    {
        // Send a trigger event to close the waiting dialog
        $(document).trigger('hideWaitingDialog');
    };

    const showError = function(message)
    {
        message = message || "Sorry, the requested action can't be processed right now. Please try again later.";

        let evt = new CustomEvent('showError', {
            detail: { message: message }
        });

        document.dispatchEvent(evt);
    };

    const buildYearFromCombobox = function()
    {
        $(yearFromSelector).selectmenu({
            change: function (event, ui) {

                // Only allow the year-from input to detect changes.
                // This is because we will clamp the year-to options
                // to include the minimum year from the selected year-from.
                if (!$( this ).hasClass('year-from'))
                    return;

                let selectedYear    = parseInt(ui.item.value);
                let toYearSelect    = $(this).closest('form').find(yearToSelect);
                let yearToGenerate  = currentYear;

                if (selectedYear != currentYear)
                    yearToGenerate = selectedYear;

                let options = YearComboBox.generateYearOptions(currentYear, yearToGenerate);
                toYearSelect.html(options);
                toYearSelect.selectmenu('refresh');
            }
        });
    };

    const bindEventHandlers = function()
    {
        // Handle closing of both Add and Edit modals
        $('.educationModal').on('hide.bs.modal', function ()
        {
            $('.educationModal .year-select').val(currentYear).selectmenu('refresh');
            $('.educationModal form .is-invalid').removeClass('is-invalid');
            $('.educationModal form').trigger('reset').removeClass('was-validated');
        });

        $('#modalAddEducation #btn-save').on('click', function () {
            $('#frm-add-education .hdn-submit').trigger('click')
        });

        $('#modalEditEducation #btn-save').on('click', function () {
            $('#frm-update-education .hdn-submit').trigger('click')
        });

        $('.btn-view-doc-proof').on('click', function()
        {
            let pdfUrl = $(this).data('url');
            DocumentViewerDialog.show(pdfUrl);
        });

        $('.btn-edit-education').on('click', async function()
        {
            const docId = $(this).data('doc-id');
            await fetchEducationDetails(docId);
        });

        $('.btn-remove-education').on('click', function()
        {
            let docId = $(this).data('doc-id');
            let institution = $(this).closest('.education-entry').find('.institution').text();
            let prompt = `Would you like to remove your education details from "${institution}"?`;

            ConfirmBox.show(prompt, 'Remove Education',
            {
                onOK: () => {
                    showWaitingDialog();
                    let form = $('#frm-remove-education');
                    form.find('#docId').val(docId);
                    form.trigger('submit');
                }
            });
        });

        $('#btn-upload-new-education').on('click', function()
        {
            appendEducationUploadForm();
        });

        $(document).on('click', '#frm-update-education .btn-revert', function()
        {
            // Your click handler logic here
            let container = $(this).closest('.file-upload-input-container');
            container.html(''); // Clear the container

            let viewer = $('#frm-update-education .documentary-proof-previewer');
            viewer.show();
        });

    };

    const appendEducationUploadForm = function()
    {
        let form = '#frm-update-education';
        let viewer    = $(`${form} .documentary-proof-previewer`);
        let container = document.querySelector(`${form} .file-upload-input-container`);
        let template  = document.querySelector('#education-file-upload-input-template');

        if (container)
        {
            if (template)
            {
                let clone = template.cloneNode(true);
                $(clone).attr('id', 'education-file-upload-input');
                container.appendChild(clone);

                viewer.hide();
            }
        }
    };

    // Will be used for modal during edit
    const fetchEducationDetails = async function(docId)
    {
        showWaitingDialog();
        let form = $('#frm-update-education');

        try
        {
            const fetchUrl = new URL(form.data('action-fetch'));
            fetchUrl.searchParams.append('docId', docId);
            const res = await fetch(fetchUrl, { method: 'GET' });

            if (res.ok)
            {
                const data = await res.json();
                $('#edit-education-fetched-data').val(data);

                form.find('#doc_id').val(data.docId);
                form.find('#institution').val(data.institution);
                form.find('#degree').val(data.degree);
                form.find('#edit-year-from').val(data.yearFrom).selectmenu('refresh');
                form.find('#document-filename').text(data.docProofOrig);
                form.find('#old-docProofFilename').val(data.docProofOrig);
                form.find('#old-docProofUrl').val(data.docProofUrl);

                const toYearSelect = form.find('#edit-year-to');
                let options = YearComboBox.generateYearOptions(new Date().getFullYear(), data.yearTo);

                toYearSelect.html(options).selectmenu();
                toYearSelect.val(data.yearTo).selectmenu('refresh');

                previewDocumentaryProof(form, data);

                let fileErrors = form.find('.has-file-errors');
                if (fileErrors.length > 0)
                {
                    fileErrors.remove();
                    form.find('.documentary-proof-previewer').show();
                }

                await sleep(500);
                hideWaitingDialog();

                await sleep(400);

                let modal = new bootstrap.Modal(document.getElementById('modalEditEducation'));
                modal.show();
            }
            else
            {
                hideWaitingDialog();

                // Handle different HTTP status codes
                let errorMsg = "Sorry, we're unable to read the data from the records. Please try again later.";

                if (res.status === 500)
                    errorMsg = "Sorry, a technical error has occurred while retrieving the record. Please try again later.";

                showError(errorMsg);
            }
        }
        catch (error)
        {
            hideWaitingDialog();
            // Show default (common) error message
            showError();
        }
    };

    const previewDocumentaryProof = function(form, data)
    {
        let pdfThumbnail = new PdfThumbnail({
            url: data.docProofUrl,
            previewSurface: form.find('#pdf-thumbnail')[0]
        });
        pdfThumbnail.load();
    };

    const initialize = function()
    {
        buildYearFromCombobox();
        bindEventHandlers();

        // Useful when edit education failed
        let frmUpdateEducation = $('#frm-update-education');

        if (frmUpdateEducation.hasClass('has-errors'))
            rebindFrmUpdatePdfPreview(frmUpdateEducation);
    };

    const rebindFrmUpdatePdfPreview = function(frmUpdateEducation)
    {
        let oldDocProofFilename = frmUpdateEducation.find('#old-docProofFilename');

        frmUpdateEducation.find('#document-filename').text(oldDocProofFilename.val());

        previewDocumentaryProof(frmUpdateEducation, {
            docProofUrl: frmUpdateEducation.find('#old-docProofUrl').val()
        });

        if ($(frmUpdateEducation).find('.has-file-errors').length > 0)
        {
            frmUpdateEducation.find('.documentary-proof-previewer').hide();
        }
    }

    return {
        'init' : initialize
    }
})();

$(document).ready(function()
{
    EditSectionEducation.init();
});
