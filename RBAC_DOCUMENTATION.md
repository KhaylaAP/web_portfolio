# CMS RBAC (Role-Based Access Control) System - Documentation

## Overview

This document describes the complete Role-Based Access Control (RBAC) system implemented for your Portfolio CMS. The system provides user authentication, permission management, and UI control based on user access rights.

---

## 📋 Table of Contents

1. [Database Schema](#database-schema)
2. [Architecture](#architecture)
3. [Files Created/Modified](#files-createdmodified)
4. [How to Use](#how-to-use)
5. [Permission Codes](#permission-codes)
6. [Implementation Guide](#implementation-guide)

---

## Database Schema

### New Tables Created

#### 1. **users** (Updated)
Stores user credentials with encrypted passwords.
```sql
- id: INT (Primary Key, Auto-increment)
- username: VARCHAR(100) UNIQUE
- password: VARCHAR(255) - bcrypt encrypted
- email: VARCHAR(100)
- full_name: VARCHAR(255)
- is_active: TINYINT(1) - Enable/disable users
- created_at: TIMESTAMP
- updated_at: TIMESTAMP
```

#### 2. **pages** 
Stores all CMS pages information.
```sql
- id: INT (Primary Key, Auto-increment)
- page_code: VARCHAR(50) UNIQUE - System identifier (e.g., 'projects', 'skills')
- page_name: VARCHAR(255) - Display name
- description: TEXT
- url: VARCHAR(255) - Page URL
- is_active: TINYINT(1)
- created_at: TIMESTAMP
- updated_at: TIMESTAMP
```

#### 3. **permissions**
Defines individual permission actions (CRUD operations).
```sql
- id: INT (Primary Key, Auto-increment)
- permission_code: VARCHAR(100) UNIQUE - System identifier (e.g., 'add_project')
- permission_name: VARCHAR(255) - Display name
- permission_type: ENUM('CREATE', 'READ', 'UPDATE', 'DELETE')
- description: TEXT
- created_at: TIMESTAMP
```

#### 4. **user_permissions**
Maps users to pages and their permissions (Foreign Key relationships).
```sql
- id: INT (Primary Key, Auto-increment)
- user_id: INT (FK → users.id)
- page_id: INT (FK → pages.id)
- permission_id: INT (FK → permissions.id)
- granted_at: TIMESTAMP
- granted_by: INT (Admin who granted the permission)
- UNIQUE: (user_id, page_id, permission_id)
```

**SQL File:** `/database/portfolio_rbac.sql`

---

## Architecture

### System Flow

```
┌─────────────────┐
│  User Request   │
└────────┬────────┘
         │
         ↓
┌──────────────────────────┐
│  auth_check.php          │
│  - Validates session     │
│  - Checks user auth      │
│  - Gets page permissions │
└────────┬─────────────────┘
         │
         ↓
┌──────────────────────────┐
│  JS Permission Control   │
│  - Hide/show buttons     │
│  - Control UI elements   │
│  - Verify every 10 sec   │
└────────┬─────────────────┘
         │
         ↓
┌──────────────────────────┐
│  Action Handler          │
│  - Validate permissions  │
│  - Execute CRUD          │
│  - Return response       │
└──────────────────────────┘
```

---

## Files Created/Modified

### New Files Created

1. **`/config/permission_controller.php`**
   - Permission management controller class
   - Methods for checking, granting, revoking permissions
   
2. **`/config/action_validator.php`**
   - Validates user permissions before CRUD operations
   - Used in handlers to enforce server-side permission checks

3. **`/admin/views/auth_check.php`**
   - Permission checking view component
   - Should be included in every CMS page
   - Verifies authentication every 10 seconds
   - Populates permission data for JavaScript

4. **`/admin/handlers/auth_verify.php`**
   - AJAX endpoint for periodic authentication verification
   - Called every 10 seconds from JavaScript
   - Returns JSON with auth status

5. **`/assets/js/permission-manager.js`**
   - Client-side permission management
   - Shows/hides UI elements based on permissions
   - Validates permissions before user actions

6. **`/admin/views/manage_permissions.php`**
   - Admin interface for managing user permissions
   - Allows granting/revoking permissions via checkboxes
   - Display all users and their page permissions

7. **`/database/portfolio_rbac.sql`**
   - Complete database schema with RBAC tables
   - Default pages and permissions
   - Default admin user

### Modified Files

1. **`/admin/views/my_skills.php`**
   - Added: `require_once 'auth_check.php';`
   - Added: `<script src="../../assets/js/permission-manager.js"></script>`

2. **`/admin/views/projects.php`**
   - Added: `require_once 'auth_check.php';`
   - Added: `<script src="../../assets/js/permission-manager.js"></script>`

3. **`/admin/handlers/skill_handler.php`**
   - Added permission validation for add, edit, delete actions
   - Uses ActionValidator class

4. **`/admin/views/project_handler.php`**
   - Added permission validation for add, edit actions
   - Uses ActionValidator class

5. **`/admin/handlers/project_actions.php`**
   - Added permission validation for list, get, save, delete actions
   - Uses ActionValidator class

---

## Permission Codes

Permission codes follow a naming pattern: `{action}_{page}`

### Default Permissions

#### Projects Page (page_code: `projects`)
- `add_project` - Create new project
- `view_project` - Read/view projects
- `edit_project` - Update project
- `delete_project` - Delete project

#### Skills Page (page_code: `skills`)
- `add_skill` - Create new skill
- `view_skill` - Read/view skills
- `edit_skill` - Update skill
- `delete_skill` - Delete skill

### Adding New Permissions

To add new permissions:

1. Insert into `permissions` table:
```sql
INSERT INTO permissions (permission_code, permission_name, permission_type)
VALUES ('add_portfolio', 'Add Portfolio Item', 'CREATE');
```

2. Use in code:
```php
$validator = new ActionValidator('portfolio');
$permissionCheck = $validator->validateCreate();
if (!$permissionCheck['allowed']) {
    // Deny action
}
```

---

## How to Use

### 1. Include in CMS Pages

In every admin CMS page, include the auth check view:

```php
<?php
require_once '../check_session.php';
require_once '../../config/database.php';

// Include authentication and permission check view
require_once 'auth_check.php';

// ... rest of your code
?>
```

At the end of your HTML, before closing `</body>`, include the permission manager script:

```html
<script src="../../assets/js/permission-manager.js"></script>
```

### 2. Validate on Action Handlers

In your CRUD handlers, validate permissions:

```php
<?php
require_once '../../config/action_validator.php';

$validator = new ActionValidator('projects'); // page code

if ($_POST['action'] === 'add') {
    $permissionCheck = $validator->validateCreate();
    if (!$permissionCheck['allowed']) {
        echo json_encode(['success' => false, 'message' => $permissionCheck['message']]);
        exit;
    }
    // ... proceed with creation
}
?>
```

### 3. Control UI Elements

Mark HTML elements with permission attributes:

#### Hide/Show Buttons
```html
<!-- Only visible if user has 'add_project' permission -->
<button data-permission="add_project" class="btn btn-primary">
    Add New Project
</button>

<!-- Only visible if user has 'edit_project' permission -->
<button data-permission="edit_project" class="btn btn-warning">
    Edit Project
</button>

<!-- Only visible if user has 'delete_project' permission -->
<button data-permission="delete_project" class="btn btn-danger">
    Delete Project
</button>
```

#### Hide/Show Form Sections
```html
<form data-permission-form="add_project">
    <!-- This entire form is hidden if user lacks 'add_project' permission -->
    <input type="text" name="title">
    <button type="submit">Save</button>
</form>
```

#### Hide/Show Sections
```html
<div data-permission-section="edit_project">
    <!-- This section is hidden if user lacks 'edit_project' permission -->
    <p>Edit options here...</p>
</div>
```

### 4. Access Permission Data in JavaScript

The permission data is available globally as `window.AuthData`:

```javascript
console.log(window.AuthData.username);      // Current user's username
console.log(window.AuthData.canCreate);     // Can user CREATE?
console.log(window.AuthData.canRead);       // Can user READ?
console.log(window.AuthData.canUpdate);     // Can user UPDATE?
console.log(window.AuthData.canDelete);     // Can user DELETE?

// Or use the PermissionManager globally
PermissionManager.canCreate();               // Returns boolean
PermissionManager.hasPermission('add_project'); // Custom check
```

### 5. Check Permissions in PHP Code

```php
<?php
$permController = new PermissionController();

// Check single permission
if ($permController->hasPermission('add_project', 'projects')) {
    echo "User can add projects";
}

// Get all user permissions for a page
$pagePerms = $permController->getPagePermissions('projects');
// Returns: [
//   ['permission_code' => 'add_project', 'permission_name' => '...', ...],
//   ['permission_code' => 'edit_project', ...],
//   ...
// ]

// Get user's pages
$pages = $permController->getUserPages();

// Get grouped permissions by type
$grouped = $permController->getPagePermissionsByType('projects');
// Returns: [
//   'create' => ['add_project'],
//   'read' => ['view_project'],
//   'update' => ['edit_project'],
//   'delete' => ['delete_project']
// ]
?>
```

---

## Implementation Guide

### Step 1: Update Database

1. Backup your current database
2. Import the new schema:
```sql
mysql -u root -p portfolio < database/portfolio_rbac.sql
```

Or manually run the SQL from `/database/portfolio_rbac.sql` in your MySQL client.

### Step 2: Set Default Permissions

After importing the schema, the default admin user will have all permissions. To modify permissions:

1. Go to `/admin/views/manage_permissions.php` (requires admin access)
2. Or use SQL to grant/revoke permissions:

```sql
-- Grant permission to user
INSERT INTO user_permissions (user_id, page_id, permission_id, granted_by)
VALUES (
    (SELECT id FROM users WHERE username = 'admin'),
    (SELECT id FROM pages WHERE page_code = 'projects'),
    (SELECT id FROM permissions WHERE permission_code = 'add_project'),
    (SELECT id FROM users WHERE username = 'admin')
);

-- Revoke permission
DELETE FROM user_permissions
WHERE user_id = (SELECT id FROM users WHERE username = 'admin')
AND page_id = (SELECT id FROM pages WHERE page_code = 'projects')
AND permission_id = (SELECT id FROM permissions WHERE permission_code = 'add_project');
```

### Step 3: Add New Users

```sql
INSERT INTO users (username, password, email, full_name, is_active)
VALUES (
    'john_doe',
    '$2y$12$...', -- Use PHP password_hash()
    'john@example.com',
    'John Doe',
    1
);
```

To generate a bcrypt password in PHP:
```php
$password = 'securePassword123';
$hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
echo $hashed; // Use this in the SQL INSERT
```

### Step 4: Create New Pages

```sql
INSERT INTO pages (page_code, page_name, description, url)
VALUES ('analytics', 'Analytics Dashboard', 'View analytics', '/admin/views/analytics.php');
```

### Step 5: Create New Permissions

```sql
INSERT INTO permissions (permission_code, permission_name, permission_type)
VALUES 
('add_analytics', 'Add Analytics Report', 'CREATE'),
('view_analytics', 'View Analytics', 'READ'),
('edit_analytics', 'Edit Analytics', 'UPDATE'),
('delete_analytics', 'Delete Analytics', 'DELETE');
```

---

## Security Features

### Server-Side Validation
- Every handler validates permissions before executing
- Returns 403 Forbidden if user lacks permission
- Session is checked every 10 seconds

### Client-Side UI Control
- Unauthorized buttons/forms are hidden from UI
- Users cannot accidentally click unauthorized actions
- Permission data is read-only in HTML attributes

### Password Security
- Passwords are bcrypt encrypted with cost 12
- Never stores plain text passwords
- Uses PHP's `password_hash()` and `password_verify()`

### Session Management
- Sessions timeout after 30 minutes of inactivity
- CSRF protection via session tokens
- User is logged out if database record is deactivated

---

## Troubleshooting

### Permission Denied Error

**Problem:** "You do not have permission to perform this action"

**Solution:**
1. Login as admin
2. Go to `/admin/views/manage_permissions.php`
3. Check the user's permissions for the page
4. Grant the necessary permission via checkbox

### Auth Check Not Working

**Problem:** Auth check view showing errors

**Solution:**
1. Verify `auth_check.php` is included before any output
2. Ensure `permission_controller.php` exists
3. Check that database tables exist (run portfolio_rbac.sql)

### UI Elements Still Show Disabled

**Problem:** Buttons show despite having permission

**Solution:**
1. Verify `permission-manager.js` is loaded
2. Check browser console for JavaScript errors
3. Verify HTML elements have correct `data-permission` attribute format
4. Clear browser cache and reload

### Permission Check Failing

**Problem:** All users showing "Access Denied"

**Solution:**
1. Verify user exists and is_active = 1
2. Check user_permissions table has entries (especially for admin)
3. Run permission grant SQL: 
```sql
-- Re-grant all permissions to admin
INSERT INTO user_permissions (user_id, page_id, permission_id, granted_by)
SELECT u.id, p.id, pe.id, u.id FROM users u, pages p, permissions pe
WHERE u.username = 'admin' AND p.page_code IN ('projects', 'skills')
ON DUPLICATE KEY UPDATE granted_by = granted_by;
```

---

## API Reference

### PermissionController Class

```php
// Check if authenticated
$pc->isAuthenticated() → bool

// Get current user
$pc->getCurrentUser() → array

// Get page by code
$pc->getPageByCode($code) → array

// Check permission
$pc->hasPermission($permission_code, $page_code) → bool

// Get page permissions grouped by type
$pc->getPagePermissionsByType($page_code) → array

// Grant permission
$pc->grantPermission($user_id, $page_code, $permission_code) → array

// Revoke permission
$pc->revokePermission($user_id, $page_code, $permission_code) → array
```

### ActionValidator Class

```php
// Validate CREATE permission
$validator->validateCreate() → array

// Validate READ permission
$validator->validateRead() → array

// Validate UPDATE permission
$validator->validateUpdate() → array

// Validate DELETE permission
$validator->validateDelete() → array

// Validate custom permission
$validator->validatePermission($code) → array
```

### JavaScript PermissionManager

```javascript
// Initialize
PermissionManager.init()

// Check permission
PermissionManager.hasPermission(code) → bool

// Can user create?
PermissionManager.canCreate() → bool

// Get permissions
PermissionManager.getPermissions() → object

// Refresh UI
PermissionManager.refresh()

// Check specific CRUD
PermissionManager.canCreate()   → bool
PermissionManager.canRead()     → bool
PermissionManager.canUpdate()   → bool
PermissionManager.canDelete()   → bool
```

---

## Support & Questions

If you encounter issues or have questions about the RBAC system:

1. Check the Troubleshooting section above
2. Review the API Reference
3. Check database tables for correct structure
4. Verify permission codes match your use cases

---

**Last Updated:** February 14, 2026
**System Version:** 1.0.0
**Status:** Production Ready
