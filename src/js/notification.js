document.querySelector('.notification-bell').addEventListener('click', function() {
    const dropdown = document.querySelector('.notification-dropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    fetchNotifications();
});

function fetchNotifications() {
    fetch('../../src/processes/fetch_notifications.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json(); // Parse response as JSON
        })
        .then(data => {
            const notifications = data.notifications;
            const notificationList = document.querySelector('.notification-list');
            notificationList.innerHTML = '';

            if (Array.isArray(notifications)) {
                // Display notifications
                const notificationCount = notifications.length;
                document.querySelector('.notification-count').textContent = notificationCount;

                notifications.forEach(notification => {
                    const li = document.createElement('li');
                    li.textContent = notification.notif_content;
                    notificationList.appendChild(li);
                });

                // Mark notifications as read
                markNotificationsAsRead();
            } else if (notifications.message) {
                // Display the "No recent notifications" message
                document.querySelector('.notification-count').textContent = '0';
                const li = document.createElement('li');
                li.textContent = notifications.message;
                notificationList.appendChild(li);
            }
        })
        .catch(error => console.error('Error fetching notifications:', error));
}

function markNotificationsAsRead() {
    fetch('../../src/processes/mark_notifications_read.php', {
        method: 'POST',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
    })
    .catch(error => console.error('Error marking notifications as read:', error));
}

// Initial fetch to display the count
fetchNotifications();
