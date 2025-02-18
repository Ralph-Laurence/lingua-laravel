const currentYear = new Date().getFullYear();
const yearFromSelector = '.year-from';
const yearToSelect = '.year-to';

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
    // const defaultMessage = "Sorry, the requested action can't be processed right now. Please try again later.";
    // message = message || defaultMessage;

    let evt = new CustomEvent('showError', {
        detail: { message: message }
    });
    document.dispatchEvent(evt);
};

const previewDocumentaryProof = function(form, data)
{
    let pdfThumbnail = new PdfThumbnail({
        url: data.docProofUrl,
        previewSurface: form.find('#pdf-thumbnail')[0]
    });
    pdfThumbnail.load();
};

const redrawPdfPreviewOnUpdateForm = function(options)
{
    let updateForm = options.updateForm;

    updateForm.find('#document-filename').text(options.oldDocProofFilename);

    previewDocumentaryProof(updateForm, { docProofUrl: options.oldDocProofUrl });

    if ($(updateForm).find('.has-file-errors').length > 0)
        updateForm.find('.documentary-proof-previewer').hide();
}

/**
 * When the "Revert" button was clicked from the update-form modal,
 * we remove the file upload input, then bring back the previewer
 */
const revertFileUploadInputOnDocModal = function(form)
{
    let container = form.find('.file-upload-input-container');

    if (container.length > 0)
        container.html(''); // Clear the container

    let viewer = form.find('.documentary-proof-previewer');

    if (viewer)
        viewer.show();
};

const resetFormOnModalClosed = function(modalSelector, options)
{
    let form = $(`${modalSelector} form`);

    // Handle closing of both Add and Edit modals
    $(modalSelector).on('hide.bs.modal', function ()
    {
        $(`${modalSelector} .year-select`).val(currentYear).selectmenu('refresh');
        form.find('.is-invalid').removeClass('is-invalid');
        form.trigger('reset').removeClass('was-validated');

        revertFileUploadInputOnDocModal(form);

        if (options && 'onResetAndClosed' in options && typeof options.onResetAndClosed === 'function')
            options.onResetAndClosed();

    });
};

$(document).ready(function()
{
    let docViewerEvt = DocumentViewerDialog.events;

    $(document).on(docViewerEvt.NotFound, function()
    {
        MsgBox.showError("Sorry, we're unable to find the document. It might have already been removed.");
    })
    .on(docViewerEvt.LoadStarted,  () => showWaiting())
    .on(docViewerEvt.LoadFinished, () => waitingDialog.hide())
    .on('showWaitingDialog', () => showWaiting())
    .on('hideWaitingDialog', () => waitingDialog.hide())
    .on('showError', (event) => {
            const message = event?.detail?.message
                ?? "Sorry, the requested action can't be processed right now. Please try again later.";

            MsgBox.showError(message);
    })
    .on('click', '.btn-view-doc-proof', function()
    {
        let pdfUrl = $(this).data('url');
        DocumentViewerDialog.show(pdfUrl);
    });
});

/**
 * // const rebindFrmUpdatePdfPreview = function(frmUpdateEducation)
    // {
    //     let oldDocProofFilename = frmUpdateEducation.find('#old-docProofFilename');

    //     frmUpdateEducation.find('#document-filename').text(oldDocProofFilename.val());

    //     previewDocumentaryProof(frmUpdateEducation, {
    //         docProofUrl: frmUpdateEducation.find('#old-docProofUrl').val()
    //     });

    //     if ($(frmUpdateEducation).find('.has-file-errors').length > 0)
    //     {
    //         frmUpdateEducation.find('.documentary-proof-previewer').hide();
    //     }
    // }
 */
