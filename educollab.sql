-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2024 at 07:47 PM
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
-- Table structure for table `archived_tasks`
--

CREATE TABLE `archived_tasks` (
  `id` int(11) NOT NULL,
  `assignedBy` int(11) DEFAULT NULL,
  `assignedTo` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `taskType` enum('private','assigned','public') DEFAULT NULL,
  `tag` enum('Normal','Urgent','Important','Urgent and Important') DEFAULT 'Normal',
  `grade` varchar(10) DEFAULT NULL,
  `progress` enum('completed','in_progress','pending') DEFAULT 'pending',
  `status` enum('archived','deactivated') DEFAULT 'deactivated',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL,
  `due_time` time DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archived_tasks`
--

INSERT INTO `archived_tasks` (`id`, `assignedBy`, `assignedTo`, `title`, `description`, `taskType`, `tag`, `grade`, `progress`, `status`, `created_at`, `due_date`, `due_time`, `completed_at`, `deleted_at`) VALUES
(3, 4, 10, 'new', 'jhb', 'assigned', 'Urgent', 'sned', 'pending', '', '2024-09-13 13:22:27', '2024-09-26', NULL, NULL, '2024-09-14 07:19:14');

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
  `year_range` varchar(9) NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `end_date`, `added_at`, `event_type`, `year_range`, `updated_at`) VALUES
(1, 'event 0', 'test\r\nedit 1\r\nedit 2', '2025-08-25', '2026-08-25', '2024-08-24', 'Holiday', '2025-2026', NULL),
(2, 'event 2', 'test edit', '2024-08-26', '2024-08-26', '2024-08-24', 'Holiday', '2025-2026', NULL),
(11, 'New Year', 'test', '2025-01-01', '2025-01-03', '2024-08-24', 'Holiday', '2025-2026', NULL),
(14, 'add 2 a', 'sy24-25', '2024-10-02', '2024-10-28', '2024-08-27', 'School', '2025-2026', NULL),
(17, 'EREH', 'ajdnfkajdnf', '2024-10-14', '2024-10-16', '2024-08-28', 'Holiday', '2023-2024', NULL),
(18, 'ETHYL ALCOHOL', 'TEAFESFBH', '2026-08-03', '2026-08-03', '2024-08-28', 'School', '2026-2027', NULL),
(20, 'kfdnkds', 'lollo', '2024-07-09', '2024-08-01', '2024-08-28', 'Holiday', '2027-2028', NULL),
(21, 'sdkgme', 'erge', '2024-08-29', '2024-08-31', '2024-08-28', 'School', '2027-2028', NULL),
(22, '27', 'wiefh', '2024-08-30', '2024-08-31', '2024-08-29', 'Holiday', '2027-2028', NULL),
(23, 'siefjowe', 'wiejewo', '2024-09-04', '2024-09-05', '2024-08-29', 'School', '2023-2024', NULL),
(26, 'notif test', 'updateee', '2024-08-31', '2024-09-01', '2024-08-30', 'School', '2024-2025', '2024-09-14 15:57:22'),
(27, 'retest', 'dgsgf', '2024-09-01', '2024-09-01', '2024-08-31', 'Others', '2024-2025', NULL),
(28, 'tom', 'xfgh', '2024-09-01', '2024-09-02', '2024-08-31', 'School', '2024-2025', NULL),
(29, 'neww', 'lgfdoklkn', '2024-09-02', '2024-09-02', '2024-09-01', 'School', '2024-2025', NULL);

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
-- Table structure for table `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(11) NOT NULL,
  `grade` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `grade`, `title`, `content`, `user_id`, `created_at`) VALUES
