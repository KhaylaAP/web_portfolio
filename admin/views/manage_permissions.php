<?php
/**
 * User Permissions Management Page
 * This page allows admins to manage user permissions for different pages and actions
 */

require_once '../check_session.php';
require_once '../../config/database.php';
require_once '../../config/permission_controller.php';

// Include authentication and permission check view
require_once 'auth_check.php';

// Initialize permission controller
$permissionController = new PermissionController();

// Check if user is admin (has permission to manage permissions)
// For now, we'll check if user has edit permissions on the page
if (!$permissionController->hasPermission('edit_permissions')) {
    die('Access Denied: You do not have permission to manage user permissions.');
}

$database = new Database();
$conn = $database->getConnection();

// Get all users
$query = "SELECT id, username, email, full_name, is_active FROM users ORDER BY username";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all pages
$query = "SELECT id, page_code, page_name FROM pages WHERE is_active = 1 ORDER BY page_name";
$stmt = $conn->prepare($query);
$stmt->execute();
$pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all permissions
$query = "SELECT id, permission_code, permission_name, permission_type FROM permissions ORDER BY permission_type, permission_name";
$stmt = $conn->prepare($query);
$stmt->execute();
$permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current user permissions mapping
$userPermissions = [];
if (!empty($users)) {
    $query = "
        SELECT up.user_id, up.page_id, p.permission_code
        FROM user_permissions up
        INNER JOIN permissions p ON up.permission_id = p.id
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $userPermsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($userPermsData as $perm) {
        $key = $perm['user_id'] . '_' . $perm['page_id'];
        if (!isset($userPermissions[$key])) {
            $userPermissions[$key] = [];
        }
        $userPermissions[$key][] = $perm['permission_code'];
    }
}

$message = '';
$messageType = '';

// Handle permission grant/revoke via AJAX or form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $page_id = isset($_POST['page_id']) ? intval($_POST['page_id']) : 0;
    $permission_id = isset($_POST['permission_id']) ? intval($_POST['permission_id']) : 0;
    
    if ($action === 'grant' && $user_id > 0 && $page_id > 0 && $permission_id > 0) {
        try {
            $query = "INSERT INTO user_permissions (user_id, page_id, permission_id, granted_by) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$user_id, $page_id, $permission_id, $_SESSION['user_id']]);
            $message = 'Permission granted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error granting permission: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'revoke' && $user_id > 0 && $page_id > 0 && $permission_id > 0) {
        try {
            $query = "DELETE FROM user_permissions WHERE user_id = ? AND page_id = ? AND permission_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$user_id, $page_id, $permission_id]);
            $message = 'Permission revoked successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error revoking permission: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Permissions Management</title>
    <link rel="stylesheet" href="../css/adminlte.css">
    <style>
        .permission-grid {
            display: grid;
            gap: 20px;
        }
        
        .permission-section {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            background: #f9f9f9;
        }
        
        .permission-section h4 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
        }
        
        .permission-checkboxes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            margin: 10px 0;
        }
        
        .permission-checkbox {
            display: flex;
            align-items: center;
        }
        
        .permission-checkbox input {
            margin-right: 8px;
        }
        
        .permission-checkbox label {
            margin: 0;
            cursor: pointer;
        }
        
        .user-card {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
        }
        
        .user-card h5 {
            margin-top: 0;
            color: #007bff;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-primary { background: #0066cc; color: white; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Content -->
        <div class="container-fluid" style="padding: 20px;">
            <h1>User Permissions Management</h1>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="permission-grid">
                <?php foreach ($users as $user): ?>
                    <div class="user-card">
                        <h5>
                            <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>
                            <span class="badge <?php echo $user['is_active'] ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </h5>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <?php if (!empty($user['email'])): ?>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <?php endif; ?>
                        
                        <div class="permission-section">
                            <h6>Page Permissions</h6>
                            <?php foreach ($pages as $page): ?>
                                <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">
                                    <h6 style="margin: 5px 0;"><?php echo htmlspecialchars($page['page_name']); ?></h6>
                                    <div class="permission-checkboxes">
                                        <?php foreach ($permissions as $permission): ?>
                                            <?php
                                            $key = $user['id'] . '_' . $page['id'];
                                            $hasPermission = isset($userPermissions[$key]) && in_array($permission['permission_code'], $userPermissions[$key]);
                                            ?>
                                            <div class="permission-checkbox">
                                                <input 
                                                    type="checkbox" 
                                                    id="perm_<?php echo $user['id']; ?>_<?php echo $page['id']; ?>_<?php echo $permission['id']; ?>"
                                                    class="permission-toggle"
                                                    data-user-id="<?php echo $user['id']; ?>"
                                                    data-page-id="<?php echo $page['id']; ?>"
                                                    data-permission-id="<?php echo $permission['id']; ?>"
                                                    data-permission-code="<?php echo htmlspecialchars($permission['permission_code']); ?>"
                                                    <?php echo $hasPermission ? 'checked' : ''; ?>
                                                >
                                                <label for="perm_<?php echo $user['id']; ?>_<?php echo $page['id']; ?>_<?php echo $permission['id']; ?>">
                                                    <?php echo htmlspecialchars($permission['permission_name']); ?>
                                                    <span class="badge badge-warning" style="margin-left: 5px;"><?php echo htmlspecialchars($permission['permission_type']); ?></span>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <script src="../js/adminlte.js"></script>
    <script src="../../assets/js/permission-manager.js"></script>
    
    <script>
        // Handle permission checkbox changes
        document.querySelectorAll('.permission-toggle').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const userId = this.getAttribute('data-user-id');
                const pageId = this.getAttribute('data-page-id');
                const permissionId = this.getAttribute('data-permission-id');
                const isChecked = this.checked;
                
                const action = isChecked ? 'grant' : 'revoke';
                
                const formData = new FormData();
                formData.append('action', action);
                formData.append('user_id', userId);
                formData.append('page_id', pageId);
                formData.append('permission_id', permissionId);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.text())
                .then(data => {
                    // Reload page to show updated permissions
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating permission');
                    this.checked = !isChecked;
                });
            });
        });
    </script>
</body>
</html>
