$(document).ready(function()
{
    const form = document.getElementById('main-form');
    const carouselElem = document.querySelector('#form-carousel');
    const carousel     = new bootstrap.Carousel(carouselElem, {
        interval: false,
        wrap: false
    });

    // Scroll to the target invalid input
    const focusInvalidInput = function()
    {
        if ($('.form-control.is-invalid').length)
        {
            let elementId = '#' + $('.form-control.is-invalid').attr('id');

            if (elementId == '#about')
                elementId = '#about-me';

            console.log(elementId);
            focusInto(elementId);
        }

        MsgBox.showError("It looks like you forgot to fill out a field. Please double-check your entries.", "Oops!");
    };

    const validateSlide = function(slideElement)
    {
        let isValid  = true;
        const inputs = slideElement.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            if (!input.checkValidity())
            {
                isValid = false;
                input.classList.add('is-invalid');

                if ($(input).attr('id') == 'about')
                    $('#about-me').addClass('is-invalid');
            }
            else
            {
                input.classList.remove('is-invalid');

                if ($(input).attr('id') == 'about')
                    $('#about-me').remove('is-invalid');
            }
        });

        return isValid;
    };

    // Handle next button clicks
    document.querySelectorAll('.btn-next-slide').forEach(button => {

        button.addEventListener('click', function()
        {
            const currentSlide = document.querySelector('.carousel-item.active');

            if (validateSlide(currentSlide))
                carousel.next();

            else
                focusInvalidInput();
        });
    });

    // Handle previous button clicks
    document.querySelectorAll('.btn-prev-slide').forEach(button => {
        button.addEventListener('click', function() {
            carousel.prev();
        });
    });

    // Reset validation state when switching slides
    document.getElementById('form-carousel').addEventListener('slide.bs.carousel', function() {
        document.querySelectorAll('.is-invalid').forEach(element => {
            element.classList.remove('is-invalid');
        });
    });

    // Handle form submission
    form.addEventListener('submit', function(event)
    {
        event.preventDefault();

        const currentSlide = document.querySelector('.carousel-item.active');

        if (!validateSlide(currentSlide))
            return;

        // If all validations pass, you can submit the form
        console.log('Form is valid, submitting...');
        form.submit();
    });
});

function focusInto(elementId) {
    $('html, body').animate({ scrollTop: $(elementId).offset().top - 120 }, 100);
}
