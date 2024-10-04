document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    const limit = 10;
    let unreadNotificationIds = []; // Array to store unread notification IDs

    const notificationBell = document.querySelector('.notification-bell');
    const dropdown = document.querySelector('.notification-dropdown');

    // Fetch notifications when the page loads
    fetchNotifications();

    // Function to toggle notifications dropdown
    function toggleNotificationsDropdown() {
        const sidebar = document.getElementById('message-sidebar'); // Message sidebar
        const profileMenu = document.getElementById('user-profile-menu'); // Profile menu, if you have one

        // Close message sidebar if it's open
        if (sidebar && sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            document.getElementById('message-icon').classList.remove('active'); // Deactivate message icon
        }

        // Close user profile if it's open
        if (profileMenu && profileMenu.classList.contains('open')) {
            profileMenu.classList.remove('open');
        }

        // Toggle notification dropdown visibility
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none'; // Hide dropdown
            notificationBell.classList.remove('active'); // Deactivate notification icon
            markNotificationsAsRead(); // Mark notifications as read
        } else {
            dropdown.style.display = 'block'; // Show dropdown
            notificationBell.classList.add('active'); // Activate notification icon
            fetchNotifications(); // Fetch notifications
        }
    }

    // Function to fetch notifications
    function fetchNotifications(isLoadMore = false) {
        fetch(`../../src/processes/fetch_notifications.php?limit=${limit}&page=${currentPage}`)
            .then(response => response.json())
            .then(data => {
                const notifications = data.notifications;
                const notificationList = document.querySelector('.notification-list');
                unreadNotificationIds = []; // Reset unread notification IDs

                // Clear previous notifications if not loading more
                if (!isLoadMore) {
                    notificationList.innerHTML = '';
                }

                if (Array.isArray(notifications)) {
                    notifications.forEach(notification => {

                        const li = document.createElement('li');
                        li.classList.add('notification-item');

                        // Add classes based on status
                        switch (notification.status) {
                            case 'unread':
                                li.classList.add('unread');
                                unreadNotificationIds.push(notification.id);
                                break;
                            case 'read':
                                li.classList.add('read');
                                break;
                            case 'past':
                                li.classList.add('read');
                                break;
                        }

                        // Notification content
                        const notificationContent = document.createElement('div');
                        notificationContent.classList.add('notification-content');

                        const title = document.createElement('div');
                        title.classList.add('notification-title');
                        title.textContent = notification.notif_content;

                        const timeAgo = document.createElement('small');
                        timeAgo.classList.add('notification-time');
                        timeAgo.textContent = `(${notification.time_ago})`;

                        // Append to notification item
                        notificationContent.appendChild(title);
                        notificationContent.appendChild(timeAgo);
                        li.appendChild(notificationContent);
                        notificationList.appendChild(li);
                    });

                    // Update notification count
                    const unreadCountElement = document.getElementById('notification-count');
                    if (unreadCountElement) {
                        const unreadCount = unreadNotificationIds.length; // line 94
                        unreadCountElement.textContent = unreadCount > 0 ? `${unreadCount}` : '';
                        unreadCountElement.style.display = unreadCount > 0 ? 'block' : 'none';
                    } else {
                        console.error('Notification count element not found!');
                    }
                } else {
                    // No notifications message
                    const li = document.createElement('li');
                    li.textContent = notifications.message;
                    notificationList.appendChild(li);
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    } //line 109

    // Function to mark notifications as read
    function markNotificationsAsRead() {
        if (unreadNotificationIds.length > 0) {
            fetch('../../src/processes/mark_notifications_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ids: unreadNotificationIds })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh notifications after marking as read
                        fetchNotifications();
                    }
                })
                .catch(error => console.error('Error marking notifications as read:', error));
        }
    }

    // Event listener for notification bell icon
    if (notificationBell) {
        notificationBell.addEventListener('click', toggleNotificationsDropdown);
    }

});
