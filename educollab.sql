-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2024 at 05:46 PM
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
(1, 'event 0', 'test\r\nedit 1', '2024-08-25', '2024-08-25', '2024-08-24', 'type2', '2024-2025'),
(2, 'event 2', 'test', '2024-08-26', '2024-08-26', '2024-08-24', 'Holiday', '2024-2025'),
(7, 'event 3', 'test', '2024-08-26', '2024-08-26', '2024-08-24', 'Holiday', '2023-2024'),
(8, 'event 4', 'test', '2024-08-01', '2024-08-09', '2024-08-24', 'School', '2023-2024'),
(9, 'event 5', 'test', '2024-07-18', '2024-07-31', '2024-08-24', 'Holiday', '2025-2026'),
(11, 'New Year', 'test', '2025-01-01', '2025-01-03', '2024-08-24', 'Holiday', '2025-2026');

-- --------------------------------------------------------

--
-- Table structure for table `event_types`
--

CREATE TABLE `event_types` (
  `id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `color_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_types`
--

INSERT INTO `event_types` (`id`, `type`, `color_id`) VALUES
(1, 'Holiday', 'Orange'),
(2, 'School', 'Green');

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
(2, 4, '2024-08-24 06:48:05', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('info','warning','error') NOT NULL,
  `notif_content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `sy_start` year(4) NOT NULL,
  `sy_end` year(4) NOT NULL,
  `year_range` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sy`
--

INSERT INTO `sy` (`sy_id`, `sy_start`, `sy_end`, `year_range`) VALUES
(1, '2024', '2025', '2024-2025'),
(2, '2025', '2026', '2025-2026'),
(3, '2023', '2024', '2023-2024');

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
  `tag` enum('UI','UNI','NUI','NUNI') DEFAULT NULL,
  `status` enum('completed','in_progress','pending') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(4, 'admintest', 'admin', 'test', 'test', 'test', '$2y$10$AejWqKaXEPWFsJEa0YYEq./SBDNG3htMmHM4NPGw48R5zfvoHOmJ.', 'active', 'ADMIN', '2024-07-26 08:58:37'),
(5, 'usertest', 'user', 'test', 'test', 'test', '$2y$10$LDw4kSr6WkVv1jZhFFYPvuqqI/c6E4kopojrW/W6YAnYiBVn.mupK', 'active', 'USER', '2024-07-26 09:25:29'),
(6, 'newuser', 'new', 'user', 'test', 'test', '$2y$10$N75cH127LXgKVYhJQpKo5.aXTUSnRLLgTC8Mo7HRqy.L0dAVsgl5y', 'active', 'USER', '2024-07-26 09:34:28'),
(7, 'an', 'an', 'an', '1', 'jhgv', '$2y$10$tPAqP5ETQ.F.nCxK8XR2lOIcrFNe/hiTyW9aazNs66zKUIY5/IfNi', 'inactive', 'USER', '2024-08-24 07:10:44'),
(8, 'andg', 'an', 'an', '1', 'jhgv', '$2y$10$xUzBHzu8otBA7LnUnigWcugqCCf5eW3eC2HRgAwhywrNfgrVUXnIi', 'inactive', 'USER', '2024-08-24 07:19:54'),
(9, 'andg', 'an', 'an', '1', 'jhgv', '$2y$10$d7yAU2LteGGIXa7nv5bEYeGBMQIotJmir0QnfLklHm2NboCJuTl3u', 'inactive', 'USER', '2024-08-24 07:20:18'),
(10, 'user', 'fname', 'lname', 'grlvl', 'sec', '$2y$10$OLTI4QId2YXVq8KJUVhJmOeJPm2Du.vWzK9COl4VNyVihAuDMWi9W', 'inactive', 'USER', '2024-08-24 07:20:53'),
(11, 'one', 'one', 'one', 'one', 'one', '$2y$10$rBxlKb0aeb5LbO5JSV1.AuDc5EQcv.3CqOlCpZuRFzoft9pdw47we', 'inactive', 'USER', '2024-08-24 07:22:12'),
(12, 'two', 'two', 'two', 'two', 'two', '$2y$10$s6IG5/bDo1/leNTkyKXtZ.aUWyKsBBxhRnYZfjkBiHHsnItbhsWTm', 'inactive', 'USER', '2024-08-24 07:35:44'),
(13, 'three', 'three', 'three', 'three', 'three', '$2y$10$Sc.6vo5LpLIPPsWmP.WoEuuYxNH0Gb1YgkjGUxaSsh3EVNVeABpNW', 'inactive', 'USER', '2024-08-24 07:39:11');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `event_types`
--
ALTER TABLE `event_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `logins`
--
ALTER TABLE `logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sy`
--
ALTER TABLE `sy`
  MODIFY `sy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
