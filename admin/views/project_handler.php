<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once '../../config/action_validator.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize validator for projects page
$validator = new ActionValidator('projects');

$database = new Database();
$conn = $database->getConnection();

// Function to generate unique filename
function generateUniqueFilename($file, $prefix = 'project') {
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $extension;
    return $filename;
}

// Function to delete old image
function deleteOldImage($filename) {
    $filePath = '../../uploads/projects/' . $filename;
    if (!empty($filename) && file_exists($filePath)) {
        return unlink($filePath);
    }
    return false;
}

// Handle different actions
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'add':
        // Validate permission
        $permissionCheck = $validator->validateCreate();
        if (!$permissionCheck['allowed']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => $permissionCheck['message']]);
            exit;
        }
        
        // Validate required fields
        $required = ['title', 'category', 'period', 'description'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
                exit;
            }
        }
        
        // Handle image upload
        $imageFilename = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../uploads/projects/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Generate unique filename
            $imageFilename = generateUniqueFilename($_FILES['image'], 'project');
            $uploadPath = $uploadDir . $imageFilename;
            
            // Move uploaded file
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit;
            }
        }
        
        // Insert into database
        $query = "INSERT INTO `projects` (`title`, `category`, `description`, `period`, `image`) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        if ($stmt->execute([
            $_POST['title'],
            $_POST['category'],
            $_POST['description'],
            $_POST['period'],
            $imageFilename
        ])) {
            echo json_encode(['success' => true, 'message' => 'Project added successfully']);
        } else {
            // Delete uploaded image if database insert fails
            if ($imageFilename) {
                deleteOldImage($imageFilename);
            }
            echo json_encode(['success' => false, 'message' => 'Failed to save project to database']);
        }
        break;
        
    case 'edit':
        // Validate permission
        $permissionCheck = $validator->validateUpdate();
        if (!$permissionCheck['allowed']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => $permissionCheck['message']]);
            exit;
        }
        
        // Validate required fields
        $required = ['id', 'title', 'category', 'period', 'description'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
                exit;
            }
        }
        
        $id = $_POST['id'];
        $existing_image = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
        
        // Handle image upload
        $imageFilename = $existing_image;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../uploads/projects/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Generate unique filename
            $imageFilename = generateUniqueFilename($_FILES['image'], 'project');
            $uploadPath = $uploadDir . $imageFilename;
            
            // Move uploaded file
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit;
            }
            
            // Delete old image if exists
            if (!empty($existing_image)) {
                deleteOldImage($existing_image);
            }
        }
        
        // Update database
        $query = "UPDATE `projects` SET 
                  `title` = ?, 
                  `category` = ?, 
                  `description` = ?, 
                  `period` = ?,
                  `image` = ?
                  WHERE `id` = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt->execute([
            $_POST['title'],
            $_POST['category'],
            $_POST['description'],
            $_POST['period'],
            $imageFilename,
            $id
        ])) {
            echo json_encode(['success' => true, 'message' => 'Project updated successfully']);
        } else {
            // Delete newly uploaded image if database update fails
            if ($imageFilename !== $existing_image) {
                deleteOldImage($imageFilename);
            }
            echo json_encode(['success' => false, 'message' => 'Failed to update project in database']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>