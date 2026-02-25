-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql201.infinityfree.com
-- Generation Time: Feb 25, 2026 at 04:23 PM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41142851_portfolio`
--

-- --------------------------------------------------------

--
-- Table structure for table `migration_log`
--

CREATE TABLE `migration_log` (
  `id` int(11) NOT NULL,
  `migration_file` varchar(255) NOT NULL,
  `executed_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('success','failed') DEFAULT 'success',
  `error_message` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `migration_log`
--

INSERT INTO `migration_log` (`id`, `migration_file`, `executed_at`, `status`, `error_message`) VALUES
(1, '2026-02-14_fix_001_disable_foreign_keys.sql', '2026-02-25 20:00:17', 'success', NULL),
(2, '2026-02-14_fix_002_create_tables.sql', '2026-02-25 20:00:17', 'success', NULL),
(3, '2026-02-14_fix_003_insert_data.sql', '2026-02-25 20:00:17', 'success', NULL),
(4, '2026-02-14_fix_004_grant_admin_permissions.sql', '2026-02-25 20:00:17', 'success', NULL),
(5, '2026-02-14_fix_005_enable_foreign_keys.sql', '2026-02-25 20:00:17', 'success', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `page_code` varchar(50) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `page_code`, `page_name`, `description`, `url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'projects', 'Projects Management', 'Manage portfolio projects', '/admin/views/projects.php', 1, '2026-02-24 18:01:04', '2026-02-24 18:01:04'),
(2, 'skills', 'Skills Management', 'Manage skills and proficiency', '/admin/views/my_skills.php', 1, '2026-02-24 18:01:04', '2026-02-24 18:01:04'),
(3, 'portfolio', 'Portfolio Page', 'View portfolio', '/views/portfolio.php', 1, '2026-02-24 18:01:04', '2026-02-24 18:01:04'),
(4, 'about', 'About Page', 'View about page', '/views/about.php', 1, '2026-02-24 18:01:04', '2026-02-24 18:01:04'),
(5, 'home', 'Home Page', 'View home page', '/index.php', 1, '2026-02-24 18:01:04', '2026-02-24 18:01:04'),
(6, 'users', 'Users', NULL, NULL, 1, '2026-02-24 18:06:00', '2026-02-24 18:06:00'),
(7, 'migrate', 'Migrate', NULL, NULL, 1, '2026-02-24 18:06:00', '2026-02-24 18:06:00');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `permission_code` varchar(100) NOT NULL,
  `permission_name` varchar(255) NOT NULL,
  `permission_type` enum('CREATE','READ','UPDATE','DELETE') NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission_code`, `permission_name`, `permission_type`, `description`, `created_at`) VALUES
(1, 'add_project', 'Add Project', 'CREATE', NULL, '2026-02-24 18:01:04'),
(2, 'view_project', 'View Project', 'READ', NULL, '2026-02-24 18:01:04'),
(3, 'edit_project', 'Edit Project', 'UPDATE', NULL, '2026-02-24 18:01:04'),
(4, 'delete_project', 'Delete Project', 'DELETE', NULL, '2026-02-24 18:01:04'),
(5, 'add_skill', 'Add Skill', 'CREATE', NULL, '2026-02-24 18:01:04'),
(6, 'view_skill', 'View Skill', 'READ', NULL, '2026-02-24 18:01:04'),
(7, 'edit_skill', 'Edit Skill', 'UPDATE', NULL, '2026-02-24 18:01:04'),
(8, 'delete_skill', 'Delete Skill', 'DELETE', NULL, '2026-02-24 18:01:04'),
(9, 'view_user', 'View User', 'READ', NULL, '2026-02-24 18:16:24'),
(10, 'add_user', 'Add User', 'CREATE', NULL, '2026-02-24 18:16:24'),
(11, 'edit_user', 'Edit User', 'UPDATE', NULL, '2026-02-24 18:16:24'),
(12, 'delete_user', 'Delete User', 'DELETE', NULL, '2026-02-24 18:16:24'),
(13, 'view_migrate', 'View Migrate', 'READ', NULL, '2026-02-24 18:16:24'),
(14, 'add_migrate', 'Add Migrate', 'CREATE', NULL, '2026-02-24 18:16:24'),
(15, 'edit_migrate', 'Edit Migrate', 'UPDATE', NULL, '2026-02-24 18:16:24'),
(16, 'delete_migrate', 'Delete Migrate', 'DELETE', NULL, '2026-02-24 18:16:24'),
(17, 'view_users', 'View Users', 'READ', NULL, '2026-02-25 20:00:17'),
(18, 'add_users', 'Add Users', 'CREATE', NULL, '2026-02-25 20:00:17'),
(19, 'edit_users', 'Edit Users', 'UPDATE', NULL, '2026-02-25 20:00:17'),
(20, 'delete_users', 'Delete Users', 'DELETE', NULL, '2026-02-25 20:00:17'),
(21, 'edit_permissions', 'Edit Permissions', 'UPDATE', NULL, '2026-02-25 20:00:17');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `period` varchar(100) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `period`, `category`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Jeans Inventory System', 'Jeans Inventory System', '2025', 'Website', 'project_1772054195_699f66b325502.png', '2026-02-25 21:16:35', '2026-02-25 21:22:13'),
(7, 'Project Sekai Database App', 'Database for Project Sekai Characters made in Android Studio', '2025', 'Mobile App', 'project_1772054589_699f683ddf546.png', '2026-02-25 21:23:10', '2026-02-25 21:23:10');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `skill_detail` varchar(100) DEFAULT NULL,
  `proficiency` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `category`, `skill_detail`, `proficiency`) VALUES
