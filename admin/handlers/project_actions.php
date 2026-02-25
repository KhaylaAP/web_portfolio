<?php
require_once '../../config/database.php';
require_once '../check_session.php';
require_once '../../config/action_validator.php';

header('Content-Type: application/json');

// Initialize validator for projects page
$validator = new ActionValidator('projects');

$database = new Database();
$db = $database->getConnection();
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

// Create upload directory if not exists
$uploadDir = '../../uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Create projects table if not exists with description field
$createTable = "CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    period VARCHAR(100),
    category VARCHAR(100),
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$db->exec($createTable);

switch($action) {
    case 'list':
        // Check read permission
        $permissionCheck = $validator->validateRead();
        if (!$permissionCheck['allowed']) {
            echo json_encode(['status' => 'error', 'message' => $permissionCheck['message']]);
            exit;
        }
        
        $query = "SELECT * FROM projects ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($projects);
        break;
        
    case 'get':
        // Check read permission
        $permissionCheck = $validator->validateRead();
        if (!$permissionCheck['allowed']) {
            echo json_encode(['status' => 'error', 'message' => $permissionCheck['message']]);
            exit;
        }
        
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $query = "SELECT * FROM projects WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($project);
        break;
        
    case 'save':
        // Check create or update permission based on whether ID exists
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        
        if (empty($id)) {
            // Create new project - check CREATE permission
            $permissionCheck = $validator->validateCreate();
        } else {
            // Update existing project - check UPDATE permission
            $permissionCheck = $validator->validateUpdate();
        }
        
        if (!$permissionCheck['allowed']) {
            echo json_encode(['status' => 'error', 'message' => $permissionCheck['message']]);
            exit;
        }
        
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $category = isset($_POST['category']) ? trim($_POST['category']) : '';
        $image = isset($_POST['image']) ? trim($_POST['image']) : '';
        
        if (empty($title) || empty($description) || empty($image)) {
            echo json_encode(['status' => 'error', 'message' => 'Title, description, and image are required']);
            exit;
        }
        
        if (empty($id)) {
            // Insert new project
            $query = "INSERT INTO `projects` (`title`, `description`, `category`, `image`) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([$title, $description, $category, $image])) {
                echo json_encode(['status' => 'success', 'message' => 'Project added successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add project']);
            }
        } else {
            // Update existing project
            $query = "UPDATE `projects` SET `title` = ?, `description` = ?, `category` = ?, `image` = ? WHERE `id` = ?";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([$title, $description, $category, $image, $id])) {
                echo json_encode(['status' => 'success', 'message' => 'Project updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update project']);
            }
        }
        break;
        
    case 'delete':
        // Check delete permission
        $permissionCheck = $validator->validateDelete();
        if (!$permissionCheck['allowed']) {
            echo json_encode(['status' => 'error', 'message' => $permissionCheck['message']]);
            exit;
        }
        
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        
        // Delete from database
        $query = "DELETE FROM projects WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$id])) {
            // --- Re-sequence IDs to prevent gaps ---
            
            // 1. Get all remaining projects ordered by their original creation date (or current ID)
            $fetchStmt = $db->query("SELECT id FROM projects ORDER BY id ASC");
            $remainingProjects = $fetchStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // 2. Temporarily disable foreign key checks (even if none exist, good practice)
            $db->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            // 3. Loop and update IDs to be strictly sequential starting from 1
            $currentExpectedId = 1;
            $updateStmt = $db->prepare("UPDATE projects SET id = ? WHERE id = ?");
            
            foreach ($remainingProjects as $proj) {
                $oldId = $proj['id'];
                if ($oldId != $currentExpectedId) {
                    $updateStmt->execute([$currentExpectedId, $oldId]);
                }
                $currentExpectedId++;
            }
            
            // 4. Reset the AUTO_INCREMENT to the next available ID
            $db->exec("ALTER TABLE projects AUTO_INCREMENT = $currentExpectedId");
            
            // 5. Re-enable foreign key checks
            $db->exec("SET FOREIGN_KEY_CHECKS = 1");
            
            // --- End of re-sequencing logic ---

            echo json_encode(['status' => 'success', 'message' => 'Project deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete project']);
        }
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>