// <<<<<<<<<<<<<<<<<<<<<< event filtering >>>>>>>>>>>>>>>>>>>>>>>>>

   // Function to open the filter modal
    function openFilterModal() {
        $('#filterRangeModal').modal('show');
    }

    // Function to apply the filter
    function applyFilter() {
        var month = $('#filterMonth').val();
        var year = $('#filterYear').val();

        // Build the filter parameters
        var filterParams = {
            month: month,
            year: year
        };

        // Pass filter parameters to the fetch function
        fetchFilteredEvents(filterParams);

        $('#filterRangeModal').modal('hide');
    }

    // Function to fetch filtered events
    function fetchFilteredEvents(filterParams) {
        $.ajax({
            url: '../processes/a/fetch_manage_events.php',
            type: 'GET',
            data: filterParams,
            success: function(data) {
                // Handle the response and update the events list
                $('#eventList').html(data);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching events:', error);
            }
        });
    }

    // Function to handle dropdown item clicks
    function filterEvents(type) {
        var filterParams = {};
        if (type === 'month') {
            // Add logic for filtering by month
        } else if (type === 'year') {
            // Add logic for filtering by year
        }

        fetchFilteredEvents(filterParams);
    }

