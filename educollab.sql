-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2024 at 02:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `educollab`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `event_date` date NOT NULL,
  `end_date` date NOT NULL,
  `added_at` date NOT NULL DEFAULT current_timestamp(),
  `event_type` varchar(100) NOT NULL,
  `year_range` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `end_date`, `added_at`, `event_type`, `year_range`) VALUES
(1, 'event 0', 'test\r\nedit 1\r\nedit 2', '2025-08-25', '2026-08-25', '2024-08-24', 'Holiday', '2025-2026'),
(2, 'event 2', 'test edit', '2024-08-26', '2024-08-26', '2024-08-24', 'Holiday', '2025-2026'),
(11, 'New Year', 'test', '2025-01-01', '2025-01-03', '2024-08-24', 'Holiday', '2025-2026'),
(14, 'add 2 a', 'sy24-25', '2024-10-02', '2024-10-28', '2024-08-27', 'School', '2025-2026'),
(15, 'LEVI', 'edited', '2024-08-30', '2024-08-31', '2024-08-27', 'Holiday', '2024-2025'),
(17, 'EREH', 'ajdnfkajdnf', '2024-10-14', '2024-10-16', '2024-08-28', 'Holiday', '2023-2024'),
(18, 'ETHYL ALCOHOL', 'TEAFESFBH', '2026-08-03', '2026-08-03', '2024-08-28', 'School', '2026-2027'),
(20, 'kfdnkds', 'lollo', '2024-07-09', '2024-08-01', '2024-08-28', 'Holiday', '2027-2028'),
(21, 'sdkgme', 'erge', '2024-08-29', '2024-08-31', '2024-08-28', 'School', '2027-2028'),
(22, '27', 'wiefh', '2024-08-30', '2024-08-31', '2024-08-29', 'Holiday', '2027-2028'),
(23, 'siefjowe', 'wiejewo', '2024-09-04', '2024-09-05', '2024-08-29', 'School', '2023-2024'),
(26, 'notif test', 'sjd', '2024-08-31', '2024-09-01', '2024-08-30', 'School', '2024-2025'),
(27, 'retest', 'dgsgf', '2024-09-01', '2024-09-01', '2024-08-31', 'Others', '2024-2025'),
(28, 'tom', 'xfgh', '2024-09-01', '2024-09-02', '2024-08-31', 'School', '2024-2025'),
(29, 'neww', 'lgfdoklkn', '2024-09-02', '2024-09-02', '2024-09-01', 'School', '2024-2025');

-- --------------------------------------------------------

--
-- Table structure for table `event_types`
--

