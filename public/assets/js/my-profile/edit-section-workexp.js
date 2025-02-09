class EditSectionWorkExp
{
    constructor()
    {
        this.upsertModal = null;
    }

    initialize()
    {
        this.upsertModal = new DocProofUpsertModal('#workExpModal', {
            baseTitle: 'Work Experience'
        });

        this.upsertModal.initialize();
        this.bindEvents();
    }

    bindEvents()
    {
        $('#btn-add-workexp').on('click', () => this.upsertModal.showCreate());

        $('.btn-remove-workexp').on('click', (e) => {
            let that = $(e.target);
            let docId = that.data('doc-id');

            if (docId.trim() === '')
            {
                // Standard error message, from event dispatch
                showError();
                return;
            }

            let company = that.closest('.workexp-entry').find('.company').text();
            let prompt = `Would you like to remove your work experience details from "${company}"?`;

            ConfirmBox.show(prompt, 'Remove Work Experience',
            {
                onOK: () => {
                    showWaitingDialog();
                    let form = $('#frm-remove-workexp');
                    form.find('#docId').val(docId);
                    form.trigger('submit');
                }
            });
        });

        $('.btn-edit-workexp').on('click', async (e) =>
        {
            const docId = e.currentTarget.getAttribute('data-doc-id');

            if (!docId)
            {
                console.error('No docId found on clicked element');
                return;
            }

            try
            {
                const data = await this.upsertModal.fetchEditDetails(docId);
                console.log('Fetch completed, data:', data);

                let options = {
                    documentaryProof: {
                        url: data.docProofUrl,
                        fileName: data.docProofOrig,
                        docId: data.docId
                    },
                    onBindInputs: function(ev)
                    {
                        let form = $(ev.mainForm);

                        form.find('#doc_id').val(data.docId);
                        form.find('#company').val(data.company);
                        form.find('#role').val(data.role);
                        form.find('#work-year-from').val(data.yearFrom).selectmenu('refresh');
                        form.find('#work-year-to').val(data.yearTo).selectmenu('refresh');
                        form.find('#document-filename').text(data.docProofOrig);
                        // form.find('#old-docProofFilename').val(data.docProofOrig);
                        // form.find('#old-docProofUrl').val(data.docProofUrl);
                    }
                };

                // useful during redirect-back, when validation fails
                sessionStorage.setItem('lastLoadedDocProofUrl', data.docProofUrl);
                sessionStorage.setItem('lastLoadedDocProofFilename', data.docProofOrig);

                this.upsertModal.showUpdate(options);
            }
            catch (error)
            {
                console.error(error);
            }
            // await this.upsertModal.showUpdate(docId);
        });
    }

    // // Will be used for modal during edit
    // async fetchWorkExpDetails(docId)
    // {
    //     showWaitingDialog();
    //     let form = $('#frm-update-education');

    //     try
    //     {
    //         const fetchUrl = new URL(form.data('action-fetch'));
    //         fetchUrl.searchParams.append('docId', docId);
    //         const res = await fetch(fetchUrl, { method: 'GET' });

    //         if (res.ok)
    //         {
    //             const data = await res.json();
    //             $('#edit-education-fetched-data').val(data);

    //             form.find('#doc_id').val(data.docId);
    //             form.find('#institution').val(data.institution);
    //             form.find('#degree').val(data.degree);
    //             form.find('#edit-year-from').val(data.yearFrom).selectmenu('refresh');
    //             form.find('#document-filename').text(data.docProofOrig);
    //             form.find('#old-docProofFilename').val(data.docProofOrig);
    //             form.find('#old-docProofUrl').val(data.docProofUrl);

    //             const toYearSelect = form.find('#edit-year-to');
    //             let options = YearComboBox.generateYearOptions(new Date().getFullYear(), data.yearTo);

    //             toYearSelect.html(options).selectmenu();
    //             toYearSelect.val(data.yearTo).selectmenu('refresh');

    //             previewDocumentaryProof(form, data);

    //             let fileErrors = form.find('.has-file-errors');
    //             if (fileErrors.length > 0)
    //             {
    //                 fileErrors.remove();
    //                 form.find('.documentary-proof-previewer').show();
    //             }

    //             await sleep(500);
    //             hideWaitingDialog();

    //             await sleep(400);

    //             let modal = new bootstrap.Modal(document.getElementById('modalEditEducation'));
    //             modal.show();
    //         }
    //         else
    //         {
    //             hideWaitingDialog();

    //             // Handle different HTTP status codes
    //             let errorMsg = "Sorry, we're unable to read the data from the records. Please try again later.";

    //             if (res.status === 500)
    //                 errorMsg = "Sorry, a technical error has occurred while retrieving the record. Please try again later.";

    //             showError(errorMsg);
    //         }
    //     }
    //     catch (error)
    //     {
    //         hideWaitingDialog();
    //         // Show default (common) error message
    //         showError();
    //     }
    // };
}

document.addEventListener('DOMContentLoaded', function()
{
    // The main driver
    let driver = new EditSectionWorkExp();

    driver.initialize();

    $('.btn-edit-workexp').removeClass('disabled');
})
