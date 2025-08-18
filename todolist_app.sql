-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 03, 2025 at 03:24 AM
-- Server version: 8.0.17
-- PHP Version: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `todolist_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `game_players`
--

CREATE TABLE `game_players` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('citizen','odd') DEFAULT 'citizen',
  `tasks` text,
  `has_submitted` tinyint(1) DEFAULT '0',
  `has_voted` tinyint(1) DEFAULT '0',
  `voted_for` int(11) DEFAULT NULL,
  `is_ready` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `game_players`
--

INSERT INTO `game_players` (`id`, `game_id`, `user_id`, `role`, `tasks`, `has_submitted`, `has_voted`, `voted_for`, `is_ready`) VALUES
(1, 1, 2, 'citizen', '[\"\\u0e25\\u0e49\\u0e32\\u0e07\\u0e08\\u0e32\\u0e19\",\"\\u0e16\\u0e39\\u0e1e\\u0e37\\u0e49\\u0e19\",\"\\u0e40\\u0e01\\u0e47\\u0e1a\\u0e02\\u0e2d\\u0e07\"]', 1, 0, NULL, 0),
(2, 1, 4, 'odd', NULL, 0, 0, NULL, 0),
(3, 1, 1, 'citizen', NULL, 0, 0, NULL, 0),
(4, 2, 2, 'citizen', '[\"\\u0e02\\u0e49\\u0e32\\u0e27\",\"\\u0e15\\u0e49\\u0e21\\u0e22\\u0e33\",\"\\u0e44\\u0e01\\u0e48\\u0e17\\u0e2d\\u0e14\"]', 1, 1, 1, 0),
(5, 2, 4, 'citizen', '[\"\\u0e02\\u0e49\\u0e32\\u0e27\\u0e1c\\u0e31\\u0e14\\u0e44\\u0e01\\u0e48\",\"\\u0e02\\u0e49\\u0e32\\u0e27\\u0e1c\\u0e31\\u0e14\\u0e44\\u0e01\\u0e48\",\"\\u0e02\\u0e49\\u0e32\\u0e27\\u0e1c\\u0e31\\u0e14\\u0e44\\u0e01\\u0e48\"]', 1, 1, 2, 0),
(6, 2, 1, 'odd', '[\"\\u0e1e\\u0e31\\u0e14\\u0e25\\u0e21\",\"\\u0e1e\\u0e31\\u0e14\\u0e25\\u0e21\",\"\\u0e1e\\u0e31\\u0e14\\u0e25\\u0e21\"]', 1, 1, 2, 0),
(7, 3, 2, 'citizen', NULL, 0, 0, NULL, 0),
(8, 3, 4, 'citizen', NULL, 0, 0, NULL, 0),
(9, 3, 1, 'citizen', NULL, 0, 0, NULL, 0),
(10, 4, 2, 'citizen', NULL, 0, 0, NULL, 0),
(11, 4, 4, 'citizen', NULL, 0, 0, NULL, 0),
(12, 4, 1, 'citizen', NULL, 0, 0, NULL, 0),
(13, 5, 2, 'citizen', NULL, 0, 0, NULL, 0),
(14, 5, 4, 'citizen', NULL, 0, 0, NULL, 0),
(15, 5, 1, 'citizen', NULL, 0, 0, NULL, 0),
(16, 6, 2, 'odd', NULL, 0, 0, NULL, 0),
(17, 6, 4, 'citizen', NULL, 0, 0, NULL, 0),
(18, 6, 1, 'citizen', NULL, 0, 0, NULL, 0),
(19, 7, 2, 'citizen', NULL, 0, 0, NULL, 1),
(20, 7, 1, 'citizen', NULL, 0, 0, NULL, 0),
(21, 7, 4, 'citizen', NULL, 0, 0, NULL, 0),
(22, 8, 2, 'citizen', NULL, 0, 0, NULL, 1),
(23, 8, 1, 'citizen', NULL, 0, 0, NULL, 0),
(24, 8, 4, 'citizen', NULL, 0, 0, NULL, 0),
(25, 9, 2, 'odd', '[\"\\u0e16\\u0e48\\u0e32\\u0e22\\u0e40\\u0e2d\\u0e01\\u0e2a\\u0e32\\u0e23\",\"\\u0e1e\\u0e34\\u0e17\\u0e1e\\u0e4c\\u0e07\\u0e32\\u0e19\",\"\\u0e19\\u0e2d\\u0e19\"]', 1, 1, 1, 1),
(26, 9, 1, 'citizen', '[\"\\u0e25\\u0e49\\u0e32\\u0e07\\u0e08\\u0e32\\u0e19\",\"\\u0e16\\u0e39\\u0e1e\\u0e37\\u0e49\\u0e19\",\"\\u0e40\\u0e01\\u0e47\\u0e1a\\u0e02\\u0e2d\\u0e07\"]', 1, 1, 2, 1),
(27, 9, 4, 'citizen', '[\"\\u0e25\\u0e49\\u0e32\\u0e07\\u0e08\\u0e32\\u0e19\",\"\\u0e16\\u0e39\\u0e1e\\u0e37\\u0e49\\u0e19\",\"\\u0e40\\u0e01\\u0e47\\u0e1a\\u0e02\\u0e2d\\u0e07\"]', 1, 1, 1, 1),
(28, 10, 2, 'citizen', NULL, 0, 0, NULL, 0),
(29, 10, 1, 'citizen', NULL, 0, 0, NULL, 0),
(30, 10, 4, 'citizen', NULL, 0, 0, NULL, 0),
(31, 11, 4, 'odd', NULL, 0, 0, NULL, 1),
(32, 12, 4, 'citizen', '[\"\\u0e40\\u0e15\\u0e49\\u0e19 \",\"\\u0e23\\u0e49\\u0e2d\\u0e07\\u0e40\\u0e1e\\u0e25\\u0e07\",\"\\u0e27\\u0e48\\u0e32\\u0e22\\u0e19\\u0e49\\u0e33\"]', 1, 1, 2, 1),
(33, 12, 1, 'citizen', '[\"\\u0e40\\u0e15\\u0e49\\u0e19 \",\"\\u0e23\\u0e49\\u0e2d\\u0e07\\u0e40\\u0e1e\\u0e25\\u0e07\",\"\\u0e40\\u0e23\\u0e35\\u0e22\\u0e19\\u0e44\\u0e17\\u0e22\"]', 1, 1, 2, 1),
(34, 12, 2, 'odd', '[\"\\u0e40\\u0e15\\u0e30\\u0e1a\\u0e2d\\u0e25\",\"\\u0e40\\u0e25\\u0e48\\u0e19\\u0e1a\\u0e32\\u0e2a\",\"\\u0e27\\u0e48\\u0e32\\u0e22\\u0e19\\u0e49\\u0e33\"]', 1, 1, 4, 1),
(35, 13, 4, 'citizen', NULL, 0, 0, NULL, 0),
(36, 13, 2, 'citizen', NULL, 0, 0, NULL, 0),
(37, 14, 1, 'citizen', NULL, 0, 0, NULL, 0),
(38, 15, 1, 'citizen', '[\"\\u0e27\\u0e48\\u0e32\\u0e22\\u0e19\\u0e49\\u0e33\",\"\\u0e23\\u0e49\\u0e2d\\u0e07\\u0e40\\u0e1e\\u0e25\\u0e07\",\"\\u0e40\\u0e15\\u0e49\\u0e19\"]', 1, 1, 2, 1),
(39, 15, 2, 'odd', '[\"\\u0e20\\u0e32\\u0e29\\u0e32\\u0e44\\u0e17\\u0e22\",\"\\u0e2d\\u0e31\\u0e07\\u0e01\\u0e24\\u0e29\",\"\\u0e27\\u0e48\\u0e32\\u0e22\\u0e19\\u0e49\\u0e33\"]', 1, 1, 1, 1),
(40, 16, 1, 'citizen', NULL, 0, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `game_rooms`
--

CREATE TABLE `game_rooms` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `pin_code` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('waiting','playing','voting','finished') DEFAULT 'waiting',
  `odd_player_id` int(11) DEFAULT NULL,
  `category_index` int(11) DEFAULT NULL,
  `countdown_started` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `game_rooms`
--

INSERT INTO `game_rooms` (`id`, `room_id`, `pin_code`, `created_at`, `status`, `odd_player_id`, `category_index`, `countdown_started`) VALUES
(1, 25, '282541', '2025-07-30 16:28:42', 'playing', 4, 0, 0),
(2, 26, '964018', '2025-07-30 16:43:29', 'finished', 1, 2, 0),
(3, 26, '964018', '2025-07-30 16:52:38', 'waiting', NULL, NULL, 0),
(4, 27, '371986', '2025-07-30 16:57:01', 'waiting', NULL, NULL, 0),
(5, 28, '249816', '2025-07-30 17:05:05', 'waiting', NULL, NULL, 0),
(6, 29, '588907', '2025-07-30 17:23:57', 'playing', 2, 1, 0),
(7, 30, '678182', '2025-07-31 16:51:58', 'waiting', NULL, NULL, 0),
(8, 31, '892874', '2025-07-31 17:18:37', 'waiting', NULL, NULL, 0),
(9, 32, '147504', '2025-07-31 17:19:41', 'playing', 2, 1, 0),
(10, 32, '147504', '2025-07-31 17:26:04', 'waiting', NULL, NULL, 0),
(11, 33, '388301', '2025-07-31 17:45:52', 'playing', 4, 0, 0),
(12, 34, '767174', '2025-07-31 17:51:43', 'finished', 2, 1, 0),
(13, 34, '767174', '2025-07-31 17:54:57', 'waiting', NULL, NULL, 0),
(14, 35, '270510', '2025-08-01 02:55:26', 'waiting', NULL, NULL, 0),
(15, 36, '500517', '2025-08-01 04:01:05', 'playing', 2, 2, 0),
(16, 36, '500517', '2025-08-01 04:18:37', 'waiting', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text,
  `sent_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `room_id`, `user_id`, `message`, `sent_at`) VALUES
