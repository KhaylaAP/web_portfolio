<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/action_validator.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$validator = new ActionValidator('users');
$database = new Database();
$conn = $database->getConnection();

$action = isset($_POST['action']) ? $_POST['action'] : '';

try {
    switch ($action) {
        case 'add':
            $perm = $validator->validateCreate();
            if (!$perm['allowed']) { http_response_code(403); echo json_encode(['success'=>false,'message'=>$perm['message']]); exit; }

            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 1;

            if (empty($username) || empty($password)) {
                echo json_encode(['success'=>false,'message'=>'Username and password are required']); exit;
            }

            // Check username uniqueness
            $q = "SELECT id FROM users WHERE username = ? LIMIT 1";
            $st = $conn->prepare($q);
            $st->execute([$username]);
            if ($st->fetch()) { echo json_encode(['success'=>false,'message'=>'Username already exists']); exit; }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = "INSERT INTO users (username, password, email, full_name, is_active, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $s = $conn->prepare($ins);
            if ($s->execute([$username, $hash, $email, $full_name, $is_active])) {
                echo json_encode(['success'=>true,'message'=>'User created']);
            } else {
                echo json_encode(['success'=>false,'message'=>'Failed to create user']);
            }
            break;

        case 'edit':
            $perm = $validator->validateUpdate();
            if (!$perm['allowed']) { http_response_code(403); echo json_encode(['success'=>false,'message'=>$perm['message']]); exit; }

            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            if ($id <= 0) { echo json_encode(['success'=>false,'message'=>'Invalid user id']); exit; }

            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 1;

            if (empty($username)) { echo json_encode(['success'=>false,'message'=>'Username is required']); exit; }

            // Ensure username uniqueness (excluding current)
            $q = "SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1";
            $st = $conn->prepare($q);
            $st->execute([$username, $id]);
            if ($st->fetch()) { echo json_encode(['success'=>false,'message'=>'Username already taken by another user']); exit; }

            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $upd = "UPDATE users SET username = ?, password = ?, email = ?, full_name = ?, is_active = ? WHERE id = ?";
                $s = $conn->prepare($upd);
                $ok = $s->execute([$username, $hash, $email, $full_name, $is_active, $id]);
            } else {
                $upd = "UPDATE users SET username = ?, email = ?, full_name = ?, is_active = ? WHERE id = ?";
                $s = $conn->prepare($upd);
                $ok = $s->execute([$username, $email, $full_name, $is_active, $id]);
            }

            if ($ok) {
                echo json_encode(['success'=>true,'message'=>'User updated']);
            } else {
                echo json_encode(['success'=>false,'message'=>'Failed to update user']);
            }
            break;

        case 'delete':
            $perm = $validator->validateDelete();
            if (!$perm['allowed']) { http_response_code(403); echo json_encode(['success'=>false,'message'=>$perm['message']]); exit; }

            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            if ($id <= 0) { echo json_encode(['success'=>false,'message'=>'Invalid user id']); exit; }

            // Prevent deleting currently logged in user
            if ($id == $_SESSION['user_id']) { echo json_encode(['success'=>false,'message'=>'Cannot delete currently logged in user']); exit; }

            $del = "DELETE FROM users WHERE id = ?";
            $s = $conn->prepare($del);
            if ($s->execute([$id])) {
                echo json_encode(['success'=>true,'message'=>'User deleted']);
            } else {
                echo json_encode(['success'=>false,'message'=>'Failed to delete user']);
            }
            break;

        default:
            echo json_encode(['success'=>false,'message'=>'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

?>
