-- MySQL dump for Portfolio CMS with RBAC
-- Updated schema with user, pages, and permissions tables

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- =====================================================
-- TABLE: users
-- Description: Store user credentials with encrypted passwords
-- =====================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100),
  `full_name` VARCHAR(255),
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- TABLE: pages
-- Description: Store all CMS pages with their metadata
-- =====================================================
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `page_code` VARCHAR(50) NOT NULL UNIQUE,
  `page_name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `url` VARCHAR(255),
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- TABLE: permissions
-- Description: Define individual permission actions
-- =====================================================
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `permission_code` VARCHAR(100) NOT NULL UNIQUE,
  `permission_name` VARCHAR(255) NOT NULL,
  `permission_type` ENUM('CREATE', 'READ', 'UPDATE', 'DELETE') NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- TABLE: user_permissions
-- Description: Map users to pages and their permissions (CRUD)
-- =====================================================
DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE `user_permissions` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `page_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  `granted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `granted_by` INT,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_page_perm` (`user_id`, `page_id`, `permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- Insert Default Pages
-- =====================================================
INSERT INTO `pages` (`page_code`, `page_name`, `description`, `url`) VALUES
('projects', 'Projects Management', 'Manage portfolio projects', '/admin/views/projects.php'),
('skills', 'Skills Management', 'Manage skills and proficiency', '/admin/views/my_skills.php'),
('portfolio', 'Portfolio Page', 'View portfolio', '/views/portfolio.php'),
('about', 'About Page', 'View about page', '/views/about.php'),
('home', 'Home Page', 'View home page', '/index.php');

-- =====================================================
-- Insert Default Permissions
-- =====================================================
INSERT INTO `permissions` (`permission_code`, `permission_name`, `permission_type`) VALUES
('add_project', 'Add Project', 'CREATE'),
('view_project', 'View Project', 'READ'),
('edit_project', 'Edit Project', 'UPDATE'),
('delete_project', 'Delete Project', 'DELETE'),
('add_skill', 'Add Skill', 'CREATE'),
('view_skill', 'View Skill', 'READ'),
('edit_skill', 'Edit Skill', 'UPDATE'),
('delete_skill', 'Delete Skill', 'DELETE');

-- =====================================================
-- Insert Default User (Admin)
-- Password: admin123 (bcrypt hashed)
-- =====================================================
INSERT INTO `users` (`username`, `password`, `email`, `full_name`, `is_active`) VALUES
('admin', '$2y$12$E5Qbjc1HvgKCIV60gRXQ8OYWV2.SfVHjmvmoI43VfTrh72jZ6uyaC', 'admin@example.com', 'Administrator', 1);

-- =====================================================
-- Insert Default Permissions for Admin (Full Access)
-- =====================================================
INSERT INTO `user_permissions` (`user_id`, `page_id`, `permission_id`) 
SELECT u.id, p.id, pe.id FROM users u, pages p, permissions pe 
WHERE u.username = 'admin' AND p.page_code IN ('projects', 'skills');

-- =====================================================
-- TABLE: projects (Original table - keep as is)
-- =====================================================
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `period` VARCHAR(100) DEFAULT NULL,
  `category` VARCHAR(100) NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- TABLE: skills (Original table - keep as is)
-- =====================================================
DROP TABLE IF EXISTS `skills`;
CREATE TABLE `skills` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `skill_detail` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `proficiency` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `skills` VALUES 
(1,'Languages','Indonesian','100'),
(2,'Languages','English','100'),
(3,'Languages','Mandarin','45'),
(4,'Coding','HTML','90'),
(5,'Coding','CSS','85'),
(6,'Coding','JavaScript','80'),
(7,'Coding','Java','75'),
(8,'Coding','PHP','65'),
(9,'Coding','Python','90'),
(10,'Coding','Kotlin','55'),
(11,'Coding','MySQL','65'),
(12,'Crafting','Crochet','90'),
(13,'Crafting','Knitting','80'),
(14,'Crafting','Embroidery','75'),
(15,'Organization','Time Management','75'),
(16,'Organization','File Management','80');

/*!40101 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET @OLD_UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
