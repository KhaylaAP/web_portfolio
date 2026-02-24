<?php
require_once '../check_session.php';
require_once '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($conn) {
    try {
        if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {

            $category = trim($_POST['category'] ?? '');
            $skill_detail = trim($_POST['skill_detail'] ?? '');
            $proficiency = intval($_POST['proficiency'] ?? 0);

            if ($category && $skill_detail && $proficiency >= 0 && $proficiency <= 100) {
                $stmt = $conn->prepare("INSERT INTO skills (category, skill_detail, proficiency) VALUES (?, ?, ?)");
                $stmt->execute([$category, $skill_detail, $proficiency]);
            }

            header('Location: ../views/my_skills.php?success=1');
            exit;
        }

        elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = intval($_POST['id'] ?? 0);
            $category = trim($_POST['category'] ?? '');
            $skill_detail = trim($_POST['skill_detail'] ?? '');
            $proficiency = intval($_POST['proficiency'] ?? 0);

            if ($id > 0 && $category && $skill_detail && $proficiency >= 0 && $proficiency <= 100) {
                $stmt = $conn->prepare("UPDATE skills SET category=?, skill_detail=?, proficiency=? WHERE id=?");
                $stmt->execute([$category, $skill_detail, $proficiency, $id]);
            }

            header('Location: ../views/my_skills.php?success=1');
            exit;
        }

        elseif ($action === 'delete' && isset($_GET['id'])) {

            $id = intval($_GET['id']);
            if ($id > 0) {
                $stmt = $conn->prepare("DELETE FROM skills WHERE id=?");
                $stmt->execute([$id]);
            }

            header('Location: ../views/my_skills.php?success=1');
            exit;
        }

    } catch (PDOException $e) {
        header('Location: ../views/my_skills.php?error=1');
        exit;
    }
}

header('Location: ../views/my_skills.php');
exit;