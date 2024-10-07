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
            month: 'long',   
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
    $('#reminderModal').modal('hide'); 
});

// Close modal if user clicks outside of it (handled by Bootstrap)
$(window).on('click', function(event) {
    const modal = $('#reminderModal');
    if ($(event.target).is(modal)) {
        modal.modal('hide'); 
    }
});


document.addEventListener('DOMContentLoaded', function() {
    fetch('../../src/processes/fetch_reminders.php')
    .then(response => {
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
                <div class="reminder-li" data-reminder-taskId="${reminder.task_id}"> 
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
                event.stopPropagation(); 
                markReminderAsDone(reminder.id); 
            });

            // Add event listener to show reminder details when the reminder is clicked
            reminderItem.addEventListener('click', function() {
                const taskId = this.querySelector('.reminder-li').dataset.reminderTaskid; // Get the task_id from the dataset
                openReminderModal(reminder.reminder_type, taskId); // Pass the task_id to openReminderModal
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
    .then(response => {
        // console.log('Response from fetch_reminders.php:', response);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json(); 
    })
    .then(data => {
        console.log('Response data:', data);  // Log raw data for inspection
        try {
            const parsedData = JSON.parse(data);
            displayReminderModal(parsedData, reminderType);  // Parse and pass the data
        } catch (err) {
            console.error('Error parsing JSON:', err, data);
        }
    })
    .catch(error => console.error('Error marking reminder as done:', error));
}

// New function to handle modal display based on reminder type
function openReminderModal(reminderType, reminderId) { 
    let reminderUrl = '';
    
    if (reminderType === 'task') {
        reminderUrl = '../../src/processes/load_task_details.php';
    } else if (reminderType === 'event') {
        reminderUrl = '../../src/processes/load_event_details.php';
    }

    fetch(reminderUrl + '?id=' + reminderId)  
        .then(response => {
            console.log('Response from openReminderModal:', response);
            console.log('rId: ', reminderId, 'rtype: ', reminderType);
            if (!response.ok) {
                throw new Error('Failed to fetch reminder details');
            }
            return response.json();
        })
        .then(data => {
            displayReminderModal(data, reminderType); 
        })
        .catch(error => console.error('Error fetching reminder details:', error));
}

// Function to display the reminder details inside the modal
function displayReminderModal(reminder, reminderType) {
    const modalTitle = document.getElementById('reminderModalLabel');
    const modalBody = document.getElementById('modal-body');

    console.log('Reminder details from displayReminderModal:', reminder);
    console.log('Reminder type from displayReminderModal:', reminderType);

    // Set modal title or provide a fallback title
    modalTitle.textContent = reminder.title || 'No Title Available';

    // Clear the modal body content first
    modalBody.innerHTML = '';

    // If it's a task reminder, display task details
    if (reminderType === 'task') {
        modalBody.innerHTML = `
            <p><strong>Task Title:</strong> ${reminder.title || 'No Title'}</p>
            <p><strong>Due Date:</strong> ${reminder.due_date ? formatDate(reminder.due_date) : 'No Due Date'}</p>
            <p><strong>Due Time:</strong> ${reminder.due_time ? formatTime(reminder.due_time) : 'No Due Time'}</p>
            <p><strong>Progress:</strong> ${reminder.progress || 'No Progress Info'}</p>
            <p><strong>Assigned To:</strong> ${reminder.assigned_username || 'No Assignee'}</p>
            <p><strong>Assigned By:</strong> ${reminder.assigned_by_username || 'No Info'}</p>
            <p><strong>Grade:</strong> ${reminder.grade ? (reminder.grade === 'SNED' ? 'SNED' : 'Grade ' + reminder.grade) : 'No Grade Info'}</p>
            <p><strong>Description:</strong> ${reminder.description || 'No Description'}</p>
        `;
    }
    // If it's an event reminder, display event details
    else if (reminderType === 'event') {
        modalBody.innerHTML = `
            <p><strong>Event Title:</strong> ${reminder.title || 'No Title'}</p>
            <p><strong>Event Date:</strong> ${reminder.event_date ? formatDate(reminder.event_date) : 'No Event Date'}</p>
            <p><strong>End Date:</strong> ${reminder.end_date ? formatDate(reminder.end_date) : 'No End Date'}</p>
            <p><strong>Description:</strong> ${reminder.description || 'No Description'}</p>
        `;
    } else {
        // Fallback in case the reminder type is unknown
        modalBody.innerHTML = `<p>No details available for this reminder type.</p>`;
    }

    // Show the modal only after the data is populated
    $('#reminderModal').modal('show');
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString(undefined, options);
}

function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const date = new Date();
    date.setHours(hours);
    date.setMinutes(minutes);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}