(1, 'Languages', 'Indonesian', '100'),
(2, 'Languages', 'English', '100'),
(3, 'Languages', 'Mandarin', '45'),
(4, 'Coding', 'HTML', '90'),
(5, 'Coding', 'CSS', '85'),
(6, 'Coding', 'JavaScript', '80'),
(7, 'Coding', 'Java', '75'),
(8, 'Coding', 'PHP', '65'),
(9, 'Coding', 'Python', '90'),
(10, 'Coding', 'Kotlin', '55'),
(11, 'Coding', 'MySQL', '65'),
(12, 'Crafting', 'Crochet', '90'),
(13, 'Crafting', 'Knitting', '80'),
(14, 'Crafting', 'Embroidery', '75'),
(15, 'Organization', 'Time Management', '75'),
(16, 'Organization', 'File Management', '80'),
(20, 'Languages', 'Indonesian', '100'),
(21, 'Languages', 'English', '100'),
(22, 'Languages', 'Mandarin', '45'),
(23, 'Coding', 'HTML', '90'),
(24, 'Coding', 'CSS', '85'),
(25, 'Coding', 'JavaScript', '80'),
(26, 'Coding', 'Java', '75'),
(27, 'Coding', 'PHP', '65'),
(28, 'Coding', 'Python', '90'),
(29, 'Coding', 'Kotlin', '55'),
(30, 'Coding', 'MySQL', '65'),
(31, 'Crafting', 'Crochet', '90'),
(32, 'Crafting', 'Knitting', '80'),
(33, 'Crafting', 'Embroidery', '75'),
(34, 'Organization', 'Time Management', '75'),
(35, 'Organization', 'File Management', '80');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` varchar(50) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `is_active`, `created_at`, `updated_at`, `role`) VALUES
(1, 'admin', '$2y$12$E5Qbjc1HvgKCIV60gRXQ8OYWV2.SfVHjmvmoI43VfTrh72jZ6uyaC', 'admin@example.com', 'Administrator', 1, '2026-02-24 18:01:04', '2026-02-25 20:22:53', 'super_admin');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted_at` timestamp NULL DEFAULT current_timestamp(),
  `granted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `page_id`, `permission_id`, `granted_at`, `granted_by`) VALUES
(95, 1, 4, 14, '2026-02-24 18:16:33', 1),
(96, 1, 5, 14, '2026-02-24 18:16:33', 1),
(97, 1, 7, 14, '2026-02-24 18:16:33', 1),
(98, 1, 3, 14, '2026-02-24 18:16:33', 1),
(99, 1, 1, 14, '2026-02-24 18:16:33', 1),
(100, 1, 2, 14, '2026-02-24 18:16:33', 1),
(101, 1, 6, 14, '2026-02-24 18:16:33', 1),
(102, 1, 4, 1, '2026-02-24 18:16:33', 1),
(103, 1, 5, 1, '2026-02-24 18:16:33', 1),
(104, 1, 7, 1, '2026-02-24 18:16:33', 1),
(105, 1, 3, 1, '2026-02-24 18:16:33', 1),
(106, 1, 1, 1, '2026-02-24 18:16:33', 1),
(107, 1, 2, 1, '2026-02-24 18:16:33', 1),
(108, 1, 6, 1, '2026-02-24 18:16:33', 1),
(109, 1, 4, 5, '2026-02-24 18:16:33', 1),
(110, 1, 5, 5, '2026-02-24 18:16:33', 1),
(111, 1, 7, 5, '2026-02-24 18:16:33', 1),
(112, 1, 3, 5, '2026-02-24 18:16:33', 1),
(113, 1, 1, 5, '2026-02-24 18:16:33', 1),
(114, 1, 2, 5, '2026-02-24 18:16:33', 1),
(115, 1, 6, 5, '2026-02-24 18:16:33', 1),
(116, 1, 4, 10, '2026-02-24 18:16:33', 1),
(117, 1, 5, 10, '2026-02-24 18:16:33', 1),
(118, 1, 7, 10, '2026-02-24 18:16:33', 1),
(119, 1, 3, 10, '2026-02-24 18:16:33', 1),
(120, 1, 1, 10, '2026-02-24 18:16:33', 1),
(121, 1, 2, 10, '2026-02-24 18:16:33', 1),
(122, 1, 6, 10, '2026-02-24 18:16:33', 1),
(123, 1, 4, 16, '2026-02-24 18:16:33', 1),
(124, 1, 5, 16, '2026-02-24 18:16:33', 1),
(125, 1, 7, 16, '2026-02-24 18:16:33', 1),
(126, 1, 3, 16, '2026-02-24 18:16:33', 1),
(127, 1, 1, 16, '2026-02-24 18:16:33', 1),
(128, 1, 2, 16, '2026-02-24 18:16:33', 1),
(129, 1, 6, 16, '2026-02-24 18:16:33', 1),
(130, 1, 4, 4, '2026-02-24 18:16:33', 1),
(131, 1, 5, 4, '2026-02-24 18:16:33', 1),
(132, 1, 7, 4, '2026-02-24 18:16:33', 1),
(133, 1, 3, 4, '2026-02-24 18:16:33', 1),
(134, 1, 1, 4, '2026-02-24 18:16:33', 1),
(135, 1, 2, 4, '2026-02-24 18:16:33', 1),
(136, 1, 6, 4, '2026-02-24 18:16:33', 1),
(137, 1, 4, 8, '2026-02-24 18:16:33', 1),
(138, 1, 5, 8, '2026-02-24 18:16:33', 1),
(139, 1, 7, 8, '2026-02-24 18:16:33', 1),
(140, 1, 3, 8, '2026-02-24 18:16:33', 1),
(141, 1, 1, 8, '2026-02-24 18:16:33', 1),
(142, 1, 2, 8, '2026-02-24 18:16:33', 1),
(143, 1, 6, 8, '2026-02-24 18:16:33', 1),
(144, 1, 4, 12, '2026-02-24 18:16:33', 1),
(145, 1, 5, 12, '2026-02-24 18:16:33', 1),
(146, 1, 7, 12, '2026-02-24 18:16:33', 1),
(147, 1, 3, 12, '2026-02-24 18:16:33', 1),
(148, 1, 1, 12, '2026-02-24 18:16:33', 1),
(149, 1, 2, 12, '2026-02-24 18:16:33', 1),
(150, 1, 6, 12, '2026-02-24 18:16:33', 1),
(151, 1, 4, 15, '2026-02-24 18:16:33', 1),
(152, 1, 5, 15, '2026-02-24 18:16:33', 1),
(153, 1, 7, 15, '2026-02-24 18:16:33', 1),
(154, 1, 3, 15, '2026-02-24 18:16:33', 1),
(155, 1, 1, 15, '2026-02-24 18:16:33', 1),
(156, 1, 2, 15, '2026-02-24 18:16:33', 1),
(157, 1, 6, 15, '2026-02-24 18:16:33', 1),
(158, 1, 4, 3, '2026-02-24 18:16:33', 1),
(159, 1, 5, 3, '2026-02-24 18:16:33', 1),
(160, 1, 7, 3, '2026-02-24 18:16:33', 1),
(161, 1, 3, 3, '2026-02-24 18:16:33', 1),
(162, 1, 1, 3, '2026-02-24 18:16:33', 1),
(163, 1, 2, 3, '2026-02-24 18:16:33', 1),
(164, 1, 6, 3, '2026-02-24 18:16:33', 1),
(165, 1, 4, 7, '2026-02-24 18:16:33', 1),
(166, 1, 5, 7, '2026-02-24 18:16:33', 1),
(167, 1, 7, 7, '2026-02-24 18:16:33', 1),
(168, 1, 3, 7, '2026-02-24 18:16:33', 1),
(169, 1, 1, 7, '2026-02-24 18:16:33', 1),
(170, 1, 2, 7, '2026-02-24 18:16:33', 1),
(171, 1, 6, 7, '2026-02-24 18:16:33', 1),
(172, 1, 4, 11, '2026-02-24 18:16:33', 1),
(173, 1, 5, 11, '2026-02-24 18:16:33', 1),
(174, 1, 7, 11, '2026-02-24 18:16:33', 1),
(175, 1, 3, 11, '2026-02-24 18:16:33', 1),
(176, 1, 1, 11, '2026-02-24 18:16:33', 1),
(177, 1, 2, 11, '2026-02-24 18:16:33', 1),
(178, 1, 6, 11, '2026-02-24 18:16:33', 1),
(179, 1, 4, 13, '2026-02-24 18:16:33', 1),
(180, 1, 5, 13, '2026-02-24 18:16:33', 1),
(181, 1, 7, 13, '2026-02-24 18:16:33', 1),
(182, 1, 3, 13, '2026-02-24 18:16:33', 1),
(183, 1, 1, 13, '2026-02-24 18:16:33', 1),
(184, 1, 2, 13, '2026-02-24 18:16:33', 1),
(185, 1, 6, 13, '2026-02-24 18:16:33', 1),
(186, 1, 4, 2, '2026-02-24 18:16:33', 1),
(187, 1, 5, 2, '2026-02-24 18:16:33', 1),
(188, 1, 7, 2, '2026-02-24 18:16:33', 1),
(189, 1, 3, 2, '2026-02-24 18:16:33', 1),
(190, 1, 1, 2, '2026-02-24 18:16:33', 1),
(191, 1, 2, 2, '2026-02-24 18:16:33', 1),
(192, 1, 6, 2, '2026-02-24 18:16:33', 1),
(193, 1, 4, 6, '2026-02-24 18:16:33', 1),
(194, 1, 5, 6, '2026-02-24 18:16:33', 1),
(195, 1, 7, 6, '2026-02-24 18:16:33', 1),
(196, 1, 3, 6, '2026-02-24 18:16:33', 1),
(197, 1, 1, 6, '2026-02-24 18:16:33', 1),
(198, 1, 2, 6, '2026-02-24 18:16:33', 1),
(199, 1, 6, 6, '2026-02-24 18:16:33', 1),
(200, 1, 4, 9, '2026-02-24 18:16:33', 1),
(201, 1, 5, 9, '2026-02-24 18:16:33', 1),
(202, 1, 7, 9, '2026-02-24 18:16:33', 1),
(203, 1, 3, 9, '2026-02-24 18:16:33', 1),
(204, 1, 1, 9, '2026-02-24 18:16:33', 1),
(205, 1, 2, 9, '2026-02-24 18:16:33', 1),
(206, 1, 6, 9, '2026-02-24 18:16:33', 1),
(209, 1, 6, 18, '2026-02-25 20:00:17', NULL),
(210, 1, 6, 20, '2026-02-25 20:00:17', NULL),
(211, 1, 6, 21, '2026-02-25 20:00:17', NULL),
(212, 1, 6, 19, '2026-02-25 20:00:17', NULL),
(213, 1, 6, 17, '2026-02-25 20:00:17', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `migration_log`
--
ALTER TABLE `migration_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `migration_file` (`migration_file`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_code` (`page_code`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_code` (`permission_code`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_page_perm` (`user_id`,`page_id`,`permission_id`),
  ADD KEY `page_id` (`page_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migration_log`
--
ALTER TABLE `migration_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_permissions_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_permissions_ibfk_3` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
