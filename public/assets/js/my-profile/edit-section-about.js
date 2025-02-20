class EditSectionAbout
{
    initialize()
    {
        this.aboutInputMax  = 2000;
        this.editModal      = document.querySelector('#aboutmeModal');
        this.editModalInst  = new bootstrap.Modal(this.editModal);
        this.lblCharCtr     = this.editModal.querySelector('#about-char-counter');
        this.btnEdit        = document.querySelector('#btn-edit-about-me');
        this.btnSave        = this.editModal.querySelector('#btn-save-edit-aboutme');
        this.aboutInputErr  = this.editModal.querySelector('#alert-error');
        this.aboutMeInput   = this.editModal.querySelector('#about-me');
        this.aboutMeOrig    = this.editModal.querySelector('#about-me-original');
        this.aboutMePreview = document.querySelector('#about-me-preview');
        this.errorLabel     = this.editModal.querySelector('#alert-error');
        this.mainForm       = this.editModal.querySelector('#frm-about');
        this.inputAboutMe   = this.editModal.querySelector('#input-about-me');
        this.hiddenSubmit   = this.editModal.querySelector('#aboutme-hidden-submit');

        this.quill = new Quill('#about-me', {
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{'script': 'sub'}, {'script': 'super'}],
                ]
            },
            placeholder: 'Tell us more about you... Share your personality, interests, and what drives you. Go beyond professional achievements—include hobbies, interests, and life outside tutoring to provide a well-rounded view.',
            theme: 'snow',
        });

        this.updateCharLengthCounter();
        this.bindEvents();

        this.aboutMePreview.value = htmlToPlainText(this.aboutMeOrig.value);
    }

    bindEvents()
    {
        this.editModal.addEventListener('show.bs.modal', (e) => {
            this.setEditorContent(this.aboutMeOrig.value);
            this.updateCharLengthCounter();
        });

        this.mainForm.addEventListener('submit', (e) => {
            this.inputAboutMe.value = this.quill.root.innerHTML;
            this.mainForm.submit();
        });

        this.btnSave.addEventListener('click', (e) => {
            // Triggers native form submit (better than .submit() for handling listeners)
            // this.hiddenSubmit.click();
            this.mainForm.requestSubmit();
        });

        this.btnEdit.addEventListener('click', (e) => this.editModalInst.show());

        this.quill.on('text-change', (delta, oldDelta, source) =>
        {
            // Clamp the enterable texts
            if (this.quill.getLength() > this.aboutInputMax)
            {
                this.quill.deleteText(this.aboutInputMax, this.quill.getLength());
            }

            // When the editor was changed but it has no contents, show the error
            if (this.quill.root.innerText.trim() === '')
            {
                this.errorLabel.classList.remove('d-none');
                this.btnSave.classList.add('disabled');
            }
            else
            {
                this.errorLabel.classList.add('d-none');
                this.btnSave.classList.remove('disabled');
            }

            this.updateCharLengthCounter();
        });
    }

    updateCharLengthCounter()
    {
        let current = this.quill.getLength() - 1;
        this.lblCharCtr.innerText = `${current}/${this.aboutInputMax}`;
    }

    setEditorContent(dangerousContent)
    {
        let sanitizedContent = DOMPurify.sanitize(dangerousContent);
        this.quill.clipboard.dangerouslyPasteHTML(sanitizedContent);
    }
}

$(document).ready(function()
{
    let driver = new EditSectionAbout();
    driver.initialize();
});
