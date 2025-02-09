class DocProofUpsertModal
{
    constructor(selector, options)
    {
        this.selector       = selector;
        this.options        = options;
        this.rootElement    = null;
        this.modalInstance  = null;
    }
    /**
     * Must wait for the DOM to be fully loaded before initializations
     */
    initialize()
    {
        this.rootElement                = document.querySelector(this.selector);
        this._modalTitleElement         = this.rootElement.querySelector('.modal-title');
        this._templatesRoot             = this.rootElement.querySelector('.control-templates');
        this._docPreviewerContainer     = this.rootElement.querySelector('.documentary-proof-previewer');
        this._fileInputContainer        = this.rootElement.querySelector('.file-upload-input-container');
        this._btnSubmit                 = this.rootElement.querySelector('.btn-save');
        this._hiddenSubmit              = this.rootElement.querySelector('.hdn-submit');
        this._mainForm                  = this.rootElement.querySelector('.main-form');
        this._routeActionFetch          = this._mainForm.dataset.actionFetch;
        this._routeActionCreate         = this._mainForm.dataset.actionCreate;
        this._routeActionUpdate         = this._mainForm.dataset.actionUpdate;

        this._fileInputTemplateCreate   = this._templatesRoot.querySelector('.file-upload-input-create');
        this._fileInputTemplateUpdate   = this._templatesRoot.querySelector('.file-upload-input-update');
        this._docProofPreviewThumbnail  = this._templatesRoot.querySelector('.docproof-preview-thumbnail');

        this.baseTitle                  = this.options.baseTitle;
        this.actionPrefixTitleCreate    = this.options.actionPrefixTitleCreate || 'Add';
        this.actionPrefixTitleUpdate    = this.options.actionPrefixTitleUpdate || 'Edit';

        this.modalInstance = new bootstrap.Modal(this.rootElement);
        this.bindEvents();

        // check for errors then display
        let errors = this.checkErrorBag();

        if (errors !== null)
        {
            this.displayErrorBagMessages(errors);
        }
    }

    bindEvents()
    {
        this.rootElement.addEventListener('hidden.bs.modal', () =>
        {
            this._mainForm.reset();
            this._mainForm.classList.remove('was-validated');
            this._fileInputContainer.innerHTML = "";
            this._docPreviewerContainer.innerHTML = "";

            let docIdHidden = this._mainForm.querySelector('.doc-id');

            if (docIdHidden)
                this._mainForm.removeChild(docIdHidden);
        });

        this._btnSubmit.addEventListener('click', () => {
            this._hiddenSubmit.click();
        });
    }

    checkErrorBag()
    {
        // Check if there was an error bag
        let errorBag = this.rootElement.querySelector('.error-bag');

        if (errorBag)
        {
            try
            {
                // Parse the JSON content of the textarea
                let validationErrors = JSON.parse(errorBag.textContent);
                return validationErrors;
            }
            catch (e)
            {
                // console.error('Invalid JSON in error-bag textarea:', e);
                return null;
            }
        }
        else
        {
            return null;
        }
    }

    displayErrorBagMessages(errorBag)
    {
        let lastAction = errorBag.last_action;
        delete errorBag.last_action;

        let errors = errorBag.errors;
        let lastLoadedDocProofUrl = sessionStorage.getItem('lastLoadedDocProofUrl');
        let lastLoadedDocProofFilename = sessionStorage.getItem('lastLoadedDocProofFilename');

        let docPreviewOptions = {
            url: lastLoadedDocProofUrl,
            docId: errors.doc_id,
            fileName: lastLoadedDocProofFilename
        };

        if ('file-upload' in errors)
        {
            this.appendFileInput(lastAction);

            if (lastAction === 'update')
            {
                docPreviewOptions['hideOnCreate'] = true;
            }
        }

        if (lastAction !== 'create')
            this.appendDocPreview(docPreviewOptions);

        // sessionStorage.removeItem('lastLoadedDocProofFilename');
        // sessionStorage.removeItem('lastLoadedDocProofUrl');

        for (let field in errors)
        {
            // Find the input element by name
            let inputElement = this._mainForm.querySelector('[name="' + field + '"]');

            if (!inputElement)
            {
                console.warn('input element not found: ' + '[name="' + field + '"]');
                continue;
            }

            if (errors[field].oldValue != null)
            {
                inputElement.value = errors[field].oldValue;
            }

            let errorMessage = errors[field].message;

            // Find the existing 'invalid-feedback' div next to the input element
            let errorDiv = inputElement.parentNode.querySelector('.invalid-feedback');

            if (errorDiv)
            {
                // If found, set the text content to the error message
                errorDiv.textContent = errorMessage;
            }
            else
            {
                // If not found, create a new 'invalid-feedback' div and set its text content
                errorDiv = document.createElement('div');
                errorDiv.classList.add('invalid-feedback');
                errorDiv.textContent = errorMessage;
                inputElement.parentNode.appendChild(errorDiv);
            }
        }

        this._mainForm.classList.add('was-validated');

        if (lastAction !== null)
        {
            console.log(lastAction);
            if (lastAction === 'create') this.showCreate();
            if (lastAction === 'update') this.showUpdate();
        }
    }

    appendFileInput(action = 'create')
    {
        // Check if there's already an appended file input
        if (this._fileInputContainer.querySelector('input[type="file"]'))
        {
            // console.log('File input already exists.');
            // Exit if a file input already exists
            return;
        }

        let clone = (action == 'create')
                  ? this._fileInputTemplateCreate.cloneNode(true)
                  : this._fileInputTemplateUpdate.cloneNode(true);

        clone.querySelector('.btn-revert')?.addEventListener('click', (e) => {
            this._fileInputContainer.innerHTML = "";
            this._docPreviewerContainer.querySelector('.d-none').classList.remove('d-none');
        });

        this._fileInputContainer.innerHTML = "";
        this._fileInputContainer.appendChild(clone);
    }

    appendDocPreview(data)
    {
        // Check if there's already an appended file input
        if (this._docPreviewerContainer.querySelector('.pdf-thumbnail'))
        {
            console.log('Thumbnail previewer already exists.');
            return; // Exit if a file input already exists
        }

        this._docPreviewerContainer.innerHTML = "";

        let clone = this._docProofPreviewThumbnail.cloneNode(true);
        clone.querySelector('.lbl-document-filename').innerText = data.fileName;
        clone.querySelector('.btn-upload-new-doc-proof').addEventListener('click', (e) =>
        {
            this.appendFileInput('update');
            clone.classList.add('d-none');
        });

        if ('hideOnCreate' in data && data.hideOnCreate !== false)
            clone.classList.add('d-none');

        this._docPreviewerContainer.appendChild(clone);
        this.appendHiddenDocIdField(this._mainForm, data.docId);

        let pdfThumbnail = new PdfThumbnail({
            url: data.url,
            previewSurface: clone.querySelector('.pdf-thumbnail')
        });

        pdfThumbnail.load();
    }

    appendHiddenDocIdField(appendTo, docIdValue)
    {
        if (this._mainForm.querySelector('.doc-id'))
        {
            return;
        }

        let attrs = {
            'class' : 'doc-id',
            'name'  : 'doc_id',
            'type'  : 'hidden',
            'value' : docIdValue
        };

        let docId = document.createElement('input');

        for (const [k, v] of Object.entries(attrs))
        {
            docId.setAttribute(k, v);
        }

        appendTo.appendChild(docId);
    }

    async fetchEditDetails(docId)
    {
        showWaitingDialog();
        let errorMsg = '';

        try
        {
            const data = await this.makeFetchRequest(this._routeActionFetch, docId);
            hideWaitingDialog();
            await sleep(400);
            return data;
        }
        catch (error)
        {
            hideWaitingDialog();
            await sleep(400);
            errorMsg = this.handleFetchError(error);
            showError(errorMsg);
            throw new Error(errorMsg);
        }
    }

    makeFetchRequest(url, docId)
    {
        return new Promise((resolve, reject) => {
            const fetchUrl = new URL(url);
            fetchUrl.searchParams.append('docId', docId);

            const xhr = new XMLHttpRequest();
            xhr.open('GET', fetchUrl.toString(), true);
            xhr.onreadystatechange = function()
            {
                if (xhr.readyState === 4)
                {
                    if (xhr.status === 200)
                        resolve(JSON.parse(xhr.responseText));

                    else
                        reject({ status: xhr.status });
                }
            };
            xhr.onerror = () => reject({ status: xhr.status });
            xhr.send();
        });
    }

    handleFetchError(error)
    {
        let errorMsg = "Sorry, we're unable to read the data from the records. Please try again later.";
        switch (error.status)
        {
            case 500:
                errorMsg = "Sorry, a technical error has occurred while retrieving the record. Please try again later.";
                break;
            case 404:
                errorMsg = "Sorry, the requested record was not found.";
                break;
            default:
                errorMsg = "Sorry, we're unable to read the data from the records. Please try again later.";
        }
        return errorMsg;
    }

    showCreate()
    {
        this._modalTitleElement.textContent = `${this.actionPrefixTitleCreate} ${this.baseTitle}`;
        this._mainForm.setAttribute('action', this._routeActionCreate);
        this.appendFileInput();
        this.showModal();
    }

    showUpdate(options)
    {
        this._modalTitleElement.textContent = `${this.actionPrefixTitleUpdate} ${this.baseTitle}`;
        this._mainForm.setAttribute('action', this._routeActionUpdate);

        if (options)
        {
            if ('onBindInputs' in options && typeof options.onBindInputs === 'function')
            {
                let eventData = {
                    'mainForm': this._mainForm
                };

                options.onBindInputs(eventData);
            }

            if ('documentaryProof' in options && options.documentaryProof)
            {
                this.appendDocPreview(options.documentaryProof);
            }
        }

        this.showModal();
        // let data = await this.fetchEditDetails(docId);

        // if (!data)
        //     return;

        // await sleep(500);



        // const toYearSelect = form.find('#work-year-to');
        // let options = YearComboBox.generateYearOptions(new Date().getFullYear(), data.yearTo);

        // toYearSelect.html(options).selectmenu();
        // toYearSelect.val(data.yearTo).selectmenu('refresh');
    }

    showModal()
    {
        if (!this.modalInstance)
        {
            console.warn('Cannot show modal of empty instance');
            return;
        }

        this.modalInstance.show();
    }

    submit()
    {

    }
}
