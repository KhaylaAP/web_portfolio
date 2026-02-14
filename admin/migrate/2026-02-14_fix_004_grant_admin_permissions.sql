-- Grant admin permissions safely

-- Dapatkan ID admin
SET @admin_id = (SELECT id FROM users WHERE username = 'admin' LIMIT 1);

-- Grant permissions untuk projects page
INSERT IGNORE INTO user_permissions (user_id, page_id, permission_id)
SELECT @admin_id, p.id, pe.id 
FROM pages p, permissions pe 
WHERE p.page_code = 'projects'
  AND pe.permission_code IN ('add_project', 'view_project', 'edit_project', 'delete_project');

-- Grant permissions untuk skills page
INSERT IGNORE INTO user_permissions (user_id, page_id, permission_id)
SELECT @admin_id, p.id, pe.id 
FROM pages p, permissions pe 
WHERE p.page_code = 'skills'
  AND pe.permission_code IN ('add_skill', 'view_skill', 'edit_skill', 'delete_skill');

-- Grant permissions untuk users page
INSERT IGNORE INTO user_permissions (user_id, page_id, permission_id)
SELECT @admin_id, p.id, pe.id 
FROM pages p, permissions pe 
WHERE p.page_code = 'users'
  AND pe.permission_code IN ('view_users', 'add_users', 'edit_users', 'delete_users', 'edit_permissions');