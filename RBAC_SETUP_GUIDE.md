# RBAC Implementation Setup Guide

## ⚠️ Important: Database Migration

Before implementing the RBAC system, follow these steps carefully.

---

## Step 1: Backup Your Database

```bash
# Command line backup
mysqldump -u root -p portfolio > portfolio_backup_$(date +%Y%m%d_%H%M%S).sql

# Or use MySQL Workbench/PhpMyAdmin to export
```

---

## Step 2: Run the New Schema

### Option A: Using Command Line
```bash
mysql -u root -p portfolio < database/portfolio_rbac.sql
```

### Option B: Using PhpMyAdmin
1. Go to PhpMyAdmin → Select 'portfolio' database
2. Click "Import" tab
3. Choose file: `/database/portfolio_rbac.sql`
4. Click "Import" button

### Option C: Using MySQL Workbench
1. Open MySQL Workbench
2. File → Open SQL Script
3. Select `/database/portfolio_rbac.sql`
4. Execute the script

### Option D: Manual SQL Execution
Copy-paste the entire contents of `/database/portfolio_rbac.sql` in your MySQL client and execute.

---

## Step 3: Verify Database Setup

### Check Tables Exist
```sql
SHOW TABLES;
-- Should show: permissions, user_permissions, users, pages, projects, skills
```

### Check Default Data
```sql
-- Check users
SELECT * FROM users;
-- Should show admin user

-- Check pages
SELECT * FROM pages;
-- Should show: projects, skills, portfolio, about, home

-- Check permissions
SELECT * FROM permissions;
-- Should show 8 default permissions

-- Check user_permissions (admin should have all)
SELECT COUNT(*) FROM user_permissions 
WHERE user_id = (SELECT id FROM users WHERE username = 'admin');
-- Should be > 0
```

---

## Step 4: Test Login

### Login with Default Credentials
- **Username:** `admin`
- **Password:** `admin123`

Access: `http://localhost:8000/admin/login.php`

✅ If you can login and see admin dashboard, database is properly set up!

---

## Step 5: Verify RBAC Components

### Check Files Exist
```bash
# From your project root, verify these files exist:
ls -la config/permission_controller.php
ls -la config/action_validator.php
ls -la admin/views/auth_check.php
ls -la admin/handlers/auth_verify.php
ls -la assets/js/permission-manager.js
ls -la admin/views/manage_permissions.php
```

All files should exist with size > 0.

---

## Step 6: Test Auth Check View

1. Visit: `http://localhost:8000/admin/views/projects.php`
2. Open Browser DevTools (F12)
3. Check Console tab for any errors
4. Check Network tab:
   - Should see `/admin/handlers/auth_verify.php` being called
   - Should be called periodically (every 10 seconds)

✅ If no errors, auth check is working!

---

## Step 7: Test Permission Manager

1. Visit: `http://localhost:8000/admin/views/manage_permissions.php`
2. Should see user permission checkboxes
3. You can toggle permissions via checkboxes
4. Changes should reflect immediately

⚠️ Only admin can access this page by default.

---

## Step 8: Test Permission Validation

### Test 1: View Projects (Should Allow)
1. Login as admin
2. Visit: `http://localhost:8000/admin/views/projects.php`
3. You should see the projects list
4. All buttons should be visible (Add, Edit, Delete)

✅ SUCCESS: Admin has permissions

### Test 2: Create Limited User
```sql
-- Create new user with limited permissions
INSERT INTO users (username, password, email, full_name, is_active)
VALUES ('viewer', '$2y$12$RIXpfYXHfL0eFYHDVYWJ..OP7x4MXvRuI8SgSFa8VRz4jH5dKgmzK', 'viewer@test.com', 'View Only User', 1);

-- Password for this user is: 'viewer123'
-- Grant only VIEW permission for projects
INSERT INTO user_permissions (user_id, page_id, permission_id, granted_by)
VALUES (
    (SELECT id FROM users WHERE username = 'viewer'),
    (SELECT id FROM pages WHERE page_code = 'projects'),
    (SELECT id FROM permissions WHERE permission_code = 'view_project'),
    (SELECT id FROM users WHERE username = 'admin')
);
```

### Test 3: Login as Limited User
1. Logout from admin
2. Login with:
   - **Username:** `viewer`
   - **Password:** `viewer123`
3. Visit: `http://localhost:8000/admin/views/projects.php`
4. Expected: Projects list visible, but Add/Edit/Delete buttons should be HIDDEN

✅ SUCCESS: Permission system working!

### Test 4: Try to Access Denied Action
1. While logged in as 'viewer', open Browser Console
2. Try to add a project via API:
```javascript
fetch('/admin/handlers/project_actions.php?action=save', {
    method: 'POST',
    body: new FormData(document.querySelector('form'))
})
.then(r => r.json())
.then(d => console.log(d));
```
3. Expected: Error message "You do not have permission to create records"

✅ SUCCESS: Server-side validation working!

---

## Step 9: Test 10-Second Auth Refresh

1. Login as any user
2. Open Browser DevTools → Network tab
3. Filter by type: XHR/Fetch
4. Watch for `/admin/handlers/auth_verify.php` requests
5. Should see request every 10 seconds

✅ SUCCESS: Auth refresh working!

---

## Step 10: Integrate into Your Pages

### For Projects Page
File: `/admin/views/projects.php`

