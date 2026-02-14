<?php
/**
 * Auth Check View - Security and Permission Checker
 * This view should be included in every CMS page
 * - Validates user session and cookies
 * - Checks page permissions
 * - Controls UI visibility based on permissions
 * - Auto-refreshes validation every 10 seconds
 */

// Check if session already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/permission_controller.php';

// Initialize permission controller
$permissionController = new PermissionController();

// Check authentication
if (!$permissionController->isAuthenticated()) {
    // Redirect to login if not authenticated
    session_destroy();
    header('Location: ' . dirname(__DIR__) . '/login.php?expired=1');
    exit;
}

// Get current user
$currentUser = $permissionController->getCurrentUser();

// Determine current page code from the requesting page
// Parse the current file to determine page code
$current_file = basename($_SERVER['PHP_SELF']);
$current_path = $_SERVER['PHP_SELF'];

// Check if this is the migrate page by checking the path
if (strpos($current_path, '/admin/migrate/') !== false) {
    $current_page_code = 'migrate';
} else {
    $page_mappings = [
        'projects.php' => 'projects',
        'my_skills.php' => 'skills',
        'portfolio.php' => 'portfolio',
        'about.php' => 'about',
        'users.php' => 'users',
        'index.php' => 'home'
    ];
    $current_page_code = $page_mappings[$current_file] ?? 'unknown';
}

// Get page information
$page_info = $permissionController->getPageByCode($current_page_code);

// Check if user has access to this page (at least view permission)
$has_view_permission = $permissionController->hasPermission('view_' . $current_page_code, $current_page_code);

// If no view permission, deny access
if (!$has_view_permission && $current_page_code !== 'unknown') {
    die('Access Denied: You do not have permission to view this page.');
}

// Get all permissions for current page
$page_permissions = $permissionController->getPagePermissionsByType($current_page_code);

// Build permission data array for JavaScript
$permission_data = [
    'user_id' => $currentUser['id'],
    'username' => $currentUser['username'],
    'page_code' => $current_page_code,
    'page_name' => $page_info['page_name'] ?? 'Unknown Page',
    'permissions' => [
        'can_create' => in_array('add_' . $current_page_code, $page_permissions['create'] ?? []),
        'can_read' => in_array('view_' . $current_page_code, $page_permissions['read'] ?? []),
        'can_update' => in_array('edit_' . $current_page_code, $page_permissions['update'] ?? []),
        'can_delete' => in_array('delete_' . $current_page_code, $page_permissions['delete'] ?? []),
        'all_permissions' => array_merge(
            $page_permissions['create'] ?? [],
            $page_permissions['read'] ?? [],
            $page_permissions['update'] ?? [],
            $page_permissions['delete'] ?? []
        )
    ],
    'authenticated' => true
];

// Update session last activity time
$_SESSION['last_activity'] = time();
?>

<!-- Auth Check Hidden Data Container -->
<div id="auth-check-data" 
     style="display: none;" 
     data-user-id="<?php echo htmlspecialchars($permission_data['user_id']); ?>"
     data-username="<?php echo htmlspecialchars($permission_data['username']); ?>"
     data-page-code="<?php echo htmlspecialchars($permission_data['page_code']); ?>"
     data-can-create="<?php echo $permission_data['permissions']['can_create'] ? 'true' : 'false'; ?>"
     data-can-read="<?php echo $permission_data['permissions']['can_read'] ? 'true' : 'false'; ?>"
     data-can-update="<?php echo $permission_data['permissions']['can_update'] ? 'true' : 'false'; ?>"
     data-can-delete="<?php echo $permission_data['permissions']['can_delete'] ? 'true' : 'false'; ?>">
</div>

<!-- Permission Control Script -->
<script>
(function() {
    'use strict';
    
    // Permission data from server
    const authCheckElement = document.getElementById('auth-check-data');
    const authData = {
        userId: authCheckElement.getAttribute('data-user-id'),
        username: authCheckElement.getAttribute('data-username'),
        pageCode: authCheckElement.getAttribute('data-page-code'),
        canCreate: authCheckElement.getAttribute('data-can-create') === 'true',
        canRead: authCheckElement.getAttribute('data-can-read') === 'true',
        canUpdate: authCheckElement.getAttribute('data-can-update') === 'true',
        canDelete: authCheckElement.getAttribute('data-can-delete') === 'true'
    };
    
    // Expose auth data globally for use in other scripts
    window.AuthData = authData;
    
    /**
     * Control button visibility based on permissions
     * Buttons should have data-permission attribute with action name
     * Example: <button data-permission="add_project">Add Project</button>
     */
    function controlUIVisibility() {
        // Hide buttons without permission
        const buttons = document.querySelectorAll('[data-permission]');
        buttons.forEach(btn => {
            const permissionCode = btn.getAttribute('data-permission');
            const hasPermission = checkPermission(permissionCode);
            
            if (!hasPermission) {
                btn.style.display = 'none';
                btn.disabled = true;
                // Mark as hidden for CSS styling if needed
                btn.classList.add('permission-denied');
            } else {
                btn.style.display = '';
                btn.disabled = false;
                btn.classList.remove('permission-denied');
            }
        });
        
        // Hide HTML sections without permission
        const sections = document.querySelectorAll('[data-permission-section]');
        sections.forEach(section => {
            const permissionCode = section.getAttribute('data-permission-section');
            const hasPermission = checkPermission(permissionCode);
            
            if (!hasPermission) {
                section.style.display = 'none';
            } else {
                section.style.display = '';
            }
        });
    }
    
    /**
     * Check if user has specific permission
     */
    function checkPermission(permissionCode) {
        // Parse permission code to determine action type
        if (permissionCode.startsWith('add_') || permissionCode.startsWith('create_')) {
            return authData.canCreate;
        }
        if (permissionCode.startsWith('view_') || permissionCode.startsWith('read_')) {
            return authData.canRead;
        }
        if (permissionCode.startsWith('edit_') || permissionCode.startsWith('update_')) {
            return authData.canUpdate;
        }
        if (permissionCode.startsWith('delete_') || permissionCode.startsWith('remove_')) {
            return authData.canDelete;
        }
        
        // Default deny if permission type not recognized
        return false;
    }
    
    /**
     * Verify authentication with server every 10 seconds
     */
    function verifyAuthentication() {
        fetch('/admin/handlers/auth_verify.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Auth-Check': 'true'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ timestamp: Date.now() })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.authenticated) {
                // Session expired, redirect to login
                window.location.href = '/admin/login.php?expired=1';
            }
        })
        .catch(error => {
            console.warn('Auth verification failed:', error);
            // Continue operation on network error
        });
    }
    
    /**
     * Initialize auth check
     */
    function init() {
        // Apply UI controls immediately on page load
        controlUIVisibility();
        
        // Set up periodic authentication verification (every 10 seconds)
        setInterval(verifyAuthentication, 10000);
        
        // Listen for permission changes (if needed for dynamic content)
        document.addEventListener('DOMContentLoaded', controlUIVisibility);
    }
    
    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Expose functions for external use
    window.PermissionController = {
        checkPermission: checkPermission,
        refreshUI: controlUIVisibility,
        getAuthData: () => authData
    };
})();
</script>

<!-- CSS for permission denied elements (optional) -->
<style>
    [data-permission].permission-denied,
    [data-permission-section] { display: none; }
</style>
