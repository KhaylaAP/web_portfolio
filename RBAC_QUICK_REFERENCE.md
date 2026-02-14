# RBAC Quick Start Guide

## For Developers

This is a quick reference for implementing RBAC in your CMS pages and handlers.

---

## 1️⃣ Add Auth Check to Your Page (PHP)

```php
<?php
require_once '../check_session.php';
require_once '../../config/database.php';

// 👇 ADD THIS LINE 👇
require_once 'auth_check.php';

// ... rest of your PHP code
?>
```

---

## 2️⃣ Add Permission Manager to Your Page (HTML)

Before closing `</body>`, add:

```html
<!-- Permission Manager Script -->
<script src="../../assets/js/permission-manager.js"></script>
</body>
```

---

## 3️⃣ Mark Buttons with Permission Attributes

```html
<!-- Add Button (CREATE permission) -->
<button data-permission="add_projectName" class="btn btn-success">
    Add New
</button>

<!-- Edit Button (UPDATE permission) -->
<button data-permission="edit_projectName" class="btn btn-warning">
    Edit
</button>

<!-- Delete Button (DELETE permission) -->
<button data-permission="delete_projectName" class="btn btn-danger">
    Delete
</button>

<!-- View Button (READ permission) -->
<button data-permission="view_projectName" class="btn btn-info">
    View Details
</button>
```

> **Pattern:** `[action]_[pageName]`
> - Examples: `add_project`, `edit_skills`, `delete_portfolio`

---

## 4️⃣ Validate in Your Handler (PHP)

```php
<?php
require_once '../../config/action_validator.php';

// Initialize validator for YOUR page
$validator = new ActionValidator('projects');

// Validate CREATE
if ($_POST['action'] === 'add') {
    $check = $validator->validateCreate();
    if (!$check['allowed']) {
        echo json_encode(['success' => false, 'message' => $check['message']]);
        exit;
    }
    // ... proceed with INSERT
}

// Validate UPDATE
if ($_POST['action'] === 'edit') {
    $check = $validator->validateUpdate();
    if (!$check['allowed']) {
        echo json_encode(['success' => false, 'message' => $check['message']]);
        exit;
    }
    // ... proceed with UPDATE
}

// Validate DELETE
if ($_POST['action'] === 'delete') {
    $check = $validator->validateDelete();
    if (!$check['allowed']) {
        echo json_encode(['success' => false, 'message' => $check['message']]);
        exit;
    }
    // ... proceed with DELETE
}

// Validate READ
if ($_GET['action'] === 'list') {
    $check = $validator->validateRead();
    if (!$check['allowed']) {
        echo json_encode(['status' => 'error', 'message' => $check['message']]);
        exit;
    }
    // ... proceed with SELECT
}
?>
```

---

## 5️⃣ Access Permission Data in JavaScript

```javascript
// Global AuthData object is available
console.log(window.AuthData);
console.log(window.AuthData.username);      // User's name
console.log(window.AuthData.canCreate);     // Boolean
console.log(window.AuthData.canRead);       // Boolean
console.log(window.AuthData.canUpdate);     // Boolean
console.log(window.AuthData.canDelete);     // Boolean

// Or use PermissionManager
PermissionManager.canCreate();               // Check CREATE
PermissionManager.canRead();                 // Check READ
PermissionManager.canUpdate();               // Check UPDATE
PermissionManager.canDelete();               // Check DELETE

// Custom checks
if (PermissionManager.hasPermission('add_project')) {
    console.log('User can add projects');
}
```

---

## Adding New Page to CMS

### 1. Add to Database

```sql
-- Add page
INSERT INTO pages (page_code, page_name, description, url, is_active)
VALUE ('mypage', 'My Page', 'Description here', '/admin/views/mypage.php', 1);

-- Add permissions for this page
INSERT INTO permissions (permission_code, permission_name, permission_type) VALUES
('add_mypage', 'Add Item', 'CREATE'),
('view_mypage', 'View Items', 'READ'),
('edit_mypage', 'Edit Item', 'UPDATE'),
('delete_mypage', 'Delete Item', 'DELETE');

-- Grant permissions to admin
INSERT INTO user_permissions (user_id, page_id, permission_id, granted_by)
SELECT 
    (SELECT id FROM users WHERE username='admin'),
    (SELECT id FROM pages WHERE page_code='mypage'),
    id,
    (SELECT id FROM users WHERE username='admin')
FROM permissions
WHERE permission_code IN ('add_mypage', 'view_mypage', 'edit_mypage', 'delete_mypage');
```

### 2. Implement in PHP File

```php
<?php
require_once '../check_session.php';
require_once '../../config/database.php';
require_once 'auth_check.php';  // ✅ Add this

// Now current user's permissions for 'mypage' are available
// auth_check.php automatically detects 'mypage.php' as page_code 'mypage'
?>
```

