$(document).ready(function() {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,list'
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

            // Add hover effect to show description
            element.hover(function() {
                var description = event.description ? event.description : 'No description available';
                var $tooltip = $('<div class="event-tooltip">' + description + '</div>');
                $('body').append($tooltip);
                $(this).mousemove(function(e) {
                    $tooltip.css({
                        top: e.pageY + 10,
                        left: e.pageX + 10
                    });
                });
            }, function() {
                $('.event-tooltip').remove();
            });
        },
        eventLimit: true
    });
});
