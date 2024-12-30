$(document).ready(function()
{
    getNewCaptcha();

    function checkInputs()
    {
        let allFilled = true;

        $('.asl-input').each(function()
        {
            if ($(this).val() === '')
                allFilled = false;
        });

        // If all inputs are filled, trigger validation
        if (allFilled)
            validateCaptcha();
    }

    $(document).on('input', '.asl-input', function()
    {
        checkInputs();

        if ($(this).val().length == $(this).attr('maxlength'))
        {
            let currentId = $(this).data('input-number');

            if (currentId == 4)
                currentId = 4;

            let nextCard = $(`input[data-input-number="${currentId+1}"]`);

            // Move focus to the next input in the next card
            nextCard.focus();
        }
    });
});

function validateCaptcha()
{
    $.ajax({
        url: '/api/validate-captcha',
        method: 'POST',
        data: {
            '_token': $('#captcha_csrf').val(),
            'captchaText' : $('#captcha_text').val(),
            'userInput' : getCaptchaInput(),
        },
        success: function(data)
        {
            if (data)
            {
                if (data.status == '200')
                {
                    $('.captcha-error').addClass('d-none');
                    $('.captcha-passed').removeClass('d-none');
                    $('.asl-input').prop('readonly', true);

                    $('.submit-target').each(function()
                    {
                        // The submit target can be enclosed in an alert box,
                        // if the user is a learner registering as a tutor
                        if (this.tagName.toLowerCase() === 'div')
                        {
                            $('.submit-target').show();
                        }

                        // Otherwise just enable the button
                        else if (this.tagName.toLowerCase() === 'button')
                        {
                            $('.submit-target').prop('disabled', false);
                        }
                    });

                    focusInto('#btn-submit');
                }
            }
            console.log(data)
        },
        error: function(xhr)
        {
            if (xhr.status == '422')
            {
                $('.captcha-error').removeClass('d-none');

                let response = JSON.parse(xhr.responseText);

                if (response.newcaptcha)
                {
                    let data = {
                        'captchaImages' : response.newcaptcha.captchaImages,
                        'captchaText' : response.newcaptcha.captchaText
                    };
                    buildCaptchaCards(data);
                }

                MsgBox.showError('That was a mistake. Please try again.', 'Oops!');
            }
            else
            {
                MsgBox.showError('The system is having issues. Please try again later.', 'Fatal Error');
            }
        }
    });
}

function getNewCaptcha()
{
    $.ajax({
        url: '/api/recaptcha',
        method: 'GET',
        success: function(data)
        {
            buildCaptchaCards(data);
        }
    });
}

function buildCaptchaCards(data)
{
    $('#captcha-box').empty(); // Clear previous captcha cards
    $('#captcha_text').val(data.captchaText);

    for (let i = 0; i < data.captchaImages.length; i++)
    {
        let image  = data.captchaImages[i];
        let imgSrc = `data:image/png;base64,${image}`;

        $('#captcha-box').append(`
            <div class="card" style="width: 10rem;">
                <div class="card-body">
                    <div class="mb-3">
                        <img src="${imgSrc}" id="asl-preview-${i}">
                    </div>
                    <div>
                      <input type="text" data-input-number="${i+1}" class="form-control asl-input text-uppercase text-center" placeholder="Letter" id="asl-input-${i}" maxlength="1">
                    </div>
                </div>
            </div>`);
    }
}

function getCaptchaInput()
{
    let res = '';

    for (let i = 0; i < 4; i++)
    {
        res += $(`#asl-input-${i}`).val();
    }

    return res;
}