(1, 1, 1, 'hi', '2025-07-25 13:57:56'),
(2, 1, 1, 'hi', '2025-07-25 13:58:00'),
(3, 1, 2, 'kuy', '2025-07-25 13:59:16'),
(4, 2, 1, 'hi', '2025-07-25 14:05:35'),
(5, 2, 1, 'hi', '2025-07-25 14:06:39'),
(6, 2, 1, 'hi', '2025-07-25 14:06:45'),
(7, 5, 2, 'hi', '2025-07-25 14:09:45'),
(8, 5, 2, 'hi', '2025-07-25 14:10:02'),
(9, 5, 2, 'hi', '2025-07-25 14:10:16'),
(10, 5, 2, 'sawadee', '2025-07-25 14:10:47'),
(11, 5, 2, 'asd', '2025-07-25 14:17:02'),
(12, 5, 1, 'hi', '2025-07-25 14:17:55'),
(13, 6, 2, 'hi', '2025-07-25 14:21:54'),
(14, 6, 2, 'test', '2025-07-25 14:21:58'),
(15, 7, 2, 'hi', '2025-07-25 14:51:05'),
(16, 7, 2, 'tests', '2025-07-25 14:51:18'),
(17, 7, 2, 'sawadee', '2025-07-25 14:52:49'),
(18, 8, 1, 'hi', '2025-07-25 15:22:02'),
(19, 8, 3, 'hut', '2025-07-25 15:38:20'),
(20, 9, 1, '้hi', '2025-07-29 08:45:51'),
(21, 11, 1, 'hi', '2025-07-29 08:57:27'),
(22, 11, 2, 'tset', '2025-07-29 08:58:51'),
(23, 13, 1, 'hi', '2025-07-29 10:38:35'),
(24, 13, 2, 'tests', '2025-07-29 10:39:06'),
(25, 13, 2, 'ฟหก', '2025-07-29 11:25:39'),
(26, 13, 2, '่าสว', '2025-07-29 11:25:43'),
(27, 13, 2, 'ฟหกด', '2025-07-29 11:25:49'),
(28, 13, 2, '่าสว', '2025-07-29 11:25:57'),
(29, 16, 1, 'ken', '2025-07-29 22:13:18'),
(30, 16, 2, 'kuy', '2025-07-29 22:13:58'),
(31, 16, 2, 'ken', '2025-07-29 22:14:09'),
(32, 16, 2, 'asd', '2025-07-29 22:16:20'),
(33, 16, 2, 'asd', '2025-07-29 22:16:22'),
(34, 16, 2, 'asd', '2025-07-29 22:16:24'),
(35, 16, 1, 'hi', '2025-07-29 22:19:59'),
(36, 16, 2, 'ken', '2025-07-29 22:26:32'),
(37, 16, 1, 'ฟหก', '2025-07-29 22:27:25'),
(38, 16, 2, 'ken', '2025-07-29 22:43:04'),
(39, 17, 1, 'asd', '2025-07-29 22:43:19'),
(40, 17, 1, 'asd', '2025-07-29 22:44:06'),
(41, 17, 2, 'asd', '2025-07-29 22:44:41'),
(42, 17, 2, 'asd', '2025-07-29 22:45:16'),
(43, 17, 2, 'asd', '2025-07-29 22:49:10'),
(44, 17, 2, 'asddada', '2025-07-29 22:49:22'),
(45, 17, 2, 'asd', '2025-07-29 22:49:46'),
(46, 16, 1, 'hi', '2025-07-29 22:56:50'),
(47, 16, 2, 'sawadee kub', '2025-07-29 23:00:08'),
(48, 18, 4, 'hi', '2025-07-29 23:41:59'),
(49, 18, 1, 'sawadee', '2025-07-29 23:42:10'),
(50, 18, 2, 'มีไร', '2025-07-29 23:42:19'),
(51, 25, 2, 'hi', '2025-07-30 23:11:03'),
(52, 25, 4, 'ken', '2025-07-30 23:11:46'),
(53, 29, 2, 'tests', '2025-07-31 00:23:50'),
(54, 30, 2, 'hi', '2025-07-31 23:51:53'),
(55, 34, 1, '้hi', '2025-08-01 00:51:07'),
(56, 34, 1, 'sawadee', '2025-08-01 00:51:15'),
(57, 34, 1, 'เล่นเกมมั้ย', '2025-08-01 00:51:30'),
(58, 34, 2, 'ได้', '2025-08-01 00:51:34'),
(59, 34, 1, 'เข้าเลย', '2025-08-01 00:51:39'),
(60, 35, 4, 'hi', '2025-08-01 09:57:25'),
(61, 36, 1, 'hi', '2025-08-01 11:00:48'),
(62, 36, 2, 'sawadee', '2025-08-01 11:01:01');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text,
  `color` varchar(20) DEFAULT NULL,
  `pattern` varchar(20) DEFAULT NULL,
  `pos_x` int(11) DEFAULT '0',
  `pos_y` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `room_id`, `user_id`, `content`, `color`, `pattern`, `pos_x`, `pos_y`, `created_at`) VALUES
