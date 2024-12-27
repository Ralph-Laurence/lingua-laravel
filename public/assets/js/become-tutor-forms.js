$(() => {
    var carouselElem = document.querySelector('#form-carousel')
    var carousel = new bootstrap.Carousel(carouselElem, {
        interval: false,
        wrap: false
    });

    $('.btn-next-step').on('click', function(e)
    {
        if (!validateFields(e.currentTarget))
        {
            MsgBox.showError("It looks like you forgot to fill out a field. Please double-check your entries.", "Oops!")
            return;
        }

        var targetFrame = $(e.currentTarget).data('next-frame');
        carousel.to(targetFrame);
    });

    $('.btn-prev-step').on('click', function(e)
    {
        var targetFrame = $(e.currentTarget).data('prev-frame');
        carousel.to(targetFrame);
    });
});

function validateFields(sender)
{
    // If step1's next button was clicked..,
    // we validate the step1's fields
    if ($(sender).attr('id') === 'step1-next-button')
    {
        if (!$('#bio').get(0).checkValidity())
        {
            $('#bio').addClass('is-invalid');
            scrollToTarget("#bio");
            return false;
        }

        if (!$('#about').get(0).checkValidity())
        {
            $('#about').addClass('is-invalid');
            scrollToTarget("#about-me");
            return false;
        }
    }

    if ($(sender).attr('id') === 'step2-submit-button')
    {
        if ($('.form-control:invalid').length)
        {
            let firstOccurrence = $('.form-control:invalid').get(0);
            scrollToTarget('#' + $(firstOccurrence).attr('id'));

            return false;
        }
    }

    return true;
}

function scrollToTarget(el)
{
    $('html, body').animate({ scrollTop: $(el).offset().top - 120 }, 100);
}
