let quill;

function initStep1()
{
    $('#disability').selectmenu();

    quill = new Quill('#about-me', {
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                // [{'list': 'ordered'}, {'list': 'bullet'}, {'list': 'check'}],
                [{'script': 'sub'}, {'script': 'super'}],
            ]
        },
        placeholder: 'Tell us more about you... Share your personality, interests, and what drives you. Go beyond professional achievements—include hobbies, interests, and life outside tutoring to provide a well-rounded view.',
        theme: 'snow',
    });

    const bioMaxLength   = 180;
    const aboutMaxLength = 2000;

    // QuillJS:
    // quill.getLength() "Retrieves the length of the editor contents. Note even when Quill is empty,
    // there is still a blank line represented by '\n', so getLength will return 1."

    updateCharLengthCounter($('#bio').val().length, bioMaxLength, '#bio-char-counter');
    updateCharLengthCounter(quill.getLength()-1, aboutMaxLength, '#about-char-counter');

    quill.on('text-change', function(delta, oldDelta, source)
    {
        if (quill.getLength() > aboutMaxLength)
        {
            quill.deleteText(aboutMaxLength, quill.getLength());
        }

        if (quill.root.innerText.trim() === '')
        {
            $('#about').val('');
            $('#about-me').addClass('is-invalid');
        }
        else
        {
            $('#about').val(quill.root.innerHTML);
            $('#about-me').removeClass('is-invalid');
        }

        updateCharLengthCounter(quill.getLength()-1, aboutMaxLength, '#about-char-counter');
    });

    $('#bio').on('input', function() {
        updateCharLengthCounter($('#bio').val().length, bioMaxLength, '#bio-char-counter');

        if ($(this).val().trim() === '')
            $(this).addClass('is-invalid');

        else
            $(this).removeClass('is-invalid');
    });

    function updateCharLengthCounter(current, max, el)
    {
        $(el).text(`${current}/${max}`);
    }
}
