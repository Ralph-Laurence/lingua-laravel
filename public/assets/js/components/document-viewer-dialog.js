class DocumentViewerDialog
{
    static pdfIframe = null;
    static pdfViewer = null;
    static events = {
        'NotFound'      : 'docProf404',
        'LoadStarted'   : 'docLoadStart',
        'LoadFinished'  : 'docLoadEnd',
    };

    static initialize()
    {
        this.pdfIframe = document.getElementById('pdf-iframe');
        this.pdfViewer = new bootstrap.Modal(document.getElementById('pdf-viewer'));
    }

    static sleep(ms)
    {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    static async show(src)
    {
        // this turns off the editor toolbar for pdf iframe
        if (!src.endsWith('#toolbar=0'))
            src = `${src}#toolbar=0`;

        if (this.pdfIframe == undefined)
            return;

        // If  the pdf iframe have the same src as the given src,
        // we should skip loading the new iframe source from the server
        if (this.pdfIframe.src == src)
        {
            this.pdfViewer.show();
            return;
        }

        document.dispatchEvent(new Event(this.events.LoadStarted));

        try
        {
            const response = await fetch(src);
            await this.sleep(1000);

            document.dispatchEvent(new Event(this.events.LoadFinished));

            if (response.ok)
            {
                this.render(src);
            }

            else
                throw new Error('Documentary Proof Not Found');
        }
        catch
        {
            let eventOnFailed = new Event(this.events.NotFound);
            document.dispatchEvent(eventOnFailed);
        }
    }

    static render(src)
    {
        this.pdfIframe.src = src;
        this.pdfViewer.show();
    }
}

// Initializing the class and interacting with the DOM inside DOMContentLoaded
document.addEventListener('DOMContentLoaded', function () {
    DocumentViewerDialog.initialize();
});