(6, '1', 'Welcome to Grade 1 Forum', 'This is the first post in the Grade 1 forum. Let\'s start discussing school topics!', 12, '2024-09-16 10:09:48'),
(7, '1', 'Grade 1 Homework Tips', 'Here are some tips to help with your Grade 1 homework: Focus on reading, practice math, and don\'t hesitate to ask for help.', 12, '2024-09-16 10:09:48'),
(8, '2', 'Welcome to Grade 2 Forum', 'Hello Grade 2 students! Feel free to post any questions or start a discussion.', 5, '2024-09-16 10:09:48'),
(9, '3', 'Math Problems in Grade 3', 'I am struggling with some math problems. Does anyone know good resources for practicing addition and subtraction?', 6, '2024-09-16 10:09:48'),
(10, '4', 'Science Project Ideas', 'Does anyone have any cool ideas for a science project? I was thinking about doing something with plants or animals.', 7, '2024-09-16 10:09:48'),
(11, '1', 'Welcome to Grade 1 Forum', 'This is the first post in the Grade 1 forum. Let\'s start discussing school topics!', 12, '2024-09-16 10:58:05'),
(12, '1', 'Grade 1 Homework Tips', 'Here are some tips to help with your Grade 1 homework: Focus on reading, practice math, and don\'t hesitate to ask for help.', 12, '2024-09-16 10:58:05'),
(13, '1', 'What is your favorite subject?', 'I love reading, but I want to know what everyone\'s favorite subject is in Grade 1.', 12, '2024-09-16 10:58:05'),
(14, '1', 'Grade 1 Math Problems', 'Anyone having trouble with today\'s math assignment? I need help with addition and subtraction.', 12, '2024-09-16 10:58:05'),
(15, '1', 'Reading Practice Tips', 'Let\'s share tips on how to improve reading skills. I practice reading out loud every day!', 12, '2024-09-16 10:58:05'),
(16, '1', 'Fun Science Experiments', 'I found some fun science experiments we can try at home! Let\'s discuss what experiments everyone likes.', 12, '2024-09-16 10:58:05'),
(17, '1', 'Grade 1 Spelling Bee', 'Are we ready for the Grade 1 spelling bee? Let\'s practice some words here!', 12, '2024-09-16 10:58:05'),
(18, '1', 'Art Class Projects', 'What projects are everyone working on in art class? I made a drawing of a tree yesterday!', 12, '2024-09-16 10:58:05'),
(19, '1', 'Recess Fun', 'What games do you like to play during recess? I love playing tag with my friends!', 12, '2024-09-16 10:58:05'),
(20, '1', 'Grade 1 Music Class', 'What songs have we learned in music class? I love singing the new songs we learned!', 12, '2024-09-16 10:58:05'),
(21, '1', 'Show and Tell Ideas', 'What is everyone bringing for show and tell? I have a cool toy I want to show everyone.', 12, '2024-09-16 10:58:05'),
(22, '1', 'Field Trip Excitement', 'I can’t wait for the field trip! Where is everyone most excited to visit?', 12, '2024-09-16 10:58:05'),
(23, '1', 'Favorite Story Books', 'What is your favorite storybook? I love reading about adventures with animals.', 12, '2024-09-16 10:58:05'),
(24, '1', 'Learning to Count', 'I’m having fun learning how to count! What numbers are we working on today?', 12, '2024-09-16 10:58:05'),
(25, '1', 'Grade 1 Class Rules', 'Does anyone have questions about our classroom rules? Let\'s go over them together!', 12, '2024-09-16 10:58:05'),
(26, '1', 'Learning New Words', 'Every day we are learning new words! What words did you learn today?', 12, '2024-09-16 10:58:05'),
(27, '1', 'Indoor Recess Ideas', 'What can we do when we have to stay inside during recess? Let\'s come up with some fun ideas!', 12, '2024-09-16 10:58:05'),
(28, '1', 'Grade 1 Reading Challenge', 'Who\'s participating in the reading challenge? Let\'s see who can read the most books!', 12, '2024-09-16 10:58:05'),
(29, '1', 'Favorite Animal', 'What is your favorite animal? I love dogs, but I also like learning about lions.', 12, '2024-09-16 10:58:05'),
(30, '1', 'Math Quiz Preparation', 'How is everyone preparing for the upcoming math quiz? Let’s help each other out!', 12, '2024-09-16 10:58:05'),
(31, '1', 'Our Classroom Pet', 'What do you think about our new classroom pet? I think it\'s so cute!', 12, '2024-09-16 10:58:05'),
(32, '1', 'Learning about Shapes', 'I love learning about shapes! What is your favorite shape? Mine is a circle!', 12, '2024-09-16 10:58:05'),
(33, '1', 'Grade 1 Writing Practice', 'What are we practicing in writing class today? I love writing stories!', 12, '2024-09-16 10:58:05'),
(34, '1', 'Story Time Favorites', 'What books do you enjoy the most during story time? I love listening to fairy tales.', 12, '2024-09-16 10:58:05'),
(35, '1', 'Math Games for Fun', 'What math games do you enjoy playing? I think math is fun when we play games!', 12, '2024-09-16 10:58:05'),
(36, '1', 'Grade 1 PE Class', 'What exercises do we do in PE class? I like jumping jacks the most!', 12, '2024-09-16 10:58:05'),
(37, '1', 'Drawing Favorite Animals', 'What animals do you like to draw in art class? I love drawing cats and dogs!', 12, '2024-09-16 10:58:05'),
(38, '1', 'Reading New Books', 'I just got a new book from the library. What book are you reading now?', 12, '2024-09-16 10:58:05'),
(39, '1', 'Practicing Letters', 'How is everyone practicing their letters? Let\'s share some tips!', 12, '2024-09-16 10:58:05'),
(40, '1', 'Playing with Friends', 'Who are your best friends in class? I love playing with everyone at recess!', 12, '2024-09-16 10:58:05'),
(41, '1', 'Learning about Seasons', 'We\'re learning about seasons in science class. What is your favorite season?', 12, '2024-09-16 10:58:05'),
(42, '1', 'New Words in Spelling', 'What new words are we learning for our spelling test? I think the words are getting harder!', 12, '2024-09-16 10:58:05'),
(43, '1', 'Favorite Snack at Lunch', 'What is your favorite snack to eat at lunch? I love apples!', 12, '2024-09-16 10:58:05'),
(44, '1', 'Best Part of the School Day', 'What is your favorite part of the school day? I love art class the most!', 12, '2024-09-16 10:58:05'),
(45, '1', 'Favorite Colors', 'What is everyone\'s favorite color? Mine is blue!', 12, '2024-09-16 10:58:05'),
(46, '1', 'Grade 1 Puzzle Time', 'Who else loves solving puzzles? Let\'s talk about our favorite puzzles!', 12, '2024-09-16 10:58:05'),
(47, '1', 'Practice Counting to 100', 'We\'re practicing counting to 100! How far can you count without stopping?', 12, '2024-09-16 10:58:05'),
(48, '1', 'Fun at the Playground', 'What is your favorite thing to do at the playground? I like climbing the jungle gym!', 12, '2024-09-16 10:58:05'),
(49, '1', 'Favorite Classroom Activity', 'What activity do you enjoy the most in class? I love building blocks!', 12, '2024-09-16 10:58:05'),
(50, '1', 'Grade 1 School Supplies', 'What school supplies do you like to use the most? I love colorful markers!', 12, '2024-09-16 10:58:05'),
(51, '1', 'Learning about the Alphabet', 'How are we learning about the alphabet in class? I\'m enjoying the fun games we play with letters!', 12, '2024-09-16 10:58:05'),
(52, '1', 'Practicing Reading Aloud', 'Who else is practicing reading aloud? I love reading my favorite stories out loud!', 12, '2024-09-16 10:58:05'),
(53, '1', 'Counting with Blocks', 'Who else loves counting with blocks? It makes learning numbers so much fun!', 12, '2024-09-16 10:58:05'),
(54, '1', 'Favorite Part of Science Class', 'What is your favorite part of science class? I love doing experiments!', 12, '2024-09-16 10:58:05'),
(55, '1', 'Math Flashcards Fun', 'Who\'s using flashcards to practice math? I think they\'re really helpful!', 12, '2024-09-16 10:58:05'),
(56, '1', 'Favorite Playground Game', 'What\'s your favorite game to play during recess? I love playing hide and seek!', 12, '2024-09-16 10:58:05'),
(57, '1', 'Grade 1 Art Project Ideas', 'What ideas do you have for our next art project? I think we should make paper animals!', 12, '2024-09-16 10:58:05'),
(58, '1', 'Practicing for the Spelling Bee', 'Who is practicing for the spelling bee? I\'m learning all the words!', 12, '2024-09-16 10:58:05'),
(59, '1', 'Learning New Words Every Day', 'Every day I learn new words. What new words did you learn today?', 12, '2024-09-16 10:58:05'),
(60, '1', 'Our Favorite Books', 'Let\'s talk about our favorite books! What book do you love to read over and over?', 12, '2024-09-16 10:58:05'),
(61, '1', 'Fun at PE Class', 'Who enjoys PE class? I love running and playing sports!', 12, '2024-09-16 10:58:05'),
(62, '1', 'What is your favorite activity?', 'In class, we do a lot of fun activities! What is your favorite one?', 12, '2024-09-16 10:58:05'),
(63, '1', 'Talking about Science', 'Science is so cool! What is your favorite topic to learn about in science class?', 12, '2024-09-16 10:58:05'),
(64, '1', 'Our Favorite Classroom Games', 'What games do we play in the classroom that everyone enjoys? Let\'s talk about them!', 12, '2024-09-16 10:58:05'),
(65, '1', 'Tips for Homework', 'How is everyone doing with their homework? I\'d love to share some tips!', 12, '2024-09-16 10:58:05');

