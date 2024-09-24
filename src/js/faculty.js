function filterFaculty() {
    const grade = document.getElementById('gradeFilter').value;
    const status = document.getElementById('statusFilter').value;

    // Build the new URL
    let newUrl = '../../public/admin/faculty.php';
    const params = [];

    if (grade) {
        params.push('grade=' + encodeURIComponent(grade));
    }
    if (status) {
        params.push('status=' + encodeURIComponent(status));
    }
    
    if (params.length > 0) {
        newUrl += '?' + params.join('&');
    }

    // Update the URL in the address bar
    history.pushState(null, '', newUrl);

    $.ajax({
        url: '../../public/admin/faculty.php',
        type: 'GET',
        data: {
            grade: grade,
            status: status
        },
        success: function(response) {
            $('#facultyList').html(response);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX Error:', textStatus, errorThrown);
            alert('An error occurred while filtering the faculty list. Please try again later.');
        }
    });
}


function searchFaculty() {
    const query = document.getElementById('searchFaculty').value.toLowerCase();
    const rows = document.querySelectorAll('#facultyList tr');

    rows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        let match = false;

        // Check if any cell matches the search query
        for (let i = 1; i < cells.length - 1; i++) { // Skip the first cell (#) and last cell (Actions)
            if (cells[i].textContent.toLowerCase().includes(query)) {
                match = true;
                break;
            }
        }

        // Show or hide the row based on the match
        row.style.display = match ? '' : 'none';
    });
}
