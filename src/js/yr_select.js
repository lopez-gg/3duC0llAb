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
});

function fetchEvents(yearRange) {
    fetch('../../src/processes/a/fetch_sy_selection.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ year_range: yearRange })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Error fetching events:', data.error);
        } else {
            updateEventTable(data.events);
        }
    })
    .catch(error => {
        console.error('Error fetching events:', error);
    });
}

function updateEventTable(events) {
    const tableBody = document.getElementById('eventList');
    if (!tableBody) return;

    tableBody.innerHTML = '';

    let index = 1;
    events.forEach(event => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index++}</td>
            <td>${event.title ?? ''}</td>
            <td>${event.description ?? ''}</td>
            <td>${event.event_date ?? ''}</td>
            <td>${event.end_date ?? ''}</td>
            <td>
                <form action="update_event.php" method="GET" style="display:inline;">
                    <input type="hidden" name="id" value="${event.id ?? ''}">
                    <input type="hidden" name="type" value="${event.event_type ?? ''}">
                    <input type="hidden" name="year_range" value="${event.year_range ?? ''}">
                    <button type="submit" class="btn btn-normal"><i class="bi bi-pencil-square"></i></button>
                </form>
                <form id="deleteForm_${event.id ?? ''}" action="../../src/processes/a/delete_event.php" method="POST" style="display:none;">
                    <input type="hidden" name="id" value="${event.id ?? ''}">
                </form>
                <button type="button" class="btn btn-danger" onclick="openVerificationModal('deleteForm_${event.id ?? ''}', 'Confirm Deletion', 'Are you sure you want to delete this event?', 'Delete')">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}
