<?php
require_once __DIR__ . '/database.php';

class SkillFunctions {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getAllSkills() {
        $query = "SELECT * FROM skills ORDER BY 
                  CASE category
                    WHEN 'Languages' THEN 1
                    WHEN 'Coding' THEN 2
                    WHEN 'Crafting' THEN 3
                    WHEN 'Organization' THEN 4
                    ELSE 5
                  END, skill_detail";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSkillsByCategory($category) {
        $query = "SELECT * FROM skills WHERE category = :category ORDER BY 
                  CASE 
                    WHEN proficiency REGEXP '^[0-9]+$' THEN CAST(proficiency AS UNSIGNED) 
                    ELSE 0 
                  END DESC, skill_detail";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProgrammingLanguages() {
        return $this->getSkillsByCategory('Coding');
    }
    
    public function getLanguageSkills() {
        return $this->getSkillsByCategory('Languages');
    }
    
    public function getCraftingSkills() {
        return $this->getSkillsByCategory('Crafting');
    }
    
    public function getOrganizationSkills() {
        return $this->getSkillsByCategory('Organization');
    }
    
    public function formatProficiency($proficiency) {
        if (is_numeric($proficiency)) {
            return $proficiency . '%';
        }
        return $proficiency;
    }
}
?>