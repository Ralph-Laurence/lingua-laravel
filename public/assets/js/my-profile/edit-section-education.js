class EditSectionEducation
{
    constructor()
    {
        this.upsertModal = null;
    }

    initialize()
    {
        this.upsertModal = new DocProofUpsertModal('#educationModal', {
            baseTitle: 'Educational Attainment'
            // base title will be used later to concat as:
            // Add Educational Attainment
            // Edit Educational Attainment
        });

        this.upsertModal.initialize();
        this.bindEvents();
    }

    bindEvents()
    {
        $('#btn-add-education').on('click', () => this.upsertModal.showCreate());

        $('.btn-remove-education').on('click', (e) => {
            let that = $(e.target);
            let docId = that.data('doc-id');

            if (docId.trim() === '')
            {
                // Standard error message, from event dispatch
                showError();
                return;
            }

            let institution = that.closest('.education-entry').find('.institution').text();
            let prompt = `Would you like to remove your educational attainment from "${institution}"?`;

            ConfirmBox.show(prompt, 'Remove Educational Attainment',
            {
                onOK: () => {
                    showWaitingDialog();
                    let form = $('#frm-remove-education');
                    form.find('#docId').val(docId);
                    form.trigger('submit');
                }
            });
        });

        $('.btn-edit-education').on('click', async (e) =>
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
                        form.find('#institution').val(data.institution);
                        form.find('#degree').val(data.degree);
                        form.find('#educ-year-from').val(data.yearFrom).selectmenu('refresh');
                        form.find('#educ-year-to').val(data.yearTo).selectmenu('refresh');
                        form.find('#document-filename').text(data.docProofOrig);
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
}

document.addEventListener('DOMContentLoaded', function()
{
    // The main driver
    let driver = new EditSectionEducation();

    driver.initialize();

    setTimeout(() => {
        $('.btn-edit-education').removeClass('disabled');
    }, 500);
})