(4, 5, 2, 'asd', 'yellow', '', 81, 113, '2025-07-25 14:16:39'),
(5, 5, 2, 'asd', 'yellow', '', 769, 55, '2025-07-25 14:16:45'),
(7, 5, 1, 'asd', 'yellow', '', 487, 239, '2025-07-25 14:20:24'),
(8, 6, 2, 'asd', 'yellow', '', 244, 16, '2025-07-25 14:49:52'),
(9, 6, 2, 'asd', 'pink', '', 108, 109, '2025-07-25 14:50:05'),
(10, 8, 1, 'asd', 'yellow', '', 55, 15, '2025-07-25 14:57:05'),
(13, 8, 1, 'kuy', 'green', 'pattern-grid', 1105, 59, '2025-07-25 15:24:21'),
(14, 8, 3, 'sad', 'yellow', 'asd', 147, 178, '2025-07-25 15:39:31'),
(16, 11, 2, 'hi', 'yellow', '', 339, 199, '2025-07-29 09:06:43'),
(17, 11, 2, 'ad', 'yellow', '', 584, 232, '2025-07-29 09:06:49');

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `room_id` varchar(10) DEFAULT NULL,
  `user_id` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'waiting'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postits`
--

CREATE TABLE `postits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text,
  `color` varchar(20) NOT NULL,
  `pattern` varchar(20) DEFAULT NULL,
  `pos_x` int(11) NOT NULL,
  `pos_y` int(11) NOT NULL,
  `z_index` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `postits`
