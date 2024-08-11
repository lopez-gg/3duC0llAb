document.addEventListener('DOMContentLoaded', (event) => {
    const addEventButton = document.getElementById('addEventButton');
    const eventContainer = document.getElementById('eventContainer');

    addEventButton.addEventListener('click', () => {
        const newEventForm = document.createElement('div');
        newEventForm.classList.add('event-form');

        newEventForm.innerHTML = `
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" class="form-control" name="title[]" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" name="description[]" required></textarea>
            </div>
            <div class="form-group">
                <label for="start">Start Date:</label>
                <input type="date" class="form-control" name="start[]" required>
            </div>
            <div class="form-group">
                <label for="end">End Date:</label>
                <input type="date" class="form-control" name="end[]" required>
            </div>
            <hr>
        `;

        eventContainer.appendChild(newEventForm);
    });
});
