class PdfThumbnail
{
    constructor(options)
    {
        this.url = options.url;
        this.previewer = options.previewSurface;
        console.log(options)
    }

    load()
    {
        let previewer = this.previewer;
        let loadingTask = pdfjsLib.getDocument({ url: this.url});

        loadingTask.promise.then(function(pdf)
        {
            // Fetch the first page
            pdf.getPage(1).then(function(page)
            {
                const scale = 0.2; // Adjust scale to fit within the thumbnail size
                const viewport = page.getViewport({ scale: scale });

                // Prepare canvas using PDF page dimensions
                //const canvas    = document.getElementById('pdf-thumbnail');
                const context    = previewer.getContext('2d');
                previewer.height = viewport.height;
                previewer.width  = viewport.width;

                // Render PDF page into canvas context
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                page.render(renderContext);
            })
            .catch(function(error)
            {
                console.error('Error fetching the page:', error);
                // showError("Sorry, the PDF file could not be found.");
                // You can handle the error more gracefully, e.g., show a placeholder image
                // const canvas = document.getElementById('pdf-thumbnail');
                const context = previewer.getContext('2d');
                context.clearRect(0, 0, previewer.width, previewer.height);
                context.fillText("PDF not found", previewer.width / 2 - 30, previewer.height / 2);
            });
        })
        .catch(function(error)
        {
            console.error('Error fetching the document:', error);
            // showError("Sorry, the PDF file could not be found.");
            // Handle the error gracefully, e.g., show a placeholder image
            // const canvas = document.getElementById('pdf-thumbnail');
            const context = previewer.getContext('2d');
            context.clearRect(0, 0, previewer.width, previewer.height);
            context.fillText("PDF not found", previewer.width / 2 - 30, previewer.height / 2);
        });
    }
}
