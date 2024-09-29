$(document).on('click', function(event) {
    if (!$(event.target).closest('.search-container').length) {
        hideUserList(); // Hide the list when clicking outside the search container
    }
});

let currentChatUserId = null; // Store the current chat user ID
let pollingInterval; // Variable to store the polling interval ID
    const options = { 
        month: 'short', // Full month name
        day: 'numeric', // Day of the month
        hour: '2-digit', // Two-digit hour
        minute: '2-digit', // Two-digit minutes
        hour12: false // Use 24-hour format (set to true for 12-hour format)
    };
   

function hideUserList() {
    $('#user-list').hide(); // jQuery hide method
}

function showUserList() {
    $('#user-list').show(); // jQuery show method
}

function scrollToBottom() {
    var chatContainer = document.getElementById('chat-history');
    console.log('Scrolling to bottom of:', chatContainer); 
    chatContainer.scrollTop = chatContainer.scrollHeight;
}


function exitPrivateChat(){
    $('#chat-interface').hide();
    $('#chat-input-maincon').hide();

    // clearInterval(pollingInterval); 
    currentChatUserId = null;

    $('.search-container').show();
    $('#message-list').show();
    loadMessageList();
}

function toggleMessageSidebar() {
    const sidebar = document.getElementById('message-sidebar');
    const messageIcon = document.getElementById('message-icon'); 
    
    sidebar.classList.toggle('open');

    if (sidebar.classList.contains('open')) {
        messageIcon.classList.add('active');
        loadMessageList(); // Load messages when sidebar opens
    } else {
        messageIcon.classList.remove('active');
    }
}

function toggleMessageBar() {
    
}