--

INSERT INTO `postits` (`id`, `user_id`, `content`, `color`, `pattern`, `pos_x`, `pos_y`, `z_index`, `created_at`) VALUES
(11, 1, 'asd', 'yellow', '', 1644, 84, 27, '2025-07-29 16:20:36'),
(12, 2, 'ต่อยตั้วเอง\n', 'yellow', '', 1675, 134, 17, '2025-07-29 16:22:10'),
(14, 2, '🥲🥲🥲', 'pink', '', 1693, 76, 16, '2025-07-29 16:22:17'),
(16, 4, '', 'yellow', '', 1636, 80, 7, '2025-07-29 16:33:36'),
(17, 1, 'hi', 'yellow', '', 1688, 150, 28, '2025-07-30 02:09:35'),
(18, 1, 'test', 'pink', '', 1381, 61, 31, '2025-07-30 02:44:30'),
(19, 1, 'rser', 'pink', '', 1430, 185, 34, '2025-07-30 02:44:33'),
(20, 1, 'ser', 'orange', '', 1130, 94, 37, '2025-07-30 02:44:36'),
(21, 1, 'ese', 'purple', '', 1217, 210, 40, '2025-07-30 02:44:40'),
(22, 1, 'sawadee\n', 'yellow', '', 1653, 276, 49, '2025-07-30 02:47:17');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `pin_code` varchar(6) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('waiting','playing','voting','results') DEFAULT 'waiting',
  `category_index` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `pin_code`, `created_at`, `status`, `category_index`) VALUES
