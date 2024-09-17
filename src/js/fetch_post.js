// fetch_post.js

document.addEventListener('DOMContentLoaded', () => {
    // Handle reply button
    document.querySelectorAll('.reply-button').forEach(button => {
        button.addEventListener('click', function() {
            const replyId = this.getAttribute('data-reply-id');
            const replyUsername = this.getAttribute('data-reply-username');
            const replyContent = this.getAttribute('data-reply-content');
            
            // Update the reply form context
            const replyContext = document.getElementById('reply-context');
            replyContext.innerHTML = `Replying to: <strong>${replyUsername}</strong><br><em>${replyContent.replace(/\n/g, '<br>')}</em>`;
            
            // Set the parent_id for the reply
            document.getElementById('parent_id').value = replyId;

            // Set the action type for the form to reply
            document.getElementById('action_type').value = 'reply';

            // Show the context container and scroll to the reply form
            document.getElementById('reply-context-container').style.display = 'block';
            document.querySelector('.reply-form-container').scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Handle edit button
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function() {
            const replyId = this.getAttribute('data-reply-id');
            const replyContent = this.getAttribute('data-reply-content');

            // Fill the reply form for editing
            document.querySelector('.reply-form-container textarea').value = replyContent;
            document.getElementById('parent_id').value = 'NULL';
            document.getElementById('action_type').value = 'edit';
            document.getElementById('reply_id').value = replyId;

            // Show the reply form
            document.querySelector('.reply-form-container').scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Handle delete button
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            const replyId = this.getAttribute('data-reply-id');
            
            // Show the verification modal
            const verificationModal = new bootstrap.Modal(document.getElementById('verificationModal'));
            document.getElementById('verificationMessage').innerText = 'Are you sure you want to delete this reply?';
            document.getElementById('confirmActionButton').onclick = function() {
                fetch('../../src/processes/delete_reply.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ reply_id: replyId, post_id: document.querySelector('input[name="post_id"]').value })
                }).then(response => response.text())
                .then(data => {
                    window.location.reload();
                });
                verificationModal.hide();
            };
            verificationModal.show();
        });
    });

    // Clear reply context on cancel
    document.getElementById('reply-context-cancel').addEventListener('click', function() {
        document.getElementById('reply-context-container').style.display = 'none';
        document.getElementById('parent_id').value = 'NULL';
        document.getElementById('reply_id').value = '0';
        document.getElementById('action_type').value = 'reply'; // Reset to reply
        document.querySelector('.reply-form-container').scrollIntoView({ behavior: 'smooth' });
    });
});
