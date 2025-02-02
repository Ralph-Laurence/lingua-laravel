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
    message = message || "Sorry, the requested action can't be processed right now. Please try again later.";
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

const resetFormOnModalClosed = function(modalSelector)
{
    // Handle closing of both Add and Edit modals
    $(modalSelector).on('hide.bs.modal', function ()
    {
        $(`${modalSelector} .year-select`).val(currentYear).selectmenu('refresh');
        $(`${modalSelector} form .is-invalid`).removeClass('is-invalid');
        $(`${modalSelector} form`).trigger('reset').removeClass('was-validated');
    });
};

$(document).ready(function()
{
    docViewerEvt = DocumentViewerDialog.events;

    $(document).on(docViewerEvt.NotFound, function()
    {
        MsgBox.showError("Sorry, we're unable to find the document. It might have already been removed.");
    })
    .on(docViewerEvt.LoadStarted,  () => showWaiting())
    .on(docViewerEvt.LoadFinished, () => waitingDialog.hide())
    .on('showWaitingDialog', () => showWaiting())
    .on('hideWaitingDialog', () => waitingDialog.hide())
    .on('showError', (event) => {
        MsgBox.showError(event.detail.message);
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
