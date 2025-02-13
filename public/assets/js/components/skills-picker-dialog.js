class SkillsPickerDialog
{
    constructor(options)
    {
        this.selector               = '#skillsPickerModal';
        this.rootElement            = null;
        this.modalInstance          = null;
        this.onDialogResult         = null; // Determines the close operation wether OK'd or Cancelled
        this.onDialogShow           = null;
        this.dialogResult           = 0;
        this.selectedItems          = null;
        this.isDirty                = false;
        this.DIALOG_RESULT_OK       = 1;
        this.DIALOG_RESULT_CANCEL   = -1;
    }

    /**
     * Must wait for the DOM to be fully loaded before initializations
     */
    initialize(datasource)
    {
        this.rootElement    = document.querySelector(this.selector);
        this._btnOk         = this.rootElement.querySelector('.btn-ok');
        this._btnCancel     = this.rootElement.querySelector('.btn-cancel');
        this.modalInstance  = new bootstrap.Modal(this.rootElement);
        this.selectedItems  = datasource || this.getSelectedItems();

        this.bindEvents();
    }

    bindEvents()
    {
        this._btnOk.addEventListener('click', () => {
            this.modalInstance.hide();
            this.dialogResult = this.DIALOG_RESULT_OK;
        });

        this._btnCancel.addEventListener('click', () => {
            this.modalInstance.hide();
            this.dialogResult = this.DIALOG_RESULT_CANCEL;
        });

        this.rootElement.addEventListener('hidden.bs.modal', async () =>
        {
            if (this.onDialogResult !== null && typeof this.onDialogResult === 'function')
            {
                await this.onDialogResult(this.dialogResult);
                this.dialogResult = 0;
            }
        });

        this.rootElement.addEventListener('show.bs.modal', (event) =>
        {
            if (this.onDialogShow !== null &&
                typeof this.onDialogShow === 'function')
            {
                this.onDialogShow();
            }
        });
    }

    clearSelectedItems()
    {
        let checked = this.rootElement.querySelectorAll('.skill-checkbox:checked');

        checked.forEach((checkbox) => checkbox.checked = false);
        this.isDirty = false;
    }

    flashSelectedItems(items) {
        this.selectedItems = items;
    }

    getOldSelectedItems() {
        return this.selectedItems;
    }

    getSelectedItems()
    {
        console.log('reading from here instead ...');
        let items   = {};
        let checked = this.rootElement.querySelectorAll('.skill-picker-body .skill-checkbox:checked');

        checked.forEach( (checkbox) => {
            let label = checkbox.nextElementSibling;

            if (label && label.tagName === 'LABEL')
            {
                // Property key   : integer values as key
                // Property value : text content string
                items[checkbox.value.trim()] = label.textContent.trim();
            }
        });

        return items;
    }

    setItemsSelected(itemsToCheck)
    {
        this.clearSelectedItems();

        if (itemsToCheck && typeof itemsToCheck === 'object')
        {
            for (let [key, value] of Object.entries(itemsToCheck))
            {
                let chkBox = this.rootElement.querySelector(`#skill_${key}`);
                chkBox.checked = true;
            }
        }
    }

    show()
    {
        if (this.modalInstance === null)
        {
            console.warn('Modal instance is null');
            return;
        }

        this.modalInstance.show();
    }
}

