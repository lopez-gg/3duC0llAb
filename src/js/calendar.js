$(document).ready(function() {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next ',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listMonth'
        },
        editable: false,
        events: {
            url: '../../src/processes/fetch_events.php',
            success: function(data) {
                console.log('Events fetched successfully:', data); // Debugging statement
            },
            error: function() {
                console.log('There was an error while fetching events.');
            }
        },


        eventRender: function(event, element) {
            // Only display the title initially
            element.find('.fc-title').text(event.title);

            // Add hover tooltip
            element.attr('data-toggle', 'tooltip');
            element.attr('title', event.description ? event.description : 'No description available');
        },

        eventClick: function(event, jsEvent, view) {
            // Show event details on click
            var description = event.description ? event.description : 'No description available';
            var start = event.start.format("MMMM-DD");
            var end = event.end ? event.end.format("MMMM-DD") : start;

            var $modal = $('#eventDetailsModal');
            $modal.find('.c-modal-title').text('Event Details');
            $modal.find('.c-modal-body').html(`
                <div class="event-details">
                    <div class="event-icon"><i class="bi bi-calendar-range"></i></div>
                    <div class="event-info">
                        <h4>${event.title}</h4>
                        <p>${start} - ${end}</p><hr>
                    </div>
                    <div class="event-info">
                        <p>${description}</p>
                    </div>
                </div>
            `);
            $modal.modal('show');
        },
        dayRender: function(date, cell) {
            if (date.isSame(new Date(), "day")) {
                cell.css("background-color", "#e8e7e3"); // Highlight the current date
            }
        },
        eventLimit: true,
        viewRender: function(view, element) {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
});