-- --------------------------------------------------------

--
-- Table structure for table `forum_replies`
--

CREATE TABLE `forum_replies` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reply_content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_replies`
--

INSERT INTO `forum_replies` (`id`, `post_id`, `user_id`, `reply_content`, `created_at`, `parent_id`) VALUES
(104, 11, 4, 'aaaaaaaaaaa', '2024-09-17 15:35:46', 0),
(105, 11, 4, 'aaaaaaaaaaaaaaaaa', '2024-09-17 15:35:56', 0),
(106, 11, 4, 'bbbbbbbbbbbbb', '2024-09-17 15:36:52', 0),
(107, 11, 4, 'asd', '2024-09-17 16:10:27', 0),
(108, 11, 4, 'srhjkj', '2024-09-17 16:17:13', 0),
(109, 11, 4, 'srhjkj', '2024-09-17 16:19:16', 0),
(110, 11, 4, 'ey', '2024-09-17 17:14:52', NULL),
(111, 11, 4, 'eyy', '2024-09-17 17:15:07', NULL),
(112, 11, 4, 'yo', '2024-09-17 17:15:42', NULL),
(113, 12, 4, 'jjj', '2024-09-17 17:18:27', NULL),
(114, 12, 4, 'kkk', '2024-09-17 17:18:49', NULL),
(115, 12, 4, 'jhlkh\r\nhjvb,\r\nkjbhl\r\nhvmv', '2024-09-17 17:31:41', NULL),
(116, 12, 4, 'fefkmd', '2024-09-17 17:35:16', NULL),
(117, 12, 4, 'eyyy', '2024-09-17 17:41:06', 115),
(118, 12, 4, 'yooo', '2024-09-17 17:41:21', 117),
(119, 12, 4, 'heee', '2024-09-17 17:41:40', 115),
(120, 12, 4, 'wiws', '2024-09-17 17:43:24', 115);

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
(74, 4, '2024-09-11 12:02:35', 1),
(75, 4, '2024-09-13 13:19:12', 1),
(76, 4, '2024-09-14 04:56:48', 1),
(77, 4, '2024-09-14 11:11:01', 1),
(78, 4, '2024-09-14 13:13:30', 1),
(79, 4, '2024-09-15 06:00:49', 1),
(80, 4, '2024-09-16 04:21:15', 1),
(81, 4, '2024-09-16 10:28:00', 1),
(82, 4, '2024-09-17 07:53:01', 1),
(83, 4, '2024-09-17 15:11:24', 1),
(84, 4, '2024-09-17 15:45:19', 1);

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
  `progress` enum('completed','in_progress','pending') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL,
  `due_time` time DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `assignedBy`, `assignedTo`, `title`, `description`, `taskType`, `tag`, `grade`, `progress`, `created_at`, `due_date`, `due_time`, `completed_at`, `updated_at`) VALUES
(1, 4, 5, 'assigned task 1', '', 'assigned', 'Normal', '2', 'pending', '2024-09-06 08:01:43', '2024-09-07', NULL, NULL, NULL),
(2, 4, 6, 'assigned task 2', 'dfsd', 'assigned', 'Normal', '1', 'pending', '2024-09-06 08:03:10', '2024-09-07', NULL, NULL, NULL),
(4, 4, 10, 'sned task 2ggggggg', 'ggggggggggggggggg', 'assigned', 'Normal', 'sned', 'in_progress', '2024-09-14 05:01:56', '2024-09-28', '21:24:00', NULL, '2024-09-15 17:54:21'),
(5, 4, 11, 'sned tas 3', 'jhv', 'assigned', 'Important', 'SNED', 'pending', '2024-09-14 14:37:18', '2024-09-19', '14:30:00', NULL, NULL),
(7, 4, 11, 'grade 5 task', 'sample', 'assigned', 'Normal', '5', 'pending', '2024-09-15 06:02:39', '2024-09-27', '11:30:00', NULL, '2024-09-15 12:16:21'),
(8, 4, 11, 'grade 5 task 2', 'test', 'assigned', 'Important', '5', 'pending', '2024-09-15 06:03:41', '2024-09-25', '13:30:00', NULL, '2024-09-15 12:17:18');

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
(11, 'one', 'one', 'one', 'Grade 5', 'one', '$2y$10$rBxlKb0aeb5LbO5JSV1.AuDc5EQcv.3CqOlCpZuRFzoft9pdw47we', 'inactive', 'USER', '2024-08-24 07:22:12'),
(12, 'two', 'two', 'two', 'Grade 1', 'two', '$2y$10$s6IG5/bDo1/leNTkyKXtZ.aUWyKsBBxhRnYZfjkBiHHsnItbhsWTm', 'inactive', 'USER', '2024-08-24 07:35:44'),
(13, 'three', 'three', 'three', 'Grade 2', 'three', '$2y$10$Sc.6vo5LpLIPPsWmP.WoEuuYxNH0Gb1YgkjGUxaSsh3EVNVeABpNW', 'inactive', 'USER', '2024-08-24 07:39:11'),
(14, 'heheh', 'hehehe', 'hehehe', 'Grade 5', 'Subject Teacher', '$2y$10$Bi0zGBR9ghFs5BpR9qWx..Zb.oCub/v28x7ifnHczE9BaUQW6STsS', 'inactive', 'USER', '2024-09-15 10:58:05'),
(15, 'user A', 'skdn', 'sdf', 'Grade 1', 'Sunflower', '$2y$10$y23XXXprzldk9CzcEHtZkuHZO8xDBXEFx1QqGwtZB3kQ4.E3DtLNK', 'inactive', 'USER', '2024-09-16 11:00:50'),
(16, 'userb', 'dfg', 'erg', 'Grade 1', 'Lemon', '$2y$10$So1p99C2S1PtCe9U1WS0Du3aMs97ciiyH6OvQbcrkkSMjlgo./nOe', 'inactive', 'USER', '2024-09-16 11:01:26'),
(17, 'userc', 'mck', 'kxcmv', 'Grade 1', 'Citrus', '$2y$10$5gzjmWO2t40B8VW62y9dcuXw2w0qV9QCPcSPN3D2AMDAug9zBCDLy', 'inactive', 'USER', '2024-09-16 11:01:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archived_tasks`
--
ALTER TABLE `archived_tasks`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `logins`
--
ALTER TABLE `logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `sy`
--
ALTER TABLE `sy`
  MODIFY `sy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD CONSTRAINT `forum_replies_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`assignedBy`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`assignedTo`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
