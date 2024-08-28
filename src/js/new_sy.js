document.getElementById('yearRangeForm').addEventListener('submit', function(event) {
    event.preventDefault();

console.log('new_sy.js is loaded');

    var startYear = document.getElementById('startYear').value;
    var endYear = document.getElementById('endYear').value;

    if (startYear && endYear) {
        var yearRange = startYear + '-' + endYear;

        // Save the year range in the database using AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../../src/processes/a/add_sy.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Redirect after successful save
                window.location.href = '../../public/admin/add_new_event.php?sy=' + encodeURIComponent(yearRange);
            }
        };
        xhr.send('yearRange=' + encodeURIComponent(yearRange));
    }
});