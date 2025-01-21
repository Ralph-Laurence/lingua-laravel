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
var btnHireTutor        = undefined;
var btnLeaveTutor       = undefined;
var btnCancelHire       = undefined;

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
    btnHireTutor        = $('.btn-hire-tutor');
    btnLeaveTutor       = $('.btn-leave-tutor');
    btnCancelHire       = $('.btn-cancel-hire-req');
}

$(document).ready(function()
{
    initializeSelectorElements();

    btnCancelEditReview .on('click', handleCancelEditReview);
    btnEditReview       .on('click', handleAllowEditReview);
    btnDeleteReview     .on('click', handleDeleteReview);
    btnHireTutor        .on('click', handleHireTutor);
    btnLeaveTutor       .on('click', handleEndTutor);
    btnCancelHire       .on('click', handleCancelHireTutor);

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

    var fullReviewPopperTriggers = [].slice.call(document.querySelectorAll('.popover-fullreview-toggle[data-bs-toggle="popover"]'));
    fullReviewPopperTriggers.map(function (popoverTriggerEl)
    {
        return new bootstrap.Popover(popoverTriggerEl)
    });

    // From utils.js
    initializeBsTooltips();

    // Display the max allowed length of review textarea
    updateCharLengthCounter();

    showReviewReadMoreTextOnOverflow();

    reviewTextarea.on('input', () => updateCharLengthCounter());
});
//
//===================================================
//      C L I C K   E V E N T   C A L L B A C K S
//===================================================
//
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

function handleDeleteReview()
{
    let form      = $('#tutor-hiring-action-form');
    let action    = form.data('action-delete-review');
    let prompt    = 'Are you sure you want to delete your review?';

    ConfirmBox.show(prompt, 'Delete Review',
    {
        onOK: () => {
            disableButton(btnDeleteReview);
            form.attr('action', action);
            showProcessingDialog();
            form.submit();
        }
    });
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
            disableButton(btnHireTutor);
            form.attr('action', action);
            showProcessingDialog();
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
            disableButton(btnLeaveTutor);
            form.attr('action', action);
            showProcessingDialog();
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
            disableButton(btnCancelHire);
            form.attr('action', action);
            showProcessingDialog();
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
    if (!reviewTextarea.length)
        return;

    let currentLength = reviewTextarea.val().length;
    reviewCharCtr.text(`${currentLength}/${reviewMaxLength}`);
}

function disableButton(targetButton)
{
    if (!targetButton.length)
        return;

    targetButton.prop('disabled', true);
}

function showProcessingDialog()
{
    waitingDialog.show("Processing...", {
        headerSize: 6,
        headerText: "Hold on, this shouldn't take long...",
        dialogSize: 'sm',
        contentClass: 'text-13'
    });
}

function showReviewReadMoreTextOnOverflow()
{
    const reviewElements = $('.review-text');

    $.each(reviewElements, function(index, el)
    {
        if (el.scrollHeight > el.clientHeight)
        {
            // Content is truncated, show the "See More" button
            const seeMoreButton = $(el).next('.popover-fullreview-toggle');
            seeMoreButton.removeClass('d-none');
        }
    });
}
