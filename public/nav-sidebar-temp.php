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
            <!-- Bell icon with notification count -->
            <div class="notification-bell">
                <i class="bi bi-bell-fill"></i>
                <span class="notification-count">0</span>
            </div>
            
            <!-- Notification dropdown-->
            <div class="notification-dropdown">
                <ul class="notification-list"> 
                    <!-- Notifications will be appended here by JavaScript -->
                </ul>
                <button class="see-more" style="display: none;">See More...</button>
            </div>

            <div class="user-profile" id="userProfile">
                <div class="user-icon" onclick="toggleDropdown()">U</div>
                <div class="dropdown" id="dropdown">
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

