// notification.js

document.querySelector('.notification-bell').addEventListener('click', function() {
    const dropdown = document.querySelector('.notification-dropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    fetchNotifications();
});

function fetchNotifications() {
    fetch('../../src/processes/fetch_notifications.php')
        .then(response => response.json())
        .then(data => {
            const notifications = data.notifications;
            const notificationCount = notifications.length;
            document.querySelector('.notification-count').textContent = notificationCount;

            const notificationList = document.querySelector('.notification-list');
            notificationList.innerHTML = '';
            notifications.forEach(notification => {
                const li = document.createElement('li');
                li.textContent = notification.message;
                notificationList.appendChild(li);
            });
        })
        .catch(error => console.error('Error fetching notifications:', error));
}

// Initial fetch to display the count
fetchNotifications();
