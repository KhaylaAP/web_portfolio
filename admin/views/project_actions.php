<?php
require_once '../../config/database.php';
require_once '../check_session.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Create upload directory if not exists
$uploadDir = '../../uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Create projects table if not exists
$createTable = "CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    period VARCHAR(100),
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$db->exec($createTable);

switch($action) {
    case 'list':
        $query = "SELECT * FROM projects ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($projects);
        break;
        
    case 'get':
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $query = "SELECT * FROM projects WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($project);
        break;
        
    case 'save':
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $period = isset($_POST['period']) ? $_POST['period'] : '';
        
        if (empty($title) || empty($description) || empty($period)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            exit;
        }
        
        // Handle file upload
        $photo = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $newFilename = time() . '_' . uniqid() . '.' . $ext;
                $uploadPath = $uploadDir . $newFilename;
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                    $photo = $newFilename;
                }
            }
        }
        
        if (empty($id)) {
            // Insert new project
            $query = "INSERT INTO projects (title, description, period, photo) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([$title, $description, $period, $photo])) {
                echo json_encode(['status' => 'success', 'message' => 'Project added successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add project']);
            }
        } else {
            // Update existing project
            if (!empty($photo)) {
                // Get old photo to delete
                $query = "SELECT photo FROM projects WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$id]);
                $old = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($old && !empty($old['photo']) && file_exists($uploadDir . $old['photo'])) {
                    unlink($uploadDir . $old['photo']);
                }
                
                $query = "UPDATE projects SET title = ?, description = ?, period = ?, photo = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $success = $stmt->execute([$title, $description, $period, $photo, $id]);
            } else {
                $query = "UPDATE projects SET title = ?, description = ?, period = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $success = $stmt->execute([$title, $description, $period, $id]);
            }
            
            if ($success) {
                echo json_encode(['status' => 'success', 'message' => 'Project updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update project']);
            }
        }
        break;
        
    case 'delete':
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        
        // Get photo to delete
        $query = "SELECT photo FROM projects WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete from database
        $query = "DELETE FROM projects WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$id])) {
            // Delete photo file
            if ($project && !empty($project['photo']) && file_exists($uploadDir . $project['photo'])) {
                unlink($uploadDir . $project['photo']);
            }
            echo json_encode(['status' => 'success', 'message' => 'Project deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete project']);
        }
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>