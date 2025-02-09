class PdfThumbnail {
    constructor(options) {
        this.url = options.url;
        this.previewer = options.previewSurface;
        console.log(options);
    }

    clamp(num, min, max) {
        return Math.min(Math.max(num, min), max);
    }

    load() {
        let previewer = this.previewer;
        let loadingTask = pdfjsLib.getDocument({ url: this.url });

        loadingTask.promise.then((pdf) => {
            // Fetch the first page
            pdf.getPage(1).then((page) => {
                const fixedWidth = 230;  // Define your fixed width
                // const fixedHeight = 100; // Define your fixed height

                // Fixed height will be half the width rounded to int
                let fixedHeight = Math.floor(fixedWidth / 2);

                // Get the original viewport of the page
                const originalViewport = page.getViewport({ scale: 1 });

                // Calculate the scale to fit the fixed width
                const scale = fixedWidth / originalViewport.width;

                // Get the adjusted viewport with the calculated scale
                const viewport = page.getViewport({ scale: scale });

                // Prepare canvas using fixed dimensions
                const context = previewer.getContext('2d');
                previewer.width = fixedWidth;
                previewer.height = fixedHeight;
                previewer.classList.add('border', 'rounded');

                // Render PDF page into canvas context
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport,
                    // Crop the PDF to only render the visible portion
                    transform: [1, 0, 0, 1, 0, 0],
                    background: 'white',
                    canvasContext: context,
                    viewport: viewport,
                };

                page.render(renderContext);
            })
            .catch((error) => {
                console.error('Error fetching the page:', error);
                const context = previewer.getContext('2d');
                context.clearRect(0, 0, previewer.width, previewer.height);
                context.fillText("PDF not found", previewer.width / 2 - 30, previewer.height / 2);
            });
        })
        .catch((error) => {
            console.error('Error fetching the document:', error);
            const context = previewer.getContext('2d');
            context.clearRect(0, 0, previewer.width, previewer.height);
            context.fillText("PDF not found", previewer.width / 2 - 30, previewer.height / 2);
        });
    }
}
