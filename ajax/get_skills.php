<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/skill_functions.php';

try {
    $skillFunc = new SkillFunctions();
    
    $skills = [
        'languages' => $skillFunc->getLanguageSkills(),
        'coding' => $skillFunc->getProgrammingLanguages(),
        'crafting' => $skillFunc->getCraftingSkills(),
        'organization' => $skillFunc->getOrganizationSkills()
    ];
    
    // Format proficiency untuk semua skill
    foreach ($skills as $category => &$categorySkills) {
        foreach ($categorySkills as &$skill) {
            $skill['proficiency_formatted'] = $skillFunc->formatProficiency($skill['proficiency']);
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $skills,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>