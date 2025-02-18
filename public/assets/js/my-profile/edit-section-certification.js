class EditSectionCertification
{
    constructor()
    {
        this.upsertModal = null;
    }

    initialize()
    {
        this.upsertModal = new DocProofUpsertModal('#certificationModal', {
            baseTitle: 'Certification'
            // base title will be used later to concat as:
            // Add Educational Attainment
            // Edit Educational Attainment
        });

        this.upsertModal.initialize();
        this.bindEvents();
    }

    bindEvents()
    {
        $('#btn-add-certification').on('click', () => this.upsertModal.showCreate());

        $('.btn-remove-certification').on('click', (e) => {
            let that = $(e.currentTarget);
            let docId = that.data('doc-id');

            if (docId.trim() === '')
            {
                // Standard error message, from event dispatch
                showError();
                return;
            }

            let certification = that.closest('.certification-entry').find('.certification').text();
            let prompt = `Would you like to remove your certification from "${certification}"?`;

            ConfirmBox.show(prompt, 'Remove Certification',
            {
                onOK: () => {
                    showWaitingDialog();
                    let form = $('#frm-remove-certification');
                    form.find('#docId').val(docId);
                    form.trigger('submit');
                }
            });
        });

        $('.btn-edit-certification').on('click', async (e) =>
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
                        form.find('#certification').val(data.certification);
                        form.find('#description').val(data.description);
                        form.find('#cert-year-from').val(data.yearFrom).selectmenu('refresh');
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
    let driver = new EditSectionCertification();

    driver.initialize();

    setTimeout(() => {
        $('.btn-edit-certification').removeClass('disabled');
    }, 500);
})