### 3. Add Buttons with Permissions

```html
<button data-permission="add_mypage">Add</button>
<button data-permission="edit_mypage">Edit</button>
<button data-permission="delete_mypage">Delete</button>
```

### 4. Validate in Handler

```php
<?php
require_once '../../config/action_validator.php';

$validator = new ActionValidator('mypage');  // Match page_code

if ($action === 'add') {
    $check = $validator->validateCreate();
    if (!$check['allowed']) exit with error;
}
?>
```

---

## Common Permission Codes

Replace `projectName` with your actual page system name (lowercase):

| Permission Code | Page Code | Action | Use When |
|---|---|---|---|
| `add_projects` | projects | CREATE | Adding new project |
| `view_projects` | projects | READ | Listing projects |
| `edit_projects` | projects | UPDATE | Editing project |
| `delete_projects` | projects | DELETE | Deleting project |
| `add_skills` | skills | CREATE | Adding new skill |
| `view_skills` | skills | READ | Listing skills |
| `edit_skills` | skills | UPDATE | Editing skill |
| `delete_skills` | skills | DELETE | Deleting skill |

---

## Checklist for New Feature

- [ ] Create database page record
- [ ] Create and insert permission records (add_, view_, edit_, delete_)
- [ ] Grant permissions to users
- [ ] Add `require_once 'auth_check.php';` to your page
- [ ] Add buttons with `data-permission="xxx"` attributes
- [ ] Validate permissions in handler with `ActionValidator`
- [ ] Add `permission-manager.js` script before `</body>`
- [ ] Test with different user roles

---

## Testing

### Test with Admin User
1. Login as admin
2. Visit admin page
3. All buttons should be visible
4. All actions should work

### Test with Limited User
1. Create new user
2. Grant only VIEW permission (not CREATE/UPDATE/DELETE)
3. Login as that user
4. Visit admin page
5. Only read buttons should be visible
6. Add/Edit/Delete buttons should be hidden
7. Attempting to call handlers directly should return error

### Test Expiration
1. Login to any admin page
2. Wait 10+ seconds
3. Check browser console
4. Auth check should run periodically
5. If session expires, redirect to login should happen

---

## File Locations Reference

```
project/
├── config/
│   ├── database.php                    # Database connection
│   ├── permission_controller.php       # ✨ Permission logic
│   └── action_validator.php            # ✨ CRUD validation
├── admin/
│   ├── check_session.php               # Session validation
│   ├── views/
│   │   ├── auth_check.php              # ✨ Include in every CMS page
│   │   ├── projects.php                # UPDATED with auth_check
│   │   ├── my_skills.php               # UPDATED with auth_check
│   │   └── manage_permissions.php      # ✨ Admin permission manager
│   └── handlers/
│       ├── auth_verify.php             # ✨ Auth check AJAX
│       ├── skill_handler.php           # UPDATED with validation
│       ├── project_actions.php         # UPDATED with validation
│       └── project_handler.php         # UPDATED with validation
├── assets/
│   └── js/
│       └── permission-manager.js       # ✨ Client-side control
├── database/
│   └── portfolio_rbac.sql              # ✨ Database schema
└── RBAC_DOCUMENTATION.md               # ✨ Full documentation
```

> ✨ = New files

---

## Quick SQL Commands

### Grant All Permissions to User
```sql
INSERT INTO user_permissions (user_id, page_id, permission_id, granted_by)
SELECT u.id, p.id, pe.id, u.id 
FROM users u, pages p, permissions pe
WHERE u.username = 'username_here'
ON DUPLICATE KEY UPDATE granted_by = granted_by;
```

### Revoke All Permissions from User
```sql
DELETE FROM user_permissions
WHERE user_id = (SELECT id FROM users WHERE username = 'username_here');
```

### Grant Single Permission
```sql
INSERT INTO user_permissions (user_id, page_id, permission_id, granted_by)
VALUES (
    (SELECT id FROM users WHERE username = 'username'),
    (SELECT id FROM pages WHERE page_code = 'projects'),
    (SELECT id FROM permissions WHERE permission_code = 'add_project'),
    1
);
```

### Check User's Permissions
```sql
SELECT u.username, pg.page_name, p.permission_code, p.permission_type
FROM user_permissions up
INNER JOIN users u ON up.user_id = u.id
INNER JOIN pages pg ON up.page_id = pg.id
INNER JOIN permissions p ON up.permission_id = p.id
WHERE u.username = 'username'
ORDER BY pg.page_name, p.permission_type;
```

---

## Need Help?

See `RBAC_DOCUMENTATION.md` for complete reference:
- Troubleshooting section
- API documentation
- Security features
- Architecture details

---

**Version:** 1.0.0  
**Last Updated:** February 14, 2026
