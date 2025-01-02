//
// Bootstrap form submit validity behaviour with Emitter
//
$(document).ready(function() {

    (function () {
        'use strict'

        var forms = document.querySelectorAll('.needs-validation');

        Array.prototype.slice.call(forms).forEach(function (form)
        {
            form.addEventListener('submit', function (event)
            {
                if (!form.checkValidity())
                {
                    event.preventDefault();
                    event.stopPropagation();
                    $(document).trigger('formValidityFailed', [form]);
                }
                else
                {
                    event.preventDefault();
                    $(document).trigger('formValidityPassed', [form]);
                }

                form.classList.add('was-validated');

            }, false)
        });
    })();

});