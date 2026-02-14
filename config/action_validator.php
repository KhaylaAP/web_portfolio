<?php
/**
 * Permission Validator Utility
 * Used in handlers to validate user permissions before CRUD operations
 * Returns error response if permission denied
 */

require_once __DIR__ . '/permission_controller.php';

class ActionValidator {
    private $permissionController;
    private $page_code;
    
    public function __construct($page_code) {
        $this->permissionController = new PermissionController();
        $this->page_code = $page_code;
    }
    
    /**
     * Validate if user can perform CREATE action
     * @return array ['allowed' => bool, 'message' => string]
     */
    public function validateCreate() {
        $permission_code = 'add_' . $this->page_code;
        
        if (!$this->permissionController->hasPermission($permission_code, $this->page_code)) {
            return [
                'allowed' => false,
                'message' => 'You do not have permission to create records on this page.'
            ];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Validate if user can perform READ action
     */
    public function validateRead() {
        $permission_code = 'view_' . $this->page_code;
        
        if (!$this->permissionController->hasPermission($permission_code, $this->page_code)) {
            return [
                'allowed' => false,
                'message' => 'You do not have permission to view records on this page.'
            ];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Validate if user can perform UPDATE action
     */
    public function validateUpdate() {
        $permission_code = 'edit_' . $this->page_code;
        
        if (!$this->permissionController->hasPermission($permission_code, $this->page_code)) {
            return [
                'allowed' => false,
                'message' => 'You do not have permission to edit records on this page.'
            ];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Validate if user can perform DELETE action
     */
    public function validateDelete() {
        $permission_code = 'delete_' . $this->page_code;
        
        if (!$this->permissionController->hasPermission($permission_code, $this->page_code)) {
            return [
                'allowed' => false,
                'message' => 'You do not have permission to delete records on this page.'
            ];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Validate generic permission with custom permission code
     */
    public function validatePermission($permission_code) {
        if (!$this->permissionController->hasPermission($permission_code, $this->page_code)) {
            return [
                'allowed' => false,
                'message' => "You do not have permission to perform this action ($permission_code)."
            ];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Get permission controller for advanced operations
     */
    public function getController() {
        return $this->permissionController;
    }
}
?>
