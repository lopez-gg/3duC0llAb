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
    var rtypetask = button.data('task-rtypetask');
    
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
    modal.find('input[name="rtypetask"]').val(rtypetask);
});

// Close modal when 'x' is clicked (handled by Bootstrap)
document.querySelector('.close').addEventListener('click', function() {
    $('#reminderModal').modal('hide'); // Use Bootstrap method
});

// Close modal if user clicks outside of it (handled by Bootstrap)
$(window).on('click', function(event) {
    const modal = $('#reminderModal');
    if ($(event.target).is(modal)) {
        modal.modal('hide'); // Use Bootstrap method
    }
});


document.addEventListener('DOMContentLoaded', function() {
    fetch('../../src/processes/fetch_reminders.php')
    .then(response => {
        console.log('Response from fetch_reminders.php:', response);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json(); 
    })
    .then(data => {
        console.log('Parsed data:', data);
        const remindersList = document.querySelector('.reminders-lists');
        remindersList.innerHTML = ''; 

        data.forEach(reminder => {
            const reminderItem = document.createElement('div');
            reminderItem.classList.add('reminder-item');
            reminderItem.innerHTML = `
                <div class="reminder-li">
                    <div class="reminder-li-left">
                        <button type="button" class="btn mark-rem-done" data-reminder-id="${reminder.id}">
                            <i class="bi bi-check-circle"></i>
                        </button>
                    </div>
                    <div class="reminder-li-right">
                        <div class="r-title">Today's reminder for: <strong>${reminder.title}</strong></div>
                        <div class="r-message"><p>${reminder.reminder_message || 'No additional message'}</p></div>
                    </div>
                </div>
            `;

            // Add event listener to the "Mark as done" button
            const markDoneButton = reminderItem.querySelector('.mark-rem-done');
            markDoneButton.addEventListener('click', function(event) {
                event.stopPropagation(); // Prevent triggering the reminder click event
                markReminderAsDone(reminder.id); // Call the function to mark the reminder as done
            });

            remindersList.appendChild(reminderItem);
        });
    })
    .catch(error => console.error('Error fetching reminders:', error));
});

// Mark reminder as done
function markReminderAsDone(reminderId) {
    fetch('../../src/processes/mark_reminder_done.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reminder_id: reminderId })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Reminder marked as done!');
            location.reload(); // Reload the page to update the reminders list
        } else {
            alert('Failed to mark reminder as done.');
        }
    })
    .catch(error => console.error('Error marking reminder as done:', error));
}


// Show reminder details in a modal
function showReminderDetails(reminder) {
    // Construct modal content
    const modalBodyContent = `
        <p><strong>Type:</strong> ${reminder.reminder_type}</p>
        <p><strong>Date:</strong> ${reminder.reminder_date}</p>
        <p><strong>Message:</strong> ${reminder.reminder_message}</p>
        ${reminder.task_title ? `<p><strong>Task:</strong> ${reminder.task_title}</p>` : ''}
        ${reminder.event_title ? `<p><strong>Event:</strong> ${reminder.event_title}</p>` : ''}
    `;

    // Set the content in the modal body
    document.getElementById('modal-body').innerHTML = modalBodyContent;

    // Show the modal using Bootstrap's modal method
    $('#reminderModal').modal('show');
}


