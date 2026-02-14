<?php
/**
 * Auth Verify Handler
 * AJAX endpoint called every 10 seconds to verify user authentication
 * Returns JSON with authentication status
 */

require_once __DIR__ . '/../../config/permission_controller.php';

header('Content-Type: application/json');

try {
    $permissionController = new PermissionController();
    
    // Check if user is authenticated
    $is_authenticated = $permissionController->isAuthenticated();
    
    if ($is_authenticated) {
        $user = $permissionController->getCurrentUser();
        
        echo json_encode([
            'authenticated' => true,
            'user_id' => $user['id'],
            'username' => $user['username'],
            'timestamp' => time()
        ]);
    } else {
        echo json_encode([
            'authenticated' => false,
            'message' => 'Session expired or user not logged in',
            'timestamp' => time()
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'authenticated' => false,
        'error' => $e->getMessage(),
        'timestamp' => time()
    ]);
}
?>