**Already Updated!** ✅ 
- Contains `require_once 'auth_check.php';`
- Contains `<script src="../../assets/js/permission-manager.js"></script>`

### For Skills Page
File: `/admin/views/my_skills.php`

**Already Updated!** ✅
- Contains `require_once 'auth_check.php';`
- Contains `<script src="../../assets/js/permission-manager.js"></script>`

### For Custom Pages
Follow this pattern:

```php
<?php
// ✅ At the top of your file, after check_session.php
require_once 'auth_check.php';

// ... your code ...
?>
```

And at the bottom before `</body>`:
```html
<!-- ✅ Include permission manager -->
<script src="../../assets/js/permission-manager.js"></script>
</body>
```

---

## Step 11: Update Your Handlers

### Use ActionValidator

**Pattern for all CRUD handlers:**

```php
<?php
require_once '../../config/action_validator.php';

// Create validator for your page
$validator = new ActionValidator('yourpage');

// Before CREATE action
if ($action === 'add') {
    $check = $validator->validateCreate();
    if (!$check['allowed']) {
        echo json_encode(['success' => false, 'message' => $check['message']]);
        exit;
    }
    // ... proceed with INSERT
}

// Before UPDATE action
if ($action === 'edit') {
    $check = $validator->validateUpdate();
    if (!$check['allowed']) {
        echo json_encode(['success' => false, 'message' => $check['message']]);
        exit;
    }
    // ... proceed with UPDATE
}

// Before DELETE action
if ($action === 'delete') {
    $check = $validator->validateDelete();
    if (!$check['allowed']) {
        echo json_encode(['success' => false, 'message' => $check['message']]);
        exit;
    }
    // ... proceed with DELETE
}

// Before READ action
if ($action === 'list' || $action === 'get') {
    $check = $validator->validateRead();
    if (!$check['allowed']) {
        echo json_encode(['success' => false, 'message' => $check['message']]);
        exit;
    }
    // ... proceed with SELECT
}
?>
```

### Handlers Already Updated:
- ✅ `/admin/handlers/skill_handler.php` - Permission validation added
- ✅ `/admin/views/project_handler.php` - Permission validation added
- ✅ `/admin/handlers/project_actions.php` - Permission validation added

---

## Step 12: Mark UI Elements with Permissions

### In Your HTML Templates

```html
<!-- Buttons with permission codes -->
<button data-permission="add_yourpage" class="btn btn-success">Add New</button>
<button data-permission="edit_yourpage" class="btn btn-warning">Edit</button>
<button data-permission="delete_yourpage" class="btn btn-danger">Delete</button>

<!-- Forms that require permission -->
<form data-permission-form="add_yourpage">
    <!-- Form fields -->
</form>

<!-- Sections that require permission -->
<div data-permission-section="edit_yourpage">
    <!-- Section content -->
</div>
```

---

## Checklist: Implementation Complete ✅

- [ ] Database backed up
- [ ] `portfolio_rbac.sql` executed successfully
- [ ] Database tables verified present
- [ ] Default users and permissions created
- [ ] Admin can login successfully
- [ ] `/admin/views/projects.php` working with permissions
- [ ] `/admin/views/my_skills.php` working with permissions
- [ ] Manage Permissions page accessible
- [ ] Permission Manager script loading (check console)
- [ ] Limited user can't access denied actions
- [ ] Auth check requests every 10 seconds
- [ ] Handlers validate permissions server-side
- [ ] UI buttons hide for unauthorized users
- [ ] All RBAC files created and in correct locations
- [ ] Documentation read and understood

---

## Troubleshooting During Setup

### Error: "Cannot find permission_controller.php"
- **Solution:** Verify file exists at `/config/permission_controller.php`
- Run: `ls -la config/permission_controller.php`

### Error: "Database table not found"
- **Solution:** Re-run the SQL schema file
- Use: `mysql -u root -p portfolio < database/portfolio_rbac.sql`

### Error: "Cannot login with admin"
- **Solution:** Verify admin user exists
- Run: `SELECT * FROM users WHERE username = 'admin';`
- If not found, re-run the SQL schema file

### Error: "Page not found" on projects.php
- **Solution:** Ensure include path is correct
- Verify: `require_once 'auth_check.php';` path is relative to file location

### Permissions not hiding buttons
- **Solution:** Verify permission-manager.js is loaded
- Check browser console for errors
- Verify HTML has correct `data-permission` attribute format

### Auth check not running every 10 seconds
- **Solution:** Verify auth_verify.php handler exists
- Check browser Network tab for XHR requests
- Verify permission-manager.js loaded successfully

---

## Support Resources

1. **Full Documentation:** See `RBAC_DOCUMENTATION.md`
2. **Quick Reference:** See `RBAC_QUICK_REFERENCE.md`
3. **Permission Management:** Use `/admin/views/manage_permissions.php`
4. **Database Queries:** Reference quickstart SQL commands

---

## Next Steps After Setup

1. Create additional users
2. Configure their permissions via manage_permissions.php
3. Add permission codes to new pages you create
4. Implement ActionValidator in your handlers
5. Mark buttons with permission attributes

---

**Setup Date:** ________________  
**Completed By:** ________________  
**Status:** ☐ In Progress | ☐ Complete | ☐ Testing

---

**System Version:** 1.0.0  
**Last Updated:** February 14, 2026
