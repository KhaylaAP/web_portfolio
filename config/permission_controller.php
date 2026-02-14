<?php
/**
 * PermissionController - Handles user permissions and access control
 * Manages RBAC (Role-Based Access Control) for CMS pages and actions
 */

require_once __DIR__ . '/database.php';

class PermissionController {
    private $conn;
    private $user_id;
    private $current_page;
    
    public function __construct() {
        // Only start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Check if user is authenticated
     * Validates user session and cookie
     */
    public function isAuthenticated() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Verify user still exists in database
        $query = "SELECT id FROM users WHERE id = ? AND is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->user_id]);
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Get current user information
     */
    public function getCurrentUser() {
        if (!$this->user_id) {
            return null;
        }
        
        $query = "SELECT id, username, email, full_name FROM users WHERE id = ? AND is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->user_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get page ID by page code
     */
    public function getPageByCode($page_code) {
        $query = "SELECT id, page_code, page_name FROM pages WHERE page_code = ? AND is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$page_code]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all pages for a user with their permissions
     */
    public function getUserPages() {
        if (!$this->user_id) {
            return [];
        }
        
        $query = "
            SELECT DISTINCT p.id, p.page_code, p.page_name, p.description, p.url
            FROM pages p
            INNER JOIN user_permissions up ON p.id = up.page_id
            WHERE up.user_id = ? AND p.is_active = 1
            ORDER BY p.page_name
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->user_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if user has permission for a specific action on a page
     * @param string $permission_code (e.g., 'add_project', 'edit_skill', 'delete_project')
     * @param string $page_code 
     * @return boolean
     */
    public function hasPermission($permission_code, $page_code = null) {
        if (!$this->user_id) {
            return false;
        }
        
        // Build query to check permission
        if ($page_code) {
            $query = "
                SELECT up.id FROM user_permissions up
                INNER JOIN permissions p ON up.permission_id = p.id
                INNER JOIN pages pg ON up.page_id = pg.id
                WHERE up.user_id = ? 
                AND p.permission_code = ? 
                AND pg.page_code = ?
                LIMIT 1
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->user_id, $permission_code, $page_code]);
        } else {
            // Check permission across all pages for user
            $query = "
                SELECT up.id FROM user_permissions up
                INNER JOIN permissions p ON up.permission_id = p.id
                WHERE up.user_id = ? AND p.permission_code = ?
                LIMIT 1
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->user_id, $permission_code]);
        }
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Get all permissions for user on a specific page
     */
    public function getPagePermissions($page_code) {
        if (!$this->user_id) {
            return [];
        }
        
        $query = "
            SELECT p.permission_code, p.permission_name, p.permission_type
            FROM user_permissions up
            INNER JOIN permissions p ON up.permission_id = p.id
            INNER JOIN pages pg ON up.page_id = pg.id
            WHERE up.user_id = ? AND pg.page_code = ?
            ORDER BY p.permission_type
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->user_id, $page_code]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all permission actions for a user on a page (grouped by type)
     * Returns: ['create' => [codes], 'read' => [codes], 'update' => [codes], 'delete' => [codes]]
     */
    public function getPagePermissionsByType($page_code) {
        $permissions = $this->getPagePermissions($page_code);
        
        $grouped = [
            'create' => [],
            'read' => [],
            'update' => [],
            'delete' => []
        ];
        
        foreach ($permissions as $perm) {
            $type = strtolower($perm['permission_type']);
            $grouped[$type][] = $perm['permission_code'];
        }
        
        return $grouped;
    }
    
    /**
     * Grant permission to user
     * @param int $user_id
     * @param string $page_code
     * @param string $permission_code
     */
    public function grantPermission($user_id, $page_code, $permission_code) {
        try {
            // Get page ID
            $pageQuery = "SELECT id FROM pages WHERE page_code = ?";
            $pageStmt = $this->conn->prepare($pageQuery);
            $pageStmt->execute([$page_code]);
            $page = $pageStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$page) {
                return ['success' => false, 'message' => 'Page not found'];
            }
            
            // Get permission ID
            $permQuery = "SELECT id FROM permissions WHERE permission_code = ?";
            $permStmt = $this->conn->prepare($permQuery);
            $permStmt->execute([$permission_code]);
            $perm = $permStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$perm) {
                return ['success' => false, 'message' => 'Permission not found'];
            }
            
            // Check if permission already exists
            $checkQuery = "SELECT id FROM user_permissions WHERE user_id = ? AND page_id = ? AND permission_id = ?";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute([$user_id, $page['id'], $perm['id']]);
            
            if ($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Permission already exists'];
            }
            
            // Insert permission
            $insertQuery = "
                INSERT INTO user_permissions (user_id, page_id, permission_id, granted_by)
                VALUES (?, ?, ?, ?)
            ";
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->execute([$user_id, $page['id'], $perm['id'], $this->user_id]);
            
            return ['success' => true, 'message' => 'Permission granted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Revoke permission from user
     */
    public function revokePermission($user_id, $page_code, $permission_code) {
        try {
            $query = "
                DELETE up FROM user_permissions up
                INNER JOIN pages pg ON up.page_id = pg.id
                INNER JOIN permissions p ON up.permission_id = p.id
                WHERE up.user_id = ? AND pg.page_code = ? AND p.permission_code = ?
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id, $page_code, $permission_code]);
            
            return ['success' => true, 'message' => 'Permission revoked successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get all permissions for a page
     */
    public function getAllPagePermissions($page_code) {
        $query = "
            SELECT up.id, u.username, p.permission_code, p.permission_name, p.permission_type
            FROM user_permissions up
            INNER JOIN users u ON up.user_id = u.id
            INNER JOIN permissions p ON up.permission_id = p.id
            INNER JOIN pages pg ON up.page_id = pg.id
            WHERE pg.page_code = ?
            ORDER BY u.username, p.permission_type
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$page_code]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