// Function to load the list of recent chats 
function loadMessageList() {
    $.ajax({
        url: '../../src/processes/load_chats.php', 
        type: 'GET',
        success: function(data) {
            const messageList = $('#message-list');
            const chatInterface = $('#chat-interface');
            const chatInput = $('#chat-input-maincon');
        
            // Hide the initial messages list and show the private chat interface

            chatInterface.hide(); 
            chatInput.hide();
            // startPolling();
            let response;

            if (typeof data === 'string') {
                try {
                    response = JSON.parse(data); 
                } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    return;
                }
            } else {
                response = data;
            }

            console.log(response);

            if (response.message) {
                messageList.html(`<div class="no-chats-message">${response.message}</div>`);
            } else {
                messageList.html('');
                response.forEach(chat => {
                    const timestamp = new Date(chat.created_at);
                    const formattedDate = timestamp.toLocaleString('en-US', options).replace(',', ' at');

                    messageList.append(`
                        <div class="message-entry" data-userid="${chat.user_id}" data-username="${chat.chat_username}" data-gradelevel="${chat.gradeLevel}" data-section="${chat.section}">
                              <div class="username">${chat.chat_username}</div>
                <div class="timestamp">${formattedDate}</div>
                            <div class="message-content">${chat.message_text}</div>
                        </div>
                    `);
                });

                // Add click event listener for each message-entry
                $('.message-entry').on('click', function() {
                    const userId = $(this).data('userid');
                    const username = $(this).data('username');
                    const gradeLevel = $(this).data('gradelevel');
                    const section = $(this).data('section');
                    // Assuming you don't have grade level and section here, you can adjust it if needed.
                    selectUser(userId, username, gradeLevel, section); 
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading chats:', error);
        }
    });
}






function searchUsers(isMessaging = false) {
    const searchInput = $('#search-input').val();
    if (searchInput.length < 1) {
        hideUserList();
        return;
    }

    $.ajax({
        url: '../../src/processes/search_users.php',
        type: 'POST',
        data: { query: searchInput, isMessaging: isMessaging }, // Include the identifier
        success: function(data) {
            const userList = $('#user-list');
            userList.empty();
            const response = JSON.parse(data);

            if (response.users && response.users.length > 0) {
                displayUserSearchResults(response.users); // Pass the users array
                showUserList(); // Show user list when new results are loaded
            } else {
                userList.append('<div class="no-users">No users found</div>');
                hideUserList(); // Hide list if no users found
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching users:', error);
        }
    });
}


function displayUserSearchResults(users) {
    const userList = $('#user-list');
    userList.empty();

    users.forEach(user => {
        userList.append(`
            <div class="search-result-item" 
                 data-userid="${user.id}" 
                 data-gradelevel="${user.gradeLevel}" 
                 data-section="${user.section}">
                ${user.username} - ${user.firstname} ${user.lastname} <!-- Display full name -->
            </div>
        `);
    });

    $('.search-result-item').each(function() {
        $(this).on('click', function() {
            const userId = $(this).data('userid');
            const username = $(this).text().split(' - ')[0]; 
            const gradeLevel = $(this).data('gradelevel'); // Get grade level
            const section = $(this).data('section');
            selectUser(userId, username, gradeLevel, section); // Select the user
            hideUserList(); // Hide user list on selecting user
        });
    });
}

function selectUser(userId, username, gradeLevel, section) {
    currentChatUserId = userId;
    const searchContainer = $('.search-container'); 
    const chatInterface = $('#chat-interface');
    const messageListcon = $('#message-list');
    const chatInput = $('#chat-input-maincon');

    // console.log('Selected User ID:', userId);
    // console.log('Selected Username:', username);
    // console.log('Selected Grade Level:', gradeLevel);
    // console.log('Selected Section:', section);

    // Hide the initial messages list and show the private chat interface
    searchContainer.hide();
    messageListcon.hide();
    chatInterface.show(); 
    chatInput.show();
    // startPolling();

        // Build the chat interface for the selected user
    chatInterface.html(`
        <div class="private-chat-header">
            <div class="msg-leftsec">
                <img src="#" class="msg-u-profile-tempo">
                <div class="msg-uinfo">
                    <div class="uname">
                        <span>${username}</span>  
                    </div>                    
                    <p class="msg-sub-info">${gradeLevel} - ${section}</p>
                </div>
                <button type="button" class="x-p-chat" onclick="exitPrivateChat()"> <p>x</p>
                            <i clas="bi bi-fullscreen" style="font-size:25px; color: #fff;"></i>
                </button>
            </div>
        </div>

        <div id="chat-history" class="chat-history">
            <!-- User's chat messages will be dynamically loaded here -->
        </div>
    `);

    chatInput.html(`
       <div class="chat-input-container">
            <textarea id="chat-input" class="chat-input" rows="1" placeholder="Type your message..."></textarea>
            <button class="send-button" onclick="sendMessage(${userId})"><i id="send-icon" class="bi bi-send"></i></button>
        </div> 
    `);
    

    loadPrivateChat(userId); // Load the chat for the selected user
    autoExpandTextArea();
}


function loadPrivateChat(selectedUserId) {
    $.ajax({
        url: '../../src/processes/load_private_chat.php',
        type: 'POST',
        data: { userId: selectedUserId }, // Only send the selected user ID
        success: function(data) {
            // console.log('Raw response:', data); 
            const chatHistory = $('#chat-history');
            chatHistory.html(''); // Clear previous chat messages

            if (data.message) {
                // If no message history, display a message
                chatHistory.html('<div class="no-history-message">' + data.message + '</div>');
            } else {
                // Append each message to the chat history
                data.forEach(message => {
                    // Apply message class based on is_mine flag from server
                    const messageClass = message.is_mine ? 'message-container my-message' : 'message-container their-message';

                    if (messageClass === 'message-container their-message') {
                        chatHistory.append(`
                            <div class="chat-container">
                                <div class="${messageClass}">
                                    <img src="their-icon.png" alt="Their Icon" class="message-icon">
                                    <div class="message">
                                        <span class="chat-timestamp">${message.created_at}</span>
                                        <span class="chat-text">${message.message_text}</span>
                                    </div>
                                </div>
                            </div>
                        `);
                    } else {
                        chatHistory.append(`
                            <div class="chat-container">
                                <div class="${messageClass}">
                                    <div class="message">
                                        <span class="chat-timestamp">${message.created_at}</span>
                                        <span class="chat-text">${message.message_text}</span>
                                    </div>
                                       <img src="their-icon.png" alt="Their Icon" class="message-icon">
                                </div>
                            </div>
                        `);
                    }
                    
                });
                scrollToBottom();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading private chat:', error);
        }
    });
}



function sendMessage(recipientId) {
    const message = document.getElementById('chat-input').value.trim();

    if (message) {
        $.ajax({
            url: '../../src/processes/save_message.php',
            type: 'POST',
            data: { recipientId: recipientId, message: message },
            success: function(response) {
                const jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    const chatHistory = $('#chat-history');
                    chatHistory.append(`
                        <div class="my-message">
                            <span class="chat-text">${message}</span>
                            <span class="chat-timestamp">${new Date().toLocaleTimeString()}</span>
                        </div>
                    `);
                    $('#chat-input').val('');
                    chatHistory.scrollTop(chatHistory[0].scrollHeight); 
                } else {
                    console.error('Error:', jsonResponse.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error sending message:', error);
            }
        });
    }
}





function autoExpandTextArea() {
    const chatInput = document.getElementById('chat-input');
    chatInput.addEventListener('input', function () {
        // Adjust the textarea height based on content (max 5 lines)
        this.style.height = 'auto'; // Reset the height
        this.style.height = Math.min(this.scrollHeight, 150) + 'px'; // 150px = approx. 5 lines
    });
}

function sendMessage(recipientId) {
    const message = document.getElementById('chat-input').value.trim();

    if (message) {
        // Send the message via AJAX to the PHP backend for saving
        $.ajax({
            url: '../../src/processes/save_message.php', // PHP script to save message
            type: 'POST',
            data: { recipientId: recipientId, message: message },
            success: function(response) {
                const chatHistory = $('#chat-history');
                
                // Append the sent message to the chat history
                chatHistory.append(`
                    <div class="my-message">
                        <span class="chat-text">${message}</span>
                        <span class="chat-timestamp">${new Date().toLocaleTimeString()}</span>
                    </div>
                `);
                $('#chat-input').val(''); // Clear the input field

                // Optionally, reload the chat to ensure up-to-date messages
                loadPrivateChat(recipientId);
            },
            error: function(xhr, status, error) {
                console.error('Error sending message:', error);
            }
        });
    }
}




// function startPolling() {
//     // Clear any existing intervals to prevent multiple polling
//     if (pollingInterval) {
//         clearInterval(pollingInterval);
//     }

//     // Set up polling
//     pollingInterval = setInterval(() => {
//         if (currentChatUserId) {
//             loadPrivateChat(currentChatUserId); // Load messages for the current chat
//         }
//     }, 5000); // Poll every 5 seconds
// }


