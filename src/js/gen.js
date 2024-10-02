
// for DOM load
document.addEventListener('DOMContentLoaded', function () {
    // Ensure the elements exist before manipulating their styles
    var loadingMessage = document.getElementById('loading-message');
    var mainContent = document.getElementById('page-main-container');
    
    // If both elements are found, hide the loading message and show the main content
    if (loadingMessage && mainContent) {
        loadingMessage.style.display = 'none';
        mainContent.style.display = 'block';
    } else {
        console.error('Elements not found in the DOM');
    }
});


