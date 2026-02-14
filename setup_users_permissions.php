<?php
/**
 * Setup script to add Users page and permissions
 * This ensures the admin user has access to the users management page
 */

require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // 1. Add the 'users' page if it doesn't exist
    $check_page = "SELECT id FROM pages WHERE page_code = 'users'";
    $stmt = $conn->query($check_page);
    
    if ($stmt->rowCount() === 0) {
        $insert_page = "INSERT INTO pages (page_code, page_name, description, url) 
                       VALUES ('users', 'Users Management', 'Manage system users and access', '/admin/views/users.php')";
        $conn->exec($insert_page);
        echo "✓ Users page added to database\n";
    } else {
        echo "✓ Users page already exists\n";
    }
    
    // 2. Add user permissions if they don't exist
    $permissions_to_add = [
        ['view_users', 'View Users', 'READ'],
        ['add_users', 'Add Users', 'CREATE'],
        ['edit_users', 'Edit Users', 'UPDATE'],
        ['delete_users', 'Delete Users', 'DELETE']
    ];
    
    foreach ($permissions_to_add as $perm) {
        $check_perm = "SELECT id FROM permissions WHERE permission_code = ?";
        $stmt = $conn->prepare($check_perm);
        $stmt->execute([$perm[0]]);
        
        if ($stmt->rowCount() === 0) {
            $insert_perm = "INSERT INTO permissions (permission_code, permission_name, permission_type) 
                           VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_perm);
            $stmt->execute($perm);
            echo "✓ Permission '{$perm[0]}' added\n";
        } else {
            echo "✓ Permission '{$perm[0]}' already exists\n";
        }
    }
    
    // 3. Grant admin user all users permissions
    $admin_query = "SELECT id FROM users WHERE username = 'admin'";
    $stmt = $conn->query($admin_query);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        $admin_id = $admin['id'];
        
        $page_query = "SELECT id FROM pages WHERE page_code = 'users'";
        $stmt = $conn->query($page_query);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($page) {
            $page_id = $page['id'];
            
            foreach ($permissions_to_add as $perm) {
                $perm_query = "SELECT id FROM permissions WHERE permission_code = ?";
                $stmt = $conn->prepare($perm_query);
                $stmt->execute([$perm[0]]);
                $permission = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($permission) {
                    $check_grant = "SELECT id FROM user_permissions 
                                   WHERE user_id = ? AND page_id = ? AND permission_id = ?";
                    $stmt = $conn->prepare($check_grant);
                    $stmt->execute([$admin_id, $page_id, $permission['id']]);
                    
                    if ($stmt->rowCount() === 0) {
                        $grant_perm = "INSERT INTO user_permissions (user_id, page_id, permission_id) 
                                      VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($grant_perm);
                        $stmt->execute([$admin_id, $page_id, $permission['id']]);
                        echo "✓ Admin granted '{$perm[0]}' permission\n";
                    } else {
                        echo "✓ Admin already has '{$perm[0]}' permission\n";
                    }
                }
            }
        }
    }
    
    echo "\n✅ Users page and permissions setup complete!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
