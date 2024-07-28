// datetime.js
function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: 'long',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true,
        timeZone: 'Asia/Manila'
    };
    const formatter = new Intl.DateTimeFormat('en-GB', options);
    const formattedDateTime = formatter.format(now);
    document.getElementById('datetime').textContent = formattedDateTime;
}

// Update the date and time every second
setInterval(updateDateTime, 1000);

// Initialize the date and time when the page loads
updateDateTime();
