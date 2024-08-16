function confirmEventDeletion(id) {
    showModal("Confirm Deletion", "Are you sure you want to delete this event?", function() {
        // Code to delete the event
        $.post('../../src/processes/a/delete_event.php', { id: id }, function(response) {
            location.reload(); // Reload the page or handle the UI update
        });
    });
}
