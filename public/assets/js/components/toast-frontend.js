class FrontendToast
{
    constructor(selector)
    {
        this.selector      = selector;
        this.toastInstance = null;
    }

    initialize()
    {
        this.rootElement   = document.querySelector(this.selector);
        this.messageBody   = this.rootElement.querySelector('.toast-message');
        this.toastTitle    = this.rootElement.querySelector('.toast-title');
        this.toastInstance = new bootstrap.Toast(this.rootElement);
    }

    show(message, title)
    {
        if (message)
            this.messageBody.innerText = message;

        if (title)
            this.toastTitle.innerText = title;

        this.toastInstance.show();
    }

    close()
    {
        this.toastInstance.hide();
    }
}
