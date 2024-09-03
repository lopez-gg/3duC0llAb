document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1; // Start with page 1
    const limit = 10; // Number of notifications per page

    const notificationBell = document.querySelector('.notification-bell');
    const seeMoreButton = document.querySelector('.see-more');

    if (notificationBell) {
        notificationBell.addEventListener('click', function() {
            const dropdown = document.querySelector('.notification-dropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
            if (dropdown.style.display === 'block') {
                fetchNotifications();
            }
        });
    }

    if (seeMoreButton) {
        seeMoreButton.addEventListener('click', function() {
            currentPage++;
            fetchNotifications(true); // Load more notifications
        });
    }

    function fetchNotifications(isLoadMore = false) {
        fetch(`../../src/processes/fetch_notifications.php?limit=${limit}&page=${currentPage}`)
            .then(response => response.json())
            .then(data => {
                const notifications = data.notifications;
                const notificationList = document.querySelector('.notification-list');
    
                console.log(notifications); // Debug: Check notification data
    
                if (!isLoadMore) {
                    notificationList.innerHTML = ''; // Clear notifications if not loading more
                }
    
                if (Array.isArray(notifications)) {
                    notifications.forEach(notification => {
                        console.log(`Processing notification ID: ${notification.id}, Status: ${notification.status}`); // Debug: Check status
    
                        const li = document.createElement('li');
                        li.classList.add('notification-item');
    
                        // Apply status-based class
                        switch(notification.status) {
                            case 'unread':
                                li.classList.add('unread');
                                break;
                            case 'read':
                                li.classList.add('read');
                                break;
                            case 'past':
                                li.classList.add('past');
                                break;
                        }
    
                        // Create the mark as read icon
                        const markAsReadIcon = document.createElement('i');
                        markAsReadIcon.classList.add('bi', notification.status === 'read' ? 'bi-check-square-fill' : 'bi-check-square');
                        markAsReadIcon.style.cursor = 'pointer';
    
                        markAsReadIcon.addEventListener('click', function() {
                            if (notification.status !== 'read') {
                                markNotificationAsRead(notification.id, markAsReadIcon, li);
                            }
                        });
    
                        // Create a div for the notification content
                        const notificationContent = document.createElement('div');
                        notificationContent.classList.add('notification-content');
    
                        // Create the title and time ago
                        const title = document.createElement('div');
                        title.classList.add('notification-title');
                        title.textContent = notification.notif_content;
    
                        const timeAgo = document.createElement('small');
                        timeAgo.classList.add('notification-time');
                        timeAgo.textContent = `(${notification.time_ago})`;
    
                        // Append elements to the content div
                        notificationContent.appendChild(title);
                        notificationContent.appendChild(timeAgo);
    
                        // Append the icon and content to the list item
                        li.appendChild(markAsReadIcon);
                        li.appendChild(notificationContent);
    
                        notificationList.appendChild(li);
                    });
    
                    // Show or hide the "See More..." button based on the number of notifications returned
                    seeMoreButton.style.display = notifications.length === limit ? 'block' : 'none';
    
                    // Update notification count
                    const notificationCount = notificationList.querySelectorAll('li').length;
                    document.querySelector('.notification-count').textContent = notificationCount;
    
                } else if (notifications.message) {
                    // Display the "No recent notifications" message
                    document.querySelector('.notification-count').textContent = '0';
                    const li = document.createElement('li');
                    li.textContent = notifications.message;
                    notificationList.appendChild(li);
                    seeMoreButton.style.display = 'none';
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }
    

    function markNotificationAsRead(notificationId, icon, li) {
        fetch('../../src/processes/mark_notifications_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: notificationId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the icon and styles
                icon.classList.remove('bi-check-square');
                icon.classList.add('bi-check-square-fill');
                li.classList.remove('unread');
                li.classList.add('read');
            } else {
                console.error('Failed to mark notification as read');
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }

    // Initial fetch to display the notifications
    fetchNotifications();
});
