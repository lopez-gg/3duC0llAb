// Event listener to capture when the modal is triggered
$('#setReminderModal').on('show.bs.modal', function (event) {
    // Button that triggered the modal
    var button = $(event.relatedTarget);
    
    // Extract task title and due date from the data-task-title and data-task-due attributes
    var taskTitle = button.data('task-title');
    var taskDueDate = button.data('task-due');
    var taskId = button.data('task-id');
    var utyp = button.data('task-utyp');
    var rtype = button.data('task-rtype');
    
    // Format the due date if it's available
    var formattedDate = 'Not set';
    if (taskDueDate) {
        var date = new Date(taskDueDate);
        formattedDate = date.toLocaleDateString('en-US', {
            year: 'numeric', 
            month: 'long',   // Displays month as a word (e.g., October)
            day: 'numeric'
        });
    }
    
    // Update the modal title and due date
    var modal = $(this);
    modal.find('input[name="id"]').val(taskId);
    modal.find('#taskTitle').text(taskTitle);
    modal.find('#taskDueDate').text(formattedDate);
    modal.find('input[name="utyp"]').val(utyp);
    modal.find('input[name="rtype"]').val(rtype);
});