(1, '929379', '2025-07-25 13:57:42', 'waiting', 0),
(2, '481170', '2025-07-25 14:04:59', 'waiting', 0),
(3, '708360', '2025-07-25 14:07:50', 'waiting', 0),
(4, '254173', '2025-07-25 14:08:18', 'waiting', 0),
(5, '470821', '2025-07-25 14:09:06', 'waiting', 0),
(6, '413358', '2025-07-25 14:21:41', 'waiting', 0),
(7, '741332', '2025-07-25 14:50:44', 'waiting', 0),
(8, '621776', '2025-07-25 14:56:55', 'waiting', 0),
(9, '854045', '2025-07-29 08:45:44', 'waiting', 0),
(10, '512769', '2025-07-29 08:49:53', 'waiting', 0),
(11, '366816', '2025-07-29 08:57:10', 'waiting', 0),
(12, '281920', '2025-07-29 09:26:13', 'waiting', 0),
(13, '237741', '2025-07-29 10:36:47', 'waiting', 0),
(14, '438361', '2025-07-29 21:58:17', 'waiting', 0),
(15, '931440', '2025-07-29 21:59:40', 'waiting', 0),
(16, '699717', '2025-07-29 22:04:09', 'waiting', 0),
(17, '034463', '2025-07-29 22:43:15', 'waiting', 0),
(18, '328842', '2025-07-29 23:39:06', 'waiting', 0),
(19, '732246', '2025-07-30 09:13:34', 'waiting', 0),
(20, '115150', '2025-07-30 09:58:39', 'waiting', 0),
(21, '835116', '2025-07-30 10:00:10', 'waiting', 0),
(22, '223924', '2025-07-30 13:25:03', 'waiting', 0),
(23, '631691', '2025-07-30 22:59:59', 'waiting', 0),
(24, '524369', '2025-07-30 23:07:40', 'waiting', 0),
(25, '282541', '2025-07-30 23:10:36', 'waiting', 0),
(26, '964018', '2025-07-30 23:43:17', 'waiting', 0),
(27, '371986', '2025-07-30 23:56:41', 'waiting', 0),
(28, '249816', '2025-07-31 00:04:54', 'waiting', 0),
(29, '588907', '2025-07-31 00:23:28', 'waiting', 0),
(30, '678182', '2025-07-31 23:51:15', 'waiting', 0),
(31, '892874', '2025-08-01 00:18:29', 'waiting', 0),
(32, '147504', '2025-08-01 00:19:13', 'waiting', 0),
(33, '388301', '2025-08-01 00:45:51', 'waiting', 0),
(34, '767174', '2025-08-01 00:50:48', 'waiting', 0),
(35, '270510', '2025-08-01 09:55:24', 'waiting', 0),
(36, '500517', '2025-08-01 11:00:31', 'waiting', 0),
(37, '318782', '2025-08-01 11:00:38', 'waiting', 0);

