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
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text(); // Get response as text first
            })
            .then(text => {
                console.log('Raw response text:', text); // Log the raw response text
                try {
                    const data = JSON.parse(text); // Attempt to parse JSON
                    const notifications = data.notifications;
                    const notificationList = document.querySelector('.notification-list');
    
                    if (!isLoadMore) {
                        notificationList.innerHTML = ''; // Clear notifications if not loading more
                    }
    
                    if (Array.isArray(notifications)) {
                        notifications.forEach(notification => {
                            const li = document.createElement('li');
                            li.textContent = notification.notif_content;
                            notificationList.appendChild(li);
                        });
    
                        // Show or hide the "See More..." button based on the number of notifications returned
                        if (notifications.length === limit) {
                            document.querySelector('.see-more').style.display = 'block';
                        } else {
                            document.querySelector('.see-more').style.display = 'none';
                        }
    
                        // Update notification count
                        const notificationCount = notificationList.querySelectorAll('li').length;
                        document.querySelector('.notification-count').textContent = notificationCount;
    
                    } else if (notifications.message) {
                        document.querySelector('.notification-count').textContent = '0';
                        const li = document.createElement('li');
                        li.textContent = notifications.message;
                        notificationList.appendChild(li);
                        document.querySelector('.see-more').style.display = 'none';
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }
    
    

    // Initial fetch to display the count
    fetchNotifications();
});
