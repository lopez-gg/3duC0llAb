    <!-- top navigation -->
    <div class="top-nav">
        <div class="left-section">
            <button class="sidebar-toggle-button" onclick="toggleSidebar()">☰</button>
            <div class="app-name">EduCollab</div>
            <div id="datetime">
                <?php echo $currentDateTime; ?>
            </div>
        </div>

        <div class="right-section">
            <div class="msg-message-icon" onclick="toggleMessageSidebar()">
                <i id="message-icon" class="bi bi-chat-right-dots-fill"></i>
                <span class="message-count">0</span>
            </div>
            <!-- Bell icon with notification count -->
            <div class="notification-bell" >
                <i class="bi bi-bell-fill"></i>
                <div id="notification-count" style="display:none;">0</div>
            </div>
            
            <!-- Notification dropdown-->
            <div class="notification-dropdown" style="display:none;">
                <ul class="notification-list"> 
                    <!-- Notifications will be appended here by JavaScript -->
                </ul>
            </div>

            <div class="user-profile" id="userProfile">
                <div class="user-icon" onclick="toggleUserProfileDropdown(event)">U</div>
                <div class="dropdown" id="dropdown" style="display: none;">
                    <form action="../../src/processes/logout.php" method="post">
                        <input type="submit" name="logout" value="Logout">
                    </form>
                </div>
            </div>
        </div>
    </div> 

    <!-- sidebar -->
    <div class="main">
    <div class="sidebar" id="sidebar">
        <div class="logo"></div> 
        <div class="nav-links">
            <a class="<?= getNavState($my_space) ?: '' ?>" href="my_space.php">My Space</a>
            <a class="<?= getNavState($dashb) ?: '' ?>" href="dashboard.php">Dashboard</a>
            <a class="<?= getNavState($calendr) ?: '' ?>" href="calendar.php">Calendar</a>
            <a class="<?= getNavState($gen_forum) ?: '' ?>" href="general_forum.php">General Forum</a>
        </div>
    </div>
    <div id="message-sidebar" class="msg-sidebar">      
        <div class="msg-mini-wind"> 
            <div class="msg-head">
                <button class="btn" onclick="toggleMessageBar()" title="View in full screen::Not Yet Available">
                    <i id="msg-full-scrn-icon" class="bi bi-fullscreen"></i>
                </button>
                <h2>Messages</h2>
            </div>
            <div class="search-container">
                <input type="text" id="search-input" oninput="searchUsers()" placeholder="Search users...">
                <div id="user-list" class="user-list-container">
                    <!-- Search results will be dynamically loaded here -->
                </div>
            </div>

            <div id="message-list">
            <div id="loading-message" style="display: none;">Loading...</div>
                <!-- Dynamically chat lists here -->
            </div>


            
            <div id="chat-interface">
                <!-- Chat message history for the selected user will be loaded here -->
            </div>

            <div id="chat-input-maincon">

            </div>
            
        </div> 

    </div>

