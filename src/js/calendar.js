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
            if (event.description) {
                element.find('.fc-title').append('<br/><span class="fc-description">' + event.description + '</span>');
            }
        },
        eventLimit: true
    });
});
