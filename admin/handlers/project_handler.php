<?php
require_once '../check_session.php';
require_once '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Determine the action
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

if (!$conn) {
    header('Location: ../views/projects.php?error=1');
    exit;
}

// Add project
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $image = trim($_POST['image'] ?? '');

    if (!empty($title) && !empty($category) && !empty($image)) {
        try {
            $query = "INSERT INTO projects (title, category, image) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$title, $category, $image]);
            header('Location: ../views/projects.php?success=1');
            exit;
        } catch (PDOException $e) {
            header('Location: ../views/projects.php?error=1');
            exit;
        }
    } else {
        header('Location: ../views/projects.php?error=1');
        exit;
    }
}

// Edit project
else if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $image = trim($_POST['image'] ?? '');

    if ($id > 0 && !empty($title) && !empty($category) && !empty($image)) {
        try {
            $query = "UPDATE projects SET title = ?, category = ?, image = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$title, $category, $image, $id]);
            header('Location: ../views/projects.php?success=1');
            exit;
        } catch (PDOException $e) {
            header('Location: ../views/projects.php?error=1');
            exit;
        }
    } else {
        header('Location: ../views/projects.php?error=1');
        exit;
    }
}

// Delete project
else if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id > 0) {
        try {
            $query = "DELETE FROM projects WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);
            header('Location: ../views/projects.php?success=1');
            exit;
        } catch (PDOException $e) {
            header('Location: ../views/projects.php?error=1');
            exit;
        }
    } else {
        header('Location: ../views/projects.php?error=1');
        exit;
    }
}

// Default: redirect back
header('Location: ../views/projects.php');
exit;
?>