-- --------------------------------------------------------

--
-- Table structure for table `room_players`
--

CREATE TABLE `room_players` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `role` enum('citizen','odd') DEFAULT NULL,
  `tasks` text,
  `voted_for` int(11) DEFAULT NULL,
  `is_alive` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room_players`
--

INSERT INTO `room_players` (`id`, `room_id`, `user_id`, `username`, `role`, `tasks`, `voted_for`, `is_alive`) VALUES
(1, 19, 1, 'ken', NULL, NULL, NULL, 1),
(2, 19, 2, 'best', NULL, NULL, NULL, 1),
(3, 19, 4, 'super', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'ken', '$2y$10$6h1DTRFPFGwDw6Wps5tNTebWpOoItrk0EeuV9KK1K.vfSDUIqMe/K'),
(2, 'best', '$2y$10$g80mP85iOs1KjS6ryMpxqetMgQDv8spex1EdX/UN6FpHmPhYwUBvS'),
(3, 'ken1', '$2y$10$59nx8HGNjCFFx8zQLZ.d1.Xb0HUeoOKX76OqBV8oFhNT5l5LJvkUG'),
(4, 'super', '$2y$10$7Ibv68iq5Cr6VlOybHL1JOuE0VL.5NGNotx0yoCn9aQsuhjT4KZRS');

-- --------------------------------------------------------

--
-- Table structure for table `user_rooms`
--

CREATE TABLE `user_rooms` (
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `joined_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_rooms`
--

INSERT INTO `user_rooms` (`user_id`, `room_id`, `joined_at`) VALUES
(1, 1, '2025-07-25 13:57:42'),
(1, 2, '2025-07-25 14:04:59'),
(1, 3, '2025-07-25 14:07:50'),
(1, 5, '2025-07-25 14:17:49'),
(1, 7, '2025-07-25 14:50:44'),
(1, 8, '2025-07-25 14:56:55'),
(1, 9, '2025-07-29 08:45:44'),
(1, 10, '2025-07-29 08:49:53'),
(1, 11, '2025-07-29 08:57:10'),
(1, 12, '2025-07-29 09:26:13'),
(1, 13, '2025-07-29 10:36:47'),
(1, 14, '2025-07-29 21:58:17'),
(1, 15, '2025-07-29 21:59:40'),
(1, 16, '2025-07-29 22:04:09'),
(1, 17, '2025-07-29 22:43:15'),
(1, 18, '2025-07-29 23:40:31'),
(1, 19, '2025-07-30 09:13:34'),
(1, 20, '2025-07-30 09:58:39'),
(1, 21, '2025-07-30 10:00:10'),
(1, 22, '2025-07-30 13:25:03'),
(1, 23, '2025-07-30 22:59:59'),
(1, 24, '2025-07-30 23:09:21'),
(1, 25, '2025-07-30 23:10:36'),
(1, 26, '2025-07-30 23:43:27'),
(1, 27, '2025-07-30 23:56:50'),
(1, 28, '2025-07-31 00:05:03'),
(1, 29, '2025-07-31 00:23:46'),
(1, 30, '2025-07-31 23:51:15'),
(1, 31, '2025-08-01 00:18:36'),
(1, 32, '2025-08-01 00:19:31'),
(1, 34, '2025-08-01 00:50:57'),
(1, 35, '2025-08-01 09:55:24'),
(1, 36, '2025-08-01 11:00:31'),
(2, 1, '2025-07-25 13:58:51'),
(2, 4, '2025-07-25 14:08:18'),
(2, 5, '2025-07-25 14:09:06'),
(2, 6, '2025-07-25 14:21:41'),
(2, 7, '2025-07-25 14:51:00'),
(2, 11, '2025-07-29 08:58:45'),
(2, 13, '2025-07-29 10:39:00'),
(2, 16, '2025-07-29 22:13:53'),
(2, 17, '2025-07-29 22:44:36'),
(2, 18, '2025-07-29 23:41:11'),
(2, 19, '2025-07-30 09:14:16'),
(2, 23, '2025-07-30 23:00:40'),
(2, 24, '2025-07-30 23:07:40'),
(2, 25, '2025-07-30 23:10:59'),
(2, 26, '2025-07-30 23:43:17'),
(2, 27, '2025-07-30 23:56:41'),
(2, 28, '2025-07-31 00:04:54'),
(2, 29, '2025-07-31 00:23:28'),
(2, 30, '2025-07-31 23:51:49'),
(2, 31, '2025-08-01 00:18:29'),
(2, 32, '2025-08-01 00:19:13'),
(2, 34, '2025-08-01 00:51:01'),
(2, 35, '2025-08-01 09:57:21'),
(2, 36, '2025-08-01 11:00:45'),
(2, 37, '2025-08-01 11:00:38'),
(3, 8, '2025-07-25 15:38:13'),
(4, 18, '2025-07-29 23:39:06'),
(4, 19, '2025-07-30 09:35:45'),
(4, 25, '2025-07-30 23:11:42'),
(4, 26, '2025-07-30 23:43:24'),
(4, 27, '2025-07-30 23:56:50'),
(4, 28, '2025-07-31 00:05:01'),
(4, 29, '2025-07-31 00:23:41'),
(4, 30, '2025-07-31 23:52:54'),
(4, 31, '2025-08-01 00:18:36'),
(4, 32, '2025-08-01 00:19:32'),
(4, 33, '2025-08-01 00:45:51'),
(4, 34, '2025-08-01 00:50:48'),
(4, 35, '2025-08-01 09:57:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `game_players`
--
ALTER TABLE `game_players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `game_rooms`
--
ALTER TABLE `game_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `postits`
--
ALTER TABLE `postits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pin_code` (`pin_code`);

--
-- Indexes for table `room_players`
--
ALTER TABLE `room_players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_rooms`
--
ALTER TABLE `user_rooms`
  ADD PRIMARY KEY (`user_id`,`room_id`),
  ADD KEY `room_id` (`room_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `game_players`
--
ALTER TABLE `game_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `game_rooms`
--
ALTER TABLE `game_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `postits`
--
ALTER TABLE `postits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `room_players`
--
ALTER TABLE `room_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `game_players`
--
ALTER TABLE `game_players`
  ADD CONSTRAINT `game_players_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game_rooms` (`id`),
  ADD CONSTRAINT `game_players_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `game_rooms`
--
ALTER TABLE `game_rooms`
  ADD CONSTRAINT `game_rooms_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `postits`
--
ALTER TABLE `postits`
  ADD CONSTRAINT `postits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_rooms`
--
ALTER TABLE `user_rooms`
  ADD CONSTRAINT `user_rooms_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_rooms_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
