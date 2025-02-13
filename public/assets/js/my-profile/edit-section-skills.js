class EditSectionSkills
{
    constructor()
    {
        this.skillsPicker = null;
        this.skillsPickerDatasource = null;
        this.frmRemoveSkills = null;
        this.mode = 0;

        this.MODE_ADD  = 1;
        this.MODE_EDIT = 2;
    }

    initialize()
    {
        this.toast = new FrontendToast('#frontendToast');
        this.toast.initialize();

        this.skillsPickerDatasource = document.querySelector('#skills-datasource');

        this.skillsPicker = new SkillsPickerDialog();
        this.skillsPicker.initialize( this.getDataSource() );

        this.frmRemoveSkills = document.querySelector('#frm-remove-skills');
        this.frmAddSkills    = document.querySelector('#frm-add-skills');

        // When the form for adding skills is present, it means there aren't
        // any skills added yet. Otherwise, the presence of form for removing
        // skill entries means there are skills already added.
        if (this.frmAddSkills !== null)
            this.mode = this.MODE_ADD;

        else if (this.frmRemoveSkills)
            this.mode = this.MODE_EDIT;

        this.bindEvents();
    }

    bindEvents()
    {
        let editBtn = document.querySelector('#btn-edit-skill');

        if (editBtn)
            editBtn.addEventListener('click', () => this.skillsPicker.show());

        let addBtn = document.querySelector('#btn-add-skill');

        if (addBtn)
            addBtn.addEventListener('click', () => this.skillsPicker.show());

        this.skillsPicker.onDialogResult = async (result) => {

            if (result === this.skillsPicker.DIALOG_RESULT_OK)
            {
                let newSelected = this.skillsPicker.getSelectedItems();

                switch (this.mode)
                {
                    case this.MODE_EDIT:

                        let oldSelected = this.skillsPicker.getOldSelectedItems();
                        let isDirty = !areObjectsEqual(oldSelected,newSelected);

                        if (isDirty)
                            return await this.updateSkills(newSelected);
                        break;

                    case this.MODE_ADD:
                        let keys = Object.keys(newSelected).join(',');

                        this.frmAddSkills.querySelector('#input-add-skills').value = keys;
                        this.frmAddSkills.submit();
                        break;
                }
            }
        };

        this.skillsPicker.onDialogShow = () => {

            let dataSource = this.getDataSource();

            if (dataSource !== null)
            {
                this.skillsPicker.setItemsSelected(dataSource);
            }
        };

        if (this.frmRemoveSkills !== null)
        {
            this.frmRemoveSkills.addEventListener('submit', (e) => {
                e.preventDefault();
                let prompt = 'Would you like to remove all skill entries?';

                ConfirmBox.show(prompt, 'Remove All Skills',
                {
                    onOK: () => {
                        showWaitingDialog();
                        this.frmRemoveSkills.submit();
                    }
                });
            });
        }
    }

    getDataSource()
    {
        let dataSource = this.skillsPickerDatasource.value.trim();

        if (dataSource !== '')
            return dataSource = JSON.parse(dataSource);

        return null;
    }

    async updateSkills(postData)
    {
        const urlAttr = this.skillsPickerDatasource.dataset.updateAction;

        showWaitingDialog();

        try
        {
            const response = await fetch(new URL(urlAttr),
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ keys: Object.keys(postData) })
            });

            if (!response.ok)
            {
                // Parse the error message from the response body
                const errorData = await response.json();

                // Check if there are validation errors
                const errorMessage = errorData.errors
                                   ? Object.values(errorData.errors).join('\n')
                                   : 'Sorry, a network error has occurred.';

                await sleep(400);
                hideWaitingDialog();

                showError(errorMessage);

                return;
            }

            const result = await response.json();
            hideWaitingDialog();

            if (result && 'data' in result && result.data)
            {
                // Update the local datasource (in entries section)
                this.skillsPickerDatasource.value = JSON.stringify(result.data);

                // Update the picker's data source
                this.skillsPicker.flashSelectedItems(result.data);

                // Set checkboxes as checked state
                this.skillsPicker.setItemsSelected(result.data);

                // Render the skills in section
                this.listDownSkills(result.data);
            }

            if (result && 'message' in result && result.message)
            {
                if (this.toast !== null)
                    this.toast.show(result.message, 'Success!');
            }
        }
        catch (err)
        {
            // Extract the error message
            const errorMessage = err.message ? err.message : 'An unknown error occurred';

            // Display the error message in a modal (example code)
            showError(errorMessage);
        }
    }

    listDownSkills(skills)
    {
        let html = '';

        for (let [key, value] of Object.entries(skills))
        {
            html += `<div data-skill-value="${key}" class="badge bg-secondary skill-item ps-3 pe-2 py-2 d-flex align-items-center justify-content-between">
                        <span class="me-2">${value}</span>
                    </div>`
        }

        document.querySelector('.skill-entry').innerHTML = html;
    }
}

document.addEventListener('DOMContentLoaded', function()
{
    let driver = new EditSectionSkills();
    driver.initialize();
});
