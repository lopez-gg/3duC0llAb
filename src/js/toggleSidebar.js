function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    sidebar.classList.toggle('hidden');
    content.classList.toggle('full-width');
}
let dropdownTimeout;

// Toggle function for the user profile dropdown
function toggleUserProfileDropdown(event) {
    event.stopPropagation(); // Prevent click event from propagating to the document
    const dropdown = document.getElementById('dropdown');

    // Close message sidebar if it is open
    const sidebar = document.getElementById('message-sidebar');
    if (sidebar.classList.contains('open')) {
        sidebar.classList.remove('open');
        document.getElementById('message-icon').classList.remove('active'); // Remove active class from message icon
    }

    // Close notification dropdown if it is open
    const notificationDropdown = document.querySelector('.notification-dropdown');
    if (notificationDropdown && notificationDropdown.style.display === 'block') {
        notificationDropdown.style.display = 'none';
        document.querySelector('.notification-bell').classList.remove('active'); // Remove active class from notification bell
    }

    // Toggle the user profile dropdown
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

// Function to hide the dropdown after a timeout
function hideDropdown() {
    const dropdown = document.getElementById('dropdown');
    dropdownTimeout = setTimeout(() => {
        dropdown.style.display = 'none';
    }, 300);
}

// Function to cancel the hiding of the dropdown
function cancelHideDropdown() {
    clearTimeout(dropdownTimeout);
}

// Close dropdown if clicking outside
document.addEventListener('click', function (event) {
    const userProfile = document.getElementById('userProfile');
    const dropdown = document.getElementById('dropdown');

    if (!userProfile.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});