CREATE TABLE `event_types` (
  `id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `color` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_types`
--

INSERT INTO `event_types` (`id`, `type`, `color`) VALUES
(1, 'Holiday', 'Orange'),
(2, 'School', '#07e328'),
(3, 'Others', '#a6a6a6');

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE `logins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logins`
--

INSERT INTO `logins` (`id`, `user_id`, `login_time`, `success`) VALUES
(1, 4, '2024-08-23 11:45:21', 1),
(2, 4, '2024-08-24 06:48:05', 1),
(3, 4, '2024-08-25 01:50:12', 1),
(4, 4, '2024-08-25 08:37:49', 1),
(5, 4, '2024-08-25 17:17:57', 1),
(6, 4, '2024-08-26 01:45:43', 1),
(7, 4, '2024-08-27 05:13:37', 0),
(8, 4, '2024-08-27 05:13:48', 1),
(9, 4, '2024-08-27 06:42:16', 1),
(10, 4, '2024-08-27 07:06:02', 1),
(11, 4, '2024-08-27 07:07:29', 1),
(12, 4, '2024-08-27 07:12:43', 1),
(13, 4, '2024-08-27 14:45:48', 1),
(14, 4, '2024-08-28 05:16:42', 1),
(15, 6, '2024-08-28 11:09:18', 0),
(16, 10, '2024-08-28 11:09:27', 0),
(17, 7, '2024-08-28 11:09:36', 1),
(18, 7, '2024-08-28 12:13:38', 1),
(19, 7, '2024-08-28 13:51:54', 1),
(20, 4, '2024-08-28 13:52:19', 1),
(21, 4, '2024-08-29 10:33:41', 1),
(22, 4, '2024-08-29 16:40:25', 1),
(23, 7, '2024-08-29 16:54:25', 1),
(24, 7, '2024-08-29 17:11:29', 1),
(25, 4, '2024-08-29 17:11:47', 1),
(26, 7, '2024-08-29 17:18:57', 1),
(27, 7, '2024-08-29 19:21:42', 1),
(28, 7, '2024-08-29 19:22:37', 1),
(29, 7, '2024-08-29 19:36:31', 1),
(30, 4, '2024-08-29 19:43:51', 1),
(31, 4, '2024-08-29 19:54:42', 1),
(32, 7, '2024-08-30 04:25:10', 1),
(33, 7, '2024-08-30 04:25:53', 1),
(34, 4, '2024-08-30 05:40:08', 0),
(35, 4, '2024-08-30 05:40:15', 0),
(36, 4, '2024-08-30 05:40:25', 1),
(37, 4, '2024-08-30 05:44:56', 1),
(38, 4, '2024-08-30 07:23:36', 1),
(39, 4, '2024-08-30 09:40:44', 1),
(40, 7, '2024-08-30 09:41:23', 1),
(41, 4, '2024-08-30 09:42:03', 1),
(42, 4, '2024-08-30 09:44:40', 1),
(43, 4, '2024-08-30 10:08:38', 1),
(44, 4, '2024-08-31 02:12:55', 1),
(45, 7, '2024-08-31 02:30:19', 1),
(46, 4, '2024-08-31 02:31:25', 0),
(47, 4, '2024-08-31 02:31:33', 1),
(48, 7, '2024-08-31 11:19:55', 1),
(49, 4, '2024-08-31 11:22:59', 1),
(50, 7, '2024-08-31 11:24:08', 1),
(51, 4, '2024-08-31 11:43:05', 1),
(52, 4, '2024-08-31 11:46:58', 1),
(53, 7, '2024-08-31 12:00:30', 1),
(54, 4, '2024-08-31 12:00:50', 1),
(55, 4, '2024-08-31 12:41:29', 0),
(56, 4, '2024-08-31 12:41:35', 1),
(57, 4, '2024-08-31 14:37:28', 1),
(58, 7, '2024-08-31 14:43:56', 1),
(59, 7, '2024-08-31 15:11:18', 1),
(60, 4, '2024-08-31 16:04:27', 1),
(61, 7, '2024-08-31 16:33:14', 1),
(62, 4, '2024-09-03 02:31:43', 1),
(63, 4, '2024-09-06 03:12:31', 1),
(64, 4, '2024-09-08 06:26:44', 1),
(65, 4, '2024-09-08 07:03:14', 1),
(66, 4, '2024-09-08 10:01:00', 1),
(67, 4, '2024-09-08 11:16:57', 1),
(68, 4, '2024-09-08 11:18:35', 1),
(69, 4, '2024-09-08 12:41:24', 1),
(70, 4, '2024-09-09 09:20:31', 1),
(71, 4, '2024-09-09 10:54:40', 0),
(72, 4, '2024-09-09 10:54:46', 1),
(73, 4, '2024-09-10 08:01:20', 1),
(74, 4, '2024-09-11 12:02:35', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('info','warning','error','calendar_event') NOT NULL,
  `notif_content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `event_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `notif_content`, `created_at`, `read_at`, `status`, `event_id`) VALUES
(19, NULL, 'calendar_event', 'Tomorrow\'s event: \'Test Event\'.', '2024-08-30 09:28:17', NULL, 'read', 25),
(20, NULL, 'calendar_event', 'Tomorrow\'s event: \'notif test\'.', '2024-08-30 09:28:17', NULL, 'read', 26),
(21, NULL, 'calendar_event', 'Tomorrow\'s event: \'retest\'.', '2024-08-31 02:35:26', NULL, 'read', NULL),
(22, NULL, 'calendar_event', 'Tomorrow\'s event: \'retest\'.', '2024-08-31 02:35:37', NULL, 'read', NULL),
(23, NULL, 'calendar_event', 'Tomorrow\'s event: \'tom\'.', '2024-08-31 11:23:26', NULL, 'read', NULL),
(24, NULL, 'calendar_event', 'Tomorrow\'s event: \'siefjowe\'.', '2024-09-03 02:31:43', NULL, 'read', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sy`
--

CREATE TABLE `sy` (
  `sy_id` int(11) NOT NULL,
  `year_range` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sy`
--

INSERT INTO `sy` (`sy_id`, `year_range`) VALUES
(1, '2024-2025'),
(2, '2025-2026'),
(3, '2023-2024'),
(4, '2026-2027'),
(5, '2027-2028');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `assignedBy` int(11) NOT NULL,
  `assignedTo` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `taskType` enum('private','assigned','public') DEFAULT NULL,
  `tag` enum('Normal','Urgent','Important') DEFAULT NULL,
  `grade` varchar(10) DEFAULT NULL,
  `status` enum('completed','in_progress','pending') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL,
  `due_time` time DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `assignedBy`, `assignedTo`, `title`, `description`, `taskType`, `tag`, `grade`, `status`, `created_at`, `due_date`, `due_time`, `completed_at`) VALUES
(1, 4, 5, 'assigned task 1', '', 'assigned', NULL, NULL, 'pending', '2024-09-06 08:01:43', '2024-09-07', NULL, NULL),
(2, 4, 6, 'assigned task 2', '', 'assigned', NULL, NULL, 'pending', '2024-09-06 08:03:10', '2024-09-07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `gradeLevel` varchar(20) NOT NULL,
  `section` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'inactive',
  `accType` enum('USER','ADMIN') DEFAULT 'USER',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `gradeLevel`, `section`, `password`, `status`, `accType`, `created_at`) VALUES
(4, 'admintest', 'admin', 'test', 'Grade 1', 'test', '$2y$10$AejWqKaXEPWFsJEa0YYEq./SBDNG3htMmHM4NPGw48R5zfvoHOmJ.', 'active', 'ADMIN', '2024-07-26 08:58:37'),
(5, 'usertest', 'user', 'test', 'Grade 2', 'test', '$2y$10$LDw4kSr6WkVv1jZhFFYPvuqqI/c6E4kopojrW/W6YAnYiBVn.mupK', 'active', 'USER', '2024-07-26 09:25:29'),
(6, 'newuser', 'new', 'user', 'Grade 3', 'test', '$2y$10$N75cH127LXgKVYhJQpKo5.aXTUSnRLLgTC8Mo7HRqy.L0dAVsgl5y', 'active', 'USER', '2024-07-26 09:34:28'),
(7, 'an', 'an', 'an', 'Grade 4', 'jhgv', '$2y$10$tPAqP5ETQ.F.nCxK8XR2lOIcrFNe/hiTyW9aazNs66zKUIY5/IfNi', 'active', 'USER', '2024-08-24 07:10:44'),
(10, 'user', 'fname', 'lname', 'SNED', 'sec', '$2y$10$OLTI4QId2YXVq8KJUVhJmOeJPm2Du.vWzK9COl4VNyVihAuDMWi9W', 'inactive', 'USER', '2024-08-24 07:20:53'),
(11, 'one', 'one', 'one', 'SNED', 'one', '$2y$10$rBxlKb0aeb5LbO5JSV1.AuDc5EQcv.3CqOlCpZuRFzoft9pdw47we', 'inactive', 'USER', '2024-08-24 07:22:12'),
(12, 'two', 'two', 'two', 'Grade 1', 'two', '$2y$10$s6IG5/bDo1/leNTkyKXtZ.aUWyKsBBxhRnYZfjkBiHHsnItbhsWTm', 'inactive', 'USER', '2024-08-24 07:35:44'),
(13, 'three', 'three', 'three', 'Grade 2', 'three', '$2y$10$Sc.6vo5LpLIPPsWmP.WoEuuYxNH0Gb1YgkjGUxaSsh3EVNVeABpNW', 'inactive', 'USER', '2024-08-24 07:39:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sy` (`year_range`);

--
-- Indexes for table `event_types`
--
ALTER TABLE `event_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logins`
--
ALTER TABLE `logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sy`
--
ALTER TABLE `sy`
  ADD PRIMARY KEY (`sy_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignedBy` (`assignedBy`),
  ADD KEY `assignedTo` (`assignedTo`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `event_types`
--
ALTER TABLE `event_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `logins`
--
ALTER TABLE `logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sy`
--
ALTER TABLE `sy`
  MODIFY `sy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`);

--
-- Constraints for table `logins`
--
ALTER TABLE `logins`
  ADD CONSTRAINT `logins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`assignedBy`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`assignedTo`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
