

function openVerificationModal(formId, title = '', message = '', confirmText = '') {
    $('#verificationModal').data('form-id', formId);
    $('#verificationModal').find('.modal-title').text(title);
    $('#verificationModal').find('.modal-body p').text(message);
    $('#verificationModal').find('.btn-danger').text(confirmText);
    $('#verificationModal').modal('show');
}

$('#verificationModal').on('hidden.bs.modal', function () {
    $(this).removeData('form-id');
});

$('#verificationModal').on('click', '.btn-danger', function () {
    var formId = $('#verificationModal').data('form-id');
    if (formId) {
        console.log('Submitting form: ', formId);  // Debugging log
        $.ajax({
            url: $('#' + formId).attr('action'),
            type: 'POST',
            data: $('#' + formId).serialize(),
            success: function(response) {
                console.log('Response: ', response);  // Debugging log
                // Redirect or update the page based on success
                window.location.href = 'handle_events.php';  // Redirect to the page after deletion
            },
            error: function(xhr, status, error) {
                console.log('Error: ', error);  // Debugging log
            }
        });
        $('#verificationModal').modal('hide');  // Close the modal after submitting
    } else {
        console.log('No form ID found.');
    }
});
