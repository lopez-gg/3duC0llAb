

function openVerificationModal(formId, title = '', message = '', confirmText = '', redirectUrl = '', usertype = '') {
    $('#verificationModal').data('form-id', formId);
    $('#verificationModal').data('redirect-url', redirectUrl); 
    $('#verificationModal').data('usertype', usertype); 
    $('#verificationModal').find('.modal-title').text(title);
    $('#verificationModal').find('.modal-body p').text(message);
    $('#verificationModal').find('.btn-danger').text(confirmText);
    $('#verificationModal').modal('show');
}

$('#verificationModal').on('hidden.bs.modal', function () {
    $(this).removeData('form-id');
    $(this).removeData('redirect-url');
    $(this).removeData('usertype');
});


$('#verificationModal').on('click', '.btn-danger', function () {
    var formId = $('#verificationModal').data('form-id');
    var redirectUrl = $('#verificationModal').data('redirect-url');
    var usertype = $('#verificationModal').data('usertype');
    if (formId) {
        console.log('Submitting form: ', formId);  // Debugging log
        $.ajax({
            url: $('#' + formId).attr('action'),
            type: 'POST',
            data: $('#' + formId).serialize(),
            success: function(response) {
                console.log('Response: ', response);  // Debugging log
                console.log(redirectUrl);
                if (redirectUrl) {
                    window.location.href = redirectUrl;  // Redirect to the specified URL
                } else if (!redirectUrl){
                    if (usertype === 'user'){
                        window.location.href = 'dashboard.php'; 
                    }else if (usertype === 'admin'){
                        window.location.href = 'dashboard.php';  
                    }else {
                        window.location.href = 'login.php'; 
                    }
                }  // Redirect to the page after deletion
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




function confirmDeleteModal(formId, title = '', message = '', confirmText = '') {
    $('#confirmDeleteModal').data('form-id', formId);
    $('#confirmDeleteModal').find('.modal-title').text(title);
    $('#confirmDeleteModal').find('.modal-body p').text(message);
    $('#confirmDeleteModal').find('.btn-danger').text(confirmText);
    $('#confirmDeleteModal').modal('show');
}


$('#confirmDeleteModal').on('click', '.btn-danger', function () {
    var formId = $('#confirmDeleteModal').data('form-id');
    if (formId) {
        console.log('Submitting form: ', formId);
        $('#' + formId).submit();  // Use regular form submission
        $('#confirmDeleteModal').modal('hide');  // Hide the modal
    } else {
        console.log('No form ID found.');
    }
});

