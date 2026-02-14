-- Insert data with duplicate handling

-- Insert Default Pages (dengan IGNORE untuk hindari duplikasi)
INSERT IGNORE INTO `pages` (`page_code`, `page_name`, `description`, `url`) VALUES
('projects', 'Projects Management', 'Manage portfolio projects', '/admin/views/projects.php'),
('skills', 'Skills Management', 'Manage skills and proficiency', '/admin/views/my_skills.php'),
('portfolio', 'Portfolio Page', 'View portfolio', '/views/portfolio.php'),
('about', 'About Page', 'View about page', '/views/about.php'),
('home', 'Home Page', 'View home page', '/index.php'),
('users', 'Users Management', 'Manage system users', '/admin/views/users.php');

-- Insert Default Permissions (dengan IGNORE)
INSERT IGNORE INTO `permissions` (`permission_code`, `permission_name`, `permission_type`) VALUES
('add_project', 'Add Project', 'CREATE'),
('view_project', 'View Project', 'READ'),
('edit_project', 'Edit Project', 'UPDATE'),
('delete_project', 'Delete Project', 'DELETE'),
('add_skill', 'Add Skill', 'CREATE'),
('view_skill', 'View Skill', 'READ'),
('edit_skill', 'Edit Skill', 'UPDATE'),
('delete_skill', 'Delete Skill', 'DELETE'),
('view_users', 'View Users', 'READ'),
('add_users', 'Add Users', 'CREATE'),
('edit_users', 'Edit Users', 'UPDATE'),
('delete_users', 'Delete Users', 'DELETE'),
('edit_permissions', 'Edit Permissions', 'UPDATE');

-- Insert Default User (Admin) - hanya jika belum ada
INSERT IGNORE INTO `users` (`username`, `password`, `email`, `full_name`, `is_active`) VALUES
('admin', '$2y$12$E5Qbjc1HvgKCIV60gRXQ8OYWV2.SfVHjmvmoI43VfTrh72jZ6uyaC', 'admin@example.com', 'Administrator', 1);

-- Insert Default Skills Data (dengan IGNORE)
INSERT IGNORE INTO `skills` (`category`, `skill_detail`, `proficiency`) VALUES 
('Languages', 'Indonesian', '100'),
('Languages', 'English', '100'),
('Languages', 'Mandarin', '45'),
('Coding', 'HTML', '90'),
('Coding', 'CSS', '85'),
('Coding', 'JavaScript', '80'),
('Coding', 'Java', '75'),
('Coding', 'PHP', '65'),
('Coding', 'Python', '90'),
('Coding', 'Kotlin', '55'),
('Coding', 'MySQL', '65'),
('Crafting', 'Crochet', '90'),
('Crafting', 'Knitting', '80'),
('Crafting', 'Embroidery', '75'),
('Organization', 'Time Management', '75'),
('Organization', 'File Management', '80');