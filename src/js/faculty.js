function searchFaculty() {
    const searchTerm = document.getElementById('searchFaculty').value; // Get the search term
    const grade = ''; // Reset the grade filter
    const status = ''; // Reset the status filter

    // Build the new URL
    let newUrl = '../../public/admin/faculty.php';
    const params = [];

    if (searchTerm) {
        params.push('search=' + encodeURIComponent(searchTerm)); // Add search term to params
    }

    if (params.length > 0) {
        newUrl += '?' + params.join('&'); // Append parameters to URL
    }

    // Update the URL in the address bar
    history.pushState(null, '', newUrl);

    // Clear the filters
    document.getElementById('gradeFilter').value = ''; // Reset grade filter
    document.getElementById('statusFilter').value = ''; // Reset status filter

    // Make AJAX request to fetch results based on search
    $.ajax({
        url: '../../public/admin/faculty.php',
        type: 'GET',
        data: {
            search: searchTerm, // Pass search term
        },
        success: function(response) {
            $('#facultyList').html(response); // Update the faculty list with the response
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX Error:', textStatus, errorThrown);
            alert('An error occurred while searching. Please try again later.');
        }
    });
}

function filterFaculty() {
    const grade = document.getElementById('gradeFilter').value; // Get the grade filter value
    const status = document.getElementById('statusFilter').value; // Get the status filter value
    const searchTerm = ''; // Reset search term

    // Build the new URL
    let newUrl = '../../public/admin/faculty.php';
    const params = [];

    if (grade) {
        params.push('grade=' + encodeURIComponent(grade)); // Add grade to params
    }
    if (status) {
        params.push('status=' + encodeURIComponent(status)); // Add status to params
    }

    if (params.length > 0) {
        newUrl += '?' + params.join('&'); // Append parameters to URL
    }

    // Update the URL in the address bar
    history.pushState(null, '', newUrl);

    // Clear the search input
    document.getElementById('searchFaculty').value = ''; // Reset search input

    // Make AJAX request to fetch filtered results
    $.ajax({
        url: '../../public/admin/faculty.php',
        type: 'GET',
        data: {
            grade: grade,
            status: status
        },
        success: function(response) {
            $('#facultyList').html(response); // Update the faculty list with the response
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX Error:', textStatus, errorThrown);
            alert('An error occurred while filtering the faculty list. Please try again later.');
        }
    });
}

$(document).on('click', '.verifyDeactivationButton', function() {
    var facultyId = $(this).data('faculty-id');
    var facultyUname = $(this).data('faculty-username');
    
    // Open modal and set the confirmation message for deactivation
    $('#verificationMessage').text('Are you sure you want to deactivate this account?');
    $('#verifyDeactivationModal').modal('show');

    // When Confirm button is clicked for deactivation
    $('#confirmDeactivation').off('click').on('click', function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../../src/processes/a/faculty_actions.php';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'deactivate';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'faculty_id';
        idInput.value = facultyId;

        const fUname = document.createElement('input');
        fUname.type = 'hidden';
        fUname.name = 'faculty_username';
        fUname.value = facultyUname;

        form.appendChild(actionInput);
        form.appendChild(idInput);
        form.appendChild(fUname);
        document.body.appendChild(form);
        form.submit();
        
        // Hide the modal
        $('#verifyDeactivationModal').modal('hide');
    });
});

$(document).on('click', '.verifyActivationButton', function() {
    var facultyId = $(this).data('faculty-id');
    var facultyUname = $(this).data('faculty-username');
    
    // Open modal and set the confirmation message for activation
    $('#verificationMessage').text('Are you sure you want to activate this account?');
    $('#verifyActivationModal').modal('show');

    // When Confirm button is clicked for activation
    $('#confirmActivation').off('click').on('click', function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../../src/processes/a/faculty_actions.php';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'activate';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'faculty_id';
        idInput.value = facultyId;

        const fUname = document.createElement('input');
        fUname.type = 'hidden';
        fUname.name = 'faculty_username';
        fUname.value = facultyUname;

        form.appendChild(actionInput);
        form.appendChild(idInput);
        form.appendChild(fUname);
        document.body.appendChild(form);
        form.submit();
        
        // Hide the modal
        $('#verifyactivationModal').modal('hide');
    });
});
