function filterFaculty() {
    const grade = document.getElementById('gradeFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    $.ajax({
        url: '../../processes/a/fetch_faculty.php',
        type: 'GET',
        data: { grade: grade, status: status },
        success: function(response) {
            $('#facultyList').html(response);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching faculty:', error);
        }
    });
}

function searchFaculty() {
    const searchTerm = document.getElementById('searchFaculty').value;

    $.ajax({
        url: '../processes/a/fetch_faculty.php',
        type: 'GET',
        data: { search: searchTerm },
        success: function(response) {
            $('#facultyList').html(response);
        },
        error: function(xhr, status, error) {
            console.error('Error searching faculty:', error);
        }
    });
}

function confirmDeactivation(id) {
    if (confirm('Are you sure you want to deactivate this faculty member?')) {
        // Send AJAX request to deactivate
        $.ajax({
            url: '../../processes/deactivate_faculty.php', // Create this file to handle deactivation
            type: 'POST',
            data: { id: id },
            success: function(response) {
                // Refresh the list after deactivation
                filterFaculty();
            },
            error: function(xhr, status, error) {
                console.error('Error deactivating faculty:', error);
            }
        });
    }
}
