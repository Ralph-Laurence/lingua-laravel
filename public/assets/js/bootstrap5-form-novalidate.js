//
// Default bootstrap form submit validity behaviour
//
// (function () {
//     'use strict'

//     var forms = document.querySelectorAll('.needs-validation');

//     console.log('total needs validation: ', forms.length)
//     console.log('reached #1');

//     Array.prototype.slice.call(forms).forEach(function (form)
//     {
//         console.log('reached #2');

//         form.addEventListener('submit', function (event)
//         {
//             console.log('reached #3');

//             if (!form.checkValidity())
//             {
//                 console.log('reached #4');
//                 event.preventDefault();
//                 event.stopPropagation();
//             }

//             form.classList.add('was-validated');

//         }, false)
//     });
// })();

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
