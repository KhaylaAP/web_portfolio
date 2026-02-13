<?php
require_once '../check_session.php';
require_once '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

if ($conn) {
    try {
        if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $category = isset($_POST['category']) ? trim($_POST['category']) : '';
            $skill_detail = isset($_POST['skill_detail']) ? trim($_POST['skill_detail']) : '';
            $proficiency = isset($_POST['proficiency']) ? intval($_POST['proficiency']) : 0;

            if (!empty($category) && !empty($skill_detail) && $proficiency >= 0 && $proficiency <= 100) {
                $query = "INSERT INTO skills (category, skill_detail, proficiency) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->execute([$category, $skill_detail, $proficiency]);
                header('Location: ../views/my_skills.php?success=1');
                exit;
            }
        } elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $category = isset($_POST['category']) ? trim($_POST['category']) : '';
            $skill_detail = isset($_POST['skill_detail']) ? trim($_POST['skill_detail']) : '';
            $proficiency = isset($_POST['proficiency']) ? intval($_POST['proficiency']) : 0;

            if ($id > 0 && !empty($category) && !empty($skill_detail) && $proficiency >= 0 && $proficiency <= 100) {
                $query = "UPDATE skills SET category = ?, skill_detail = ?, proficiency = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$category, $skill_detail, $proficiency, $id]);
                header('Location: ../views/my_skills.php?success=1');
                exit;
            }
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            if ($id > 0) {
                $query = "DELETE FROM skills WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id]);
                header('Location: ../views/my_skills.php?success=1');
                exit;
            }
        }
    } catch (PDOException $e) {
        // Handle error - redirect back with error message
        header('Location: ../views/my_skills.php?error=1');
        exit;
    }
}

// If we reach here, redirect back
header('Location: ../views/my_skills.php');
exit;
?>
