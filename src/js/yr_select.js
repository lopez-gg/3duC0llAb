document.addEventListener('DOMContentLoaded', function() {
    const yearRangeDropdown = document.getElementById('yearRangeDropdown');
    const currentYearRangeSpan = document.getElementById('currentYearRange');

    if (yearRangeDropdown) {
        yearRangeDropdown.addEventListener('click', function(event) {
            const target = event.target;

            if (target.classList.contains('dropdown-item')) {
                const selectedYearRange = target.getAttribute('data-year-range');
                console.log('Fetching events for year range:', selectedYearRange);

                // Update the displayed current year range
                if (currentYearRangeSpan) {
                    currentYearRangeSpan.textContent = selectedYearRange;
                }

                // Fetch events for the selected year range
                fetchEvents(selectedYearRange);
            }
        });
    }

    const sortOptions = document.querySelectorAll('.sort-option');

    sortOptions.forEach(option => {
        option.addEventListener('click', function (e) {
            e.preventDefault();
            const order = this.getAttribute('data-order');
            handleSort(order);
        });
    });
});

function handleSort(order) {
    const selectedYearRange = document.getElementById('currentYearRange').textContent.trim();
    const page = 1; // Reset to first page when sorting
    fetchEvents(selectedYearRange, order, page);
}

function fetchEvents(yearRange = '', order = 'ASC', page = 1) {
    fetch('../../src/processes/a/fetch_events.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            year_range: yearRange,
            order: order,
            page: page
        })
    })
    .then(response => response.json())
    .then(data => updateEventTable(data.events))
    .catch(error => console.error('Error fetching events:', error));
}



$(document).ready(function() {
    $('#yearRangeDropdown .dropdown-item').click(function() {
        var selectedYearRange = $(this).data('year-range');
        window.location.href = updateURLParameter(window.location.href, 'year_range', selectedYearRange);
    });

    $('.sort-option').click(function() {
        var selectedOrder = $(this).data('order');
        window.location.href = updateURLParameter(window.location.href, 'order', selectedOrder);
    });
});

function updateURLParameter(url, param, paramVal) {
    var newUrl = new URL(url);
    newUrl.searchParams.set(param, paramVal);
    return newUrl.toString();
}