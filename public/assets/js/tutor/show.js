//if (confirm("Do you want to hire this ASL tutor? You can end the contract anytime.

var lastStarRating      = 0;
var reviewTextarea      = undefined;
var reviewMaxLength     = undefined;
var reviewCharCtr       = undefined;
var ratingHiddenInput   = undefined;
var btnEditReview       = undefined;
var starControlsBlocker = undefined;
var btnCancelEditReview = undefined;
var btnSubmitEditReview = undefined;
var btnDeleteReview     = undefined;

function initializeSelectorElements()
{
    reviewTextarea      = $('#input-review-comment');
    reviewMaxLength     = reviewTextarea.attr('maxlength') || 120;
    reviewCharCtr       = $('#review-char-counter');
    ratingHiddenInput   = $('#rating');
    lastStarRating      = ratingHiddenInput.val() || 0;
    btnEditReview       = $('.btn-edit-review');
    starControlsBlocker = $('.star-controls-blocker');
    btnCancelEditReview = $('#btn-cancel-update-review');
    btnSubmitEditReview = $('#btn-submit-update-review');
    btnDeleteReview     = $('#btn-delete-review');
}

$(document).ready(function()
{
    initializeSelectorElements();

    btnCancelEditReview.on('click', handleCancelEditReview);
    btnEditReview.on('click', handleAllowEditReview);
    btnDeleteReview.on('click', handleDeleteReview);

    $('.btn-hire-tutor').on('click', handleHireTutor);
    $('.btn-end-tutor').on('click',  handleEndTutor);
    $('.btn-cancel-hire-req').on('click', handleCancelHireTutor);

    $('.star-rating-control').on('click', function()
    {
        var selectedRating = $(this).data('rating');
        lastStarRating = selectedRating;
        ratingHiddenInput.val(selectedRating);

        fillStars(selectedRating);
    })
    .on('mouseover', function()
    {
        var targetRating = $(this).data('rating');

        $('.star-rating-control').removeClass('filled hover');
        $('.star-rating-control').each(function()
        {
            if ($(this).data('rating') <= targetRating)
            {
                $(this).addClass('hover');
            }
        });
    });

    $('.star-rating-wrapper').on('mouseleave', function()
    {
        fillStars(lastStarRating);
    });

    // From utils.js
    initializeBsTooltips();

    // Display the max allowed length of review textarea
    updateCharLengthCounter();

    reviewTextarea.on('input', () => updateCharLengthCounter());
});
//
//===================================================
//      C L I C K   E V E N T   C A L L B A C K S
//===================================================
//
function handleDeleteReview()
{
    let form      = $('#tutor-hiring-action-form');
    let action    = form.data('action-delete-review');
    let prompt    = 'Are you sure you want to delete your review?';

    ConfirmBox.show(prompt, 'Delete Review',
    {
        onOK: () => {
            form.attr('action', action);
            form.submit();
        }
    });
}

function handleAllowEditReview()
{
    btnEditReview.hide();
    starControlsBlocker.hide();
    btnCancelEditReview.removeClass('d-none');
    btnSubmitEditReview.prop('disabled', false);
    reviewTextarea.prop('readonly', false);
    btnDeleteReview.removeClass('d-none');
}

function handleCancelEditReview()
{
    // Get the original review comment:
    var originalReview = $('#original-review').val().trim();

    // Revert the changes to the input review
    reviewTextarea.val(originalReview);

    // Get the original stars
    var originalRating = ratingHiddenInput.data('original');

    // Revert the changes to the rating inputs
    lastStarRating = originalRating;
    ratingHiddenInput.val(originalRating);
    fillStars(originalRating);

    btnEditReview.show();
    starControlsBlocker.show();
    btnCancelEditReview.addClass('d-none');
    btnDeleteReview.addClass('d-none');
    btnSubmitEditReview.prop('disabled', true);
    reviewTextarea.prop('readonly', true);
}

function handleHireTutor()
{
    let form      = $('#tutor-hiring-action-form');
    let action    = form.data('action-hire-tutor');
    let firstname = DOMPurify.sanitize($('#tutor_name').val());
    let prompt    = `Would you like to hire <strong>${firstname}</strong> as your ASL tutor?<br><br>You can end the contract anytime.`;

    ConfirmBox.show(prompt, 'Hire Tutor',
    {
        onOK: () => {
            form.attr('action', action);
            form.submit();
        }
    });
}

function handleEndTutor()
{
    let form      = $('#tutor-hiring-action-form');
    let action    = form.data('action-leave-tutor');
    let firstname = DOMPurify.sanitize($('#tutor_name').val());
    let prompt    = `Would you like to end the tutorial contract with <strong>${firstname}</strong>?<br><br>You can hire ${firstname} again anytime.`;

    ConfirmBox.show(prompt, 'Leave Tutor',
    {
        onOK: () => {
            form.attr('action', action);
            form.submit();
        }
    });
}

function handleCancelHireTutor()
{
    let form      = $('#tutor-hiring-action-form');
    let action    = form.data('action-cancel-hire');
    let firstname = DOMPurify.sanitize(form.find('#tutor_name').val());
    let prompt    = `Would you like to cancel your hire request with <strong>${firstname}</strong>?`;

    ConfirmBox.show(prompt, 'Cancel Hire',
    {
        onOK: () => {
            form.attr('action', action);
            form.submit();
        }
    });
}

function fillStars(selectedRating)
{
    $('.star-rating-control').removeClass('filled hover');
    $('.star-rating-control').each(function()
    {
        if ($(this).data('rating') <= selectedRating)
        {
            $(this).addClass('filled');
        }
    });
}

function updateCharLengthCounter()
{
    let currentLength = reviewTextarea.val().length;
    reviewCharCtr.text(`${currentLength}/${reviewMaxLength}`);
}
