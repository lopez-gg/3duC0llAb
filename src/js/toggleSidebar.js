let dropdownTimeout;

function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

function hideDropdown() {
    dropdownTimeout = setTimeout(() => {
        document.getElementById('dropdown').style.display = 'none';
    }, 300);
}

function cancelHideDropdown() {
    clearTimeout(dropdownTimeout);
}

document.addEventListener('click', function (event) {
    const userProfile = document.getElementById('userProfile');
    const dropdown = document.getElementById('dropdown');

    if (!userProfile.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    sidebar.classList.toggle('hidden');
    content.classList.toggle('full-width');
}

const dropdown = document.getElementById('dropdown');
dropdown.addEventListener('mouseenter', cancelHideDropdown);
dropdown.addEventListener('mouseleave', hideDropdown);
const userIcon = document.querySelector('.user-icon');
userIcon.addEventListener('mouseenter', cancelHideDropdown);
userIcon.addEventListener('mouseleave', hideDropdown);