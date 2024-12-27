(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
})()

// (function () {
//     'use strict';
//     var forms = document.querySelectorAll('.needs-validation');
//     Array.prototype.slice.call(forms)
//         .forEach(function (form) {
//             form.addEventListener('submit', function (event) {
//                 if (!form.checkValidity()) {
//                     event.preventDefault();
//                     event.stopPropagation();

//                     // Find all invalid elements and add the is-invalid class
//                     var invalidElements = form.querySelectorAll(':invalid');
//                     invalidElements.forEach(function (element) {
//                         element.classList.add('is-invalid');
//                     });
//                 }
//                 form.classList.add('was-validated');
//             }, false);
//         });
// })();
